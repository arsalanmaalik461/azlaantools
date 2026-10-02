@extends('layouts.app')

@section('title', 'Product & Service Price Catalog - Azlaan Tools')
@section('meta_description', 'Save a price list for your products and services — add items to an invoice in one click. Rate card library.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Product &amp; Service Price Catalog</h1>
            <p class="lead text-muted">Save your price list here — name, rate, unit and tax %. Items get added to an invoice in one click. Data is saved only in your browser, nothing is uploaded.</p>

            <div class="row">
                <div class="col-12 col-md-5 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title" id="pcFormTitle">Add new item</h5>
                            <div class="mb-2">
                                <label for="pcName" class="form-label fw-semibold">Item / Service name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pcName" placeholder="e.g. 550W Solar Panel">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label for="pcCategory" class="form-label fw-semibold">Category</label>
                                    <input type="text" class="form-control" id="pcCategory" placeholder="e.g. Solar" list="pcCatList">
                                    <datalist id="pcCatList"></datalist>
                                </div>
                                <div class="col-6">
                                    <label for="pcUnit" class="form-label fw-semibold">Unit</label>
                                    <select class="form-select" id="pcUnit">
                                        <option value="pcs">pcs</option>
                                        <option value="hour">hour</option>
                                        <option value="day">day</option>
                                        <option value="kg">kg</option>
                                        <option value="liter">liter</option>
                                        <option value="meter">meter</option>
                                        <option value="sq ft">sq ft</option>
                                        <option value="service">service</option>
                                        <option value="month">month</option>
                                        <option value="project">project</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label for="pcRate" class="form-label fw-semibold">Rate (Rs) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="pcRate" placeholder="0" min="0" step="0.01">
                                </div>
                                <div class="col-6">
                                    <label for="pcTax" class="form-label fw-semibold">Tax %</label>
                                    <input type="number" class="form-control" id="pcTax" placeholder="0" min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="pcNotes" class="form-label fw-semibold">Notes</label>
                                <input type="text" class="form-control" id="pcNotes" placeholder="optional">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary flex-fill" id="pcSave">Save Item</button>
                                <button type="button" class="btn btn-outline-secondary d-none" id="pcCancel">Cancel</button>
                            </div>
                            <div class="alert alert-danger mt-3 d-none" id="pcError" role="alert"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Price List <span class="badge bg-secondary" id="pcCount">0</span></h5>
                                <div class="d-flex gap-2">
                                    <select class="form-select form-select-sm w-auto" id="pcCatFilter" aria-label="Category filter">
                                        <option value="">All categories</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="pcExport">CSV</button>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">&#128269;</span>
                                <input type="text" class="form-control" id="pcSearch" placeholder="Search items...">
                            </div>
                            <div id="pcList"></div>
                            <p class="text-muted small mt-2 mb-0">This same rate card will be used to select items in <strong>Invoice Maker</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the item name, category, rate and unit, then press <strong>Save Item</strong>. Tax % is optional (default 0).</li>
                <li>Find items with search or the category filter. Use <strong>Edit</strong> to change the rate.</li>
                <li>For backup, download the file with <strong>CSV</strong>.</li>
            </ol>
            <p class="small text-muted">Data is saved only in your browser, nothing is uploaded. If you clear browser data the records will be lost — keep taking CSV backups.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan_price_catalog';
    var pcName = document.getElementById('pcName');
    var pcCategory = document.getElementById('pcCategory');
    var pcCatList = document.getElementById('pcCatList');
    var pcUnit = document.getElementById('pcUnit');
    var pcRate = document.getElementById('pcRate');
    var pcTax = document.getElementById('pcTax');
    var pcNotes = document.getElementById('pcNotes');
    var pcSave = document.getElementById('pcSave');
    var pcCancel = document.getElementById('pcCancel');
    var pcError = document.getElementById('pcError');
    var pcList = document.getElementById('pcList');
    var pcSearch = document.getElementById('pcSearch');
    var pcCount = document.getElementById('pcCount');
    var pcExport = document.getElementById('pcExport');
    var pcCatFilter = document.getElementById('pcCatFilter');
    var pcFormTitle = document.getElementById('pcFormTitle');

    var items = [];
    var editingId = null;

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) items = p;
            }
        } catch (e) { items = []; }
    }
    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(items)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function uid() {
        return 'pi' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function showError(msg) {
        pcError.textContent = msg;
        pcError.classList.remove('d-none');
    }
    function hideError() {
        pcError.classList.add('d-none');
        pcError.textContent = '';
    }
    function resetForm() {
        pcName.value = '';
        pcCategory.value = '';
        pcUnit.value = 'pcs';
        pcRate.value = '';
        pcTax.value = '';
        pcNotes.value = '';
        editingId = null;
        pcSave.textContent = 'Save Item';
        pcFormTitle.textContent = 'Add new item';
        pcCancel.classList.add('d-none');
    }
    function getItem(id) {
        for (var i = 0; i < items.length; i++) {
            if (items[i].id === id) return items[i];
        }
        return null;
    }
    function categories() {
        var seen = {};
        var out = [];
        items.forEach(function (it) {
            var c = (it.category || 'General').trim() || 'General';
            if (!seen[c]) { seen[c] = true; out.push(c); }
        });
        out.sort();
        return out;
    }
    function refreshCatControls() {
        var cats = categories();
        var cur = pcCatFilter.value;
        pcCatFilter.innerHTML = '<option value="">All categories</option>';
        pcCatList.innerHTML = '';
        cats.forEach(function (c) {
            var opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            pcCatFilter.appendChild(opt);
            var opt2 = document.createElement('option');
            opt2.value = c;
            pcCatList.appendChild(opt2);
        });
        if (cats.indexOf(cur) !== -1) pcCatFilter.value = cur;
    }

    function render() {
        hideError();
        refreshCatControls();
        var q = pcSearch.value.trim().toLowerCase();
        var catF = pcCatFilter.value;
        pcCount.textContent = items.length;
        pcList.innerHTML = '';
        if (!items.length) {
            pcList.innerHTML = '<div class="text-muted small p-3">No items yet. Add the first item from the form.</div>';
            return;
        }
        var filtered = items.filter(function (it) {
            var cat = (it.category || 'General').trim() || 'General';
            if (catF && cat !== catF) return false;
            if (!q) return true;
            return (it.name + ' ' + cat + ' ' + (it.notes || '')).toLowerCase().indexOf(q) !== -1;
        });
        if (!filtered.length) {
            pcList.innerHTML = '<div class="text-muted small p-3">No item found for this search/filter.</div>';
            return;
        }
        var groups = {};
        filtered.forEach(function (it) {
            var cat = (it.category || 'General').trim() || 'General';
            if (!groups[cat]) groups[cat] = [];
            groups[cat].push(it);
        });
        Object.keys(groups).sort().forEach(function (cat) {
            var head = document.createElement('h6');
            head.className = 'mt-3 mb-1 text-primary';
            head.textContent = cat + ' (' + groups[cat].length + ')';
            pcList.appendChild(head);
            var lg = document.createElement('div');
            lg.className = 'list-group mb-2';
            groups[cat].forEach(function (it) {
                var row = document.createElement('div');
                row.className = 'list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2';
                var left = document.createElement('div');
                var nameEl = document.createElement('strong');
                nameEl.textContent = it.name;
                left.appendChild(nameEl);
                var sub = document.createElement('div');
                sub.className = 'small text-muted';
                var subTxt = 'Rs ' + fmt(it.rate) + ' / ' + it.unit;
                if (Number(it.taxPct) > 0) subTxt += ' + ' + it.taxPct + '% tax';
                if (it.notes) subTxt += ' — ' + it.notes;
                sub.textContent = subTxt;
                left.appendChild(sub);
                var right = document.createElement('div');
                right.className = 'd-flex gap-1';
                var copyBtn = document.createElement('button');
                copyBtn.type = 'button';
                copyBtn.className = 'btn btn-sm btn-outline-secondary';
                copyBtn.textContent = 'Copy Rate';
                copyBtn.addEventListener('click', function () {
                    var txt = it.name + ' — Rs ' + fmt(it.rate) + ' / ' + it.unit;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            copyBtn.textContent = 'Copied!';
                            setTimeout(function () { copyBtn.textContent = 'Copy Rate'; }, 1500);
                        }, function () { showError('Could not copy.'); });
                    } else { showError('Could not copy.'); }
                });
                var editBtn = document.createElement('button');
                editBtn.type = 'button';
                editBtn.className = 'btn btn-sm btn-outline-primary';
                editBtn.textContent = 'Edit';
                editBtn.addEventListener('click', function () { startEdit(it.id); });
                var delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'btn btn-sm btn-outline-danger';
                delBtn.textContent = 'Delete';
                delBtn.addEventListener('click', function () { deleteItem(it.id); });
                right.appendChild(copyBtn);
                right.appendChild(editBtn);
                right.appendChild(delBtn);
                row.appendChild(left);
                row.appendChild(right);
                lg.appendChild(row);
            });
            pcList.appendChild(lg);
        });
    }

    function startEdit(id) {
        var it = getItem(id);
        if (!it) return;
        editingId = id;
        pcName.value = it.name || '';
        pcCategory.value = it.category || '';
        pcUnit.value = it.unit || 'pcs';
        pcRate.value = it.rate != null ? it.rate : '';
        pcTax.value = it.taxPct != null ? it.taxPct : '';
        pcNotes.value = it.notes || '';
        pcSave.textContent = 'Update';
        pcFormTitle.textContent = 'Edit item';
        pcCancel.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function deleteItem(id) {
        var it = getItem(id);
        if (!it) return;
        if (!confirm('Delete ' + it.name + ' from the catalog?')) return;
        items = items.filter(function (x) { return x.id !== id; });
        if (editingId === id) resetForm();
        save();
        render();
    }

    pcSave.addEventListener('click', function () {
        hideError();
        var name = pcName.value.trim();
        if (!name) { showError('Item name is required.'); pcName.focus(); return; }
        var rate = parseFloat(pcRate.value);
        if (isNaN(rate) || rate < 0) { showError('Enter a valid rate (0 or more).'); pcRate.focus(); return; }
        var tax = parseFloat(pcTax.value);
        if (isNaN(tax) || tax < 0) tax = 0;
        if (tax > 100) { showError('Tax % cannot be more than 100.'); pcTax.focus(); return; }
        var entry = {
            id: editingId || uid(),
            name: name,
            category: pcCategory.value.trim() || 'General',
            unit: pcUnit.value,
            rate: Math.round(rate * 100) / 100,
            taxPct: Math.round(tax * 100) / 100,
            notes: pcNotes.value.trim(),
            createdAt: new Date().toISOString()
        };
        if (editingId) {
            var it = getItem(editingId);
            if (it) {
                it.name = entry.name;
                it.category = entry.category;
                it.unit = entry.unit;
                it.rate = entry.rate;
                it.taxPct = entry.taxPct;
                it.notes = entry.notes;
            }
        } else {
            items.unshift(entry);
        }
        save();
        resetForm();
        render();
    });

    pcCancel.addEventListener('click', function () {
        hideError();
        resetForm();
    });

    pcSearch.addEventListener('input', render);
    pcCatFilter.addEventListener('change', render);

    pcExport.addEventListener('click', function () {
        hideError();
        if (!items.length) { showError('Add an item first before export.'); return; }
        var rows = ['Name,Category,Rate,Unit,TaxPct,Notes'];
        items.forEach(function (it) {
            var cols = [it.name, it.category, it.rate, it.unit, it.taxPct, it.notes || ''];
            rows.push(cols.map(function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(','));
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'price-catalog.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    load();
    render();
})();
</script>
@endsection
