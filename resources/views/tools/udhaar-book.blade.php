@extends('layouts.app')

@section('title', 'Lend & Borrow Book - Azlaan Tools')
@section('meta_description', 'Free lend and borrow book. Track what friends or relatives owe you and what you owe them, with settle-up.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Lend &amp; Borrow Book</h1>
            <p class="lead text-muted">Personal money tracking: who borrowed from you, who you owe — with settle-up. This is a <strong>personal</strong> record, separate from a business account. Data is saved only in your browser.</p>

            <div class="row text-center g-3 mb-4">
                <div class="col-6">
                    <div class="card bg-light"><div class="card-body py-2">
                        <small class="text-muted">You have to receive (receivable)</small>
                        <div class="fw-bold text-danger" id="sumRecv">Rs 0</div>
                    </div></div>
                </div>
                <div class="col-6">
                    <div class="card bg-light"><div class="card-body py-2">
                        <small class="text-muted">You have to pay (payable)</small>
                        <div class="fw-bold text-warning" id="sumPay">Rs 0</div>
                    </div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">People</h5>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="udhName" placeholder="Write the name">
                                <button type="button" class="btn btn-primary" id="udhAddBtn">Add</button>
                            </div>
                            <div id="udhList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">Click a name to open their record.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div id="noPerson" class="text-muted">First add a person's name — their record will appear here.</div>
                            <div id="udhPane" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <h5 class="mb-0" id="udhTitle">-</h5>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-success" id="udhSettleBtn">Settle up</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="udhDelBtn">Delete</button>
                                    </div>
                                </div>
                                <div class="alert alert-light border mb-3" id="udhNetBox">
                                    Net balance: <strong id="udhNet">Rs 0</strong>
                                    <div class="small text-muted" id="udhNetHint"></div>
                                </div>
                                <h6>New entry</h6>
                                <div class="row g-2 mb-2">
                                    <div class="col-6 col-sm-4">
                                        <select class="form-select" id="udhType">
                                            <option value="lent">Given (they have to return)</option>
                                            <option value="borrowed">Taken (you have to return)</option>
                                            <option value="received">Got back (from them)</option>
                                            <option value="paid">Returned (to them)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="number" class="form-control" id="udhAmt" placeholder="Amount" min="0.01" step="0.01">
                                    </div>
                                    <div class="col-12 col-sm-5">
                                        <input type="text" class="form-control" id="udhNote" placeholder="Note (optional)">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary mb-3" id="udhGoBtn">Add Entry</button>
                                <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>
                                <h6>History</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-light">
                                            <tr><th>Date</th><th>Type</th><th>Note</th><th class="text-end">Amount</th><th></th></tr>
                                        </thead>
                                        <tbody id="udhRows"></tbody>
                                    </table>
                                </div>
                                <p class="small text-muted mb-0" id="udhEmpty">No entries yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type a person's name and press <strong>Add</strong>.</li>
                <li>Select the name and make an entry — <strong>Given / Taken / Got back / Returned</strong> — the net balance is calculated automatically.</li>
                <li>When the record is settled, press <strong>Settle up</strong> — the balance becomes zero and it is recorded in history.</li>
            </ol>
            <p class="text-muted small">Note: Data is saved only in your browser, nothing is uploaded. If you clear your browser data, this record will be lost.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_udhaar_book';
    var udhName = document.getElementById('udhName');
    var udhAddBtn = document.getElementById('udhAddBtn');
    var udhList = document.getElementById('udhList');
    var noPerson = document.getElementById('noPerson');
    var udhPane = document.getElementById('udhPane');
    var udhTitle = document.getElementById('udhTitle');
    var udhSettleBtn = document.getElementById('udhSettleBtn');
    var udhDelBtn = document.getElementById('udhDelBtn');
    var udhNet = document.getElementById('udhNet');
    var udhNetHint = document.getElementById('udhNetHint');
    var udhType = document.getElementById('udhType');
    var udhAmt = document.getElementById('udhAmt');
    var udhNote = document.getElementById('udhNote');
    var udhGoBtn = document.getElementById('udhGoBtn');
    var errorBox = document.getElementById('errorBox');
    var udhRows = document.getElementById('udhRows');
    var udhEmpty = document.getElementById('udhEmpty');
    var sumRecv = document.getElementById('sumRecv');
    var sumPay = document.getElementById('sumPay');

    var data = { persons: [] };
    var activeId = null;
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.persons) data = parsed;
        }
    } catch (e) { data = { persons: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid() {
        return 'u' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
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
    function getPerson(id) {
        for (var i = 0; i < data.persons.length; i++) {
            if (data.persons[i].id === id) return data.persons[i];
        }
        return null;
    }
    // Net positive = they owe you; negative = you owe them
    function net(p) {
        var b = 0;
        p.entries.forEach(function (e) {
            if (e.type === 'lent' || e.type === 'paid') b += e.amount;
            else b -= e.amount;
        });
        return Math.round(b * 100) / 100;
    }
    function typeLabel(t) {
        if (t === 'lent') return '<span class="badge bg-danger">Given</span>';
        if (t === 'borrowed') return '<span class="badge bg-warning text-dark">Taken</span>';
        if (t === 'received') return '<span class="badge bg-success">Got back</span>';
        if (t === 'paid') return '<span class="badge bg-primary">Returned</span>';
        return '<span class="badge bg-light text-dark">' + esc(t) + '</span>';
    }

    function renderList() {
        udhList.innerHTML = '';
        if (!data.persons.length) {
            udhList.innerHTML = '<div class="text-muted small">No names yet. Type above and add.</div>';
            return;
        }
        data.persons.forEach(function (p) {
            var b = net(p);
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (p.id === activeId ? ' active' : '');
            var sp = document.createElement('span');
            sp.textContent = p.name;
            var badge = document.createElement('span');
            badge.className = 'badge rounded-pill ' + (b > 0 ? 'bg-danger' : (b < 0 ? 'bg-warning text-dark' : 'bg-success'));
            badge.textContent = fmt(Math.abs(b)) + (b === 0 ? '' : (b > 0 ? ' ↑' : ' ↓'));
            a.appendChild(sp); a.appendChild(badge);
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                activeId = p.id;
                renderList(); renderPane();
            });
            udhList.appendChild(a);
        });
    }

    function renderSums() {
        var recv = 0, pay = 0;
        data.persons.forEach(function (p) {
            var b = net(p);
            if (b > 0) recv += b; else pay += -b;
        });
        sumRecv.textContent = fmt(recv);
        sumPay.textContent = fmt(pay);
    }

    function renderPane() {
        hideError();
        var p = getPerson(activeId);
        if (!p) {
            noPerson.classList.remove('d-none');
            udhPane.classList.add('d-none');
            return;
        }
        noPerson.classList.add('d-none');
        udhPane.classList.remove('d-none');
        udhTitle.textContent = p.name;
        var b = net(p);
        udhNet.textContent = fmt(Math.abs(b));
        if (b > 0) {
            udhNet.className = 'text-danger';
            udhNetHint.textContent = 'You have to receive ' + fmt(b) + ' from this person.';
        } else if (b < 0) {
            udhNet.className = 'text-warning';
            udhNetHint.textContent = 'You have to pay ' + fmt(-b) + ' to this person.';
        } else {
            udhNet.className = 'text-success';
            udhNetHint.textContent = 'Record is settled — nothing pending.';
        }

        udhRows.innerHTML = '';
        var sorted = p.entries.slice().sort(function (x, y) {
            if (x.date === y.date) return y.seq - x.seq;
            return x.date < y.date ? 1 : -1;
        });
        sorted.forEach(function (e) {
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.textContent = e.date;
            var tdT = document.createElement('td'); tdT.innerHTML = typeLabel(e.type);
            var tdN = document.createElement('td'); tdN.textContent = e.note || '-';
            var tdA = document.createElement('td');
            tdA.className = 'text-end ' + ((e.type === 'lent' || e.type === 'paid') ? 'text-danger' : 'text-success');
            tdA.textContent = fmt(e.amount);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var x = document.createElement('button');
            x.type = 'button'; x.className = 'btn btn-sm btn-outline-danger'; x.textContent = '×';
            x.setAttribute('aria-label', 'Delete entry');
            x.addEventListener('click', function () {
                p.entries = p.entries.filter(function (ee) { return ee.id !== e.id; });
                save(); renderList(); renderPane(); renderSums();
            });
            tdX.appendChild(x);
            tr.appendChild(tdD); tr.appendChild(tdT); tr.appendChild(tdN);
            tr.appendChild(tdA); tr.appendChild(tdX);
            udhRows.appendChild(tr);
        });
        udhEmpty.style.display = sorted.length ? 'none' : '';
    }

    udhAddBtn.addEventListener('click', function () {
        hideError();
        var name = udhName.value.trim();
        if (!name) { showError('Write the name.'); return; }
        data.persons.push({ id: uid(), name: name, entries: [], seq: 0 });
        save();
        udhName.value = '';
        activeId = data.persons[data.persons.length - 1].id;
        renderList(); renderPane(); renderSums();
    });

    udhGoBtn.addEventListener('click', function () {
        hideError();
        var p = getPerson(activeId);
        if (!p) { showError('Select a person first.'); return; }
        var amt = Number(udhAmt.value);
        if (!amt || amt <= 0) { showError('Enter a correct amount (more than 0).'); return; }
        p.seq = (p.seq || 0) + 1;
        p.entries.push({
            id: uid(),
            date: todayYmd(),
            type: udhType.value,
            amount: Math.round(amt * 100) / 100,
            note: udhNote.value.trim(),
            seq: p.seq
        });
        save();
        udhAmt.value = ''; udhNote.value = '';
        renderList(); renderPane(); renderSums();
    });

    udhSettleBtn.addEventListener('click', function () {
        hideError();
        var p = getPerson(activeId);
        if (!p) return;
        var b = net(p);
        if (b === 0) { showError('Record is already settled.'); return; }
        var desc = b > 0 ? 'paid you' : 'you paid them';
        if (!confirm('Settle-up: ' + p.name + ' ' + desc + ' ' + fmt(Math.abs(b)) + '? Balance will become zero.')) return;
        p.seq = (p.seq || 0) + 1;
        p.entries.push({
            id: uid(),
            date: todayYmd(),
            type: b > 0 ? 'received' : 'paid',
            amount: Math.abs(b),
            note: 'Settle-up — record settled',
            seq: p.seq
        });
        save(); renderList(); renderPane(); renderSums();
    });

    udhDelBtn.addEventListener('click', function () {
        hideError();
        var p = getPerson(activeId);
        if (!p) return;
        if (!confirm('Delete the full record of ' + p.name + '?')) return;
        data.persons = data.persons.filter(function (x) { return x.id !== p.id; });
        activeId = null;
        save(); renderList(); renderPane(); renderSums();
    });

    renderList(); renderPane(); renderSums();
})();
</script>
@endsection
