@extends('layouts.app')

@section('title', 'IVF Due Date Calculator — Free Online Tool')
@section('meta_description', 'Calculate an IVF due date from egg retrieval, day 3 transfer or day 5 blastocyst transfer')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">IVF Due Date Calculator</h1>
            <p class="lead small text-muted">IVF pregnancies are dated from retrieval and transfer, not from a last period. Choose the event and its date to get the due date.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="type">Event</label><select class="form-select" id="type"><option value="266">Egg retrieval / egg collection</option><option value="263">Day 3 embryo transfer</option><option value="261">Day 5 blastocyst transfer</option></select></div>                    <div class="mb-3"><label class="form-label" for="date">Date of that event</label><input type="date" class="form-control" id="date" value="2026-09-15"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose retrieval, day 3 transfer or day 5 transfer.</li><li>Enter the date it happened (or is planned).</li><li>Your estimated due date appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Due dates count 266 days from conception (retrieval). A day 3 transfer is already 3 days along, so 263 days remain; a day 5 blastocyst transfer has 261 days remaining. Your clinic dates the pregnancy from scans as well, and most babies do not arrive on the exact date. Estimate only — not medical advice.</p>
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
        var days = num("type");
        var v = document.getElementById("date").value;
        if (!v) { out("Please choose the event date."); return; }
        var d = new Date(v + "T00:00:00");
        if (isNaN(d.getTime())) { out("Please choose a valid date."); return; }
        d.setDate(d.getDate() + days);
        var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        out("<strong>Estimated due date:</strong> " + d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear());
    }
    bind(["type", "date"], calc); calc();
})();
</script>
@endsection
