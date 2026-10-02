@extends('layouts.app')

@section('title', 'Zakat on Gold and Silver Calculator - Azlaan Tools')
@section('meta_description', 'Calculate zakat on gold and silver — enter weight in tola or grams. Free online calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Zakat on Gold and Silver Calculator</h1>
            <p class="lead text-muted">Enter your gold and silver weight (in tola or grams) and today's rates — the tool will check nisab and show your zakat amount.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="goldWt" class="form-label fw-semibold">Gold weight</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="goldWt" min="0" step="any" placeholder="0">
                                <select class="form-select" id="goldUnit" style="max-width:110px">
                                    <option value="tola" selected>Tola</option>
                                    <option value="gram">Gram</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="silverWt" class="form-label fw-semibold">Silver weight</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="silverWt" min="0" step="any" placeholder="0">
                                <select class="form-select" id="silverUnit" style="max-width:110px">
                                    <option value="tola" selected>Tola</option>
                                    <option value="gram">Gram</option>
                                </select>
                            </div>
                            <div class="form-text">1 tola = 11.6638 gram</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cash" class="form-label fw-semibold">Cash / savings (PKR, optional)</label>
                        <input type="number" class="form-control" id="cash" min="0" step="any" placeholder="e.g. 50000">
                        <div class="form-text">Bank balance, cash kept at home, etc. — include them for zakat.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="goldRate" class="form-label fw-semibold">Gold rate (PKR per tola)</label>
                            <input type="number" class="form-control" id="goldRate" min="0" step="any" placeholder="e.g. 265000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="silverRate" class="form-label fw-semibold">Silver rate (PKR per tola)</label>
                            <input type="number" class="form-control" id="silverRate" min="0" step="any" placeholder="e.g. 2900">
                        </div>
                    </div>
                    <div class="form-text mb-3">Rates change daily — confirm today's rate from your market or a trusted source.</div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Zakat</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center mb-3">
                            <div class="display-5 fw-bold text-primary" id="zakatAmt">Rs 0</div>
                            <div class="text-muted">Zakat (2.5%) — estimate</div>
                        </div>
                        <div class="alert" id="nisabBox" role="alert"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light"><tr><th>Item</th><th>Weight</th><th>Value (PKR)</th></tr></thead>
                                <tbody id="breakBody"></tbody>
                                <tfoot><tr><td colspan="2" class="text-end fw-bold">Total zakatable amount</td><td class="fw-bold" id="totalVal"></td></tr></tfoot>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">This is only an estimate. For special cases (loans, business goods, etc.), ask your scholar or mufti.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your gold and silver weight in tola or grams.</li>
                <li>Enter cash savings (if any) and today's gold/silver rates.</li>
                <li>Press "Calculate Zakat" — you will get the nisab and zakat amount instantly.</li>
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
    var zakatAmt = document.getElementById('zakatAmt');
    var nisabBox = document.getElementById('nisabBox');
    var breakBody = document.getElementById('breakBody');
    var totalVal = document.getElementById('totalVal');

    var TOLA_G = 11.6638;
    var GOLD_NISAB_G = 87.48;   // 7.5 tola
    var SILVER_NISAB_G = 612.36; // 52.5 tola

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) {
        return 'Rs ' + Math.round(n).toLocaleString('en-PK');
    }
    function num(id) {
        var v = parseFloat(document.getElementById(id).value);
        return isNaN(v) ? 0 : v;
    }
    function addRow(item, weight, value) {
        var tr = document.createElement('tr');
        var a = document.createElement('td'); a.textContent = item;
        var b = document.createElement('td'); b.textContent = weight;
        var c = document.createElement('td'); c.textContent = fmt(value);
        tr.appendChild(a); tr.appendChild(b); tr.appendChild(c);
        breakBody.appendChild(tr);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var goldWt = num('goldWt'), silverWt = num('silverWt'), cash = num('cash');
        var goldRate = num('goldRate'), silverRate = num('silverRate');
        if (goldWt < 0 || silverWt < 0 || cash < 0) { showError('Weight or amount cannot be negative.'); return; }
        if (goldWt === 0 && silverWt === 0 && cash === 0) {
            showError('Enter at least some gold, silver, or cash.');
            return;
        }
        if (goldWt > 0 && goldRate <= 0) { showError('Enter the gold rate today (per tola).'); return; }
        if (silverWt > 0 && silverRate <= 0) { showError('Enter the silver rate today (per tola).'); return; }

        var goldUnit = document.getElementById('goldUnit').value;
        var silverUnit = document.getElementById('silverUnit').value;
        var goldG = goldUnit === 'tola' ? goldWt * TOLA_G : goldWt;
        var silverG = silverUnit === 'tola' ? silverWt * TOLA_G : silverWt;

        var goldVal = (goldG / TOLA_G) * goldRate;
        var silverVal = (silverG / TOLA_G) * silverRate;
        var total = goldVal + silverVal + cash;

        var goldNisabVal = (GOLD_NISAB_G / TOLA_G) * goldRate;
        var silverNisabVal = (SILVER_NISAB_G / TOLA_G) * silverRate;

        breakBody.innerHTML = '';
        if (goldWt > 0) { addRow('Gold', goldG.toFixed(2) + ' gram (' + (goldG / TOLA_G).toFixed(2) + ' tola)', goldVal); }
        if (silverWt > 0) { addRow('Silver', silverG.toFixed(2) + ' gram (' + (silverG / TOLA_G).toFixed(2) + ' tola)', silverVal); }
        if (cash > 0) { addRow('Cash', '-', cash); }
        totalVal.textContent = fmt(total);

        var zakat = total * 0.025;
        if (total >= goldNisabVal && goldNisabVal > 0) {
            zakatAmt.textContent = fmt(zakat);
            nisabBox.className = 'alert alert-success';
            nisabBox.textContent = 'Your total is above the gold nisab (' + fmt(goldNisabVal) + ') — zakat is due.';
        } else if (total >= silverNisabVal && silverNisabVal > 0) {
            zakatAmt.textContent = fmt(zakat);
            nisabBox.className = 'alert alert-warning';
            nisabBox.textContent = 'Your amount is above the silver nisab (' + fmt(silverNisabVal) + ') but below the gold nisab — most scholars say it is better to pay zakat. Confirm with your scholar.';
        } else {
            zakatAmt.textContent = 'Rs 0';
            nisabBox.className = 'alert alert-info';
            nisabBox.textContent = 'Your total is below nisab — no zakat is due.';
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
