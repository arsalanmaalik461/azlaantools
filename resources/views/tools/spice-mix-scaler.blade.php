@extends('layouts.app')

@section('title', 'Spice Mix Scaler — Free Online Tool')
@section('meta_description', 'Scale a homemade spice mix or garam masala blend to any total weight.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Spice Mix Scaler</h1>
                    <p class="lead small text-muted">Set the percentage of each spice in your blend and the total batch weight, and get the grams of every spice — prefilled with a classic garam masala blend.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="spTotal">Total blend weight (g)</label><input type="number" class="form-control" id="spTotal" value="100" min="0" step="any"></div>
                        <div class="col-md-6"><label class="form-label" for="spP0">Coriander powder (%)</label><input type="number" class="form-control sp-pct" id="spP0" value="30" min="0" step="any" data-name="Coriander powder"></div>
                        <div class="col-md-6"><label class="form-label" for="spP1">Cumin powder (%)</label><input type="number" class="form-control sp-pct" id="spP1" value="20" min="0" step="any" data-name="Cumin powder"></div>
                        <div class="col-md-6"><label class="form-label" for="spP2">Black pepper (%)</label><input type="number" class="form-control sp-pct" id="spP2" value="15" min="0" step="any" data-name="Black pepper"></div>
                        <div class="col-md-6"><label class="form-label" for="spP3">Green cardamom (%)</label><input type="number" class="form-control sp-pct" id="spP3" value="10" min="0" step="any" data-name="Green cardamom"></div>
                        <div class="col-md-6"><label class="form-label" for="spP4">Cinnamon (%)</label><input type="number" class="form-control sp-pct" id="spP4" value="10" min="0" step="any" data-name="Cinnamon"></div>
                        <div class="col-md-6"><label class="form-label" for="spP5">Cloves (%)</label><input type="number" class="form-control sp-pct" id="spP5" value="5" min="0" step="any" data-name="Cloves"></div>
                        <div class="col-md-6"><label class="form-label" for="spP6">Bay leaf (%)</label><input type="number" class="form-control sp-pct" id="spP6" value="5" min="0" step="any" data-name="Bay leaf"></div>
                        <div class="col-md-6"><label class="form-label" for="spP7">Nutmeg (%)</label><input type="number" class="form-control sp-pct" id="spP7" value="5" min="0" step="any" data-name="Nutmeg"></div>
                    </div>
                    <div class="table-responsive mt-3"><table class="table table-striped mb-0"><thead><tr><th>Spice</th><th>Grams</th></tr></thead><tbody id="spBody"></tbody></table></div>                    <div class="alert alert-info mt-3 mb-0" id="spMsg">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the total weight of blend you want to make.</li>
                        <li>Adjust the percentage of each spice — the classic garam masala percentages are prefilled.</li>
                        <li>Weigh out each spice from the table, mix, and store airtight away from light.</li>
                    </ol>
                    <p class="small text-muted mb-0">Percentages are normalised automatically, so they do not need to add up to exactly 100. Toasting whole spices before grinding gives a stronger aroma; the blend keeps well for about 3 months in an airtight jar.</p>
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
        var total = parseFloat(el("spTotal").value);
        var inputs = document.querySelectorAll(".sp-pct");
        var sum = 0, i;
        for (i = 0; i < inputs.length; i++) { var v = parseFloat(inputs[i].value); if (!isNaN(v) && v > 0) { sum += v; } }
        var body = el("spBody"), msg = el("spMsg");
        if (isNaN(total) || total <= 0 || sum <= 0) { msg.textContent = "Please enter a total weight and at least one spice percentage greater than zero."; body.innerHTML = ""; return; }
        var html = "";
        for (i = 0; i < inputs.length; i++) { var p = parseFloat(inputs[i].value); if (isNaN(p) || p <= 0) { continue; } html += "<tr><td>" + inputs[i].getAttribute("data-name") + "</td><td>" + (total * p / sum).toFixed(1) + " g</td></tr>"; }
        body.innerHTML = html;
        msg.textContent = "Blend total: " + total.toFixed(0) + " g across " + inputs.length + " spices (percentages normalised from a sum of " + sum + "%).";
    }
    el("spTotal").addEventListener("input", calc);
    var all = document.querySelectorAll(".sp-pct");
    for (var i = 0; i < all.length; i++) { all[i].addEventListener("input", calc); }
    calc();
})();
</script>
@endsection
