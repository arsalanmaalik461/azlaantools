@extends('layouts.app')

@section('title', 'Supplier Payables Ledger - Azlaan Tools')
@section('meta_description', 'A separate ledger for credit taken from a supplier and amounts paid back — supplier payables ledger.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Supplier Payables Ledger</h1>
            <p class="lead text-muted">Keep a separate ledger for <strong>goods taken on credit</strong> and <strong>amounts paid back</strong> from each supplier / distributor. Each supplier's balance (payable) is calculated automatically.</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Suppliers</h5>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="supName" placeholder="Supplier name">
                                <button type="button" class="btn btn-primary" id="addSupBtn">Add</button>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="supPhone" placeholder="Phone (optional)">
                            </div>
                            <div id="supList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">Click a supplier to open its ledger.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div id="noSup" class="text-muted">Add a supplier first, then its ledger will appear here.</div>
                            <div id="ledgerPane" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0" id="ledgerName">-</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="delSupBtn">Supplier Delete</button>
                                </div>
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Total Credit Taken</div>
                                            <div class="fw-bold text-danger" id="totTaken">Rs 0</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Total Paid Back</div>
                                            <div class="fw-bold text-success" id="totGiven">Rs 0</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Balance (Payable)</div>
                                            <div class="fw-bold" id="totBal">Rs 0</div>
                                        </div>
                                    </div>
                                </div>
                                <h6>New entry</h6>
                                <div class="row g-2 mb-3">
                                    <div class="col-6 col-sm-3">
                                        <select class="form-select" id="entryType">
                                            <option value="credit">Credit taken</option>
                                            <option value="payment">Payment made</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="number" class="form-control" id="entryAmt" placeholder="Amount" min="1" step="0.01">
                                    </div>
                                    <div class="col-8 col-sm-4">
                                        <input type="text" class="form-control" id="entryNote" placeholder="Note (e.g. 10 cartons of oil)">
                                    </div>
                                    <div class="col-4 col-sm-2">
                                        <button type="button" class="btn btn-primary w-100" id="addEntryBtn">Add</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr><th>Date</th><th>Detail</th><th class="text-end">Credit Taken</th><th class="text-end">Paid Back</th><th></th></tr>
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
                <li>Type the supplier name and press <strong>Add</strong> (distributor, wholesaler, etc.).</li>
                <li>Select the supplier: use <strong>Credit taken</strong> when goods arrive on credit, <strong>Payment made</strong> when you pay them back.</li>
                <li><strong>Balance (Payable)</strong> is always shown live — how much is still owed to the supplier.</li>
                <li>Keep a record by downloading the <strong>CSV</strong> when needed.</li>
            </ol>
            <p class="text-muted small">Note: the data is saved only in your browser and is never uploaded. If you clear the browser data, the record will be lost — keep a CSV backup of important ledgers.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_supplier_ledger';
    var supName = document.getElementById('supName');
    var supPhone = document.getElementById('supPhone');
    var addSupBtn = document.getElementById('addSupBtn');
    var supList = document.getElementById('supList');
    var noSup = document.getElementById('noSup');
    var ledgerPane = document.getElementById('ledgerPane');
    var ledgerName = document.getElementById('ledgerName');
    var delSupBtn = document.getElementById('delSupBtn');
    var totTaken = document.getElementById('totTaken');
    var totGiven = document.getElementById('totGiven');
    var totBal = document.getElementById('totBal');
    var entryType = document.getElementById('entryType');
    var entryAmt = document.getElementById('entryAmt');
    var entryNote = document.getElementById('entryNote');
    var addEntryBtn = document.getElementById('addEntryBtn');
    var entryRows = document.getElementById('entryRows');
    var csvBtn = document.getElementById('csvBtn');
    var errorBox = document.getElementById('errorBox');

    var data = { suppliers: [] };
    var activeId = null;

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.suppliers) data = parsed;
        }
    } catch (e) { data = { suppliers: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) { /* storage full/blocked */ }
    }
    function uid() {
        return 's' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n).toLocaleString('en-PK');
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
    function getSup(id) {
        for (var i = 0; i < data.suppliers.length; i++) {
            if (data.suppliers[i].id === id) return data.suppliers[i];
        }
        return null;
    }
    function balance(s) {
        var b = 0;
        for (var i = 0; i < s.entries.length; i++) {
            b += s.entries[i].type === 'credit' ? s.entries[i].amount : -s.entries[i].amount;
        }
        return b;
    }
    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }

    function renderList() {
        supList.innerHTML = '';
        if (!data.suppliers.length) {
            supList.innerHTML = '<div class="text-muted small">No suppliers. Type a name above and add it.</div>';
            return;
        }
        data.suppliers.forEach(function (s) {
            var b = balance(s);
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (s.id === activeId ? ' active' : '');
            var nameSpan = document.createElement('span');
            nameSpan.innerHTML = esc(s.name) + (s.phone ? '<br><small class="' + (s.id === activeId ? 'text-white-50' : 'text-muted') + '">' + esc(s.phone) + '</small>' : '');
            var badge = document.createElement('span');
            badge.className = 'badge ' + (b > 0 ? 'bg-warning text-dark' : 'bg-success') + ' rounded-pill';
            badge.textContent = fmt(b);
            a.appendChild(nameSpan);
            a.appendChild(badge);
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                activeId = s.id;
                renderList();
                renderLedger();
            });
            supList.appendChild(a);
        });
    }

    function renderLedger() {
        hideError();
        var s = getSup(activeId);
        if (!s) {
            noSup.classList.remove('d-none');
            ledgerPane.classList.add('d-none');
            return;
        }
        noSup.classList.add('d-none');
        ledgerPane.classList.remove('d-none');
        ledgerName.textContent = s.name + (s.phone ? ' (' + s.phone + ')' : '');
        var cr = 0, pd = 0;
        entryRows.innerHTML = '';
        var sorted = s.entries.slice().sort(function (x, y) {
            if (x.date === y.date) return y.seq - x.seq;
            return x.date < y.date ? 1 : -1;
        });
        sorted.forEach(function (e) {
            if (e.type === 'credit') cr += e.amount; else pd += e.amount;
            var tr = document.createElement('tr');
            var tdD = document.createElement('td');
            tdD.textContent = e.date;
            var tdN = document.createElement('td');
            tdN.innerHTML = esc(e.note || (e.type === 'credit' ? 'Goods on credit' : 'Payment'));
            var tdC = document.createElement('td');
            tdC.className = 'text-end text-danger';
            tdC.textContent = e.type === 'credit' ? fmt(e.amount) : '-';
            var tdP = document.createElement('td');
            tdP.className = 'text-end text-success';
            tdP.textContent = e.type === 'payment' ? fmt(e.amount) : '-';
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '\u00D7';
            del.setAttribute('aria-label', 'Delete entry');
            del.addEventListener('click', function () {
                s.entries = s.entries.filter(function (en) { return en.id !== e.id; });
                save();
                renderList();
                renderLedger();
            });
            tdX.appendChild(del);
            tr.appendChild(tdD); tr.appendChild(tdN); tr.appendChild(tdC); tr.appendChild(tdP); tr.appendChild(tdX);
            entryRows.appendChild(tr);
        });
        totTaken.textContent = fmt(cr);
        totGiven.textContent = fmt(pd);
        var b = cr - pd;
        totBal.textContent = fmt(b);
        totBal.className = 'fw-bold ' + (b > 0 ? 'text-danger' : 'text-success');
    }

    addSupBtn.addEventListener('click', function () {
        hideError();
        var name = supName.value.trim();
        if (!name) { showError('Please type the supplier name.'); return; }
        data.suppliers.push({
            id: uid(),
            name: name,
            phone: supPhone.value.trim(),
            entries: [],
            seq: 0
        });
        save();
        supName.value = '';
        supPhone.value = '';
        activeId = data.suppliers[data.suppliers.length - 1].id;
        renderList();
        renderLedger();
    });

    addEntryBtn.addEventListener('click', function () {
        hideError();
        var s = getSup(activeId);
        if (!s) { showError('Please select a supplier first.'); return; }
        var amt = parseFloat(entryAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Please enter a valid amount (more than 0).'); return; }
        s.seq = (s.seq || 0) + 1;
        s.entries.push({
            id: uid(),
            date: todayStr(),
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

    delSupBtn.addEventListener('click', function () {
        hideError();
        var s = getSup(activeId);
        if (!s) return;
        if (!confirm('Delete the full ledger of ' + s.name + '?')) return;
        data.suppliers = data.suppliers.filter(function (x) { return x.id !== s.id; });
        activeId = null;
        save();
        renderList();
        renderLedger();
    });

    csvBtn.addEventListener('click', function () {
        var s = getSup(activeId);
        if (!s) return;
        var lines = ['Date,Type,Amount,Note'];
        s.entries.forEach(function (e) {
            var note = '"' + String(e.note || '').replace(/"/g, '""') + '"';
            lines.push(e.date + ',' + e.type + ',' + e.amount + ',' + note);
        });
        lines.push(',,,');
        lines.push(',Balance Payable,' + balance(s) + ',');
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = s.name.replace(/[^\w\- ]+/g, '').trim().replace(/\s+/g, '-') + '-supplier.csv';
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
