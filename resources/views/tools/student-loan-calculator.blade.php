@extends('layouts.app')

@section('title', 'Student Loan Calculator — Free Online Tool')
@section('meta_description', 'Calculate education loan payment, total interest and payoff time including the study period grace years')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Student Loan Calculator</h1>
            <p class="lead small text-muted">Calculate education loan payment, total interest and payoff time including the study period grace years</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="amount">Loan Amount (Rs)</label><input type="number" step="any" class="form-control" id="amount" value="800000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Yearly Rate %</label><input type="number" step="any" class="form-control" id="rate" value="15"></div>
                    <div class="col-md-6"><label class="form-label" for="grace">Study / Grace Period (years)</label><input type="number" step="any" class="form-control" id="grace" value="4"></div>
                    <div class="col-md-6"><label class="form-label" for="repayYears">Repayment Period (years)</label><input type="number" step="any" class="form-control" id="repayYears" value="5"></div>
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
                        <li>During the grace years interest keeps adding to the loan yearly. Repayment then runs as a normal monthly installment loan on the grown balance.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Estimate only — some schemes charge simple interest or subsidize the grace period. Rates change — verify with the official source before relying on this.</p>
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
        var P = n("amount"), r = n("rate") / 100; if (P <= 0 || n("repayYears") <= 0) { showMsg("Enter a loan amount and repayment period above zero."); return; } showMsg(""); var grown = P * Math.pow(1 + r, n("grace")); var graceInt = grown - P; var mr = r / 12, mm = Math.round(n("repayYears") * 12); var emi = mr > 0 ? grown * mr / (1 - Math.pow(1 + mr, -mm)) : grown / mm; setResult("Interest added during grace period: " + fmt(graceInt) + "<br>Balance when repayment starts: " + fmt(grown) + "<br>Monthly installment: " + fmt(emi) + "<br>Total repayment: " + fmt(emi * mm) + "<br>Total interest overall: " + fmt(graceInt + emi * mm - grown));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
