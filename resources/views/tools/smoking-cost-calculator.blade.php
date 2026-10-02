@extends('layouts.app')

@section('title', 'Smoking Cost and Quit Savings Calculator — Free Online Tool')
@section('meta_description', 'See what smoking costs per month and year, and what quitting could save with health timeline milestones')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Smoking Cost and Quit Savings Calculator</h1>
            <p class="lead small text-muted">Enter your daily cigarettes and the price of a pack to see the true cost — and what quitting today could save over the years ahead.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="perday">Cigarettes per day</label><input type="number" class="form-control" id="perday" value="10" step="any"></div>                    <div class="mb-3"><label class="form-label" for="packprice">Price per pack (Rs)</label><input type="number" class="form-control" id="packprice" value="600" step="any"></div>                    <div class="mb-3"><label class="form-label" for="perpack">Cigarettes per pack</label><input type="number" class="form-control" id="perpack" value="20" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter how many cigarettes you smoke per day.</li><li>Enter the pack price in rupees and cigarettes per pack.</li><li>See daily, monthly and yearly costs plus quit savings.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Costs are simple price arithmetic at todays prices, with no inflation. Health timeline milestones are the widely published quit-smoking milestones (for example from public health quit programmes): circulation and lung function improve over weeks to months, and heart disease risk falls substantially over years — individual timelines vary. Estimate only — not medical advice.</p>
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
        var perDay = num("perday"), price = num("packprice"), perPack = num("perpack");
        if ([perDay, price, perPack].some(isNaN) || perDay <= 0 || price <= 0 || perPack <= 0) { out("Please enter valid positive values."); return; }
        var daily = perDay / perPack * price;
        var yearly = daily * 365;
        out("<strong>Cost per day:</strong> Rs " + fmt(daily, 0) + "<br><strong>Cost per month:</strong> Rs " + fmt(daily * 30.44, 0) + "<br><strong>Cost per year:</strong> Rs " + fmt(yearly, 0) + "<br><strong>If you quit today, saved in 1 year:</strong> Rs " + fmt(yearly, 0) + " — <strong>in 5 years:</strong> Rs " + fmt(yearly * 5, 0) + " — <strong>in 10 years:</strong> Rs " + fmt(yearly * 10, 0) + "<br><br><strong>Quit timeline (typical published milestones):</strong> after 20 minutes pulse and blood pressure begin to settle; after 12 hours carbon monoxide levels normalise; after 2 to 12 weeks circulation and lung function improve; after 1 year coronary heart disease risk is about half that of a smoker; after 10 years lung cancer risk falls to about half that of a smoker.");
    }
    bind(["perday", "packprice", "perpack"], calc); calc();
})();
</script>
@endsection
