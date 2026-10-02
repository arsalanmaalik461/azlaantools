@extends('layouts.app')

@section('title', 'Defrost Time Calculator — Free Online Tool')
@section('meta_description', 'Estimate safe defrost time for frozen meat by type, weight and method.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Defrost Time Calculator</h1>
                    <p class="lead small text-muted">Get safe thawing times for chicken, beef, mutton and fish in the fridge or in cold water, based on standard food safety guidance.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="dfMeat">Meat type</label><select class="form-select" id="dfMeat"><option value="chicken">Chicken (whole or pieces)</option><option value="beef">Beef / mutton cuts</option><option value="mince">Mince / ground meat</option><option value="fish">Fish and seafood</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="dfWeight">Weight (kg)</label><input type="number" class="form-control" id="dfWeight" value="1" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="dfMethod">Method</label><select class="form-select" id="dfMethod"><option value="fridge">Fridge (safest)</option><option value="water">Cold water bath</option></select></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="dfOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose the meat type and enter its frozen weight.</li>
                        <li>Choose fridge thawing (plan ahead) or a cold water bath (change the water every 30 minutes).</li>
                        <li>Start early enough — cook fridge thawed meat within 1 to 2 days, and cold water thawed meat immediately.</li>
                    </ol>
                    <p class="small text-muted mb-0">Rates follow standard food safety guidance: fridge thawing takes about 10 hours per kg for chicken, 12 hours per kg for beef and mutton cuts, 8 hours per kg for mince and 6 hours per kg for fish. Cold water is roughly 4 times faster. Never thaw meat at room temperature, and never refreeze raw thawed meat. Estimate only — check the meat is fully thawed and cold to the touch before cooking.</p>
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
    function fmtH(h) { if (h < 1) { return Math.round(h * 60) + " minutes"; } var hh = Math.floor(h), mm = Math.round((h - hh) * 60); return hh + " h " + (mm ? mm + " min" : ""); }
    function calc() {
        var meat = el("dfMeat").value, kg = parseFloat(el("dfWeight").value), method = el("dfMethod").value;
        var out = el("dfOut");
        if (isNaN(kg) || kg <= 0) { out.textContent = "Please enter a weight greater than zero."; return; }
        var fridgePerKg = { chicken: 10, beef: 12, mince: 8, fish: 6 };
        var hours = kg * fridgePerKg[meat] / (method === "water" ? 4 : 1);
        out.innerHTML = "<strong>Estimated thaw time:</strong> about " + fmtH(hours) + " using the " + (method === "water" ? "cold water method (keep the meat sealed in a bag and change the water every 30 minutes)" : "fridge method (place on a tray on the lowest shelf)") + ".<br>Plan ahead: for a " + kg + " kg item, start thawing roughly " + fmtH(hours) + " before cooking.";
    }
    ["dfMeat", "dfWeight", "dfMethod"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
