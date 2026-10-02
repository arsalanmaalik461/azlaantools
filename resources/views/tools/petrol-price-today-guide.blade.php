@extends('layouts.app')

@section('title', 'Petrol Price Today Guide - Azlaan Tools')
@section('meta_description', 'How to check the latest petrol and diesel prices in Pakistan, plus the OGRA revision schedule — free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Petrol Price Today Guide</h1>
            <p class="lead text-muted">Petrol and diesel prices in Pakistan are revised by <strong>OGRA</strong> on the <strong>1st and 16th of every month</strong> (fortnightly). Below you will find how to check prices from official sources, and a calculator for your trip cost.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Fuel Trip Cost Calculator</h5>
                    <p class="text-muted small">Enter the current petrol price yourself (check it from the official sources below), then find out your trip cost.</p>
                    <div class="mb-3">
                        <label for="priceInput" class="form-label fw-semibold">Petrol / Diesel price (Rs per litre)</label>
                        <input type="number" class="form-control" id="priceInput" placeholder="e.g. 265.50" min="0" step="0.01">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="distInput" class="form-label fw-semibold">Trip distance (km)</label>
                            <input type="number" class="form-control" id="distInput" placeholder="e.g. 120" min="0" step="0.1">
                        </div>
                        <div class="col-md-6">
                            <label for="mileageInput" class="form-label fw-semibold">Car average (km per litre)</label>
                            <input type="number" class="form-control" id="mileageInput" placeholder="e.g. 12" min="0.1" step="0.1">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate Cost</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr><th class="w-50">Fuel needed</th><td id="resLitres"></td></tr>
                                    <tr><th>Total cost</th><td id="resTotal" class="fw-bold"></td></tr>
                                    <tr><th>Cost per kilometre</th><td id="resPerKm"></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Where to check the latest petrol price (official sources)</h2>
            <ol>
                <li><strong>OGRA notification:</strong> the Oil &amp; Gas Regulatory Authority issues a fortnightly price notification on its website <strong>ogra.org.pk</strong> — this is the most official source.</li>
                <li><strong>PSO (Pakistan State Oil):</strong> retail prices are listed on <strong>psopk.com</strong>.</li>
                <li><strong>Petrol pumps:</strong> every pump displays the OGRA notified price on its board — the price at the pump is the real price.</li>
                <li><strong>News channels:</strong> on revision day (the night of the 1st and 16th) the new price is announced on the news, and it applies from the next morning.</li>
            </ol>

            <h2 class="mt-4">Revision schedule</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>Revision</th><th>When announced</th><th>Effective from</th></tr></thead>
                    <tbody>
                        <tr><td>First revision of the month</td><td>Night before the 1st of every month</td><td>Morning of the 1st</td></tr>
                        <tr><td>Second revision of the month</td><td>Night before the 16th of every month</td><td>Morning of the 16th</td></tr>
                    </tbody>
                </table>
            </div>

            <h2 class="mt-4">What the price includes</h2>
            <ul>
                <li><strong>Ex-refinery price</strong> — the price set at the refinery</li>
                <li><strong>IFEM (Inland Freight Equalization Margin)</strong> — keeps the price the same across the country</li>
                <li><strong>Petroleum levy</strong> — government tax</li>
                <li><strong>Dealer and OMC margin</strong> — the pump and company profit</li>
            </ul>

            <div class="alert alert-warning mt-4">
                <strong>Note:</strong> This page does not show a live price — prices change every 15 days. Always confirm the real price from <strong>ogra.org.pk</strong> or the board at your nearest petrol pump. Rates can change — confirm on the official website.
            </div>

            <h2 class="mt-4">Ways to save fuel</h2>
            <ul>
                <li>Keep your car speed between 80–100 km/h — high speed uses more fuel.</li>
                <li>Keep tyre pressure as per the company instructions.</li>
                <li>Use the AC only as needed, and avoid idling (keeping the parked car running).</li>
                <li>Keep weight low — do not keep unneeded items in the trunk.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var priceInput = document.getElementById('priceInput');
    var distInput = document.getElementById('distInput');
    var mileageInput = document.getElementById('mileageInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resLitres = document.getElementById('resLitres');
    var resTotal = document.getElementById('resTotal');
    var resPerKm = document.getElementById('resPerKm');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var price = parseFloat(priceInput.value);
        var dist = parseFloat(distInput.value);
        var mileage = parseFloat(mileageInput.value);
        if (!(price > 0)) { showError('Enter the petrol price (Rs per litre).'); return; }
        if (!(dist > 0)) { showError('Enter the trip distance (in km).'); return; }
        if (!(mileage > 0)) { showError('Enter the car average (km per litre).'); return; }
        var litres = dist / mileage;
        var total = litres * price;
        var perKm = total / dist;
        resLitres.textContent = litres.toFixed(2) + ' litres';
        resTotal.textContent = 'Rs ' + total.toLocaleString('en-PK', { maximumFractionDigits: 0 });
        resPerKm.textContent = 'Rs ' + perKm.toFixed(2) + ' per km';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
