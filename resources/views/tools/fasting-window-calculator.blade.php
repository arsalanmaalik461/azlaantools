@extends('layouts.app')

@section('title', 'Intermittent Fasting Window Calculator — Free Online Tool')
@section('meta_description', 'Plan 16 8, 18 6 and OMAD fasting windows with exact start and eating times')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Intermittent Fasting Window Calculator</h1>
            <p class="lead small text-muted">Choose a fasting protocol and the time your fast starts, and get the exact clock times for your eating window.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="proto">Fasting protocol</label><select class="form-select" id="proto"><option value="16">16:8 — fast 16 hours, eat within 8 hours</option><option value="18">18:6 — fast 18 hours, eat within 6 hours</option><option value="20">20:4 — fast 20 hours, eat within 4 hours</option><option value="23">OMAD 23:1 — one meal a day</option><option value="14">14:10 — fast 14 hours, eat within 10 hours</option><option value="12">12:12 — fast 12 hours, eat within 12 hours</option></select></div>                    <div class="mb-3"><label class="form-label" for="start">Fast starts at (last bite / last meal ends)</label><input type="time" class="form-control" id="start" value="20:00"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose your fasting protocol.</li><li>Enter the time your fast begins, usually when your last meal ends.</li><li>Read when you can eat again and when the eating window closes.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">This is clock arithmetic only: eating window opens after the fasting hours and closes after the eating hours. Fasting is not suitable for everyone — including during pregnancy, for people with diabetes on medication, or with a history of eating disorders — without medical guidance. Estimate only — not medical advice.</p>
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
    function fmtTime(mins) { mins = ((mins % 1440) + 1440) % 1440; var h = Math.floor(mins / 60), m = mins % 60; var ap = h >= 12 ? "PM" : "AM"; var h12 = h % 12; if (h12 === 0) { h12 = 12; } return h12 + ":" + (m < 10 ? "0" : "") + m + " " + ap; }
    function calc() {
        var fast = num("proto");
        var v = document.getElementById("start").value;
        if (!v) { out("Please set the time your fast starts."); return; }
        var parts = v.split(":");
        var start = parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
        var eat = 24 - fast;
        out("<strong>Fast:</strong> " + fast + " hours, from " + fmtTime(start) + "<br><strong>Eating window opens:</strong> " + fmtTime(start + fast * 60) + "<br><strong>Eating window closes:</strong> " + fmtTime(start + 24 * 60) + " (" + eat + " hours to eat)");
    }
    bind(["proto", "start"], calc); calc();
})();
</script>
@endsection
