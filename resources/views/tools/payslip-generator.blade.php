@extends('layouts.app')

@section('title', 'Payslip Generator — Free Online Tool')
@section('meta_description', 'Generate a complete printable payslip with earnings, allowances, tax, EOBI and other deductions and net pay')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Payslip Generator</h1>
            <p class="lead small text-muted">Generate a complete printable payslip with earnings, allowances, tax, EOBI and other deductions and net pay</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="emp">Employee Name</label><input type="text" class="form-control" id="emp" value="Ahmed Khan"></div>
                    <div class="col-md-6"><label class="form-label" for="desig">Designation</label><input type="text" class="form-control" id="desig" value="Sales Officer"></div>
                    <div class="col-md-6"><label class="form-label" for="month">Pay Month</label><input type="text" class="form-control" id="month" value="October 2026"></div>
                    <div class="col-md-6"><label class="form-label" for="company">Company Name</label><input type="text" class="form-control" id="company" value="Azlaan Enterprises"></div>
                    <div class="col-md-6"><label class="form-label" for="basic">Basic Salary (Rs)</label><input type="number" step="any" class="form-control" id="basic" value="60000"></div>
                    <div class="col-md-6"><label class="form-label" for="house">House Rent Allowance (Rs)</label><input type="number" step="any" class="form-control" id="house" value="15000"></div>
                    <div class="col-md-6"><label class="form-label" for="conv">Conveyance Allowance (Rs)</label><input type="number" step="any" class="form-control" id="conv" value="5000"></div>
                    <div class="col-md-6"><label class="form-label" for="otherEarn">Other Allowances (Rs)</label><input type="number" step="any" class="form-control" id="otherEarn" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="tax">Less: Income Tax (Rs)</label><input type="number" step="any" class="form-control" id="tax" value="2000"></div>
                    <div class="col-md-6"><label class="form-label" for="eobi">Less: Employee EOBI (Rs, editable)</label><input type="number" step="any" class="form-control" id="eobi" value="370"></div>
                    <div class="col-md-6"><label class="form-label" for="otherDed">Less: Other Deductions / Advance (Rs)</label><input type="number" step="any" class="form-control" id="otherDed" value="0"></div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" onclick="window.print()">Print Payslip</button><style>@media print { body * { visibility: hidden; } #payslipDoc, #payslipDoc * { visibility: visible; } #payslipDoc { position: absolute; left: 0; top: 0; width: 100%; } }</style><div id="payslipDoc" class="border rounded p-4 mt-3"><h3 class="h5 text-center" id="psCompany">Company</h3><p class="text-center small mb-3">Payslip for <span id="psMonth"></span></p><p class="mb-1"><strong>Employee:</strong> <span id="psEmp"></span> &nbsp; <strong>Designation:</strong> <span id="psDesig"></span></p><table class="table table-bordered table-sm"><thead><tr><th>Earnings</th><th>Amount</th><th>Deductions</th><th>Amount</th></tr></thead><tbody><tr><td>Basic Salary</td><td id="psBasic"></td><td>Income Tax</td><td id="psTax"></td></tr><tr><td>House Rent Allowance</td><td id="psHouse"></td><td>EOBI</td><td id="psEobi"></td></tr><tr><td>Conveyance Allowance</td><td id="psConv"></td><td>Other Deductions</td><td id="psOtherD"></td></tr><tr><td>Other Allowances</td><td id="psOtherE"></td><td></td><td></td></tr><tr><th>Gross Pay</th><th id="psGross"></th><th>Total Deductions</th><th id="psDed"></th></tr></tbody></table><p class="fs-5"><strong>Net Pay: <span id="psNet"></span></strong></p><p class="small text-muted mb-0">Computer generated payslip. Signature: ______________________</p></div>
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
                        <li>Fill employee details and salary heads. The printable payslip below updates live — press Print Payslip and save or print it.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> EOBI employee share is an editable field because the rate follows the notified minimum wage. Rates change — verify with the official source before relying on this. Keep a signed copy for payroll records.</p>
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
        var gross = n("basic") + n("house") + n("conv") + n("otherEarn"); var ded = n("tax") + n("eobi") + n("otherDed"); var net = gross - ded; setResult("Gross pay: " + fmt(gross) + "<br>Total deductions: " + fmt(ded) + "<br><strong>Net pay: " + fmt(net) + "</strong>"); document.getElementById("psCompany").textContent = t("company"); document.getElementById("psEmp").textContent = t("emp"); document.getElementById("psDesig").textContent = t("desig"); document.getElementById("psMonth").textContent = t("month"); document.getElementById("psBasic").textContent = fmt(n("basic")); document.getElementById("psHouse").textContent = fmt(n("house")); document.getElementById("psConv").textContent = fmt(n("conv")); document.getElementById("psOtherE").textContent = fmt(n("otherEarn")); document.getElementById("psGross").textContent = fmt(gross); document.getElementById("psTax").textContent = fmt(n("tax")); document.getElementById("psEobi").textContent = fmt(n("eobi")); document.getElementById("psOtherD").textContent = fmt(n("otherDed")); document.getElementById("psDed").textContent = fmt(ded); document.getElementById("psNet").textContent = fmt(net); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
