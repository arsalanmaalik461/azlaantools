@extends('layouts.app')

@section('title', 'Days Since Calculator — Free Online Tool')
@section('meta_description', 'Count how many days, weeks and months have passed since any past date.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Days Since Calculator</h1>
                    <p class="lead small text-muted">How long ago was it? Enter a past date — a habit started, a baby born, a project launched — and see the full time elapsed in days, weeks, months and years.</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="dsDate">Past date</label><input type="date" class="form-control" id="dsDate"></div>
                        <div class="col-md-6"><label class="form-label" for="dsLabel">What started then? (optional)</label><input type="text" class="form-control" id="dsLabel" placeholder="e.g. quit smoking, baby born"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="dsOut">Pick a past date to count from.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick the date in the past you want to count from.</li>
                        <li>Optionally label it, then read the elapsed days, weeks and calendar breakdown.</li>
                    </ol>
                    <p class="small text-muted mb-0">The calendar breakdown counts full years, then full months, then remaining days, using real month lengths — the same method as a careful manual count.</p>
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
    function breakdown(from, to) { var y = to.getFullYear() - from.getFullYear(); var m = to.getMonth() - from.getMonth(); var dd = to.getDate() - from.getDate(); var cursor = new Date(to.getFullYear(), to.getMonth(), 1); while (dd < 0) { m--; cursor.setMonth(cursor.getMonth() - 1); dd += new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate(); } if (m < 0) { y--; m += 12; } return { y: y, m: m, d: dd }; }
    function calc() {
        var d = parseD(el("dsDate").value);
        var out = el("dsOut");
        if (!d) { out.textContent = "Please pick a date."; return; }
        var today = parseD(todayStr());
        var days = Math.round((today - d) / 86400000);
        var label = el("dsLabel").value.trim();
        if (days < 0) { out.textContent = "That date is in the future. For future dates, use the Days Until Calculator."; return; }
        var b = breakdown(d, today);
        out.innerHTML = "<strong>" + days.toLocaleString("en-US") + " days</strong> have passed" + (label ? " since " + label : "") + " (" + fmtD(d) + ").<br>That is " + (days / 7).toFixed(1) + " weeks, or " + b.y + " years, " + b.m + " months and " + b.d + " days. Total hours: " + (days * 24).toLocaleString("en-US") + ".";
    }
    el("dsDate").addEventListener("input", calc); el("dsDate").addEventListener("change", calc); el("dsLabel").addEventListener("input", calc);
    calc();
})();
</script>
@endsection
