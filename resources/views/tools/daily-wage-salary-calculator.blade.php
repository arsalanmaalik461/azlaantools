@extends('layouts.app')

@section('title', 'Daily-Wage Salary Calculator - Azlaan Tools')
@section('meta_description', 'Daily wage x present days = monthly salary — daily-wage pay calculator with leave deductions and overtime.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Daily-Wage Salary Calculator</h1>
            <p class="lead text-muted">Daily wage &times; present days = monthly salary, with leave deductions and overtime. Data is saved only in your browser, it is not uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Calculate salary</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="dwName" class="form-label fw-semibold">Staff name</label>
                            <input type="text" class="form-control" id="dwName" placeholder="Example: Ahmed">
                        </div>
                        <div class="col-md-6">
                            <label for="dwMonth" class="form-label fw-semibold">Month</label>
                            <input type="month" class="form-control" id="dwMonth">
                        </div>
                        <div class="col-md-4">
                            <label for="dwRate" class="form-label fw-semibold">Daily rate (Rs)</label>
                            <input type="number" class="form-control" id="dwRate" placeholder="1000" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="dwPresent" class="form-label fw-semibold">Full days present</label>
                            <input type="number" class="form-control" id="dwPresent" placeholder="0" min="0" step="1">
                        </div>
                        <div class="col-md-4">
                            <label for="dwHalf" class="form-label fw-semibold">Half days (0.5 each)</label>
                            <input type="number" class="form-control" id="dwHalf" placeholder="0" min="0" step="1">
                        </div>
                        <div class="col-md-4">
                            <label for="dwAbsent" class="form-label fw-semibold">Leave / absent (days)</label>
                            <input type="number" class="form-control" id="dwAbsent" placeholder="0" min="0" step="1">
                            <div class="form-text">Full absent days — no pay for these days.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="dwOtHours" class="form-label fw-semibold">Overtime hours</label>
                            <input type="number" class="form-control" id="dwOtHours" placeholder="0" min="0" step="0.5">
                        </div>
                        <div class="col-md-4">
                            <label for="dwOtRate" class="form-label fw-semibold">Overtime rate (Rs/hr)</label>
                            <input type="number" class="form-control" id="dwOtRate" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="dwOther" class="form-label fw-semibold">Other deductions (Rs)</label>
                            <input type="number" class="form-control" id="dwOther" placeholder="0" min="0" step="0.01">
                            <div class="form-text">Example: advance deduction, fine.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="dwBonus" class="form-label fw-semibold">Bonus (Rs)</label>
                            <input type="number" class="form-control" id="dwBonus" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button type="button" class="btn btn-primary" id="calcBtn">Calculate</button>
                        <button type="button" class="btn btn-outline-success" id="saveBtn">Save Record</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h3 class="h6 text-muted">Summary of calculation</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr><th>Paid days (full + half&times;0.5)</th><td class="text-end" id="rPaidDays">-</td></tr>
                                    <tr><th>Wage pay (paid days &times; daily rate)</th><td class="text-end" id="rWagePay">-</td></tr>
                                    <tr><th>Overtime pay</th><td class="text-end" id="rOtPay">-</td></tr>
                                    <tr><th>Bonus</th><td class="text-end" id="rBonus">-</td></tr>
                                    <tr class="table-light"><th>Gross pay</th><td class="text-end fw-bold" id="rGross">-</td></tr>
                                    <tr><th>Deductions</th><td class="text-end text-danger" id="rDed">-</td></tr>
                                    <tr class="table-success"><th>Net pay (in hand)</th><td class="text-end fw-bold fs-5" id="rNet">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary" id="printBtn">Print Pay Slip</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 mb-0">Saved records</h2>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearBtn">Clear All</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Month</th><th>Name</th><th class="text-end">Rate</th><th class="text-end">Paid Days</th><th class="text-end">Gross</th><th class="text-end">Deduction</th><th class="text-end">Net Pay</th><th></th></tr>
                            </thead>
                            <tbody id="recRows"></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0" id="recEmpty">No records saved yet. Calculate above and then save.</p>
                </div>
            </div>

            <div id="printArea" class="d-none"></div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the staff name, month, daily rate and days present.</li>
                <li>Enter half days separately (each half day adds half a daily wage); absent days are deducted.</li>
                <li>Press <strong>Calculate</strong> — you will see gross, deductions and net pay.</li>
                <li>Press <strong>Save Record</strong> to keep the monthly record, or print the <strong>Pay Slip</strong>.</li>
            </ol>
            <p class="text-muted small">Note: data stays in this browser only. If you clear browser data, or use another device, these records will not show.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_dailywage';
    var dwName = document.getElementById('dwName');
    var dwMonth = document.getElementById('dwMonth');
    var dwRate = document.getElementById('dwRate');
    var dwPresent = document.getElementById('dwPresent');
    var dwHalf = document.getElementById('dwHalf');
    var dwAbsent = document.getElementById('dwAbsent');
    var dwOtHours = document.getElementById('dwOtHours');
    var dwOtRate = document.getElementById('dwOtRate');
    var dwOther = document.getElementById('dwOther');
    var dwBonus = document.getElementById('dwBonus');
    var calcBtn = document.getElementById('calcBtn');
    var saveBtn = document.getElementById('saveBtn');
    var printBtn = document.getElementById('printBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var rPaidDays = document.getElementById('rPaidDays');
    var rWagePay = document.getElementById('rWagePay');
    var rOtPay = document.getElementById('rOtPay');
    var rBonus = document.getElementById('rBonus');
    var rGross = document.getElementById('rGross');
    var rDed = document.getElementById('rDed');
    var rNet = document.getElementById('rNet');
    var recRows = document.getElementById('recRows');
    var recEmpty = document.getElementById('recEmpty');
    var printArea = document.getElementById('printArea');

    var lastCalc = null;

    function num(el) { return Number(el.value) || 0; }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && p.records) return p;
            }
        } catch (e) {}
        return { records: [] };
    }
    function save(data) {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function currentMonth() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        return d.getFullYear() + '-' + m;
    }

    function compute() {
        hideError();
        var rate = num(dwRate);
        if (rate <= 0) { showError('Enter a daily rate above 0.'); return null; }
        var present = num(dwPresent), half = num(dwHalf), absent = num(dwAbsent);
        if (present < 0 || half < 0 || absent < 0) { showError('Days cannot be less than 0.'); return null; }
        var otH = num(dwOtHours), otR = num(dwOtRate);
        var other = num(dwOther), bonus = num(dwBonus);
        var paidDays = present + 0.5 * half;
        var wagePay = Math.round(paidDays * rate * 100) / 100;
        var otPay = Math.round(otH * otR * 100) / 100;
        var gross = Math.round((wagePay + otPay + bonus) * 100) / 100;
        var ded = Math.round(other * 100) / 100;
        var net = Math.round((gross - ded) * 100) / 100;
        return {
            name: dwName.value.trim() || 'No name entered',
            month: dwMonth.value || currentMonth(),
            rate: rate, present: present, half: half, absent: absent,
            otH: otH, otR: otR, other: other, bonus: bonus,
            paidDays: paidDays, wagePay: wagePay, otPay: otPay,
            gross: gross, ded: ded, net: net
        };
    }

    function renderResult(c) {
        rPaidDays.textContent = c.paidDays + ' (full: ' + c.present + ', half: ' + c.half + ', absent: ' + c.absent + ')';
        rWagePay.textContent = fmt(c.wagePay);
        rOtPay.textContent = fmt(c.otPay) + ' (' + c.otH + ' hrs × Rs ' + c.otR + ')';
        rBonus.textContent = fmt(c.bonus);
        rGross.textContent = fmt(c.gross);
        rDed.textContent = fmt(c.ded);
        rNet.textContent = fmt(c.net);
        results.classList.remove('d-none');
    }

    function renderRecords() {
        var data = load();
        recRows.innerHTML = '';
        recEmpty.style.display = data.records.length ? 'none' : '';
        var sorted = data.records.slice().sort(function (a, b) {
            return a.month < b.month ? 1 : -1;
        });
        sorted.forEach(function (r) {
            var tr = document.createElement('tr');
            var cells = [
                ['text-start', r.month],
                ['text-start', r.name],
                ['text-end', fmt(r.rate)],
                ['text-end', r.paidDays],
                ['text-end', fmt(r.gross)],
                ['text-end', fmt(r.ded)],
                ['text-end fw-bold', fmt(r.net)]
            ];
            cells.forEach(function (c) {
                var td = document.createElement('td');
                td.className = c[0];
                td.textContent = c[1];
                tr.appendChild(td);
            });
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.addEventListener('click', function () {
                var d = load();
                d.records = d.records.filter(function (x) { return x.id !== r.id; });
                save(d);
                renderRecords();
            });
            tdX.appendChild(del);
            tr.appendChild(tdX);
            recRows.appendChild(tr);
        });
    }

    calcBtn.addEventListener('click', function () {
        var c = compute();
        if (!c) { results.classList.add('d-none'); return; }
        lastCalc = c;
        renderResult(c);
    });

    saveBtn.addEventListener('click', function () {
        hideError();
        var c = compute();
        if (!c) return;
        c.id = 'r' + Date.now().toString(36);
        var d = load();
        d.records.push(c);
        save(d);
        lastCalc = c;
        renderResult(c);
        renderRecords();
        showErrorSaved();
    });
    function showErrorSaved() {
        // small confirmation using the error box styled as success
        errorBox.textContent = 'Record saved.';
        errorBox.classList.remove('d-none');
        errorBox.classList.remove('alert-danger');
        errorBox.classList.add('alert-success');
        setTimeout(function () {
            errorBox.classList.add('alert-danger');
            errorBox.classList.remove('alert-success');
            hideError();
        }, 2000);
    }

    printBtn.addEventListener('click', function () {
        var c = lastCalc || compute();
        if (!c) return;
        var html = '<div class="container py-4">' +
            '<h3>Pay Slip — ' + esc(c.month) + '</h3>' +
            '<p><strong>Name:</strong> ' + esc(c.name) + '</p>' +
            '<table class="table table-bordered">' +
            '<tr><td>Daily rate</td><td class="text-end">' + fmt(c.rate) + '</td></tr>' +
            '<tr><td>Paid days (full ' + c.present + ', half ' + c.half + ')</td><td class="text-end">' + c.paidDays + '</td></tr>' +
            '<tr><td>Wage pay</td><td class="text-end">' + fmt(c.wagePay) + '</td></tr>' +
            '<tr><td>Overtime pay</td><td class="text-end">' + fmt(c.otPay) + '</td></tr>' +
            '<tr><td>Bonus</td><td class="text-end">' + fmt(c.bonus) + '</td></tr>' +
            '<tr><th>Gross pay</th><th class="text-end">' + fmt(c.gross) + '</th></tr>' +
            '<tr><td>Deductions</td><td class="text-end">' + fmt(c.ded) + '</td></tr>' +
            '<tr><th>Net pay</th><th class="text-end">' + fmt(c.net) + '</th></tr>' +
            '</table><p class="text-muted small">Azlaan Tools — Daily-Wage Salary Calculator</p></div>';
        printArea.innerHTML = html;
        printArea.classList.remove('d-none');
        window.print();
        printArea.classList.add('d-none');
    });

    clearBtn.addEventListener('click', function () {
        if (!confirm('Delete all saved records?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        renderRecords();
    });

    dwMonth.value = currentMonth();
    renderRecords();
})();
</script>
@endsection
