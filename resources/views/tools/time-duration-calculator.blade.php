@extends('layouts.app')

@section('title', 'Time Duration Calculator — Free Online Tool')
@section('meta_description', 'Calculate the exact hours and minutes between two times on the same or next day.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Time Duration Calculator</h1>
                    <p class="lead small text-muted">Enter a start and end time to get the exact duration in hours and minutes, decimal hours and total minutes — with automatic next day handling for overnight spans.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="tdStart">Start time</label><input type="time" class="form-control" id="tdStart" value="09:00"></div>
                        <div class="col-md-4"><label class="form-label" for="tdEnd">End time</label><input type="time" class="form-control" id="tdEnd" value="17:30"></div>
                        <div class="col-md-4"><label class="form-label" for="tdBreak">Break to subtract (minutes)</label><input type="number" class="form-control" id="tdBreak" value="0" min="0" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="tdOut">Enter start and end times to see the duration.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the start time and the end time.</li>
                        <li>Optionally subtract a break in minutes.</li>
                        <li>Read the net duration in hours and minutes, decimal hours and total minutes.</li>
                    </ol>
                    <p class="small text-muted mb-0">If the end time is earlier than the start time, the end is treated as falling on the next day, which is how overnight shifts and late night events work.</p>
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
    function toMin(t) { if (!t) { return null; } var p = t.split(":"); return parseInt(p[0], 10) * 60 + parseInt(p[1], 10); }
    function calc() {
        var s = toMin(el("tdStart").value), e = toMin(el("tdEnd").value);
        var brk = parseInt(el("tdBreak").value, 10);
        var out = el("tdOut");
        if (s === null || e === null) { out.textContent = "Please set both a start and an end time."; return; }
        if (isNaN(brk) || brk < 0) { brk = 0; }
        if (e < s) { e += 1440; }
        var gross = e - s;
        var net = gross - brk;
        if (net < 0) { out.textContent = "The break is longer than the total duration. Please check the values."; return; }
        var h = Math.floor(net / 60), m = net % 60;
        out.innerHTML = "<strong>Duration:</strong> " + h + " hours " + m + " minutes &nbsp; <strong>Decimal:</strong> " + (net / 60).toFixed(2) + " hours &nbsp; <strong>Total:</strong> " + net + " minutes" + (brk ? " (after subtracting a " + brk + " minute break from " + Math.floor(gross / 60) + " h " + (gross % 60) + " min)" : "");
    }
    ["tdStart", "tdEnd", "tdBreak"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
