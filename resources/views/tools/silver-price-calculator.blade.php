@extends('layouts.app')

@section('title', 'Silver Price Calculator — Free Online Tool')
@section('meta_description', 'Enter the silver rate per tola and find the price in grams, masha and ratti.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Silver Price Calculator</h1>
                    <p class="lead small text-muted">Write your local market silver rate per tola and instantly find the price of any weight — tola, gram, masha or ratti — just like the gold calculator.</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="svRate">Rate per tola (Rs) — enter your local market rate for today</label><input type="number" class="form-control" id="svRate" value="3500" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="svWeight">Weight</label><input type="number" class="form-control" id="svWeight" value="1" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="svUnit">Unit</label><select class="form-select" id="svUnit"><option value="11.6638">Tola</option><option value="1">Gram</option><option value="0.971983">Masha</option><option value="0.121249">Ratti</option></select></div>
                    </div>
                    <div class="row g-3 mt-1 text-center">
                        <div class="col-md-3"><div class="border rounded p-3"><div class="small text-muted">Total Value</div><div class="fs-5 fw-bold" id="svTotal">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3"><div class="small text-muted">Per Gram</div><div class="fs-5 fw-bold" id="svGram">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3"><div class="small text-muted">Per Masha</div><div class="fs-5 fw-bold" id="svMasha">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3"><div class="small text-muted">Per Ratti</div><div class="fs-5 fw-bold" id="svRatti">—</div></div></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="svConv">The weight conversion will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>First write your silver rate per tola for today — the default is only an example, not a live rate.</li>
                        <li>Choose the weight and unit (tola, gram, masha, ratti).</li>
                        <li>The total price and per gram, per masha, per ratti rates will appear instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0">Constants: 1 tola = 11.6638 gram = 12 masha = 96 ratti. This calculator does not use any live rate — market rates change daily, so always confirm and enter today's rate. Rates change — verify with the official source before relying on this.</p>
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
    function money(n) { return "Rs " + Math.round(n).toLocaleString("en-PK"); }
    function calc() {
        var rate = num("svRate"), w = num("svWeight"), unit = parseFloat(el("svUnit").value);
        if (rate <= 0 || w <= 0) { el("svConv").textContent = "Please enter a valid rate and weight."; return; }
        var perGram = rate / 11.6638;
        var grams = w * unit;
        el("svTotal").textContent = money(grams * perGram);
        el("svGram").textContent = money(perGram);
        el("svMasha").textContent = money(perGram * 0.971983);
        el("svRatti").textContent = money(perGram * 0.121249);
        el("svConv").textContent = "Your weight: " + grams.toFixed(4) + " gram = " + (grams / 11.6638).toFixed(4) + " tola = " + (grams / 0.971983).toFixed(2) + " masha = " + (grams / 0.121249).toFixed(2) + " ratti.";
    }
    ["svRate", "svWeight", "svUnit"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
