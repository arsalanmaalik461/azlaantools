@extends('layouts.app')

@section('title', 'Transfer Between Accounts - Azlaan Tools')
@section('meta_description', 'Free account transfer tracker: record money moved between Cash, Bank, Easypaisa and Card accounts.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Transfer Between Accounts</h1>
            <p class="lead text-muted">From Cash to Bank or Bank to Card — record transfers between accounts. Data is saved only in your browser, it is not uploaded anywhere.</p>

            <div id="accHint" class="alert alert-light border d-none">
                <span id="accHintText"></span>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Record a new transfer</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="trFrom" class="form-label fw-semibold">From (which account)</label>
                            <select class="form-select" id="trFrom"></select>
                            <input type="text" class="form-control mt-2 d-none" id="trFromManual" placeholder="Type the From account name">
                        </div>
                        <div class="col-md-6">
                            <label for="trTo" class="form-label fw-semibold">To (which account)</label>
                            <select class="form-select" id="trTo"></select>
                            <input type="text" class="form-control mt-2 d-none" id="trToManual" placeholder="Type the To account name">
                        </div>
                        <div class="col-md-6">
                            <label for="trAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="trAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="trDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="trDate">
                        </div>
                        <div class="col-12">
                            <label for="trNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="trNote" placeholder="Example: cash withdrawn from ATM">
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="trManual">
                        <label class="form-check-label" for="trManual">I want to type the account names myself (manual mode)</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="addTrBtn">Record Transfer</button>
                    <div class="alert alert-danger mt-3 d-none" id="trError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Transfer history</h2>
                        <span class="badge bg-secondary" id="trCount">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light"><tr><th>Date</th><th>From</th><th>To</th><th class="text-end">Amount</th><th>Note</th><th></th></tr></thead>
                            <tbody id="trRows"></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0" id="trEmpty">No transfer recorded yet.</p>
                    <div class="row g-2 mt-3">
                        <div class="col-6"><div class="border rounded p-2 text-center"><div class="small text-muted">Total transfers</div><div class="fw-bold" id="trTotalCount">0</div></div></div>
                        <div class="col-6"><div class="border rounded p-2 text-center"><div class="small text-muted">Total amount moved</div><div class="fw-bold" id="trTotalAmt">Rs 0</div></div></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the <strong>From</strong> and <strong>To</strong> accounts (accounts from the Multiple Wallets tool will appear automatically).</li>
                <li>Enter the amount, date and note, then press <strong>Record Transfer</strong>.</li>
                <li>Transfers from the Wallets tool will also appear here in the history.</li>
            </ol>
            <p class="text-muted small">Note: data stays safe in this same browser. Clearing the browser data will delete this record.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_transfers';
    var ACC_KEY = 'azlaan7_accounts';

    var trFrom = document.getElementById('trFrom');
    var trTo = document.getElementById('trTo');
    var trFromManual = document.getElementById('trFromManual');
    var trToManual = document.getElementById('trToManual');
    var trAmt = document.getElementById('trAmt');
    var trDate = document.getElementById('trDate');
    var trNote = document.getElementById('trNote');
    var trManual = document.getElementById('trManual');
    var addTrBtn = document.getElementById('addTrBtn');
    var trError = document.getElementById('trError');
    var trRows = document.getElementById('trRows');
    var trEmpty = document.getElementById('trEmpty');
    var trCount = document.getElementById('trCount');
    var trTotalCount = document.getElementById('trTotalCount');
    var trTotalAmt = document.getElementById('trTotalAmt');
    var accHint = document.getElementById('accHint');
    var accHintText = document.getElementById('accHintText');

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && p.transfers) return p;
            }
        } catch (e) {}
        return { transfers: [] };
    }
    function save(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); } catch (e) {}
    }
    function loadAccounts() {
        try {
            var raw = localStorage.getItem(ACC_KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && p.accounts) return p.accounts;
            }
        } catch (e) {}
        return [];
    }
    function showError(msg) { trError.textContent = msg; trError.classList.remove('d-none'); }
    function hideError() { trError.classList.add('d-none'); trError.textContent = ''; }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function uid() { return 't' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36); }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }

    function fillAccountPickers() {
        var accs = loadAccounts();
        trFrom.innerHTML = '';
        trTo.innerHTML = '';
        accs.forEach(function (a) {
            var o1 = document.createElement('option');
            o1.value = a.name; o1.textContent = a.name;
            trFrom.appendChild(o1);
            var o2 = document.createElement('option');
            o2.value = a.name; o2.textContent = a.name;
            trTo.appendChild(o2);
        });
        if (accs.length) {
            accHintText.textContent = accs.length + ' accounts found from the "Multiple Wallets & Accounts" tool — select them in From/To.';
            accHint.classList.remove('d-none');
        } else {
            accHintText.textContent = 'No account found — type names in manual mode or first create accounts in the "Multiple Wallets & Accounts" tool.';
            accHint.classList.remove('d-none');
            trManual.checked = true;
            trManual.dispatchEvent(new Event('change'));
        }
    }

    trManual.addEventListener('change', function () {
        var m = trManual.checked;
        trFrom.classList.toggle('d-none', m);
        trTo.classList.toggle('d-none', m);
        trFromManual.classList.toggle('d-none', !m);
        trToManual.classList.toggle('d-none', !m);
    });

    function render() {
        hideError();
        fillAccountPickers();
        var d = load();
        trRows.innerHTML = '';
        var totalAmt = 0;
        var sorted = d.transfers.slice().reverse();
        sorted.forEach(function (t) {
            totalAmt += t.amount;
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = t.date;
            var td2 = document.createElement('td');
            var b1 = document.createElement('span'); b1.className = 'badge bg-danger'; b1.textContent = t.fromName;
            td2.appendChild(b1);
            var td3 = document.createElement('td');
            var b2 = document.createElement('span'); b2.className = 'badge bg-success'; b2.textContent = t.toName;
            td3.appendChild(b2);
            var td4 = document.createElement('td'); td4.className = 'text-end'; td4.textContent = fmt(t.amount);
            var td5 = document.createElement('td'); td5.textContent = t.note || '—';
            var td6 = document.createElement('td'); td6.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = '×';
            del.addEventListener('click', function () {
                var d2 = load();
                d2.transfers = d2.transfers.filter(function (x) { return x.id !== t.id; });
                save(d2);
                render();
            });
            td6.appendChild(del);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            tr.appendChild(td4); tr.appendChild(td5); tr.appendChild(td6);
            trRows.appendChild(tr);
        });
        trEmpty.style.display = d.transfers.length ? 'none' : '';
        trCount.textContent = d.transfers.length;
        trTotalCount.textContent = d.transfers.length;
        trTotalAmt.textContent = fmt(totalAmt);
    }

    addTrBtn.addEventListener('click', function () {
        hideError();
        var fromName, toName;
        if (trManual.checked) {
            fromName = trFromManual.value.trim();
            toName = trToManual.value.trim();
        } else {
            fromName = trFrom.value;
            toName = trTo.value;
        }
        if (!fromName || !toName) { showError('Type or select both From and To names.'); return; }
        if (fromName === toName) { showError('From and To must be different.'); return; }
        var amt = Number(trAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Amount must be more than 0.'); return; }
        if (!trDate.value) { showError('Select a date.'); return; }
        var d = load();
        d.transfers.push({
            id: uid(),
            fromName: fromName,
            toName: toName,
            amount: Math.round(amt * 100) / 100,
            date: trDate.value,
            note: trNote.value.trim()
        });
        save(d);
        trAmt.value = '';
        trNote.value = '';
        render();
    });

    trDate.value = todayStr();
    render();
})();
</script>
@endsection
