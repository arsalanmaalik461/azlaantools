@extends('layouts.app')

@section('title', 'Installment Payment Planner - Azlaan Tools')
@section('meta_description', 'Plan a big amount in installments: date, amount and remaining balance for every installment — with mark-paid tracking.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Installment Payment Planner</h1>
            <p class="lead text-muted">Plan a big amount in installments — make a schedule of each installment's date, amount and remaining balance, and tick them off as you pay. Data is saved only in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="totAmt" class="form-label fw-semibold">Total amount (Rs)</label>
                            <input type="number" class="form-control" id="totAmt" placeholder="500000" min="1" step="1">
                        </div>
                        <div class="col-md-6">
                            <label for="downPay" class="form-label fw-semibold">Down payment (Rs)</label>
                            <input type="number" class="form-control" id="downPay" placeholder="50000" min="0" step="1" value="0">
                        </div>
                        <div class="col-md-6">
                            <label for="numInst" class="form-label fw-semibold">Number of installments</label>
                            <input type="number" class="form-control" id="numInst" placeholder="10" min="1" max="120" step="1">
                        </div>
                        <div class="col-md-6">
                            <label for="startDate" class="form-label fw-semibold">First installment date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="genBtn">Make Schedule</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="planWrap" class="d-none">
                <div class="row text-center g-2 mb-3">
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Financed (total − down)</small><div class="fw-bold" id="sumFin">-</div></div></div></div>
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Each installment</small><div class="fw-bold text-primary" id="sumInst">-</div></div></div></div>
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Paid</small><div class="fw-bold text-success" id="sumPaid">-</div></div></div></div>
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Remaining</small><div class="fw-bold" id="sumBal">-</div></div></div></div>
                </div>
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <button type="button" class="btn btn-outline-success btn-sm" id="csvBtn">Download Schedule CSV</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="resetBtn">Reset plan</button>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr><th>Paid</th><th>Installment #</th><th>Date</th><th class="text-end">Installment (Rs)</th><th class="text-end">Remaining</th></tr>
                                </thead>
                                <tbody id="instBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the total amount, down payment, number of installments and first installment date, then press <strong>Make Schedule</strong>.</li>
                <li>When you pay an installment, tick its checkbox — Paid and Remaining update automatically and your progress is saved.</li>
                <li>To start a new plan, press <strong>Reset plan</strong>.</li>
            </ol>
            <p class="text-muted small">Note: this is only a schedule/estimate tool — not financial advice or a lending service. Data is saved only in your browser, never uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_installments';
    var totAmt = document.getElementById('totAmt');
    var downPay = document.getElementById('downPay');
    var numInst = document.getElementById('numInst');
    var startDate = document.getElementById('startDate');
    var genBtn = document.getElementById('genBtn');
    var errorBox = document.getElementById('errorBox');
    var planWrap = document.getElementById('planWrap');
    var instBody = document.getElementById('instBody');
    var sumFin = document.getElementById('sumFin');
    var sumInst = document.getElementById('sumInst');
    var sumPaid = document.getElementById('sumPaid');
    var sumBal = document.getElementById('sumBal');
    var csvBtn = document.getElementById('csvBtn');
    var resetBtn = document.getElementById('resetBtn');

    var plan = null; // {total, down, n, start, rows:[{no,date,inst,bal}]}
    var paid = {};   // no -> true

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

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify({ plan: plan, paid: paid })); } catch (e) {}
    }
    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (!raw) return;
            var s = JSON.parse(raw);
            if (s && s.plan && s.plan.rows) {
                plan = s.plan;
                paid = s.paid || {};
            }
        } catch (e) {}
    }

    function buildPlan() {
        hideError();
        var total = Number(totAmt.value);
        var down = Number(downPay.value) || 0;
        var n = Math.floor(Number(numInst.value));
        var start = startDate.value;
        if (!total || total <= 0) { showError('Enter a total amount greater than 0.'); return; }
        if (down < 0) { showError('Down payment cannot be negative.'); return; }
        if (down >= total) { showError('Down payment must be less than the total amount.'); return; }
        if (!n || n < 1 || n > 120) { showError('Enter a number of installments between 1 and 120.'); return; }
        if (!start) { showError('Select the first installment date.'); return; }

        var financed = round2(total - down);
        var inst = round2(financed / n);
        var rows = [];
        var cum = 0;
        for (var k = 1; k <= n; k++) {
            var qist = (k === n) ? round2(financed - cum) : inst; // last qist adjusts rounding
            cum = round2(cum + qist);
            rows.push({ no: k, date: addMonths(start, k - 1), inst: qist, bal: round2(financed - cum) });
        }
        plan = { total: total, down: down, n: n, start: start, financed: financed, inst: inst, rows: rows };
        paid = {};
        save();
        render();
    }

    function render() {
        if (!plan) { planWrap.classList.add('d-none'); return; }
        planWrap.classList.remove('d-none');
        instBody.innerHTML = '';
        var paidSum = 0, count = 0;
        plan.rows.forEach(function (r) {
            var tr = document.createElement('tr');
            if (paid[r.no]) tr.className = 'table-success';
            var tdC = document.createElement('td');
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input';
            cb.checked = !!paid[r.no];
            cb.setAttribute('aria-label', 'Mark installment ' + r.no + ' as paid');
            cb.addEventListener('change', function () {
                if (cb.checked) paid[r.no] = true; else delete paid[r.no];
                save();
                render();
            });
            tdC.appendChild(cb);
            tr.appendChild(tdC);
            var tdN = document.createElement('td'); tdN.textContent = r.no;
            var tdD = document.createElement('td'); tdD.textContent = fmtDate(r.date);
            var tdI = document.createElement('td'); tdI.className = 'text-end fw-semibold'; tdI.textContent = fmt(r.inst);
            var tdB = document.createElement('td'); tdB.className = 'text-end text-muted'; tdB.textContent = fmt(r.bal);
            tr.appendChild(tdN); tr.appendChild(tdD); tr.appendChild(tdI); tr.appendChild(tdB);
            instBody.appendChild(tr);
            if (paid[r.no]) { paidSum = round2(paidSum + r.inst); count++; }
        });
        sumFin.textContent = fmt(plan.financed);
        sumInst.textContent = fmt(plan.inst);
        sumPaid.textContent = fmt(paidSum) + ' (' + count + '/' + plan.n + ')';
        var balEl = sumBal;
        var bal = round2(plan.financed - paidSum);
        balEl.textContent = fmt(bal);
        balEl.classList.remove('text-success');
        if (bal <= 0) balEl.classList.add('text-success');
    }

    csvBtn.addEventListener('click', function () {
        hideError();
        if (!plan) { showError('Make a schedule first.'); return; }
        var lines = ['Installment #,Date,Installment (Rs),Remaining (Rs),Paid'];
        plan.rows.forEach(function (r) {
            lines.push([r.no, r.date, r.inst.toFixed(2), r.bal.toFixed(2), paid[r.no] ? 'yes' : 'no'].join(','));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'installment-plan.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    resetBtn.addEventListener('click', function () {
        if (!confirm('Reset the plan and mark-paid progress?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        plan = null; paid = {};
        render();
    });

    genBtn.addEventListener('click', buildPlan);
    startDate.value = today();
    load();
    render();
})();
</script>
@endsection
