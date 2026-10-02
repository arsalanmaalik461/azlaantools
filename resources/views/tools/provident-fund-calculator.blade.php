@extends('layouts.app')

@section('title', 'Provident Fund Calculator — Free Online Tool')
@section('meta_description', 'Project provident fund balance from salary, employee and employer contribution percent and yearly profit rate')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Provident Fund Calculator</h1>
            <p class="lead small text-muted">Project provident fund balance from salary, employee and employer contribution percent and yearly profit rate</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="salary">Monthly Basic Salary (Rs)</label><input type="number" step="any" class="form-control" id="salary" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="empPct">Employee Contribution % of basic</label><input type="number" step="any" class="form-control" id="empPct" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="emprPct">Employer Contribution % of basic</label><input type="number" step="any" class="form-control" id="emprPct" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="profit">Expected Yearly Profit Rate %</label><input type="number" step="any" class="form-control" id="profit" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="years">Years of Service</label><input type="number" step="any" class="form-control" id="years" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="opening">Opening PF Balance (Rs)</label><input type="number" step="any" class="form-control" id="opening" value="0"></div>
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
                        <li>Both employee and employer contributions are added every month and the balance grows at the yearly profit rate.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Projection only. Actual PF profit rates are declared yearly by the fund and salary may change over time.</p>
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
        var monthly = n("salary") * (n("empPct") + n("emprPct")) / 100; var bal = n("opening"); var r = n("profit") / 1200; var monthsTotal = Math.round(n("years") * 12); for (var i = 0; i < monthsTotal; i++) { bal = bal * (1 + r) + monthly; } var contributed = monthly * monthsTotal + n("opening"); setResult("Monthly contribution (employee + employer): " + fmt(monthly) + "<br>Total contributed: " + fmt(contributed) + "<br>Projected PF balance: " + fmt(bal) + "<br>Profit earned: " + fmt(bal - contributed)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
