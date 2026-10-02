@extends('layouts.app')

@section('title', 'Interest-Free Loan Planner - Azlaan Tools')
@section('meta_description', 'Interest-free loan payment plan: total amount divided by months = monthly installment, with a full repayment schedule.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Interest-Free Loan Planner</h1>
            <p class="lead text-muted">Payment plan for an interest-free loan — total amount divided by months = monthly installment, with a full schedule table. This is only an estimate tool, not financial advice or a lending service.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="loanAmt" class="form-label fw-semibold">Total loan amount (Rs)</label>
                            <input type="number" class="form-control" id="loanAmt" placeholder="120000" min="1" step="1">
                        </div>
                        <div class="col-md-4">
                            <label for="loanMonths" class="form-label fw-semibold">Repayment months</label>
                            <input type="number" class="form-control" id="loanMonths" placeholder="12" min="1" max="240" step="1">
                        </div>
                        <div class="col-md-4">
                            <label for="loanStart" class="form-label fw-semibold">First installment date</label>
                            <input type="date" class="form-control" id="loanStart">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="calcBtn">Make Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="resultWrap" class="d-none">
                <div class="row text-center g-2 mb-3">
                    <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total loan</small><div class="fw-bold" id="sumAmt">-</div></div></div></div>
                    <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Monthly installment</small><div class="fw-bold text-primary" id="sumInst">-</div></div></div></div>
                    <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Final payment date</small><div class="fw-bold text-success" id="sumEnd">-</div></div></div></div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="h5 mb-0">Repayment schedule</h2>
                            <button type="button" class="btn btn-outline-success btn-sm" id="csvBtn">CSV Download</button>
                        </div>
                        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light sticky-top">
                                    <tr><th>Installment #</th><th>Date</th><th class="text-end">Installment (Rs)</th><th class="text-end">Paid so far</th><th class="text-end">Balance</th></tr>
                                </thead>
                                <tbody id="schedBody"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mb-0 mt-3">
                            Interest-free calculation: no markup, no extra charges — <strong>total amount divided by months</strong>. Always plan to pay on time.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the total loan amount, repayment months, and first installment date.</li>
                <li>Press <strong>Make Plan</strong> — the monthly installment and each date, with paid and balance amounts, will show in the schedule.</li>
                <li>Use <strong>CSV Download</strong> to keep a record if needed.</li>
            </ol>
            <p class="text-muted small">Disclaimer: this is only a math estimate — not financial advice or a lending service. Always keep loan terms in writing.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var loanAmt = document.getElementById('loanAmt');
    var loanMonths = document.getElementById('loanMonths');
    var loanStart = document.getElementById('loanStart');
    var calcBtn = document.getElementById('calcBtn');
    var errorBox = document.getElementById('errorBox');
    var resultWrap = document.getElementById('resultWrap');
    var sumAmt = document.getElementById('sumAmt');
    var sumInst = document.getElementById('sumInst');
    var sumEnd = document.getElementById('sumEnd');
    var schedBody = document.getElementById('schedBody');
    var csvBtn = document.getElementById('csvBtn');

    var rows = [];

    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function fmt(n) {
        return 'Rs ' + Number(n).toLocaleString('en-PK', { maximumFractionDigits: 2, minimumFractionDigits: 2 });
    }
    function today() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function fmtDate(iso) {
        var parts = iso.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    function addMonths(iso, months) {
        var parts = iso.split('-').map(Number);
        var d = new Date(parts[0], parts[1] - 1 + months, parts[2]);
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function round2(n) { return Math.round(n * 100) / 100; }

    function calculate() {
        hideError();
        var amt = Number(loanAmt.value);
        var n = Math.floor(Number(loanMonths.value));
        var start = loanStart.value;
        if (!amt || amt <= 0) { showError('Enter a loan amount greater than 0.'); return; }
        if (!n || n < 1 || n > 240) { showError('Enter months between 1 and 240.'); return; }
        if (!start) { showError('Select the first installment date.'); return; }

        var inst = round2(amt / n);
        rows = [];
        var paid = 0;
        for (var k = 1; k <= n; k++) {
            var qist = (k === n) ? round2(amt - paid) : inst; // last installment adjusts rounding
            paid = round2(paid + qist);
            rows.push({ no: k, date: addMonths(start, k - 1), inst: qist, paid: paid, bal: round2(amt - paid) });
        }

        sumAmt.textContent = fmt(amt);
        sumInst.textContent = fmt(inst);
        sumEnd.textContent = addMonths(start, n - 1).split('-').reverse().join('/');

        schedBody.innerHTML = '';
        rows.forEach(function (r) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + r.no + '</td>' +
                '<td>' + fmtDate(r.date) + '</td>' +
                '<td class="text-end fw-semibold">' + fmt(r.inst) + '</td>' +
                '<td class="text-end text-success">' + fmt(r.paid) + '</td>' +
                '<td class="text-end text-muted">' + fmt(r.bal) + '</td>';
            schedBody.appendChild(tr);
        });
        resultWrap.classList.remove('d-none');
    }

    csvBtn.addEventListener('click', function () {
        hideError();
        if (!rows.length) { showError('Make a plan first.'); return; }
        var lines = ['Installment #,Date,Installment (Rs),Paid so far (Rs),Balance (Rs)'];
        rows.forEach(function (r) {
            lines.push([r.no, r.date, r.inst.toFixed(2), r.paid.toFixed(2), r.bal.toFixed(2)].join(','));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'interest-free-loan-plan.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    calcBtn.addEventListener('click', calculate);
    loanStart.value = today();
})();
</script>
@endsection
