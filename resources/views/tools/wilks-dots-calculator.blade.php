@extends('layouts.app')

@section('title', 'Wilks and DOTS Calculator — Free Online Tool')
@section('meta_description', 'Calculate Wilks and DOTS powerlifting scores to compare lifts across body weights')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Wilks and DOTS Calculator</h1>
            <p class="lead small text-muted">Wilks and DOTS both scale a powerlifting total by body weight so lifters of different sizes can be compared. Enter yours to get both scores.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male">Male</option><option value="female">Female</option></select></div>                    <div class="mb-3"><label class="form-label" for="bw">Body weight (kg)</label><input type="number" class="form-control" id="bw" value="83" step="any"></div>                    <div class="mb-3"><label class="form-label" for="total">Total lifted — squat + bench + deadlift (kg)</label><input type="number" class="form-control" id="total" value="500" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose your sex.</li><li>Enter your body weight on the day.</li><li>Enter your three-lift total and read both scores.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Wilks uses the original published polynomial coefficients; DOTS uses its published 2019 polynomial coefficients. Both are defined for typical competition body weights (roughly 40 to 150 kg) and are only meaningful for the full three-lift total. Estimate only — not medical advice.</p>
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
        var sex = document.getElementById("sex").value;
        var x = num("bw"), total = num("total");
        if (isNaN(x) || isNaN(total) || x <= 0 || total <= 0) { out("Please enter valid body weight and total values."); return; }
        var wa, wb, wc, wd, we, wf, da, db, dc, dd, de;
        if (sex === "male") { wa = -216.0475144; wb = 16.2606339; wc = -0.002388645; wd = -0.00113732; we = 7.01863e-06; wf = -1.291e-08; da = -307.75076; db = 24.0900756; dc = -0.191331922; dd = 0.0007391293; de = -0.000001093; }
        else { wa = 594.31747775582; wb = -27.23842536447; wc = 0.82112226871; wd = -0.00930733913; we = 0.00004731582; wf = -0.00000009054; da = -57.96288; db = 13.6175032; dc = -0.1126655499; dd = 0.0005158568; de = -0.0000010706; }
        var wDen = wa + wb * x + wc * Math.pow(x, 2) + wd * Math.pow(x, 3) + we * Math.pow(x, 4) + wf * Math.pow(x, 5);
        var dDen = da + db * x + dc * Math.pow(x, 2) + dd * Math.pow(x, 3) + de * Math.pow(x, 4);
        if (wDen <= 0 || dDen <= 0) { out("Body weight is outside the range these formulas are defined for."); return; }
        var wilks = total * 500 / wDen;
        var dots = total * 500 / dDen;
        out("<strong>Wilks score:</strong> " + fmt(wilks, 2) + "<br><strong>DOTS score:</strong> " + fmt(dots, 2));
    }
    bind(["sex", "bw", "total"], calc); calc();
})();
</script>
@endsection
