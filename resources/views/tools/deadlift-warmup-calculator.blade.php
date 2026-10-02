@extends('layouts.app')

@section('title', 'Deadlift Warmup Calculator - Azlaan Tools')
@section('meta_description', 'Calculate safe deadlift warmup sets and plate loading online for free. Warmup weight and plates per side.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Deadlift Warmup Calculator</h1>
            <p class="lead text-muted">Calculate the weight for your deadlift warmup sets — a warmup plate calculator for safe lifting. It also shows how many plates to load on each side of the bar.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="workWeight" class="form-label fw-semibold">Working weight</label>
                            <input type="number" class="form-control" id="workWeight" placeholder="e.g. 100" min="20">
                            <div class="form-text">Your main deadlift set</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unitSel" class="form-label fw-semibold">Unit</label>
                            <select class="form-select" id="unitSel">
                                <option value="kg">Kilograms (kg)</option>
                                <option value="lb">Pounds (lb)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="barSel" class="form-label fw-semibold">Bar weight</label>
                            <select class="form-select" id="barSel">
                                <option value="20">20 kg (standard)</option>
                                <option value="15">15 kg</option>
                                <option value="45">45 lb (standard)</option>
                                <option value="35">35 lb</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="schemeSel" class="form-label fw-semibold">Warmup scheme</label>
                        <select class="form-select" id="schemeSel">
                            <option value="standard">Standard — 4 sets (50%×8, 65%×5, 80%×3, 90%×1)</option>
                            <option value="light">Light — 3 sets (50%×5, 70%×3, 85%×1)</option>
                            <option value="heavy">Heavy day — 5 sets (40%×10, 55%×6, 70%×4, 85%×2, 95%×1)</option>
                        </select>
                        <div class="form-text">Sets and reps are set automatically for each scheme</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Warmup Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Warmup Plan — <span id="planHead"></span></h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr><th>Set</th><th class="text-end">Weight</th><th class="text-end">Reps</th><th>Plates (per side)</th></tr>
                                </thead>
                                <tbody id="planRows"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-success" id="mainSetBox" role="status"></div>
                        <p class="text-muted small">This is only an estimate, not medical advice. Always deadlift heavy with good form and ideally a coach or spotter. If this is your first time lifting this weight, start lighter.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your working weight (main set weight) and choose a unit.</li>
                <li>Select the bar weight and warmup scheme.</li>
                <li>Press <strong>Create Warmup Plan</strong> — you will get the weight and plates for each set.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var workWeight = document.getElementById('workWeight');
    var unitSel = document.getElementById('unitSel');
    var barSel = document.getElementById('barSel');
    var schemeSel = document.getElementById('schemeSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var planHead = document.getElementById('planHead');
    var planRows = document.getElementById('planRows');
    var mainSetBox = document.getElementById('mainSetBox');

    var SCHEMES = {
        standard: [[50, 8], [65, 5], [80, 3], [90, 1]],
        light: [[50, 5], [70, 3], [85, 1]],
        heavy: [[40, 10], [55, 6], [70, 4], [85, 2], [95, 1]]
    };
    var PLATES_KG = [25, 20, 15, 10, 5, 2.5, 1.25];
    var PLATES_LB = [45, 35, 25, 10, 5, 2.5];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function roundStep(w, unit) {
        var step = unit === 'kg' ? 2.5 : 5;
        return Math.max(step, Math.round(w / step) * step);
    }
    function plateList(target, bar, plates) {
        var perSide = (target - bar) / 2;
        if (perSide <= 0) return 'Bar only (empty bar)';
        var used = [];
        var rest = perSide;
        for (var i = 0; i < plates.length; i++) {
            while (rest >= plates[i] - 0.001) {
                used.push(plates[i]);
                rest -= plates[i];
            }
        }
        var txt = used.length ? used.join(' + ') : 'no plates';
        if (rest > 0.01) txt += ' (≈ ' + rest.toFixed(2) + ' left — closest fit)';
        return txt;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var w = parseFloat(workWeight.value);
        var unit = unitSel.value;
        var bar = parseFloat(barSel.value);
        if (isNaN(w) || w <= 0) { showError('Please enter a valid working weight.'); return; }
        if (bar <= 0) { showError('Please select a bar weight.'); return; }
        if (w <= bar) { showError('Working weight must be more than the bar weight.'); return; }
        var scheme = SCHEMES[schemeSel.value] || SCHEMES.standard;
        var plates = unit === 'kg' ? PLATES_KG : PLATES_LB;
        var u = ' ' + unit;

        planHead.textContent = w + u + ' working weight';
        planRows.innerHTML = '';
        scheme.forEach(function (s, i) {
            var pct = s[0], reps = s[1];
            var setW = roundStep(w * pct / 100, unit);
            if (setW < bar) setW = bar;
            var tr = document.createElement('tr');
            var tdS = document.createElement('td');
            tdS.textContent = 'Warmup ' + (i + 1) + ' (' + pct + '%)';
            var tdW = document.createElement('td');
            tdW.className = 'text-end fw-bold';
            tdW.textContent = setW + u;
            var tdR = document.createElement('td');
            tdR.className = 'text-end';
            tdR.textContent = reps;
            var tdP = document.createElement('td');
            tdP.textContent = plateList(setW, bar, plates) + ' ' + unit;
            tr.appendChild(tdS); tr.appendChild(tdW); tr.appendChild(tdR); tr.appendChild(tdP);
            planRows.appendChild(tr);
        });
        mainSetBox.textContent = 'Then your main set: ' + w + u + ' — rest 2-3 minutes between sets and keep your back straight.';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
