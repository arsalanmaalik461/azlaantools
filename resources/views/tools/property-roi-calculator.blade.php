@extends('layouts.app')

@section('title', 'Property ROI Calculator — Free Online Tool')
@section('meta_description', 'Calculate total property return combining rent received, price appreciation and buying and selling costs')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Property ROI Calculator</h1>
            <p class="lead small text-muted">Calculate total property return combining rent received, price appreciation and buying and selling costs</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="buy">Purchase Price (Rs)</label><input type="number" step="any" class="form-control" id="buy" value="10000000"></div>
                    <div class="col-md-6"><label class="form-label" for="buyCost">Buying Costs: transfer, commission etc (Rs)</label><input type="number" step="any" class="form-control" id="buyCost" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="rentMonthly">Monthly Rent Received (Rs)</label><input type="number" step="any" class="form-control" id="rentMonthly" value="40000"></div>
                    <div class="col-md-6"><label class="form-label" for="years">Holding Period (years)</label><input type="number" step="any" class="form-control" id="years" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="sell">Expected Selling Price (Rs)</label><input type="number" step="any" class="form-control" id="sell" value="14000000"></div>
                    <div class="col-md-6"><label class="form-label" for="sellCost">Selling Costs (Rs)</label><input type="number" step="any" class="form-control" id="sellCost" value="200000"></div>
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
                        <li>ROI combines rent received and price appreciation, minus buying and selling costs, divided by total invested.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Projection only — future prices and rents are your own estimates, not a guarantee.</p>
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
        var invested = n("buy") + n("buyCost"); var rentTotal = n("rentMonthly") * 12 * n("years"); var gain = (n("sell") - n("sellCost")) + rentTotal - invested; if (invested <= 0) { showMsg("Enter a purchase price above zero."); return; } showMsg(""); var roi = gain / invested * 100; var annual = n("years") > 0 ? roi / n("years") : 0; setResult("Total invested: " + fmt(invested) + "<br>Total rent received: " + fmt(rentTotal) + "<br>Net gain: " + fmt(gain) + "<br>Total ROI: " + pct(roi) + "<br>Simple average yearly ROI: " + pct(annual));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
