@extends('layouts.app')
@section('title', 'Relative Fat Mass RFM Calculator - Azlaan Tools')
@section('meta_description', 'Estimate your body fat percentage from height and waist measurements only. Free RFM calculator online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Relative Fat Mass (RFM) Calculator</h1>
            <p class="lead text-muted">Get an estimate of your body fat percentage from just your height and waist measurements. A better formula than BMI — a measuring tape is all you need.</p>

            <div class="alert alert-info">
                <strong>RFM formula:</strong> Men: 64 &minus; (20 &times; height &divide; waist) &nbsp;|&nbsp; Women: 76 &minus; (20 &times; height &divide; waist). Height and waist must both be in the <strong>same unit</strong>.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="genderSel" class="form-label fw-semibold">Gender</label>
                            <select class="form-select" id="genderSel">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="unitSel" class="form-label fw-semibold">Unit</label>
                            <select class="form-select" id="unitSel">
                                <option value="cm" selected>Centimeters (cm)</option>
                                <option value="in">Inches</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="heightIn" class="form-label fw-semibold">Height</label>
                            <input type="number" class="form-control" id="heightIn" placeholder="e.g. 170" min="50" max="250" step="0.1">
                            <div class="form-text">Stand straight and measure from head to foot.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="waistIn" class="form-label fw-semibold">Waist (at belly button level)</label>
                            <input type="number" class="form-control" id="waistIn" placeholder="e.g. 85" min="20" max="200" step="0.1">
                            <div class="form-text">Wrap the tape around your waist and breathe out.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center mb-3">
                            <div class="display-4 fw-bold text-primary" id="rfmVal">0%</div>
                            <div class="text-muted">Estimated body fat (RFM)</div>
                        </div>
                        <div class="progress mb-3" style="height: 22px;">
                            <div class="progress-bar" id="rfmBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="alert" id="catBox" role="alert"></div>
                        <p class="text-muted small mb-0">This is an estimate, not medical advice. See a doctor for medical decisions.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">RFM Categories (estimate)</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead><tr><th>Category</th><th>Men</th><th>Women</th></tr></thead>
                            <tbody>
                                <tr><td>Low</td><td>&lt; 18%</td><td>&lt; 25%</td></tr>
                                <tr><td>Healthy</td><td>18&ndash;24%</td><td>25&ndash;32%</td></tr>
                                <tr><td>Overweight</td><td>25&ndash;29%</td><td>33&ndash;38%</td></tr>
                                <tr><td>Obese</td><td>&ge; 30%</td><td>&ge; 39%</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select gender and unit.</li>
                <li>Enter your height and waist (at belly button level).</li>
                <li>Press "Calculate" to see your body fat % and category.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var genderSel = document.getElementById('genderSel');
    var unitSel = document.getElementById('unitSel');
    var heightIn = document.getElementById('heightIn');
    var waistIn = document.getElementById('waistIn');
    var rfmVal = document.getElementById('rfmVal');
    var rfmBar = document.getElementById('rfmBar');
    var catBox = document.getElementById('catBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function categoryFor(rfm, gender) {
        if (gender === 'male') {
            if (rfm < 18) return ['Low', 'alert-info', 'Your body fat is in the low range. If you feel weak, pay attention to your diet.'];
            if (rfm <= 24) return ['Healthy', 'alert-success', 'Good job! You are in the healthy range. Keep up this routine.'];
            if (rfm <= 29) return ['Overweight', 'alert-warning', 'You are in the overweight range. A daily walk and less sugar can help.'];
            return ['Obese', 'alert-danger', 'You are in the obese range. Ask a doctor or nutritionist for a weight loss plan.'];
        }
        if (rfm < 25) return ['Low', 'alert-info', 'Your body fat is in the low range.'];
        if (rfm <= 32) return ['Healthy', 'alert-success', 'Good job! You are in the healthy range.'];
        if (rfm <= 38) return ['Overweight', 'alert-warning', 'You are in the overweight range. Walking and a balanced diet can help.'];
        return ['Obese', 'alert-danger', 'You are in the obese range. Please see a doctor or nutritionist.'];
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var h = parseFloat(heightIn.value);
        var w = parseFloat(waistIn.value);
        var unit = unitSel.value;
        var gender = genderSel.value;

        if (isNaN(h) || isNaN(w)) { showError('Please enter both height and waist.'); return; }
        var hCm = unit === 'in' ? h * 2.54 : h;
        var wCm = unit === 'in' ? w * 2.54 : w;
        if (hCm < 100 || hCm > 250) { showError('Height looks wrong (it should be between 100 and 250 cm).'); return; }
        if (wCm < 40 || wCm > 200) { showError('Waist looks wrong (it should be between 40 and 200 cm).'); return; }
        if (wCm >= hCm) { showError('Waist must be less than height. Please check your measurements again.'); return; }

        var rfm = gender === 'male'
            ? 64 - 20 * (hCm / wCm)
            : 76 - 20 * (hCm / wCm);
        rfm = Math.round(rfm * 10) / 10;

        rfmVal.textContent = rfm.toFixed(1) + '%';
        var pct = Math.min(100, Math.max(0, Math.round(rfm * 2)));
        rfmBar.style.width = pct + '%';
        rfmBar.textContent = rfm.toFixed(1) + '%';

        var cat = categoryFor(rfm, gender);
        catBox.className = 'alert ' + cat[1];
        catBox.innerHTML = '';
        var strong = document.createElement('strong');
        strong.textContent = 'Category: ' + cat[0];
        var br = document.createElement('br');
        var txt = document.createTextNode(cat[2]);
        catBox.appendChild(strong);
        catBox.appendChild(br);
        catBox.appendChild(txt);

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
