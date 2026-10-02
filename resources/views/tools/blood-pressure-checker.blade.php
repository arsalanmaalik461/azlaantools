@extends('layouts.app')

@section('title', 'Blood Pressure Checker — Free Online Tool')
@section('meta_description', 'Enter systolic and diastolic readings to see the category on the standard AHA blood pressure chart')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Blood Pressure Checker</h1>
            <p class="lead small text-muted">Enter a systolic and diastolic reading to see which category it falls into on the American Heart Association chart.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="sys">Systolic — top number (mmHg)</label><input type="number" class="form-control" id="sys" value="120" step="any"></div>                    <div class="mb-3"><label class="form-label" for="dia">Diastolic — bottom number (mmHg)</label><input type="number" class="form-control" id="dia" value="80" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the top (systolic) number.</li><li>Enter the bottom (diastolic) number.</li><li>Your AHA category appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Categories follow the published AHA bands. A single reading is not a diagnosis — blood pressure varies through the day, and repeated readings and clinical advice matter. Seek urgent care for readings in the crisis range with symptoms. Estimate only — not medical advice.</p>
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
        var s = num("sys"), d = num("dia");
        if (isNaN(s) || isNaN(d) || s <= 0 || d <= 0) { out("Please enter valid systolic and diastolic values."); return; }
        var cat;
        if (s > 180 || d > 120) { cat = "Hypertensive crisis — seek urgent medical care, especially with chest pain, breathlessness or vision changes"; }
        else if (s >= 140 || d >= 90) { cat = "High blood pressure — Stage 2 hypertension range"; }
        else if ((s >= 130 && s <= 139) || (d >= 80 && d <= 89)) { cat = "High blood pressure — Stage 1 hypertension range"; }
        else if (s >= 120 && s <= 129 && d < 80) { cat = "Elevated"; }
        else if (s < 120 && d < 80) { cat = "Normal"; }
        else { cat = "Stage 1 hypertension range (driven by the diastolic reading)"; }
        out("<strong>Reading:</strong> " + fmt(s, 0) + " / " + fmt(d, 0) + " mmHg<br><strong>AHA category:</strong> " + cat);
    }
    bind(["sys", "dia"], calc); calc();
})();
</script>
@endsection
