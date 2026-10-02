@extends('layouts.app')

@section('title', 'Supplier Quotation Comparison - Azlaan Tools')
@section('meta_description', 'Compare rates of two or more suppliers in one sheet — best rate for each item and each supplier total automatic.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Supplier Quotation Comparison</h1>
            <p class="lead text-muted">Compare rates of two or more suppliers in one sheet. Each item's best rate is highlighted in green. The data is saved only in your browser and is never uploaded.</p>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Suppliers</h5>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="supName" placeholder="Supplier name">
                                <button type="button" class="btn btn-primary" id="addSupBtn">Add</button>
                            </div>
                            <div id="supList" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Items</h5>
                            <div class="row g-2 mb-2">
                                <div class="col-7">
                                    <input type="text" class="form-control" id="itemName" placeholder="Item name">
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control" id="itemUnit" placeholder="Unit (kg/pcs)">
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-primary w-100" id="addItemBtn">Add</button>
                                </div>
                            </div>
                            <div id="itemList" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Rate sheet <small class="text-muted">(rates are saved and compared as you type)</small></h5>
                    <div class="alert alert-danger d-none" id="qcError" role="alert"></div>
                    <div id="bestBanner" class="alert alert-success d-none"></div>
                    <div class="table-responsive" id="gridWrap">
                        <table class="table table-bordered table-sm align-middle" id="gridTable">
                            <thead class="table-light" id="gridHead"></thead>
                            <tbody id="gridBody"></tbody>
                            <tfoot id="gridFoot"></tfoot>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="gridEmpty">First add at least 1 supplier and 1 item — then the rate sheet will appear here.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add suppliers and items.</li>
                <li>Type the rate for each item in each supplier's column — leave it blank if you have no rate.</li>
                <li>Each item's <strong class="text-success">cheapest rate</strong> will automatically turn green, and each supplier's total will appear below.</li>
            </ol>
            <p class="text-muted small">Note: the data stays safe in your browser. The comparison only covers items that have a rate.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_quote_compare';
    var supName = document.getElementById('supName');
    var addSupBtn = document.getElementById('addSupBtn');
    var supList = document.getElementById('supList');
    var itemName = document.getElementById('itemName');
    var itemUnit = document.getElementById('itemUnit');
    var addItemBtn = document.getElementById('addItemBtn');
    var itemList = document.getElementById('itemList');
    var gridWrap = document.getElementById('gridWrap');
    var gridHead = document.getElementById('gridHead');
    var gridBody = document.getElementById('gridBody');
    var gridFoot = document.getElementById('gridFoot');
    var gridEmpty = document.getElementById('gridEmpty');
    var bestBanner = document.getElementById('bestBanner');
    var qcError = document.getElementById('qcError');

    var data = { suppliers: [], items: [], prices: {} };
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) { var p = JSON.parse(raw); if (p && Array.isArray(p.suppliers) && Array.isArray(p.items)) data = p; }
    } catch (e) {}

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid(prefix) {
        return prefix + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36);
    }
    function showError(msg) {
        qcError.textContent = msg;
        qcError.classList.remove('d-none');
    }
    function hideError() {
        qcError.classList.add('d-none');
        qcError.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }
    function priceOf(itemId, supId) {
        if (data.prices[itemId] && typeof data.prices[itemId][supId] === 'number') {
            return data.prices[itemId][supId];
        }
        return null;
    }
    function setPrice(itemId, supId, val) {
        if (!data.prices[itemId]) data.prices[itemId] = {};
        if (val === null) {
            delete data.prices[itemId][supId];
        } else {
            data.prices[itemId][supId] = val;
        }
    }
    function bestForItem(itemId) {
        var best = null, bestSup = null;
        data.suppliers.forEach(function (s) {
            var pr = priceOf(itemId, s.id);
            if (pr !== null && (best === null || pr < best)) { best = pr; bestSup = s; }
        });
        return { price: best, supplier: bestSup };
    }

    function renderSuppliers() {
        supList.innerHTML = '';
        data.suppliers.forEach(function (s) {
            var chip = document.createElement('span');
            chip.className = 'badge bg-primary fs-6 fw-normal d-inline-flex align-items-center gap-2';
            var nm = document.createElement('span');
            nm.textContent = s.name;
            var x = document.createElement('button');
            x.type = 'button';
            x.className = 'btn-close btn-close-white';
            x.style.fontSize = '10px';
            x.setAttribute('aria-label', 'Delete supplier');
            x.addEventListener('click', function () {
                if (!confirm('Delete ' + s.name + '? Its rates will also be removed.')) return;
                data.suppliers = data.suppliers.filter(function (a) { return a.id !== s.id; });
                Object.keys(data.prices).forEach(function (iid) { delete data.prices[iid][s.id]; });
                save(); renderAll();
            });
            chip.appendChild(nm);
            chip.appendChild(x);
            supList.appendChild(chip);
        });
        if (!data.suppliers.length) supList.innerHTML = '<span class="text-muted small">No suppliers.</span>';
    }

    function renderItems() {
        itemList.innerHTML = '';
        data.items.forEach(function (it) {
            var chip = document.createElement('span');
            chip.className = 'badge bg-secondary fs-6 fw-normal d-inline-flex align-items-center gap-2';
            var nm = document.createElement('span');
            nm.textContent = it.name + (it.unit ? ' (' + it.unit + ')' : '');
            var x = document.createElement('button');
            x.type = 'button';
            x.className = 'btn-close btn-close-white';
            x.style.fontSize = '10px';
            x.setAttribute('aria-label', 'Delete item');
            x.addEventListener('click', function () {
                if (!confirm('Delete ' + it.name + '? Its rates will also be removed.')) return;
                data.items = data.items.filter(function (a) { return a.id !== it.id; });
                delete data.prices[it.id];
                save(); renderAll();
            });
            chip.appendChild(nm);
            chip.appendChild(x);
            itemList.appendChild(chip);
        });
        if (!data.items.length) itemList.innerHTML = '<span class="text-muted small">No items.</span>';
    }

    function renderGrid() {
        hideError();
        gridHead.innerHTML = '';
        gridBody.innerHTML = '';
        gridFoot.innerHTML = '';
        bestBanner.classList.add('d-none');
        var hasGrid = data.suppliers.length && data.items.length;
        gridEmpty.style.display = hasGrid ? 'none' : '';
        if (!hasGrid) return;

        var tr = document.createElement('tr');
        var th0 = document.createElement('th');
        th0.textContent = 'Item';
        tr.appendChild(th0);
        data.suppliers.forEach(function (s) {
            var th = document.createElement('th');
            th.textContent = s.name;
            tr.appendChild(th);
        });
        var thB = document.createElement('th');
        thB.textContent = 'Best rate';
        thB.className = 'text-success';
        tr.appendChild(thB);
        gridHead.appendChild(tr);

        var totals = {};
        data.suppliers.forEach(function (s) { totals[s.id] = 0; });
        var counts = {};
        data.suppliers.forEach(function (s) { counts[s.id] = 0; });

        data.items.forEach(function (it) {
            var best = bestForItem(it.id);
            var row = document.createElement('tr');
            var td0 = document.createElement('td');
            td0.innerHTML = '<strong>' + esc(it.name) + '</strong>' + (it.unit ? '<br><small class="text-muted">' + esc(it.unit) + '</small>' : '');
            row.appendChild(td0);
            data.suppliers.forEach(function (s) {
                var td = document.createElement('td');
                var inp = document.createElement('input');
                inp.type = 'number';
                inp.min = '0';
                inp.step = '0.01';
                inp.className = 'form-control form-control-sm';
                inp.style.minWidth = '90px';
                var cur = priceOf(it.id, s.id);
                if (cur !== null) inp.value = cur;
                inp.setAttribute('aria-label', it.name + ' rate - ' + s.name);
                if (best.price !== null && cur !== null && cur === best.price) {
                    td.classList.add('table-success');
                    inp.classList.add('border', 'border-success', 'fw-bold');
                }
                inp.addEventListener('change', function () {
                    hideError();
                    var v = inp.value.trim();
                    if (v === '') {
                        setPrice(it.id, s.id, null);
                    } else {
                        var num = parseFloat(v);
                        if (isNaN(num) || num < 0) { showError('The rate must be 0 or more.'); renderGrid(); return; }
                        setPrice(it.id, s.id, Math.round(num * 100) / 100);
                    }
                    save();
                    renderGrid();
                });
                td.appendChild(inp);
                row.appendChild(td);
                if (cur !== null) { totals[s.id] += cur; counts[s.id]++; }
            });
            var tdB = document.createElement('td');
            if (best.price !== null) {
                tdB.innerHTML = '<span class="fw-bold text-success">' + fmt(best.price) + '</span><br><small class="text-muted">' + esc(best.supplier.name) + '</small>';
            } else {
                tdB.innerHTML = '<span class="text-muted">—</span>';
            }
            row.appendChild(tdB);
            gridBody.appendChild(row);
        });

        var ftr = document.createElement('tr');
        ftr.className = 'table-light fw-bold';
        var ftd0 = document.createElement('td');
        ftd0.textContent = 'Total';
        ftr.appendChild(ftd0);
        var bestTotal = null, bestTotalSup = null;
        data.suppliers.forEach(function (s) {
            var td = document.createElement('td');
            if (counts[s.id]) {
                td.textContent = fmt(totals[s.id]);
                if (bestTotal === null || totals[s.id] < bestTotal) { bestTotal = totals[s.id]; bestTotalSup = s; }
            } else {
                td.innerHTML = '<span class="text-muted">—</span>';
            }
            ftr.appendChild(td);
        });
        var ftdB = document.createElement('td');
        ftdB.textContent = '';
        ftr.appendChild(ftdB);
        gridFoot.appendChild(ftr);

        if (bestTotalSup) {
            bestBanner.classList.remove('d-none');
            bestBanner.innerHTML = 'Cheapest total: <strong>' + esc(bestTotalSup.name) + '</strong> — ' + fmt(bestTotal) + ' (based on rates of ' + counts[bestTotalSup.id] + ' items).';
        }
    }

    function renderAll() {
        renderSuppliers();
        renderItems();
        renderGrid();
    }

    addSupBtn.addEventListener('click', function () {
        hideError();
        var name = supName.value.trim();
        if (!name) { showError('Please type the supplier name.'); return; }
        data.suppliers.push({ id: uid('s'), name: name });
        save();
        supName.value = '';
        supName.focus();
        renderAll();
    });
    supName.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addSupBtn.click(); }
    });

    addItemBtn.addEventListener('click', function () {
        hideError();
        var name = itemName.value.trim();
        if (!name) { showError('Please type the item name.'); return; }
        data.items.push({ id: uid('i'), name: name, unit: itemUnit.value.trim() });
        save();
        itemName.value = '';
        itemUnit.value = '';
        itemName.focus();
        renderAll();
    });
    itemName.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addItemBtn.click(); }
    });

    renderAll();
})();
</script>
@endsection
