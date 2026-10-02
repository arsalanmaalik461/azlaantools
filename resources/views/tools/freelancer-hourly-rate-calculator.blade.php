@extends('layouts.app')

@section('title', 'Freelancer Hourly Rate Calculator — Free Online Tool')
@section('meta_description', 'Calculate the hourly rate a freelancer must charge from target income, working hours, expenses and tax load')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Freelancer Hourly Rate Calculator</h1>
            <p class="lead small text-muted">Calculate the hourly rate a freelancer must charge from target income, working hours, expenses and tax load</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="income">Target Monthly Income (Rs)</label><input type="number" step="any" class="form-control" id="income" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="hoursDay">Billable Hours per Day</label><input type="number" step="any" class="form-control" id="hoursDay" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="daysMonth">Working Days per Month</label><input type="number" step="any" class="form-control" id="daysMonth" value="22"></div>
                    <div class="col-md-6"><label class="form-label" for="exp">Monthly Business Expenses (Rs)</label><input type="number" step="any" class="form-control" id="exp" value="30000"></div>
                    <div class="col-md-6"><label class="form-label" for="taxPct">Tax and Platform Fee Load %</label><input type="number" step="any" class="form-control" id="taxPct" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="margin">Profit Margin % on top</label><input type="number" step="any" class="form-control" id="margin" value="20"></div>
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
                        <li>Only billable hours earn money, so target income plus expenses is grossed up for tax, platform fees and margin, then divided by billable hours.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Charge above this floor when a niche is in demand. Keep billable hours realistic — admin and marketing time is unpaid.</p>
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
        var billable = n("hoursDay") * n("daysMonth"); if (billable <= 0) { showMsg("Enter billable hours and working days above zero."); return; } showMsg(""); var need = (n("income") + n("exp")) * (1 + n("margin") / 100) / (1 - n("taxPct") / 100); var rate = need / billable; setResult("Billable hours per month: " + num(billable) + "<br>Revenue you must bill monthly: " + fmt(need) + "<br><strong>Minimum hourly rate: " + fmt(rate) + "</strong><br>Equivalent daily rate: " + fmt(rate * n("hoursDay")));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
