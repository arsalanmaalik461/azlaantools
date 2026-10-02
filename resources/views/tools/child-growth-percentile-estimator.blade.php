@extends('layouts.app')

@section('title', 'Child Growth Percentile Estimator - Azlaan Tools')
@section('meta_description', 'Estimate your child growth percentile online free. Compare height and weight with WHO growth standards.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Child Growth Percentile Estimator</h1>
            <p class="lead text-muted">Get your child's growth percentile estimate from WHO growth charts — using age, height and weight.</p>

            <div class="alert alert-warning small">
                <strong>Disclaimer:</strong> This is only an estimate, not a replacement for medical advice. Before making any decision about your child's growth, please consult a doctor (pediatrician).
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sexSelect" class="form-label fw-semibold">Child's Gender</label>
                            <select class="form-select" id="sexSelect">
                                <option value="boy">Boy</option>
                                <option value="girl">Girl</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ageMonths" class="form-label fw-semibold">Age (in months, 0-60)</label>
                            <input type="number" class="form-control" id="ageMonths" placeholder="e.g. 24" min="0" max="60">
                            <div class="form-text">1 year = 12 months, 2 years = 24 months.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="weightInput" class="form-label fw-semibold">Weight (kg)</label>
                            <input type="number" class="form-control" id="weightInput" placeholder="e.g. 12" min="0" step="0.1">
                        </div>
                        <div class="col-md-6">
                            <label for="heightInput" class="form-label fw-semibold">Height (cm)</label>
                            <input type="number" class="form-control" id="heightInput" placeholder="e.g. 87" min="0" step="0.1">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Get Percentile</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <p class="text-muted mb-1 small">Weight-for-age percentile</p>
                                        <p class="h3 fw-bold text-primary mb-1" id="wPct">-</p>
                                        <p class="small mb-0" id="wNote"></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1 small">Height-for-age percentile</p>
                                        <p class="h3 fw-bold text-primary mb-1" id="hPct">-</p>
                                        <p class="small mb-0" id="hNote"></p>
                                    </div>
                                </div>
                                <hr>
                                <p class="small text-muted mb-0" id="explainText"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the child's gender, age (in months), weight (kg) and height (cm).</li>
                <li>Press "Get Percentile".</li>
                <li>Compare the percentile with the WHO reference — and see a doctor if you have any concern.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var sexSelect = document.getElementById('sexSelect');
    var ageMonths = document.getElementById('ageMonths');
    var weightInput = document.getElementById('weightInput');
    var heightInput = document.getElementById('heightInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var wPct = document.getElementById('wPct');
    var hPct = document.getElementById('hPct');
    var wNote = document.getElementById('wNote');
    var hNote = document.getElementById('hNote');
    var explainText = document.getElementById('explainText');

    // WHO 2006 child growth standards, approximate median values.
    // [ageMonths, weightBoyKg, weightGirlKg, heightBoyCm, heightGirlCm]
    var WHO = [
        [0, 3.3, 3.2, 49.9, 49.1],
        [6, 7.9, 7.3, 67.6, 65.7],
        [12, 9.6, 8.9, 75.7, 74.0],
        [18, 10.9, 10.2, 82.3, 80.7],
        [24, 12.2, 11.5, 87.8, 86.4],
        [30, 13.3, 12.7, 91.9, 90.7],
        [36, 14.3, 13.9, 95.2, 94.1],
        [42, 15.3, 15.0, 98.9, 97.9],
        [48, 16.3, 16.1, 102.8, 101.6],
        [54, 17.3, 17.2, 106.4, 105.4],
        [60, 18.3, 18.2, 110.0, 109.4]
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function erf(x) {
        var t = 1 / (1 + 0.3275911 * Math.abs(x));
        var y = 1 - (((((1.061405429 * t - 1.453152027) * t) + 1.421413741) * t - 0.284496736) * t + 0.254829592) * t * Math.exp(-x * x);
        return x < 0 ? -y : y;
    }
    function normalCdf(z) {
        return 0.5 * (1 + erf(z / Math.SQRT2));
    }

    function interpolate(age, sex, isWeight) {
        var lo = WHO[0], hi = WHO[WHO.length - 1];
        for (var i = 0; i < WHO.length - 1; i++) {
            if (age >= WHO[i][0] && age <= WHO[i + 1][0]) {
                lo = WHO[i];
                hi = WHO[i + 1];
                break;
            }
        }
        var frac = hi[0] === lo[0] ? 0 : (age - lo[0]) / (hi[0] - lo[0]);
        var wIdx = sex === 'boy' ? 1 : 2;
        var hIdx = sex === 'boy' ? 3 : 4;
        var median = isWeight
            ? lo[wIdx] + (hi[wIdx] - lo[wIdx]) * frac
            : lo[hIdx] + (hi[hIdx] - lo[hIdx]) * frac;
        var sdFrac = isWeight ? 0.10 : 0.035;
        return { median: median, sd: median * sdFrac };
    }

    function classify(z) {
        if (z >= -2 && z <= 2) return 'It is in the normal range.';
        if (z < -2) return 'It is below average - please consult a doctor.';
        return 'It is above average - please consult a doctor.';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var age = parseFloat(ageMonths.value);
        var wt = parseFloat(weightInput.value);
        var ht = parseFloat(heightInput.value);
        if (isNaN(age) || age < 0 || age > 60) {
            showError('Please enter age between 0 and 60 months.');
            return;
        }
        if (isNaN(wt) || wt <= 0 || wt > 80) {
            showError('Please enter a valid weight in kg.');
            return;
        }
        if (isNaN(ht) || ht <= 0 || ht > 150) {
            showError('Please enter a valid height in cm.');
            return;
        }
        var sex = sexSelect.value;

        var wRef = interpolate(age, sex, true);
        var hRef = interpolate(age, sex, false);
        var zW = (wt - wRef.median) / wRef.sd;
        var zH = (ht - hRef.median) / hRef.sd;
        var pW = Math.round(normalCdf(zW) * 100);
        var pH = Math.round(normalCdf(zH) * 100);

        wPct.textContent = pW + 'th';
        hPct.textContent = pH + 'th';
        wNote.textContent = classify(zW);
        hNote.textContent = classify(zH);
        explainText.textContent = 'For example, the ' + pW + 'th percentile means that among 100 children of the same age and gender, about ' + pW + ' weigh less. According to WHO, growth between the 3rd and 97th percentile is usually considered normal. This is only an estimate - only a doctor can give a final opinion.';

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
