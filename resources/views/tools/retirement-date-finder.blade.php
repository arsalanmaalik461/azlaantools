@extends('layouts.app')

@section('title', 'Retirement Date Finder — Free Online Tool')
@section('meta_description', 'Find the exact date you reach any retirement age from your birth date.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Retirement Date Finder</h1>
                    <p class="lead small text-muted">Enter a date of birth and a retirement age — 60 for many government jobs in Pakistan, or any age you choose — and get the exact retirement date and the time left.</p>
                    <div class="row g-3">
                        <div class="col-md-5"><label class="form-label" for="rtDob">Date of birth</label><input type="date" class="form-control" id="rtDob"></div>
                        <div class="col-md-4"><label class="form-label" for="rtAge">Retirement age (years)</label><input type="number" class="form-control" id="rtAge" value="60" min="1" max="100" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="rtOut">Enter a date of birth and retirement age.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the date of birth.</li>
                        <li>Set the retirement age (60 is prefilled, the standard superannuation age for many Pakistan government employees).</li>
                        <li>Read the exact retirement date, days remaining and current age.</li>
                    </ol>
                    <p class="small text-muted mb-0">The retirement date is the date the chosen age is reached. Actual last working day rules vary by employer and service rules — many services count retirement from the afternoon of the day before the birthday, so confirm with your department. This tool covers dates only, not pension amounts.</p>
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
        var dob = parseD(el("rtDob").value);
        var age = parseInt(el("rtAge").value, 10);
        var out = el("rtOut");
        if (!dob || isNaN(age) || age <= 0) { out.textContent = "Please enter a date of birth and a valid retirement age."; return; }
        var today = parseD(todayStr());
        var retire = new Date(dob.getFullYear() + age, dob.getMonth(), dob.getDate());
        if (dob.getMonth() === 1 && dob.getDate() === 29 && retire.getMonth() !== 1) { retire = new Date(dob.getFullYear() + age, 2, 1); }
        var cur = breakdown(dob, today);
        if (retire <= today) { out.innerHTML = "<strong>Retirement date was:</strong> " + fmtD(retire) + " (age " + age + " reached). Current age: " + cur.y + " years, " + cur.m + " months, " + cur.d + " days."; return; }
        var days = Math.round((retire - today) / 86400000);
        var left = breakdown(today, retire);
        out.innerHTML = "<strong>Retirement date:</strong> " + fmtD(retire) + "<br>Time remaining: " + left.y + " years, " + left.m + " months, " + left.d + " days (" + days.toLocaleString("en-US") + " days). Current age: " + cur.y + " years, " + cur.m + " months, " + cur.d + " days.";
    }
    el("rtDob").addEventListener("input", calc); el("rtDob").addEventListener("change", calc); el("rtAge").addEventListener("input", calc);
    calc();
})();
</script>
@endsection
