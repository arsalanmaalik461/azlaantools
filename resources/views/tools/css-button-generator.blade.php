@extends('layouts.app')

@section('title', 'CSS Button Generator — Free Online Tool')
@section('meta_description', 'Design custom CSS buttons visually and copy the final code')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CSS Button Generator</h1>
            <p class="lead small text-muted">Style a button with colour, padding, radius, border and font controls, preview it live, and copy the CSS including a hover state.</p>
            <div class="border rounded p-4 mb-3 text-center"><button type="button" id="btnPreview">Click Me</button></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="btnText">Button text</label><input type="text" class="form-control" id="btnText" value="Click Me"></div>
                <div class="col-md-4"><label class="form-label" for="btnBg">Background colour</label><input type="color" class="form-control form-control-color w-100" id="btnBg" value="#0d6efd"></div>
                <div class="col-md-4"><label class="form-label" for="btnColor">Text colour</label><input type="color" class="form-control form-control-color w-100" id="btnColor" value="#ffffff"></div>
                <div class="col-md-3"><label class="form-label" for="btnPadY">Padding Y: <span id="btnPadYVal">10</span>px</label><input type="range" class="form-range" id="btnPadY" min="0" max="30" value="10"></div>
                <div class="col-md-3"><label class="form-label" for="btnPadX">Padding X: <span id="btnPadXVal">24</span>px</label><input type="range" class="form-range" id="btnPadX" min="0" max="60" value="24"></div>
                <div class="col-md-3"><label class="form-label" for="btnRadius">Radius: <span id="btnRadiusVal">8</span>px</label><input type="range" class="form-range" id="btnRadius" min="0" max="50" value="8"></div>
                <div class="col-md-3"><label class="form-label" for="btnFont">Font size: <span id="btnFontVal">16</span>px</label><input type="range" class="form-range" id="btnFont" min="10" max="32" value="16"></div>
                <div class="col-md-3"><label class="form-label" for="btnBorderW">Border width: <span id="btnBorderWVal">0</span>px</label><input type="range" class="form-range" id="btnBorderW" min="0" max="6" value="0"></div>
                <div class="col-md-3"><label class="form-label" for="btnBorderC">Border colour</label><input type="color" class="form-control form-control-color w-100" id="btnBorderC" value="#0a58ca"></div>
                <div class="col-md-3"><label class="form-label" for="btnHover">Hover background</label><input type="color" class="form-control form-control-color w-100" id="btnHover" value="#0a58ca"></div>
            </div>
            <label class="form-label mt-3" for="btnOut">CSS code</label>
            <textarea class="form-control font-monospace" id="btnOut" rows="7" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="btnCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Type the button text and pick the colours.</li><li>Adjust padding, radius, font size and border with the sliders.</li><li>Copy the CSS — it includes a ready hover rule.</li></ol>
            <p class="small text-muted mb-0">Note: The generated class name is .my-button — rename it in your stylesheet if needed.</p>
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
    function calc() {
        ["btnPadY","btnPadX","btnRadius","btnFont","btnBorderW"].forEach(function (id) { el(id + "Val").textContent = num(id); });
        var b = el("btnPreview");
        b.textContent = el("btnText").value || "Button";
        b.style.background = el("btnBg").value; b.style.color = el("btnColor").value;
        b.style.padding = num("btnPadY") + "px " + num("btnPadX") + "px";
        b.style.borderRadius = num("btnRadius") + "px"; b.style.fontSize = num("btnFont") + "px";
        b.style.border = num("btnBorderW") + "px solid " + el("btnBorderC").value;
        b.style.cursor = "pointer";
        el("btnOut").value = ".my-button {\n  background: " + el("btnBg").value + ";\n  color: " + el("btnColor").value + ";\n  padding: " + num("btnPadY") + "px " + num("btnPadX") + "px;\n  border: " + num("btnBorderW") + "px solid " + el("btnBorderC").value + ";\n  border-radius: " + num("btnRadius") + "px;\n  font-size: " + num("btnFont") + "px;\n  cursor: pointer;\n}\n.my-button:hover {\n  background: " + el("btnHover").value + ";\n}";
    }
    ["btnText","btnBg","btnColor","btnPadY","btnPadX","btnRadius","btnFont","btnBorderW","btnBorderC","btnHover"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    el("btnCopy").addEventListener("click", function () { copyText("btnOut", el("btnCopy")); });
    calc();
})();
</script>
@endsection
