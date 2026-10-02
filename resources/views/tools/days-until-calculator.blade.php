@extends('layouts.app')

@section('title', 'Days Until Calculator — Free Online Tool')
@section('meta_description', 'Count days, weeks and working days from today until a chosen future date.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Days Until Calculator</h1>
                    <p class="lead small text-muted">Planning leave, a deadline or a trip? Pick the date and get days, weeks, working days (Monday to Friday) and weekends remaining until it.</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="duDate">Future date</label><input type="date" class="form-control" id="duDate"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="duOut">Pick a future date to count down to.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick the future date you are counting down to.</li>
                        <li>Read the total days, full weeks, working days and weekends in between.</li>
                    </ol>
                    <p class="small text-muted mb-0">Working days count Monday to Friday only and do not exclude public holidays, which vary by country and province. The count is the number of midnights between today and the target date.</p>
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
    function el(id) { return document.getElementById(id); }
    function parseD(v) { if (!v) { return null; } var p = v.split("-"); var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10)); return isNaN(d.getTime()) ? null : d; }
    function todayStr() { var d = new Date(); function pad(n) { return (n < 10 ? "0" : "") + n; } return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
    function fmtD(d) { return d.toLocaleDateString("en-GB", { weekday: "long", year: "numeric", month: "long", day: "numeric" }); }
    function calc() {
        var d = parseD(el("duDate").value);
        var out = el("duOut");
        if (!d) { out.textContent = "Please pick a date."; return; }
        var today = parseD(todayStr());
        var days = Math.round((d - today) / 86400000);
        if (days < 0) { out.textContent = "That date has already passed. For past dates, use the Days Since Calculator."; return; }
        if (days === 0) { out.textContent = "That date is today."; return; }
        var work = 0, wknd = 0, cur = new Date(today.getTime());
        for (var i = 0; i < days; i++) { cur.setDate(cur.getDate() + 1); var w = cur.getDay(); if (w === 0 || w === 6) { wknd++; } else { work++; } }
        out.innerHTML = "<strong>" + days.toLocaleString("en-US") + " days</strong> until <strong>" + fmtD(d) + "</strong>.<br>That is " + Math.floor(days / 7) + " full weeks and " + (days % 7) + " extra days, with " + work + " working days (Mon to Fri) and " + wknd + " weekend days in between.";
    }
    el("duDate").addEventListener("input", calc); el("duDate").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
