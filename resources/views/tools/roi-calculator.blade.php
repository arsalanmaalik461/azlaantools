@extends('layouts.app')

@section('title', 'ROI Calculator — Free Online Tool')
@section('meta_description', 'Calculate return on investment percentage and annualized return from amount invested, gain and holding period')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">ROI Calculator</h1>
            <p class="lead small text-muted">Calculate return on investment percentage and annualized return from amount invested, gain and holding period</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="invested">Amount Invested (Rs)</label><input type="number" step="any" class="form-control" id="invested" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="returned">Final Value / Amount Returned (Rs)</label><input type="number" step="any" class="form-control" id="returned" value="130000"></div>
                    <div class="col-md-6"><label class="form-label" for="years">Holding Period (years)</label><input type="number" step="any" class="form-control" id="years" value="2"></div>
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
                        <li>ROI is gain divided by amount invested. Annualized return spreads the same growth evenly over the holding years.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Past return does not guarantee future return. Fees and taxes are not deducted unless you include them in the final value.</p>
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
        if (n("invested") <= 0) { showMsg("Enter an invested amount above zero."); return; } showMsg(""); var gain = n("returned") - n("invested"); var roi = gain / n("invested") * 100; var ann = n("years") > 0 && n("returned") > 0 ? (Math.pow(n("returned") / n("invested"), 1 / n("years")) - 1) * 100 : 0; setResult("Gain / Loss: " + fmt(gain) + "<br>Total ROI: " + pct(roi) + "<br>Annualized return (CAGR): " + pct(ann));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
