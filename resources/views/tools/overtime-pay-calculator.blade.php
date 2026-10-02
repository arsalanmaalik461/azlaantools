@extends('layouts.app')

@section('title', 'Overtime Pay Calculator — Free Online Tool')
@section('meta_description', 'Enter your basic salary and overtime hours to find your per-hour rate and total overtime pay.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Overtime Pay Calculator</h1>
            <p class="lead small text-muted">Enter your basic salary and overtime hours to find your per-hour rate and total overtime pay.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="salary">Monthly Basic Salary (Rs)</label><input type="number" step="any" class="form-control" id="salary" value="60000"></div>
                    <div class="col-md-6"><label class="form-label" for="days">Working Days per Month (editable)</label><input type="number" step="any" class="form-control" id="days" value="26"></div>
                    <div class="col-md-6"><label class="form-label" for="hoursDay">Hours per Day</label><input type="number" step="any" class="form-control" id="hoursDay" value="8"></div>
                    <div class="col-md-6"><label class="form-label" for="otHours">Overtime Hours</label><input type="number" step="any" class="form-control" id="otHours" value="20"></div>
                    <div class="col-md-6"><label class="form-label" for="mult">Overtime Multiplier</label><select class="form-select" id="mult"><option value="1.5">1.5x</option><option value="2" selected>2x (double)</option></select></div>
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
                        <li>Hourly rate is monthly salary divided by working days and hours per day, then multiplied by the overtime multiplier you select.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Pakistan factories commonly pay double rate for overtime. Days per month and the multiplier are editable — confirm your company policy. Rates change — verify with the official source before relying on this.</p>
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
        var hrs = n("days") * n("hoursDay"); if (hrs <= 0) { showMsg("Working days and hours per day must be above zero."); return; } showMsg(""); var hourly = n("salary") / hrs; var otRate = hourly * n("mult"); var total = otRate * n("otHours"); setResult("Normal hourly rate: " + fmt2(hourly) + "<br>Overtime hourly rate: " + fmt2(otRate) + "<br>Total overtime pay: " + fmt(total));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
