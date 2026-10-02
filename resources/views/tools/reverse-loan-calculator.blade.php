@extends('layouts.app')

@section('title', 'Reverse Loan Calculator - Azlaan Tools')
@section('meta_description', 'Know your monthly installment? Find how much loan you can get. Reverse-solve loan amount, interest rate, tenure or EMI.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Reverse Loan Calculator</h1>
            <p class="lead text-muted">Know your installment amount and want to know how much loan you can get? First choose which value to solve, then enter the other three.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="solveFor" class="form-label fw-semibold">What do you want to solve?</label>
                        <select class="form-select" id="solveFor">
                            <option value="amount">Loan Amount</option>
                            <option value="rate">Annual Interest Rate</option>
                            <option value="months">Tenure (Months)</option>
                            <option value="emi">Monthly Installment (EMI)</option>
                        </select>
                        <div class="form-text" id="solveHint">Based on your monthly installment, see how much loan amount you can get.</div>
                    </div>

                    <div class="row g-3" id="inputRow">
                        <div class="col-12 col-md-4" id="wrapAmount">
                            <label for="rvAmount" class="form-label fw-semibold">Loan Amount (Rs)</label>
                            <input type="number" class="form-control" id="rvAmount" placeholder="500000" min="1" step="0.01">
                        </div>
                        <div class="col-12 col-md-4" id="wrapRate">
                            <label for="rvRate" class="form-label fw-semibold">Annual Interest Rate (%)</label>
                            <input type="number" class="form-control" id="rvRate" placeholder="18" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-12 col-md-4" id="wrapMonths">
                            <label for="rvMonths" class="form-label fw-semibold">Tenure (months)</label>
                            <input type="number" class="form-control" id="rvMonths" placeholder="36" min="1" max="600" step="1">
                        </div>
                        <div class="col-12 col-md-4 d-none" id="wrapEmi">
                            <label for="rvEmi" class="form-label fw-semibold">Monthly Installment (Rs)</label>
                            <input type="number" class="form-control" id="rvEmi" placeholder="20000" min="1" step="0.01">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="rvCalcBtn">Solve</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="rvError" role="alert"></div>

                    <div id="rvResults" class="mt-4 d-none">
                        <div class="card bg-light">
                            <div class="card-body text-center py-4">
                                <small class="text-muted" id="rvLabel">Loan Amount</small>
                                <div class="fw-bold fs-3 text-primary" id="rvValue">-</div>
                                <div class="small text-muted mt-1" id="rvDetail">-</div>
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">This is an approximate answer from the reducing-balance formula, not financial advice. Actual bank rates and terms may differ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    function $(id) { return document.getElementById(id); }

    var HINTS = {
        amount: 'From your monthly installment, calculate how much loan amount you can get.',
        rate: 'From loan amount, installment and tenure, find the annual interest rate.',
        months: 'From loan amount, rate and installment, find in how many months the loan will be paid off.',
        emi: 'Standard calculation — from loan amount, rate and tenure, get the monthly installment.'
    };

    var LABELS = {
        amount: 'Loan Amount',
        rate: 'Annual Interest Rate (% per year)',
        months: 'Tenure (months)',
        emi: 'Monthly Installment (EMI)'
    };

    function showError(msg) {
        var box = $('rvError');
        if (msg) { box.textContent = msg; box.classList.remove('d-none'); }
        else { box.classList.add('d-none'); }
    }

    function toggleTarget() {
        var target = $('solveFor').value;
        $('wrapAmount').classList.toggle('d-none', target === 'amount');
        $('wrapRate').classList.toggle('d-none', target === 'rate');
        $('wrapMonths').classList.toggle('d-none', target === 'months');
        $('wrapEmi').classList.toggle('d-none', target !== 'emi');
        $('solveHint').textContent = HINTS[target];
    }

    function emiFrom(p, rate, n) {
        var r = rate / 1200;
        if (r === 0) return p / n;
        var f = Math.pow(1 + r, n);
        return p * r * f / (f - 1);
    }

    // solve annual rate by bisection on the EMI equation
    function rateFrom(p, n, emi) {
        var lo = 0.0001, hi = 200, mid, e;
        for (var i = 0; i < 100; i++) {
            mid = (lo + hi) / 2;
            e = emiFrom(p, mid, n);
            if (e > emi) hi = mid; else lo = mid;
        }
        return (lo + hi) / 2;
    }

    function getNum(id) {
        var v = parseFloat($(id).value);
        return isFinite(v) ? v : NaN;
    }

    function validAmount(p) { return isFinite(p) && p > 0; }
    function validRate(r) { return isFinite(r) && r >= 0 && r <= 100; }
    function validMonths(n) { return isFinite(n) && n >= 1 && n <= 600; }
    function validEmi(e) { return isFinite(e) && e > 0; }

    function solve() {
        showError(null);
        var target = $('solveFor').value;
        var p = getNum('rvAmount'), rate = getNum('rvRate'), n = getNum('rvMonths'), emi = getNum('rvEmi');
        var answer = NaN, detail = '';

        if (target === 'amount') {
            if (!validRate(rate)) { showError('Enter annual interest rate between 0 and 100.'); return; }
            if (!validMonths(n)) { showError('Enter tenure from 1 to 600 months.'); return; }
            if (!validEmi(emi)) { showError('Enter monthly installment greater than 0.'); return; }
            var r = rate / 1200;
            if (r === 0) {
                answer = emi * n;
            } else {
                var f = Math.pow(1 + r, n);
                answer = emi * (f - 1) / (r * f);
            }
            detail = 'EMI Rs ' + emi.toLocaleString('en-PK') + ', rate ' + rate + '%, over ' + n + ' months';
        } else if (target === 'emi') {
            if (!validAmount(p)) { showError('Enter loan amount greater than 0.'); return; }
            if (!validRate(rate)) { showError('Enter annual interest rate between 0 and 100.'); return; }
            if (!validMonths(n)) { showError('Enter tenure from 1 to 600 months.'); return; }
            answer = emiFrom(p, rate, n);
            detail = 'Total payment ~ Rs ' + (answer * n).toLocaleString('en-PK', { maximumFractionDigits: 0 });
        } else if (target === 'months') {
            if (!validAmount(p)) { showError('Enter loan amount greater than 0.'); return; }
            if (!validRate(rate) || rate <= 0) { showError('Enter annual interest rate more than 0 and less than 100.'); return; }
            if (!validEmi(emi)) { showError('Enter monthly installment greater than 0.'); return; }
            var rr = rate / 1200;
            if (emi <= p * rr) { showError('Installment is too low to even cover the interest — enter a higher installment.'); return; }
            var months = Math.log(emi / (emi - p * rr)) / Math.log(1 + rr);
            answer = Math.ceil(months);
            detail = 'Exact: ' + months.toFixed(1) + ' months (rounded up)';
        } else if (target === 'rate') {
            if (!validAmount(p)) { showError('Enter loan amount greater than 0.'); return; }
            if (!validMonths(n)) { showError('Enter tenure from 1 to 600 months.'); return; }
            if (!validEmi(emi)) { showError('Enter monthly installment greater than 0.'); return; }
            if (emi <= p / n) { showError('Installment must be at least ' + (p / n).toLocaleString('en-PK', { maximumFractionDigits: 2 }) + ' (without interest).'); return; }
            answer = rateFrom(p, Math.round(n), emi);
            detail = 'Effective annual rate, on reducing balance';
        }

        if (!isFinite(answer) || answer <= 0) { showError('No answer found from this calculation — check your inputs.'); return; }

        var shown;
        if (target === 'rate') shown = answer.toFixed(2) + '%';
        else if (target === 'months') shown = Math.round(answer) + ' months';
        else shown = 'Rs ' + answer.toLocaleString('en-PK', { maximumFractionDigits: 2, minimumFractionDigits: 2 });

        $('rvLabel').textContent = LABELS[target];
        $('rvValue').textContent = shown;
        $('rvDetail').textContent = detail;
        $('rvResults').classList.remove('d-none');
    }

    $('solveFor').addEventListener('change', toggleTarget);
    $('rvCalcBtn').addEventListener('click', solve);
    ['rvAmount', 'rvRate', 'rvMonths', 'rvEmi'].forEach(function (id) {
        $(id).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); solve(); }
        });
    });

    toggleTarget();
})();
</script>
@endsection
