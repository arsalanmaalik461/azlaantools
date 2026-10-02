@extends('layouts.app')

@section('title', 'Credit Due Date Tracker - Azlaan Tools')
@section('meta_description', 'Set a return date on every loan — overdue amounts separate, one list sorted by due date.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Credit Due Date Tracker</h1>
            <p class="lead text-muted">Set a <strong>due date</strong> on every loan or payment promise. Overdue entries will show in red automatically, and the full list stays sorted by due date.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">New entry</h5>
                    <div class="row g-2">
                        <div class="col-12 col-md-3">
                            <label for="ddParty" class="form-label fw-semibold">Party / name</label>
                            <input type="text" class="form-control" id="ddParty" placeholder="Example: Imran Bhai">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="ddType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="ddType">
                                <option value="lena">To receive (from customer)</option>
                                <option value="dena">To pay (to supplier)</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="ddAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="ddAmount" placeholder="0" min="1" step="0.01">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="ddDue" class="form-label fw-semibold">Due date</label>
                            <input type="date" class="form-control" id="ddDue">
                        </div>
                        <div class="col-6 col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="ddAdd">Add</button>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <label for="ddNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="ddNote" placeholder="Example: promised before Eid">
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="row text-center g-2 mb-4">
                <div class="col-4">
                    <div class="card shadow-sm"><div class="card-body py-2">
                        <div class="small text-muted">Total Pending</div>
                        <div class="fw-bold" id="sumTotal">Rs 0</div>
                    </div></div>
                </div>
                <div class="col-4">
                    <div class="card shadow-sm border-danger"><div class="card-body py-2">
                        <div class="small text-muted">Overdue</div>
                        <div class="fw-bold text-danger" id="sumOverdue">Rs 0</div>
                    </div></div>
                </div>
                <div class="col-4">
                    <div class="card shadow-sm border-warning"><div class="card-body py-2">
                        <div class="small text-muted">Next 7 days</div>
                        <div class="fw-bold text-warning" id="sumSoon">Rs 0</div>
                    </div></div>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" id="ddTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tabAll" type="button" role="tab">All (due date sort)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tabOverdue" type="button" role="tab">Overdue</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tabDone" type="button" role="tab">Settled</button>
                </li>
            </ul>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr><th>Due Date</th><th>Party</th><th>Type</th><th class="text-end">Amount</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody id="ddRows"></tbody>
                </table>
            </div>
            <p class="text-muted small" id="ddEmpty">No entries yet. Add an entry with a due date from above.</p>

            <h2>How to use</h2>
            <ol>
                <li>Type the party name, type (to receive / to pay), amount and <strong>due date</strong>, then press Add.</li>
                <li>If today's date passes and the entry is still pending, it will show highlighted in red under <span class="badge bg-danger">Overdue</span>.</li>
                <li>Once received or paid, mark the entry as <strong>Settled</strong> — it will move to the history tab.</li>
            </ol>
            <p class="text-muted small">Note: Data is saved only in your browser, nothing is uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_due_dates';
    var ddParty = document.getElementById('ddParty');
    var ddType = document.getElementById('ddType');
    var ddAmount = document.getElementById('ddAmount');
    var ddDue = document.getElementById('ddDue');
    var ddNote = document.getElementById('ddNote');
    var ddAdd = document.getElementById('ddAdd');
    var ddRows = document.getElementById('ddRows');
    var ddEmpty = document.getElementById('ddEmpty');
    var errorBox = document.getElementById('errorBox');
    var sumTotal = document.getElementById('sumTotal');
    var sumOverdue = document.getElementById('sumOverdue');
    var sumSoon = document.getElementById('sumSoon');
    var tabAll = document.getElementById('tabAll');
    var tabOverdue = document.getElementById('tabOverdue');
    var tabDone = document.getElementById('tabDone');

    var data = { entries: [] };
    var filter = 'all';

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.entries) data = parsed;
        }
    } catch (e) { data = { entries: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) { /* ignore */ }
    }
    function uid() {
        return 'd' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n).toLocaleString('en-PK');
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function daysDiff(dateStr) {
        var t = new Date(todayStr() + 'T00:00:00');
        var d = new Date(dateStr + 'T00:00:00');
        return Math.round((d - t) / 86400000);
    }
    function statusOf(e) {
        if (e.settled) return 'settled';
        if (daysDiff(e.due) < 0) return 'overdue';
        if (daysDiff(e.due) <= 7) return 'soon';
        return 'pending';
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function setFilter(f) {
        filter = f;
        [tabAll, tabOverdue, tabDone].forEach(function (t) { t.classList.remove('active'); });
        if (f === 'all') tabAll.classList.add('active');
        else if (f === 'overdue') tabOverdue.classList.add('active');
        else tabDone.classList.add('active');
        render();
    }

    function render() {
        hideError();
        var today = todayStr();
        var tot = 0, ov = 0, soon = 0;
        data.entries.forEach(function (e) {
            if (e.settled) return;
            tot += e.amount;
            var st = statusOf(e);
            if (st === 'overdue') ov += e.amount;
            else if (st === 'soon') soon += e.amount;
        });
        sumTotal.textContent = fmt(tot);
        sumOverdue.textContent = fmt(ov);
        sumSoon.textContent = fmt(soon);

        var list = data.entries.slice().sort(function (a, b) {
            if (a.due === b.due) return b.created - a.created;
            return a.due < b.due ? -1 : 1;
        });
        list = list.filter(function (e) {
            if (filter === 'overdue') return statusOf(e) === 'overdue';
            if (filter === 'done') return e.settled;
            return !e.settled;
        });

        ddRows.innerHTML = '';
        ddEmpty.style.display = list.length ? 'none' : '';
        list.forEach(function (e) {
            var st = statusOf(e);
            var tr = document.createElement('tr');
            if (st === 'overdue') tr.className = 'table-danger';

            var tdDate = document.createElement('td');
            var diff = daysDiff(e.due);
            var dateTxt = e.due + (diff < 0 ? ' (' + Math.abs(diff) + ' days late)' : diff === 0 ? ' (today)' : ' (' + diff + ' days left)');
            tdDate.textContent = dateTxt;
            tdDate.className = st === 'overdue' ? 'fw-bold' : '';

            var tdParty = document.createElement('td');
            tdParty.innerHTML = esc(e.party) + (e.note ? '<br><small class="text-muted">' + esc(e.note) + '</small>' : '');

            var tdType = document.createElement('td');
            tdType.innerHTML = e.type === 'lena'
                ? '<span class="badge bg-success">To receive</span>'
                : '<span class="badge bg-primary">To pay</span>';

            var tdAmt = document.createElement('td');
            tdAmt.className = 'text-end fw-bold';
            tdAmt.textContent = fmt(e.amount);

            var tdStatus = document.createElement('td');
            var bdg = document.createElement('span');
            if (st === 'overdue') { bdg.className = 'badge bg-danger'; bdg.textContent = 'Overdue'; }
            else if (st === 'soon') { bdg.className = 'badge bg-warning text-dark'; bdg.textContent = 'Due soon'; }
            else if (st === 'settled') { bdg.className = 'badge bg-secondary'; bdg.textContent = 'Settled'; }
            else { bdg.className = 'badge bg-light text-dark border'; bdg.textContent = 'Pending'; }
            tdStatus.appendChild(bdg);

            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            tdX.style.whiteSpace = 'nowrap';
            if (!e.settled) {
                var done = document.createElement('button');
                done.type = 'button';
                done.className = 'btn btn-sm btn-outline-success me-1';
                done.textContent = 'Settled';
                done.addEventListener('click', function () {
                    e.settled = true;
                    save(); render();
                });
                tdX.appendChild(done);
            }
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '\u00D7';
            del.setAttribute('aria-label', 'Delete entry');
            del.addEventListener('click', function () {
                data.entries = data.entries.filter(function (x) { return x.id !== e.id; });
                save(); render();
            });
            tdX.appendChild(del);

            tr.appendChild(tdDate); tr.appendChild(tdParty); tr.appendChild(tdType);
            tr.appendChild(tdAmt); tr.appendChild(tdStatus); tr.appendChild(tdX);
            ddRows.appendChild(tr);
        });
    }

    ddAdd.addEventListener('click', function () {
        hideError();
        var party = ddParty.value.trim();
        if (!party) { showError('Write the party name.'); return; }
        var amt = parseFloat(ddAmount.value);
        if (isNaN(amt) || amt <= 0) { showError('Enter a correct amount (more than 0).'); return; }
        if (!ddDue.value) { showError('Select the due date.'); return; }
        data.entries.push({
            id: uid(),
            party: party,
            type: ddType.value,
            amount: Math.round(amt * 100) / 100,
            due: ddDue.value,
            note: ddNote.value.trim(),
            settled: false,
            created: Date.now()
        });
        save();
        ddParty.value = ''; ddAmount.value = ''; ddDue.value = ''; ddNote.value = '';
        render();
    });

    tabAll.addEventListener('click', function () { setFilter('all'); });
    tabOverdue.addEventListener('click', function () { setFilter('overdue'); });
    tabDone.addEventListener('click', function () { setFilter('done'); });

    ddDue.value = todayStr();
    render();
})();
</script>
@endsection
