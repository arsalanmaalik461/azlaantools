@extends('layouts.app')

@section('title', 'Moon Phase Calculator — Free Online Tool')
@section('meta_description', 'Find the moon phase, illumination and moon age for any date.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Moon Phase Calculator</h1>
                    <p class="lead small text-muted">Pick any date, past or future, to see the moon phase name, the illuminated percentage, the moon age in days, and when the next full moon and new moon fall.</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="mnDate">Date</label><input type="date" class="form-control" id="mnDate"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="mnOut">Pick a date to see the moon phase.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick any date — today is prefilled.</li>
                        <li>Read the phase name, illumination, moon age, and the days to the next full and new moon.</li>
                    </ol>
                    <p class="small text-muted mb-0">Uses the standard synodic month of 29.530588853 days counted from the known new moon of 6 January 2000, 18:14 UTC. Phase boundaries are approximate to within a few hours, which is normal for calendar moon calculators.</p>
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
    function parseD(v) { if (!v) { return null; } var p = v.split("-"); return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10), 12, 0, 0); }
    function todayStr() { var d = new Date(); function pad(n) { return (n < 10 ? "0" : "") + n; } return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
    el("mnDate").value = todayStr();
    var SYNODIC = 29.530588853;
    var REF = Date.UTC(2000, 0, 6, 18, 14) / 86400000;
    var NAMES = ["New Moon", "Waxing Crescent", "First Quarter", "Waxing Gibbous", "Full Moon", "Waning Gibbous", "Last Quarter", "Waning Crescent"];
    function calc() {
        var d = parseD(el("mnDate").value);
        var out = el("mnOut");
        if (!d) { out.textContent = "Please pick a valid date."; return; }
        var days = d.getTime() / 86400000 - REF;
        var age = ((days % SYNODIC) + SYNODIC) % SYNODIC;
        var illum = (1 - Math.cos(2 * Math.PI * age / SYNODIC)) / 2 * 100;
        var idx = Math.floor(((age / SYNODIC) * 8 + 0.5)) % 8;
        var toFull = (SYNODIC / 2 - age + SYNODIC) % SYNODIC;
        var toNew = (SYNODIC - age) % SYNODIC;
        function fmtDatePlus(n) { var t = new Date(d.getTime()); t.setDate(t.getDate() + Math.round(n)); return t.toLocaleDateString("en-GB", { weekday: "long", year: "numeric", month: "long", day: "numeric" }); }
        out.innerHTML = "<strong>Phase:</strong> " + NAMES[idx] + " &nbsp; <strong>Illumination:</strong> " + illum.toFixed(1) + "% &nbsp; <strong>Moon age:</strong> " + age.toFixed(1) + " days<br>Next full moon in about " + toFull.toFixed(1) + " days (" + fmtDatePlus(toFull) + "). Next new moon in about " + toNew.toFixed(1) + " days (" + fmtDatePlus(toNew) + ").";
    }
    el("mnDate").addEventListener("input", calc); el("mnDate").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
