@extends('layouts.app')

@section('title', 'Purchase Return Note Maker - Azlaan Tools')
@section('meta_description', 'Make a purchase return note for goods sent back to the supplier — free debit note maker. Print and save it.')

@section('content')
<style>
@media print {
    body * { visibility: hidden; }
    #notePrint, #notePrint * { visibility: visible; }
    #notePrint { position: absolute; left: 0; top: 0; width: 100%; padding: 10px; }
}
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Purchase Return Note Maker</h1>
            <p class="lead text-muted">Make a purchase return note for goods returned to the supplier — a ready debit record. You can print it and save it too. Data is saved only in your browser, nothing is uploaded anywhere.</p>

            <div class="row">
                <div class="col-12 col-md-5 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Return note details</h5>
                            <div class="mb-2">
                                <label for="prNo" class="form-label fw-semibold">Return note no.</label>
                                <input type="text" class="form-control" id="prNo">
                            </div>
                            <div class="mb-2">
                                <label for="prDate" class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control" id="prDate">
                            </div>
                            <div class="mb-2">
                                <label for="prSupplier" class="form-label fw-semibold">Supplier name</label>
                                <input type="text" class="form-control" id="prSupplier" placeholder="e.g. City Traders">
                            </div>
                            <div class="mb-2">
                                <label for="prBill" class="form-label fw-semibold">Original purchase bill no. (optional)</label>
                                <input type="text" class="form-control" id="prBill" placeholder="e.g. PB-778">
                            </div>
                            <div class="mb-3">
                                <label for="prReason" class="form-label fw-semibold">Return reason</label>
                                <select class="form-select" id="prReason">
                                    <option value="Defective / damaged goods">Defective / damaged goods</option>
                                    <option value="Wrong item sent">Wrong item sent</option>
                                    <option value="Near expiry">Near expiry</option>
                                    <option value="Extra goods received">Extra goods received</option>
                                    <option value="Other reason">Other reason</option>
                                </select>
                            </div>
                            <h6>Items</h6>
                            <div id="itemRows"></div>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-3" id="addItemBtn">+ Add Item</button>
                            <button type="button" class="btn btn-primary w-100 mb-2" id="previewBtn">Preview Note</button>
                            <button type="button" class="btn btn-success w-100" id="saveBtn">Save Note</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <h5 class="mb-0">Note preview</h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="printBtn">Print / PDF</button>
                            </div>
                            <div id="notePrint" class="border rounded p-3">
                                <div class="text-center mb-3">
                                    <h4 class="mb-1">Purchase Return Note</h4>
                                    <div class="text-muted small">Goods returned to the supplier — debit record</div>
                                </div>
                                <div id="noteBody" class="text-muted">Add items, then press "Preview Note".</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Saved return notes</h5>
                    <div id="savedList"></div>
                    <p class="small text-muted mb-0" id="savedEmpty">No saved notes yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the supplier name, purchase bill no. and return <strong>reason</strong>.</li>
                <li>Add the returned <strong>items</strong> (name, qty, rate) — the total is calculated automatically.</li>
                <li>Check with <strong>Preview Note</strong>, then <strong>Print / PDF</strong> or <strong>Save</strong>.</li>
            </ol>
            <p class="text-muted small">Note: this record is saved only in your browser, nothing is uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_purchase_returns';
    var prNo = document.getElementById('prNo');
    var prDate = document.getElementById('prDate');
    var prSupplier = document.getElementById('prSupplier');
    var prBill = document.getElementById('prBill');
    var prReason = document.getElementById('prReason');
    var itemRows = document.getElementById('itemRows');
    var addItemBtn = document.getElementById('addItemBtn');
    var previewBtn = document.getElementById('previewBtn');
    var saveBtn = document.getElementById('saveBtn');
    var printBtn = document.getElementById('printBtn');
    var noteBody = document.getElementById('noteBody');
    var errorBox = document.getElementById('errorBox');
    var savedList = document.getElementById('savedList');
    var savedEmpty = document.getElementById('savedEmpty');

    function today() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    prDate.value = today();

    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
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
    function uid() {
        return 'pr' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function nextNoteNo() {
        var d = new Date();
        var seq = (loadNotes().length + 1).toString().padStart(3, '0');
        return 'PR-' + d.getFullYear() + ('0' + (d.getMonth() + 1)).slice(-2) + d.getDate() + '-' + seq;
    }

    function addItemRow(name, qty, rate) {
        var wrap = document.createElement('div');
        wrap.className = 'border rounded p-2 mb-2 item-row';
        var grid = document.createElement('div');
        grid.className = 'row g-2';
        grid.innerHTML =
            '<div class="col-12"><input type="text" class="form-control form-control-sm it-name" placeholder="Item name" value="' + esc(name || '') + '"></div>' +
            '<div class="col-6"><input type="number" class="form-control form-control-sm it-qty" placeholder="Qty" min="1" step="1" value="' + (qty || '') + '"></div>' +
            '<div class="col-6"><input type="number" class="form-control form-control-sm it-rate" placeholder="Rate (Rs)" min="0" step="0.01" value="' + (rate || '') + '"></div>' +
            '<div class="col-12 d-flex justify-content-between align-items-center"><small class="text-muted it-amt">Rs 0</small><button type="button" class="btn btn-sm btn-outline-danger it-del">×</button></div>';
        wrap.appendChild(grid);
        var qtyEl = wrap.querySelector('.it-qty');
        var rateEl = wrap.querySelector('.it-rate');
        var amtEl = wrap.querySelector('.it-amt');
        function upd() {
            var q = parseFloat(qtyEl.value) || 0;
            var r = parseFloat(rateEl.value) || 0;
            amtEl.textContent = fmt(q * r);
            updatePreview();
        }
        qtyEl.addEventListener('input', upd);
        rateEl.addEventListener('input', upd);
        wrap.querySelector('.it-del').addEventListener('click', function () {
            wrap.remove();
            updatePreview();
        });
        wrap.querySelector('.it-name').addEventListener('input', updatePreview);
        itemRows.appendChild(wrap);
    }

    function getItems() {
        var items = [];
        var rows = itemRows.querySelectorAll('.item-row');
        rows.forEach(function (row) {
            var name = row.querySelector('.it-name').value.trim();
            var qty = parseFloat(row.querySelector('.it-qty').value) || 0;
            var rate = parseFloat(row.querySelector('.it-rate').value) || 0;
            if (name && qty > 0) {
                items.push({ name: name, qty: qty, rate: rate, amount: Math.round(qty * rate * 100) / 100 });
            }
        });
        return items;
    }

    function noteData() {
        var items = getItems();
        var totalQty = 0, totalAmt = 0;
        items.forEach(function (it) { totalQty += it.qty; totalAmt += it.amount; });
        return {
            no: prNo.value.trim(),
            date: prDate.value,
            supplier: prSupplier.value.trim(),
            bill: prBill.value.trim(),
            reason: prReason.value,
            items: items,
            totalQty: totalQty,
            totalAmount: Math.round(totalAmt * 100) / 100
        };
    }

    function renderNote(n) {
        var html = '<div class="row mb-2 small"><div class="col-6"><strong>Note No:</strong> ' + esc(n.no || '—') + '<br><strong>Date:</strong> ' + esc(n.date || '—') + '</div>' +
            '<div class="col-6"><strong>Supplier:</strong> ' + esc(n.supplier || '—') + '<br><strong>Original Bill:</strong> ' + esc(n.bill || '—') + '</div></div>' +
            '<div class="small mb-2"><strong>Reason:</strong> ' + esc(n.reason || '—') + '</div>';
        html += '<table class="table table-sm table-bordered"><thead class="table-light"><tr><th>#</th><th>Item</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th></tr></thead><tbody>';
        n.items.forEach(function (it, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(it.name) + '</td><td class="text-end">' + it.qty + '</td><td class="text-end">' + fmt(it.rate) + '</td><td class="text-end">' + fmt(it.amount) + '</td></tr>';
        });
        html += '<tr><td colspan="2"><strong>Total</strong></td><td class="text-end"><strong>' + n.totalQty + '</strong></td><td></td><td class="text-end"><strong>' + fmt(n.totalAmount) + '</strong></td></tr>';
        html += '</tbody></table>';
        html += '<p class="small text-muted mb-0">A debit of ' + fmt(n.totalAmount) + ' is applied to the supplier. Goods have been returned.</p>';
        return html;
    }

    function updatePreview() {
        var n = noteData();
        if (!n.items.length) {
            noteBody.className = 'text-muted';
            noteBody.textContent = 'Add items, then press "Preview Note".';
            return;
        }
        noteBody.className = '';
        noteBody.innerHTML = renderNote(n);
    }

    function loadNotes() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) return p;
            }
        } catch (e) {}
        return [];
    }
    function saveNotes(n) {
        try { localStorage.setItem(KEY, JSON.stringify(n)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }

    addItemBtn.addEventListener('click', function () {
        hideError();
        addItemRow();
    });
    previewBtn.addEventListener('click', function () {
        hideError();
        updatePreview();
    });
    printBtn.addEventListener('click', function () {
        hideError();
        var items = getItems();
        if (!items.length) { showError('Please add an item first.'); return; }
        updatePreview();
        setTimeout(function () { window.print(); }, 100);
    });
    saveBtn.addEventListener('click', function () {
        hideError();
        var n = noteData();
        if (!n.supplier) { showError('Please write the supplier name.'); return; }
        if (!n.items.length) { showError('Please add an item first.'); return; }
        n.id = uid();
        n.savedAt = new Date().toLocaleString('en-PK');
        var notes = loadNotes();
        notes.unshift(n);
        if (notes.length > 200) notes.length = 200;
        saveNotes(notes);
        renderSaved();
        saveBtn.textContent = 'Saved!';
        setTimeout(function () { saveBtn.textContent = 'Save Note'; }, 2000);
        prNo.value = nextNoteNo();
    });

    function renderSaved() {
        var notes = loadNotes();
        savedList.innerHTML = '';
        savedEmpty.style.display = notes.length ? 'none' : '';
        notes.forEach(function (n) {
            var div = document.createElement('div');
            div.className = 'border rounded p-2 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2';
            var left = document.createElement('div');
            left.innerHTML = '<strong>' + esc(n.no || '—') + '</strong> · ' + esc(n.supplier) +
                '<br><small class="text-muted">' + esc(n.date) + ' · ' + n.items.length + ' items · ' + fmt(n.totalAmount) + ' · ' + esc(n.reason || '') + '</small>';
            var right = document.createElement('div');
            var viewB = document.createElement('button');
            viewB.type = 'button';
            viewB.className = 'btn btn-sm btn-outline-primary me-2';
            viewB.textContent = 'View / Print';
            viewB.addEventListener('click', function () {
                prNo.value = n.no || '';
                prDate.value = n.date || '';
                prSupplier.value = n.supplier || '';
                prBill.value = n.bill || '';
                prReason.value = n.reason || prReason.options[0].value;
                itemRows.innerHTML = '';
                n.items.forEach(function (it) { addItemRow(it.name, it.qty, it.rate); });
                updatePreview();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            var delB = document.createElement('button');
            delB.type = 'button';
            delB.className = 'btn btn-sm btn-outline-danger';
            delB.textContent = 'Delete';
            delB.addEventListener('click', function () {
                if (!confirm('Delete this note?')) return;
                saveNotes(loadNotes().filter(function (x) { return x.id !== n.id; }));
                renderSaved();
            });
            right.appendChild(viewB);
            right.appendChild(delB);
            div.appendChild(left);
            div.appendChild(right);
            savedList.appendChild(div);
        });
    }

    prNo.value = nextNoteNo();
    addItemRow();
    renderSaved();
})();
</script>
@endsection
