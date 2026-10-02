@extends('layouts.app')

@section('title', 'Staff Advance & Loan Ledger - Azlaan Tools')
@section('meta_description', 'Ledger of advances given to staff — advance entries, payday deductions and running balance per staff member.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Staff Advance &amp; Loan Ledger</h1>
            <p class="lead text-muted">Track advances given to staff — deductions on payday and the running balance of each employee. Data is saved only in your browser, nothing is uploaded.</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Staff</h5>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="staffName" placeholder="Staff name">
                                <button type="button" class="btn btn-primary" id="addStaffBtn">Add</button>
                            </div>
                            <div id="staffList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">Click a staff member to open their advance ledger.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div id="noStaff" class="text-muted">Add a staff member first, then their ledger will appear here.</div>
                            <div id="ledgerPane" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0" id="ledgerName">-</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="delStaffBtn">Delete Staff</button>
                                </div>
                                <div class="row text-center mb-3 g-2">
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Total Advance (given)</div>
                                            <div class="fw-bold text-danger" id="totAdv">Rs 0</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Total Deduction (taken)</div>
                                            <div class="fw-bold text-success" id="totDed">Rs 0</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Running Balance</div>
                                            <div class="fw-bold" id="totBal">Rs 0</div>
                                        </div>
                                    </div>
                                </div>
                                <h6>New entry</h6>
                                <div class="row g-2 mb-3">
                                    <div class="col-6 col-sm-3">
                                        <select class="form-select" id="entryType">
                                            <option value="advance">Advance (given)</option>
                                            <option value="deduction">Deduction (payday)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="number" class="form-control" id="entryAmt" placeholder="Amount" min="1" step="0.01">
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="date" class="form-control" id="entryDate">
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <button type="button" class="btn btn-primary w-100" id="addEntryBtn">Add</button>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="entryNote" placeholder="Note (example: Eid advance, deduction from June salary)">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead class="table-light">
                                            <tr><th>Date</th><th>Note</th><th class="text-end">Advance</th><th class="text-end">Deduction</th><th></th></tr>
                                        </thead>
                                        <tbody id="entryRows"></tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="csvBtn">Download CSV</button>
                            </div>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the staff name and click <strong>Add</strong>.</li>
                <li>When you give an advance, add an <strong>Advance (given)</strong> entry; on payday, add a <strong>Deduction</strong> entry for the deduction.</li>
                <li><strong>Running Balance</strong> shows how much advance is still left with the staff member.</li>
                <li>Download a <strong>CSV</strong> backup when needed.</li>
            </ol>
            <p class="text-muted small">Note: data stays safe in this browser. If you clear browser data or use another device, this record will not appear — keep a CSV backup of important records.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_staff_advances';
    var staffName = document.getElementById('staffName');
    var addStaffBtn = document.getElementById('addStaffBtn');
    var staffList = document.getElementById('staffList');
    var noStaff = document.getElementById('noStaff');
    var ledgerPane = document.getElementById('ledgerPane');
    var ledgerName = document.getElementById('ledgerName');
    var delStaffBtn = document.getElementById('delStaffBtn');
    var totAdv = document.getElementById('totAdv');
    var totDed = document.getElementById('totDed');
    var totBal = document.getElementById('totBal');
    var entryType = document.getElementById('entryType');
    var entryAmt = document.getElementById('entryAmt');
    var entryDate = document.getElementById('entryDate');
    var entryNote = document.getElementById('entryNote');
    var addEntryBtn = document.getElementById('addEntryBtn');
    var entryRows = document.getElementById('entryRows');
    var csvBtn = document.getElementById('csvBtn');
    var errorBox = document.getElementById('errorBox');

    var data = { staff: [] };
    var activeId = null;

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.staff) data = parsed;
        }
    } catch (e) { data = { staff: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid() {
        return 's' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK');
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
    function getStaff(id) {
        for (var i = 0; i < data.staff.length; i++) {
            if (data.staff[i].id === id) return data.staff[i];
        }
        return null;
    }
    function balance(s) {
        var b = 0;
        for (var i = 0; i < s.entries.length; i++) {
            b += s.entries[i].type === 'advance' ? s.entries[i].amount : -s.entries[i].amount;
        }
        return b;
    }
    function todayStr() {
        var d = new Date();
        var m = d.getMonth() + 1;
        var day = d.getDate();
        return d.getFullYear() + '-' + (m < 10 ? '0' + m : m) + '-' + (day < 10 ? '0' + day : day);
    }

    function renderList() {
        staffList.innerHTML = '';
        if (!data.staff.length) {
            staffList.innerHTML = '<div class="text-muted small">No staff yet. Type a name above and add.</div>';
            return;
        }
        data.staff.forEach(function (s) {
            var b = balance(s);
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (s.id === activeId ? ' active' : '');
            var nm = document.createElement('span');
            nm.textContent = s.name;
            var badge = document.createElement('span');
            badge.className = 'badge ' + (b > 0 ? 'bg-warning text-dark' : 'bg-success') + ' rounded-pill';
            badge.textContent = fmt(b);
            a.appendChild(nm);
            a.appendChild(badge);
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                activeId = s.id;
                renderList();
                renderLedger();
            });
            staffList.appendChild(a);
        });
    }

    function renderLedger() {
        hideError();
        var s = getStaff(activeId);
        if (!s) {
            noStaff.classList.remove('d-none');
            ledgerPane.classList.add('d-none');
            return;
        }
        noStaff.classList.add('d-none');
        ledgerPane.classList.remove('d-none');
        ledgerName.textContent = s.name;
        var adv = 0, ded = 0;
        entryRows.innerHTML = '';
        var sorted = s.entries.slice().sort(function (x, y) {
            if (x.date === y.date) return y.seq - x.seq;
            return x.date < y.date ? 1 : -1;
        });
        sorted.forEach(function (e) {
            if (e.type === 'advance') adv += e.amount; else ded += e.amount;
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.textContent = e.date;
            var tdN = document.createElement('td'); tdN.innerHTML = esc(e.note || (e.type === 'advance' ? 'Advance' : 'Deduction'));
            var tdA = document.createElement('td');
            tdA.className = 'text-end text-danger';
            tdA.textContent = e.type === 'advance' ? fmt(e.amount) : '-';
            var tdK = document.createElement('td');
            tdK.className = 'text-end text-success';
            tdK.textContent = e.type === 'deduction' ? fmt(e.amount) : '-';
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete entry');
            del.addEventListener('click', function () {
                s.entries = s.entries.filter(function (en) { return en.id !== e.id; });
                save();
                renderList();
                renderLedger();
            });
            tdX.appendChild(del);
            tr.appendChild(tdD); tr.appendChild(tdN); tr.appendChild(tdA); tr.appendChild(tdK); tr.appendChild(tdX);
            entryRows.appendChild(tr);
        });
        totAdv.textContent = fmt(adv);
        totDed.textContent = fmt(ded);
        var b = adv - ded;
        totBal.textContent = fmt(b);
        totBal.className = 'fw-bold ' + (b > 0 ? 'text-warning' : 'text-success');
    }

    addStaffBtn.addEventListener('click', function () {
        hideError();
        var name = staffName.value.trim();
        if (!name) { showError('Please type the staff name.'); return; }
        data.staff.push({ id: uid(), name: name, entries: [], seq: 0 });
        save();
        staffName.value = '';
        activeId = data.staff[data.staff.length - 1].id;
        renderList();
        renderLedger();
    });

    addEntryBtn.addEventListener('click', function () {
        hideError();
        var s = getStaff(activeId);
        if (!s) { showError('Please select a staff member first.'); return; }
        var amt = parseFloat(entryAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Please enter a correct amount (more than 0).'); return; }
        var b = balance(s);
        if (entryType.value === 'deduction' && amt > b) {
            showError('Deduction cannot be more than the balance (' + fmt(b) + ').');
            return;
        }
        s.seq = (s.seq || 0) + 1;
        s.entries.push({
            id: uid(),
            date: entryDate.value || todayStr(),
            type: entryType.value,
            amount: Math.round(amt * 100) / 100,
            note: entryNote.value.trim(),
            seq: s.seq
        });
        save();
        entryAmt.value = '';
        entryNote.value = '';
        renderList();
        renderLedger();
    });

    delStaffBtn.addEventListener('click', function () {
        hideError();
        var s = getStaff(activeId);
        if (!s) return;
        if (!confirm('Delete the complete advance record of ' + s.name + '?')) return;
        data.staff = data.staff.filter(function (x) { return x.id !== s.id; });
        activeId = null;
        save();
        renderList();
        renderLedger();
    });

    csvBtn.addEventListener('click', function () {
        var s = getStaff(activeId);
        if (!s) return;
        var lines = ['Date,Type,Amount,Note'];
        s.entries.forEach(function (e) {
            var note = '"' + String(e.note || '').replace(/"/g, '""') + '"';
            lines.push(e.date + ',' + e.type + ',' + e.amount + ',' + note);
        });
        lines.push(',,,');
        lines.push(',Running Balance,' + balance(s) + ',');
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = s.name.replace(/[^\w\- ]+/g, '').trim().replace(/\s+/g, '-') + '-advance-ledger.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () {
            URL.revokeObjectURL(a.href);
            a.remove();
        }, 500);
    });

    renderList();
    renderLedger();
})();
</script>
@endsection
