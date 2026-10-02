@extends('layouts.app')

@section('title', 'Adult Height Predictor Calculator — Free Online Tool')
@section('meta_description', 'Predict a child adult height from mother and father height with the mid parental method')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Adult Height Predictor Calculator</h1>
            <p class="lead small text-muted">The mid-parental method gives a rough guide to how tall a child is likely to be as an adult, based on both parents heights.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="father">Father height (cm)</label><input type="number" class="form-control" id="father" value="172" step="any"></div>                    <div class="mb-3"><label class="form-label" for="mother">Mother height (cm)</label><input type="number" class="form-control" id="mother" value="160" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Child</label><select class="form-select" id="sex"><option value="boy">Boy</option><option value="girl">Girl</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the father height in centimetres.</li><li>Enter the mother height in centimetres.</li><li>Choose boy or girl and read the predicted adult height and range.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Mid-parental height: boys = (father + mother + 13) / 2, girls = (father + mother - 13) / 2. Most children finish within about 8.5 cm of this target, but nutrition, health and genetics all play a part. Estimate only — not medical advice.</p>
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
        var f = num("father"), m = num("mother"), sex = document.getElementById("sex").value;
        if (isNaN(f) || isNaN(m) || f <= 0 || m <= 0) { out("Please enter valid heights for both parents."); return; }
        var mid = sex === "boy" ? (f + m + 13) / 2 : (f + m - 13) / 2;
        out("<strong>Predicted adult height:</strong> about " + fmt(mid, 1) + " cm<br><strong>Likely range:</strong> " + fmt(mid - 8.5, 1) + " cm to " + fmt(mid + 8.5, 1) + " cm");
    }
    bind(["father", "mother", "sex"], calc); calc();
})();
</script>
@endsection
