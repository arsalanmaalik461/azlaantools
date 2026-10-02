@extends('layouts.app')

@section('title', 'Split Transaction - Azlaan Tools')
@section('meta_description', 'Split one receipt across multiple categories — with live sum validation. Free split transaction tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Split Transaction</h1>
            <p class="lead text-muted">Split one receipt across multiple categories — the total is checked live. Data is saved only in your browser, it is not uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Receipt details</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="stTitle" class="form-label fw-semibold">Receipt name</label>
                            <input type="text" class="form-control" id="stTitle" placeholder="e.g. Supermarket shopping">
                        </div>
                        <div class="col-md-6">
                            <label for="stTotal" class="form-label fw-semibold">Receipt grand total (Rs)</label>
                            <input type="number" class="form-control" id="stTotal" placeholder="0" min="0.01" step="0.01">
                        </div>
                    </div>

                    <h2 class="h5 mb-3">Splits</h2>
                    <div id="stRows"></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="stAddRow">+ Add split</button>

                    <div class="row g-2 text-center mb-3">
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Receipt Total</div><div class="fw-bold" id="stTotalBox">Rs 0</div></div></div>
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Splits Sum</div><div class="fw-bold" id="stSumBox">Rs 0</div></div></div>
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Remaining (difference)</div><div class="fw-bold" id="stDiffBox">Rs 0</div></div></div>
                    </div>
                    <div class="alert alert-success d-none" id="stOk" role="status">Good — the splits equal the receipt total. You can save.</div>
                    <div class="alert alert-danger d-none" id="stError" role="alert"></div>

                    <button type="button" class="btn btn-primary w-100" id="stSave">Save Split</button>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Saved splits</h2>
                    <div id="stSaved" class="accordion"></div>
                    <p class="text-muted small mb-0 mt-2" id="stSavedEmpty">Nothing saved yet.</p>
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="stClear">Clear all saved</button>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the receipt name and <strong>grand total</strong>.</li>
                <li>Add a line for each category: item name, category, amount.</li>
                <li>When <strong>remaining is zero</strong>, save — the sum must equal the receipt.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_splits';
    var CATS = ['Home expenses', 'Food', 'Transport', 'Bills', 'Health', 'Education', 'Shopping', 'Other'];

    var stTitle = document.getElementById('stTitle');
    var stTotal = document.getElementById('stTotal');
    var stRows = document.getElementById('stRows');
    var stAddRow = document.getElementById('stAddRow');
    var stTotalBox = document.getElementById('stTotalBox');
    var stSumBox = document.getElementById('stSumBox');
    var stDiffBox = document.getElementById('stDiffBox');
    var stOk = document.getElementById('stOk');
    var stError = document.getElementById('stError');
    var stSave = document.getElementById('stSave');
    var stSaved = document.getElementById('stSaved');
    var stSavedEmpty = document.getElementById('stSavedEmpty');
    var stClear = document.getElementById('stClear');

    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function round2(n) { return Math.round(n * 100) / 100; }
    function load() {
        try { var r = localStorage.getItem(KEY); if (r) { var p = JSON.parse(r); if (Array.isArray(p)) return p; } } catch (e) {}
        return [];
    }
    function save(arr) { try { localStorage.setItem(KEY, JSON.stringify(arr)); } catch (e) {} }

    function makeRow(name, cat, amt) {
        var div = document.createElement('div');
        div.className = 'row g-2 mb-2 split-row';
        div.innerHTML =
            '<div class="col-4"><input type="text" class="form-control form-control-sm split-name" placeholder="Item (e.g. milk)" value="' + esc(name || '') + '"></div>' +
            '<div class="col-4"><select class="form-select form-select-sm split-cat">' +
            CATS.map(function (c) { return '<option value="' + esc(c) + '"' + (c === cat ? ' selected' : '') + '>' + esc(c) + '</option>'; }).join('') +
            '</select></div>' +
            '<div class="col-3"><input type="number" class="form-control form-control-sm split-amt" placeholder="Rs" min="0" step="0.01" value="' + (amt || '') + '"></div>' +
            '<div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline-danger split-del" aria-label="Remove">×</button></div>';
        div.querySelector('.split-del').addEventListener('click', function () {
            if (stRows.querySelectorAll('.split-row').length > 1) { div.remove(); recalc(); }
        });
        div.querySelector('.split-amt').addEventListener('input', recalc);
        div.querySelector('.split-name').addEventListener('input', recalc);
        div.querySelector('.split-cat').addEventListener('change', recalc);
        return div;
    }

    function readRows() {
        var rows = [];
        stRows.querySelectorAll('.split-row').forEach(function (div) {
            var amt = Number(div.querySelector('.split-amt').value) || 0;
            rows.push({
                name: div.querySelector('.split-name').value.trim(),
                category: div.querySelector('.split-cat').value,
                amount: round2(amt)
            });
        });
        return rows;
    }

    function recalc() {
        var total = Number(stTotal.value) || 0;
        var rows = readRows();
        var sum = rows.reduce(function (a, r) { return a + r.amount; }, 0);
        var diff = round2(total - sum);
        stTotalBox.textContent = fmt(total);
        stSumBox.textContent = fmt(sum);
        stDiffBox.textContent = fmt(diff);
        stDiffBox.className = 'fw-bold ' + (diff === 0 && total > 0 ? 'text-success' : (diff !== 0 ? 'text-danger' : ''));
        stOk.classList.toggle('d-none', !(diff === 0 && total > 0 && sum > 0));
        return { total: total, sum: sum, diff: diff, rows: rows };
    }

    function renderSaved() {
        var saved = load();
        stSavedEmpty.style.display = saved.length ? 'none' : '';
        stSaved.innerHTML = '';
        saved.slice().reverse().forEach(function (s, i) {
            var item = document.createElement('div');
            item.className = 'accordion-item';
            var hid = 'stAcc' + i;
            var rows = s.splits.map(function (r) {
                return '<li class="list-group-item d-flex justify-content-between"><span>' + esc(r.name || '-') + ' <small class="text-muted">(' + esc(r.category) + ')</small></span><span class="fw-semibold">' + fmt(r.amount) + '</span></li>';
            }).join('');
            item.innerHTML =
                '<h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#' + hid + '">' +
                esc(s.title || 'Receipt') + ' — <span class="ms-1 fw-bold">' + fmt(s.total) + '</span>' +
                '</button></h2>' +
                '<div id="' + hid + '" class="accordion-collapse collapse"><div class="accordion-body p-0"><ul class="list-group list-group-flush">' + rows + '</ul>' +
                '<div class="p-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger st-del" data-i="' + i + '">Delete</button></div></div></div>';
            stSaved.appendChild(item);
        });
        stSaved.querySelectorAll('.st-del').forEach(function (b) {
            b.addEventListener('click', function () {
                var saved = load().slice().reverse();
                saved.splice(Number(b.getAttribute('data-i')), 1);
                save(saved.slice().reverse());
                renderSaved();
            });
        });
    }

    stAddRow.addEventListener('click', function () {
        stRows.appendChild(makeRow('', CATS[0], ''));
        recalc();
    });
    stTotal.addEventListener('input', recalc);

    stSave.addEventListener('click', function () {
        stError.classList.add('d-none'); stError.textContent = '';
        var r = recalc();
        if (!(r.total > 0)) { stError.textContent = 'Please enter a receipt total greater than 0.'; stError.classList.remove('d-none'); return; }
        if (r.diff !== 0) { stError.textContent = 'The splits sum does not equal the receipt total. Remaining: ' + fmt(r.diff) + ' — please bring it to zero first.'; stError.classList.remove('d-none'); return; }
        if (!r.rows.length) { stError.textContent = 'Please add at least one split line.'; stError.classList.remove('d-none'); return; }
        var saved = load();
        saved.push({ id: 's' + Date.now().toString(36), title: stTitle.value.trim() || 'Receipt', total: round2(r.total), splits: r.rows });
        save(saved);
        stTitle.value = ''; stTotal.value = '';
        stRows.innerHTML = '';
        stRows.appendChild(makeRow('', CATS[0], ''));
        stRows.appendChild(makeRow('', CATS[0], ''));
        recalc(); renderSaved();
        stOk.classList.remove('d-none');
        stOk.textContent = 'Saved! You can see it in the saved list below.';
    });

    stClear.addEventListener('click', function () {
        if (!confirm('Delete all saved splits?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        renderSaved();
    });

    stRows.appendChild(makeRow('', CATS[0], ''));
    stRows.appendChild(makeRow('', CATS[0], ''));
    recalc(); renderSaved();
})();
</script>
@endsection
