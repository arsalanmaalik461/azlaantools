@extends('layouts.app')

@section('title', 'Hourly To Salary Calculator — Free Online Tool')
@section('meta_description', 'Convert hourly wage into daily, weekly, monthly and yearly pay and convert a salary back into an hourly rate')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Hourly To Salary Calculator</h1>
            <p class="lead small text-muted">Convert hourly wage into daily, weekly, monthly and yearly pay and convert a salary back into an hourly rate</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="mode">Convert</label><select class="form-select" id="mode"><option value="h2s" selected>Hourly rate to salary</option><option value="s2h">Salary to hourly rate</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="amount">Hourly Rate OR Monthly Salary (Rs)</label><input type="number" step="any" class="form-control" id="amount" value="500"></div>
                    <div class="col-md-6"><label class="form-label" for="hoursDay">Hours per Day</label><input type="number" step="any" class="form-control" id="hoursDay" value="8"></div>
                    <div class="col-md-6"><label class="form-label" for="daysWeek">Days per Week</label><input type="number" step="any" class="form-control" id="daysWeek" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="weeksYear">Weeks per Year</label><input type="number" step="any" class="form-control" id="weeksYear" value="52"></div>
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
                        <li>Pick the direction first. In hourly mode the amount is your hourly rate; in salary mode the amount is your monthly salary.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Paid leave and holidays are not adjusted — the year is taken as the weeks you enter.</p>
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
        var hpw = n("hoursDay") * n("daysWeek"); if (hpw <= 0) { showMsg("Enter hours per day and days per week above zero."); return; } showMsg(""); var hourly, daily, weekly, monthly, yearly; if (t("mode") === "h2s") { hourly = n("amount"); daily = hourly * n("hoursDay"); weekly = daily * n("daysWeek"); yearly = weekly * n("weeksYear"); monthly = yearly / 12; } else { monthly = n("amount"); yearly = monthly * 12; weekly = yearly / n("weeksYear"); daily = weekly / n("daysWeek"); hourly = daily / n("hoursDay"); } setResult("Hourly: " + fmt2(hourly) + "<br>Daily: " + fmt(daily) + "<br>Weekly: " + fmt(weekly) + "<br>Monthly: " + fmt(monthly) + "<br>Yearly: " + fmt(yearly));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
