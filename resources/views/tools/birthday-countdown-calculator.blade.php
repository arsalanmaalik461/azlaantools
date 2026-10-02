@extends('layouts.app')

@section('title', 'Birthday Countdown Calculator — Free Online Tool')
@section('meta_description', 'Count days until your next birthday and the age you will turn.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Birthday Countdown Calculator</h1>
                    <p class="lead small text-muted">Enter a date of birth to see the next birthday date, the exact countdown in days, the age being turned, and fun totals like days already lived.</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="bdDob">Date of birth</label><input type="date" class="form-control" id="bdDob"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="bdOut">Enter a date of birth to see the countdown.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the date of birth.</li>
                        <li>See the next birthday, how many days away it is, and the age that will be turned.</li>
                    </ol>
                    <p class="small text-muted mb-0">For a 29 February birthday, the next birthday in a non-leap year is shown on 1 March. Days lived counts full days from the date of birth to today.</p>
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
        var dob = parseD(el("bdDob").value);
        var out = el("bdOut");
        if (!dob) { out.textContent = "Please enter a date of birth."; return; }
        var today = parseD(todayStr());
        if (dob > today) { out.textContent = "The date of birth cannot be in the future."; return; }
        var year = today.getFullYear();
        function bdayIn(y) { var m = dob.getMonth(), dd = dob.getDate(); if (m === 1 && dd === 29) { var leap = (y % 4 === 0 && y % 100 !== 0) || y % 400 === 0; if (!leap) { return new Date(y, 2, 1); } } return new Date(y, m, dd); }
        var next = bdayIn(year);
        if (next < today) { next = bdayIn(year + 1); }
        var daysUntil = Math.round((next - today) / 86400000);
        var turning = next.getFullYear() - dob.getFullYear();
        var lived = Math.round((today - dob) / 86400000);
        var when = daysUntil === 0 ? "The birthday is TODAY." : ("Next birthday in <strong>" + daysUntil + "</strong> days");
        out.innerHTML = when + ": <strong>" + fmtD(next) + "</strong>, turning <strong>" + turning + "</strong> years old.<br>Days already lived: " + lived.toLocaleString("en-US") + " (about " + (lived * 24).toLocaleString("en-US") + " hours).";
    }
    el("bdDob").addEventListener("input", calc); el("bdDob").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
