@extends('layouts.app')

@section('title', 'Glassmorphism Generator — Free Online Tool')
@section('meta_description', 'Create frosted glass UI effects and copy the CSS code')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Glassmorphism Generator</h1>
            <p class="lead small text-muted">Tune blur, transparency, colour and radius on a glass card floating over a colourful background — then copy the CSS.</p>
            <div id="gmWrap" class="rounded p-4 mb-3 d-flex align-items-center justify-content-center" style="min-height:230px">
                <div id="gmPreview" class="p-4 text-center" style="width:280px"><div class="fw-bold fs-5">Glass Card</div><div class="small">Frosted glass effect preview</div></div>
            </div>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="gmBlur">Blur: <span id="gmBlurVal">12</span>px</label><input type="range" class="form-range" id="gmBlur" min="0" max="30" value="12"></div>
                <div class="col-md-3"><label class="form-label" for="gmOpacity">Background opacity: <span id="gmOpacityVal">25</span>%</label><input type="range" class="form-range" id="gmOpacity" min="0" max="80" value="25"></div>
                <div class="col-md-3"><label class="form-label" for="gmColor">Tint colour</label><input type="color" class="form-control form-control-color w-100" id="gmColor" value="#ffffff"></div>
                <div class="col-md-3"><label class="form-label" for="gmRadius">Radius: <span id="gmRadiusVal">16</span>px</label><input type="range" class="form-range" id="gmRadius" min="0" max="50" value="16"></div>
                <div class="col-md-3"><label class="form-label" for="gmBorder">Border opacity: <span id="gmBorderVal">35</span>%</label><input type="range" class="form-range" id="gmBorder" min="0" max="100" value="35"></div>
            </div>
            <label class="form-label mt-3" for="gmOut">CSS code</label>
            <textarea class="form-control font-monospace" id="gmOut" rows="6" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="gmCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Move the blur and opacity sliders while watching the glass card.</li><li>Pick a tint colour and radius.</li><li>Copy the CSS — it includes the -webkit- backdrop-filter line for Safari.</li></ol>
            <p class="small text-muted mb-0">Note: Glassmorphism needs something colourful behind the element — on a plain background the effect is almost invisible, as the preview shows.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    function rgba(hex, op) { var r = parseInt(hex.slice(1, 3), 16), g = parseInt(hex.slice(3, 5), 16), b = parseInt(hex.slice(5, 7), 16); return "rgba(" + r + ", " + g + ", " + b + ", " + (op / 100) + ")"; }
    function calc() {
        ["gmBlur","gmOpacity","gmRadius","gmBorder"].forEach(function (id) { el(id + "Val").textContent = num(id); });
        el("gmWrap").style.background = "linear-gradient(135deg, #0d6efd, #6610f2 45%, #d63384)";
        var card = el("gmPreview");
        var bg = rgba(el("gmColor").value, num("gmOpacity"));
        card.style.background = bg; card.style.backdropFilter = "blur(" + num("gmBlur") + "px)"; card.style.webkitBackdropFilter = "blur(" + num("gmBlur") + "px)";
        card.style.borderRadius = num("gmRadius") + "px"; card.style.border = "1px solid " + rgba(el("gmColor").value, num("gmBorder"));
        el("gmOut").value = ".glass {\n  background: " + bg + ";\n  backdrop-filter: blur(" + num("gmBlur") + "px);\n  -webkit-backdrop-filter: blur(" + num("gmBlur") + "px);\n  border-radius: " + num("gmRadius") + "px;\n  border: 1px solid " + rgba(el("gmColor").value, num("gmBorder")) + ";\n}";
    }
    ["gmBlur","gmOpacity","gmColor","gmRadius","gmBorder"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    el("gmCopy").addEventListener("click", function () { copyText("gmOut", el("gmCopy")); });
    calc();
})();
</script>
@endsection
