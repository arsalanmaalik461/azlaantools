@extends('layouts.app')

@section('title', 'Loan Prepayment Savings Calculator - Azlaan Tools')
@section('meta_description', 'How much interest you save and how much the loan term shortens with mid-term extra payments — see the full benefit of prepayment.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Loan Prepayment Savings Calculator</h1>
            <p class="lead text-muted">If you make extra payments (prepayment) in the middle of the term, see how much interest you save and how many months sooner the loan ends.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold">Loan details</h2>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label for="ppAmount" class="form-label fw-semibold">Loan amount (Rs)</label>
                            <input type="number" class="form-control" id="ppAmount" placeholder="500000" min="1" step="0.01">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="ppRate" class="form-label fw-semibold">Annual rate (%)</label>
                            <input type="number" class="form-control" id="ppRate" placeholder="18" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="ppMonths" class="form-label fw-semibold">Term (months)</label>
                            <input type="number" class="form-control" id="ppMonths" placeholder="36" min="1" max="600" step="1">
                        </div>
                    </div>

                    <h2 class="h6 fw-bold">Extra payment (prepayment)</h2>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="ppOnce" class="form-label fw-semibold">One-time extra amount (Rs)</label>
                            <input type="number" class="form-control" id="ppOnce" placeholder="0" min="0" step="0.01">
                            <div class="form-text">Optional — a one-time extra payment</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="ppOnceMonth" class="form-label fw-semibold">In which month?</label>
                            <input type="number" class="form-control" id="ppOnceMonth" placeholder="12" min="1" max="600" step="1">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="ppMonthly" class="form-label fw-semibold">Extra every month (Rs)</label>
                            <input type="number" class="form-control" id="ppMonthly" placeholder="0" min="0" step="0.01">
                            <div class="form-text">Optional — extra with each installment</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="ppCalcBtn">Calculate Benefit</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="ppError" role="alert"></div>

                    <div id="ppResults" class="mt-4 d-none">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">Interest saved</small>
                                    <div class="fw-bold fs-5 text-success" id="ppSaved">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">Term reduction</small>
                                    <div class="fw-bold fs-5" id="ppMonthsSaved">0 months</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">New payoff date</small>
                                    <div class="fw-bold fs-5" id="ppDate">-</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">Original total interest</small>
                                    <div class="fw-bold" id="ppOrigInt">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">New total interest</small>
                                    <div class="fw-bold" id="ppNewInt">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">New term</small>
                                    <div class="fw-bold" id="ppNewTerm">0 months</div>
                                </div></div>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Interest is charged on the remaining balance each month — extra payments reduce the principal, so the interest falls. Prepayment penalty or fees are not included in this calculation; confirm with your bank. Estimated calculation, not financial advice.</p>
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

    function money(n) {
        return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2, minimumFractionDigits: 2 });
    }

    function showError(msg) {
        var box = $('ppError');
        if (msg) { box.textContent = msg; box.classList.remove('d-none'); }
        else { box.classList.add('d-none'); }
    }

    function emiFor(p, rate, n) {
        var r = rate / 1200;
        if (r === 0) return p / n;
        var f = Math.pow(1 + r, n);
        return p * r * f / (f - 1);
    }

    // Simulate a loan; returns {interest, term}
    // prepayOnce: {amount, month} | null; monthlyExtra: number
    function simulate(p, rate, n, prepayOnce, monthlyExtra) {
        var r = rate / 1200;
        var emi = emiFor(p, rate, n);
        var bal = p, interest = 0, term = 0;
        for (var m = 1; m <= n; m++) {
            interest += bal * r;
            var pay = emi - bal * r;
            bal -= pay;
            if (prepayOnce && m === prepayOnce.month) {
                bal -= prepayOnce.amount;
            }
            if (monthlyExtra > 0) bal -= monthlyExtra;
            term = m;
            if (bal <= 0) { bal = 0; break; }
        }
        return { interest: interest, term: term, emi: emi };
    }

    function calc() {
        showError(null);
        var p = parseFloat($('ppAmount').value);
        var rate = parseFloat($('ppRate').value);
        var n = parseInt($('ppMonths').value, 10);
        var once = parseFloat($('ppOnce').value) || 0;
        var onceMonth = parseInt($('ppOnceMonth').value, 10) || 0;
        var monthlyExtra = parseFloat($('ppMonthly').value) || 0;

        if (!isFinite(p) || p <= 0) { showError('Enter a loan amount greater than 0.'); return; }
        if (!isFinite(rate) || rate < 0 || rate > 100) { showError('Enter an annual rate between 0 and 100.'); return; }
        if (!isFinite(n) || n < 1 || n > 600) { showError('Enter a tenure of 1 to 600 months.'); return; }
        if (once < 0) { showError('One-time extra amount cannot be negative.'); return; }
        if (monthlyExtra < 0) { showError('Monthly extra amount cannot be negative.'); return; }
        if (once > 0 && (!isFinite(onceMonth) || onceMonth < 1 || onceMonth > n)) { showError('Enter the prepayment month between 1 and ' + n + '.'); return; }
        if (once === 0 && monthlyExtra === 0) { showError('Enter an extra payment — one-time or monthly.'); return; }

        var base = simulate(p, rate, n, null, 0);
        var withPP = simulate(p, rate, n, once > 0 ? { amount: once, month: onceMonth } : null, monthlyExtra);

        var saved = base.interest - withPP.interest;
        var monthsSaved = base.term - withPP.term;

        $('ppSaved').textContent = money(Math.max(0, saved));
        $('ppMonthsSaved').textContent = monthsSaved + ' months';
        $('ppNewTerm').textContent = withPP.term + ' months';
        $('ppOrigInt').textContent = money(base.interest);
        $('ppNewInt').textContent = money(withPP.interest);

        // new payoff date = today + withPP.term months
        var d = new Date();
        d.setMonth(d.getMonth() + withPP.term);
        var monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                          'July', 'August', 'September', 'October', 'November', 'December'];
        $('ppDate').textContent = monthNames[d.getMonth()] + ' ' + d.getFullYear();

        $('ppResults').classList.remove('d-none');
    }

    $('ppCalcBtn').addEventListener('click', calc);

    ['ppAmount', 'ppRate', 'ppMonths', 'ppOnce', 'ppOnceMonth', 'ppMonthly'].forEach(function (id) {
        $(id).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); calc(); }
        });
    });
})();
</script>
@endsection
