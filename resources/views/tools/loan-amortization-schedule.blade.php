@extends('layouts.app')

@section('title', 'Loan Amortization Schedule - Azlaan Tools')
@section('meta_description', 'How much principal and interest is in each installment — month-wise detailed schedule and remaining balance table.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Loan Amortization Schedule</h1>
            <p class="lead text-muted">How much principal and how much interest is in each monthly installment — see the full schedule in the table.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="asAmount" class="form-label fw-semibold">Loan amount (Rs)</label>
                            <input type="number" class="form-control" id="asAmount" placeholder="e.g. 500000" min="1" step="0.01">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="asRate" class="form-label fw-semibold">Annual rate (% per year)</label>
                            <input type="number" class="form-control" id="asRate" placeholder="e.g. 18" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="asMonths" class="form-label fw-semibold">Tenure (months)</label>
                            <input type="number" class="form-control" id="asMonths" placeholder="e.g. 36" min="1" max="600" step="1">
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="asCalcBtn">Make Schedule</button>
                        <button type="button" class="btn btn-outline-secondary" id="asCsvBtn">Download CSV</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="asError" role="alert"></div>

                    <div id="asSummary" class="mt-4 d-none">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Monthly Installment</small><div class="fw-bold" id="asEmi">Rs 0</div></div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total Interest</small><div class="fw-bold text-danger" id="asInterest">Rs 0</div></div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total Payable</small><div class="fw-bold" id="asTotal">Rs 0</div></div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Last Installment</small><div class="fw-bold" id="asLast">Rs 0</div></div></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-end">Installment (Rs)</th>
                                        <th class="text-end">Principal (Rs)</th>
                                        <th class="text-end">Interest (Rs)</th>
                                        <th class="text-end">Remaining Balance (Rs)</th>
                                    </tr>
                                </thead>
                                <tbody id="asTable"></tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end" id="asTotPay">Rs 0</th>
                                        <th class="text-end" id="asTotPrin">Rs 0</th>
                                        <th class="text-end" id="asTotInt">Rs 0</th>
                                        <th class="text-end">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">This is an estimated schedule made with monthly compounding, not financial advice. The amount may differ slightly due to bank rates, fees or rounding.</p>
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

    function fmt(n) {
        return n.toLocaleString('en-PK', { maximumFractionDigits: 2, minimumFractionDigits: 2 });
    }

    function money(n) { return 'Rs ' + fmt(n); }

    function showError(msg) {
        var box = $('asError');
        if (msg) { box.textContent = msg; box.classList.remove('d-none'); }
        else { box.classList.add('d-none'); }
    }

    var lastRows = [];
    var lastMeta = null;

    function build() {
        showError(null);
        var p = parseFloat($('asAmount').value);
        var rate = parseFloat($('asRate').value);
        var n = parseInt($('asMonths').value, 10);

        if (!isFinite(p) || p <= 0) { showError('Enter a loan amount greater than 0.'); return; }
        if (!isFinite(rate) || rate < 0 || rate > 100) { showError('Enter an annual rate between 0 and 100.'); return; }
        if (!isFinite(n) || n < 1 || n > 600) { showError('Enter a tenure of 1 to 600 months.'); return; }

        var r = rate / 1200;
        var emi = r === 0 ? p / n : (p * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);

        var bal = p;
        var rows = [];
        var totPrin = 0, totInt = 0, totPay = 0;
        for (var m = 1; m <= n; m++) {
            var intPart = bal * r;
            var prinPart = emi - intPart;
            var pay = emi;
            // rounding: final month clears the balance
            if (m === n) {
                prinPart = bal;
                intPart = bal * r;
                pay = prinPart + intPart;
                bal = 0;
            } else {
                bal = bal - prinPart;
                if (bal < 0) bal = 0;
            }
            rows.push({ m: m, pay: pay, prin: prinPart, int: intPart, bal: bal });
            totPrin += prinPart; totInt += intPart; totPay += pay;
        }

        lastRows = rows;
        lastMeta = { amount: p, rate: rate, months: n, emi: emi };

        var html = '';
        rows.forEach(function (row) {
            html += '<tr><td>' + row.m + '</td>' +
                '<td class="text-end">' + fmt(row.pay) + '</td>' +
                '<td class="text-end text-primary">' + fmt(row.prin) + '</td>' +
                '<td class="text-end text-danger">' + fmt(row.int) + '</td>' +
                '<td class="text-end fw-semibold">' + fmt(row.bal) + '</td></tr>';
        });
        $('asTable').innerHTML = html;

        $('asEmi').textContent = money(emi);
        $('asInterest').textContent = money(totInt);
        $('asTotal').textContent = money(totPay);
        $('asLast').textContent = money(rows[rows.length - 1].pay);
        $('asTotPay').textContent = money(totPay);
        $('asTotPrin').textContent = money(totPrin);
        $('asTotInt').textContent = money(totInt);

        $('asSummary').classList.remove('d-none');
    }

    function downloadCsv() {
        if (!lastRows.length || !lastMeta) { showError('First make the schedule, then download the CSV.'); return; }
        showError(null);
        var lines = ['Month,Payment,Principal,Interest,Balance'];
        lastRows.forEach(function (row) {
            lines.push([row.m, row.pay.toFixed(2), row.prin.toFixed(2), row.int.toFixed(2), row.bal.toFixed(2)].join(','));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'loan-amortization-schedule.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    }

    $('asCalcBtn').addEventListener('click', build);
    $('asCsvBtn').addEventListener('click', downloadCsv);

    ['asAmount', 'asRate', 'asMonths'].forEach(function (id) {
        $(id).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); build(); }
        });
    });
})();
</script>
@endsection
