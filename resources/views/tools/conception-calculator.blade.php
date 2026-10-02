@extends('layouts.app')

@section('title', 'Conception Calculator — Free Online Tool')
@section('meta_description', 'Estimate your conception date and fertile window from your due date or last period')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Conception Calculator</h1>
            <p class="lead small text-muted">Work backwards from a due date, or forwards from your last period, to estimate when conception happened and the fertile window around it.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="mode">Start from</label><select class="form-select" id="mode"><option value="due">My due date</option><option value="lmp">First day of my last period (LMP)</option></select></div>                    <div class="mb-3"><label class="form-label" for="date">Date</label><input type="date" class="form-control" id="date" value="2027-06-01"></div>                    <div class="mb-3"><label class="form-label" for="cycle">Average cycle length (days) — used only with LMP</label><input type="number" class="form-control" id="cycle" value="28" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose whether to start from your due date or your last period.</li><li>Enter the date (and cycle length if using LMP).</li><li>The estimated conception date and fertile window appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">From a due date, conception is estimated as due date minus 266 days (38 weeks). From LMP, ovulation is estimated as LMP plus cycle length minus 14 days. The fertile window is roughly the 5 days before ovulation through the day after. Real cycles vary, so treat these as estimates. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function fmtDate(d) { var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]; return d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear(); }
    function shift(base, days) { var d = new Date(base.getTime()); d.setDate(d.getDate() + days); return d; }
    function calc() {
        var mode = document.getElementById("mode").value;
        var v = document.getElementById("date").value;
        var cycle = num("cycle");
        if (!v) { out("Please choose a date."); return; }
        var base = new Date(v + "T00:00:00");
        if (isNaN(base.getTime())) { out("Please choose a valid date."); return; }
        var conception;
        if (mode === "due") { conception = shift(base, -266); }
        else { if (isNaN(cycle) || cycle < 20 || cycle > 45) { out("Please enter a cycle length between 20 and 45 days."); return; } conception = shift(base, cycle - 14); }
        out("<strong>Estimated conception (ovulation) date:</strong> " + fmtDate(conception) + "<br><strong>Estimated fertile window:</strong> " + fmtDate(shift(conception, -5)) + " to " + fmtDate(shift(conception, 1)));
    }
    bind(["mode", "date", "cycle"], calc); calc();
})();
</script>
@endsection
