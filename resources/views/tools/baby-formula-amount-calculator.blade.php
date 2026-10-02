@extends('layouts.app')

@section('title', 'Baby Formula Amount Calculator — Free Online Tool')
@section('meta_description', 'Estimate daily formula milk amount from baby weight and split it across feeds per day')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Baby Formula Amount Calculator</h1>
            <p class="lead small text-muted">A simple guide to how much formula a baby may need per day based on weight, split across the number of feeds you choose.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Baby weight (kg)</label><input type="number" class="form-control" id="weight" value="5" step="any"></div>                    <div class="mb-3"><label class="form-label" for="feeds">Feeds per day</label><input type="number" class="form-control" id="feeds" value="6" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your baby weight in kilograms.</li><li>Enter how many feeds per day you plan.</li><li>Read the estimated daily total and the amount per feed.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the commonly cited guide of about 150 ml per kg per day (typical range 120 to 180 ml per kg). Always follow the product label and your pediatric guidance, and watch hunger cues rather than forcing a set amount. Estimate only — not medical advice.</p>
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
        var w = num("weight"), feeds = num("feeds");
        if (isNaN(w) || isNaN(feeds) || w <= 0 || feeds <= 0) { out("Please enter a valid weight and number of feeds."); return; }
        var daily = w * 150;
        out("<strong>Estimated daily total:</strong> about " + fmt(daily, 0) + " ml (" + fmt(daily / 29.5735, 1) + " fl oz)<br><strong>Per feed:</strong> about " + fmt(daily / feeds, 0) + " ml (" + fmt(daily / feeds / 29.5735, 1) + " fl oz)<br><strong>Typical range:</strong> " + fmt(w * 120, 0) + " to " + fmt(w * 180, 0) + " ml per day");
    }
    bind(["weight", "feeds"], calc); calc();
})();
</script>
@endsection
