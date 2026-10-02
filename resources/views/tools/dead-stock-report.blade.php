@extends('layouts.app')

@section('title', 'Dead Stock Report - Azlaan Tools')
@section('meta_description', 'Find items that have not sold for months — dead stock report for clearance planning.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Dead Stock Report</h1>
            <p class="lead text-muted">Find the items that have not sold for months. Enter the last sale date for each item — the report builds itself. Your data stays in your browser only and is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row text-center g-2 mb-3">
                        <div class="col-4">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">30+ days</small>
                                <div class="fw-bold text-warning" id="stat30">0</div>
                            </div></div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">60+ days</small>
                                <div class="fw-bold" style="color:#e65100" id="stat60">0</div>
                            </div></div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">90+ days (Dead)</small>
                                <div class="fw-bold text-danger" id="stat90">0</div>
                            </div></div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Filter">
                            <button type="button" class="btn btn-outline-secondary active" id="fltAll">All</button>
                            <button type="button" class="btn btn-outline-secondary" id="flt30">30+ days</button>
                            <button type="button" class="btn btn-outline-secondary" id="flt60">60+ days</button>
                            <button type="button" class="btn btn-outline-secondary" id="flt90">90+ days</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success ms-auto" id="csvBtn">CSV Download</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Item</th><th class="text-end">Qty</th><th>Last Sale</th><th class="text-end">Days Since</th><th>Status</th><th class="text-end">Action</th></tr>
                            </thead>
                            <tbody id="rows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="emptyMsg">No items. Add items to your inventory (from the Low Stock Alert List) and this report will build itself.</p>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the <strong>last sale date</strong> for each item — the day count is calculated for you.</li>
                <li>When an item sells, press <strong>Sold Today</strong> — the date will be set to today.</li>
                <li>Items unsold for 90+ days are <strong>dead stock</strong> — clear them with a discount.</li>
            </ol>
            <p class="small text-muted">Note: items come from your Inventory (Low Stock Alert List); last-sale dates are saved separately.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var INV_KEY = 'azlaan7_inventory';
    var KEY = 'azlaan7_deadstock';
    var rows = document.getElementById('rows');
    var emptyMsg = document.getElementById('emptyMsg');
    var errorBox = document.getElementById('errorBox');
    var csvBtn = document.getElementById('csvBtn');
    var stat30 = document.getElementById('stat30');
    var stat60 = document.getElementById('stat60');
    var stat90 = document.getElementById('stat90');
    var fltAll = document.getElementById('fltAll');
    var flt30 = document.getElementById('flt30');
    var flt60 = document.getElementById('flt60');
    var flt90 = document.getElementById('flt90');
    var filter = 0;

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
    function loadMap() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (p && typeof p === 'object') return p;
            }
        } catch (e) {}
        return {};
    }
    function saveMap(m) {
        try { localStorage.setItem(KEY, JSON.stringify(m)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
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
    function daysSince(dateStr) {
        if (!dateStr) return null;
        var d = new Date(dateStr + 'T00:00:00');
        if (isNaN(d.getTime())) return null;
        var now = new Date();
        now.setHours(0, 0, 0, 0);
        return Math.floor((now - d) / 86400000);
    }
    function todayStr() {
        var d = new Date();
        var m = d.getMonth() + 1;
        var day = d.getDate();
        return d.getFullYear() + '-' + (m < 10 ? '0' + m : m) + '-' + (day < 10 ? '0' + day : day);
    }
    function statusOf(days) {
        if (days === null) return { label: 'No sale date', cls: 'bg-secondary' };
        if (days >= 90) return { label: 'DEAD (90+)', cls: 'bg-danger' };
        if (days >= 60) return { label: 'Slow (60+)', cls: 'bg-warning text-dark' };
        if (days >= 30) return { label: 'Low (30+)', cls: 'bg-info text-dark' };
        return { label: 'Active', cls: 'bg-success' };
    }

    function setFilter(btn, n) {
        [fltAll, flt30, flt60, flt90].forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        filter = n;
        render();
    }
    fltAll.addEventListener('click', function () { setFilter(fltAll, 0); });
    flt30.addEventListener('click', function () { setFilter(flt30, 30); });
    flt60.addEventListener('click', function () { setFilter(flt60, 60); });
    flt90.addEventListener('click', function () { setFilter(flt90, 90); });

    function render() {
        hideError();
        var items = loadInv();
        var map = loadMap();
        var c30 = 0, c60 = 0, c90 = 0;
        items.forEach(function (it) {
            var days = daysSince(map[it.id] || '');
            if (days !== null && days >= 90) c90++;
            else if (days !== null && days >= 60) c60++;
            else if (days !== null && days >= 30) c30++;
        });
        stat30.textContent = c30;
        stat60.textContent = c60;
        stat90.textContent = c90;
        emptyMsg.style.display = items.length ? 'none' : '';

        rows.innerHTML = '';
        var shown = items.filter(function (it) {
            if (filter === 0) return true;
            var days = daysSince(map[it.id] || '');
            return days !== null && days >= filter;
        });
        shown.sort(function (a, b) {
            var da = daysSince(map[a.id] || '');
            var db = daysSince(map[b.id] || '');
            da = da === null ? 9999 : da;
            db = db === null ? 9999 : db;
            return db - da;
        });
        shown.forEach(function (it) {
            var dateVal = map[it.id] || '';
            var days = daysSince(dateVal);
            var st = statusOf(days);
            var tr = document.createElement('tr');
            var tdN = document.createElement('td');
            tdN.innerHTML = '<span class="fw-semibold">' + esc(it.name) + '</span>';
            var tdQ = document.createElement('td');
            tdQ.className = 'text-end';
            tdQ.textContent = it.qty;
            var tdD = document.createElement('td');
            var dateInp = document.createElement('input');
            dateInp.type = 'date';
            dateInp.className = 'form-control form-control-sm';
            dateInp.style.width = '150px';
            dateInp.value = dateVal;
            dateInp.setAttribute('aria-label', it.name + ' last sale date');
            dateInp.addEventListener('change', function () {
                var m = loadMap();
                if (dateInp.value) m[it.id] = dateInp.value;
                else delete m[it.id];
                saveMap(m);
                render();
            });
            tdD.appendChild(dateInp);
            var tdDays = document.createElement('td');
            tdDays.className = 'text-end fw-semibold';
            tdDays.textContent = days === null ? '—' : days + ' days';
            var tdS = document.createElement('td');
            tdS.innerHTML = '<span class="badge ' + st.cls + '">' + st.label + '</span>';
            var tdA = document.createElement('td');
            tdA.className = 'text-end';
            var soldBtn = document.createElement('button');
            soldBtn.type = 'button';
            soldBtn.className = 'btn btn-sm btn-outline-success';
            soldBtn.textContent = 'Sold Today';
            soldBtn.addEventListener('click', function () {
                var m = loadMap();
                m[it.id] = todayStr();
                saveMap(m);
                render();
            });
            tdA.appendChild(soldBtn);
            tr.appendChild(tdN); tr.appendChild(tdQ); tr.appendChild(tdD);
            tr.appendChild(tdDays); tr.appendChild(tdS); tr.appendChild(tdA);
            rows.appendChild(tr);
        });
    }

    csvBtn.addEventListener('click', function () {
        hideError();
        var items = loadInv();
        if (!items.length) { showError('Please add items to your inventory first.'); return; }
        var map = loadMap();
        var lines = ['Item,Qty,Last Sale Date,Days Since Sale,Status'];
        items.forEach(function (it) {
            var dateVal = map[it.id] || '';
            var days = daysSince(dateVal);
            var st = statusOf(days);
            var name = '"' + String(it.name).replace(/"/g, '""') + '"';
            lines.push([name, it.qty, dateVal || 'not set', days === null ? '' : days, st.label].join(','));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'dead-stock-report.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    render();
})();
</script>
@endsection
