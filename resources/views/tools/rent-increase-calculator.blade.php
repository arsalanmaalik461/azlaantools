@extends('layouts.app')

@section('title', 'Rent Increase Calculator — Free Online Tool')
@section('meta_description', 'Calculate new rent after a yearly percentage increase and total extra rent paid over the coming lease period')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Rent Increase Calculator</h1>
            <p class="lead small text-muted">Calculate new rent after a yearly percentage increase and total extra rent paid over the coming lease period</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="rent">Current Monthly Rent (Rs)</label><input type="number" step="any" class="form-control" id="rent" value="50000"></div>
                    <div class="col-md-6"><label class="form-label" for="inc">Yearly Increase %</label><input type="number" step="any" class="form-control" id="inc" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="years">Lease Period Ahead (years)</label><input type="number" step="any" class="form-control" id="years" value="3"></div>
                    </div>
                    
                    <div id="msg" class="alert alert-warning mt-3 d-none"></div>
                    <div class="border rounded p-3 mt-3">
                        <div class="small text-muted mb-1">Result</div>
                        <div id="result" class="fw-semibold">Enter values to see the result.</div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Fill in the fields above with your own figures — results update live as you type.</li>
                        <li>The yearly percent is applied again each year on the already increased rent, which is how most tenancy renewals work.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Confirm the increase clause in your rent agreement — some agreements fix a flat amount instead of a percent.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function n(id) { var v = parseFloat(document.getElementById(id).value); return isNaN(v) ? 0 : v; }
    function t(id) { return document.getElementById(id).value; }
    function fmt(x) { return "Rs " + Math.round(x).toLocaleString("en-US"); }
    function fmt2(x) { return "Rs " + x.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function pct(x) { return x.toFixed(2) + "%"; }
    function num(x) { return x.toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function showMsg(m) { var b = document.getElementById("msg"); if (m) { b.textContent = m; b.classList.remove("d-none"); } else { b.classList.add("d-none"); } }
    function setResult(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var nr = n("rent") * (1 + n("inc") / 100); var totalOld = n("rent") * 12 * n("years"); var totalNew = 0; var cur = n("rent"); for (var i = 0; i < Math.round(n("years")); i++) { cur = cur * (1 + n("inc") / 100); totalNew += cur * 12; } setResult("New monthly rent after one increase: " + fmt(nr) + "<br>Monthly increase: " + fmt(nr - n("rent")) + "<br>Total rent over the period with yearly increases: " + fmt(totalNew) + "<br>Extra paid versus no increase: " + fmt(totalNew - totalOld)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
