@extends('layouts.app')
@section('title', 'Fee Installment Calculator - Free Online | Azlaan Tools')
@section('meta_description', 'Split your semester fee into easy installments online for free - with each installment amount and due dates.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Fee Installment Calculator</h1>
            <p class="lead text-muted">Split your semester fee into installments — a full plan with every installment amount and due date. Free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="totalFeeInput" class="form-label fw-semibold">Total fee (Rs)</label>
                            <input type="number" class="form-control" id="totalFeeInput" placeholder="e.g. 120000" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="installmentsInput" class="form-label fw-semibold">Number of installments</label>
                            <input type="number" class="form-control" id="installmentsInput" placeholder="e.g. 6" min="2" max="24" step="1">
                            <div class="form-text">From 2 to 24 installments.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="downPaymentInput" class="form-label fw-semibold">Down payment (Rs, optional)</label>
                            <input type="number" class="form-control" id="downPaymentInput" placeholder="e.g. 20000" min="0" step="any" value="0">
                        </div>
                        <div class="col-md-6">
                            <label for="startDateInput" class="form-label fw-semibold">First installment due date</label>
                            <input type="date" class="form-control" id="startDateInput">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 btn-lg" id="goBtn">Calculate Installment Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-3 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 text-center h-100">
                                    <div class="text-muted small">Total fee</div>
                                    <div class="h5 mb-0" id="sumTotal"></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 text-center h-100">
                                    <div class="text-muted small">Down payment</div>
                                    <div class="h5 mb-0" id="sumDown"></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 text-center h-100">
                                    <div class="text-muted small">Per installment</div>
                                    <div class="h5 mb-0 text-primary" id="sumPer"></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 text-center h-100">
                                    <div class="text-muted small">Last due date</div>
                                    <div class="h5 mb-0" id="sumLast"></div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Installment #</th>
                                        <th>Due date</th>
                                        <th class="text-end">Amount (Rs)</th>
                                        <th class="text-end">Remaining (Rs)</th>
                                    </tr>
                                </thead>
                                <tbody id="planBody"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" id="printBtn">🖨 Print Plan</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Note:</strong> This is an estimate plan — be sure to confirm the actual schedule, late fees and terms with your institute.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the total semester fee and how many installments you want.</li>
                <li>Add a down payment if you plan to pay one upfront, and pick the first due date.</li>
                <li>Click <strong>Calculate Installment Plan</strong> to see every installment with its due date.</li>
                <li>Print the plan to keep as a reminder.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var totalFeeInput = document.getElementById('totalFeeInput');
    var installmentsInput = document.getElementById('installmentsInput');
    var downPaymentInput = document.getElementById('downPaymentInput');
    var startDateInput = document.getElementById('startDateInput');
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var planBody = document.getElementById('planBody');

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
        return 'Rs ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    function fmtDate(d) {
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }
    function addMonths(date, m) {
        var d = new Date(date.getFullYear(), date.getMonth() + m, 1);
        var lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
        d.setDate(Math.min(date.getDate(), lastDay));
        return d;
    }

    var lastPlan = [];

    goBtn.addEventListener('click', function () {
        hideError();
        var total = parseFloat(totalFeeInput.value);
        var n = parseInt(installmentsInput.value, 10);
        var down = parseFloat(downPaymentInput.value) || 0;
        var startIso = startDateInput.value;

        if (isNaN(total) || total <= 0) { showError('Please enter a valid total fee.'); return; }
        if (isNaN(n) || n < 2 || n > 24) { showError('Installments must be between 2 and 24.'); return; }
        if (down < 0) { showError('Down payment must be zero or more.'); return; }
        if (down >= total) { showError('Down payment must be less than the total fee.'); return; }
        if (!startIso) { showError('Please select the first installment due date.'); return; }

        var remaining = total - down;
        // distribute to whole rupees: floor for all, last installment takes the leftover
        var base = Math.floor(remaining / n);
        var plan = [];
        var firstDate = new Date(startIso + 'T00:00:00');
        var acc = 0;
        for (var i = 0; i < n; i++) {
            var amt = (i === n - 1) ? (remaining - acc) : base;
            acc += amt;
            plan.push({ no: i + 1, date: addMonths(firstDate, i), amount: amt, remaining: remaining - acc });
        }

        planBody.innerHTML = '';
        plan.forEach(function (p) {
            var tr = document.createElement('tr');
            var tdNo = document.createElement('td'); tdNo.textContent = p.no;
            var tdDate = document.createElement('td'); tdDate.textContent = fmtDate(p.date);
            var tdAmt = document.createElement('td'); tdAmt.className = 'text-end fw-semibold'; tdAmt.textContent = fmtRs(p.amount);
            var tdRem = document.createElement('td'); tdRem.className = 'text-end text-muted'; tdRem.textContent = fmtRs(p.remaining);
            tr.appendChild(tdNo); tr.appendChild(tdDate); tr.appendChild(tdAmt); tr.appendChild(tdRem);
            planBody.appendChild(tr);
        });

        document.getElementById('sumTotal').textContent = fmtRs(total);
        document.getElementById('sumDown').textContent = fmtRs(down);
        document.getElementById('sumPer').textContent = fmtRs(plan[0].amount) + (plan[n - 1].amount !== plan[0].amount ? ' *' : '');
        document.getElementById('sumLast').textContent = fmtDate(plan[n - 1].date);
        if (plan[n - 1].amount !== plan[0].amount) {
            document.getElementById('sumPer').title = 'Last installment: ' + fmtRs(plan[n - 1].amount) + ' (rounding adjusted)';
        }

        lastPlan = plan;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    printBtn.addEventListener('click', function () {
        if (lastPlan.length === 0) { return; }
        var w = window.open('', '_blank', 'width=800,height=900');
        if (!w) { showError('Popup blocked — please allow popups to print.'); return; }
        var rows = '';
        for (var i = 0; i < lastPlan.length; i++) {
            var p = lastPlan[i];
            rows += '<tr><td>' + p.no + '</td><td>' + fmtDate(p.date) + '</td><td style="text-align:right">' + fmtRs(p.amount) + '</td></tr>';
        }
        w.document.write('<html><head><title>Fee Installment Plan</title>');
        w.document.write('<style>body{font-family:Arial,sans-serif;max-width:640px;margin:40px auto;padding:0 20px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:8px}th{background:#f0f0f0}</style>');
        w.document.write('</head><body><h2>Fee Installment Plan</h2>');
        w.document.write('<table><tr><th>Installment #</th><th>Due date</th><th style="text-align:right">Amount</th></tr>' + rows + '</table>');
        w.document.write('<p style="color:#666;font-size:13px">Estimate only — confirm the actual schedule with your institute.</p></body></html>');
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); }, 500);
    });
})();
</script>
@endsection
