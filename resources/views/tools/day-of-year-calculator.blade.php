@extends('layouts.app')

@section('title', 'Day of Year Calculator — Free Online Tool')
@section('meta_description', 'Find the day number of the year and how many days remain in the year.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Day of Year Calculator</h1>
                    <p class="lead small text-muted">See the ordinal day number (1 to 365, or 366 in a leap year), the ISO week number, days remaining and the percentage of the year already gone.</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="dyDate">Date</label><input type="date" class="form-control" id="dyDate"></div>
                        <div class="col-md-6"><label class="form-label" for="dyNum">Or find a date by day number</label><input type="number" class="form-control" id="dyNum" placeholder="e.g. 100" min="1" max="366" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="dyOut">Pick a date to see its day number.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick a date to see its day number, week number and days left in that year.</li>
                        <li>Or type a day number (1 to 366) to find which date it falls on in the selected year.</li>
                    </ol>
                    <p class="small text-muted mb-0">Day numbers count from 1 January as day 1. Week numbers follow ISO 8601, where week 1 is the week containing the first Thursday of the year.</p>
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
    el("dyDate").value = todayStr();
    function isoWeek(d) { var t = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate())); var dayNum = t.getUTCDay() || 7; t.setUTCDate(t.getUTCDate() + 4 - dayNum); var yearStart = new Date(Date.UTC(t.getUTCFullYear(), 0, 1)); return Math.ceil(((t - yearStart) / 86400000 + 1) / 7); }
    function calc() {
        var d = parseD(el("dyDate").value);
        var out = el("dyOut");
        if (!d) { out.textContent = "Please pick a valid date."; return; }
        var year = d.getFullYear();
        var leap = (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
        var total = leap ? 366 : 365;
        var doy = Math.round((d - new Date(year, 0, 1)) / 86400000) + 1;
        var html = "<strong>Day " + doy + " of " + total + "</strong> in " + year + (leap ? " (leap year)" : "") + " &nbsp; <strong>Days remaining after this date:</strong> " + (total - doy) + " &nbsp; <strong>ISO week:</strong> " + isoWeek(d) + " &nbsp; <strong>Year progress:</strong> " + (doy / total * 100).toFixed(1) + "%";
        var n = parseInt(el("dyNum").value, 10);
        if (!isNaN(n) && n >= 1 && n <= total) { var target = new Date(year, 0, n); html += "<br>Day " + n + " of " + year + " is <strong>" + fmtD(target) + "</strong>."; }
        out.innerHTML = html;
    }
    el("dyDate").addEventListener("input", calc); el("dyDate").addEventListener("change", calc); el("dyNum").addEventListener("input", calc);
    calc();
})();
</script>
@endsection
