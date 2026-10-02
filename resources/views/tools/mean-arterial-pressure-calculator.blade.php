@extends('layouts.app')

@section('title', 'Mean Arterial Pressure Calculator — Free Online Tool')
@section('meta_description', 'Calculate mean arterial pressure from systolic and diastolic blood pressure')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Mean Arterial Pressure Calculator</h1>
            <p class="lead small text-muted">Mean arterial pressure (MAP) is the average pressure in the arteries during one heartbeat cycle, tracked alongside home blood pressure readings.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="sys">Systolic — top number (mmHg)</label><input type="number" class="form-control" id="sys" value="120" step="any"></div>                    <div class="mb-3"><label class="form-label" for="dia">Diastolic — bottom number (mmHg)</label><input type="number" class="form-control" id="dia" value="80" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the top (systolic) number.</li><li>Enter the bottom (diastolic) number.</li><li>Your MAP and pulse pressure appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">MAP = diastolic + (systolic - diastolic) / 3. A MAP of at least about 65 mmHg is generally considered needed to perfuse vital organs in acute care, but home readings should be interpreted by your clinician. Estimate only — not medical advice.</p>
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
        var s = num("sys"), d = num("dia");
        if (isNaN(s) || isNaN(d) || s <= 0 || d <= 0 || s <= d) { out("Please enter valid readings — systolic must be higher than diastolic."); return; }
        var map = d + (s - d) / 3;
        out("<strong>Mean arterial pressure (MAP):</strong> " + fmt(map, 1) + " mmHg<br><strong>Pulse pressure:</strong> " + fmt(s - d, 0) + " mmHg");
    }
    bind(["sys", "dia"], calc); calc();
})();
</script>
@endsection
