@extends('layouts.app')

@section('title', 'Earliest Pregnancy Test Calculator — Free Online Tool')
@section('meta_description', 'Find the earliest sensible date to take a pregnancy test from your ovulation date')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Earliest Pregnancy Test Calculator</h1>
            <p class="lead small text-muted">Implantation usually happens 6 to 12 days after ovulation. Enter your ovulation date to see when a test can first turn positive and when it is most reliable.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="ov">Ovulation date</label><input type="date" class="form-control" id="ov" value="2026-09-20"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the date you ovulated (or your best estimate).</li><li>Read the earliest test date and the more reliable dates.</li><li>Retest if an early test is negative but your period has not arrived.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Sensitive tests can sometimes detect pregnancy from about 10 days past ovulation (DPO), but accuracy is much higher from 12 to 14 DPO, around a missed period. An early negative result can change — test again in 2 to 3 days. Estimate only — not medical advice.</p>
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
        var v = document.getElementById("ov").value;
        if (!v) { out("Please choose your ovulation date."); return; }
        var base = new Date(v + "T00:00:00");
        if (isNaN(base.getTime())) { out("Please choose a valid date."); return; }
        out("<strong>Implantation window:</strong> " + fmtDate(shift(base, 6)) + " to " + fmtDate(shift(base, 12)) + " (6 to 12 DPO)<br><strong>Earliest sensitive test:</strong> " + fmtDate(shift(base, 10)) + " (10 DPO — may still be negative)<br><strong>More reliable:</strong> " + fmtDate(shift(base, 12)) + " to " + fmtDate(shift(base, 14)) + " (12 to 14 DPO)<br><strong>After a missed period:</strong> from " + fmtDate(shift(base, 14)));
    }
    bind(["ov"], calc); calc();
})();
</script>
@endsection
