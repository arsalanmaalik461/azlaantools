@extends('layouts.app')

@section('title', 'Period Calculator — Free Online Tool')
@section('meta_description', 'Predict your next three periods from your last period date and average cycle length')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Period Calculator</h1>
            <p class="lead small text-muted">Enter the first day of your last period and your usual cycle length to predict the next three period start dates, plus ovulation estimates.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="last">First day of last period</label><input type="date" class="form-control" id="last" value="2026-09-10"></div>                    <div class="mb-3"><label class="form-label" for="cycle">Average cycle length (days)</label><input type="number" class="form-control" id="cycle" value="28" step="any"></div>                    <div class="mb-3"><label class="form-label" for="dur">Usual period length (days)</label><input type="number" class="form-control" id="dur" value="5" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the first day of your last period.</li><li>Enter your average cycle length and usual period length.</li><li>Your next three predicted periods appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Predictions add your average cycle length repeatedly, and ovulation is estimated as 14 days before the next period. Cycles naturally vary by a few days, and stress, illness and travel shift them — predictions get less accurate further ahead and with irregular cycles. Estimate only — not medical advice.</p>
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
    function fmtDate(d) { var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]; return d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear(); }
    function shift(base, days) { var d = new Date(base.getTime()); d.setDate(d.getDate() + days); return d; }
    function calc() {
        var v = document.getElementById("last").value;
        var cycle = num("cycle"), dur = num("dur");
        if (!v) { out("Please choose the first day of your last period."); return; }
        if (isNaN(cycle) || cycle < 20 || cycle > 45) { out("Please enter a cycle length between 20 and 45 days."); return; }
        if (isNaN(dur) || dur < 1 || dur > 10) { dur = 5; }
        var base = new Date(v + "T00:00:00");
        if (isNaN(base.getTime())) { out("Please choose a valid date."); return; }
        var html = "";
        for (var i = 1; i <= 3; i++) {
            var start = shift(base, cycle * i);
            html += "<strong>Period " + i + ":</strong> " + fmtDate(start) + " to " + fmtDate(shift(start, dur - 1)) + " — estimated ovulation " + fmtDate(shift(start, -14)) + "<br>";
        }
        out(html);
    }
    bind(["last", "cycle", "dur"], calc); calc();
})();
</script>
@endsection
