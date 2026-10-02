@extends('layouts.app')

@section('title', 'Week Number Converter — Free Online Tool')
@section('meta_description', 'Enter a date to find the week number of the year, or enter a week number to see its date range.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Week Number Converter</h1>
            <p class="lead small text-muted mb-4">Enter a date to find the week number of the year, or enter a week number to see its date range.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Date to Week Number</h2>
                    <div class="mb-3"><label for="wkDate" class="form-label">Date</label><input type="date" class="form-control" id="wkDate" value="2026-10-01"></div>
                    <div class="alert alert-info mb-0"><div class="fw-bold" id="wkOut">—</div><div class="small" id="wkDetail"></div></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Week Number to Dates</h2>
                    <div class="row g-2">
                        <div class="col-6"><label for="wkYear" class="form-label">Year</label><input type="number" class="form-control" id="wkYear" value="2026" step="1"></div>
                        <div class="col-6"><label for="wkNum" class="form-label">ISO Week (1–53)</label><input type="number" class="form-control" id="wkNum" value="40" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0"><div class="fw-bold" id="wkRangeOut">—</div></div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your values in the boxes above or select an option.</li>
                        <li>The result updates live right away — no button needed.</li>
                        <li>Change a value or unit and the new result shows on its own.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">ISO 8601 standard: the week starts on Monday, and the first week of the year is the one that has the first Thursday of the year. Some years have 53 weeks.</p>
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
    function isoWeek(date) {
        var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        var dayNum = (d.getUTCDay() + 6) % 7;
        d.setUTCDate(d.getUTCDate() - dayNum + 3);
        var isoYear = d.getUTCFullYear();
        var firstThursday = new Date(Date.UTC(isoYear, 0, 4));
        var ftDay = (firstThursday.getUTCDay() + 6) % 7;
        firstThursday.setUTCDate(firstThursday.getUTCDate() - ftDay + 3);
        var week = 1 + Math.round((d - firstThursday) / (7 * 24 * 3600 * 1000));
        return { year: isoYear, week: week };
    }
    function fromDate() {
        var v = document.getElementById("wkDate").value;
        var o = document.getElementById("wkOut"), det = document.getElementById("wkDetail");
        if (!v) { o.textContent = "—"; det.textContent = ""; return; }
        var parts = v.split("-");
        var date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        var r = isoWeek(date);
        var dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        var doy = Math.floor((date - new Date(date.getFullYear(), 0, 0)) / 86400000);
        o.textContent = "ISO Week " + r.week + " of " + r.year;
        det.textContent = dayNames[date.getDay()] + " — Day of year: " + doy + " — Quarter: Q" + (Math.floor(date.getMonth() / 3) + 1);
    }
    function fromWeek() {
        var y = parseInt(document.getElementById("wkYear").value, 10);
        var w = parseInt(document.getElementById("wkNum").value, 10);
        var o = document.getElementById("wkRangeOut");
        if (isNaN(y) || isNaN(w) || w < 1 || w > 53) { o.textContent = "—"; return; }
        var jan4 = new Date(Date.UTC(y, 0, 4));
        var ftDay = (jan4.getUTCDay() + 6) % 7;
        var monday = new Date(jan4);
        monday.setUTCDate(jan4.getUTCDate() - ftDay + (w - 1) * 7);
        var sunday = new Date(monday);
        sunday.setUTCDate(monday.getUTCDate() + 6);
        function f(d) { return d.toISOString().slice(0, 10); }
        o.textContent = "Week " + w + " of " + y + ": Monday " + f(monday) + " to Sunday " + f(sunday);
    }
    document.getElementById("wkDate").addEventListener("input", fromDate);
    document.getElementById("wkYear").addEventListener("input", fromWeek);
    document.getElementById("wkNum").addEventListener("input", fromWeek);
    fromDate(); fromWeek();
})();
</script>
@endsection
