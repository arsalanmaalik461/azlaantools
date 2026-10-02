@extends('layouts.app')

@section('title', 'Multiple Wallets & Accounts - Azlaan Tools')
@section('meta_description', 'Free multi-wallet tracker: manage Cash, Bank, Easypaisa, JazzCash and Card balances in one place.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Multiple Wallets &amp; Accounts</h1>
            <p class="lead text-muted">Cash, Bank, Easypaisa/JazzCash and Card — each account's balance separately. Data is saved only in your browser, it is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 mb-0">Total money in all accounts</h2>
                        <div class="fs-4 fw-bold" id="grandTotal">Rs 0</div>
                    </div>
                    <p class="text-muted small mb-0">The balance updates automatically with every entry.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add a new account</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="accName" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="accName" placeholder="Example: Meezan Bank">
                        </div>
                        <div class="col-md-4">
                            <label for="accType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="accType">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="easypaisa">Easypaisa</option>
                                <option value="jazzcash">JazzCash</option>
                                <option value="card">Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="accOpen" class="form-label fw-semibold">Opening balance (Rs)</label>
                            <input type="number" class="form-control" id="accOpen" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="addAccBtn">Add Account</button>
                    <div class="alert alert-danger mt-3 d-none" id="accError" role="alert"></div>
                </div>
            </div>

            <div id="accList"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add a transaction</h2>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="txAcc" class="form-label fw-semibold">Account</label>
                            <select class="form-select" id="txAcc"></select>
                        </div>
                        <div class="col-md-3">
                            <label for="txType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="txType">
                                <option value="in">Income (money received)</option>
                                <option value="out">Expense (money spent)</option>
                                <option value="transfer">Transfer (from one account to another)</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="txToWrap" style="display:none;">
                            <label for="txTo" class="form-label fw-semibold">Transfer to</label>
                            <select class="form-select" id="txTo"></select>
                        </div>
                        <div class="col-md-3">
                            <label for="txAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="txAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="txNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="txNote" placeholder="Example: shop sale">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="addTxBtn">Add Entry</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Recent transactions</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light"><tr><th>Date</th><th>Account</th><th>Type</th><th class="text-end">Amount</th><th>Note</th><th></th></tr></thead>
                            <tbody id="txRows"></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0" id="txEmpty">No entries yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add your accounts (Cash, Bank, Easypaisa, JazzCash, Card) with the opening balance.</li>
                <li>Add an income or expense entry — that account's <strong>balance will update</strong> automatically.</li>
                <li>Use transfer to move money from one account to another (both balances update).</li>
            </ol>
            <p class="text-muted small">Note: your data stays safe in this browser. Clearing browser data will delete these records.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_accounts';
    var TYPE_LABELS = { cash: 'Cash', bank: 'Bank', easypaisa: 'Easypaisa', jazzcash: 'JazzCash', card: 'Card', other: 'Other' };
    var TYPE_COLORS = { cash: 'bg-success', bank: 'bg-primary', easypaisa: 'bg-danger', jazzcash: 'bg-warning text-dark', card: 'bg-info text-dark', other: 'bg-secondary' };

    var grandTotal = document.getElementById('grandTotal');
    var accName = document.getElementById('accName');
    var accType = document.getElementById('accType');
    var accOpen = document.getElementById('accOpen');
    var addAccBtn = document.getElementById('addAccBtn');
    var accError = document.getElementById('accError');
    var accList = document.getElementById('accList');
    var txAcc = document.getElementById('txAcc');
    var txType = document.getElementById('txType');
    var txToWrap = document.getElementById('txToWrap');
    var txTo = document.getElementById('txTo');
    var txAmt = document.getElementById('txAmt');
    var txNote = document.getElementById('txNote');
    var addTxBtn = document.getElementById('addTxBtn');
    var txRows = document.getElementById('txRows');
    var txEmpty = document.getElementById('txEmpty');

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && p.accounts) return p;
            }
        } catch (e) {}
        return { accounts: [], txs: [] };
    }
    function save(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); } catch (e) {}
    }
    function showError(msg) { accError.textContent = msg; accError.classList.remove('d-none'); }
    function hideError() { accError.classList.add('d-none'); accError.textContent = ''; }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function uid() { return 'a' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36); }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function balance(acc, txs) {
        var b = Number(acc.opening) || 0;
        txs.forEach(function (t) {
            if (t.accId === acc.id) {
                if (t.type === 'in') b += t.amount;
                else if (t.type === 'out') b -= t.amount;
                else if (t.type === 'transfer_out') b -= t.amount;
                else if (t.type === 'transfer_in') b += t.amount;
            }
        });
        return Math.round(b * 100) / 100;
    }
    function accById(d, id) {
        for (var i = 0; i < d.accounts.length; i++) if (d.accounts[i].id === id) return d.accounts[i];
        return null;
    }

    function render() {
        hideError();
        var d = load();
        var total = 0;
        accList.innerHTML = '';
        if (!d.accounts.length) {
            accList.innerHTML = '<div class="alert alert-light border">No accounts. Add an account from above.</div>';
        }
        d.accounts.forEach(function (a) {
            var b = balance(a, d.txs);
            total += b;
            var card = document.createElement('div');
            card.className = 'card shadow-sm mb-3';
            var body = document.createElement('div');
            body.className = 'card-body d-flex justify-content-between align-items-center';
            var left = document.createElement('div');
            var nm = document.createElement('div');
            nm.className = 'fw-bold';
            nm.textContent = a.name;
            var badge = document.createElement('span');
            badge.className = 'badge me-1 ' + (TYPE_COLORS[a.type] || 'bg-secondary');
            badge.textContent = TYPE_LABELS[a.type] || a.type;
            var bal = document.createElement('div');
            bal.className = 'fs-5 fw-bold ' + (b < 0 ? 'text-danger' : '');
            bal.textContent = fmt(b);
            left.appendChild(nm);
            left.appendChild(badge);
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = 'Delete';
            del.addEventListener('click', function () {
                if (!confirm('Delete the ' + a.name + ' account and all its entries?')) return;
                var d2 = load();
                d2.accounts = d2.accounts.filter(function (x) { return x.id !== a.id; });
                d2.txs = d2.txs.filter(function (t) { return t.accId !== a.id && t.toId !== a.id; });
                save(d2);
                render();
            });
            body.appendChild(left);
            var right = document.createElement('div');
            right.className = 'text-end';
            right.appendChild(bal);
            right.appendChild(document.createElement('br'));
            right.appendChild(del);
            body.appendChild(right);
            card.appendChild(body);
            accList.appendChild(card);
        });
        grandTotal.textContent = fmt(total);
        grandTotal.className = 'fs-4 fw-bold ' + (total < 0 ? 'text-danger' : 'text-success');

        txAcc.innerHTML = '';
        txTo.innerHTML = '';
        d.accounts.forEach(function (a) {
            var o1 = document.createElement('option');
            o1.value = a.id; o1.textContent = a.name;
            txAcc.appendChild(o1);
            var o2 = document.createElement('option');
            o2.value = a.id; o2.textContent = a.name;
            txTo.appendChild(o2);
        });

        txRows.innerHTML = '';
        var sorted = d.txs.slice().reverse().slice(0, 50);
        sorted.forEach(function (t) {
            var a = accById(d, t.accId);
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = t.date;
            var td2 = document.createElement('td'); td2.textContent = a ? a.name : '(deleted)';
            var td3 = document.createElement('td');
            var lbl = t.type === 'in' ? 'Income' : (t.type === 'out' ? 'Expense' : 'Transfer');
            if (t.type === 'transfer_in') lbl = 'Transfer In';
            if (t.type === 'transfer_out') lbl = 'Transfer Out';
            var badge = document.createElement('span');
            badge.className = 'badge ' + (t.type === 'in' || t.type === 'transfer_in' ? 'bg-success' : (t.type === 'transfer_out' ? 'bg-info text-dark' : 'bg-danger'));
            badge.textContent = lbl;
            td3.appendChild(badge);
            var td4 = document.createElement('td'); td4.className = 'text-end';
            var signed = (t.type === 'in' || t.type === 'transfer_in') ? '+' : '−';
            td4.textContent = signed + fmt(t.amount);
            var td5 = document.createElement('td'); td5.textContent = t.note || '—';
            var td6 = document.createElement('td'); td6.className = 'text-end';
            var delb = document.createElement('button');
            delb.type = 'button'; delb.className = 'btn btn-sm btn-outline-danger'; delb.textContent = '×';
            delb.addEventListener('click', function () {
                var d2 = load();
                d2.txs = d2.txs.filter(function (x) { return x.id !== t.id; });
                save(d2);
                render();
            });
            td6.appendChild(delb);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            tr.appendChild(td4); tr.appendChild(td5); tr.appendChild(td6);
            txRows.appendChild(tr);
        });
        txEmpty.style.display = d.txs.length ? 'none' : '';
    }

    addAccBtn.addEventListener('click', function () {
        hideError();
        var name = accName.value.trim();
        if (!name) { showError('Enter the account name.'); return; }
        var open = Number(accOpen.value) || 0;
        var d = load();
        d.accounts.push({ id: uid(), name: name, type: accType.value, opening: Math.round(open * 100) / 100 });
        save(d);
        accName.value = '';
        accOpen.value = '';
        render();
    });

    txType.addEventListener('change', function () {
        txToWrap.style.display = txType.value === 'transfer' ? '' : 'none';
    });

    addTxBtn.addEventListener('click', function () {
        hideError();
        var d = load();
        if (!d.accounts.length) { showError('Add an account first.'); return; }
        var amt = Number(txAmt.value);
        if (isNaN(amt) || amt <= 0) { showError('Enter an amount greater than 0.'); return; }
        var fromId = txAcc.value;
        var note = txNote.value.trim();
        var date = todayStr();
        if (txType.value === 'transfer') {
            var toId = txTo.value;
            if (fromId === toId) { showError('Choose two different accounts for a transfer.'); return; }
            var a = accById(d, fromId), b = accById(d, toId);
            d.txs.push({ id: uid(), accId: fromId, type: 'transfer_out', amount: amt, note: note + (note ? ' | ' : '') + '→ ' + (b ? b.name : ''), date: date });
            d.txs.push({ id: uid(), accId: toId, type: 'transfer_in', amount: amt, note: note + (note ? ' | ' : '') + '← ' + (a ? a.name : ''), date: date, toId: fromId });
            try {
                var trRaw = localStorage.getItem('azlaan7_transfers');
                var tr = trRaw ? JSON.parse(trRaw) : { transfers: [] };
                tr.transfers.push({ id: uid(), fromName: a ? a.name : '', toName: b ? b.name : '', amount: amt, date: date, note: note });
                localStorage.setItem('azlaan7_transfers', JSON.stringify(tr));
            } catch (e) {}
        } else {
            d.txs.push({ id: uid(), accId: fromId, type: txType.value, amount: Math.round(amt * 100) / 100, note: note, date: date });
        }
        save(d);
        txAmt.value = '';
        txNote.value = '';
        render();
    });

    render();
})();
</script>
@endsection
