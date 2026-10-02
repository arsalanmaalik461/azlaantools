@extends('layouts.app')

@section('title', 'YAML to JSON Converter — Free Online Tool')
@section('meta_description', 'Convert YAML to JSON and JSON back to YAML instantly')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">YAML to JSON Converter</h1>
            <p class="lead small text-muted">Convert config files between YAML and JSON in both directions with the js-yaml library — handy for Kubernetes, CI pipelines and app settings.</p>
            <label class="form-label" for="yjIn">Input</label>
            <textarea class="form-control font-monospace" id="yjIn" rows="9">name: Azlaan Tools
free: true
tools:
  - pdf
  - solar
settings:
  theme: dark</textarea>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-primary" id="yjToJson">YAML to JSON</button>
                <button type="button" class="btn btn-secondary" id="yjToYaml">JSON to YAML</button>
                <button type="button" class="btn btn-success" id="yjCopy">Copy output</button>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="yjErr"></div>
            <label class="form-label mt-3" for="yjOut">Output</label>
            <textarea class="form-control font-monospace" id="yjOut" rows="9" readonly></textarea>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste YAML or JSON into the input box.</li><li>Click YAML to JSON or JSON to YAML depending on your input.</li><li>Copy the converted output.</li></ol>
            <p class="small text-muted mb-0">Note: Conversion uses the js-yaml library loaded from a CDN. YAML-only features such as anchors are resolved into plain JSON values; comments are not preserved.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js"></script>
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    function fail(msg) { var e = el("yjErr"); e.textContent = msg; e.classList.remove("d-none"); el("yjOut").value = ""; }
    el("yjToJson").addEventListener("click", function () {
        el("yjErr").classList.add("d-none");
        if (!window.jsyaml) { fail("The js-yaml library could not be loaded — check your internet connection."); return; }
        try { var obj = jsyaml.load(el("yjIn").value); el("yjOut").value = JSON.stringify(obj, null, 2); }
        catch (e) { fail("YAML error: " + e.message); }
    });
    el("yjToYaml").addEventListener("click", function () {
        el("yjErr").classList.add("d-none");
        if (!window.jsyaml) { fail("The js-yaml library could not be loaded — check your internet connection."); return; }
        try { var obj = JSON.parse(el("yjIn").value); el("yjOut").value = jsyaml.dump(obj); }
        catch (e) { fail("JSON error: " + e.message); }
    });
    el("yjCopy").addEventListener("click", function () { copyText("yjOut", el("yjCopy")); });
})();
</script>
@endsection
