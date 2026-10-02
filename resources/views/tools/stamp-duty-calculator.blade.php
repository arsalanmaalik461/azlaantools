@extends('layouts.app')

@section('title', 'Stamp Duty Calculator — Free Online Tool')
@section('meta_description', 'Enter your agreement or document value and calculate the stamp paper cost by percentage.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Stamp Duty Calculator</h1>
            <p class="lead small text-muted">Enter your agreement or document value and calculate the stamp paper cost by percentage.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="value">Agreement / Document Value (Rs)</label><input type="number" step="any" class="form-control" id="value" value="1000000"></div>
                    <div class="col-md-6"><label class="form-label" for="pctRate">Stamp Duty % (editable)</label><input type="number" step="any" class="form-control" id="pctRate" value="1"></div>
                    <div class="col-md-6"><label class="form-label" for="fixed">E-Stamp / Issuance Fixed Fee (Rs)</label><input type="number" step="any" class="form-control" id="fixed" value="0"></div>
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
                        <li>Stamp duty is the document value multiplied by the duty percent you select or edit for your agreement type and province.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Duty rates differ by document type and province. Rates change — verify with the official source before relying on this.</p>
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
        var duty = n("value") * n("pctRate") / 100; setResult("Stamp duty: " + fmt(duty) + "<br>Fixed fee: " + fmt(n("fixed")) + "<br><strong>Total stamp paper cost: " + fmt(duty + n("fixed")) + "</strong>"); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
