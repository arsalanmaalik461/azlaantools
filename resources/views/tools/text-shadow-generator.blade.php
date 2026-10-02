@extends('layouts.app')

@section('title', 'Text Shadow Generator — Free Online Tool')
@section('meta_description', 'Create layered CSS text shadows visually with instant code')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Text Shadow Generator</h1>
            <p class="lead small text-muted">Design a CSS text shadow with offset, blur and colour controls, preview it on real text, try the one-click presets, and copy the code.</p>
            <div class="border rounded p-4 mb-3 text-center"><div id="tsPreview" class="fw-bold" style="font-size:44px">Shadow Text</div></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="tsText">Preview text</label><input type="text" class="form-control" id="tsText" value="Shadow Text"></div>
                <div class="col-md-4"><label class="form-label" for="tsX">Offset X: <span id="tsXVal">3</span>px</label><input type="range" class="form-range" id="tsX" min="-20" max="20" value="3"></div>
                <div class="col-md-4"><label class="form-label" for="tsY">Offset Y: <span id="tsYVal">3</span>px</label><input type="range" class="form-range" id="tsY" min="-20" max="20" value="3"></div>
                <div class="col-md-4"><label class="form-label" for="tsBlur">Blur: <span id="tsBlurVal">4</span>px</label><input type="range" class="form-range" id="tsBlur" min="0" max="30" value="4"></div>
                <div class="col-md-4"><label class="form-label" for="tsColor">Shadow colour</label><input type="color" class="form-control form-control-color w-100" id="tsColor" value="#000000"></div>
                <div class="col-md-4"><label class="form-label" for="tsLayers">Layered depth (extra layers)</label><select class="form-select" id="tsLayers"><option value="0">None — single shadow</option><option value="3">3 layers</option><option value="5">5 layers</option><option value="8">8 layers</option></select></div>
            </div>
            <div class="d-flex gap-2 mt-3 flex-wrap">
                <button type="button" class="btn btn-outline-primary btn-sm ts-preset" data-x="2" data-y="2" data-b="0" data-c="#dc3545">Hard retro</button>
                <button type="button" class="btn btn-outline-primary btn-sm ts-preset" data-x="0" data-y="0" data-b="12" data-c="#0d6efd">Soft glow</button>
                <button type="button" class="btn btn-outline-primary btn-sm ts-preset" data-x="-2" data-y="-2" data-b="3" data-c="#ffc107">Outline feel</button>
            </div>
            <label class="form-label mt-3" for="tsOut">CSS code</label>
            <textarea class="form-control font-monospace" id="tsOut" rows="3" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="tsCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Type your preview text and move the offset and blur sliders.</li><li>Optionally add layered depth for a stacked 3D shadow.</li><li>Copy the text-shadow CSS into your stylesheet.</li></ol>
            <p class="small text-muted mb-0">Note: Layered depth repeats the shadow at growing offsets — the classic technique behind retro and 3D headline effects.</p>
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
        ["tsX","tsY","tsBlur"].forEach(function (id) { el(id + "Val").textContent = num(id); });
        var x = num("tsX"), y = num("tsY"), b = num("tsBlur"), col = el("tsColor").value, extra = parseInt(el("tsLayers").value, 10);
        var parts = [x + "px " + y + "px " + b + "px " + col], i;
        for (i = 2; i <= extra + 1; i++) { parts.push(Math.round(x * i / 2) + "px " + Math.round(y * i / 2) + "px 0 " + col); }
        var css = parts.join(", ");
        var pv = el("tsPreview"); pv.textContent = el("tsText").value || "Shadow Text"; pv.style.textShadow = css;
        el("tsOut").value = "text-shadow: " + css + ";";
    }
    ["tsText","tsX","tsY","tsBlur","tsColor","tsLayers"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    document.querySelectorAll(".ts-preset").forEach(function (btn) { btn.addEventListener("click", function () { el("tsX").value = btn.getAttribute("data-x"); el("tsY").value = btn.getAttribute("data-y"); el("tsBlur").value = btn.getAttribute("data-b"); el("tsColor").value = btn.getAttribute("data-c"); calc(); }); });
    el("tsCopy").addEventListener("click", function () { copyText("tsOut", el("tsCopy")); });
    calc();
})();
</script>
@endsection
