@extends('layouts.app')

@section('title', 'Party Ledger Statement Generator - Azlaan Tools')
@section('meta_description', 'Free party ledger statement generator. Create a monthly statement for any customer — with bills, payments and balance.')

@section('content')
<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff; }
    .stmt-card { box-shadow: none !important; border: 1px solid #000; }
}
.stmt-card { border: 1px solid #dee2e6; }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3 no-print">Party Ledger Statement Generator</h1>
            <p class="lead text-muted no-print">A monthly statement for any customer — bills, payments, balance — with a clean print layout. Your data is saved only in your browser.</p>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <h2 class="h5 mb-3">Statement details</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="stBiz" class="form-label fw-semibold">Business name (header)</label>
                            <input type="text" class="form-control" id="stBiz" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-4">
                            <label for="stParty" class="form-label fw-semibold">Party / customer name</label>
                            <input type="text" class="form-control" id="stParty" placeholder="Customer name">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="stFrom" class="form-label fw-semibold">From date</label>
                            <input type="date" class="form-control" id="stFrom">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="stTo" class="form-label fw-semibold">To date</label>
                            <input type="date" class="form-control" id="stTo">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="stOpening" class="form-label fw-semibold">Opening balance (Rs) — previous balance</label>
                            <input type="number" class="form-control" id="stOpening" placeholder="0" step="0.01">
                        </div>
                        <div class="col-6 col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary" id="stNewBtn">Start new statement</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <h2 class="h5 mb-3">Bill or payment entry</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-2">
                            <label for="stDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="stDate">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="stType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="stType">
                                <option value="bill">Bill (credit given)</option>
                                <option value="payment">Payment (received)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="stDesc" class="form-label fw-semibold">Description</label>
                            <input type="text" class="form-control" id="stDesc" placeholder="e.g. Bill #123, 5kg wire">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="stAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="stAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="stAddBtn">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="card stmt-card mb-4" id="stmtCard">
                <div class="card-body">
                    <div class="text-center border-bottom pb-3 mb-3">
                        <h2 class="h4 mb-1" id="pvBiz">Business Name</h2>
                        <div class="text-muted">Account Statement</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Party:</strong> <span id="pvParty">-</span><br>
                            <strong>Period:</strong> <span id="pvPeriod">-</span>
                        </div>
                        <div class="col-6 text-end">
                            <strong>Generated:</strong> <span id="pvGen">-</span><br>
                            <strong>Opening balance:</strong> <span id="pvOpening">Rs 0</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr><th>#</th><th>Date</th><th>Description</th><th class="text-end">Bill (Rs)</th><th class="text-end">Payment (Rs)</th><th class="text-end">Balance (Rs)</th><th class="no-print"></th></tr>
                            </thead>
                            <tbody id="pvRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-3" id="pvEmpty">No entries yet.</p>
                    <div class="row">
                        <div class="col-6 col-md-4"><div class="small text-muted">Total Bills</div><div class="fw-bold" id="pvTotBill">Rs 0</div></div>
                        <div class="col-6 col-md-4"><div class="small text-muted">Total Payments</div><div class="fw-bold text-success" id="pvTotPay">Rs 0</div></div>
                        <div class="col-12 col-md-4 mt-2 mt-md-0"><div class="small text-muted">Closing balance (due)</div><div class="fw-bold fs-5" id="pvTotBal">Rs 0</div></div>
                    </div>
                    <div class="text-center mt-4 pt-3 border-top small text-muted">
                        This statement was prepared by computer — no signature needed.
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap mb-4 no-print">
                <button type="button" class="btn btn-primary" id="stPrintBtn">Print / PDF Statement</button>
                <button type="button" class="btn btn-outline-success" id="stCsvBtn">CSV Download</button>
            </div>

            <div class="no-print">
                <h2>How to use</h2>
                <ol>
                    <li>Write the <strong>business</strong> and party name, date range and previous <strong>opening balance</strong>.</li>
                    <li>Add every <strong>bill</strong> (credit) or <strong>payment</strong> entry — the running balance will calculate itself.</li>
                    <li>Press <strong>Print / PDF</strong> to print a clean statement or save it as PDF.</li>
                </ol>
                <p class="text-muted small">Note: Your data is saved only in your browser — nothing is uploaded. If you clear your browser data, this record will be deleted.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_statements';
    var stBiz = document.getElementById('stBiz');
    var stParty = document.getElementById('stParty');
    var stFrom = document.getElementById('stFrom');
    var stTo = document.getElementById('stTo');
    var stOpening = document.getElementById('stOpening');
    var stNewBtn = document.getElementById('stNewBtn');
    var stDate = document.getElementById('stDate');
    var stType = document.getElementById('stType');
    var stDesc = document.getElementById('stDesc');
    var stAmt = document.getElementById('stAmt');
    var stAddBtn = document.getElementById('stAddBtn');
    var errorBox = document.getElementById('errorBox');
    var pvBiz = document.getElementById('pvBiz');
    var pvParty = document.getElementById('pvParty');
    var pvPeriod = document.getElementById('pvPeriod');
    var pvGen = document.getElementById('pvGen');
    var pvOpening = document.getElementById('pvOpening');
    var pvRows = document.getElementById('pvRows');
    var pvEmpty = document.getElementById('pvEmpty');
    var pvTotBill = document.getElementById('pvTotBill');
    var pvTotPay = document.getElementById('pvTotPay');
    var pvTotBal = document.getElementById('pvTotBal');

    var data = { biz: '', party: '', from: '', to: '', opening: 0, rows: [] };
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.rows) data = parsed;
        }
    } catch (e) { data = { biz: '', party: '', from: '', to: '', opening: 0, rows: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid() {
        return 's' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function todayYmd() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function syncForm() {
        data.biz = stBiz.value;
        data.party = stParty.value;
        data.from = stFrom.value;
        data.to = stTo.value;
        data.opening = Number(stOpening.value) || 0;
        save();
    }
    function inRange(r) {
        if (data.from && r.date < data.from) return false;
        if (data.to && r.date > data.to) return false;
        return true;
    }

    function render() {
        pvBiz.textContent = data.biz.trim() || 'Business Name';
        pvParty.textContent = data.party.trim() || '-';
        pvPeriod.textContent = (data.from || '...') + ' to ' + (data.to || '...');
        pvGen.textContent = todayYmd();
        var opening = Number(data.opening) || 0;
        pvOpening.textContent = fmt(opening);

        var sorted = data.rows.filter(inRange).sort(function (a, b) {
            if (a.date === b.date) return a.seq - b.seq;
            return a.date < b.date ? -1 : 1;
        });
        pvRows.innerHTML = '';
        var bal = opening, totB = 0, totP = 0;
        sorted.forEach(function (r, i) {
            if (r.type === 'bill') { bal += r.amount; totB += r.amount; }
            else { bal -= r.amount; totP += r.amount; }
            var tr = document.createElement('tr');
            var tdI = document.createElement('td'); tdI.textContent = i + 1;
            var tdD = document.createElement('td'); tdD.textContent = r.date;
            var tdDesc = document.createElement('td'); tdDesc.textContent = r.desc || (r.type === 'bill' ? 'Bill' : 'Payment');
            var tdB = document.createElement('td'); tdB.className = 'text-end';
            tdB.textContent = r.type === 'bill' ? fmt(r.amount) : '-';
            var tdP = document.createElement('td'); tdP.className = 'text-end text-success';
            tdP.textContent = r.type === 'payment' ? fmt(r.amount) : '-';
            var tdBal = document.createElement('td'); tdBal.className = 'text-end fw-bold';
            tdBal.textContent = fmt(bal);
            var tdX = document.createElement('td'); tdX.className = 'no-print text-end';
            var x = document.createElement('button');
            x.type = 'button'; x.className = 'btn btn-sm btn-outline-danger'; x.textContent = '×';
            x.setAttribute('aria-label', 'Delete entry');
            (function (id) {
                x.addEventListener('click', function () {
                    data.rows = data.rows.filter(function (rr) { return rr.id !== id; });
                    save(); render();
                });
            })(r.id);
            tdX.appendChild(x);
            tr.appendChild(tdI); tr.appendChild(tdD); tr.appendChild(tdDesc);
            tr.appendChild(tdB); tr.appendChild(tdP); tr.appendChild(tdBal); tr.appendChild(tdX);
            pvRows.appendChild(tr);
        });
        pvEmpty.style.display = sorted.length ? 'none' : '';
        pvTotBill.textContent = fmt(totB);
        pvTotPay.textContent = fmt(totP);
        pvTotBal.textContent = fmt(bal);
        pvTotBal.className = 'fw-bold fs-5 ' + (bal > 0 ? 'text-danger' : 'text-success');
    }

    stBiz.value = data.biz; stParty.value = data.party;
    stFrom.value = data.from; stTo.value = data.to;
    if (data.opening) stOpening.value = data.opening;
    stDate.value = todayYmd();

    [stBiz, stParty, stFrom, stTo, stOpening].forEach(function (el) {
        el.addEventListener('change', syncForm);
    });

    stAddBtn.addEventListener('click', function () {
        hideError();
        syncForm();
        if (!data.party.trim()) { showError('Please enter the party / customer name.'); return; }
        if (!stDate.value) { showError('Please select a date.'); return; }
        var amt = Number(stAmt.value);
        if (!amt || amt <= 0) { showError('Please enter a correct amount (more than 0).'); return; }
        data.rows.push({
            id: uid(),
            date: stDate.value,
            type: stType.value,
            desc: stDesc.value.trim(),
            amount: Math.round(amt * 100) / 100,
            seq: data.rows.length + 1
        });
        save();
        stDesc.value = ''; stAmt.value = '';
        render();
    });

    stNewBtn.addEventListener('click', function () {
        if (!confirm('Start a new statement? Current entries will be deleted.')) return;
        data = { biz: stBiz.value, party: '', from: '', to: '', opening: 0, rows: [] };
        stParty.value = ''; stFrom.value = ''; stTo.value = ''; stOpening.value = '';
        save(); render();
    });

    document.getElementById('stPrintBtn').addEventListener('click', function () {
        hideError();
        syncForm();
        if (!data.rows.length) { showError('Please add an entry before printing.'); return; }
        render();
        window.print();
    });

    document.getElementById('stCsvBtn').addEventListener('click', function () {
        hideError();
        syncForm();
        var sorted = data.rows.filter(inRange).sort(function (a, b) {
            if (a.date === b.date) return a.seq - b.seq;
            return a.date < b.date ? -1 : 1;
        });
        if (!sorted.length) { showError('Please add an entry before downloading CSV.'); return; }
        var lines = ['Party,' + '"' + data.party.replace(/"/g, '""') + '"', 'Date,Type,Description,Bill,Payment'];
        sorted.forEach(function (r) {
            var d = '"' + String(r.desc || '').replace(/"/g, '""') + '"';
            lines.push(r.date + ',' + r.type + ',' + d + ',' + (r.type === 'bill' ? r.amount : 0) + ',' + (r.type === 'payment' ? r.amount : 0));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'statement-' + data.party.replace(/[^\w\- ]+/g, '').trim().replace(/\s+/g, '-') + '.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    render();
})();
</script>
@endsection
