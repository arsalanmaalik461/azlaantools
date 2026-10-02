@extends('layouts.app')

@section('title', 'Best Seller Report - Azlaan Tools')
@section('meta_description', 'A top-list report of your best-selling items — ranking by units sold and revenue.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Best Seller Report</h1>
            <p class="lead text-muted">A top list of your best-selling items. Enter the sold qty and revenue for each item — the ranking is built automatically. Data is saved only in your browser, nothing is uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row text-center g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Items Tracked</small>
                                <div class="fw-bold" id="statItems">0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Total Units Sold</small>
                                <div class="fw-bold" id="statUnits">0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Total Revenue</small>
                                <div class="fw-bold text-success" id="statRev">Rs 0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Top Item Share</small>
                                <div class="fw-bold" id="statShare">0%</div>
                            </div></div>
                        </div>
                    </div>

                    <h2 class="h5 mb-3">Add sales record</h2>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-5">
                            <label for="bsItem" class="form-label fw-semibold">Item</label>
                            <select class="form-select" id="bsItem"></select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="bsUnits" class="form-label fw-semibold">Units sold</label>
                            <input type="number" class="form-control" id="bsUnits" placeholder="0" min="1" step="1">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="bsRev" class="form-label fw-semibold">Revenue (Rs)</label>
                            <input type="number" class="form-control" id="bsRev" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="row g-2 mb-3 d-none" id="manualRow">
                        <div class="col-12">
                            <label for="bsManual" class="form-label fw-semibold">Item name (manual)</label>
                            <input type="text" class="form-control" id="bsManual" placeholder="e.g. Surf Excel 1kg">
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="bsAddBtn">Add Sales</button>
                        <button type="button" class="btn btn-outline-secondary" id="bsClearBtn">Clear Report</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">Top sellers (by units)</h2>
                    <div id="chartWrap"></div>
                    <p class="small text-muted" id="emptyMsg">No sales records yet. Add from above — the top list will appear here.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select an item (or type a name manually), enter the sold <strong>units</strong> and <strong>revenue</strong>, then add it.</li>
                <li>Each time you add, the item total grows — ranking is by <strong>units sold</strong>.</li>
                <li><strong>Top item share</strong> shows what % of total sales your best-selling item makes up.</li>
            </ol>
            <p class="small text-muted">Item names come automatically from inventory; manual entries also work.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var INV_KEY = 'azlaan7_inventory';
    var KEY = 'azlaan7_bestsellers';
    var bsItem = document.getElementById('bsItem');
    var bsUnits = document.getElementById('bsUnits');
    var bsRev = document.getElementById('bsRev');
    var bsManual = document.getElementById('bsManual');
    var manualRow = document.getElementById('manualRow');
    var bsAddBtn = document.getElementById('bsAddBtn');
    var bsClearBtn = document.getElementById('bsClearBtn');
    var errorBox = document.getElementById('errorBox');
    var chartWrap = document.getElementById('chartWrap');
    var emptyMsg = document.getElementById('emptyMsg');
    var statItems = document.getElementById('statItems');
    var statUnits = document.getElementById('statUnits');
    var statRev = document.getElementById('statRev');
    var statShare = document.getElementById('statShare');

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
    function save(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function fillItems() {
        var inv = loadInv();
        bsItem.innerHTML = '';
        inv.forEach(function (it) {
            var o = document.createElement('option');
            o.value = it.id;
            o.textContent = it.name;
            bsItem.appendChild(o);
        });
        var o2 = document.createElement('option');
        o2.value = '__manual';
        o2.textContent = '— Type a new name —';
        bsItem.appendChild(o2);
        manualRow.classList.add('d-none');
    }
    bsItem.addEventListener('change', function () {
        manualRow.classList.toggle('d-none', bsItem.value !== '__manual');
    });

    function render() {
        hideError();
        var data = load();
        data.sort(function (a, b) { return Number(b.units || 0) - Number(a.units || 0); });
        var totUnits = 0, totRev = 0;
        data.forEach(function (d) {
            totUnits += Number(d.units || 0);
            totRev += Number(d.revenue || 0);
        });
        statItems.textContent = data.length;
        statUnits.textContent = totUnits.toLocaleString('en-PK');
        statRev.textContent = fmt(totRev);
        statShare.textContent = (data.length && totUnits) ? Math.round((Number(data[0].units || 0) / totUnits) * 100) + '%' : '0%';
        emptyMsg.style.display = data.length ? 'none' : '';

        chartWrap.innerHTML = '';
        var maxUnits = data.length ? Number(data[0].units || 1) : 1;
        data.forEach(function (d, i) {
            var row = document.createElement('div');
            row.className = 'mb-3';
            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-center mb-1';
            head.innerHTML = '<span class="fw-semibold">' + (i + 1) + '. ' + esc(d.name) + '</span>' +
                '<span class="small text-muted">' + Number(d.units || 0).toLocaleString('en-PK') + ' units &middot; ' + fmt(d.revenue) + '</span>';
            var barBg = document.createElement('div');
            barBg.className = 'bg-light rounded';
            barBg.style.height = '22px';
            barBg.style.overflow = 'hidden';
            var bar = document.createElement('div');
            bar.className = 'rounded text-white small d-flex align-items-center ps-2';
            bar.style.height = '100%';
            bar.style.width = Math.max(3, Math.round((Number(d.units || 0) / maxUnits) * 100)) + '%';
            bar.style.background = i === 0 ? '#198754' : (i < 3 ? '#0d6efd' : '#6c757d');
            bar.textContent = i === 0 ? 'TOP' : (Math.round((Number(d.units || 0) / maxUnits) * 100) + '%');
            barBg.appendChild(bar);
            row.appendChild(head);
            row.appendChild(barBg);
            chartWrap.appendChild(row);
        });
    }

    bsAddBtn.addEventListener('click', function () {
        hideError();
        var units = Number(bsUnits.value);
        var rev = Number(bsRev.value) || 0;
        if (!units || units <= 0) { showError('Enter units sold greater than 0.'); return; }
        var name;
        if (bsItem.value === '__manual') {
            name = bsManual.value.trim();
            if (!name) { showError('Enter the item name.'); return; }
        } else {
            var opt = bsItem.options[bsItem.selectedIndex];
            name = opt ? opt.textContent : '';
            if (!name) { showError('Select an item.'); return; }
        }
        var data = load();
        var found = null;
        data.forEach(function (d) {
            if (String(d.name).toLowerCase() === name.toLowerCase()) found = d;
        });
        if (found) {
            found.units = Number(found.units || 0) + units;
            found.revenue = Number(found.revenue || 0) + rev;
        } else {
            data.push({ name: name, units: units, revenue: rev });
        }
        save(data);
        bsUnits.value = '';
        bsRev.value = '';
        bsManual.value = '';
        fillItems();
        render();
    });

    bsClearBtn.addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all best seller report data?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        render();
    });

    fillItems();
    render();
})();
</script>
@endsection
