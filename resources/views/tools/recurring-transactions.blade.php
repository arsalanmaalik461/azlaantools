@extends('layouts.app')

@section('title', 'Recurring Transactions Tracker - Azlaan Tools')
@section('meta_description', 'Free recurring transactions tracker: track salary, rent, bills and other repeating income or expenses with rollover.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Recurring Transactions Tracker</h1>
            <p class="lead text-muted">Set up repeating expenses or income like salary, rent or the electricity bill — the next due date comes up automatically. Data is saved only in your browser, never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Set a new recurring item</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="rcName" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="rcName" placeholder="e.g. House rent">
                        </div>
                        <div class="col-md-6">
                            <label for="rcType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="rcType">
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="rcAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="rcAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="rcFreq" class="form-label fw-semibold">Frequency</label>
                            <select class="form-select" id="rcFreq">
                                <option value="weekly">Weekly</option>
                                <option value="monthly" selected>Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="rcNext" class="form-label fw-semibold">Next date (next due)</label>
                            <input type="date" class="form-control" id="rcNext">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="addRcBtn">Add Recurring</button>
                    <div class="alert alert-danger mt-3 d-none" id="rcError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Upcoming schedule</h2>
                    <div id="upcomingList"></div>
                    <p class="text-muted small mb-0" id="rcEmpty">No recurring items set yet.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Completed log</h2>
                        <span class="badge bg-secondary" id="doneCount">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light"><tr><th>Date</th><th>Name</th><th class="text-end">Amount</th></tr></thead>
                            <tbody id="doneRows"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the recurring item's <strong>name, type, amount, frequency</strong> and next due date, then add it.</li>
                <li>When the payment is made, press <strong>Mark Done</strong> — the next due date will move to the next cycle on its own.</li>
                <li>Overdue items appear in <strong>red</strong>.</li>
            </ol>
            <p class="text-muted small">Note: data stays safe in this same browser. Clearing browser data will erase this record.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_recurring';
    var FREQ_LABELS = { weekly: 'Weekly', monthly: 'Monthly', quarterly: 'Quarterly', yearly: 'Yearly' };

    var rcName = document.getElementById('rcName');
    var rcType = document.getElementById('rcType');
    var rcAmt = document.getElementById('rcAmt');
    var rcFreq = document.getElementById('rcFreq');
    var rcNext = document.getElementById('rcNext');
    var addRcBtn = document.getElementById('addRcBtn');
    var rcError = document.getElementById('rcError');
    var upcomingList = document.getElementById('upcomingList');
    var rcEmpty = document.getElementById('rcEmpty');
    var doneCount = document.getElementById('doneCount');
    var doneRows = document.getElementById('doneRows');

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && p.items) return p;
            }
        } catch (e) {}
        return { items: [], done: [] };
    }
    function save(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); } catch (e) {}
    }
    function showError(msg) { rcError.textContent = msg; rcError.classList.remove('d-none'); }
    function hideError() { rcError.classList.add('d-none'); rcError.textContent = ''; }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function uid() { return 'r' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36); }
    function parseYMD(s) {
        var parts = String(s).split('-');
        return new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    }
    function fmtYMD(d) {
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function todayYMD() { return fmtYMD(new Date()); }
    function addCycle(dateStr, freq) {
        var d = parseYMD(dateStr);
        if (freq === 'weekly') d.setDate(d.getDate() + 7);
        else if (freq === 'monthly') d.setMonth(d.getMonth() + 1);
        else if (freq === 'quarterly') d.setMonth(d.getMonth() + 3);
        else if (freq === 'yearly') d.setFullYear(d.getFullYear() + 1);
        return fmtYMD(d);
    }
    function daysLeft(dateStr) {
        var a = parseYMD(todayYMD());
        var b = parseYMD(dateStr);
        return Math.round((b - a) / 86400000);
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function render() {
        hideError();
        var d = load();
        upcomingList.innerHTML = '';
        var sorted = d.items.slice().sort(function (x, y) { return x.next < y.next ? -1 : 1; });
        sorted.forEach(function (it) {
            var dl = daysLeft(it.next);
            var item = document.createElement('div');
            item.className = 'border rounded p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2' + (dl < 0 ? ' border-danger' : '');
            var left = document.createElement('div');
            var nm = document.createElement('div');
            nm.className = 'fw-bold';
            nm.textContent = it.name;
            var meta = document.createElement('div');
            meta.className = 'small text-muted';
            meta.textContent = (it.type === 'income' ? 'Income' : 'Expense') + ' • ' + (FREQ_LABELS[it.freq] || it.freq) + ' • Next: ' + it.next;
            var status = document.createElement('div');
            status.className = 'small fw-bold ' + (dl < 0 ? 'text-danger' : (dl <= 3 ? 'text-warning' : 'text-success'));
            status.textContent = dl < 0 ? 'OVERDUE (' + Math.abs(dl) + ' days late)' : (dl === 0 ? 'DUE TODAY' : dl + ' days left');
            left.appendChild(nm);
            left.appendChild(meta);
            left.appendChild(status);
            var right = document.createElement('div');
            right.className = 'text-end';
            var amtDiv = document.createElement('div');
            amtDiv.className = 'fw-bold fs-5 ' + (it.type === 'income' ? 'text-success' : 'text-danger');
            amtDiv.textContent = fmt(it.amount);
            var btnRow = document.createElement('div');
            btnRow.className = 'mt-1 d-flex gap-1 justify-content-end';
            var doneBtn = document.createElement('button');
            doneBtn.type = 'button';
            doneBtn.className = 'btn btn-sm btn-success';
            doneBtn.textContent = 'Mark Done';
            doneBtn.addEventListener('click', function () {
                var d2 = load();
                for (var i = 0; i < d2.items.length; i++) {
                    if (d2.items[i].id === it.id) {
                        d2.done.push({ id: uid(), name: d2.items[i].name, type: d2.items[i].type, amount: d2.items[i].amount, date: todayYMD() });
                        d2.items[i].next = addCycle(d2.items[i].next, d2.items[i].freq);
                        break;
                    }
                }
                save(d2);
                render();
            });
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = 'Delete';
            delBtn.addEventListener('click', function () {
                if (!confirm('Delete ' + it.name + ' recurring?')) return;
                var d2 = load();
                d2.items = d2.items.filter(function (x) { return x.id !== it.id; });
                save(d2);
                render();
            });
            btnRow.appendChild(doneBtn);
            btnRow.appendChild(delBtn);
            right.appendChild(amtDiv);
            right.appendChild(btnRow);
            item.appendChild(left);
            item.appendChild(right);
            upcomingList.appendChild(item);
        });
        rcEmpty.style.display = d.items.length ? 'none' : '';

        doneRows.innerHTML = '';
        var doneSorted = d.done.slice().reverse();
        doneSorted.forEach(function (e) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = e.date;
            var td2 = document.createElement('td');
            var b = document.createElement('span');
            b.className = 'badge ' + (e.type === 'income' ? 'bg-success' : 'bg-danger');
            b.textContent = e.type === 'income' ? 'Income' : 'Expense';
            td2.appendChild(document.createTextNode(e.name + ' '));
            td2.appendChild(b);
            var td3 = document.createElement('td'); td3.className = 'text-end'; td3.textContent = fmt(e.amount);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            doneRows.appendChild(tr);
        });
        doneCount.textContent = d.done.length;
    }

    addRcBtn.addEventListener('click', function () {
        hideError();
        var name = rcName.value.trim();
        if (!name) { showError('Please enter a name (e.g. House rent).'); return; }
        var amt = Number(rcAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Please enter an amount more than 0.'); return; }
        if (!rcNext.value) { showError('Please select the next due date.'); return; }
        var d = load();
        d.items.push({
            id: uid(),
            name: name,
            type: rcType.value,
            amount: Math.round(amt * 100) / 100,
            freq: rcFreq.value,
            next: rcNext.value
        });
        save(d);
        rcName.value = '';
        rcAmt.value = '';
        rcNext.value = '';
        render();
    });

    render();
})();
</script>
@endsection
