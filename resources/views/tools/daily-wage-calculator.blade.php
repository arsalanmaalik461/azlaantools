@extends('layouts.app')

@section('title', 'Daily Wage Calculator — Free Online Tool')
@section('meta_description', 'Enter your daily wage and working days to find weekly and monthly earnings.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Daily Wage Calculator</h1>
            <p class="lead small text-muted">Enter your daily wage and working days to find weekly and monthly earnings.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="rate">Daily Wage Rate (Rs)</label><input type="number" step="any" class="form-control" id="rate" value="1500"></div>
                    <div class="col-md-6"><label class="form-label" for="days">Working Days</label><input type="number" step="any" class="form-control" id="days" value="26"></div>
                    <div class="col-md-6"><label class="form-label" for="otDays">Overtime / Extra Days</label><input type="number" step="any" class="form-control" id="otDays" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="otMult">Overtime Day Multiplier</label><input type="number" step="any" class="form-control" id="otMult" value="1.5"></div>
                    <div class="col-md-6"><label class="form-label" for="ded">Deductions: advance etc (Rs)</label><input type="number" step="any" class="form-control" id="ded" value="0"></div>
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
                        <li>Normal pay is daily rate times working days. Extra days are paid at the overtime multiplier you set.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Minimum wage rates are set by each province and change over time. Rates change — verify with the official source before relying on this.</p>
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
        var normal = n("rate") * n("days"); var ot = n("rate") * n("otDays") * n("otMult"); var gross = normal + ot; setResult("Normal pay: " + fmt(normal) + "<br>Overtime pay: " + fmt(ot) + "<br>Gross pay: " + fmt(gross) + "<br><strong>Net pay after deductions: " + fmt(gross - n("ded")) + "</strong><br>Weekly equivalent (6 days): " + fmt(n("rate") * 6)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
