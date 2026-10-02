@extends('layouts.app')

@section('title', 'Rule Of 72 Calculator — Free Online Tool')
@section('meta_description', 'Estimate years needed for money to double at a given return rate, or the rate needed to double in a set time')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Rule Of 72 Calculator</h1>
            <p class="lead small text-muted">Estimate years needed for money to double at a given return rate, or the rate needed to double in a set time</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="rate">Yearly Return Rate %</label><input type="number" step="any" class="form-control" id="rate" value="12"></div>
                    <div class="col-md-6"><label class="form-label" for="targetYears">Or: Years in Which You Want Money to Double</label><input type="number" step="any" class="form-control" id="targetYears" value="6"></div>
                    <div class="col-md-6"><label class="form-label" for="amount">Optional Amount (Rs)</label><input type="number" step="any" class="form-control" id="amount" value="100000"></div>
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
                        <li>Divide 72 by the yearly rate to estimate doubling years, or divide 72 by your target years to find the rate you need.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> The Rule of 72 is a quick estimate — exact compounding gives a slightly different figure, closest near 8 percent.</p>
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
        var yrs = n("rate") > 0 ? 72 / n("rate") : 0; var need = n("targetYears") > 0 ? 72 / n("targetYears") : 0; setResult("At " + num(n("rate")) + "% yearly return, money doubles in about " + num(yrs) + " years.<br>To double in " + num(n("targetYears")) + " years you need about " + pct(need) + " yearly return." + (n("amount") > 0 ? "<br>" + fmt(n("amount")) + " becomes about " + fmt(n("amount") * 2) + " in that doubling time." : "")); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
