@extends('layouts.app')

@section('title', 'Weight Price Calculator - Azlaan Tools')
@section('meta_description', 'Find the price of any weight from the per-kilo rate - 250g, 750g, 1kg. Free grocery price tool, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Weight Price Calculator</h1>
            <p class="lead text-muted">Find the price of any weight instantly from the per-kilo rate - perfect for grocery shopping. <span class="text-nowrap">Example: 1 kilo of sugar is Rs. 150, so how much is 250 grams?</span></p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="rateInput" class="form-label fw-semibold">Rate (1 kilo / Rs.)</label>
                            <input type="number" class="form-control" id="rateInput" placeholder="e.g. 150" min="0" step="any" inputmode="decimal">
                            <div class="form-text">The rate for 1 kilogram in rupees.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="weightInput" class="form-label fw-semibold">Weight</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="weightInput" placeholder="e.g. 250" min="0" step="any" inputmode="decimal">
                                <select class="form-select" id="unitSel" style="max-width:110px;">
                                    <option value="g" selected>gram</option>
                                    <option value="kg">kilo</option>
                                </select>
                            </div>
                            <div class="form-text">How much weight do you want?</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-label fw-semibold small">Pick a quick weight:</div>
                        <div class="d-flex gap-2 flex-wrap" id="quickRow">
                            <button type="button" class="btn btn-outline-secondary btn-sm quick" data-g="100">100 g</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick" data-g="250">250 g</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick" data-g="500">500 g</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick" data-g="750">750 g</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick" data-g="1000">1 kg</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick" data-g="2000">2 kg</button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Find Price</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card border-success mb-3">
                            <div class="card-body text-center">
                                <div class="text-muted small" id="resultLabel"></div>
                                <div class="display-5 fw-bold text-success" id="resultTotal"></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle" id="rateTable">
                                <thead class="table-light"><tr><th>Weight</th><th>Price</th></tr></thead>
                                <tbody id="rateBody"></tbody>
                            </table>
                        </div>
                        <p class="small text-muted">Per gram rate: <strong id="perGram"></strong></p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the per-kilo rate in rupees.</li>
                <li>Type the weight and select gram or kilo (or press a quick button).</li>
                <li>Press <strong>Find Price</strong> - the total price and a table of common weights will appear below.</li>
            </ol>
            <p class="small text-muted">This tool is for help with the calculation - confirm the real price with the shopkeeper.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var rateInput = document.getElementById('rateInput');
    var weightInput = document.getElementById('weightInput');
    var unitSel = document.getElementById('unitSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultLabel = document.getElementById('resultLabel');
    var resultTotal = document.getElementById('resultTotal');
    var rateBody = document.getElementById('rateBody');
    var perGram = document.getElementById('perGram');

    var COMMON = [100, 250, 500, 750, 1000, 2000, 5000];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtRs(n) {
        return 'Rs. ' + (Math.round(n * 100) / 100).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }
    function fmtWeight(g) {
        if (g >= 1000) {
            var kg = g / 1000;
            return (kg % 1 === 0 ? kg : kg.toFixed(2)) + ' kilo';
        }
        return g + ' gram';
    }
    function gramsOf() {
        var w = parseFloat(weightInput.value);
        if (isNaN(w) || w <= 0) return null;
        return unitSel.value === 'kg' ? w * 1000 : w;
    }

    function calculate() {
        hideError();
        var rate = parseFloat(rateInput.value);
        if (isNaN(rate) || rate <= 0) { showError('Please enter a valid per-kilo rate.'); return; }
        var g = gramsOf();
        if (g === null) { showError('Please enter a valid weight.'); return; }
        if (g > 1000000) { showError('Weight is unrealistically large.'); return; }

        var perG = rate / 1000;
        var total = perG * g;
        resultLabel.textContent = fmtWeight(g) + ' @ ' + fmtRs(rate) + ' / kilo';
        resultTotal.textContent = fmtRs(total);
        perGram.textContent = fmtRs(perG);

        rateBody.innerHTML = '';
        var rows = COMMON.slice();
        if (rows.indexOf(Math.round(g)) === -1 && g <= 5000) rows.push(Math.round(g));
        rows.sort(function (a, b) { return a - b; });
        rows.forEach(function (wg) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td');
            td1.textContent = fmtWeight(wg);
            var td2 = document.createElement('td');
            td2.textContent = fmtRs(perG * wg);
            td2.className = 'fw-semibold';
            if (Math.round(g) === wg) tr.className = 'table-success';
            tr.appendChild(td1); tr.appendChild(td2);
            rateBody.appendChild(tr);
        });
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', calculate);

    var quickBtns = document.querySelectorAll('#quickRow .quick');
    quickBtns.forEach(function (b) {
        b.addEventListener('click', function () {
            weightInput.value = b.getAttribute('data-g');
            unitSel.value = 'g';
            if (rateInput.value.trim()) calculate();
        });
    });
    rateInput.addEventListener('input', function () {
        if (rateInput.value && weightInput.value && !results.classList.contains('d-none')) calculate();
    });
})();
</script>
@endsection
