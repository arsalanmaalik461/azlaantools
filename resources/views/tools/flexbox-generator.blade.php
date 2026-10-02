@extends('layouts.app')

@section('title', 'CSS Flexbox Generator — Free Online Tool')
@section('meta_description', 'Build flexbox layouts visually and copy the generated CSS code')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CSS Flexbox Generator</h1>
            <p class="lead small text-muted">Control direction, justification, alignment, wrapping and gap — the flex preview and container CSS update live.</p>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="fxDir">Direction</label><select class="form-select fx-in" id="fxDir"><option value="row">row</option><option value="row-reverse">row-reverse</option><option value="column">column</option><option value="column-reverse">column-reverse</option></select></div>
                <div class="col-md-3"><label class="form-label" for="fxJustify">Justify content</label><select class="form-select fx-in" id="fxJustify"><option value="flex-start">flex-start</option><option value="center">center</option><option value="flex-end">flex-end</option><option value="space-between">space-between</option><option value="space-around">space-around</option><option value="space-evenly">space-evenly</option></select></div>
                <div class="col-md-3"><label class="form-label" for="fxAlign">Align items</label><select class="form-select fx-in" id="fxAlign"><option value="stretch">stretch</option><option value="flex-start">flex-start</option><option value="center">center</option><option value="flex-end">flex-end</option><option value="baseline">baseline</option></select></div>
                <div class="col-md-3"><label class="form-label" for="fxWrap">Wrap</label><select class="form-select fx-in" id="fxWrap"><option value="nowrap">nowrap</option><option value="wrap">wrap</option><option value="wrap-reverse">wrap-reverse</option></select></div>
                <div class="col-md-3"><label class="form-label" for="fxGap">Gap: <span id="fxGapVal">10</span>px</label><input type="range" class="form-range fx-in" id="fxGap" min="0" max="40" value="10"></div>
                <div class="col-md-3"><label class="form-label" for="fxItems">Items: <span id="fxItemsVal">5</span></label><input type="range" class="form-range fx-in" id="fxItems" min="1" max="10" value="5"></div>
            </div>
            <div id="fxPreview" class="border rounded p-2 mt-3" style="min-height:130px"></div>
            <label class="form-label mt-3" for="fxOut">CSS code</label>
            <textarea class="form-control font-monospace" id="fxOut" rows="7" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="fxCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose direction, justify and align values from the dropdowns.</li><li>Adjust gap and item count while watching the preview.</li><li>Copy the CSS for your own container class.</li></ol>
            <p class="small text-muted mb-0">Note: Item heights vary on purpose so align-items effects (especially stretch and baseline) are easy to see.</p>
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
        el("fxGapVal").textContent = num("fxGap"); el("fxItemsVal").textContent = num("fxItems");
        var pv = el("fxPreview");
        pv.style.display = "flex"; pv.style.flexDirection = el("fxDir").value; pv.style.justifyContent = el("fxJustify").value; pv.style.alignItems = el("fxAlign").value; pv.style.flexWrap = el("fxWrap").value; pv.style.gap = num("fxGap") + "px";
        pv.innerHTML = "";
        for (var i = 1; i <= num("fxItems"); i++) { var d = document.createElement("div"); d.className = "border rounded text-center fw-semibold"; d.style.padding = "10px 16px"; d.style.minHeight = (34 + (i % 3) * 18) + "px"; d.textContent = "Item " + i; pv.appendChild(d); }
        el("fxOut").value = ".flex-container {\n  display: flex;\n  flex-direction: " + el("fxDir").value + ";\n  justify-content: " + el("fxJustify").value + ";\n  align-items: " + el("fxAlign").value + ";\n  flex-wrap: " + el("fxWrap").value + ";\n  gap: " + num("fxGap") + "px;\n}";
    }
    document.querySelectorAll(".fx-in").forEach(function (f) { f.addEventListener("input", calc); f.addEventListener("change", calc); });
    el("fxCopy").addEventListener("click", function () { copyText("fxOut", el("fxCopy")); });
    calc();
})();
</script>
@endsection
