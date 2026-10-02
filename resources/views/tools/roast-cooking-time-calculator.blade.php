@extends('layouts.app')

@section('title', 'Roast Cooking Time Calculator — Free Online Tool')
@section('meta_description', 'Calculate roasting time for beef, chicken and lamb from weight and doneness.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Roast Cooking Time Calculator</h1>
                    <p class="lead small text-muted">Enter the meat, its weight and your preferred doneness to get total roasting time at standard oven temperatures, plus resting time.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="roMeat">Meat</label><select class="form-select" id="roMeat"><option value="beef">Beef joint</option><option value="chicken">Whole chicken</option><option value="lamb">Lamb leg / shoulder</option><option value="turkey">Turkey</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="roWeight">Weight (kg)</label><input type="number" class="form-control" id="roWeight" value="1.5" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="roDone">Doneness (beef and lamb)</label><select class="form-select" id="roDone"><option value="rare">Rare</option><option value="medium" selected>Medium</option><option value="well">Well done</option></select></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="roOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose the meat and enter its weight in kilograms.</li>
                        <li>For beef and lamb, choose rare, medium or well done (poultry is always cooked through).</li>
                        <li>Roast for the time shown and rest the meat before carving.</li>
                    </ol>
                    <p class="small text-muted mb-0">Times use standard published charts: beef at 180 C uses 35 (rare), 45 (medium) or 55 (well) minutes per kg plus 20 minutes; lamb 40, 50 or 60 minutes per kg plus 20; chicken 45 minutes per kg plus 20 at 190 C; turkey 40 minutes per kg plus 30. Always confirm poultry reaches 74 C inside and juices run clear.</p>
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
    function fmtTime(mins) { var h = Math.floor(mins / 60), m = Math.round(mins % 60); return (h > 0 ? h + " h " : "") + m + " min"; }
    function calc() {
        var meat = el("roMeat").value, kg = parseFloat(el("roWeight").value), done = el("roDone").value;
        var out = el("roOut");
        if (isNaN(kg) || kg <= 0) { out.textContent = "Please enter a weight greater than zero."; return; }
        var perKg, extra, temp, rest;
        if (meat === "beef") { perKg = done === "rare" ? 35 : (done === "well" ? 55 : 45); extra = 20; temp = "180 C (350 F)"; rest = 20; }
        else if (meat === "lamb") { perKg = done === "rare" ? 40 : (done === "well" ? 60 : 50); extra = 20; temp = "180 C (350 F)"; rest = 20; }
        else if (meat === "chicken") { perKg = 45; extra = 20; temp = "190 C (375 F)"; rest = 10; }
        else { perKg = 40; extra = 30; temp = "180 C (350 F)"; rest = 30; }
        var total = kg * perKg + extra;
        out.innerHTML = "<strong>Roasting time:</strong> " + fmtTime(total) + " at " + temp + "<br><strong>Resting time:</strong> " + rest + " minutes covered with foil before carving. Bring the meat to room temperature for 30 minutes before roasting for even cooking.";
    }
    ["roMeat", "roWeight", "roDone"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
