@extends('layouts.app')

@section('title', 'A1C to Average Glucose Converter — Free Online Tool')
@section('meta_description', 'Convert HbA1c percentage to estimated average glucose and back in mg per dL and mmol per L')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">A1C to Average Glucose Converter</h1>
            <p class="lead small text-muted">Convert HbA1c to estimated average glucose (eAG), or convert an average glucose reading back to A1C, using the published ADAG formula.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="mode">Conversion direction</label><select class="form-select" id="mode"><option value="a1c">HbA1c (%) to average glucose</option><option value="eag">Average glucose (mg/dL) to HbA1c</option></select></div>                    <div class="mb-3"><label class="form-label" for="value">Value</label><input type="number" class="form-control" id="value" value="6" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose the conversion direction.</li><li>Enter your HbA1c percentage or your average glucose in mg/dL.</li><li>The converted values in mg/dL and mmol/L appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the ADAG study formula: eAG (mg/dL) = 28.7 x A1C - 46.7. Laboratory results vary from estimates. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var mode = document.getElementById("mode").value;
        var v = num("value");
        if (isNaN(v) || v <= 0) { out("Please enter a valid positive value."); return; }
        var a1c, mg;
        if (mode === "a1c") { a1c = v; mg = 28.7 * a1c - 46.7; } else { mg = v; a1c = (mg + 46.7) / 28.7; }
        if (mg <= 0 || a1c <= 0) { out("That value is outside the range this formula can convert."); return; }
        out("<strong>HbA1c:</strong> " + fmt(a1c, 1) + "%<br><strong>Estimated average glucose:</strong> " + fmt(mg, 0) + " mg/dL (" + fmt(mg / 18.01559, 1) + " mmol/L)");
    }
    bind(["mode", "value"], calc); calc();
})();
</script>
@endsection
