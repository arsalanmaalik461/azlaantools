@extends('layouts.app')

@section('title', 'Personal Loan Calculator — Free Online Tool')
@section('meta_description', 'Calculate unsecured personal loan payment with processing fee and a full month by month repayment schedule')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Personal Loan Calculator</h1>
            <p class="lead small text-muted">Calculate unsecured personal loan payment with processing fee and a full month by month repayment schedule</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="amount">Loan Amount (Rs)</label><input type="number" step="any" class="form-control" id="amount" value="500000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Annual Profit / Interest Rate %</label><input type="number" step="any" class="form-control" id="rate" value="24"></div>
                    <div class="col-md-6"><label class="form-label" for="months">Term (months)</label><input type="number" step="any" class="form-control" id="months" value="36"></div>
                    <div class="col-md-6"><label class="form-label" for="feePct">Processing Fee % of loan</label><input type="number" step="any" class="form-control" id="feePct" value="1"></div>
                    </div>
                    <div class="table-responsive mt-3"><table class="table table-sm table-bordered"><thead><tr><th>Month</th><th>Installment</th><th>Principal</th><th>Interest</th><th>Balance</th></tr></thead><tbody id="schedBody"></tbody></table></div>
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
                        <li>The schedule below shows every month: installment, principal, interest and remaining balance.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Estimate only. Banks may add insurance, documentation and late charges. Rates change — verify with the official source before relying on this.</p>
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
        var P = n("amount"), r = n("rate") / 1200, m = Math.round(n("months")); if (P <= 0 || m <= 0) { showMsg("Enter a loan amount and term above zero."); return; } showMsg(""); var fee = P * n("feePct") / 100; var emi = r > 0 ? P * r / (1 - Math.pow(1 + r, -m)) : P / m; var totalPay = emi * m; var bal = P; var rows = ""; for (var i = 1; i <= m; i++) { var intr = bal * r; var prin = emi - intr; bal = Math.max(0, bal - prin); rows += "<tr><td>" + i + "</td><td>" + fmt(emi) + "</td><td>" + fmt(prin) + "</td><td>" + fmt(intr) + "</td><td>" + fmt(bal) + "</td></tr>"; } setResult("Monthly installment: " + fmt(emi) + "<br>Processing fee (upfront): " + fmt(fee) + "<br>Total interest: " + fmt(totalPay - P) + "<br>Total cost (installments + fee): " + fmt(totalPay + fee)); document.getElementById("schedBody").innerHTML = rows;
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
