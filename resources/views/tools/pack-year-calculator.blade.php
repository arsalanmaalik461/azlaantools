@extends('layouts.app')

@section('title', 'Pack Year Calculator — Free Online Tool')
@section('meta_description', 'Calculate pack years from packs smoked per day and years of smoking')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Pack Year Calculator</h1>
            <p class="lead small text-muted">Pack years are the standard way smoking history is recorded in medical notes. This tool does that arithmetic only.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="packs">Packs smoked per day</label><input type="number" class="form-control" id="packs" value="1" step="any"></div>                    <div class="mb-3"><label class="form-label" for="years">Years smoked</label><input type="number" class="form-control" id="years" value="10" step="any"></div>                    <div class="mb-3"><label class="form-label" for="perpack">Cigarettes per pack</label><input type="number" class="form-control" id="perpack" value="20" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter how many packs you smoked per day on average.</li><li>Enter for how many years.</li><li>Optionally adjust cigarettes per pack if yours differ from 20.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Pack years = packs per day x years smoked. This is a record-keeping metric used in clinical history taking — it is not a risk score and does not diagnose anything. Quitting at any point improves health outcomes. Estimate only — not medical advice.</p>
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
        var p = num("packs"), y = num("years"), per = num("perpack");
        if ([p, y, per].some(isNaN) || p <= 0 || y <= 0 || per <= 0) { out("Please enter valid positive values."); return; }
        var py = p * y;
        var totalCigs = p * per * 365.25 * y;
        out("<strong>Pack years:</strong> " + fmt(py, 1) + "<br><strong>Total packs:</strong> about " + fmt(p * 365.25 * y, 0) + "<br><strong>Total cigarettes:</strong> about " + fmt(totalCigs, 0));
    }
    bind(["packs", "years", "perpack"], calc); calc();
})();
</script>
@endsection
