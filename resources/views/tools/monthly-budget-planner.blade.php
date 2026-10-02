@extends('layouts.app')

@section('title', 'Monthly Budget Planner - Azlaan Tools')
@section('meta_description', 'Free monthly budget planner: set spending limits per category with progress bars and overspend alerts.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Monthly Budget Planner</h1>
            <p class="lead text-muted">Set a spending limit for each category — with a progress bar and overspend alert. Data is saved only in your browser, it is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="bpMonth" class="form-label fw-semibold">Month</label>
                            <input type="month" class="form-control" id="bpMonth">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control" id="newCatName" placeholder="New category (example: Groceries)">
                                <input type="number" class="form-control" id="newCatLimit" placeholder="Limit Rs" min="0" step="0.01" style="max-width:140px;">
                                <button type="button" class="btn btn-primary" id="addCatBtn">Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="bpError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add expense entry</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="expCat" class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="expCat"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="expAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="expAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="expNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="expNote" placeholder="Example: electricity bill">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="addExpBtn">Add Expense</button>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">This month's budget</h2>
                        <span class="badge bg-secondary" id="monthBadge"></span>
                    </div>
                    <div id="catCards"></div>
                    <p class="text-muted small mb-0" id="bpEmpty">No categories yet. Add a category above.</p>
                    <div class="row text-center g-2 mt-3">
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Total Budget</div><div class="fw-bold" id="totBudget">Rs 0</div></div></div>
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Total Spent</div><div class="fw-bold text-danger" id="totSpent">Rs 0</div></div></div>
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Remaining</div><div class="fw-bold text-success" id="totLeft">Rs 0</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Entries</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light"><tr><th>Category</th><th>Note</th><th class="text-end">Amount</th><th></th></tr></thead>
                            <tbody id="expRows"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="clearMonthBtn">Clear this month's data</button>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the month and add a <strong>budget limit</strong> for each category (Groceries, Rent, Electricity bill…).</li>
                <li>When you spend, choose the category and add the amount — the <strong>progress bar</strong> will update itself.</li>
                <li>When the limit is crossed, the bar will turn <strong>red</strong> and an overspend alert will appear.</li>
            </ol>
            <p class="text-muted small">Note: your data stays in this browser only. If you clear browser data, these records will be deleted — keep a backup.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_budget';
    var bpMonth = document.getElementById('bpMonth');
    var newCatName = document.getElementById('newCatName');
    var newCatLimit = document.getElementById('newCatLimit');
    var addCatBtn = document.getElementById('addCatBtn');
    var bpError = document.getElementById('bpError');
    var expCat = document.getElementById('expCat');
    var expAmt = document.getElementById('expAmt');
    var expNote = document.getElementById('expNote');
    var addExpBtn = document.getElementById('addExpBtn');
    var catCards = document.getElementById('catCards');
    var bpEmpty = document.getElementById('bpEmpty');
    var monthBadge = document.getElementById('monthBadge');
    var totBudget = document.getElementById('totBudget');
    var totSpent = document.getElementById('totSpent');
    var totLeft = document.getElementById('totLeft');
    var expRows = document.getElementById('expRows');
    var clearMonthBtn = document.getElementById('clearMonthBtn');

    function curMonth() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2);
    }
    function loadAll() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) return JSON.parse(raw);
        } catch (e) {}
        return { months: {} };
    }
    function saveAll(all) {
        try { localStorage.setItem(KEY, JSON.stringify(all)); } catch (e) {}
    }
    function getMonthData(all) {
        var m = bpMonth.value || curMonth();
        if (!all.months[m]) all.months[m] = { cats: [], expenses: [] };
        return all.months[m];
    }
    function showError(msg) {
        bpError.textContent = msg;
        bpError.classList.remove('d-none');
    }
    function hideError() { bpError.classList.add('d-none'); bpError.textContent = ''; }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function uid() { return 'x' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36); }

    function render() {
        hideError();
        var all = loadAll();
        var md = getMonthData(all);
        monthBadge.textContent = bpMonth.value || curMonth();

        expCat.innerHTML = '';
        md.cats.forEach(function (c) {
            var o = document.createElement('option');
            o.value = c.id;
            o.textContent = c.name + ' (limit ' + fmt(c.limit) + ')';
            expCat.appendChild(o);
        });

        var totalLimit = 0, totalSpent = 0;
        catCards.innerHTML = '';
        md.cats.forEach(function (c) {
            var spent = 0;
            md.expenses.forEach(function (e) { if (e.catId === c.id) spent += e.amount; });
            totalLimit += c.limit;
            totalSpent += spent;
            var pct = c.limit > 0 ? Math.min(100, Math.round(spent / c.limit * 100)) : (spent > 0 ? 100 : 0);
            var over = c.limit > 0 && spent > c.limit;
            var barClass = over ? 'bg-danger' : (pct >= 80 ? 'bg-warning' : 'bg-success');

            var wrap = document.createElement('div');
            wrap.className = 'mb-3';
            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-center mb-1';
            var nameSpan = document.createElement('span');
            nameSpan.className = 'fw-semibold';
            nameSpan.textContent = c.name;
            var amtSpan = document.createElement('span');
            amtSpan.className = 'small ' + (over ? 'text-danger fw-bold' : 'text-muted');
            amtSpan.textContent = fmt(spent) + ' / ' + fmt(c.limit) + (over ? ' — LIMIT CROSSED!' : '');
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger ms-2';
            delBtn.textContent = '×';
            delBtn.setAttribute('aria-label', 'Delete category');
            delBtn.addEventListener('click', function () {
                if (!confirm('Delete the "' + c.name + '" category and its entries?')) return;
                var a2 = loadAll();
                var m2 = getMonthData(a2);
                m2.cats = m2.cats.filter(function (x) { return x.id !== c.id; });
                m2.expenses = m2.expenses.filter(function (x) { return x.catId !== c.id; });
                saveAll(a2);
                render();
            });
            var left = document.createElement('div');
            left.appendChild(nameSpan);
            left.appendChild(delBtn);
            head.appendChild(left);
            head.appendChild(amtSpan);
            wrap.appendChild(head);
            var prog = document.createElement('div');
            prog.className = 'progress';
            prog.style.height = '18px';
            var bar = document.createElement('div');
            bar.className = 'progress-bar ' + barClass;
            bar.setAttribute('role', 'progressbar');
            bar.style.width = pct + '%';
            bar.textContent = pct + '%';
            prog.appendChild(bar);
            wrap.appendChild(prog);
            catCards.appendChild(wrap);
        });
        bpEmpty.style.display = md.cats.length ? 'none' : '';

        totBudget.textContent = fmt(totalLimit);
        totSpent.textContent = fmt(totalSpent);
        var left2 = totalLimit - totalSpent;
        totLeft.textContent = fmt(left2);
        totLeft.className = 'fw-bold ' + (left2 < 0 ? 'text-danger' : 'text-success');

        expRows.innerHTML = '';
        var sorted = md.expenses.slice().reverse();
        sorted.forEach(function (e) {
            var c = null;
            md.cats.forEach(function (x) { if (x.id === e.catId) c = x; });
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = c ? c.name : '(deleted)';
            var td2 = document.createElement('td'); td2.textContent = e.note || '—';
            var td3 = document.createElement('td'); td3.className = 'text-end text-danger'; td3.textContent = fmt(e.amount);
            var td4 = document.createElement('td'); td4.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = '×';
            del.addEventListener('click', function () {
                var a2 = loadAll();
                var m2 = getMonthData(a2);
                m2.expenses = m2.expenses.filter(function (x) { return x.id !== e.id; });
                saveAll(a2);
                render();
            });
            td4.appendChild(del);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4);
            expRows.appendChild(tr);
        });
    }

    addCatBtn.addEventListener('click', function () {
        hideError();
        var name = newCatName.value.trim();
        var limit = Number(newCatLimit.value);
        if (!name) { showError('Enter a category name.'); return; }
        if (isNaN(limit) || limit < 0) { showError('Enter a correct limit amount.'); return; }
        var all = loadAll();
        var md = getMonthData(all);
        md.cats.push({ id: uid(), name: name, limit: Math.round(limit * 100) / 100 });
        saveAll(all);
        newCatName.value = '';
        newCatLimit.value = '';
        render();
    });

    addExpBtn.addEventListener('click', function () {
        hideError();
        var all = loadAll();
        var md = getMonthData(all);
        if (!md.cats.length) { showError('Add a category first.'); return; }
        var amt = Number(expAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Enter an amount greater than 0.'); return; }
        md.expenses.push({
            id: uid(),
            catId: expCat.value,
            amount: Math.round(amt * 100) / 100,
            note: expNote.value.trim(),
            at: new Date().toISOString()
        });
        saveAll(all);
        expAmt.value = '';
        expNote.value = '';
        render();
    });

    clearMonthBtn.addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all budget data for this month?')) return;
        var all = loadAll();
        delete all.months[bpMonth.value || curMonth()];
        saveAll(all);
        render();
    });

    bpMonth.value = curMonth();
    bpMonth.addEventListener('change', render);
    render();
})();
</script>
@endsection
