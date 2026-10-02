@extends('layouts.app')

@section('title', 'Loan EMI Calculator - Azlaan Tools')
@section('meta_description', 'Calculate monthly installment (EMI), total interest and total payable from loan amount, rate and tenure.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Loan EMI Calculator</h1>
            <p class="lead text-muted">Enter the loan amount, rate and tenure — get the monthly installment (EMI), total interest and total payable instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="loanAmount" class="form-label fw-semibold">Loan amount (Rs)</label>
                            <input type="number" class="form-control" id="loanAmount" placeholder="e.g. 500000" min="1" step="0.01">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="annualRate" class="form-label fw-semibold">Annual rate (% per year)</label>
                            <input type="number" class="form-control" id="annualRate" placeholder="e.g. 18" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="loanMonths" class="form-label fw-semibold">Tenure (months)</label>
                            <input type="number" class="form-control" id="loanMonths" placeholder="e.g. 36" min="1" max="600" step="1">
                            <div class="form-text">Or tenure in years: <span id="yearsHint" class="fw-semibold">-</span></div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="calcBtn">Calculate EMI</button>
                        <button type="button" class="btn btn-outline-secondary" id="resetBtn">Reset</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4 d-none">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-12 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted">Monthly Installment (EMI)</small>
                                    <div class="fw-bold fs-4 text-primary" id="emiOut">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted">Total Interest</small>
                                    <div class="fw-bold fs-5 text-danger" id="intOut">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted">Total Payable</small>
                                    <div class="fw-bold fs-5" id="totOut">Rs 0</div>
                                </div></div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Principal vs Interest</h6>
                                <div class="progress" style="height: 26px;">
                                    <div class="progress-bar bg-primary" id="prinBar" role="progressbar" style="width:50%"></div>
                                    <div class="progress-bar bg-warning text-dark" id="intBar" role="progressbar" style="width:50%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 small">
                                    <span><span class="badge bg-primary">Principal</span> <span id="prinPct">50%</span></span>
                                    <span><span class="badge bg-warning text-dark">Interest</span> <span id="intPct">50%</span></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Formula: reducing-balance (monthly compounding). This is an estimate, not financial advice. The actual amount may differ due to bank rates, fees and terms.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How does it work?</h2>
                    <p class="mb-0">EMI = P × r × (1+r)^n / ((1+r)^n − 1), where P = loan amount, r = monthly rate (annual / 12 / 100), n = number of months. Interest is charged on the remaining balance with each installment, so early installments have more interest and less principal.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    function $(id) { return document.getElementById(id); }

    function fmt(n) {
        return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2, minimumFractionDigits: 2 });
    }

    function emiFor(p, annualRate, n) {
        var r = annualRate / 1200; // monthly rate
        if (r === 0) return p / n;
        var f = Math.pow(1 + r, n);
        return p * r * f / (f - 1);
    }

    function showError(msg) {
        var box = $('errorBox');
        if (msg) {
            box.textContent = msg;
            box.classList.remove('d-none');
        } else {
            box.classList.add('d-none');
        }
    }

    function calc() {
        showError(null);
        var p = parseFloat($('loanAmount').value);
        var rate = parseFloat($('annualRate').value);
        var n = parseInt($('loanMonths').value, 10);

        if (!isFinite(p) || p <= 0) { showError('Enter a loan amount greater than 0.'); return; }
        if (!isFinite(rate) || rate < 0 || rate > 100) { showError('Enter an annual rate between 0 and 100.'); return; }
        if (!isFinite(n) || n < 1 || n > 600) { showError('Enter a tenure of 1 to 600 months.'); return; }

        var emi = emiFor(p, rate, n);
        var total = emi * n;
        var interest = total - p;

        $('emiOut').textContent = fmt(emi);
        $('intOut').textContent = fmt(interest);
        $('totOut').textContent = fmt(total);

        var pp = total > 0 ? (p / total * 100) : 50;
        var ip = total > 0 ? (interest / total * 100) : 50;
        $('prinBar').style.width = pp + '%';
        $('intBar').style.width = ip + '%';
        $('prinPct').textContent = pp.toFixed(1) + '%';
        $('intPct').textContent = ip.toFixed(1) + '%';

        $('results').classList.remove('d-none');
    }

    $('calcBtn').addEventListener('click', calc);

    $('resetBtn').addEventListener('click', function () {
        $('loanAmount').value = '';
        $('annualRate').value = '';
        $('loanMonths').value = '';
        $('yearsHint').textContent = '-';
        $('results').classList.add('d-none');
        showError(null);
    });

    $('loanMonths').addEventListener('input', function () {
        var n = parseInt(this.value, 10);
        if (isFinite(n) && n > 0) {
            $('yearsHint').textContent = (n / 12).toFixed(1) + ' years';
        } else {
            $('yearsHint').textContent = '-';
        }
    });

    ['loanAmount', 'annualRate', 'loanMonths'].forEach(function (id) {
        $(id).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); calc(); }
        });
    });
})();
</script>
@endsection
