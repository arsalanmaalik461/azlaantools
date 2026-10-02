@extends('layouts.app')

@section('title', 'Savings Goal Calculator — Free Online Tool')
@section('meta_description', 'Calculate the monthly saving required to reach a target amount by a target date at an expected profit rate')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Savings Goal Calculator</h1>
            <p class="lead small text-muted">Calculate the monthly saving required to reach a target amount by a target date at an expected profit rate</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="target">Target Amount (Rs)</label><input type="number" step="any" class="form-control" id="target" value="500000"></div>
                    <div class="col-md-6"><label class="form-label" for="have">Already Saved (Rs)</label><input type="number" step="any" class="form-control" id="have" value="50000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Expected Yearly Profit Rate %</label><input type="number" step="any" class="form-control" id="rate" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="months">Months to Reach the Goal</label><input type="number" step="any" class="form-control" id="months" value="24"></div>
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
                        <li>The monthly figure is the annuity payment that fills the gap between your target and what your current savings will grow to.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Profit rate is an assumption, not a promise. If returns are lower, the required monthly saving will be higher.</p>
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
        var m = Math.round(n("months")), r = n("rate") / 1200, gap = n("target") - n("have") * Math.pow(1 + r, m); if (m <= 0 || n("target") <= 0) { showMsg("Enter a target amount and months above zero."); return; } showMsg(""); var pmt = r > 0 ? gap * r / (Math.pow(1 + r, m) - 1) : gap / m; if (pmt < 0) { pmt = 0; } setResult("Amount still needed: " + fmt(n("target") - n("have")) + "<br>Required monthly saving: " + fmt(pmt) + "<br>Your existing savings grow to: " + fmt(n("have") * Math.pow(1 + r, m)));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
