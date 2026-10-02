@extends('layouts.app')

@section('title', 'Cycling Power Estimate Calculator — Free Online Tool')
@section('meta_description', 'Estimate cycling watts from speed, rider weight, gradient and road position')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Cycling Power Estimate Calculator</h1>
            <p class="lead small text-muted">Estimate the power needed to hold a speed on the road from the physics of gravity, rolling resistance and air drag.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="rider">Rider weight (kg)</label><input type="number" class="form-control" id="rider" value="75" step="any"></div>                    <div class="mb-3"><label class="form-label" for="bike">Bike and kit weight (kg)</label><input type="number" class="form-control" id="bike" value="9" step="any"></div>                    <div class="mb-3"><label class="form-label" for="speed">Speed (km/h)</label><input type="number" class="form-control" id="speed" value="30" step="any"></div>                    <div class="mb-3"><label class="form-label" for="grade">Gradient (%) — use a negative number downhill</label><input type="number" class="form-control" id="grade" value="0" step="any"></div>                    <div class="mb-3"><label class="form-label" for="pos">Riding position</label><select class="form-select" id="pos"><option value="0.40">Relaxed upright — CdA 0.40</option><option value="0.32">On the hoods — CdA 0.32</option><option value="0.28">In the drops — CdA 0.28</option><option value="0.23">Aero / triathlon bars — CdA 0.23</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter rider and bike weights.</li><li>Enter your speed and the road gradient.</li><li>Choose your riding position and read the estimated watts.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Model: power = (gravity + rolling resistance + air drag) x speed, divided by 0.97 for drivetrain losses. Air density 1.225 kg/m3 and rolling coefficient 0.005 are assumed. Wind, road surface and drafting change real power a lot. Estimate only — not medical advice.</p>
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
        var rider = num("rider"), bike = num("bike"), kmh = num("speed"), grade = num("grade"), cda = num("pos");
        if ([rider, bike, kmh, grade].some(isNaN) || rider <= 0 || bike < 0 || kmh <= 0) { out("Please enter valid weights and speed."); return; }
        var m = rider + bike, v = kmh / 3.6, g = 9.81;
        var pGrav = m * g * (grade / 100) * v;
        var pRoll = m * g * 0.005 * v;
        var pAir = 0.5 * 1.225 * cda * Math.pow(v, 3);
        var total = (pGrav + pRoll + pAir) / 0.97;
        if (total < 0) { total = 0; }
        out("<strong>Estimated power:</strong> about " + fmt(total, 0) + " W (" + fmt(total / rider, 2) + " W per kg of rider weight)<br>Gravity " + fmt(Math.max(pGrav, 0), 0) + " W, rolling " + fmt(pRoll, 0) + " W, air drag " + fmt(pAir, 0) + " W (before drivetrain loss)");
    }
    bind(["rider", "bike", "speed", "grade", "pos"], calc); calc();
})();
</script>
@endsection
