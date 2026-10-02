@extends('layouts.app')

@section('title', 'Alcohol Calories Calculator — Free Online Tool')
@section('meta_description', 'See calories and sugar in common drinks and what a night out adds up to')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Alcohol Calories Calculator</h1>
            <p class="lead small text-muted">Add up the calories in beer, wine and spirits, and see how much of it comes from the alcohol itself.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="drink">Drink</label><select class="form-select" id="drink"><option value="150|355|5">Beer, 355 ml at 5% — about 150 kcal</option><option value="125|150|12">Wine, 150 ml at 12% — about 125 kcal</option><option value="97|44|40">Spirits, single 44 ml shot at 40% — about 97 kcal</option><option value="210|330|5">Ready-to-drink bottle, 330 ml at 5% — about 210 kcal</option><option value="200|473|5">Beer, pint 473 ml at 5% — about 200 kcal</option></select></div>                    <div class="mb-3"><label class="form-label" for="qty">Number of drinks</label><input type="number" class="form-control" id="qty" value="2" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose a drink from the typical-values list.</li><li>Enter how many you had.</li><li>See the total calories and the share that comes from pure alcohol.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Alcohol itself provides 7 kcal per gram; the rest comes from sugars and carbohydrates in the drink. Typical drink values are averages and brands differ. Sugar content varies widely, so this tool reports calories rather than a precise sugar figure. Estimate only — not medical advice.</p>
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
        var parts = document.getElementById("drink").value.split("|");
        var kcal = parseFloat(parts[0]), vol = parseFloat(parts[1]), abv = parseFloat(parts[2]);
        var qty = num("qty");
        if (isNaN(qty) || qty < 0) { out("Please enter a valid number of drinks."); return; }
        var grams = vol * abv / 100 * 0.789 * qty;
        out("<strong>Total calories:</strong> about " + fmt(kcal * qty, 0) + " kcal<br><strong>Pure alcohol:</strong> " + fmt(grams, 1) + " g, which alone is about " + fmt(grams * 7, 0) + " kcal<br>The rest is mostly sugars and carbohydrates from the drink itself.");
    }
    bind(["drink", "qty"], calc); calc();
})();
</script>
@endsection
