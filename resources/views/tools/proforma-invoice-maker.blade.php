@extends('layouts.app')

@section('title', 'Proforma Invoice Maker - Azlaan Tools')
@section('meta_description', 'Free online proforma invoice maker. Create a proforma invoice for advance payment or quotes, and download the PDF. No signup.')

@section('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
    @page { margin: 12mm; }
}
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Proforma Invoice Maker</h1>
            <p class="lead text-muted">A <strong>PROFORMA</strong> invoice for advance payment or quotation — not a tax invoice, just a payment request. Your data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">1. Your business</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pBizName" class="form-label fw-semibold">Business name</label>
                            <input type="text" class="form-control" id="pBizName" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="pBizLogo" class="form-label fw-semibold">Logo (optional)</label>
                            <input type="file" class="form-control" id="pBizLogo" accept="image/*">
                            <div class="mt-2"><img id="pLogoPreview" class="d-none border rounded" style="max-height:60px;" alt="Logo preview"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="pBizAddress" class="form-label fw-semibold">Address</label>
                            <input type="text" class="form-control" id="pBizAddress" placeholder="Shop address">
                        </div>
                        <div class="col-md-6">
                            <label for="pBizPhone" class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="pBizPhone" placeholder="e.g. 0300-1234567">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">2. Proforma details</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label for="pNumber" class="form-label fw-semibold">Proforma number</label>
                            <input type="text" class="form-control" id="pNumber">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="pDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="pDate">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="pValid" class="form-label fw-semibold">Quote valid till</label>
                            <input type="date" class="form-control" id="pValid">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="pAdvance" class="form-label fw-semibold">Advance required %</label>
                            <input type="number" class="form-control" id="pAdvance" value="50" min="0" max="100" step="1">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">3. Client (bill to)</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pClientPick" class="form-label fw-semibold">Choose a saved client</label>
                            <select class="form-select" id="pClientPick">
                                <option value="">— Type manually —</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pClientName" class="form-label fw-semibold">Client name</label>
                            <input type="text" class="form-control" id="pClientName" placeholder="Client / company name">
                        </div>
                        <div class="col-md-6">
                            <label for="pClientAddress" class="form-label fw-semibold">Client address</label>
                            <input type="text" class="form-control" id="pClientAddress" placeholder="Address">
                        </div>
                        <div class="col-md-6">
                            <label for="pClientPhone" class="form-label fw-semibold">Client phone</label>
                            <input type="text" class="form-control" id="pClientPhone" placeholder="Phone">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">4. Items</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr><th style="min-width:180px;">Details</th><th style="width:90px;">Qty</th><th style="width:120px;">Rate (Rs)</th><th style="width:90px;">Tax %</th><th class="text-end" style="width:120px;">Amount</th><th style="width:44px;"></th></tr>
                            </thead>
                            <tbody id="pItemRows"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="pAddItemBtn">+ Add item</button>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">5. Discount, terms and payment details</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label for="pDiscType" class="form-label fw-semibold">Discount type</label>
                            <select class="form-select" id="pDiscType">
                                <option value="flat">Rs (flat)</option>
                                <option value="pct">% (percent)</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="pDiscVal" class="form-label fw-semibold">Discount</label>
                            <input type="number" class="form-control" id="pDiscVal" value="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="pPayDetail" class="form-label fw-semibold">Payment detail (bank / mobile account)</label>
                            <input type="text" class="form-control" id="pPayDetail" placeholder="e.g. Easypaisa 0300-1234567 — Azlaan">
                        </div>
                        <div class="col-12">
                            <label for="pNotes" class="form-label fw-semibold">Terms / notes</label>
                            <input type="text" class="form-control" id="pNotes" placeholder="e.g. This is a proforma invoice — the final tax invoice will be issued after the work is complete.">
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="pErrorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Proforma Preview</h2>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary" id="pRefreshBtn">Preview refresh</button>
                            <button type="button" class="btn btn-success" id="pPrintBtn">Download / Print PDF</button>
                            <button type="button" class="btn btn-primary" id="pSaveBtn">Save Proforma</button>
                        </div>
                    </div>
                    <div id="printArea" class="border rounded p-4 bg-white">
                        <div class="alert alert-warning py-2 small mb-3">This is a <strong>PROFORMA INVOICE</strong> — for advance payment / quotation. Not a tax invoice.</div>
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <img id="pPvLogo" class="d-none" style="max-height:70px;" alt="Business logo">
                                <div>
                                    <h3 class="mb-0" id="pPvBiz">—</h3>
                                    <div class="small text-muted" id="pPvBizMeta">—</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="text-primary mb-1">PROFORMA INVOICE</h4>
                                <div class="small"><strong id="pPvNumber">—</strong></div>
                                <div class="small text-muted" id="pPvDates">—</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-muted">Bill to:</div>
                            <div class="fw-semibold" id="pPvClient">—</div>
                            <div class="small text-muted" id="pPvClientMeta">—</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr><th>#</th><th>Details</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Tax %</th><th class="text-end">Amount</th></tr>
                                </thead>
                                <tbody id="pPvItems"></tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="small text-muted" id="pPvNotes"></div>
                                <div class="small mt-2"><strong>Payment:</strong> <span id="pPvPay">—</span></div>
                            </div>
                            <div class="col-5">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr><td>Subtotal</td><td class="text-end" id="pPvSubtotal">Rs 0</td></tr>
                                        <tr><td>Discount</td><td class="text-end" id="pPvDiscount">Rs 0</td></tr>
                                        <tr><td>Tax</td><td class="text-end" id="pPvTax">Rs 0</td></tr>
                                        <tr class="fw-bold"><td>Grand Total</td><td class="text-end" id="pPvTotal">Rs 0</td></tr>
                                        <tr class="table-warning"><td>Advance required</td><td class="text-end fw-bold" id="pPvAdvance">Rs 0</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">For the PDF, click "Download / Print PDF", then select <strong>Save as PDF</strong> in the print dialog.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Saved proforma invoices</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Number</th><th>Date</th><th>Client</th><th class="text-end">Total</th><th></th></tr>
                            </thead>
                            <tbody id="pSavedRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="pSavedEmpty">No proforma saved yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Fill in your business, client and item details — set the advance %.</li>
                <li>Check the preview and click <strong>Save Proforma</strong> (numbering is PI-, separate from invoices).</li>
                <li>Use <strong>Download / Print PDF</strong> to create the PDF and send it to the client.</li>
            </ol>
            <p class="small text-muted">Note: a proforma invoice is only a quotation / advance request, not a tax invoice. Your data stays in your browser. Figures are informational.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan_proforma_invoices';
    var CLIENT_KEY = 'azlaan_clients';
    var LOGO_KEY = 'azlaan_invoice_logo';
    var BIZ_KEY = 'azlaan_invoice_biz';
    var CNT_KEY = 'azlaan_proforma_counter';

    var $ = function (id) { return document.getElementById(id); };
    var bizName = $('pBizName'), bizLogo = $('pBizLogo'), logoPreview = $('pLogoPreview'),
        bizAddress = $('pBizAddress'), bizPhone = $('pBizPhone'),
        pNumber = $('pNumber'), pDate = $('pDate'), pValid = $('pValid'), pAdvance = $('pAdvance'),
        clientPick = $('pClientPick'), clientName = $('pClientName'),
        clientAddress = $('pClientAddress'), clientPhone = $('pClientPhone'),
        itemRows = $('pItemRows'), discType = $('pDiscType'), discVal = $('pDiscVal'),
        payDetail = $('pPayDetail'), notes = $('pNotes'), errorBox = $('pErrorBox'),
        savedRows = $('pSavedRows'), savedEmpty = $('pSavedEmpty');

    var logoDataUrl = '';

    function loadJson(key, fallback) {
        try { var raw = localStorage.getItem(key); if (raw) return JSON.parse(raw); } catch (e) {}
        return fallback;
    }
    function saveJson(key, val) { try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) {} }
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
        return 'PI-' + new Date().getFullYear() + '-' + ('0000' + c).slice(-4);
    }

    var biz = loadJson(BIZ_KEY, {});
    if (biz.name) bizName.value = biz.name;
    if (biz.address) bizAddress.value = biz.address;
    if (biz.phone) bizPhone.value = biz.phone;
    logoDataUrl = loadJson(LOGO_KEY, '');
    if (logoDataUrl) { logoPreview.src = logoDataUrl; logoPreview.classList.remove('d-none'); }
    function saveBiz() {
        saveJson(BIZ_KEY, { name: bizName.value.trim(), address: bizAddress.value.trim(), phone: bizPhone.value.trim() });
    }
    [bizName, bizAddress, bizPhone].forEach(function (el) { el.addEventListener('change', saveBiz); });
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

    function loadClients() {
        var clients = loadJson(CLIENT_KEY, []);
        clientPick.innerHTML = '<option value="">— Type manually —</option>';
        clients.forEach(function (c, i) {
            var o = document.createElement('option');
            o.value = String(i);
            o.textContent = c.name || ('Client ' + (i + 1));
            clientPick.appendChild(o);
        });
    }
    clientPick.addEventListener('change', function () {
        var c = loadJson(CLIENT_KEY, [])[Number(clientPick.value)];
        if (c) {
            clientName.value = c.name || '';
            clientAddress.value = c.address || '';
            clientPhone.value = c.phone || '';
        }
    });

    function addItemRow(desc, qty, rate, tax) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" class="form-control form-control-sm i-desc" placeholder="Item details" value="' + esc(desc || '') + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm i-qty" min="0" step="0.01" value="' + (qty == null ? 1 : qty) + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm i-rate" min="0" step="0.01" value="' + (rate || 0) + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm i-tax" min="0" step="0.01" value="' + (tax || 0) + '"></td>' +
            '<td class="text-end i-amt fw-semibold">Rs 0</td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger i-del" aria-label="Delete item">×</button></td>';
        tr.querySelector('.i-del').addEventListener('click', function () { tr.remove(); recalc(); });
        ['i-desc', 'i-qty', 'i-rate', 'i-tax'].forEach(function (cls) {
            tr.querySelector('.' + cls).addEventListener('input', recalc);
        });
        itemRows.appendChild(tr);
        recalc();
    }
    $('pAddItemBtn').addEventListener('click', function () { addItemRow('', 1, 0, 0); });

    function readItems() {
        var out = [];
        itemRows.querySelectorAll('tr').forEach(function (tr) {
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
        var advPct = Number(pAdvance.value) || 0;
        return { sub: sub, discAmt: discAmt, tax: taxT, grand: grand, advance: grand * (advPct / 100) };
    }

    function recalc() {
        var items = readItems();
        var t = totals(items);
        var rows = itemRows.querySelectorAll('tr');
        rows.forEach(function (tr, i) {
            var it = items[i];
            if (it) tr.querySelector('.i-amt').textContent = fmt(it.qty * it.rate);
        });
        $('pPvBiz').textContent = bizName.value.trim() || '—';
        var meta = [];
        if (bizAddress.value.trim()) meta.push(bizAddress.value.trim());
        if (bizPhone.value.trim()) meta.push(bizPhone.value.trim());
        $('pPvBizMeta').textContent = meta.join(' • ') || '—';
        var pvLogo = $('pPvLogo');
        if (logoDataUrl) { pvLogo.src = logoDataUrl; pvLogo.classList.remove('d-none'); }
        else pvLogo.classList.add('d-none');
        $('pPvNumber').textContent = pNumber.value.trim() || '—';
        $('pPvDates').textContent = 'Date: ' + (pDate.value || '—') + '  •  Valid till: ' + (pValid.value || '—');
        $('pPvClient').textContent = clientName.value.trim() || '—';
        var cmeta = [];
        if (clientAddress.value.trim()) cmeta.push(clientAddress.value.trim());
        if (clientPhone.value.trim()) cmeta.push(clientPhone.value.trim());
        $('pPvClientMeta').textContent = cmeta.join(' • ') || '';
        var html = '';
        items.forEach(function (it, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(it.desc || '—') + '</td>' +
                '<td class="text-end">' + it.qty + '</td><td class="text-end">' + fmt(it.rate) + '</td>' +
                '<td class="text-end">' + it.tax + '%</td><td class="text-end">' + fmt(it.qty * it.rate) + '</td></tr>';
        });
        $('pPvItems').innerHTML = html || '<tr><td colspan="6" class="text-center text-muted">No items</td></tr>';
        $('pPvSubtotal').textContent = fmt(t.sub);
        $('pPvDiscount').textContent = '− ' + fmt(t.discAmt);
        $('pPvTax').textContent = fmt(t.tax);
        $('pPvTotal').textContent = fmt(t.grand);
        $('pPvAdvance').textContent = fmt(t.advance) + ' (' + (Number(pAdvance.value) || 0) + '%)';
        $('pPvNotes').textContent = notes.value.trim();
        $('pPvPay').textContent = payDetail.value.trim() || '—';
    }
    [discType, discVal, pAdvance, notes, payDetail, bizName, bizAddress, bizPhone,
     pNumber, pDate, pValid, clientName, clientAddress, clientPhone].forEach(function (el) {
        el.addEventListener('input', recalc);
        el.addEventListener('change', recalc);
    });
    $('pRefreshBtn').addEventListener('click', recalc);
    $('pPrintBtn').addEventListener('click', function () {
        hideError();
        if (!readItems().length) { showError('Add at least one item first.'); return; }
        recalc();
        window.print();
    });

    function renderSaved() {
        var list = loadJson(KEY, []);
        savedRows.innerHTML = '';
        savedEmpty.style.display = list.length ? 'none' : '';
        list.slice().reverse().forEach(function (p) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td'); tdN.textContent = p.number;
            var tdD = document.createElement('td'); tdD.textContent = p.date || '—';
            var tdC = document.createElement('td'); tdC.textContent = p.client ? (p.client.name || '—') : '—';
            var tdT = document.createElement('td'); tdT.className = 'text-end fw-semibold'; tdT.textContent = fmt(p.grandTotal);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = '×';
            del.setAttribute('aria-label', 'Delete proforma');
            del.addEventListener('click', function () {
                if (!confirm('Delete ' + p.number + '?')) return;
                saveJson(KEY, loadJson(KEY, []).filter(function (x) { return x.id !== p.id; }));
                renderSaved();
            });
            tdX.appendChild(del);
            tr.appendChild(tdN); tr.appendChild(tdD); tr.appendChild(tdC); tr.appendChild(tdT); tr.appendChild(tdX);
            savedRows.appendChild(tr);
        });
    }

    $('pSaveBtn').addEventListener('click', function () {
        hideError();
        var items = readItems();
        if (!items.length) { showError('Add at least one item first.'); return; }
        if (!clientName.value.trim()) { showError('Please enter the client name.'); return; }
        var t = totals(items);
        var list = loadJson(KEY, []);
        var num = pNumber.value.trim() || nextNumber();
        list.push({
            id: 'pi' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36),
            number: num,
            date: pDate.value || todayStr(),
            validTill: pValid.value || '',
            client: { name: clientName.value.trim(), address: clientAddress.value.trim(), phone: clientPhone.value.trim() },
            items: items,
            discount: { type: discType.value, value: Number(discVal.value) || 0, amount: Math.round(t.discAmt * 100) / 100 },
            subtotal: Math.round(t.sub * 100) / 100,
            taxTotal: Math.round(t.tax * 100) / 100,
            grandTotal: Math.round(t.grand * 100) / 100,
            advancePct: Number(pAdvance.value) || 0,
            advanceAmount: Math.round(t.advance * 100) / 100,
            paymentDetail: payDetail.value.trim(),
            notes: notes.value.trim(),
            createdAt: new Date().toISOString()
        });
        saveJson(KEY, list);
        renderSaved();
        pNumber.value = nextNumber();
        errorBox.textContent = 'Proforma ' + num + ' saved.';
        errorBox.classList.remove('d-none', 'alert-danger');
        errorBox.classList.add('alert-success');
        setTimeout(function () {
            errorBox.classList.add('alert-danger');
            errorBox.classList.remove('alert-success');
            hideError();
        }, 3000);
    });

    pDate.value = todayStr();
    pNumber.value = nextNumber();
    loadClients();
    addItemRow('', 1, 0, 0);
    renderSaved();
    recalc();
})();
</script>
@endsection
