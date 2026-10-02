@extends('layouts.app')

@section('title', 'Room Lighting Calculator — Free Online Tool')
@section('meta_description', 'Calculate lumens and bulb count needed to light a room by its size and use.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Room Lighting Calculator</h1>
                    <p class="lead small text-muted">Enter room size and how the room is used to get the total lumens required, the number of LED bulbs, and a wattage guide — no more dark corners or over bright rooms.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="rlL">Room length (ft)</label><input type="number" class="form-control" id="rlL" value="12" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="rlW">Room width (ft)</label><input type="number" class="form-control" id="rlW" value="10" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="rlUse">Room use</label><select class="form-select" id="rlUse"><option value="living">Living room</option><option value="bedroom">Bedroom</option><option value="kitchen">Kitchen</option><option value="office">Office / study</option><option value="bathroom">Bathroom</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="rlLux">Target brightness (lux, editable)</label><input type="number" class="form-control" id="rlLux" value="150" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="rlBulb">Lumens per bulb (editable)</label><input type="number" class="form-control" id="rlBulb" value="800" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="rlOut">Enter room size to plan the lighting.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the room length and width in feet.</li>
                        <li>Choose the room use — the recommended lux level fills in automatically and stays editable.</li>
                        <li>Read the total lumens and how many bulbs of your chosen brightness you need.</li>
                    </ol>
                    <p class="small text-muted mb-0">Recommended ambient levels: living room and bedroom about 100 to 200 lux, kitchen 300 to 500 lux (with task lights over counters), office and study 300 to 500 lux, bathroom 200 to 300 lux. One lux equals one lumen per square metre. A typical 800 lumen LED bulb uses about 9 watts.</p>
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
    var luxMap = { living: 150, bedroom: 120, kitchen: 400, office: 450, bathroom: 250 };
    el("rlUse").addEventListener("change", function () { el("rlLux").value = luxMap[el("rlUse").value]; calc(); });
    function calc() {
        var L = num("rlL"), W = num("rlW"), lux = num("rlLux"), bulb = num("rlBulb");
        var out = el("rlOut");
        if (L <= 0 || W <= 0 || lux <= 0 || bulb <= 0) { out.textContent = "Please enter room dimensions, lux and bulb lumens greater than zero."; return; }
        var areaM2 = L * W * 0.092903;
        var lumens = areaM2 * lux;
        var bulbs = Math.ceil(lumens / bulb);
        out.innerHTML = "<strong>Room area:</strong> " + (L * W).toFixed(0) + " sq ft (" + areaM2.toFixed(1) + " sq m)<br><strong>Total light needed:</strong> about " + Math.round(lumens).toLocaleString("en-US") + " lumens at " + lux + " lux &nbsp; <strong>Bulbs needed:</strong> " + bulbs + " x " + bulb + " lumen bulbs (about " + Math.round(bulb / 90) + " W LED each).";
    }
    ["rlL", "rlW", "rlLux", "rlBulb"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
