@extends('layouts.app')

@section('title', 'Low Stock Alert List - Azlaan Tools')
@section('meta_description', 'Automatic list of low stock — know when and how much to order, never miss.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Low Stock Alert List</h1>
            <p class="lead text-muted">A list of low stock items — never miss an order. Data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row text-center g-2 mb-3">
                        <div class="col-6 col-md-4">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Total Items</small>
                                <div class="fw-bold" id="statTotal">0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Low Stock</small>
                                <div class="fw-bold text-danger" id="statLow">0</div>
                            </div></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Zero Stock</small>
                                <div class="fw-bold text-danger" id="statZero">0</div>
                            </div></div>
                        </div>
                    </div>

                    <h2 class="h5 mb-3">Add item</h2>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="lsName" class="form-label fw-semibold">Item name</label>
                            <input type="text" class="form-control" id="lsName" placeholder="e.g. Cheeni 1kg">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="lsQty" class="form-label fw-semibold">Qty</label>
                            <input type="number" class="form-control" id="lsQty" placeholder="0" min="0" step="1">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="lsLowAt" class="form-label fw-semibold">Alert when qty (low level)</label>
                            <input type="number" class="form-control" id="lsLowAt" placeholder="e.g. 10" min="0" step="1">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="lsPrice" class="form-label fw-semibold">Purchase price (Rs)</label>
                            <input type="number" class="form-control" id="lsPrice" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="lsAddBtn">Add Item</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">Low stock alerts <span class="badge bg-danger" id="alertCount">0</span></h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Item</th><th class="text-end">Current Qty</th><th class="text-end">Low Level</th><th class="text-end">Suggested Order</th><th class="text-end">Action</th></tr>
                            </thead>
                            <tbody id="alertRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="noAlertMsg">No low stock items — every item is above its low level.</p>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">All items</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Low Level</th><th class="text-end">Stock +</th><th></th></tr>
                            </thead>
                            <tbody id="allRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="noItemMsg">No items yet. Add them from above.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write each item's <strong>name</strong>, current <strong>qty</strong> and <strong>low level</strong>, then add it.</li>
                <li>When qty goes below the low level, the item appears in the <strong>alert list</strong>.</li>
                <li><strong>Suggested order</strong> = stock to reach double the low level (lowAt &times; 2 &minus; qty).</li>
                <li>When stock arrives, increase the item qty — the alert will clear by itself.</li>
            </ol>
            <p class="small text-muted">This list is shared with the inventory tools — an item added here also appears in other inventory tools.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_inventory';
    var lsName = document.getElementById('lsName');
    var lsQty = document.getElementById('lsQty');
    var lsLowAt = document.getElementById('lsLowAt');
    var lsPrice = document.getElementById('lsPrice');
    var lsAddBtn = document.getElementById('lsAddBtn');
    var errorBox = document.getElementById('errorBox');
    var alertRows = document.getElementById('alertRows');
    var allRows = document.getElementById('allRows');
    var noAlertMsg = document.getElementById('noAlertMsg');
    var noItemMsg = document.getElementById('noItemMsg');
    var alertCount = document.getElementById('alertCount');
    var statTotal = document.getElementById('statTotal');
    var statLow = document.getElementById('statLow');
    var statZero = document.getElementById('statZero');

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) return p;
            }
        } catch (e) {}
        return [];
    }
    function save(items) {
        try { localStorage.setItem(KEY, JSON.stringify(items)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function uid() {
        return 'i' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function suggestedQty(item) {
        var target = Math.max(Number(item.lowAt || 0) * 2, Number(item.lowAt || 0) + 1);
        return Math.max(0, target - Number(item.qty || 0));
    }

    function render() {
        var items = load();
        var low = items.filter(function (it) { return Number(it.qty || 0) <= Number(it.lowAt || 0); });
        var zero = items.filter(function (it) { return Number(it.qty || 0) <= 0; });

        statTotal.textContent = items.length;
        statLow.textContent = low.length;
        statZero.textContent = zero.length;
        alertCount.textContent = low.length;
        noAlertMsg.style.display = low.length ? 'none' : '';
        noItemMsg.style.display = items.length ? 'none' : '';

        alertRows.innerHTML = '';
        low.sort(function (a, b) { return Number(a.qty || 0) - Number(b.qty || 0); });
        low.forEach(function (it) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td');
            tdN.innerHTML = '<span class="fw-semibold">' + esc(it.name) + '</span>' +
                (Number(it.qty || 0) <= 0 ? ' <span class="badge bg-danger">OUT</span>' : ' <span class="badge bg-warning text-dark">LOW</span>');
            var tdQ = document.createElement('td');
            tdQ.className = 'text-end text-danger fw-bold';
            tdQ.textContent = it.qty;
            var tdL = document.createElement('td');
            tdL.className = 'text-end';
            tdL.textContent = it.lowAt;
            var tdS = document.createElement('td');
            tdS.className = 'text-end fw-semibold';
            tdS.textContent = suggestedQty(it);
            var tdA = document.createElement('td');
            tdA.className = 'text-end';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-success';
            btn.textContent = 'Stock received +';
            btn.addEventListener('click', function () {
                var list = load();
                for (var i = 0; i < list.length; i++) {
                    if (list[i].id === it.id) {
                        list[i].qty = Number(list[i].qty || 0) + suggestedQty(it);
                        break;
                    }
                }
                save(list);
                render();
            });
            tdA.appendChild(btn);
            tr.appendChild(tdN); tr.appendChild(tdQ); tr.appendChild(tdL); tr.appendChild(tdS); tr.appendChild(tdA);
            alertRows.appendChild(tr);
        });

        allRows.innerHTML = '';
        items.forEach(function (it) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td');
            tdN.textContent = it.name;
            var tdQ = document.createElement('td');
            tdQ.className = 'text-end';
            tdQ.textContent = it.qty;
            var tdL = document.createElement('td');
            tdL.className = 'text-end';
            tdL.textContent = it.lowAt;
            var tdA = document.createElement('td');
            tdA.className = 'text-end';
            var inp = document.createElement('input');
            inp.type = 'number';
            inp.min = '1';
            inp.step = '1';
            inp.className = 'form-control form-control-sm d-inline-block';
            inp.style.width = '80px';
            inp.placeholder = '+qty';
            var addB = document.createElement('button');
            addB.type = 'button';
            addB.className = 'btn btn-sm btn-outline-primary ms-1';
            addB.textContent = '+';
            addB.addEventListener('click', function () {
                var add = Number(inp.value);
                if (!add || add <= 0) { showError('Enter qty more than 0 to increase stock.'); return; }
                hideError();
                var list = load();
                for (var i = 0; i < list.length; i++) {
                    if (list[i].id === it.id) { list[i].qty = Number(list[i].qty || 0) + add; break; }
                }
                save(list);
                render();
            });
            tdA.appendChild(inp);
            tdA.appendChild(addB);
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete item');
            del.addEventListener('click', function () {
                if (!confirm(it.name + ' — delete it?')) return;
                save(load().filter(function (x) { return x.id !== it.id; }));
                render();
            });
            tdX.appendChild(del);
            tr.appendChild(tdN); tr.appendChild(tdQ); tr.appendChild(tdL); tr.appendChild(tdA); tr.appendChild(tdX);
            allRows.appendChild(tr);
        });
    }

    lsAddBtn.addEventListener('click', function () {
        hideError();
        var name = lsName.value.trim();
        if (!name) { showError('Enter the item name.'); return; }
        var items = load();
        var exists = false;
        items.forEach(function (it) {
            if (String(it.name).toLowerCase() === name.toLowerCase()) exists = true;
        });
        if (exists) { showError('This item is already in the list.'); return; }
        items.push({
            id: uid(),
            name: name,
            sku: '',
            purchasePrice: Number(lsPrice.value) || 0,
            salePrice: 0,
            qty: Number(lsQty.value) || 0,
            lowAt: Number(lsLowAt.value) || 0,
            expiry: '',
            batch: '',
            godown: 'shop'
        });
        save(items);
        lsName.value = '';
        lsQty.value = '';
        lsLowAt.value = '';
        lsPrice.value = '';
        render();
    });

    render();
})();
</script>
@endsection
