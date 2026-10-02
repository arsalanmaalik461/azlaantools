@extends('layouts.app')

@section('title', 'Diaper Size by Weight Converter - Azlaan Tools')
@section('meta_description', 'Find the right diaper size from your baby weight in kg or lb - free online diaper size chart.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Diaper Size by Weight Converter</h1>
            <p class="lead text-muted">Find the right diaper size from your baby weight. Enter the weight — it will match with the size chart.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="weightInput" class="form-label fw-semibold">Baby weight</label>
                            <input type="number" class="form-control" id="weightInput" placeholder="e.g. 7.5" step="0.1" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unitSelect" class="form-label fw-semibold">Unit</label>
                            <select class="form-select" id="unitSelect">
                                <option value="kg" selected>kg</option>
                                <option value="lb">lb (pounds)</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Find Size</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="verdictBox"></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm" id="chartTable">
                                <thead class="table-light"><tr><th>Size</th><th>Weight (kg)</th><th>Weight (lb)</th><th>Match</th></tr></thead>
                                <tbody id="chartBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">This is only a guide, not medical advice. Every brand fits a little differently — check the fit: two fingers of space at the waist and no marks on the legs.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your baby weight in kg or lb.</li>
                <li>Press <strong>Find Size</strong> — the recommended size and the full chart will appear.</li>
                <li>If the weight falls between two sizes, take the bigger size (for comfort).</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var weightInput = document.getElementById('weightInput');
    var unitSelect = document.getElementById('unitSelect');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var verdictBox = document.getElementById('verdictBox');
    var chartBody = document.getElementById('chartBody');

    // Generic size chart (kg). Brands vary slightly; ranges overlap on purpose.
    var SIZES = [
        { size: 'Preemie (P)', min: 0, max: 2.7 },
        { size: 'Newborn (NB)', min: 0, max: 4.5 },
        { size: 'Size 1', min: 3.6, max: 6.5 },
        { size: 'Size 2', min: 5.5, max: 8.5 },
        { size: 'Size 3', min: 7, max: 12.5 },
        { size: 'Size 4', min: 9.5, max: 14.5 },
        { size: 'Size 5', min: 12.5, max: 18 },
        { size: 'Size 6', min: 14.5, max: 21 },
        { size: 'Size 7', min: 18, max: 99 }
    ];
    var KG_TO_LB = 2.20462;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtLb(kg) {
        if (kg >= 99) { return '18+ kg'; }
        return (kg * KG_TO_LB).toFixed(1);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        chartBody.innerHTML = '';
        var w = parseFloat(weightInput.value);
        if (isNaN(w) || w <= 0) { showError('Enter a valid weight (a number above 0).'); return; }
        if (w > 150) { showError('The weight looks too high — check the unit (kg/lb).'); return; }
        var kg = unitSelect.value === 'lb' ? w / KG_TO_LB : w;

        var matches = [];
        SIZES.forEach(function (s) {
            if (kg >= s.min && kg <= s.max) { matches.push(s); }
        });
        // If nothing matched (gap), take nearest by midpoint
        if (matches.length === 0) {
            var best = SIZES[0], bestD = Infinity;
            SIZES.forEach(function (s) {
                var mid = (s.min + s.max) / 2;
                var d = Math.abs(mid - kg);
                if (d < bestD) { bestD = d; best = s; }
            });
            matches.push(best);
        }

        var names = matches.map(function (s) { return s.size; }).join(' or ');
        verdictBox.textContent = kg.toFixed(1) + ' kg — recommended size: ' + names +
            (matches.length > 1 ? ' (weight in between — the bigger size will be more comfortable).' : '.');

        SIZES.forEach(function (s) {
            var tr = document.createElement('tr');
            var isMatch = matches.indexOf(s) >= 0;
            if (isMatch) { tr.className = 'table-success fw-bold'; }
            var tdS = document.createElement('td'); tdS.textContent = s.size;
            var tdKg = document.createElement('td'); tdKg.textContent = (s.min === 0 ? 'Up to ' : s.min + ' - ') + (s.max >= 99 ? '18+' : s.max);
            var tdLb = document.createElement('td'); tdLb.textContent = s.min === 0 ? 'Up to ' + fmtLb(s.max) : fmtLb(s.min) + ' - ' + fmtLb(s.max);
            var tdM = document.createElement('td'); tdM.textContent = isMatch ? 'BEST MATCH' : '';
            tr.appendChild(tdS); tr.appendChild(tdKg); tr.appendChild(tdLb); tr.appendChild(tdM);
            chartBody.appendChild(tr);
        });
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
