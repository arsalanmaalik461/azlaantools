@extends('layouts.app')

@section('title', 'Payables Aging Report - Azlaan Tools')
@section('meta_description', 'Supplier dues aging report — see the amount owed to suppliers in 0-30, 31-60, 61-90 and 90+ day buckets.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Payables Aging Report</h1>
            <p class="lead text-muted">An overdue-bucket report of the amount owed to suppliers — see how long each amount has been pending, all at a glance. Data is saved only in your browser; nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add a new payable</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="paSupplier" class="form-label fw-semibold">Supplier name</label>
                            <input type="text" class="form-control" id="paSupplier" placeholder="e.g. Ali Traders">
                        </div>
                        <div class="col-md-4">
                            <label for="paAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="paAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="paDate" class="form-label fw-semibold">Date (bill / due date)</label>
                            <input type="date" class="form-control" id="paDate">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="paAddBtn">Add Payable</button>
                    <div class="alert alert-danger mt-3 d-none" id="paError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Aging buckets</h2>
                    <div class="row text-center g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-3">
                                <div class="small text-muted fw-semibold">0 – 30 days</div>
                                <div class="fw-bold" id="paB0">Rs 0</div>
                                <div class="small text-muted" id="paC0">0 entries</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-3">
                                <div class="small text-muted fw-semibold">31 – 60 days</div>
                                <div class="fw-bold" id="paB1">Rs 0</div>
                                <div class="small text-muted" id="paC1">0 entries</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-3">
                                <div class="small text-muted fw-semibold">61 – 90 days</div>
                                <div class="fw-bold text-warning" id="paB2">Rs 0</div>
                                <div class="small text-muted" id="paC2">0 entries</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-3">
                                <div class="small text-muted fw-semibold">90+ days</div>
                                <div class="fw-bold text-danger" id="paB3">Rs 0</div>
                                <div class="small text-muted" id="paC3">0 entries</div>
                            </div></div>
                        </div>
                    </div>
                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-0" role="alert">
                        <span class="fw-semibold">Total payables (grand total)</span>
                        <span class="fw-bold fs-5" id="paGrand">Rs 0</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">All payables</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Supplier</th><th>Date</th><th>Age (days)</th><th>Bucket</th><th class="text-end">Amount (Rs)</th><th></th></tr>
                            </thead>
                            <tbody id="paRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="paEmpty">No payables yet. Add an entry above.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-success" id="paCsvBtn">Download CSV</button>
                        <button type="button" class="btn btn-outline-danger" id="paClearBtn">Clear all data</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the supplier name, amount and bill date, then press <strong>Add Payable</strong>.</li>
                <li>The amount goes automatically into the <strong>0–30 / 31–60 / 61–90 / 90+ day</strong> buckets, counted from today.</li>
                <li>Contact the supplier right away about amounts in the 90+ day bucket — those payments are the most overdue.</li>
            </ol>
            <p class="small text-muted">Note: data is saved only in this browser; nothing is uploaded. This is a simplified information report, not audit-grade accounting.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_pay_aging';
    var supplierEl = document.getElementById('paSupplier');
    var amountEl = document.getElementById('paAmount');
    var dateEl = document.getElementById('paDate');
    var addBtn = document.getElementById('paAddBtn');
    var errorBox = document.getElementById('paError');
    var rowsEl = document.getElementById('paRows');
    var emptyEl = document.getElementById('paEmpty');
    var grandEl = document.getElementById('paGrand');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var parsed = JSON.parse(raw);
                if (parsed && parsed.entries) return parsed;
            }
        } catch (e) {}
        return { entries: [] };
    }
    function save(data) {
        try { localStorage.setItem(KEY, JSON.stringify(data)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function uid() {
        return 'p' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function ageDays(dateStr) {
        var today = new Date(todayStr() + 'T00:00:00');
        var dt = new Date(dateStr + 'T00:00:00');
        var diff = Math.floor((today.getTime() - dt.getTime()) / 86400000);
        return diff < 0 ? 0 : diff;
    }
    function bucketIdx(age) {
        if (age <= 30) return 0;
        if (age <= 60) return 1;
        if (age <= 90) return 2;
        return 3;
    }
    var bucketNames = ['0–30 days', '31–60 days', '61–90 days', '90+ days'];

    function render() {
        hideError();
        var data = load();
        var totals = [0, 0, 0, 0];
        var counts = [0, 0, 0, 0];
        var grand = 0;
        data.entries.forEach(function (e) {
            var age = ageDays(e.date);
            var b = bucketIdx(age);
            totals[b] += e.amount;
            counts[b] += 1;
            grand += e.amount;
        });
        document.getElementById('paB0').textContent = fmt(totals[0]);
        document.getElementById('paB1').textContent = fmt(totals[1]);
        document.getElementById('paB2').textContent = fmt(totals[2]);
        document.getElementById('paB3').textContent = fmt(totals[3]);
        document.getElementById('paC0').textContent = counts[0] + ' entries';
        document.getElementById('paC1').textContent = counts[1] + ' entries';
        document.getElementById('paC2').textContent = counts[2] + ' entries';
        document.getElementById('paC3').textContent = counts[3] + ' entries';
        grandEl.textContent = fmt(grand);
        emptyEl.style.display = data.entries.length ? 'none' : '';

        var sorted = data.entries.slice().sort(function (a, b) {
            var da = ageDays(a.date), db = ageDays(b.date);
            if (da !== db) return db - da;
            return b.id < a.id ? -1 : 1;
        });
        rowsEl.innerHTML = '';
        sorted.forEach(function (e) {
            var age = ageDays(e.date);
            var b = bucketIdx(age);
            var tr = document.createElement('tr');
            var badgeCls = b === 0 ? 'bg-success' : (b === 1 ? 'bg-info' : (b === 2 ? 'bg-warning text-dark' : 'bg-danger'));
            tr.innerHTML = '<td>' + esc(e.supplier) + '</td>' +
                '<td>' + esc(e.date) + '</td>' +
                '<td>' + age + '</td>' +
                '<td><span class="badge ' + badgeCls + '">' + bucketNames[b] + '</span></td>' +
                '<td class="text-end">' + fmt(e.amount) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger del-btn" data-id="' + e.id + '" aria-label="Delete">×</button></td>';
            rowsEl.appendChild(tr);
        });
        rowsEl.querySelectorAll('.del-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var d = load();
                var id = btn.getAttribute('data-id');
                d.entries = d.entries.filter(function (e) { return e.id !== id; });
                save(d);
                render();
            });
        });
    }

    addBtn.addEventListener('click', function () {
        hideError();
        var supplier = supplierEl.value.trim();
        if (!supplier) { showError('Enter the supplier name.'); return; }
        var amount = Number(amountEl.value);
        if (!amount || amount <= 0) { showError('Enter an amount greater than 0.'); return; }
        if (!dateEl.value) { showError('Pick the bill date.'); return; }
        var data = load();
        data.entries.push({
            id: uid(),
            supplier: supplier,
            amount: Math.round(amount * 100) / 100,
            date: dateEl.value
        });
        save(data);
        supplierEl.value = '';
        amountEl.value = '';
        render();
    });

    document.getElementById('paCsvBtn').addEventListener('click', function () {
        hideError();
        var data = load();
        if (!data.entries.length) { showError('Add an entry first to download the CSV.'); return; }
        var rows = ['Supplier,Date,AgeDays,Bucket,Amount'];
        data.entries.forEach(function (e) {
            var age = ageDays(e.date);
            rows.push('"' + e.supplier.replace(/"/g, '""') + '",' + e.date + ',' + age + ',' + bucketNames[bucketIdx(age)] + ',' + e.amount);
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'payables-aging-report.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    document.getElementById('paClearBtn').addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all payables data?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        render();
    });

    dateEl.value = todayStr();
    render();
})();
</script>
@endsection
