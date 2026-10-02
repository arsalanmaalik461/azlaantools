@extends('layouts.app')

@section('title', 'Box Shadow Generator — Free Online Tool')
@section('meta_description', 'Create CSS box shadows visually and copy ready to use code')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Box Shadow Generator</h1>
            <p class="lead small text-muted">Tune offset, blur, spread, colour and opacity with sliders, see the shadow live on the preview box, and copy ready CSS.</p>
            <div class="border rounded p-4 mb-3 d-flex align-items-center justify-content-center" style="min-height:210px">
                <div id="bsPreview" class="border rounded p-4 text-center" style="width:220px">Preview box</div>
            </div>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="bsX">Offset X: <span id="bsXVal">8</span>px</label><input type="range" class="form-range" id="bsX" min="-60" max="60" value="8"></div>
                <div class="col-md-3"><label class="form-label" for="bsY">Offset Y: <span id="bsYVal">10</span>px</label><input type="range" class="form-range" id="bsY" min="-60" max="60" value="10"></div>
                <div class="col-md-3"><label class="form-label" for="bsBlur">Blur: <span id="bsBlurVal">18</span>px</label><input type="range" class="form-range" id="bsBlur" min="0" max="100" value="18"></div>
                <div class="col-md-3"><label class="form-label" for="bsSpread">Spread: <span id="bsSpreadVal">0</span>px</label><input type="range" class="form-range" id="bsSpread" min="-30" max="60" value="0"></div>
                <div class="col-md-3"><label class="form-label" for="bsColor">Shadow colour</label><input type="color" class="form-control form-control-color w-100" id="bsColor" value="#000000"></div>
                <div class="col-md-3"><label class="form-label" for="bsOpacity">Opacity: <span id="bsOpacityVal">30</span>%</label><input type="range" class="form-range" id="bsOpacity" min="0" max="100" value="30"></div>
                <div class="col-md-3 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" id="bsInset"><label class="form-check-label" for="bsInset">Inset shadow</label></div></div>
            </div>
            <label class="form-label mt-3" for="bsOut">CSS code</label>
            <textarea class="form-control font-monospace" id="bsOut" rows="2" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="bsCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Move the sliders to design the shadow on the preview box.</li><li>Toggle inset for an inner shadow.</li><li>Copy the CSS and paste it into your stylesheet.</li></ol>
            <p class="small text-muted mb-0">Note: The colour is output as rgba so the opacity slider is preserved exactly in the copied code.</p>
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
        ["bsX","bsY","bsBlur","bsSpread","bsOpacity"].forEach(function (id) { el(id + "Val").textContent = num(id); });
        var css = (el("bsInset").checked ? "inset " : "") + num("bsX") + "px " + num("bsY") + "px " + num("bsBlur") + "px " + num("bsSpread") + "px " + rgba(el("bsColor").value, num("bsOpacity"));
        el("bsPreview").style.boxShadow = css;
        el("bsOut").value = "box-shadow: " + css + ";";
    }
    ["bsX","bsY","bsBlur","bsSpread","bsColor","bsOpacity","bsInset"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    el("bsCopy").addEventListener("click", function () { copyText("bsOut", el("bsCopy")); });
    calc();
})();
</script>
@endsection
