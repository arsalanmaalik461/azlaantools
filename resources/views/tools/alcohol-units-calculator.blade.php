@extends('layouts.app')

@section('title', 'Alcohol Units Calculator — Free Online Tool')
@section('meta_description', 'Work out alcohol units and pure alcohol grams in any drink from volume and strength')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Alcohol Units Calculator</h1>
            <p class="lead small text-muted">Enter the size and strength of any drink to find its UK alcohol units, grams of pure alcohol and US standard drinks.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="volume">Drink volume (ml)</label><input type="number" class="form-control" id="volume" value="355" step="any"></div>                    <div class="mb-3"><label class="form-label" for="abv">Strength ABV (%)</label><input type="number" class="form-control" id="abv" value="5" step="any"></div>                    <div class="mb-3"><label class="form-label" for="qty">Number of drinks</label><input type="number" class="form-control" id="qty" value="1" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the drink volume in millilitres.</li><li>Enter the ABV percentage from the label.</li><li>Enter the number of drinks and read the totals.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">UK units = volume (ml) x ABV (%) / 1000. Pure alcohol grams = volume x ABV / 100 x 0.789. One US standard drink contains 14 g of pure alcohol. Estimate only — not medical advice.</p>
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
        var vol = num("volume"), abv = num("abv"), qty = num("qty");
        if ([vol, abv, qty].some(isNaN) || vol <= 0 || abv <= 0 || qty <= 0) { out("Please enter valid positive values."); return; }
        var units = vol * abv / 1000 * qty;
        var grams = vol * abv / 100 * 0.789 * qty;
        out("<strong>UK alcohol units:</strong> " + fmt(units, 1) + "<br><strong>Pure alcohol:</strong> " + fmt(grams, 1) + " g<br><strong>US standard drinks:</strong> " + fmt(grams / 14, 1));
    }
    bind(["volume", "abv", "qty"], calc); calc();
})();
</script>
@endsection
