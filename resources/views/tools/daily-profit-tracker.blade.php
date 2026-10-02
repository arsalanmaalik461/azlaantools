@extends('layouts.app')

@section('title', 'Daily Profit Tracker - Azlaan Tools')
@section('meta_description', 'Track daily profit free online: daily sales, expenses and net profit calculations.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Daily Profit Tracker</h1>
            <p class="lead text-muted">Keep a daily record of your shop or business — sales, expenses, net profit — everything stays saved in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="entryDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="entryDate">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="entrySales" class="form-label fw-semibold">Sales (Rs)</label>
                            <input type="number" class="form-control" id="entrySales" min="0" step="1" placeholder="e.g. 15000">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="entryExpense" class="form-label fw-semibold">Expense (Rs)</label>
                            <input type="number" class="form-control" id="entryExpense" min="0" step="1" placeholder="e.g. 9000">
                        </div>
                        <div class="col-12">
                            <label for="entryNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="entryNote" maxlength="80" placeholder="e.g. Festival day, high sales">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-2 text-center mb-3">
                            <div class="col-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">Total Sales</div>
                                    <div class="fw-bold text-primary" id="totSales">Rs 0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">Total Expense</div>
                                    <div class="fw-bold text-danger" id="totExpense">Rs 0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">Net Profit</div>
                                    <div class="fw-bold text-success" id="totProfit">Rs 0</div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead><tr><th>Date</th><th>Sales</th><th>Expense</th><th>Profit</th><th>Note</th><th></th></tr></thead>
                                <tbody id="entryTable"></tbody>
                            </table>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-success" id="csvBtn">Download CSV</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="clearBtn">Clear All</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the date, today sale and expense; you can also add a note.</li>
                <li>Press Add Entry — the record will be saved and the totals will update.</li>
                <li>See old entries in the table or download them as CSV.</li>
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
    var entryDate = document.getElementById('entryDate');
    var entrySales = document.getElementById('entrySales');
    var entryExpense = document.getElementById('entryExpense');
    var entryNote = document.getElementById('entryNote');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var totSales = document.getElementById('totSales');
    var totExpense = document.getElementById('totExpense');
    var totProfit = document.getElementById('totProfit');
    var entryTable = document.getElementById('entryTable');
    var csvBtn = document.getElementById('csvBtn');
    var clearBtn = document.getElementById('clearBtn');
    var KEY = 'dpt_entries_v1';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function loadEntries() {
        try {
            var raw = localStorage.getItem(KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) { return []; }
    }
    function saveEntries(list) {
        try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) { /* noop */ }
    }
    function fmt(n) { return 'Rs ' + Number(n).toLocaleString('en-PK'); }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function render() {
        var list = loadEntries();
        list.sort(function (a, b) { return b.date < a.date ? -1 : (b.date > a.date ? 1 : 0); });
        var ts = 0, te = 0;
        entryTable.innerHTML = '';
        for (var i = 0; i < list.length; i++) {
            var e = list[i];
            var profit = e.sales - e.expense;
            ts += e.sales; te += e.expense;
            var tr = document.createElement('tr');
            var pc = profit >= 0 ? 'text-success fw-semibold' : 'text-danger fw-semibold';
            tr.innerHTML = '<td>' + esc(e.date) + '</td><td>' + fmt(e.sales) + '</td><td>' +
                fmt(e.expense) + '</td><td class="' + pc + '">' + fmt(profit) + '</td><td>' +
                esc(e.note || '-') + '</td>';
            var delTd = document.createElement('td');
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = 'x';
            delBtn.setAttribute('data-idx', String(i));
            delBtn.addEventListener('click', function () {
                var arr = loadEntries();
                arr.splice(parseInt(this.getAttribute('data-idx'), 10), 1);
                saveEntries(arr);
                render();
            });
            delTd.appendChild(delBtn);
            tr.appendChild(delTd);
            entryTable.appendChild(tr);
        }
        if (list.length === 0) {
            entryTable.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No entries yet. Add your first entry.</td></tr>';
        }
        totSales.textContent = fmt(ts);
        totExpense.textContent = fmt(te);
        var p = ts - te;
        totProfit.textContent = fmt(p);
        totProfit.className = 'fw-bold ' + (p >= 0 ? 'text-success' : 'text-danger');
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var d = entryDate.value;
        var s = parseFloat(entrySales.value);
        var ex = parseFloat(entryExpense.value);
        if (!d) { showError('Please select a date first.'); return; }
        if (isNaN(s) || s < 0) { showError('Please enter a correct sales amount (0 or more).'); return; }
        if (isNaN(ex) || ex < 0) { showError('Please enter a correct expense amount (0 or more).'); return; }
        var list = loadEntries();
        list.push({ date: d, sales: s, expense: ex, note: entryNote.value.trim() });
        saveEntries(list);
        entrySales.value = '';
        entryExpense.value = '';
        entryNote.value = '';
        render();
    });

    csvBtn.addEventListener('click', function () {
        var list = loadEntries();
        var rows = ['Date,Sales,Expense,Profit,Note'];
        for (var i = 0; i < list.length; i++) {
            var e = list[i];
            rows.push(e.date + ',' + e.sales + ',' + e.expense + ',' + (e.sales - e.expense) + ',"' +
                String(e.note || '').replace(/"/g, '""') + '"');
        }
        var blob = new Blob([rows.join('\n')], { type: 'text/csv' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = 'daily-profit.csv';
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
        setTimeout(function () { URL.revokeObjectURL(url); }, 3000);
    });

    clearBtn.addEventListener('click', function () {
        if (confirm('Delete all entries?')) {
            saveEntries([]);
            render();
        }
    });

    if (!entryDate.value) {
        var t = new Date();
        entryDate.value = t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' +
            String(t.getDate()).padStart(2, '0');
    }
    if (loadEntries().length > 0) { render(); }
})();
</script>
@endsection
