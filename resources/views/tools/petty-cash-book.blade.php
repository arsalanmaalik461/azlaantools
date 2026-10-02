@extends('layouts.app')

@section('title', 'Petty Cash Book - Azlaan Tools')
@section('meta_description', 'Track small daily expenses — write down each day\'s spending and see the total. Free online petty cash book.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Petty Cash Book</h1>
            <p class="lead text-muted">Write down the small expenses of your shop or office (tea, petrol, photocopy...) every day. Cash in, cash out and the remaining balance are calculated automatically. Your data stays saved in your browser.</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">New entry</h2>
                            <div class="mb-3">
                                <label for="entryDate" class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control" id="entryDate">
                            </div>
                            <div class="mb-3">
                                <label for="entryDesc" class="form-label fw-semibold">Details</label>
                                <input type="text" class="form-control" id="entryDesc" placeholder="for example: Tea, Petrol, Photocopy">
                            </div>
                            <div class="mb-3">
                                <label for="entryType" class="form-label fw-semibold">Type</label>
                                <select class="form-select" id="entryType">
                                    <option value="out">Expense (money out)</option>
                                    <option value="in">Received (money in)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="entryAmt" class="form-label fw-semibold">Amount (Rs)</label>
                                <input type="number" class="form-control" id="entryAmt" min="1" step="1" placeholder="for example: 250">
                            </div>
                            <div class="mb-3">
                                <label for="openBal" class="form-label fw-semibold">Opening balance (Rs)</label>
                                <input type="number" class="form-control" id="openBal" min="0" step="1" placeholder="0">
                                <div class="form-text">How much cash you had in hand before writing the first expense.</div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="goBtn">Add Entry</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <div class="card text-center shadow-sm"><div class="card-body py-3">
                                <div class="text-muted small">Total In</div>
                                <div class="h5 mb-0 text-success" id="totalIn">Rs 0</div>
                            </div></div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center shadow-sm"><div class="card-body py-3">
                                <div class="text-muted small">Total Out</div>
                                <div class="h5 mb-0 text-danger" id="totalOut">Rs 0</div>
                            </div></div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center shadow-sm"><div class="card-body py-3">
                                <div class="text-muted small">Remaining Balance</div>
                                <div class="h5 mb-0 text-primary" id="balance">Rs 0</div>
                            </div></div>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">Ledger</h2>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="csvBtn">CSV Download</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearBtn">Clear All</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm align-middle">
                                    <thead><tr><th>Date</th><th>Details</th><th>In</th><th>Out</th><th></th></tr></thead>
                                    <tbody id="entryRows"><tr><td colspan="5" class="text-center text-muted">No entries yet.</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="results" class="d-none"></div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the opening balance (if you already have some cash).</li>
                <li>Add an entry for every expense or income — with date, details and amount.</li>
                <li>The total and remaining balance update automatically in the cards above. Download the CSV to keep a safe record.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan_petty_cash_v1';
    var entryDate = document.getElementById('entryDate');
    var entryDesc = document.getElementById('entryDesc');
    var entryType = document.getElementById('entryType');
    var entryAmt = document.getElementById('entryAmt');
    var openBal = document.getElementById('openBal');
    var goBtn = document.getElementById('goBtn');
    var csvBtn = document.getElementById('csvBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var entryRows = document.getElementById('entryRows');
    var totalInEl = document.getElementById('totalIn');
    var totalOutEl = document.getElementById('totalOut');
    var balanceEl = document.getElementById('balance');

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
            if (raw) return JSON.parse(raw);
        } catch (e) {}
        return { opening: 0, entries: [] };
    }
    function save(data) {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK'); }
    function today() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    var data = load();
    entryDate.value = today();
    openBal.value = data.opening || '';

    function render() {
        entryRows.innerHTML = '';
        var tIn = 0, tOut = 0;
        if (!data.entries.length) {
            var tr0 = document.createElement('tr');
            var td0 = document.createElement('td');
            td0.colSpan = 5;
            td0.className = 'text-center text-muted';
            td0.textContent = 'No entries yet.';
            tr0.appendChild(td0);
            entryRows.appendChild(tr0);
        }
        data.entries.forEach(function (en, idx) {
            if (en.type === 'in') tIn += en.amt; else tOut += en.amt;
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.textContent = en.date;
            var tdT = document.createElement('td'); tdT.textContent = en.desc;
            var tdI = document.createElement('td'); tdI.className = 'text-success'; tdI.textContent = en.type === 'in' ? fmt(en.amt) : '-';
            var tdO = document.createElement('td'); tdO.className = 'text-danger'; tdO.textContent = en.type === 'out' ? fmt(en.amt) : '-';
            var tdX = document.createElement('td');
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = 'X';
            del.setAttribute('aria-label', 'Delete entry');
            del.addEventListener('click', function () {
                data.entries.splice(idx, 1);
                save(data);
                render();
            });
            tdX.appendChild(del);
            tr.appendChild(tdD); tr.appendChild(tdT); tr.appendChild(tdI); tr.appendChild(tdO); tr.appendChild(tdX);
            entryRows.appendChild(tr);
        });
        totalInEl.textContent = fmt(tIn);
        totalOutEl.textContent = fmt(tOut);
        var bal = (Number(data.opening) || 0) + tIn - tOut;
        balanceEl.textContent = fmt(bal);
        balanceEl.className = 'h5 mb-0 ' + (bal < 0 ? 'text-danger' : 'text-primary');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var desc = entryDesc.value.trim();
        var amt = parseFloat(entryAmt.value);
        var dt = entryDate.value || today();
        if (!desc) { showError('Please enter a description.'); return; }
        if (!(amt > 0)) { showError('Amount must be greater than 0.'); return; }
        data.opening = parseFloat(openBal.value) || 0;
        data.entries.push({ date: dt, desc: desc, type: entryType.value, amt: Math.round(amt) });
        save(data);
        entryDesc.value = '';
        entryAmt.value = '';
        render();
        results.classList.remove('d-none');
    });

    openBal.addEventListener('change', function () {
        data.opening = parseFloat(openBal.value) || 0;
        save(data);
        render();
    });

    clearBtn.addEventListener('click', function () {
        if (!data.entries.length) return;
        if (confirm('All entries will be deleted. Are you sure you want to clear?')) {
            data.entries = [];
            save(data);
            render();
        }
    });

    csvBtn.addEventListener('click', function () {
        hideError();
        if (!data.entries.length) { showError('Add an entry first before downloading the CSV.'); return; }
        var lines = ['Date,Description,Type,Amount (Rs)'];
        data.entries.forEach(function (en) {
            var d = '"' + en.desc.replace(/"/g, '""') + '"';
            lines.push([en.date, d, en.type === 'in' ? 'In' : 'Out', en.amt].join(','));
        });
        lines.push('');
        lines.push('Opening Balance,,' + (data.opening || 0));
        var blob = new Blob(['\ufeff' + lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'petty-cash-book.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    render();
})();
</script>
@endsection
