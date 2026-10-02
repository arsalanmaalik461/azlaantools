@extends('layouts.app')

@section('title', 'Zakat Nisab Threshold Checker - Azlaan Tools')
@section('meta_description', 'Check the nisab limit free online. Find the Zakat nisab in PKR from the gold or silver rate.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Zakat Nisab Threshold Checker</h1>
            <p class="lead text-muted">Enter your gold/silver rates and find out if your wealth is above the nisab limit or not.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="goldRate" class="form-label fw-semibold">Gold Rate per Tola (PKR)</label>
                            <input type="number" class="form-control" id="goldRate" min="1" step="1" placeholder="e.g. 290000">
                            <small class="text-muted">Enter the gold rate for today</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="silverRate" class="form-label fw-semibold">Silver Rate per Tola (PKR)</label>
                            <input type="number" class="form-control" id="silverRate" min="1" step="1" placeholder="e.g. 3600">
                            <small class="text-muted">Enter the silver rate for today</small>
                        </div>
                        <div class="col-12">
                            <label for="wealthInput" class="form-label fw-semibold">Your Total Zakatable Wealth (PKR)</label>
                            <input type="number" class="form-control" id="wealthInput" min="0" step="1" placeholder="e.g. 500000">
                            <small class="text-muted">Cash, bank balance, gold/silver, trade goods — minus any debt</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Check Nisab</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light"><tr><th>Nisab Base</th><th>Amount</th><th>Nisab Value (PKR)</th></tr></thead>
                                <tbody>
                                    <tr><td>Gold — 7.5 tola (87.48 g)</td><td>7.5 tola</td><td id="nisabGold">-</td></tr>
                                    <tr><td>Silver — 52.5 tola (612.36 g)</td><td>52.5 tola</td><td id="nisabSilver">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert" id="verdictBox"></div>
                        <div class="alert alert-info">
                            <strong>Zakat amount:</strong> if Zakat is due, then <span id="zakatAmt" class="fw-bold"></span>
                            <span class="text-muted">(2.5% of wealth)</span>
                        </div>
                        <small class="text-muted d-block">Rates change daily — use the correct rate for today. For religious rulings, confirm with a trusted scholar.</small>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the per-tola gold and silver rates for today (PKR).</li>
                <li>Enter your total zakatable wealth (minus any debt).</li>
                <li>Click Check Nisab — see both nisab limits and the decision.</li>
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
    var goldRate = document.getElementById('goldRate');
    var silverRate = document.getElementById('silverRate');
    var wealthInput = document.getElementById('wealthInput');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var nisabGold = document.getElementById('nisabGold');
    var nisabSilver = document.getElementById('nisabSilver');
    var verdictBox = document.getElementById('verdictBox');
    var zakatAmt = document.getElementById('zakatAmt');

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

    goBtn.addEventListener('click', function () {
        hideError();
        var g = parseFloat(goldRate.value);
        var s = parseFloat(silverRate.value);
        var w = parseFloat(wealthInput.value);
        if (isNaN(g) || g <= 0) { showError('Please enter the per-tola gold rate.'); return; }
        if (isNaN(s) || s <= 0) { showError('Enter the per-tola silver rate.'); return; }
        if (isNaN(w) || w < 0) { showError('Enter your total wealth amount.'); return; }

        var nGold = 7.5 * g;
        var nSilver = 52.5 * s;
        nisabGold.textContent = fmt(nGold);
        nisabSilver.textContent = fmt(nSilver);

        var aboveGold = w >= nGold;
        var aboveSilver = w >= nSilver;
        verdictBox.className = 'alert';
        if (aboveGold && aboveSilver) {
            verdictBox.classList.add('alert-success');
            verdictBox.innerHTML = '<strong>Zakat is DUE.</strong> Your wealth is above both the gold and silver nisab. You must pay Zakat.';
            zakatAmt.textContent = fmt(w * 0.025);
        } else if (!aboveGold && !aboveSilver) {
            verdictBox.classList.add('alert-secondary');
            verdictBox.innerHTML = '<strong>Zakat is NOT due.</strong> Your wealth is below the nisab limit.';
            zakatAmt.textContent = fmt(0);
        } else {
            verdictBox.classList.add('alert-warning');
            verdictBox.innerHTML = '<strong>A disputed case.</strong> Your wealth is above one nisab and below the other. By some scholars, Zakat is due (on the silver nisab). Confirm with a trusted scholar.';
            zakatAmt.textContent = fmt(w * 0.025) + ' (if ruled due)';
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
