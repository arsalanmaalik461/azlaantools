@extends('layouts.app')

@section('title', 'Day of Week Calculator — Free Online Tool')
@section('meta_description', 'Find which day of the week any past or future date falls on.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Day of Week Calculator</h1>
                    <p class="lead small text-muted">Enter any date — a birthday, a historical event, a future appointment — and instantly see its day of the week, day of the year and how far it is from today.</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="dwDate">Date</label><input type="date" class="form-control" id="dwDate"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="dwOut">Pick a date to see its weekday.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick or type any past or future date.</li>
                        <li>Read the weekday, plus the day number in its year and the distance from today.</li>
                    </ol>
                    <p class="small text-muted mb-0">Weekdays are calculated from the proleptic Gregorian calendar, which is exact for all modern dates and standard for historical lookups.</p>
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
    el("dwDate").value = todayStr();
    function calc() {
        var d = parseD(el("dwDate").value);
        var out = el("dwOut");
        if (!d) { out.textContent = "Please pick a valid date."; return; }
        var start = new Date(d.getFullYear(), 0, 1);
        var doy = Math.round((d - start) / 86400000) + 1;
        var today = parseD(todayStr());
        var diff = Math.round((d - today) / 86400000);
        var rel = diff === 0 ? "That is today." : (diff > 0 ? "That is " + diff.toLocaleString("en-US") + " days from today." : "That was " + Math.abs(diff).toLocaleString("en-US") + " days ago.");
        out.innerHTML = "<strong>" + fmtD(d) + "</strong><br>Day " + doy + " of " + d.getFullYear() + ". " + rel;
    }
    el("dwDate").addEventListener("input", calc); el("dwDate").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
