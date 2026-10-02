@extends('layouts.app')

@section('title', 'Invoice Maker - Azlaan Tools')
@section('meta_description', 'Free online invoice maker. Create a professional invoice — items, tax, discount, with your business logo, PDF download. No signup.')

@section('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
    @page { margin: 12mm; }
}
.inv-preview { background: #fff; }
.inv-table th, .inv-table td { font-size: 0.9rem; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Invoice Maker</h1>
            <p class="lead text-muted">Create a professional invoice — items, tax, discount and your business logo. Your data is saved only in your browser, never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">1. Your business</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="bizName" class="form-label fw-semibold">Business name</label>
                            <input type="text" class="form-control" id="bizName" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="bizLogo" class="form-label fw-semibold">Logo (optional)</label>
                            <input type="file" class="form-control" id="bizLogo" accept="image/*">
                            <div class="mt-2"><img id="logoPreview" class="d-none border rounded" style="max-height:60px;" alt="Logo preview"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="bizAddress" class="form-label fw-semibold">Address</label>
                            <input type="text" class="form-control" id="bizAddress" placeholder="Shop address">
                        </div>
                        <div class="col-md-6">
                            <label for="bizPhone" class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="bizPhone" placeholder="e.g. 0300-1234567">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">2. Invoice details</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label for="invNumber" class="form-label fw-semibold">Invoice number</label>
                            <input type="text" class="form-control" id="invNumber">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="invDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="invDate">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="invDue" class="form-label fw-semibold">Due date</label>
                            <input type="date" class="form-control" id="invDue">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="invStatus" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="invStatus">
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">3. Client (bill to)</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="clientPick" class="form-label fw-semibold">Choose a saved client</label>
                            <select class="form-select" id="clientPick">
                                <option value="">— Type manually —</option>
                            </select>
                            <p class="small text-muted mt-1 mb-0" id="clientHint">The client list comes from the "Client Manager" tool.</p>
                        </div>
                        <div class="col-md-6">
                            <label for="clientName" class="form-label fw-semibold">Client name</label>
                            <input type="text" class="form-control" id="clientName" placeholder="Client / company name">
                        </div>
                        <div class="col-md-6">
                            <label for="clientAddress" class="form-label fw-semibold">Client address</label>
                            <input type="text" class="form-control" id="clientAddress" placeholder="Address">
                        </div>
                        <div class="col-md-6">
                            <label for="clientPhone" class="form-label fw-semibold">Client phone</label>
                            <input type="text" class="form-control" id="clientPhone" placeholder="Phone">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">4. Items</h2>
                    <div class="row g-2 mb-2 align-items-end">
                        <div class="col-md-5">
                            <label for="catalogPick" class="form-label fw-semibold small">Choose from price catalog</label>
                            <select class="form-select" id="catalogPick">
                                <option value="">— Catalog is empty —</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <p class="small text-muted mb-0">The catalog comes from the "Price List Catalog" tool. You can also add a manual item below.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr><th style="min-width:180px;">Details</th><th style="width:90px;">Qty</th><th style="width:120px;">Rate (Rs)</th><th style="width:90px;">Tax %</th><th class="text-end" style="width:120px;">Amount</th><th style="width:44px;"></th></tr>
                            </thead>
                            <tbody id="itemRows"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">+ Add item</button>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">5. Discount and notes</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label for="discType" class="form-label fw-semibold">Discount type</label>
                            <select class="form-select" id="discType">
                                <option value="flat">Rs (flat)</option>
                                <option value="pct">% (percent)</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="discVal" class="form-label fw-semibold">Discount</label>
                            <input type="number" class="form-control" id="discVal" value="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="invNotes" class="form-label fw-semibold">Notes / terms</label>
                            <input type="text" class="form-control" id="invNotes" placeholder="e.g. Payment within 7 days. Thank you!">
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Invoice Preview</h2>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary" id="refreshBtn">Preview refresh</button>
                            <button type="button" class="btn btn-success" id="printBtn">Download / Print PDF</button>
                            <button type="button" class="btn btn-primary" id="saveBtn">Save Invoice</button>
                        </div>
                    </div>
                    <div id="printArea" class="inv-preview border rounded p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <img id="pvLogo" class="d-none" style="max-height:70px;" alt="Business logo">
                                <div>
                                    <h3 class="mb-0" id="pvBiz">—</h3>
                                    <div class="small text-muted" id="pvBizMeta">—</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="text-primary mb-1">INVOICE</h4>
                                <div class="small"><strong id="pvNumber">—</strong></div>
                                <div class="small text-muted" id="pvDates">—</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-muted">Bill to:</div>
                            <div class="fw-semibold" id="pvClient">—</div>
                            <div class="small text-muted" id="pvClientMeta">—</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered inv-table">
                                <thead class="table-light">
                                    <tr><th>#</th><th>Details</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Tax %</th><th class="text-end">Amount</th></tr>
                                </thead>
                                <tbody id="pvItems"></tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="small text-muted" id="pvNotes"></div>
                            </div>
                            <div class="col-5">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr><td>Subtotal</td><td class="text-end" id="pvSubtotal">Rs 0</td></tr>
                                        <tr><td>Discount</td><td class="text-end" id="pvDiscount">Rs 0</td></tr>
                                        <tr><td>Tax</td><td class="text-end" id="pvTax">Rs 0</td></tr>
                                        <tr class="fw-bold"><td>Grand Total</td><td class="text-end" id="pvTotal">Rs 0</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="text-end"><span class="badge bg-secondary" id="pvStatus">Unpaid</span></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">For PDF, press "Download / Print PDF", then select <strong>Save as PDF</strong> in the print dialog.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Saved invoices</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Number</th><th>Date</th><th>Client</th><th class="text-end">Total</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody id="savedRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="savedEmpty">No invoice saved yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your business name, address and logo (the logo is saved in your browser).</li>
                <li>Fill in the invoice number, date, due date and client details. You can pick a saved client or catalog item.</li>
                <li>Add items — with qty, rate and tax %. The total is calculated automatically.</li>
                <li>Press <strong>Save Invoice</strong> — then use <strong>Download / Print PDF</strong> to make the PDF.</li>
            </ol>
            <p class="small text-muted">This record stays in your own browser only — nothing goes to a server. Figures are for information only, not tax advice.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var INV_KEY = 'azlaan_invoices';
    var CLIENT_KEY = 'azlaan_clients';
    var CAT_KEY = 'azlaan_price_catalog';
    var LOGO_KEY = 'azlaan_invoice_logo';
    var BIZ_KEY = 'azlaan_invoice_biz';
    var CNT_KEY = 'azlaan_invoice_counter';

    var $ = function (id) { return document.getElementById(id); };
    var bizName = $('bizName'), bizLogo = $('bizLogo'), logoPreview = $('logoPreview'),
        bizAddress = $('bizAddress'), bizPhone = $('bizPhone'),
        invNumber = $('invNumber'), invDate = $('invDate'), invDue = $('invDue'), invStatus = $('invStatus'),
        clientPick = $('clientPick'), clientHint = $('clientHint'),
        clientName = $('clientName'), clientAddress = $('clientAddress'), clientPhone = $('clientPhone'),
        catalogPick = $('catalogPick'), itemRows = $('itemRows'), addItemBtn = $('addItemBtn'),
        discType = $('discType'), discVal = $('discVal'), invNotes = $('invNotes'),
        errorBox = $('errorBox'), savedRows = $('savedRows'), savedEmpty = $('savedEmpty');

    var logoDataUrl = '';

    function loadJson(key, fallback) {
        try {
            var raw = localStorage.getItem(key);
            if (raw) return JSON.parse(raw);
        } catch (e) {}
        return fallback;
    }
    function saveJson(key, val) {
        try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) {}
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function nextNumber() {
        var c = Number(loadJson(CNT_KEY, 0)) || 0;
        c += 1;
        saveJson(CNT_KEY, c);
        var y = new Date().getFullYear();
        return 'INV-' + y + '-' + ('0000' + c).slice(-4);
    }

    // ---- business defaults ----
    var biz = loadJson(BIZ_KEY, {});
    if (biz.name) bizName.value = biz.name;
    if (biz.address) bizAddress.value = biz.address;
    if (biz.phone) bizPhone.value = biz.phone;
    logoDataUrl = loadJson(LOGO_KEY, '');
    if (logoDataUrl) { logoPreview.src = logoDataUrl; logoPreview.classList.remove('d-none'); }

    function saveBiz() {
        saveJson(BIZ_KEY, { name: bizName.value.trim(), address: bizAddress.value.trim(), phone: bizPhone.value.trim() });
    }
    [bizName, bizAddress, bizPhone].forEach(function (el) {
        el.addEventListener('change', saveBiz);
    });
    bizLogo.addEventListener('change', function () {
        var f = bizLogo.files && bizLogo.files[0];
        if (!f) return;
        var r = new FileReader();
        r.onload = function () {
            logoDataUrl = r.result;
            logoPreview.src = logoDataUrl;
            logoPreview.classList.remove('d-none');
            saveJson(LOGO_KEY, logoDataUrl);
        };
        r.readAsDataURL(f);
    });

    // ---- clients dropdown ----
    function loadClients() {
        var clients = loadJson(CLIENT_KEY, []);
        clientPick.innerHTML = '<option value="">— Type manually —</option>';
        if (!clients.length) {
            clientHint.textContent = 'No saved client found. Type manually below (you can add clients in the Client Manager tool).';
            return;
        }
        clientHint.textContent = clients.length + ' saved clients found.';
        clients.forEach(function (c, i) {
            var o = document.createElement('option');
            o.value = String(i);
            o.textContent = c.name || ('Client ' + (i + 1));
            clientPick.appendChild(o);
        });
    }
    clientPick.addEventListener('change', function () {
        var clients = loadJson(CLIENT_KEY, []);
        var c = clients[Number(clientPick.value)];
        if (c) {
            clientName.value = c.name || '';
            clientAddress.value = c.address || '';
            clientPhone.value = c.phone || '';
        }
    });

    // ---- catalog dropdown ----
    function loadCatalog() {
        var items = loadJson(CAT_KEY, []);
        catalogPick.innerHTML = '<option value="">— Choose an item from the catalog —</option>';
        if (!items.length) return;
        items.forEach(function (it, i) {
            var o = document.createElement('option');
            o.value = String(i);
            o.textContent = (it.desc || it.name || 'Item') + ' — Rs ' + (it.rate || 0);
            catalogPick.appendChild(o);
        });
    }
    catalogPick.addEventListener('change', function () {
        var items = loadJson(CAT_KEY, []);
        var it = items[Number(catalogPick.value)];
        if (it) addItemRow(it.desc || it.name || '', 1, Number(it.rate) || 0, 0);
        catalogPick.value = '';
    });

    // ---- item rows ----
    function addItemRow(desc, qty, rate, tax) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" class="form-control form-control-sm i-desc" placeholder="Item details" value="' + esc(desc || '') + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm i-qty" min="0" step="0.01" value="' + (qty == null ? 1 : qty) + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm i-rate" min="0" step="0.01" value="' + (rate || 0) + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm i-tax" min="0" step="0.01" value="' + (tax || 0) + '"></td>' +
            '<td class="text-end i-amt fw-semibold">Rs 0</td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger i-del" aria-label="Delete item">×</button></td>';
        tr.querySelector('.i-del').addEventListener('click', function () {
            tr.remove(); recalc();
        });
        ['i-desc', 'i-qty', 'i-rate', 'i-tax'].forEach(function (cls) {
            tr.querySelector('.' + cls).addEventListener('input', recalc);
        });
        itemRows.appendChild(tr);
        recalc();
    }
    addItemBtn.addEventListener('click', function () { addItemRow('', 1, 0, 0); });

    function readItems() {
        var out = [];
        var rows = itemRows.querySelectorAll('tr');
        rows.forEach(function (tr) {
            var desc = tr.querySelector('.i-desc').value.trim();
            var qty = Number(tr.querySelector('.i-qty').value) || 0;
            var rate = Number(tr.querySelector('.i-rate').value) || 0;
            var tax = Number(tr.querySelector('.i-tax').value) || 0;
            if (desc || qty || rate) out.push({ desc: desc, qty: qty, rate: rate, tax: tax });
        });
        return out;
    }
    function totals(items) {
        var sub = 0, taxT = 0;
        items.forEach(function (it) {
            var base = it.qty * it.rate;
            sub += base;
            taxT += base * (it.tax / 100);
        });
        var disc = Number(discVal.value) || 0;
        var discAmt = discType.value === 'pct' ? sub * (disc / 100) : disc;
        if (discAmt > sub) discAmt = sub;
        var grand = sub - discAmt + taxT;
        return { sub: sub, discAmt: discAmt, tax: taxT, grand: grand };
    }

    function recalc() {
        var items = readItems();
        var t = totals(items);
        var rows = itemRows.querySelectorAll('tr');
        rows.forEach(function (tr, i) {
            var it = items[i];
            if (it) tr.querySelector('.i-amt').textContent = fmt(it.qty * it.rate);
        });
        // preview
        $('pvBiz').textContent = bizName.value.trim() || '—';
        var meta = [];
        if (bizAddress.value.trim()) meta.push(bizAddress.value.trim());
        if (bizPhone.value.trim()) meta.push(bizPhone.value.trim());
        $('pvBizMeta').textContent = meta.join(' • ') || '—';
        var pvLogo = $('pvLogo');
        if (logoDataUrl) { pvLogo.src = logoDataUrl; pvLogo.classList.remove('d-none'); }
        else pvLogo.classList.add('d-none');
        $('pvNumber').textContent = invNumber.value.trim() || '—';
        $('pvDates').textContent = 'Date: ' + (invDate.value || '—') + '  •  Due: ' + (invDue.value || '—');
        $('pvClient').textContent = clientName.value.trim() || '—';
        var cmeta = [];
        if (clientAddress.value.trim()) cmeta.push(clientAddress.value.trim());
        if (clientPhone.value.trim()) cmeta.push(clientPhone.value.trim());
        $('pvClientMeta').textContent = cmeta.join(' • ') || '';
        var html = '';
        items.forEach(function (it, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(it.desc || '—') + '</td>' +
                '<td class="text-end">' + it.qty + '</td><td class="text-end">' + fmt(it.rate) + '</td>' +
                '<td class="text-end">' + it.tax + '%</td><td class="text-end">' + fmt(it.qty * it.rate) + '</td></tr>';
        });
        $('pvItems').innerHTML = html || '<tr><td colspan="6" class="text-center text-muted">No items</td></tr>';
        $('pvSubtotal').textContent = fmt(t.sub);
        $('pvDiscount').textContent = '− ' + fmt(t.discAmt);
        $('pvTax').textContent = fmt(t.tax);
        $('pvTotal').textContent = fmt(t.grand);
        $('pvNotes').textContent = invNotes.value.trim();
        var st = invStatus.value;
        var stEl = $('pvStatus');
        stEl.textContent = st.charAt(0).toUpperCase() + st.slice(1);
        stEl.className = 'badge ' + (st === 'paid' ? 'bg-success' : (st === 'partial' ? 'bg-warning text-dark' : 'bg-secondary'));
    }
    [discType, discVal, invNotes, bizName, bizAddress, bizPhone, invNumber, invDate, invDue, invStatus,
     clientName, clientAddress, clientPhone].forEach(function (el) {
        el.addEventListener('input', recalc);
        el.addEventListener('change', recalc);
    });
    $('refreshBtn').addEventListener('click', recalc);
    $('printBtn').addEventListener('click', function () {
        hideError();
        if (!readItems().length) { showError('Please add at least one item first.'); return; }
        recalc();
        window.print();
    });

    // ---- save ----
    function renderSaved() {
        var invs = loadJson(INV_KEY, []);
        savedRows.innerHTML = '';
        savedEmpty.style.display = invs.length ? 'none' : '';
        invs.slice().reverse().forEach(function (inv) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td'); tdN.textContent = inv.number;
            var tdD = document.createElement('td'); tdD.textContent = inv.date || '—';
            var tdC = document.createElement('td'); tdC.textContent = inv.client ? (inv.client.name || '—') : '—';
            var tdT = document.createElement('td'); tdT.className = 'text-end fw-semibold'; tdT.textContent = fmt(inv.grandTotal);
            var tdS = document.createElement('td');
            var badge = document.createElement('span');
            badge.className = 'badge ' + (inv.status === 'paid' ? 'bg-success' : (inv.status === 'partial' ? 'bg-warning text-dark' : 'bg-secondary'));
            badge.textContent = inv.status || 'unpaid';
            tdS.appendChild(badge);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = '×';
            del.setAttribute('aria-label', 'Delete invoice');
            del.addEventListener('click', function () {
                if (!confirm('Delete ' + inv.number + '?')) return;
                var all = loadJson(INV_KEY, []).filter(function (x) { return x.id !== inv.id; });
                saveJson(INV_KEY, all); renderSaved();
            });
            tdX.appendChild(del);
            tr.appendChild(tdN); tr.appendChild(tdD); tr.appendChild(tdC);
            tr.appendChild(tdT); tr.appendChild(tdS); tr.appendChild(tdX);
            savedRows.appendChild(tr);
        });
    }

    $('saveBtn').addEventListener('click', function () {
        hideError();
        var items = readItems();
        if (!items.length) { showError('Please add at least one item first.'); return; }
        if (!clientName.value.trim()) { showError('Please enter the client name.'); return; }
        var t = totals(items);
        var invs = loadJson(INV_KEY, []);
        var num = invNumber.value.trim() || nextNumber();
        var inv = {
            id: 'inv' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36),
            number: num,
            date: invDate.value || todayStr(),
            dueDate: invDue.value || '',
            client: { name: clientName.value.trim(), address: clientAddress.value.trim(), phone: clientPhone.value.trim() },
            items: items,
            discount: { type: discType.value, value: Number(discVal.value) || 0, amount: Math.round(t.discAmt * 100) / 100 },
            subtotal: Math.round(t.sub * 100) / 100,
            taxTotal: Math.round(t.tax * 100) / 100,
            grandTotal: Math.round(t.grand * 100) / 100,
            notes: invNotes.value.trim(),
            status: invStatus.value,
            payments: [],
            createdAt: new Date().toISOString()
        };
        invs.push(inv);
        saveJson(INV_KEY, invs);
        renderSaved();
        invNumber.value = nextNumber();
        errorBox.textContent = 'Invoice ' + num + ' saved.';
        errorBox.classList.remove('d-none', 'alert-danger');
        errorBox.classList.add('alert-success');
        setTimeout(function () {
            errorBox.classList.add('alert-danger');
            errorBox.classList.remove('alert-success');
            hideError();
        }, 3000);
    });

    // ---- init ----
    invDate.value = todayStr();
    invNumber.value = nextNumber();
    loadClients();
    loadCatalog();
    addItemRow('', 1, 0, 0);
    renderSaved();
    recalc();
})();
</script>
@endsection
