@extends('layouts.app')

@section('title', 'Daily Expense Tracker - Azlaan Tools')
@section('meta_description', 'Track your daily spending and see your monthly total. Free — data is saved only in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Daily Expense Tracker</h1>
            <p class="lead text-muted">Enter your daily spending and your monthly total is calculated automatically. Data stays only in your browser (localStorage) — no account or internet needed.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <label for="expDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="expDate">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="expCat" class="form-label fw-semibold">Category</label>
                            <select class="form-control" id="expCat">
                                <option>Food</option>
                                <option>Transport</option>
                                <option>Bills (Electricity/Gas)</option>
                                <option>Shopping</option>
                                <option>Health</option>
                                <option>Education</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="expDesc" class="form-label fw-semibold">Details</label>
                            <input type="text" class="form-control" id="expDesc" placeholder="e.g. vegetables, petrol">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="expAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="expAmt" min="1" placeholder="500">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Add Expense</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-4"><div class="card bg-light"><div class="card-body p-2"><div class="small text-muted">Today</div><div class="fw-bold" id="sumToday">Rs 0</div></div></div></div>
                            <div class="col-4"><div class="card bg-light"><div class="card-body p-2"><div class="small text-muted">This month</div><div class="fw-bold" id="sumMonth">Rs 0</div></div></div></div>
                            <div class="col-4"><div class="card bg-light"><div class="card-body p-2"><div class="small text-muted">Total entries</div><div class="fw-bold" id="sumCount">0</div></div></div></div>
                        </div>
                        <h6>Category-wise (this month)</h6>
                        <div id="catBars" class="mb-3"></div>
                        <h6>Recent entries</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Date</th><th>Category</th><th>Details</th><th class="text-end">Amount</th><th></th></tr></thead>
                                <tbody id="expRows"></tbody>
                            </table>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm" id="csvBtn">CSV Download</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="clearBtn">Delete All Data</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the date, category, details and amount, then press "Add Expense".</li>
                <li>Your spending today, this month's total and the category-wise bars update automatically below.</li>
                <li>Use the CSV button to open your record in Excel. Clearing your browser data deletes the record.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var KEY = 'azlaanExpenseTrackerV1';

    function load() { try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { return []; } }
    function save(a) { try { localStorage.setItem(KEY, JSON.stringify(a)); } catch (e) {} }
    function fmt(n) { return 'Rs ' + n.toLocaleString('en-PK'); }
    function todayStr() { var d = new Date(); return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }

    document.getElementById('expDate').value = todayStr();

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function render() {
        var all = load();
        var t = todayStr();
        var month = t.slice(0, 7);
        var sumT = 0, sumM = 0;
        var cats = {};
        all.forEach(function (e) {
            if (e.date === t) sumT += e.amt;
            if (e.date.slice(0, 7) === month) { sumM += e.amt; cats[e.cat] = (cats[e.cat] || 0) + e.amt; }
        });
        document.getElementById('sumToday').textContent = fmt(sumT);
        document.getElementById('sumMonth').textContent = fmt(sumM);
        document.getElementById('sumCount').textContent = all.length;

        var cb = document.getElementById('catBars');
        cb.innerHTML = '';
        var maxC = 0;
        Object.keys(cats).forEach(function (k) { if (cats[k] > maxC) maxC = cats[k]; });
        var keys = Object.keys(cats).sort(function (a, b) { return cats[b] - cats[a]; });
        if (!keys.length) cb.innerHTML = '<p class="text-muted small">No entries this month yet.</p>';
        keys.forEach(function (k) {
            var pct = maxC ? Math.round(cats[k] / maxC * 100) : 0;
            var row = document.createElement('div');
            row.className = 'mb-2';
            row.innerHTML = '<div class="d-flex justify-content-between small"><span>' + k + '</span><span class="fw-semibold">' + fmt(cats[k]) + '</span></div>' +
                '<div class="progress" style="height:10px"><div class="progress-bar bg-primary" style="width:' + pct + '%"></div></div>';
            cb.appendChild(row);
        });

        var tbody = document.getElementById('expRows');
        tbody.innerHTML = '';
        var sorted = all.slice().sort(function (a, b) { return b.ts - a.ts; }).slice(0, 50);
        if (!sorted.length) tbody.innerHTML = '<tr><td colspan="5" class="text-muted small">No entries yet — add one above.</td></tr>';
        sorted.forEach(function (e, idx) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td class="small">' + e.date + '</td><td class="small">' + e.cat + '</td><td class="small">' + e.desc + '</td>' +
                '<td class="text-end small">' + fmt(e.amt) + '</td>';
            var td = document.createElement('td');
            var b = document.createElement('button');
            b.type = 'button'; b.className = 'btn btn-sm btn-outline-danger py-0'; b.textContent = '×';
            b.addEventListener('click', function () {
                var a2 = load();
                var gi = all.indexOf(e);
                a2.splice(gi, 1);
                save(a2); render();
            });
            td.appendChild(b); tr.appendChild(td);
            tbody.appendChild(tr);
        });
    }

    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

    goBtn.addEventListener('click', function () {
        hideError();
        var date = document.getElementById('expDate').value;
        var cat = document.getElementById('expCat').value;
        var desc = document.getElementById('expDesc').value.trim();
        var amt = parseFloat(document.getElementById('expAmt').value);
        if (!date) { showError('Please select a date.'); return; }
        if (!desc) { showError('Please enter the details (e.g. vegetables, petrol).'); return; }
        if (isNaN(amt) || amt <= 0) { showError('Please enter a correct amount (more than 0).'); return; }
        var all = load();
        all.push({ date: date, cat: cat, desc: esc(desc), amt: Math.round(amt), ts: Date.now() });
        save(all);
        document.getElementById('expDesc').value = '';
        document.getElementById('expAmt').value = '';
        render();
    });

    document.getElementById('csvBtn').addEventListener('click', function () {
        var all = load();
        if (!all.length) { showError('Add an entry first before downloading a CSV.'); return; }
        hideError();
        var csv = 'Date,Category,Description,Amount (Rs)\n';
        all.forEach(function (e) { csv += e.date + ',' + '"' + e.cat + '","' + e.desc + '",' + e.amt + '\n'; });
        var blob = new Blob([csv], { type: 'text/csv' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'expenses.csv';
        document.body.appendChild(a); a.click(); a.remove();
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        if (confirm('All expense data will be deleted. Are you sure?')) { save([]); render(); }
    });

    render();
})();
</script>
@endsection
