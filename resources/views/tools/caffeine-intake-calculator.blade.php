@extends('layouts.app')

@section('title', 'Caffeine Intake Calculator — Free Online Tool')
@section('meta_description', 'Total the caffeine in your coffee, tea and energy drinks and compare it with the 400 mg daily guide')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Caffeine Intake Calculator</h1>
            <p class="lead small text-muted">Count the servings of each drink you had today and see your total caffeine against the commonly cited 400 mg daily guide for adults.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="coffee">Mugs of brewed coffee (about 95 mg each)</label><input type="number" class="form-control" id="coffee" value="2" step="any"></div>                    <div class="mb-3"><label class="form-label" for="espresso">Espresso shots (about 63 mg each)</label><input type="number" class="form-control" id="espresso" value="0" step="any"></div>                    <div class="mb-3"><label class="form-label" for="tea">Cups of black tea (about 47 mg each)</label><input type="number" class="form-control" id="tea" value="1" step="any"></div>                    <div class="mb-3"><label class="form-label" for="cola">Cans of cola, 355 ml (about 34 mg each)</label><input type="number" class="form-control" id="cola" value="0" step="any"></div>                    <div class="mb-3"><label class="form-label" for="energy">Energy drinks, 250 ml (about 80 mg each)</label><input type="number" class="form-control" id="energy" value="0" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter how many of each drink you had today.</li><li>Your total appears instantly.</li><li>Compare it with the 400 mg guide shown in the result.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Caffeine contents are typical published averages; actual values vary by brand and brew strength. The 400 mg figure is the widely cited daily level not associated with safety concerns for healthy adults; the guide in pregnancy is commonly 200 mg. Estimate only — not medical advice.</p>
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
        var total = (num("coffee") || 0) * 95 + (num("espresso") || 0) * 63 + (num("tea") || 0) * 47 + (num("cola") || 0) * 34 + (num("energy") || 0) * 80;
        var vals = [num("coffee"), num("espresso"), num("tea"), num("cola"), num("energy")];
        if (vals.some(function (v) { return isNaN(v) || v < 0; })) { out("Please enter valid counts (zero or more) for each drink."); return; }
        var pct = total / 400 * 100;
        var note = total <= 400 ? "Within the 400 mg daily guide." : "Above the 400 mg daily guide by " + fmt(total - 400, 0) + " mg.";
        out("<strong>Total caffeine today:</strong> about " + fmt(total, 0) + " mg (" + fmt(pct, 0) + "% of the 400 mg guide)<br>" + note);
    }
    bind(["coffee", "espresso", "tea", "cola", "energy"], calc); calc();
})();
</script>
@endsection
