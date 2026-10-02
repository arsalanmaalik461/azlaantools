@extends('layouts.app')

@section('title', 'Bill & Subscription Reminders - Azlaan Tools')
@section('meta_description', 'Free bill reminder tracker: save due dates for bills and subscriptions and never pay a late fee again.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Bill &amp; Subscription Reminders</h1>
            <p class="lead text-muted">Save your bills' due dates — avoid late fees. Data is saved only in your browser, nothing is uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add a new bill</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="blName" class="form-label fw-semibold">Bill name</label>
                            <input type="text" class="form-control" id="blName" placeholder="Example: Electricity bill">
                        </div>
                        <div class="col-md-6">
                            <label for="blAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="blAmt" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="blDue" class="form-label fw-semibold">Due date</label>
                            <input type="date" class="form-control" id="blDue">
                        </div>
                        <div class="col-md-6">
                            <label for="blRepeat" class="form-label fw-semibold">Repeat</label>
                            <select class="form-select" id="blRepeat">
                                <option value="monthly">Every month (monthly)</option>
                                <option value="once">Only once (one-time)</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="addBlBtn">Add Bill</button>
                    <div class="alert alert-danger mt-3 d-none" id="blError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Upcoming bills</h2>
                        <span class="badge bg-primary" id="upCount">0</span>
                    </div>
                    <div id="upList"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Paid / done</h2>
                        <span class="badge bg-success" id="paidCount">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light"><tr><th>Paid date</th><th>Bill</th><th class="text-end">Amount</th></tr></thead>
                            <tbody id="paidRows"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add a bill with its <strong>name, amount and due date</strong> — monthly bills repeat themselves next month.</li>
                <li>When you pay, press <strong>Paid</strong>.</li>
                <li><strong>Overdue</strong> bills (date passed) appear in red at the top.</li>
            </ol>
            <p class="text-muted small">Note: your data stays safe in this browser. If you clear your browser data, this record will be deleted.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_bill_reminders';

    var blName = document.getElementById('blName');
    var blAmt = document.getElementById('blAmt');
    var blDue = document.getElementById('blDue');
    var blRepeat = document.getElementById('blRepeat');
    var addBlBtn = document.getElementById('addBlBtn');
    var blError = document.getElementById('blError');
    var upList = document.getElementById('upList');
    var upCount = document.getElementById('upCount');
    var paidRows = document.getElementById('paidRows');
    var paidCount = document.getElementById('paidCount');

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && p.bills) return p;
            }
        } catch (e) {}
        return { bills: [], paid: [] };
    }
    function save(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); } catch (e) {}
    }
    function showError(msg) { blError.textContent = msg; blError.classList.remove('d-none'); }
    function hideError() { blError.classList.add('d-none'); blError.textContent = ''; }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function uid() { return 'b' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36); }
    function parseYMD(s) {
        var parts = String(s).split('-');
        return new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    }
    function fmtYMD(d) {
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function todayYMD() { return fmtYMD(new Date()); }
    function daysLeft(dateStr) {
        var a = parseYMD(todayYMD());
        var b = parseYMD(dateStr);
        return Math.round((b - a) / 86400000);
    }
    function nextMonth(dateStr) {
        var d = parseYMD(dateStr);
        d.setMonth(d.getMonth() + 1);
        return fmtYMD(d);
    }

    function render() {
        hideError();
        var d = load();
        upList.innerHTML = '';
        var pending = d.bills.filter(function (b) { return !b.paid; })
            .sort(function (x, y) { return x.due < y.due ? -1 : 1; });

        if (!pending.length) {
            upList.innerHTML = '<p class="text-muted small mb-0">No pending bills. Fill the form below and add.</p>';
        }
        pending.forEach(function (b) {
            var dl = daysLeft(b.due);
            var overdue = dl < 0;
            var card = document.createElement('div');
            card.className = 'border rounded p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2' + (overdue ? ' border-danger bg-light' : '');
            var left = document.createElement('div');
            var nm = document.createElement('div');
            nm.className = 'fw-bold';
            nm.textContent = b.name;
            var meta = document.createElement('div');
            meta.className = 'small text-muted';
            meta.textContent = 'Due: ' + b.due + ' • ' + (b.repeat === 'monthly' ? 'Monthly' : 'One-time') + (b.amount ? ' • ' + fmt(b.amount) : '');
            var status = document.createElement('div');
            status.className = 'small fw-bold ' + (overdue ? 'text-danger' : (dl <= 3 ? 'text-warning' : 'text-success'));
            status.textContent = overdue ? 'OVERDUE — ' + Math.abs(dl) + ' days late! A late fee may apply.' : (dl === 0 ? 'DUE TODAY' : dl + ' days left');
            left.appendChild(nm);
            left.appendChild(meta);
            left.appendChild(status);
            var right = document.createElement('div');
            right.className = 'd-flex gap-1';
            var paidBtn = document.createElement('button');
            paidBtn.type = 'button';
            paidBtn.className = 'btn btn-sm btn-success';
            paidBtn.textContent = 'Paid';
            paidBtn.addEventListener('click', function () {
                var d2 = load();
                for (var i = 0; i < d2.bills.length; i++) {
                    if (d2.bills[i].id === b.id) {
                        var it = d2.bills[i];
                        d2.paid.push({ id: uid(), name: it.name, amount: it.amount, date: todayYMD() });
                        if (it.repeat === 'monthly') {
                            it.due = nextMonth(it.due);
                            it.paid = false;
                        } else {
                            d2.bills.splice(i, 1);
                        }
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
                if (!confirm('Delete the ' + b.name + ' reminder?')) return;
                var d2 = load();
                d2.bills = d2.bills.filter(function (x) { return x.id !== b.id; });
                save(d2);
                render();
            });
            right.appendChild(paidBtn);
            right.appendChild(delBtn);
            card.appendChild(left);
            card.appendChild(right);
            upList.appendChild(card);
        });
        upCount.textContent = pending.length;

        paidRows.innerHTML = '';
        var ps = d.paid.slice().reverse();
        ps.forEach(function (p) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = p.date;
            var td2 = document.createElement('td'); td2.textContent = p.name;
            var td3 = document.createElement('td'); td3.className = 'text-end text-success'; td3.textContent = fmt(p.amount);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            paidRows.appendChild(tr);
        });
        paidCount.textContent = d.paid.length;
    }

    addBlBtn.addEventListener('click', function () {
        hideError();
        var name = blName.value.trim();
        if (!name) { showError('Enter the bill name.'); return; }
        if (!blDue.value) { showError('Select the due date.'); return; }
        var d = load();
        d.bills.push({
            id: uid(),
            name: name,
            amount: Math.round((Number(blAmt.value) || 0) * 100) / 100,
            due: blDue.value,
            repeat: blRepeat.value,
            paid: false
        });
        save(d);
        blName.value = '';
        blAmt.value = '';
        blDue.value = '';
        render();
    });

    render();
})();
</script>
@endsection
