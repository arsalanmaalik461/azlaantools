@extends('layouts.app')

@section('title', 'Zakat on Shares and Stocks Calculator - Azlaan Tools')
@section('meta_description', 'Estimate zakat on shares and stocks online free. Enter your portfolio value and dividends to calculate zakat.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Zakat on Shares and Stocks Calculator</h1>
            <p class="lead text-muted">Estimate zakat on shares and stocks. Enter your portfolio value and dividends.</p>

            <div class="alert alert-warning small">
                <strong>Note:</strong> Scholars differ on zakat for shares (some say 2.5% of market value for trading shares; for long-term shares only on dividends). This calculator only gives an estimate — ask your mufti or scholar for the final decision. Confirm the nisab amount with your local gold/silver rate.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="methodSelect" class="form-label fw-semibold">Why do you hold these shares?</label>
                        <select class="form-select" id="methodSelect">
                            <option value="trading">Trading (to sell) — 2.5% of market value</option>
                            <option value="investment">Long-term investment — 2.5% on dividends only</option>
                        </select>
                        <div class="form-text">For trading, shares count as business goods; for investment, most scholars say zakat is due on dividends only.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sharesValue" class="form-label fw-semibold">Current market value of shares (Rs.)</label>
                            <input type="number" class="form-control" id="sharesValue" placeholder="Example: 200000" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="dividends" class="form-label fw-semibold">Yearly dividends (Rs.)</label>
                            <input type="number" class="form-control" id="dividends" placeholder="Example: 15000" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="debts" class="form-label fw-semibold">Loans / liabilities to subtract (Rs.)</label>
                            <input type="number" class="form-control" id="debts" placeholder="0" min="0" value="0">
                        </div>
                        <div class="col-md-6">
                            <label for="nisab" class="form-label fw-semibold">Nisab amount (Rs.)</label>
                            <input type="number" class="form-control" id="nisab" placeholder="Example: 150000" min="0">
                            <div class="form-text">Equal to the value of 52.5 tola silver or 7.5 tola gold — check with the current rate.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate Zakat</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <p class="mb-1 text-muted">Estimated zakat is</p>
                                <p class="display-5 fw-bold text-primary mb-2" id="zakatResult">Rs. 0</p>
                                <div class="text-start small">
                                    <p class="mb-1"><strong>Zakatable amount:</strong> <span id="baseAmount">-</span></p>
                                    <p class="mb-1"><strong>Method:</strong> <span id="methodUsed">-</span></p>
                                    <p class="mb-0"><strong>Nisab status:</strong> <span id="nisabNote">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose your purpose — trading or long-term investment.</li>
                <li>Enter market value, dividends, loans, and the nisab amount.</li>
                <li>Press "Calculate Zakat" — see the estimate and confirm with your mufti.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var methodSelect = document.getElementById('methodSelect');
    var sharesValue = document.getElementById('sharesValue');
    var dividends = document.getElementById('dividends');
    var debts = document.getElementById('debts');
    var nisab = document.getElementById('nisab');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var zakatResult = document.getElementById('zakatResult');
    var baseAmount = document.getElementById('baseAmount');
    var methodUsed = document.getElementById('methodUsed');
    var nisabNote = document.getElementById('nisabNote');

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
        return 'Rs. ' + Math.round(n).toLocaleString('en-PK');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var sv = parseFloat(sharesValue.value) || 0;
        var dv = parseFloat(dividends.value) || 0;
        var db = parseFloat(debts.value) || 0;
        var ns = parseFloat(nisab.value);
        if (sv < 0 || dv < 0 || db < 0) {
            showError('Amounts cannot be negative.');
            return;
        }
        if (isNaN(ns) || ns <= 0) {
            showError('Please enter the nisab amount.');
            return;
        }
        if (sv === 0 && dv === 0) {
            showError('Enter at least a market value or dividends.');
            return;
        }

        var method = methodSelect.value;
        var base, methodText;
        if (method === 'trading') {
            base = sv + dv - db;
            methodText = 'Trading: 2.5% on market value + dividends';
        } else {
            base = dv - db;
            methodText = 'Investment: 2.5% on dividends only';
        }
        if (base < 0) base = 0;

        var zakat = 0;
        var note;
        if (base >= ns) {
            zakat = base * 0.025;
            note = 'Amount is above nisab — zakat is due (after a full lunar year).';
        } else {
            note = 'Amount is below nisab — no zakat is due by this calculation.';
        }

        zakatResult.textContent = fmtRs(zakat);
        baseAmount.textContent = fmtRs(base);
        methodUsed.textContent = methodText;
        nisabNote.textContent = note;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
