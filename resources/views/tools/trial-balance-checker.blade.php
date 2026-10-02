@extends('layouts.app')

@section('title', 'Trial Balance Checker - Azlaan Tools')
@section('meta_description', 'Match debit and credit of all accounts — check instantly if your books are balanced.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Trial Balance Checker</h1>
            <p class="lead text-muted">Write your journal entries — total debit and total credit are matched for you. You will know instantly if your books balance. Data is saved only in your browser, it is not uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">New journal entry</h2>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="tbDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="tbDate">
                        </div>
                        <div class="col-md-5">
                            <label for="tbAccount" class="form-label fw-semibold">Account name</label>
                            <input type="text" class="form-control" id="tbAccount" placeholder="e.g. Cash, Sales, Rent">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="tbDebit" class="form-label fw-semibold">Debit (Rs)</label>
                            <input type="number" class="form-control" id="tbDebit" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="tbCredit" class="form-label fw-semibold">Credit (Rs)</label>
                            <input type="number" class="form-control" id="tbCredit" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="tbAddBtn">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="tbError" role="alert"></div>
                    <p class="small text-muted mb-0 mt-2">In each entry, write an amount in only one of <strong>Debit or Credit</strong> (keep the other empty).</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Trial balance</h2>
                    <div class="row text-center g-2 mb-3">
                        <div class="col-6">
                            <div class="card bg-light"><div class="card-body py-3">
                                <div class="small text-muted fw-semibold">Total Debit</div>
                                <div class="fw-bold fs-5 text-primary" id="tbDebitTotal">Rs 0</div>
                            </div></div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-light"><div class="card-body py-3">
                                <div class="small text-muted fw-semibold">Total Credit</div>
                                <div class="fw-bold fs-5 text-primary" id="tbCreditTotal">Rs 0</div>
                            </div></div>
                        </div>
                    </div>
                    <div class="alert d-flex justify-content-between align-items-center" id="tbVerdictBox" role="alert">
                        <span class="fw-semibold" id="tbVerdict">No entries yet.</span>
                        <span class="fw-bold" id="tbDiff"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Date</th><th>Account</th><th class="text-end">Debit (Rs)</th><th class="text-end">Credit (Rs)</th><th></th></tr>
                            </thead>
                            <tbody id="tbRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="tbEmpty">No entries yet. Add an entry from above.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-success" id="tbCsvBtn">CSV Download</button>
                        <button type="button" class="btn btn-outline-danger" id="tbClearBtn">Clear all entries</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the date, the account name, and an amount in one of <strong>Debit or Credit</strong>.</li>
                <li>Keep pressing <strong>Add Entry</strong> — totals update on their own.</li>
                <li>At the end, <strong>Total Debit = Total Credit</strong> should hold. If there is a difference, you will see it — then check your entries.</li>
            </ol>
            <p class="small text-muted">Note: data is saved only in this browser, it is not uploaded anywhere. This is a simplified information tool — not audit-grade accounting.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_journal';
    var dateEl = document.getElementById('tbDate');
    var accountEl = document.getElementById('tbAccount');
    var debitEl = document.getElementById('tbDebit');
    var creditEl = document.getElementById('tbCredit');
    var addBtn = document.getElementById('tbAddBtn');
    var errorBox = document.getElementById('tbError');
    var rowsEl = document.getElementById('tbRows');
    var emptyEl = document.getElementById('tbEmpty');
    var verdictBox = document.getElementById('tbVerdictBox');
    var verdictEl = document.getElementById('tbVerdict');
    var diffEl = document.getElementById('tbDiff');

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
        return 'j' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function round2(n) {
        return Math.round(Number(n) * 100) / 100;
    }

    function render() {
        hideError();
        var data = load();
        var totD = 0, totC = 0;
        data.entries.forEach(function (e) {
            totD += e.debit;
            totC += e.credit;
        });
        totD = round2(totD);
        totC = round2(totC);
        document.getElementById('tbDebitTotal').textContent = fmt(totD);
        document.getElementById('tbCreditTotal').textContent = fmt(totC);

        verdictBox.classList.remove('alert-success', 'alert-danger', 'alert-secondary');
        if (!data.entries.length) {
            verdictBox.classList.add('alert-secondary');
            verdictEl.textContent = 'No entries yet.';
            diffEl.textContent = '';
        } else {
            var diff = round2(totD - totC);
            if (diff === 0) {
                verdictBox.classList.add('alert-success');
                verdictEl.textContent = 'Balanced! Total Debit = Total Credit — your books match.';
                diffEl.textContent = '';
            } else {
                verdictBox.classList.add('alert-danger');
                verdictEl.textContent = 'Not balanced — there is a difference in your books.';
                diffEl.textContent = 'Difference: ' + fmt(Math.abs(diff));
            }
        }

        emptyEl.style.display = data.entries.length ? 'none' : '';
        rowsEl.innerHTML = '';
        data.entries.forEach(function (e) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(e.date) + '</td>' +
                '<td>' + esc(e.account) + '</td>' +
                '<td class="text-end">' + (e.debit ? fmt(e.debit) : '—') + '</td>' +
                '<td class="text-end">' + (e.credit ? fmt(e.credit) : '—') + '</td>' +
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
        var account = accountEl.value.trim();
        if (!account) { showError('Enter the account name.'); return; }
        if (!dateEl.value) { showError('Pick a date.'); return; }
        var d = Number(debitEl.value) || 0;
        var c = Number(creditEl.value) || 0;
        if (d > 0 && c > 0) { showError('Enter an amount in only Debit OR Credit, not both.'); return; }
        if (d <= 0 && c <= 0) { showError('Enter an amount in Debit or Credit.'); return; }
        var data = load();
        data.entries.push({
            id: uid(),
            date: dateEl.value,
            account: account,
            debit: round2(d),
            credit: round2(c)
        });
        save(data);
        accountEl.value = '';
        debitEl.value = '';
        creditEl.value = '';
        render();
    });

    document.getElementById('tbCsvBtn').addEventListener('click', function () {
        hideError();
        var data = load();
        if (!data.entries.length) { showError('Add an entry first for the CSV.'); return; }
        var rows = ['Date,Account,Debit,Credit'];
        data.entries.forEach(function (e) {
            rows.push(e.date + ',"' + e.account.replace(/"/g, '""') + '",' + e.debit + ',' + e.credit);
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'trial-balance.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    document.getElementById('tbClearBtn').addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all journal entries?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        render();
    });

    dateEl.value = todayStr();
    render();
})();
</script>
@endsection
