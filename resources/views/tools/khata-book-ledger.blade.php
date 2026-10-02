@extends('layouts.app')

@section('title', 'Credit Ledger Book - Azlaan Tools')
@section('meta_description', 'Free digital credit ledger book. Keep customer transactions, balance and payment history safe.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Credit Ledger Book</h1>
            <p class="lead text-muted">Digital credit ledger — keep customer transactions, balance and payment history safe. Your data is saved only in your browser.</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Customers</h5>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="custName" placeholder="Customer name">
                                <button type="button" class="btn btn-primary" id="addCustBtn">Add</button>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="custPhone" placeholder="Phone (optional)">
                            </div>
                            <div id="custList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">Click a customer to open their ledger.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div id="noCust" class="text-muted">Add a customer first, then their ledger will appear here.</div>
                            <div id="ledgerPane" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0" id="ledgerName">-</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="delCustBtn">Delete Customer</button>
                                </div>
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Total Credit</div>
                                            <div class="fw-bold text-danger" id="totCredit">Rs 0</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Total Payment</div>
                                            <div class="fw-bold text-success" id="totPaid">Rs 0</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Balance</div>
                                            <div class="fw-bold" id="totBal">Rs 0</div>
                                        </div>
                                    </div>
                                </div>
                                <h6>New entry</h6>
                                <div class="row g-2 mb-3">
                                    <div class="col-6 col-sm-3">
                                        <select class="form-select" id="entryType">
                                            <option value="udhaar">Credit (given)</option>
                                            <option value="payment">Payment (received)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="number" class="form-control" id="entryAmt" placeholder="Amount" min="1">
                                    </div>
                                    <div class="col-8 col-sm-4">
                                        <input type="text" class="form-control" id="entryNote" placeholder="Note (example: 2kg sugar)">
                                    </div>
                                    <div class="col-4 col-sm-2">
                                        <button type="button" class="btn btn-primary w-100" id="addEntryBtn">Add</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr><th>Date</th><th>Detail</th><th class="text-end">Credit</th><th class="text-end">Payment</th><th></th></tr>
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
                <li>Write the customer name and press <strong>Add</strong>.</li>
                <li>Click the customer, then add a <strong>Credit</strong> or <strong>Payment</strong> entry.</li>
                <li>The balance is calculated automatically. Download the CSV when you need it.</li>
            </ol>
            <p class="text-muted small">Note: your data stays in this browser only. If you clear your browser data or use another device, these records will not appear — keep taking CSV backups of your important records.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'khataLedgerV1';
    var custName = document.getElementById('custName');
    var custPhone = document.getElementById('custPhone');
    var addCustBtn = document.getElementById('addCustBtn');
    var custList = document.getElementById('custList');
    var noCust = document.getElementById('noCust');
    var ledgerPane = document.getElementById('ledgerPane');
    var ledgerName = document.getElementById('ledgerName');
    var delCustBtn = document.getElementById('delCustBtn');
    var totCredit = document.getElementById('totCredit');
    var totPaid = document.getElementById('totPaid');
    var totBal = document.getElementById('totBal');
    var entryType = document.getElementById('entryType');
    var entryAmt = document.getElementById('entryAmt');
    var entryNote = document.getElementById('entryNote');
    var addEntryBtn = document.getElementById('addEntryBtn');
    var entryRows = document.getElementById('entryRows');
    var csvBtn = document.getElementById('csvBtn');
    var errorBox = document.getElementById('errorBox');

    var data = { customers: [] };
    var activeId = null;

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.customers) data = parsed;
        }
    } catch (e) { data = { customers: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) { /* storage full/blocked */ }
    }
    function uid() {
        return 'c' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
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
    function getCust(id) {
        for (var i = 0; i < data.customers.length; i++) {
            if (data.customers[i].id === id) return data.customers[i];
        }
        return null;
    }
    function balance(c) {
        var b = 0;
        for (var i = 0; i < c.entries.length; i++) {
            b += c.entries[i].type === 'udhaar' ? c.entries[i].amount : -c.entries[i].amount;
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
        custList.innerHTML = '';
        if (!data.customers.length) {
            custList.innerHTML = '<div class="text-muted small">No customers yet. Write a name above and add it.</div>';
            return;
        }
        data.customers.forEach(function (c) {
            var b = balance(c);
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (c.id === activeId ? ' active' : '');
            var nameSpan = document.createElement('span');
            nameSpan.innerHTML = esc(c.name) + (c.phone ? '<br><small class="' + (c.id === activeId ? 'text-white-50' : 'text-muted') + '">' + esc(c.phone) + '</small>' : '');
            var badge = document.createElement('span');
            badge.className = 'badge ' + (b > 0 ? 'bg-danger' : 'bg-success') + ' rounded-pill';
            badge.textContent = fmt(b);
            a.appendChild(nameSpan);
            a.appendChild(badge);
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                activeId = c.id;
                renderList();
                renderLedger();
            });
            custList.appendChild(a);
        });
    }

    function renderLedger() {
        hideError();
        var c = getCust(activeId);
        if (!c) {
            noCust.classList.remove('d-none');
            ledgerPane.classList.add('d-none');
            return;
        }
        noCust.classList.add('d-none');
        ledgerPane.classList.remove('d-none');
        ledgerName.textContent = c.name + (c.phone ? ' (' + c.phone + ')' : '');
        var cr = 0, pd = 0;
        entryRows.innerHTML = '';
        var sorted = c.entries.slice().sort(function (x, y) {
            if (x.date === y.date) return y.seq - x.seq;
            return x.date < y.date ? 1 : -1;
        });
        sorted.forEach(function (e) {
            if (e.type === 'udhaar') cr += e.amount; else pd += e.amount;
            var tr = document.createElement('tr');
            var tdD = document.createElement('td');
            tdD.textContent = e.date;
            var tdN = document.createElement('td');
            tdN.innerHTML = esc(e.note || (e.type === 'udhaar' ? 'Credit' : 'Payment'));
            var tdC = document.createElement('td');
            tdC.className = 'text-end text-danger';
            tdC.textContent = e.type === 'udhaar' ? fmt(e.amount) : '-';
            var tdP = document.createElement('td');
            tdP.className = 'text-end text-success';
            tdP.textContent = e.type === 'payment' ? fmt(e.amount) : '-';
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete entry');
            del.addEventListener('click', function () {
                c.entries = c.entries.filter(function (en) { return en.id !== e.id; });
                save();
                renderList();
                renderLedger();
            });
            tdX.appendChild(del);
            tr.appendChild(tdD); tr.appendChild(tdN); tr.appendChild(tdC); tr.appendChild(tdP); tr.appendChild(tdX);
            entryRows.appendChild(tr);
        });
        totCredit.textContent = fmt(cr);
        totPaid.textContent = fmt(pd);
        var b = cr - pd;
        totBal.textContent = fmt(b);
        totBal.className = 'fw-bold ' + (b > 0 ? 'text-danger' : 'text-success');
    }

    addCustBtn.addEventListener('click', function () {
        hideError();
        var name = custName.value.trim();
        if (!name) { showError('Write the customer name.'); return; }
        data.customers.push({
            id: uid(),
            name: name,
            phone: custPhone.value.trim(),
            entries: [],
            seq: 0
        });
        save();
        custName.value = '';
        custPhone.value = '';
        activeId = data.customers[data.customers.length - 1].id;
        renderList();
        renderLedger();
    });

    addEntryBtn.addEventListener('click', function () {
        hideError();
        var c = getCust(activeId);
        if (!c) { showError('Select a customer first.'); return; }
        var amt = parseFloat(entryAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Enter a valid amount (more than 0).'); return; }
        c.seq = (c.seq || 0) + 1;
        c.entries.push({
            id: uid(),
            date: todayStr(),
            type: entryType.value,
            amount: Math.round(amt * 100) / 100,
            note: entryNote.value.trim(),
            seq: c.seq
        });
        save();
        entryAmt.value = '';
        entryNote.value = '';
        renderList();
        renderLedger();
    });

    delCustBtn.addEventListener('click', function () {
        hideError();
        var c = getCust(activeId);
        if (!c) return;
        if (!confirm(c.name + ': delete the full ledger?')) return;
        data.customers = data.customers.filter(function (x) { return x.id !== c.id; });
        activeId = null;
        save();
        renderList();
        renderLedger();
    });

    csvBtn.addEventListener('click', function () {
        var c = getCust(activeId);
        if (!c) return;
        var lines = ['Date,Type,Amount,Note'];
        c.entries.forEach(function (e) {
            var note = '"' + String(e.note || '').replace(/"/g, '""') + '"';
            lines.push(e.date + ',' + e.type + ',' + e.amount + ',' + note);
        });
        lines.push(',,,');
        lines.push(',Balance,' + balance(c) + ',');
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = c.name.replace(/[^\w\- ]+/g, '').trim().replace(/\s+/g, '-') + '-khata.csv';
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
