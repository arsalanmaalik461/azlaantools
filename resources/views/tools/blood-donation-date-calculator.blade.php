@extends('layouts.app')

@section('title', 'Blood Donation Date Calculator — Free Online Tool')
@section('meta_description', 'Find your next eligible blood donation date from your last donation and donation type')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Blood Donation Date Calculator</h1>
            <p class="lead small text-muted">Enter your last donation date and type to find the earliest date you may be able to donate again.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="last">Last donation date</label><input type="date" class="form-control" id="last" value="2026-08-01"></div>                    <div class="mb-3"><label class="form-label" for="type">Donation type</label><select class="form-select" id="type"><option value="56">Whole blood — 56 days</option><option value="112">Double red cells — 112 days</option><option value="7">Platelets — 7 days</option><option value="28">Plasma — 28 days</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the date of your last donation.</li><li>Choose the donation type.</li><li>Your next eligible date appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Intervals shown are the widely used minimum intervals for each donation type. Local blood service rules vary, and eligibility also depends on health, travel and medicines — confirm with your blood service. Estimate only — not medical advice.</p>
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
    function calc() {
        var v = document.getElementById("last").value;
        var days = num("type");
        if (!v) { out("Please choose your last donation date."); return; }
        var d = new Date(v + "T00:00:00");
        if (isNaN(d.getTime())) { out("Please choose a valid date."); return; }
        d.setDate(d.getDate() + days);
        var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        out("<strong>Next eligible date:</strong> " + d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear() + " (" + days + " days after your last donation)");
    }
    bind(["last", "type"], calc); calc();
})();
</script>
@endsection
