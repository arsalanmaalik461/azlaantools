@extends('layouts.app')

@section('title', 'Loan Eligibility Calculator - Azlaan Tools')
@section('meta_description', 'How much loan you can get on your salary — free online loan eligibility calculator with monthly installment estimate.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Loan Eligibility Calculator</h1>
            <p class="lead text-muted">Enter your monthly income — estimate how much loan the bank can give you and what the monthly installment will be.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="income" class="form-label fw-semibold">Monthly income (Rs)</label>
                            <input type="number" class="form-control" id="income" placeholder="e.g. 80000" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="otherIncome" class="form-label fw-semibold">Other monthly income (Rs)</label>
                            <input type="number" class="form-control" id="otherIncome" placeholder="e.g. 0" min="0" value="0">
                        </div>
                        <div class="col-md-6">
                            <label for="existing" class="form-label fw-semibold">Existing loan installments / month (Rs)</label>
                            <input type="number" class="form-control" id="existing" placeholder="e.g. 0" min="0" value="0">
                            <div class="form-text">Monthly installment of an existing loan, if any.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="loanType" class="form-label fw-semibold">Loan type</label>
                            <select class="form-select" id="loanType">
                                <option value="personal">Personal loan</option>
                                <option value="auto">Auto / car loan</option>
                                <option value="home">Home loan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tenure" class="form-label fw-semibold">Tenure (years)</label>
                            <input type="number" class="form-control" id="tenure" placeholder="e.g. 3" min="1" max="25" value="3">
                        </div>
                        <div class="col-md-6">
                            <label for="rate" class="form-label fw-semibold">Annual profit rate (%)</label>
                            <input type="number" class="form-control" id="rate" placeholder="e.g. 22" min="0" max="60" step="0.1" value="22">
                            <div class="form-text">Bank markup/profit rate. Enter an estimate.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Check Eligibility</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3"><div class="border rounded p-3 bg-light"><div class="text-muted small">Max loan amount</div><div class="h5 mb-0 text-primary" id="rEligible">–</div></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-3 bg-light"><div class="text-muted small">Monthly installment</div><div class="h5 mb-0" id="rInstall">–</div></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-3 bg-light"><div class="text-muted small">Total markup</div><div class="h5 mb-0" id="rMarkup">–</div></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-3 bg-light"><div class="text-muted small">Total payable</div><div class="h5 mb-0" id="rTotal">–</div></div></div>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0"><small id="rNote"></small></div>
                        <p class="text-muted mt-3 mb-0"><small>This is only an estimate — each bank has its own policy, DBR limit and charges. Rates can change, please confirm with the official bank.</small></p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your monthly income and any existing installment.</li>
                <li>Select loan type, tenure and profit rate.</li>
                <li>Press <strong>Check Eligibility</strong> — see max loan, installment and total markup.</li>
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

    var CAPS = { personal: 12, auto: 5, home: 50 };
    var TYPE_LABEL = { personal: 'Personal', auto: 'Auto', home: 'Home' };

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isNaN(v) ? 0 : v; }

    goBtn.addEventListener('click', function () {
        hideError();
        var income = num('income'), other = num('otherIncome'), existing = num('existing');
        var tenure = num('tenure'), rate = num('rate');
        var type = document.getElementById('loanType').value;

        if (income <= 0) { showError('Please enter your monthly income.'); return; }
        if (tenure < 1 || tenure > 25) { showError('Tenure must be between 1 and 25 years.'); return; }
        if (rate < 0 || rate > 60) { showError('Enter a profit rate between 0 and 60%.'); return; }

        var netIncome = income + other - existing;
        if (netIncome <= 0) { showError('Existing installments are higher than income — eligibility is not possible.'); return; }

        // Debt Burden Ratio: max 50% of net monthly income as installment (SBP guideline style cap)
        var maxInstall = netIncome * 0.5;
        var r = rate / 12 / 100;
        var n = Math.round(tenure * 12);
        var pv;
        if (r === 0) { pv = maxInstall * n; }
        else { pv = maxInstall * (1 - Math.pow(1 + r, -n)) / r; }

        // Income-multiple cap per loan type (annual income multiple)
        var cap = CAPS[type] * netIncome * 12;
        var eligible = Math.min(pv, cap);
        var limitedBy = (cap < pv) ? 'income-multiple cap (' + CAPS[type] + 'x annual income)' : '50% debt-burden limit';

        // Recompute actual installment for the eligible amount
        var install = (r === 0) ? eligible / n : eligible * r / (1 - Math.pow(1 + r, -n));
        var total = install * n;
        var markup = total - eligible;

        document.getElementById('rEligible').textContent = fmt(eligible);
        document.getElementById('rInstall').textContent = fmt(install);
        document.getElementById('rMarkup').textContent = fmt(markup);
        document.getElementById('rTotal').textContent = fmt(total);
        document.getElementById('rNote').textContent =
            TYPE_LABEL[type] + ' loan: eligibility is limited to ' + fmt(eligible) + ' due to the ' + limitedBy +
            '. Monthly installment will be ' + Math.round((install / netIncome) * 100) + '% of your net income.';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
