@extends('layouts.app')

@section('title', 'Recurring Deposit Calculator — Free Online Tool')
@section('meta_description', 'Calculate maturity value of equal monthly deposits earning compound profit over a fixed term')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Recurring Deposit Calculator</h1>
            <p class="lead small text-muted">Calculate maturity value of equal monthly deposits earning compound profit over a fixed term</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="dep">Monthly Deposit (Rs)</label><input type="number" step="any" class="form-control" id="dep" value="10000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Yearly Profit Rate %</label><input type="number" step="any" class="form-control" id="rate" value="12"></div>
                    <div class="col-md-6"><label class="form-label" for="months">Term (months)</label><input type="number" step="any" class="form-control" id="months" value="36"></div>
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
                        <li>Each monthly deposit is added at the start of the month and the balance compounds monthly at the yearly rate.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Uses monthly compounding. Bank RD and defence certificate schemes may compound differently — check the scheme terms.</p>
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
        var r = n("rate") / 1200, m = Math.round(n("months")), d = n("dep"); if (d <= 0 || m <= 0) { showMsg("Enter a deposit and term above zero."); return; } showMsg(""); var bal = 0; for (var i = 0; i < m; i++) { bal = (bal + d) * (1 + r); } setResult("Total deposited: " + fmt(d * m) + "<br>Maturity value: " + fmt(bal) + "<br>Profit earned: " + fmt(bal - d * m));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
