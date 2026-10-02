@extends('layouts.app')

@section('title', 'Boiled Egg Timer Calculator — Free Online Tool')
@section('meta_description', 'Get boiling time for soft, medium and hard eggs by egg size and altitude.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Boiled Egg Timer Calculator</h1>
                    <p class="lead small text-muted">Perfect eggs every time: choose egg size and how runny you like the yolk, add your altitude, and get the exact minutes from a rolling boil.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="egSize">Egg size</label><select class="form-select" id="egSize"><option value="-1">Small (about 50 g)</option><option value="0" selected>Medium (about 58 g)</option><option value="1">Large (about 68 g)</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="egDone">Doneness</label><select class="form-select" id="egDone"><option value="soft">Soft — runny yolk</option><option value="medium">Medium — jammy yolk</option><option value="hard">Hard — fully set</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="egAlt">Altitude (metres above sea level)</label><input type="number" class="form-control" id="egAlt" value="0" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="egStart">Egg starting temperature</label><select class="form-select" id="egStart"><option value="0">Fridge cold</option><option value="-0.5">Room temperature</option></select></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="egOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose the egg size and your preferred doneness.</li>
                        <li>Enter your altitude if you live in a hilly area — water boils cooler there, so eggs need longer.</li>
                        <li>Lower the eggs into already boiling water, start the time shown, then cool in cold water to stop cooking.</li>
                    </ol>
                    <p class="small text-muted mb-0">Base times for a medium egg at sea level, lowered into boiling water: soft 6 minutes, medium (jammy) 8 minutes, hard 11 minutes. Large eggs add about 1 minute, small eggs need about 1 minute less, and every 1000 m of altitude adds roughly 30 seconds because boiling water is cooler.</p>
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
    function calc() {
        var sizeAdj = parseFloat(el("egSize").value);
        var done = el("egDone").value;
        var alt = parseFloat(el("egAlt").value);
        var startAdj = parseFloat(el("egStart").value);
        var out = el("egOut");
        if (isNaN(alt) || alt < 0) { alt = 0; }
        var base = done === "soft" ? 6 : (done === "medium" ? 8 : 11);
        var mins = base + sizeAdj + startAdj + (alt / 1000) * 0.5;
        if (mins < 3) { mins = 3; }
        var whole = Math.floor(mins), secs = Math.round((mins - whole) * 60);
        out.innerHTML = "<strong>Boil for:</strong> " + whole + " min " + (secs ? secs + " sec" : "") + " from the moment the eggs go into boiling water.<br>Tip: older eggs peel more easily; roll and peel under running water after a 2 minute cold bath.";
    }
    ["egSize", "egDone", "egAlt", "egStart"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
