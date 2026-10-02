@extends('layouts.app')

@section('title', 'Rent Vs Buy Calculator — Free Online Tool')
@section('meta_description', 'Compare total cost of renting versus buying a home over chosen years with appreciation and rent increase')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Rent Vs Buy Calculator</h1>
            <p class="lead small text-muted">Compare total cost of renting versus buying a home over chosen years with appreciation and rent increase</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="rent">Monthly Rent Today (Rs)</label><input type="number" step="any" class="form-control" id="rent" value="60000"></div>
                    <div class="col-md-6"><label class="form-label" for="rentInc">Yearly Rent Increase %</label><input type="number" step="any" class="form-control" id="rentInc" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="years">Compare Over (years)</label><input type="number" step="any" class="form-control" id="years" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="price">Home Price (Rs)</label><input type="number" step="any" class="form-control" id="price" value="20000000"></div>
                    <div class="col-md-6"><label class="form-label" for="down">Down Payment (Rs)</label><input type="number" step="any" class="form-control" id="down" value="4000000"></div>
                    <div class="col-md-6"><label class="form-label" for="loanRate">Loan Yearly Rate %</label><input type="number" step="any" class="form-control" id="loanRate" value="20"></div>
                    <div class="col-md-6"><label class="form-label" for="apprec">Yearly Price Appreciation %</label><input type="number" step="any" class="form-control" id="apprec" value="8"></div>
                    <div class="col-md-6"><label class="form-label" for="maint">Yearly Maintenance and Tax (Rs)</label><input type="number" step="any" class="form-control" id="maint" value="100000"></div>
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
                        <li>Renting total uses yearly rent increases. Buying assumes the loan is fully repaid inside the period and the home is worth its appreciated price at the end.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Simplified comparison — it ignores the return you could earn by investing the down payment, plus transfer costs. Use your own realistic appreciation figure.</p>
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
        var yrs = Math.round(n("years")); var totalRent = 0, cur = n("rent"); for (var i = 0; i < yrs; i++) { totalRent += cur * 12; cur = cur * (1 + n("rentInc") / 100); } var loan = Math.max(0, n("price") - n("down")); var mr = n("loanRate") / 1200; var months = yrs * 12; var emi = loan > 0 ? (mr > 0 ? loan * mr / (1 - Math.pow(1 + mr, -months)) : loan / months) : 0; var loanPaid = emi * months; var futurePrice = n("price") * Math.pow(1 + n("apprec") / 100, yrs); var buyCost = n("down") + loanPaid + n("maint") * yrs - futurePrice; setResult("Total rent paid over the period: " + fmt(totalRent) + " (you own nothing at the end)<br>Buying: down payment + loan payments + maintenance = " + fmt(n("down") + loanPaid + n("maint") * yrs) + "<br>Home value at the end: " + fmt(futurePrice) + "<br>Net cost of buying (outflows minus final home value): " + fmt(buyCost)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
