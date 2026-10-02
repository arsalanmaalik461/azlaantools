@extends('layouts.app')

@section('title', 'Rent Split Calculator — Free Online Tool')
@section('meta_description', 'Split house rent and shared bills fairly among flatmates by room size, days stayed or equal shares')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Rent Split Calculator</h1>
            <p class="lead small text-muted">Split house rent and shared bills fairly among flatmates by room size, days stayed or equal shares</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="rent">Total Monthly Rent (Rs)</label><input type="number" step="any" class="form-control" id="rent" value="60000"></div>
                    <div class="col-md-6"><label class="form-label" for="bills">Shared Bills: electricity, gas, internet (Rs)</label><input type="number" step="any" class="form-control" id="bills" value="12000"></div>
                    <div class="col-md-6"><label class="form-label" for="w1">Person 1 Share Weight (room size or % share)</label><input type="number" step="any" class="form-control" id="w1" value="40"></div>
                    <div class="col-md-6"><label class="form-label" for="w2">Person 2 Share Weight</label><input type="number" step="any" class="form-control" id="w2" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="w3">Person 3 Share Weight</label><input type="number" step="any" class="form-control" id="w3" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="w4">Person 4 Share Weight (0 if none)</label><input type="number" step="any" class="form-control" id="w4" value="0"></div>
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
                        <li>Weights can be room sizes in square feet or simple share numbers — the total is divided in the same ratio. Set a weight to zero for an empty slot.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Days stayed proration is not included; adjust a weight down for someone who stayed part of the month.</p>
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
        var total = n("rent") + n("bills"); var w = n("w1") + n("w2") + n("w3") + n("w4"); if (w <= 0) { showMsg("Enter at least one share weight above zero."); return; } showMsg(""); setResult("Total to split: " + fmt(total) + "<br>Person 1 pays: " + fmt(total * n("w1") / w) + "<br>Person 2 pays: " + fmt(total * n("w2") / w) + "<br>Person 3 pays: " + fmt(total * n("w3") / w) + (n("w4") > 0 ? "<br>Person 4 pays: " + fmt(total * n("w4") / w) : "") + "<br>Equal share each (for comparison): " + fmt(total / [n("w1"), n("w2"), n("w3"), n("w4")].filter(function (x) { return x > 0; }).length));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
