@extends('layouts.app')

@section('title', 'Personal Account Book - Azlaan Tools')
@section('meta_description', 'A separate ledger for family and friends — money given or taken, every person\'s account in one place.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Personal Account Book</h1>
            <p class="lead text-muted">Your own <strong>ledger for family, relatives and friends</strong> — fully separate from the shop account. Who took how much, who you gave how much to — track everything, no awkwardness.</p>

            <div class="alert alert-info d-flex align-items-center" role="alert">
                <div><strong>Your ledger, your people:</strong> this is only for personal dealings. Keep shop credit in the separate tool (<em>Credit Ledger Book</em>) so accounts never get mixed.</div>
            </div>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">People</h5>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="pkName" placeholder="Name (example: Uncle)">
                                <button type="button" class="btn btn-primary" id="addPkBtn">Add</button>
                            </div>
                            <div class="mb-2">
                                <select class="form-select" id="pkRelation">
                                    <option value="">Relation (optional)</option>
                                    <option value="Family">Family</option>
                                    <option value="Dost">Friend</option>
                                    <option value="Rishtedar">Relative</option>
                                    <option value="Parosi">Neighbour</option>
                                    <option value="Aur">Other</option>
                                </select>
                            </div>
                            <div id="pkList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">Click a name to open their account.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div id="noPk" class="text-muted">First add a name, then their transactions will appear here.</div>
                            <div id="ledgerPane" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0" id="ledgerName">-</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="delPkBtn">Delete Name</button>
                                </div>
                                <div class="alert py-2 mb-3" id="netBox" role="status">
                                    <span id="netLabel">Total balance</span>: <strong id="netVal">Rs 0</strong>
                                </div>
                                <h6>New entry</h6>
                                <div class="row g-2 mb-3">
                                    <div class="col-6 col-sm-3">
                                        <select class="form-select" id="entryType">
                                            <option value="gave">I gave</option>
                                            <option value="took">I took</option>
                                            <option value="gotback">Received back</option>
                                            <option value="returned">Paid back</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="number" class="form-control" id="entryAmt" placeholder="Amount" min="1" step="0.01">
                                    </div>
                                    <div class="col-8 col-sm-4">
                                        <input type="text" class="form-control" id="entryNote" placeholder="For what? (example: dinner)">
                                    </div>
                                    <div class="col-4 col-sm-2">
                                        <button type="button" class="btn btn-primary w-100" id="addEntryBtn">Add</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr><th>Date</th><th>Detail</th><th>Type</th><th class="text-end">Amount</th><th></th></tr>
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
                <li>Type a name and relation, then press <strong>Add</strong>.</li>
                <li>Select the name, then choose the entry type: <strong>I gave</strong> (lent money), <strong>I took</strong> (borrowed money), <strong>Received back</strong> or <strong>Paid back</strong>.</li>
                <li>The total balance will show automatically: whether they still owe you or you owe them.</li>
            </ol>
            <p class="text-muted small">Note: Data is saved only in your browser, never uploaded anywhere. This is a personal record — not advice.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_personal_khata';
    var pkName = document.getElementById('pkName');
    var pkRelation = document.getElementById('pkRelation');
    var addPkBtn = document.getElementById('addPkBtn');
    var pkList = document.getElementById('pkList');
    var noPk = document.getElementById('noPk');
    var ledgerPane = document.getElementById('ledgerPane');
    var ledgerName = document.getElementById('ledgerName');
    var delPkBtn = document.getElementById('delPkBtn');
    var netBox = document.getElementById('netBox');
    var netLabel = document.getElementById('netLabel');
    var netVal = document.getElementById('netVal');
    var entryType = document.getElementById('entryType');
    var entryAmt = document.getElementById('entryAmt');
    var entryNote = document.getElementById('entryNote');
    var addEntryBtn = document.getElementById('addEntryBtn');
    var entryRows = document.getElementById('entryRows');
    var csvBtn = document.getElementById('csvBtn');
    var errorBox = document.getElementById('errorBox');

    var TYPE_LABEL = {
        gave: 'I gave',
        took: 'I took',
        gotback: 'Received back',
        returned: 'Paid back'
    };

    var data = { people: [] };
    var activeId = null;

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.people) data = parsed;
        }
    } catch (e) { data = { people: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) { /* ignore */ }
    }
    function uid() {
        return 'p' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
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
    function getPerson(id) {
        for (var i = 0; i < data.people.length; i++) {
            if (data.people[i].id === id) return data.people[i];
        }
        return null;
    }
    /* positive = they owe you (you gave more); negative = you owe them */
    function net(p) {
        var b = 0;
        for (var i = 0; i < p.entries.length; i++) {
            var e = p.entries[i];
            if (e.type === 'gave') b += e.amount;
            else if (e.type === 'gotback') b -= e.amount;
            else if (e.type === 'took') b -= e.amount;
            else if (e.type === 'returned') b += e.amount;
        }
        return b;
    }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }

    function renderList() {
        pkList.innerHTML = '';
        if (!data.people.length) {
            pkList.innerHTML = '<div class="text-muted small">No names yet. Type a name above and add it.</div>';
            return;
        }
        data.people.forEach(function (p) {
            var n = net(p);
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (p.id === activeId ? ' active' : '');
            var nameSpan = document.createElement('span');
            nameSpan.innerHTML = esc(p.name) + (p.relation ? '<br><small class="' + (p.id === activeId ? 'text-white-50' : 'text-muted') + '">' + esc(p.relation) + '</small>' : '');
            var badge = document.createElement('span');
            badge.className = 'badge ' + (n > 0 ? 'bg-warning text-dark' : n < 0 ? 'bg-info text-dark' : 'bg-success') + ' rounded-pill';
            badge.textContent = n === 0 ? 'Clear' : fmt(Math.abs(n));
            a.appendChild(nameSpan);
            a.appendChild(badge);
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                activeId = p.id;
                renderList();
                renderLedger();
            });
            pkList.appendChild(a);
        });
    }

    function renderLedger() {
        hideError();
        var p = getPerson(activeId);
        if (!p) {
            noPk.classList.remove('d-none');
            ledgerPane.classList.add('d-none');
            return;
        }
        noPk.classList.add('d-none');
        ledgerPane.classList.remove('d-none');
        ledgerName.textContent = p.name + (p.relation ? ' (' + p.relation + ')' : '');
        var n = net(p);
        netVal.textContent = fmt(Math.abs(n));
        if (n > 0) {
            netLabel.textContent = 'They owe you (your given amount remaining)';
            netBox.className = 'alert alert-warning py-2 mb-3';
        } else if (n < 0) {
            netLabel.textContent = 'You owe them (your taken amount remaining)';
            netBox.className = 'alert alert-info py-2 mb-3';
        } else {
            netLabel.textContent = 'Account is clear — nothing pending';
            netBox.className = 'alert alert-success py-2 mb-3';
        }
        entryRows.innerHTML = '';
        var sorted = p.entries.slice().sort(function (x, y) {
            if (x.date === y.date) return y.seq - x.seq;
            return x.date < y.date ? 1 : -1;
        });
        sorted.forEach(function (e) {
            var tr = document.createElement('tr');
            var tdD = document.createElement('td');
            tdD.textContent = e.date;
            var tdN = document.createElement('td');
            tdN.textContent = e.note || '—';
            var tdT = document.createElement('td');
            var cls = e.type === 'gave' ? 'bg-warning text-dark' : e.type === 'took' ? 'bg-info text-dark' : 'bg-success';
            tdT.innerHTML = '<span class="badge ' + cls + '">' + TYPE_LABEL[e.type] + '</span>';
            var tdA = document.createElement('td');
            tdA.className = 'text-end';
            tdA.textContent = fmt(e.amount);
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '\u00D7';
            del.setAttribute('aria-label', 'Delete entry');
            del.addEventListener('click', function () {
                p.entries = p.entries.filter(function (en) { return en.id !== e.id; });
                save();
                renderList();
                renderLedger();
            });
            tdX.appendChild(del);
            tr.appendChild(tdD); tr.appendChild(tdN); tr.appendChild(tdT); tr.appendChild(tdA); tr.appendChild(tdX);
            entryRows.appendChild(tr);
        });
    }

    addPkBtn.addEventListener('click', function () {
        hideError();
        var name = pkName.value.trim();
        if (!name) { showError('Enter a name.'); return; }
        data.people.push({
            id: uid(),
            name: name,
            relation: pkRelation.value,
            entries: [],
            seq: 0
        });
        save();
        pkName.value = '';
        pkRelation.value = '';
        activeId = data.people[data.people.length - 1].id;
        renderList();
        renderLedger();
    });

    addEntryBtn.addEventListener('click', function () {
        hideError();
        var p = getPerson(activeId);
        if (!p) { showError('Select a name first.'); return; }
        var amt = parseFloat(entryAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Enter a valid amount (more than 0).'); return; }
        p.seq = (p.seq || 0) + 1;
        p.entries.push({
            id: uid(),
            date: todayStr(),
            type: entryType.value,
            amount: Math.round(amt * 100) / 100,
            note: entryNote.value.trim(),
            seq: p.seq
        });
        save();
        entryAmt.value = '';
        entryNote.value = '';
        renderList();
        renderLedger();
    });

    delPkBtn.addEventListener('click', function () {
        hideError();
        var p = getPerson(activeId);
        if (!p) return;
        if (!confirm('Delete the full account of ' + p.name + '?')) return;
        data.people = data.people.filter(function (x) { return x.id !== p.id; });
        activeId = null;
        save();
        renderList();
        renderLedger();
    });

    csvBtn.addEventListener('click', function () {
        var p = getPerson(activeId);
        if (!p) return;
        var lines = ['Date,Type,Amount,Note'];
        p.entries.forEach(function (e) {
            var note = '"' + String(e.note || '').replace(/"/g, '""') + '"';
            lines.push(e.date + ',' + TYPE_LABEL[e.type] + ',' + e.amount + ',' + note);
        });
        lines.push(',,,');
        lines.push(',Total Balance,' + net(p) + ',');
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = p.name.replace(/[^\w\- ]+/g, '').trim().replace(/\s+/g, '-') + '-personal-khata.csv';
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
