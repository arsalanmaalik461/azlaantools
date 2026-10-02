@extends('layouts.app')

@section('title', 'Godown Stock Manager - Azlaan Tools')
@section('meta_description', 'Track shop and godown stock separately — transfer record and history.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Godown Stock Manager</h1>
            <p class="lead text-muted">Track shop and godown stock separately — with a transfer record. Your data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 mb-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label for="gdName" class="form-label fw-semibold">Location name</label>
                            <input type="text" class="form-control" id="gdName" placeholder="e.g. Godown 3">
                        </div>
                        <div class="col-6 col-md-4">
                            <button type="button" class="btn btn-outline-primary" id="gdAddLocBtn">Add Location</button>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="locFilter" class="form-label fw-semibold">View</label>
                            <select class="form-select" id="locFilter"></select>
                        </div>
                    </div>
                    <div id="locChips" class="d-flex flex-wrap gap-2 mb-3"></div>

                    <h2 class="h5 mb-3">Add an item</h2>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="gdItem" class="form-label fw-semibold">Item name</label>
                            <input type="text" class="form-control" id="gdItem" list="gdItemList" placeholder="e.g. Atta 20kg">
                            <datalist id="gdItemList"></datalist>
                            <div class="form-text">Names come automatically from your inventory — you can also pick from the dropdown.</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="gdItemLoc" class="form-label fw-semibold">Location</label>
                            <select class="form-select" id="gdItemLoc"></select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="gdItemQty" class="form-label fw-semibold">Qty</label>
                            <input type="number" class="form-control" id="gdItemQty" placeholder="0" min="0" step="1">
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="gdAddItemBtn">Add Item</button>
                        </div>
                    </div>
                    <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">Stock transfer</h2>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-3">
                            <label for="trItem" class="form-label fw-semibold">Item</label>
                            <select class="form-select" id="trItem"></select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="trFrom" class="form-label fw-semibold">From</label>
                            <select class="form-select" id="trFrom"></select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="trTo" class="form-label fw-semibold">To</label>
                            <select class="form-select" id="trTo"></select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="trQty" class="form-label fw-semibold">Qty</label>
                            <input type="number" class="form-control" id="trQty" placeholder="0" min="1" step="1">
                        </div>
                        <div class="col-6 col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-success w-100" id="trBtn">Transfer</button>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">Stock table</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Item</th><th class="text-end">Total Qty</th><th id="locHeadRow" class="d-none"></th><th class="text-end">Action</th></tr>
                            </thead>
                            <tbody id="stockRows"></tbody>
                        </table>
                    </div>
                    <div id="locTableWrap"></div>
                    <p class="small text-muted" id="emptyMsg">No items. Add from above.</p>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 mb-0">Transfer history</h2>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="histClearBtn">Clear history</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr><th>Date / Time</th><th>Item</th><th>From</th><th>To</th><th class="text-end">Qty</th></tr>
                            </thead>
                            <tbody id="histRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="histEmpty">No transfers yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add <strong>locations</strong> — like Godown-1, Godown-2 besides the shop.</li>
                <li>Choose the <strong>location</strong> when you add an item.</li>
                <li>To move stock between places, use <strong>Transfer</strong> — the history saves on its own.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_godowns';
    var INV_KEY = 'azlaan7_inventory';
    var DEFAULT_LOCS = ['Shop', 'Godown-1', 'Godown-2'];

    var gdName = document.getElementById('gdName');
    var gdAddLocBtn = document.getElementById('gdAddLocBtn');
    var locFilter = document.getElementById('locFilter');
    var locChips = document.getElementById('locChips');
    var gdItem = document.getElementById('gdItem');
    var gdItemLoc = document.getElementById('gdItemLoc');
    var gdItemQty = document.getElementById('gdItemQty');
    var gdAddItemBtn = document.getElementById('gdAddItemBtn');
    var trItem = document.getElementById('trItem');
    var trFrom = document.getElementById('trFrom');
    var trTo = document.getElementById('trTo');
    var trQty = document.getElementById('trQty');
    var trBtn = document.getElementById('trBtn');
    var stockRows = document.getElementById('stockRows');
    var locTableWrap = document.getElementById('locTableWrap');
    var emptyMsg = document.getElementById('emptyMsg');
    var histRows = document.getElementById('histRows');
    var histEmpty = document.getElementById('histEmpty');
    var histClearBtn = document.getElementById('histClearBtn');
    var errorBox = document.getElementById('errorBox');

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && p.locations && p.stock) return p;
            }
        } catch (e) {}
        return { locations: DEFAULT_LOCS.slice(), stock: [], history: [] };
    }
    function save(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function loadInv() {
        try {
            var raw = localStorage.getItem(INV_KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) return p;
            }
        } catch (e) {}
        return [];
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
    function qtyOf(item, loc) {
        return Number((item.qtyByLoc && item.qtyByLoc[loc]) || 0);
    }
    function totalQty(item) {
        var t = 0;
        for (var k in item.qtyByLoc) { if (item.qtyByLoc.hasOwnProperty(k)) t += Number(item.qtyByLoc[k] || 0); }
        return t;
    }
    function nowStr() {
        var d = new Date();
        function p2(n) { return (n < 10 ? '0' : '') + n; }
        return d.getFullYear() + '-' + p2(d.getMonth() + 1) + '-' + p2(d.getDate()) + ' ' + p2(d.getHours()) + ':' + p2(d.getMinutes());
    }

    function fillLocSelects() {
        var d = load();
        [gdItemLoc, trFrom, trTo, locFilter].forEach(function (sel, idx) {
            sel.innerHTML = '';
            if (idx === 3) {
                var all = document.createElement('option');
                all.value = '__all';
                all.textContent = 'All locations';
                sel.appendChild(all);
            }
            d.locations.forEach(function (l) {
                var o = document.createElement('option');
                o.value = l;
                o.textContent = l;
                sel.appendChild(o);
            });
        });
    }
    function fillItemSelects() {
        var d = load();
        var inv = loadInv();
        var names = {};
        d.stock.forEach(function (it) { names[it.name] = true; });
        inv.forEach(function (it) { names[it.name] = true; });
        trItem.innerHTML = '';
        Object.keys(names).forEach(function (n) {
            var o = document.createElement('option');
            o.value = n;
            o.textContent = n;
            trItem.appendChild(o);
        });
        // datalist for item input
        var dl = document.getElementById('gdItemList');
        dl.innerHTML = '';
        Object.keys(names).forEach(function (n) {
            var o = document.createElement('option');
            o.value = n;
            dl.appendChild(o);
        });
    }

    function render() {
        hideError();
        var d = load();
        fillLocSelects();
        fillItemSelects();

        // location chips with totals
        locChips.innerHTML = '';
        d.locations.forEach(function (l) {
            var t = 0;
            d.stock.forEach(function (it) { t += qtyOf(it, l); });
            var chip = document.createElement('span');
            chip.className = 'badge bg-primary rounded-pill p-2';
            chip.textContent = l + ': ' + t + ' units';
            locChips.appendChild(chip);
        });

        var filterLoc = locFilter.value || '__all';
        emptyMsg.style.display = d.stock.length ? 'none' : '';

        // per-location breakdown table
        var html = '<div class="table-responsive mb-3"><table class="table table-sm table-bordered align-middle"><thead class="table-light"><tr><th>Item</th>';
        d.locations.forEach(function (l) { html += '<th class="text-end">' + esc(l) + '</th>'; });
        html += '<th class="text-end">Total</th></tr></thead><tbody>';
        var shown = d.stock.filter(function (it) {
            return filterLoc === '__all' || qtyOf(it, filterLoc) > 0;
        });
        shown.forEach(function (it) {
            html += '<tr><td class="fw-semibold">' + esc(it.name) + '</td>';
            d.locations.forEach(function (l) {
                html += '<td class="text-end">' + qtyOf(it, l) + '</td>';
            });
            html += '<td class="text-end fw-bold">' + totalQty(it) + '</td></tr>';
        });
        html += '</tbody></table></div>';
        locTableWrap.innerHTML = shown.length ? html : '';

        // main stock rows with stock-in control
        stockRows.innerHTML = '';
        d.stock.forEach(function (it) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td');
            tdN.innerHTML = '<span class="fw-semibold">' + esc(it.name) + '</span>';
            var tdT = document.createElement('td');
            tdT.className = 'text-end fw-bold';
            tdT.textContent = totalQty(it);
            var tdA = document.createElement('td');
            tdA.className = 'text-end';
            var sel = document.createElement('select');
            sel.className = 'form-select form-select-sm d-inline-block';
            sel.style.width = '130px';
            sel.setAttribute('aria-label', 'Location');
            d.locations.forEach(function (l) {
                var o = document.createElement('option');
                o.value = l;
                o.textContent = l;
                sel.appendChild(o);
            });
            var inp = document.createElement('input');
            inp.type = 'number';
            inp.min = '1';
            inp.step = '1';
            inp.className = 'form-control form-control-sm d-inline-block ms-1';
            inp.style.width = '75px';
            inp.placeholder = '+qty';
            var addB = document.createElement('button');
            addB.type = 'button';
            addB.className = 'btn btn-sm btn-outline-primary ms-1';
            addB.textContent = 'Stock +';
            addB.addEventListener('click', function () {
                var add = Number(inp.value);
                if (!add || add <= 0) { showError('Enter a qty greater than 0.'); return; }
                hideError();
                var dd = load();
                for (var i = 0; i < dd.stock.length; i++) {
                    if (dd.stock[i].name === it.name) {
                        var l = sel.value;
                        dd.stock[i].qtyByLoc = dd.stock[i].qtyByLoc || {};
                        dd.stock[i].qtyByLoc[l] = qtyOf(dd.stock[i], l) + add;
                        break;
                    }
                }
                save(dd);
                render();
            });
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger ms-1';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete item');
            del.addEventListener('click', function () {
                if (!confirm('Delete ' + it.name + ' from the godown record?')) return;
                var dd = load();
                dd.stock = dd.stock.filter(function (x) { return x.name !== it.name; });
                save(dd);
                render();
            });
            tdA.appendChild(sel);
            tdA.appendChild(inp);
            tdA.appendChild(addB);
            tdA.appendChild(del);
            tr.appendChild(tdN);
            tr.appendChild(tdT);
            tr.appendChild(tdA);
            stockRows.appendChild(tr);
        });

        // history
        histEmpty.style.display = (d.history && d.history.length) ? 'none' : '';
        histRows.innerHTML = '';
        (d.history || []).slice().reverse().forEach(function (h) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(h.when) + '</td><td>' + esc(h.item) + '</td><td>' + esc(h.from) +
                '</td><td>' + esc(h.to) + '</td><td class="text-end fw-semibold">' + h.qty + '</td>';
            histRows.appendChild(tr);
        });
    }

    locFilter.addEventListener('change', function () { render(); });

    gdAddLocBtn.addEventListener('click', function () {
        hideError();
        var name = gdName.value.trim();
        if (!name) { showError('Enter a location name.'); return; }
        var d = load();
        var dup = false;
        d.locations.forEach(function (l) { if (l.toLowerCase() === name.toLowerCase()) dup = true; });
        if (dup) { showError('This location already exists.'); return; }
        d.locations.push(name);
        save(d);
        gdName.value = '';
        render();
    });

    gdAddItemBtn.addEventListener('click', function () {
        hideError();
        var name = gdItem.value.trim();
        if (!name) { showError('Enter an item name.'); return; }
        var qty = Number(gdItemQty.value) || 0;
        var d = load();
        var found = null;
        d.stock.forEach(function (it) { if (it.name.toLowerCase() === name.toLowerCase()) found = it; });
        if (found) {
            found.qtyByLoc = found.qtyByLoc || {};
            found.qtyByLoc[gdItemLoc.value] = qtyOf(found, gdItemLoc.value) + qty;
        } else {
            var qb = {};
            qb[gdItemLoc.value] = qty;
            d.stock.push({ name: name, qtyByLoc: qb });
        }
        save(d);
        gdItem.value = '';
        gdItemQty.value = '';
        render();
    });

    trBtn.addEventListener('click', function () {
        hideError();
        var item = trItem.value;
        var from = trFrom.value;
        var to = trTo.value;
        var qty = Number(trQty.value);
        if (!item) { showError('Select an item.'); return; }
        if (from === to) { showError('From and To must be different locations.'); return; }
        if (!qty || qty <= 0) { showError('Enter a qty greater than 0.'); return; }
        var d = load();
        var found = null;
        d.stock.forEach(function (it) { if (it.name === item) found = it; });
        if (!found) { showError('Item not found in stock.'); return; }
        var have = qtyOf(found, from);
        if (have < qty) { showError(from + ' has only ' + have + ' units.'); return; }
        found.qtyByLoc = found.qtyByLoc || {};
        found.qtyByLoc[from] = have - qty;
        found.qtyByLoc[to] = qtyOf(found, to) + qty;
        d.history = d.history || [];
        d.history.push({ when: nowStr(), item: item, from: from, to: to, qty: qty });
        save(d);
        trQty.value = '';
        render();
    });

    histClearBtn.addEventListener('click', function () {
        hideError();
        if (!confirm('Clear the transfer history?')) return;
        var d = load();
        d.history = [];
        save(d);
        render();
    });

    render();
})();
</script>
@endsection
