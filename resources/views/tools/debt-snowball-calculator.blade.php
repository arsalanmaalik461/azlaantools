@extends('layouts.app')
@section('title', 'Debt Snowball Calculator - Azlaan Tools')
@section('meta_description', 'Pay off loans fastest with the debt snowball method — free online calculator with payoff plan and interest comparison.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Debt Snowball Calculator</h1>
            <p class="lead text-muted">Small debts first, then big ones — use the debt snowball method to see when you will become debt-free and how much interest you will save.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Your Debts</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="debtTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:30%">Debt name</th>
                                    <th style="width:24%">Balance</th>
                                    <th style="width:20%">APR %</th>
                                    <th style="width:22%">Min payment / month</th>
                                    <th style="width:4%"></th>
                                </tr>
                            </thead>
                            <tbody id="debtBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-4" id="addDebtBtn">+ Add Debt</button>

                    <div class="mb-3">
                        <label for="extraPay" class="form-label fw-semibold">Extra amount you can pay each month (on top of minimums)</label>
                        <input type="number" class="form-control" id="extraPay" min="0" step="any" value="5000">
                        <div class="form-text">This amount goes toward your smallest debt first — the more you add, the faster you become debt-free.</div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Payoff Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-3 mb-4" id="summaryCards"></div>
                        <h5>Payoff Order (snowball: smallest balance first)</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr><th>#</th><th>Debt</th><th>Starting balance</th><th>Paid off in month</th><th>Interest paid</th></tr>
                                </thead>
                                <tbody id="orderBody"></tbody>
                            </table>
                        </div>
                        <h5>Snowball vs Minimum-Only</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr><th>Strategy</th><th>Debt-free in</th><th>Total interest</th><th>Total paid</th></tr>
                                </thead>
                                <tbody id="compareBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Disclaimer:</strong> This is only an estimate, not financial advice. Interest rates and fees can change — please confirm with your bank or lender.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter all your debts: name, remaining amount (balance), yearly interest (APR %) and minimum monthly payment.</li>
                <li>Enter the extra monthly amount you can pay on top of the minimums.</li>
                <li>Press <strong>Calculate Payoff Plan</strong> — you will see your debt-free date, payoff order and an interest comparison.</li>
            </ol>

            <h2>What is the debt snowball?</h2>
            <p>Clear the debt with the smallest balance first (keep paying minimums on all of them, but put the extra only on the smallest). As soon as one debt is gone, add its payment to the next smallest debt — the "snowball" grows bigger and bigger, and your motivation grows too. Research says this method works better for most people because they get early wins.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var debtBody = document.getElementById('debtBody');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

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
        return Number(n).toLocaleString('en-US', { maximumFractionDigits: 0 });
    }

    function addDebt(name, bal, apr, minp) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" class="form-control form-control-sm d-name" placeholder="e.g. Credit card" value="' + (name || '') + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm d-bal" min="0" step="any" value="' + (bal != null ? bal : '') + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm d-apr" min="0" step="any" value="' + (apr != null ? apr : '') + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm d-min" min="0" step="any" value="' + (minp != null ? minp : '') + '"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger d-del" title="Remove">&times;</button></td>';
        debtBody.appendChild(tr);
        tr.querySelector('.d-del').addEventListener('click', function () {
            if (debtBody.rows.length > 1) { tr.remove(); }
            else { showError('At least one debt is required.'); }
        });
    }

    function readDebts() {
        var rows = debtBody.querySelectorAll('tr');
        var out = [];
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            out.push({
                name: r.querySelector('.d-name').value.trim() || ('Debt ' + (i + 1)),
                balance: parseFloat(r.querySelector('.d-bal').value) || 0,
                apr: parseFloat(r.querySelector('.d-apr').value) || 0,
                minPay: parseFloat(r.querySelector('.d-min').value) || 0
            });
        }
        return out;
    }

    function simulate(debts, extraMonthly, snowball) {
        var ds = debts.map(function (d) {
            return { name: d.name, balance: d.balance, rate: d.apr / 100 / 12, minPay: d.minPay, interestPaid: 0, startBalance: d.balance, payoffMonth: 0 };
        });
        var month = 0, totalInterest = 0, totalPaid = 0;
        var order = ds.slice().sort(function (a, b) { return a.balance - b.balance; });
        var safety = 0;
        while (safety < 1200) {
            safety++;
            var alive = ds.filter(function (d) { return d.balance > 0.005; });
            if (alive.length === 0) { break; }
            month++;
            var extra = extraMonthly;
            var targets = snowball ? order.filter(function (d) { return d.balance > 0.005; }) : alive;
            for (var t = 0; t < targets.length; t++) {
                var d = targets[t];
                if (d.balance <= 0.005) { continue; }
                var interest = d.balance * d.rate;
                d.interestPaid += interest;
                totalInterest += interest;
                d.balance += interest;
                var pay = d.minPay + (t === 0 ? extra : 0);
                if (pay > d.balance) { pay = d.balance; }
                d.balance -= pay;
                totalPaid += pay;
                if (d.balance <= 0.005) { d.balance = 0; d.payoffMonth = month; }
            }
            if (month > 600 && totalPaid === 0) { break; }
        }
        return { months: month, totalInterest: totalInterest, totalPaid: totalPaid, debts: ds, order: order, failed: safety >= 1200 };
    }

    function monthLabel(addMonths) {
        var d = new Date();
        d.setMonth(d.getMonth() + addMonths);
        return d.toLocaleString('en-US', { month: 'short', year: 'numeric' });
    }

    document.getElementById('addDebtBtn').addEventListener('click', function () { hideError(); addDebt('', '', '', ''); });
    document.getElementById('goBtn').addEventListener('click', function () {
        hideError();
        var debts = readDebts();
        for (var i = 0; i < debts.length; i++) {
            if (debts[i].balance <= 0) { showError('Each debt balance must be more than 0.'); return; }
            if (debts[i].minPay <= 0) { showError('Please enter the minimum monthly payment for each debt.'); return; }
        }
        var extra = Math.max(0, parseFloat(document.getElementById('extraPay').value) || 0);
        var withExtra = simulate(debts, extra, true);
        var minOnly = simulate(debts, 0, true);
        if (withExtra.failed || minOnly.failed) {
            showError('Your payments look smaller than the interest — the balance will never go down. Please increase the minimum payment.');
            return;
        }

        var sc = document.getElementById('summaryCards');
        sc.innerHTML =
            card('Debt-free date', monthLabel(withExtra.months), 'primary') +
            card('Total months', withExtra.months + ' months', 'success') +
            card('Total interest', 'Rs ' + fmt(withExtra.totalInterest), 'warning') +
            card('Interest saved vs min-only', 'Rs ' + fmt(minOnly.totalInterest - withExtra.totalInterest), 'info');

        var ob = document.getElementById('orderBody');
        ob.innerHTML = '';
        for (var j = 0; j < withExtra.order.length; j++) {
            var dd = withExtra.order[j];
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + (j + 1) + '</td><td>' + dd.name.replace(/</g, '&lt;') + '</td><td>Rs ' + fmt(dd.startBalance) + '</td>' +
                '<td>Month ' + dd.payoffMonth + ' (' + monthLabel(dd.payoffMonth) + ')</td><td>Rs ' + fmt(dd.interestPaid) + '</td>';
            ob.appendChild(tr);
        }

        var cb = document.getElementById('compareBody');
        cb.innerHTML =
            '<tr><td><strong>Snowball (min + Rs ' + fmt(extra) + ' extra)</strong></td><td>' + withExtra.months + ' months (' + monthLabel(withExtra.months) + ')</td>' +
            '<td>Rs ' + fmt(withExtra.totalInterest) + '</td><td>Rs ' + fmt(withExtra.totalPaid) + '</td></tr>' +
            '<tr><td>Minimum payments only</td><td>' + minOnly.months + ' months (' + monthLabel(minOnly.months) + ')</td>' +
            '<td>Rs ' + fmt(minOnly.totalInterest) + '</td><td>Rs ' + fmt(minOnly.totalPaid) + '</td></tr>';

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    function card(label, value, color) {
        return '<div class="col-6 col-md-3"><div class="card border-' + color + ' h-100"><div class="card-body p-3">' +
            '<div class="text-muted small">' + label + '</div>' +
            '<div class="fs-5 fw-bold text-' + color + '">' + value + '</div></div></div></div>';
    }

    addDebt('Credit card', 80000, 36, 3000);
    addDebt('Personal loan', 200000, 24, 8000);
})();
</script>
@endsection
