@extends('layouts.app')

@section('title', 'Advance Payment Tracker - Azlaan Tools')
@section('meta_description', 'Free advance payment tracker. Track advances taken from customers separately and adjust them against dues.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Advance Payment Tracker</h1>
            <p class="lead text-muted">Track advances taken from customers separately — adjust them against dues or bills. Your data stays saved only in your browser.</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Parties</h5>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="advName" placeholder="Party name">
                                <button type="button" class="btn btn-primary" id="advAddBtn">Add</button>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="advPhone" placeholder="Phone (optional)">
                            </div>
                            <div id="advList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">Click a party to open its advance account.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div id="noParty" class="text-muted">First add a party, then its advance account will appear here.</div>
                            <div id="advPane" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <h5 class="mb-0" id="advTitle">-</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="advDelBtn">Party Delete</button>
                                </div>
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Total Advance Received</div>
                                            <div class="fw-bold text-success" id="advTotIn">Rs 0</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="small text-muted">Current Advance Balance</div>
                                            <div class="fw-bold" id="advTotBal">Rs 0</div>
                                        </div>
                                    </div>
                                </div>
                                <h6>New action</h6>
                                <div class="row g-2 mb-2">
                                    <div class="col-6 col-sm-4">
                                        <select class="form-select" id="advAction">
                                            <option value="advance">Advance received</option>
                                            <option value="adjust">Adjust against dues</option>
                                            <option value="refund">Refund (given back)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="number" class="form-control" id="advAmt" placeholder="Amount" min="0.01" step="0.01">
                                    </div>
                                    <div class="col-12 col-sm-5">
                                        <input type="text" class="form-control" id="advNote" placeholder="Note (e.g. adjusted in bill #45)">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary mb-3" id="advGoBtn">Save</button>
                                <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>
                                <h6>History</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-light">
                                            <tr><th>Date</th><th>Action</th><th>Note</th><th class="text-end">Amount</th><th></th></tr>
                                        </thead>
                                        <tbody id="advRows"></tbody>
                                    </table>
                                </div>
                                <p class="small text-muted mb-0" id="advEmpty">No history yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>How it works:</strong> Advance received = balance goes up (you are holding the customer's money).
                Adjust against dues = balance goes down (the advance was used against dues or a bill).
                Refund = money returned to the customer.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the party name and press <strong>Add</strong>.</li>
                <li>Select the party and add an <strong>Advance received</strong> entry when you get an advance.</li>
                <li>When there is a bill or dues, use <strong>Adjust against dues</strong> to use up the advance — the balance updates by itself.</li>
            </ol>
            <p class="text-muted small">Note: Your data is saved only in your browser and is never uploaded anywhere. Clearing your browser data will erase this record.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_advances';
    var advName = document.getElementById('advName');
    var advPhone = document.getElementById('advPhone');
    var advAddBtn = document.getElementById('advAddBtn');
    var advList = document.getElementById('advList');
    var noParty = document.getElementById('noParty');
    var advPane = document.getElementById('advPane');
    var advTitle = document.getElementById('advTitle');
    var advDelBtn = document.getElementById('advDelBtn');
    var advTotIn = document.getElementById('advTotIn');
    var advTotBal = document.getElementById('advTotBal');
    var advAction = document.getElementById('advAction');
    var advAmt = document.getElementById('advAmt');
    var advNote = document.getElementById('advNote');
    var advGoBtn = document.getElementById('advGoBtn');
    var errorBox = document.getElementById('errorBox');
    var advRows = document.getElementById('advRows');
    var advEmpty = document.getElementById('advEmpty');

    var data = { parties: [] };
    var activeId = null;
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.parties) data = parsed;
        }
    } catch (e) { data = { parties: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid() {
        return 'a' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
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
    function getParty(id) {
        for (var i = 0; i < data.parties.length; i++) {
            if (data.parties[i].id === id) return data.parties[i];
        }
        return null;
    }
    function balance(p) {
        var b = 0;
        p.history.forEach(function (h) {
            if (h.kind === 'advance') b += h.amount;
            else b -= h.amount;
        });
        return Math.round(b * 100) / 100;
    }
    function kindLabel(k) {
        if (k === 'advance') return '<span class="badge bg-success">Advance received</span>';
        if (k === 'adjust') return '<span class="badge bg-primary">Adjust</span>';
        return '<span class="badge bg-warning text-dark">Refund</span>';
    }

    function renderList() {
        advList.innerHTML = '';
        if (!data.parties.length) {
            advList.innerHTML = '<div class="text-muted small">No parties yet. Type a name above and add it.</div>';
            return;
        }
        data.parties.forEach(function (p) {
            var b = balance(p);
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (p.id === activeId ? ' active' : '');
            var sp = document.createElement('span');
            sp.innerHTML = esc(p.name) + (p.phone ? '<br><small class="' + (p.id === activeId ? 'text-white-50' : 'text-muted') + '">' + esc(p.phone) + '</small>' : '');
            var badge = document.createElement('span');
            badge.className = 'badge bg-primary rounded-pill';
            badge.textContent = fmt(b);
            a.appendChild(sp); a.appendChild(badge);
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                activeId = p.id;
                renderList(); renderPane();
            });
            advList.appendChild(a);
        });
    }

    function renderPane() {
        hideError();
        var p = getParty(activeId);
        if (!p) {
            noParty.classList.remove('d-none');
            advPane.classList.add('d-none');
            return;
        }
        noParty.classList.add('d-none');
        advPane.classList.remove('d-none');
        advTitle.textContent = p.name + (p.phone ? ' (' + p.phone + ')' : '');
        var totIn = 0;
        p.history.forEach(function (h) { if (h.kind === 'advance') totIn += h.amount; });
        var b = balance(p);
        advTotIn.textContent = fmt(totIn);
        advTotBal.textContent = fmt(b);
        advTotBal.className = 'fw-bold ' + (b > 0 ? 'text-success' : 'text-muted');

        advRows.innerHTML = '';
        var sorted = p.history.slice().sort(function (x, y) {
            if (x.date === y.date) return y.seq - x.seq;
            return x.date < y.date ? 1 : -1;
        });
        sorted.forEach(function (h) {
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.textContent = h.date;
            var tdK = document.createElement('td'); tdK.innerHTML = kindLabel(h.kind);
            var tdN = document.createElement('td'); tdN.textContent = h.note || '-';
            var tdA = document.createElement('td');
            tdA.className = 'text-end ' + (h.kind === 'advance' ? 'text-success' : 'text-danger');
            tdA.textContent = (h.kind === 'advance' ? '+' : '-') + fmt(h.amount);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var x = document.createElement('button');
            x.type = 'button'; x.className = 'btn btn-sm btn-outline-danger'; x.textContent = '×';
            x.setAttribute('aria-label', 'Delete entry');
            x.addEventListener('click', function () {
                p.history = p.history.filter(function (hh) { return hh.id !== h.id; });
                save(); renderList(); renderPane();
            });
            tdX.appendChild(x);
            tr.appendChild(tdD); tr.appendChild(tdK); tr.appendChild(tdN);
            tr.appendChild(tdA); tr.appendChild(tdX);
            advRows.appendChild(tr);
        });
        advEmpty.style.display = sorted.length ? 'none' : '';
    }

    advAddBtn.addEventListener('click', function () {
        hideError();
        var name = advName.value.trim();
        if (!name) { showError('Type the party name.'); return; }
        data.parties.push({ id: uid(), name: name, phone: advPhone.value.trim(), history: [], seq: 0 });
        save();
        advName.value = ''; advPhone.value = '';
        activeId = data.parties[data.parties.length - 1].id;
        renderList(); renderPane();
    });

    advGoBtn.addEventListener('click', function () {
        hideError();
        var p = getParty(activeId);
        if (!p) { showError('Select a party first.'); return; }
        var amt = Number(advAmt.value);
        if (!amt || amt <= 0) { showError('Enter a valid amount (more than 0).'); return; }
        var kind = advAction.value;
        var amtR = Math.round(amt * 100) / 100;
        if (kind !== 'advance' && balance(p) < amtR) {
            showError('The amount is more than the available balance, so it cannot be adjusted or refunded. Current balance: ' + fmt(balance(p)));
            return;
        }
        p.seq = (p.seq || 0) + 1;
        p.history.push({
            id: uid(),
            date: todayYmd(),
            kind: kind,
            amount: amtR,
            note: advNote.value.trim(),
            seq: p.seq
        });
        save();
        advAmt.value = ''; advNote.value = '';
        renderList(); renderPane();
    });

    advDelBtn.addEventListener('click', function () {
        hideError();
        var p = getParty(activeId);
        if (!p) return;
        if (!confirm('Delete the whole advance account of ' + p.name + '?')) return;
        data.parties = data.parties.filter(function (x) { return x.id !== p.id; });
        activeId = null;
        save(); renderList(); renderPane();
    });

    renderList(); renderPane();
})();
</script>
@endsection
