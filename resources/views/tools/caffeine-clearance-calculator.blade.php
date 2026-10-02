@extends('layouts.app')

@section('title', 'Caffeine Clearance Calculator — Free Online Tool')
@section('meta_description', 'Estimate how much caffeine is still in your system hour by hour using the five hour half life')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Caffeine Clearance Calculator</h1>
            <p class="lead small text-muted">Caffeine fades with a half-life of about five hours. See how much of your coffee is still active hour by hour — and at bedtime.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="amount">Caffeine consumed (mg) — a mug of coffee is about 95 mg</label><input type="number" class="form-control" id="amount" value="200" step="any"></div>                    <div class="mb-3"><label class="form-label" for="bed">Hours from intake until bedtime</label><input type="number" class="form-control" id="bed" value="8" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the caffeine amount in milligrams.</li><li>Enter how many hours pass between the drink and bedtime.</li><li>See what remains each hour and at bedtime.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses exponential decay with a 5-hour half-life: remaining = amount x 0.5^(hours/5). Half-life varies between people (roughly 3 to 7 hours) and is longer in pregnancy and with some medicines. Estimate only — not medical advice.</p>
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
        var amt = num("amount"), bed = num("bed");
        if (isNaN(amt) || isNaN(bed) || amt <= 0 || bed < 0) { out("Please enter a valid caffeine amount and hours to bedtime."); return; }
        var rows = "";
        for (var h = 0; h <= 12; h++) { rows += "<tr><td>" + h + " h</td><td>" + fmt(amt * Math.pow(0.5, h / 5), 0) + " mg</td></tr>"; }
        var atBed = amt * Math.pow(0.5, bed / 5);
        out("<strong>Still in your system at bedtime (" + fmt(bed, 1) + " h later):</strong> about " + fmt(atBed, 0) + " mg" + "<table class=\"table table-sm mt-2\"><thead><tr><th>Hours after drink</th><th>Caffeine left</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["amount", "bed"], calc); calc();
})();
</script>
@endsection
