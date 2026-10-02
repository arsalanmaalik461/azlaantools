@extends('layouts.app')

@section('title', 'Salary Increase Calculator — Free Online Tool')
@section('meta_description', 'Calculate new salary, monthly increase and yearly impact after a percentage or fixed amount raise')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Salary Increase Calculator</h1>
            <p class="lead small text-muted">Calculate new salary, monthly increase and yearly impact after a percentage or fixed amount raise</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="mode">Increase Type</label><select class="form-select" id="mode"><option value="pct" selected>Percentage increase</option><option value="flat">Fixed amount increase</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="salary">Current Monthly Salary (Rs)</label><input type="number" step="any" class="form-control" id="salary" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="inc">Increase % OR Fixed Amount (Rs)</label><input type="number" step="any" class="form-control" id="inc" value="10"></div>
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
                        <li>In percentage mode the increase value is a percent of current salary. In fixed mode it is a straight rupee amount.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Gross figures only — your income tax slab may change with the new salary, so check net pay with the salary tax tool.</p>
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
        var amt = t("mode") === "pct" ? n("salary") * n("inc") / 100 : n("inc"); var ns = n("salary") + amt; setResult("Monthly increase: " + fmt(amt) + "<br><strong>New monthly salary: " + fmt(ns) + "</strong><br>New yearly salary: " + fmt(ns * 12) + "<br>Extra income per year: " + fmt(amt * 12)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
