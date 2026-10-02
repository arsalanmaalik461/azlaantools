@extends('layouts.app')

@section('title', 'Sourdough Feeding Calculator — Free Online Tool')
@section('meta_description', 'Calculate starter, flour and water amounts for any feeding ratio and target weight.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Sourdough Feeding Calculator</h1>
                    <p class="lead small text-muted">Tell the calculator how much fed starter (levain) you need and your feeding ratio, and get the exact starter, flour and water to mix.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="sdTarget">Target fed starter weight (g)</label><input type="number" class="form-control" id="sdTarget" value="200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="sdStarterParts">Ratio — starter parts</label><input type="number" class="form-control" id="sdStarterParts" value="1" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="sdFlourParts">Ratio — flour parts</label><input type="number" class="form-control" id="sdFlourParts" value="2" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="sdWaterParts">Ratio — water parts</label><input type="number" class="form-control" id="sdWaterParts" value="2" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="sdKeep">Extra to keep as mother starter (g)</label><input type="number" class="form-control" id="sdKeep" value="0" min="0" step="any"></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="sdOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the total fed starter weight you need, including any extra you want to keep.</li>
                        <li>Set your feeding ratio, for example 1:2:2 means 1 part starter, 2 parts flour, 2 parts water.</li>
                        <li>Mix the starter, flour and water amounts shown and leave to peak.</li>
                    </ol>
                    <p class="small text-muted mb-0">A 1:1:1 feed peaks fastest and suits same day baking in a warm kitchen; 1:2:2 and 1:3:3 feeds peak more slowly and are common for overnight schedules. Flour and water are kept equal here so the starter stays at 100 percent hydration.</p>
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
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function calc() {
        var target = num("sdTarget") + num("sdKeep");
        var sp = num("sdStarterParts"), fp = num("sdFlourParts"), wp = num("sdWaterParts");
        var out = el("sdOut");
        var parts = sp + fp + wp;
        if (target <= 0 || parts <= 0 || fp <= 0) { out.textContent = "Please enter a target weight and a valid ratio with flour parts greater than zero."; return; }
        out.innerHTML = "<strong>Starter to use:</strong> " + (target * sp / parts).toFixed(1) + " g &nbsp; <strong>Flour:</strong> " + (target * fp / parts).toFixed(1) + " g &nbsp; <strong>Water:</strong> " + (target * wp / parts).toFixed(1) + " g<br>Total mixed: " + target.toFixed(0) + " g at a " + sp + ":" + fp + ":" + wp + " ratio.";
    }
    ["sdTarget", "sdStarterParts", "sdFlourParts", "sdWaterParts", "sdKeep"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
