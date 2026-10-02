@extends('layouts.app')

@section('title', 'Daily Cash Book - Azlaan Tools')
@section('meta_description', 'Track daily shop cash: opening balance, sales, expenses and closing balance with a free online daily cash book.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Daily Cash Book</h1>
            <p class="lead text-muted">Your daily cash record in one place — opening, sales, expenses and closing balance. Your data is saved in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="cbDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="cbDate">
                        </div>
                        <div class="col-md-6">
                            <label for="cbOpening" class="form-label fw-semibold">Opening balance (Rs)</label>
                            <input type="number" class="form-control" id="cbOpening" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>

                    <hr>
                    <h2 class="h5 mb-3">Add a new entry</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="cbType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="cbType">
                                <option value="in">Income / Sale (Cash in)</option>
                                <option value="out">Expense (Cash out)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="cbNote" class="form-label fw-semibold">Description (note)</label>
                            <input type="text" class="form-control" id="cbNote" placeholder="e.g. Morning sale, tea expense">
                        </div>
                        <div class="col-md-3">
                            <label for="cbAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="cbAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total In</small><div class="fw-bold text-success" id="cbTotalIn">0</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total Out</small><div class="fw-bold text-danger" id="cbTotalOut">0</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Closing</small><div class="fw-bold" id="cbClosing">0</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Entries</small><div class="fw-bold" id="cbCount">0</div></div></div></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr><th>#</th><th>Time</th><th>Type</th><th>Description</th><th class="text-end">Amount (Rs)</th><th></th></tr>
                                </thead>
                                <tbody id="cbTable"></tbody>
                            </table>
                        </div>
                        <p class="small text-muted" id="cbEmpty">No entries yet. Add an entry above.</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-success" id="cbCsv">CSV Download</button>
                            <button type="button" class="btn btn-outline-danger" id="cbClear">Clear this date</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the <strong>date</strong> and morning <strong>opening balance</strong> (closing is saved automatically if you select the same date).</li>
                <li>Press <strong>Add Entry</strong> for every sale or expense.</li>
                <li><strong>Closing balance</strong> is always calculated live: Opening + In − Out.</li>
                <li>At the end of the day, <strong>download the CSV</strong> to keep your record.</li>
            </ol>
            <p class="small text-muted">This record stays only in your own browser — nothing goes to a server. This is a finance record, not tax advice.</p>
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
    var results = document.getElementById('results');
    var dateEl = document.getElementById('cbDate');
    var openingEl = document.getElementById('cbOpening');
    var typeEl = document.getElementById('cbType');
    var noteEl = document.getElementById('cbNote');
    var amountEl = document.getElementById('cbAmount');
    var tableEl = document.getElementById('cbTable');

    function key() { return 'cashbook_' + dateEl.value; }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function load() {
        try {
            var raw = localStorage.getItem(key());
            if (raw) return JSON.parse(raw);
        } catch (e) {}
        return { opening: 0, entries: [] };
    }
    function save(data) {
        try { localStorage.setItem(key(), JSON.stringify(data)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function fmt(n) {
        return Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function render() {
        var data = load();
        var opening = Number(openingEl.value) || data.opening || 0;
        var totalIn = 0, totalOut = 0;
        data.entries.forEach(function (e) {
            if (e.type === 'in') totalIn += e.amount; else totalOut += e.amount;
        });
        var closing = opening + totalIn - totalOut;
        document.getElementById('cbTotalIn').textContent = 'Rs ' + fmt(totalIn);
        document.getElementById('cbTotalOut').textContent = 'Rs ' + fmt(totalOut);
        var closingEl = document.getElementById('cbClosing');
        closingEl.textContent = 'Rs ' + fmt(closing);
        closingEl.classList.remove('text-danger');
        if (closing < 0) closingEl.classList.add('text-danger');
        document.getElementById('cbCount').textContent = data.entries.length;
        document.getElementById('cbEmpty').style.display = data.entries.length ? 'none' : '';
        var html = '';
        data.entries.forEach(function (e, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(e.time) + '</td>' +
                '<td><span class="badge ' + (e.type === 'in' ? 'bg-success' : 'bg-danger') + '">' +
                (e.type === 'in' ? 'In' : 'Out') + '</span></td><td>' + esc(e.note || '—') + '</td>' +
                '<td class="text-end">' + fmt(e.amount) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger del-btn" data-idx="' + i + '">×</button></td></tr>';
        });
        tableEl.innerHTML = html;
        var dels = tableEl.querySelectorAll('.del-btn');
        dels.forEach(function (b) {
            b.addEventListener('click', function () {
                var d = load();
                d.entries.splice(Number(b.getAttribute('data-idx')), 1);
                save(d); render();
            });
        });
        return data;
    }
    function today() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }

    dateEl.value = today();
    var initial = load();
    if (initial.opening) openingEl.value = initial.opening;

    dateEl.addEventListener('change', function () {
        hideError();
        var d = load();
        openingEl.value = d.opening || '';
        render();
    });
    openingEl.addEventListener('change', function () {
        hideError();
        var d = load();
        d.opening = Number(openingEl.value) || 0;
        save(d); render();
    });

    goBtn.addEventListener('click', function () {
        hideError();
        if (!dateEl.value) { showError('Please select a date first.'); return; }
        var amount = Number(amountEl.value);
        if (!amount || amount <= 0) { showError('Please enter an amount greater than 0.'); return; }
        var d = load();
        d.opening = Number(openingEl.value) || d.opening || 0;
        var now = new Date();
        d.entries.push({
            type: typeEl.value,
            note: noteEl.value.trim(),
            amount: Math.round(amount * 100) / 100,
            time: ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2)
        });
        save(d);
        noteEl.value = '';
        amountEl.value = '';
        render();
    });

    document.getElementById('cbCsv').addEventListener('click', function () {
        hideError();
        var d = load();
        if (!d.entries.length) { showError('Please add an entry before downloading CSV.'); return; }
        var rows = ['Date,Opening,Type,Time,Note,Amount'];
        d.entries.forEach(function (e) {
            var note = '"' + String(e.note || '').replace(/"/g, '""') + '"';
            rows.push([dateEl.value, d.opening || 0, e.type, e.time, note, e.amount].join(','));
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'cash-book-' + dateEl.value + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
    document.getElementById('cbClear').addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all data for this date?')) return;
        try { localStorage.removeItem(key()); } catch (e) {}
        openingEl.value = '';
        render();
    });

    render();
})();
</script>
@endsection
