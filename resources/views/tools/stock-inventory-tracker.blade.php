@extends('layouts.app')
@section('title', 'Stock Inventory Tracker - Free Shop Stock Record | Azlaan Tools')
@section('meta_description', 'Track your shop stock for free: add items, reduce stock on sale, get low stock alerts, and keep a sale record. Everything stays in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-2">Stock Inventory Tracker</h1>
            <p class="lead text-muted">Track your shop stock — add items, reduce on sale, get low stock alerts.</p>
            <div id="alertBox"></div>

            <div class="row text-center g-2 mb-4">
                <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2"><div class="small text-muted">Total items</div><strong id="statItems" class="fs-4">0</strong></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2"><div class="small text-muted">Stock value (buy)</div><strong id="statValue" class="fs-4">Rs 0</strong></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2"><div class="small text-muted">Expected sale value</div><strong id="statRevenue" class="fs-4">Rs 0</strong></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2"><div class="small text-muted">Low stock</div><strong id="statLow" class="fs-4 text-danger">0</strong></div></div></div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Add a new item</h2>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label for="itemName" class="form-label fw-semibold small">Item name</label>
                            <input type="text" class="form-control" id="itemName" placeholder="e.g. Rice 5kg">
                        </div>
                        <div class="col-4 col-md-2">
                            <label for="itemQty" class="form-label fw-semibold small">Quantity</label>
                            <input type="number" class="form-control" id="itemQty" min="0" value="0">
                        </div>
                        <div class="col-4 col-md-2">
                            <label for="itemUnit" class="form-label fw-semibold small">Unit</label>
                            <input type="text" class="form-control" id="itemUnit" placeholder="pcs/kg" value="pcs">
                        </div>
                        <div class="col-4 col-md-2">
                            <label for="itemBuy" class="form-label fw-semibold small">Buy rate (Rs)</label>
                            <input type="number" class="form-control" id="itemBuy" min="0" value="0">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="itemSell" class="form-label fw-semibold small">Sale rate (Rs)</label>
                            <input type="number" class="form-control" id="itemSell" min="0" value="0">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="itemLow" class="form-label fw-semibold small">Low alert at</label>
                            <input type="number" class="form-control" id="itemLow" min="0" value="5">
                        </div>
                        <div class="col-md-10 d-none d-md-block"></div>
                    </div>
                    <button type="button" id="addBtn" class="btn btn-primary mt-3">Add Item</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Current stock</h2>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search item..." style="max-width: 180px;">
                            <button type="button" id="exportBtn" class="btn btn-sm btn-outline-secondary">CSV Download</button>
                            <button type="button" id="clearBtn" class="btn btn-sm btn-outline-danger">Clear All</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr><th>Item</th><th>Stock</th><th>Buy</th><th>Sale</th><th>Value</th><th>Status</th><th style="min-width: 190px;">Sale / Restock</th><th></th></tr>
                            </thead>
                            <tbody id="stockBody"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="emptyMsg">No items yet — add your first item above.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Recent transactions</h2>
                    <ul class="list-group list-group-flush small" id="txnList"></ul>
                    <p class="small text-muted mt-2 mb-0">Your record stays saved in your browser — it is safe on this device and is not uploaded anywhere.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>New item: enter the name, quantity, buy/sale rate and low-stock limit, then press <strong>Add Item</strong>.</li>
                <li>When selling to a customer, enter the quantity and press <strong>Sale</strong> — stock reduces automatically.</li>
                <li>When new stock arrives, add it with <strong>+ Stock</strong>.</li>
                <li>When stock runs low, the <strong>Low stock</strong> alert appears automatically.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var LS_KEY = 'azlaan_stock_v1';

    var itemName = document.getElementById('itemName');
    var itemQty = document.getElementById('itemQty');
    var itemUnit = document.getElementById('itemUnit');
    var itemBuy = document.getElementById('itemBuy');
    var itemSell = document.getElementById('itemSell');
    var itemLow = document.getElementById('itemLow');
    var addBtn = document.getElementById('addBtn');
    var errorBox = document.getElementById('errorBox');
    var searchInput = document.getElementById('searchInput');
    var stockBody = document.getElementById('stockBody');
    var emptyMsg = document.getElementById('emptyMsg');
    var txnList = document.getElementById('txnList');
    var exportBtn = document.getElementById('exportBtn');
    var clearBtn = document.getElementById('clearBtn');
    var alertBox = document.getElementById('alertBox');
    var statItems = document.getElementById('statItems');
    var statValue = document.getElementById('statValue');
    var statRevenue = document.getElementById('statRevenue');
    var statLow = document.getElementById('statLow');

    var state = { items: [], txns: [] };

    function load() {
        try {
            var s = localStorage.getItem(LS_KEY);
            if (s) state = JSON.parse(s);
            if (!state.items) state.items = [];
            if (!state.txns) state.txns = [];
        } catch (e) { state = { items: [], txns: [] }; }
    }
    function save() {
        try { localStorage.setItem(LS_KEY, JSON.stringify(state)); } catch (e) { /* ignore */ }
    }
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function rs(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-US'); }
    function uid() { return 'i' + Date.now().toString(36) + Math.floor(Math.random() * 9999); }
    function findItem(id) {
        for (var i = 0; i < state.items.length; i++) if (state.items[i].id === id) return state.items[i];
        return null;
    }
    function logTxn(type, name, qty) {
        state.txns.unshift({ t: Date.now(), type: type, name: name, qty: qty });
        state.txns = state.txns.slice(0, 50);
    }
    function statusOf(it) {
        if (it.qty <= 0) return { label: 'Out of stock', cls: 'bg-danger' };
        if (it.qty <= it.low) return { label: 'Low stock', cls: 'bg-warning text-dark' };
        return { label: 'OK', cls: 'bg-success' };
    }

    addBtn.addEventListener('click', function () {
        hideError();
        var name = itemName.value.trim();
        var qty = parseFloat(itemQty.value);
        var buy = parseFloat(itemBuy.value);
        var sell = parseFloat(itemSell.value);
        var low = parseFloat(itemLow.value);
        if (!name) { showError('Please enter the item name.'); return; }
        if (isNaN(qty) || qty < 0) { showError('Quantity must be 0 or more.'); return; }
        if (isNaN(buy) || buy < 0 || isNaN(sell) || sell < 0) { showError('Buy and sale rates must be 0 or more.'); return; }
        if (isNaN(low) || low < 0) low = 0;
        state.items.unshift({
            id: uid(), name: name, qty: qty,
            unit: itemUnit.value.trim() || 'pcs',
            buy: buy, sell: sell, low: low
        });
        logTxn('add', name, qty);
        save(); render();
        itemName.value = ''; itemQty.value = '0'; itemBuy.value = '0'; itemSell.value = '0'; itemLow.value = '5';
        itemName.focus();
    });

    stockBody.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('button[data-act]') : null;
        if (!btn) return;
        var id = btn.getAttribute('data-id');
        var act = btn.getAttribute('data-act');
        var it = findItem(id);
        if (!it) return;
        hideError();
        if (act === 'del') {
            if (!confirm('"' + it.name + '" — delete this item?')) return;
            state.items = state.items.filter(function (x) { return x.id !== id; });
            logTxn('delete', it.name, 0);
        } else {
            var row = btn.closest('tr');
            var qtyInput = row ? row.querySelector('.qty-in') : null;
            var n = qtyInput ? parseFloat(qtyInput.value) : 1;
            if (isNaN(n) || n <= 0) { showError('Quantity must be 1 or more.'); return; }
            if (act === 'sale') {
                if (n > it.qty) { showError('Only ' + it.qty + ' ' + it.unit + ' in stock — you cannot sell more than this.'); return; }
                it.qty -= n;
                logTxn('sale', it.name, n);
            } else if (act === 'restock') {
                it.qty += n;
                logTxn('restock', it.name, n);
            }
        }
        save(); render();
    });

    searchInput.addEventListener('input', render);

    exportBtn.addEventListener('click', function () {
        if (!state.items.length) { showError('There are no items to export.'); return; }
        hideError();
        var csv = 'Name,Qty,Unit,Buy Rate,Sell Rate,Low Alert,Stock Value\n';
        state.items.forEach(function (it) {
            csv += '"' + it.name.replace(/"/g, '""') + '",' + it.qty + ',' + it.unit + ',' +
                it.buy + ',' + it.sell + ',' + it.low + ',' + (it.qty * it.buy) + '\n';
        });
        var blob = new Blob([csv], { type: 'text/csv' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'stock-inventory.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); URL.revokeObjectURL(a.href); }, 500);
    });

    clearBtn.addEventListener('click', function () {
        if (!state.items.length) return;
        if (!confirm('This will delete the whole stock record. Are you sure?')) return;
        state = { items: [], txns: [] };
        save(); render();
    });

    function render() {
        var q = searchInput.value.trim().toLowerCase();
        var html = '';
        var totalVal = 0, totalRev = 0, lowCount = 0;
        var lowNames = [];
        state.items.forEach(function (it) {
            totalVal += it.qty * it.buy;
            totalRev += it.qty * it.sell;
            var st = statusOf(it);
            if (st.label !== 'OK') {
                lowCount++;
                lowNames.push(it.name + ' (' + it.qty + ' ' + it.unit + ')');
            }
            if (q && it.name.toLowerCase().indexOf(q) === -1) return;
            html += '<tr>' +
                '<td class="fw-semibold">' + esc(it.name) + '</td>' +
                '<td>' + it.qty + ' ' + esc(it.unit) + '</td>' +
                '<td>' + rs(it.buy) + '</td>' +
                '<td>' + rs(it.sell) + '</td>' +
                '<td>' + rs(it.qty * it.buy) + '</td>' +
                '<td><span class="badge ' + st.cls + '">' + st.label + '</span></td>' +
                '<td><div class="d-flex gap-1 align-items-center">' +
                '<input type="number" class="form-control form-control-sm qty-in" value="1" min="1" style="width: 64px;">' +
                '<button type="button" class="btn btn-sm btn-outline-success" data-act="sale" data-id="' + it.id + '">Sale</button>' +
                '<button type="button" class="btn btn-sm btn-outline-primary" data-act="restock" data-id="' + it.id + '">+ Stock</button>' +
                '</div></td>' +
                '<td><button type="button" class="btn btn-sm btn-outline-danger" data-act="del" data-id="' + it.id + '">Delete</button></td>' +
                '</tr>';
        });
        stockBody.innerHTML = html;
        emptyMsg.style.display = state.items.length ? 'none' : 'block';
        statItems.textContent = state.items.length;
        statValue.textContent = rs(totalVal);
        statRevenue.textContent = rs(totalRev);
        statLow.textContent = lowCount;
        if (lowNames.length) {
            alertBox.innerHTML = '<div class="alert alert-danger"><strong>Low stock alert:</strong> ' +
                esc(lowNames.slice(0, 5).join(', ')) + (lowNames.length > 5 ? ' and ' + (lowNames.length - 5) + ' more' : '') +
                ' — stock is running out!</div>';
        } else {
            alertBox.innerHTML = '';
        }
        var th = '';
        if (!state.txns.length) {
            th = '<li class="list-group-item text-muted">No transactions yet.</li>';
        } else {
            state.txns.slice(0, 15).forEach(function (tx) {
                var d = new Date(tx.t);
                var when = d.getDate() + '/' + (d.getMonth() + 1) + ' ' + d.getHours() + ':' + (d.getMinutes() < 10 ? '0' : '') + d.getMinutes();
                var what = tx.type === 'sale' ? 'Sale' : tx.type === 'restock' ? 'Stock added' : tx.type === 'add' ? 'New item' : 'Delete';
                th += '<li class="list-group-item d-flex justify-content-between"><span><strong>' + what + ':</strong> ' +
                    esc(tx.name) + (tx.qty ? ' — ' + tx.qty : '') + '</span><span class="text-muted">' + when + '</span></li>';
            });
        }
        txnList.innerHTML = th;
    }

    load();
    render();
})();
</script>
@endsection
