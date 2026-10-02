@extends('layouts.app')

@section('title', 'Remittance Comparison Calculator — Free Online Tool')
@section('meta_description', 'Enter the exchange rate and fee of two services, and compare which gives a better deal on the money sent home.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Remittance Comparison Calculator</h1>
            <p class="lead small text-muted">Enter the exchange rate and fee of two services, and compare which gives a better deal on the money sent home.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="amount">Amount to Send (foreign currency)</label><input type="number" step="any" class="form-control" id="amount" value="1000"></div>
                    <div class="col-md-6"><label class="form-label" for="rateA">Service A Exchange Rate (PKR per unit)</label><input type="number" step="any" class="form-control" id="rateA" value="278"></div>
                    <div class="col-md-6"><label class="form-label" for="feeA">Service A Fee (foreign currency)</label><input type="number" step="any" class="form-control" id="feeA" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="rateB">Service B Exchange Rate (PKR per unit)</label><input type="number" step="any" class="form-control" id="rateB" value="276"></div>
                    <div class="col-md-6"><label class="form-label" for="feeB">Service B Fee (foreign currency)</label><input type="number" step="any" class="form-control" id="feeB" value="0"></div>
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
                        <li>Fee is deducted from the sent amount first, then the remaining amount is converted at the rate you entered for each service.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Rates and fees are the ones you type in — no live exchange rates are fetched. Check the live rate before sending.</p>
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
        var a = (n("amount") - n("feeA")) * n("rateA"); var b = (n("amount") - n("feeB")) * n("rateB"); var better = a >= b ? "Service A" : "Service B"; setResult("Service A delivers: " + fmt(a) + "<br>Service B delivers: " + fmt(b) + "<br>Difference: " + fmt(Math.abs(a - b)) + "<br>Better deal: " + better); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
