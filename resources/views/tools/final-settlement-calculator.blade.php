@extends('layouts.app')

@section('title', 'Final Settlement Calculator — Free Online Tool')
@section('meta_description', 'Total a full and final settlement with last salary, leave encashment, gratuity, notice pay and deductions')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Final Settlement Calculator</h1>
            <p class="lead small text-muted">Total a full and final settlement with last salary, leave encashment, gratuity, notice pay and deductions</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="lastBasic">Last Monthly Basic Salary (Rs)</label><input type="number" step="any" class="form-control" id="lastBasic" value="80000"></div>
                    <div class="col-md-6"><label class="form-label" for="daysWorked">Unpaid Days Worked in Final Month</label><input type="number" step="any" class="form-control" id="daysWorked" value="15"></div>
                    <div class="col-md-6"><label class="form-label" for="daysMonth">Days in Month Basis</label><input type="number" step="any" class="form-control" id="daysMonth" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="leaveDays">Unused Leave Days</label><input type="number" step="any" class="form-control" id="leaveDays" value="20"></div>
                    <div class="col-md-6"><label class="form-label" for="gratuity">Gratuity Amount (Rs)</label><input type="number" step="any" class="form-control" id="gratuity" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="notice">Notice Pay / Other Dues (Rs)</label><input type="number" step="any" class="form-control" id="notice" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="advance">Less: Advance / Loan (Rs)</label><input type="number" step="any" class="form-control" id="advance" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="tax">Less: Tax and Other Deductions (Rs)</label><input type="number" step="any" class="form-control" id="tax" value="0"></div>
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
                        <li>Per day salary is basic divided by the month basis. Leave encashment and gratuity use the commonly applied Pakistan practice of basic pay based calculation — edit the gratuity amount if your company policy or labour formula gives a different figure.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Settlement rules vary by contract, province and company policy. Rates change — verify with the official source before relying on this.</p>
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
        if (n("daysMonth") <= 0) { showMsg("Days in month basis must be above zero."); return; } showMsg(""); var perDay = n("lastBasic") / n("daysMonth"); var unpaid = perDay * n("daysWorked"); var leave = perDay * n("leaveDays"); var dues = unpaid + leave + n("gratuity") + n("notice"); var ded = n("advance") + n("tax"); setResult("Unpaid salary: " + fmt(unpaid) + "<br>Leave encashment: " + fmt(leave) + "<br>Gratuity: " + fmt(n("gratuity")) + "<br>Total dues: " + fmt(dues) + "<br>Total deductions: " + fmt(ded) + "<br><strong>Net payable settlement: " + fmt(dues - ded) + "</strong>");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
