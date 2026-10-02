@extends('layouts.app')

@section('title', 'Hourly Wage Calculator — Free Online Tool')
@section('meta_description', 'Convert monthly or yearly salary to hourly, daily and weekly pay rates.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Hourly Wage Calculator</h1>
            <p class="lead small text-muted">Enter your salary and its period — you will get all equivalent rates for hour, day, week, month and year in one table.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="hwAmt">Salary amount</label><input type="number" class="form-control inp" id="hwAmt" placeholder="e.g. 150000" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="hwPer">Period</label><select class="form-select inp" id="hwPer"><option value="monthly">Monthly</option><option value="yearly">Yearly</option><option value="weekly">Weekly</option><option value="daily">Daily</option><option value="hourly">Hourly</option></select></div>                    <div class="col-md-4"><label class="form-label" for="hwHrs">Hours per week</label><input type="number" class="form-control inp" id="hwHrs" placeholder="40" step="any" value="40"></div>                    <div class="col-md-4"><label class="form-label" for="hwDays">Working days per week</label><input type="number" class="form-control inp" id="hwDays" placeholder="5" step="any" value="5"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Select your salary and period.</li><li>Set hours per week and working days to match your job (default 40 hours, 5 days).</li><li>A table of all rates will be shown.</li></ol>
                    <p class="small text-muted mb-0">Note: A year is assumed to have 52 weeks and 12 months. The daily rate is based on working days per week; holidays and overtime are not included.</p>
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
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    function calc() {
        var amt = num("hwAmt"), per = el("hwPer").value, hrs = num("hwHrs"), days = num("hwDays");
        if (amt === null || amt < 0) { err("Enter the salary amount."); return; }
        if (hrs === null || hrs <= 0 || days === null || days <= 0 || days > 7) { err("Enter valid hours per week and working days."); return; }
        var hourly;
        if (per === "hourly") { hourly = amt; } else if (per === "daily") { hourly = amt / (hrs / days); } else if (per === "weekly") { hourly = amt / hrs; } else if (per === "monthly") { hourly = amt * 12 / (52 * hrs); } else { hourly = amt / (52 * hrs); }
        var daily = hourly * (hrs / days), weekly = hourly * hrs, monthly = weekly * 52 / 12, yearly = weekly * 52;
        var sym = "Rs ";
        var rows = [["Hourly", hourly], ["Daily", daily], ["Weekly", weekly], ["Monthly", monthly], ["Yearly", yearly]].map(function (r) { return "<tr><td>" + r[0] + "</td><td><strong>" + sym + fmt(r[1], 2) + "</strong></td></tr>"; }).join("");
        show("<div class=\"table-responsive\"><table class=\"table table-sm w-auto\"><tbody>" + rows + "</tbody></table></div>");
        showSteps(["First, the hourly rate: " + sym + fmt(hourly, 2) + " per hour", "Daily = hourly x (hours ÷ days) = hourly x " + fmt(hrs / days, 2), "Monthly = yearly ÷ 12, where yearly = weekly x 52"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
