@extends('layouts.app')

@section('title', 'Rental Yield Calculator — Free Online Tool')
@section('meta_description', 'Calculate gross and net rental yield of a property from price, monthly rent, and yearly running costs')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Rental Yield Calculator</h1>
            <p class="lead small text-muted">Calculate gross and net rental yield of a property from price, monthly rent, and yearly running costs</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="price">Property Price (Rs)</label><input type="number" step="any" class="form-control" id="price" value="10000000"></div>
                    <div class="col-md-6"><label class="form-label" for="rent">Monthly Rent (Rs)</label><input type="number" step="any" class="form-control" id="rent" value="45000"></div>
                    <div class="col-md-6"><label class="form-label" for="costs">Yearly Running Costs: tax, maintenance, vacant months (Rs)</label><input type="number" step="any" class="form-control" id="costs" value="60000"></div>
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
                        <li>Gross yield is yearly rent divided by price. Net yield deducts yearly running costs first.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Yield excludes price appreciation and financing cost — use the Property ROI tool for the full picture.</p>
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
        if (n("price") <= 0) { showMsg("Enter a property price above zero."); return; } showMsg(""); var yearly = n("rent") * 12; var gross = yearly / n("price") * 100; var net = (yearly - n("costs")) / n("price") * 100; setResult("Yearly rent: " + fmt(yearly) + "<br>Gross rental yield: " + pct(gross) + "<br>Net rental yield (after costs): " + pct(net));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
