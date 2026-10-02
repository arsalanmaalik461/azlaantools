@extends('layouts.app')

@section('title', 'Credit Interest Tracker - Azlaan Tools')
@section('meta_description', 'Monthly markup on a given loan — total return and installment calculation. Free interest calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Credit Interest Tracker</h1>
            <p class="lead text-muted">Monthly markup on a given loan — total return and installment calculation. This is only an educational calculation tool, not a lending or borrowing service.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="uiPrincipal" class="form-label fw-semibold">Principal (Rs) — given amount</label>
                            <input type="number" class="form-control" id="uiPrincipal" placeholder="e.g. 100000" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="uiRate" class="form-label fw-semibold">Monthly markup (%)</label>
                            <input type="number" class="form-control" id="uiRate" placeholder="e.g. 2" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="uiMonths" class="form-label fw-semibold">Duration (months)</label>
                            <input type="number" class="form-control" id="uiMonths" placeholder="e.g. 12" min="1" step="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="uiMode" class="form-label fw-semibold">Markup method</label>
                        <select class="form-select" id="uiMode">
                            <option value="flat">Flat markup (straight % on principal)</option>
                            <option value="reducing">Reducing balance (% on remaining principal)</option>
                        </select>
                        <div class="form-text">With reducing balance, markup is charged on the remaining amount after each installment.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="uiCalc">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="uiError" role="alert"></div>

                    <div id="uiResults" class="mt-4 d-none">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total Markup</small><div class="fw-bold text-danger" id="uiTotalMarkup">0</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total Return</small><div class="fw-bold" id="uiTotalPay">0</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Monthly Installment</small><div class="fw-bold text-primary" id="uiMonthly">0</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Effective yearly rate</small><div class="fw-bold" id="uiApr">0%</div></div></div></div>
                        </div>
                        <h2 class="h5">Monthly schedule</h2>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="table-light"><tr><th>Month</th><th class="text-end">Markup</th><th class="text-end">Installment</th><th class="text-end">Remaining principal</th></tr></thead>
                                <tbody id="uiSched"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning small" role="note">
                <strong>Disclaimer:</strong> This calculator is for education and record-keeping only. It is not a loan or financing service, and its results are not financial or legal advice. Talk to a qualified advisor before making financial decisions.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the given amount (principal), monthly markup % and duration.</li>
                <li>Press <strong>Calculate</strong> — you will get the total markup, total return and monthly installment.</li>
                <li>If you select reducing balance, the schedule is also built on the remaining amount.</li>
            </ol>
            <p class="text-muted small">Note: nothing is saved in this tool — calculate each time.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var uiPrincipal = document.getElementById('uiPrincipal');
    var uiRate = document.getElementById('uiRate');
    var uiMonths = document.getElementById('uiMonths');
    var uiMode = document.getElementById('uiMode');
    var uiCalc = document.getElementById('uiCalc');
    var uiError = document.getElementById('uiError');
    var uiResults = document.getElementById('uiResults');
    var uiTotalMarkup = document.getElementById('uiTotalMarkup');
    var uiTotalPay = document.getElementById('uiTotalPay');
    var uiMonthly = document.getElementById('uiMonthly');
    var uiApr = document.getElementById('uiApr');
    var uiSched = document.getElementById('uiSched');

    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function r2(n) { return Math.round(n * 100) / 100; }
    function showError(m) { uiError.textContent = m; uiError.classList.remove('d-none'); }
    function hideError() { uiError.classList.add('d-none'); uiError.textContent = ''; }

    uiCalc.addEventListener('click', function () {
        hideError();
        var P = Number(uiPrincipal.value);
        var ratePct = Number(uiRate.value);
        var n = Math.floor(Number(uiMonths.value));
        if (!(P > 0)) { showError('Enter principal more than 0.'); return; }
        if (!(ratePct >= 0)) { showError('Enter the monthly markup % correctly.'); return; }
        if (!(n > 0)) { showError('Enter a duration of at least 1 month.'); return; }

        var r = ratePct / 100;
        var rows = '';
        var totalMarkup, monthly;

        if (uiMode.value === 'flat') {
            totalMarkup = P * r * n;
            var totalPay = P + totalMarkup;
            monthly = totalPay / n;
            var mP = P / n, mI = totalMarkup / n;
            for (var i = 1; i <= n; i++) {
                var bal = r2(P - mP * i);
                rows += '<tr><td>' + i + '</td><td class="text-end">' + fmt(mI) + '</td><td class="text-end">' + fmt(monthly) + '</td><td class="text-end">' + fmt(Math.max(0, bal)) + '</td></tr>';
            }
            var totalPayF = r2(P + totalMarkup);
            var monthlyF = r2(totalPayF / n);
            uiTotalMarkup.textContent = fmt(totalMarkup);
            uiTotalPay.textContent = fmt(totalPayF);
            uiMonthly.textContent = fmt(monthlyF);
            uiApr.textContent = (ratePct * 12).toFixed(2) + '%';
            uiResults.classList.remove('d-none');
        } else {
            // reducing balance: equal monthly installment
            monthly = r === 0 ? P / n : P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
            totalMarkup = 0;
            var bal0 = P;
            for (var j = 1; j <= n; j++) {
                var intPart = bal0 * r;
                var prinPart = monthly - intPart;
                totalMarkup += intPart;
                bal0 = Math.max(0, bal0 - prinPart);
                rows += '<tr><td>' + j + '</td><td class="text-end">' + fmt(r2(intPart)) + '</td><td class="text-end">' + fmt(r2(monthly)) + '</td><td class="text-end">' + fmt(r2(bal0)) + '</td></tr>';
            }
            var totalPayR = r2(P + totalMarkup);
            uiTotalMarkup.textContent = fmt(r2(totalMarkup));
            uiTotalPay.textContent = fmt(totalPayR);
            uiMonthly.textContent = fmt(r2(monthly));
            var apr = Math.pow(1 + r, 12) - 1;
            uiApr.textContent = (apr * 100).toFixed(2) + '%';
            uiResults.classList.remove('d-none');
        }
        uiSched.innerHTML = rows;
    });
})();
</script>
@endsection
