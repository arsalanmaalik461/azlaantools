@extends('layouts.app')

@section('title', 'Credit Note Maker - Azlaan Tools')
@section('meta_description', 'Free online credit note maker. Issue a credit note for a return or discount, linked to the original invoice. No signup.')

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
            <h1 class="mb-3">Credit Note Maker</h1>
            <p class="lead text-muted">Issue a <strong>credit note</strong> for a return, discount, or mistake fix — linked to the original invoice. Data is saved only in your browser — it is not uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">1. Credit note details</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label for="cnNumber" class="form-label fw-semibold">Credit note number</label>
                            <input type="text" class="form-control" id="cnNumber">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="cnDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="cnDate">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="cnInvoice" class="form-label fw-semibold">Original invoice</label>
                            <select class="form-select" id="cnInvoice">
                                <option value="">— Choose from saved invoices or write manually —</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6" id="cnInvoiceManualWrap">
                            <label for="cnInvoiceManual" class="form-label fw-semibold">Original invoice no (manual)</label>
                            <input type="text" class="form-control" id="cnInvoiceManual" placeholder="e.g. INV-2026-0007">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="cnReason" class="form-label fw-semibold">Reason</label>
                            <select class="form-select" id="cnReason">
                                <option value="Return">Return / goods returned</option>
                                <option value="Discount">Discount</option>
                                <option value="Damaged">Damaged / broken goods</option>
                                <option value="Overcharge">Overcharge / extra bill</option>
                                <option value="Cancelled">Order cancelled</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">2. Party (who gets the credit)</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cnClientPick" class="form-label fw-semibold">Choose saved client</label>
                            <select class="form-select" id="cnClientPick">
                                <option value="">— Write manually —</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="cnClientName" class="form-label fw-semibold">Party name</label>
                            <input type="text" class="form-control" id="cnClientName" placeholder="Client / company name">
                        </div>
                        <div class="col-md-6">
                            <label for="cnClientAddress" class="form-label fw-semibold">Address</label>
                            <input type="text" class="form-control" id="cnClientAddress" placeholder="Address">
                        </div>
                        <div class="col-md-6">
                            <label for="cnClientPhone" class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="cnClientPhone" placeholder="Phone">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">3. Amount / items</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr><th style="min-width:180px;">Details</th><th style="width:90px;">Qty</th><th style="width:120px;">Rate (Rs)</th><th style="width:90px;">Tax %</th><th class="text-end" style="width:120px;">Amount</th><th style="width:44px;"></th></tr>
                            </thead>
                            <tbody id="cnItemRows"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="cnAddItemBtn">+ Add item</button>

                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label for="cnNote" class="form-label fw-semibold">More details (optional)</label>
                            <textarea class="form-control" id="cnNote" rows="2" placeholder="e.g. 2 panels came back, their amount is credited."></textarea>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="cnErrorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Credit Note Preview</h2>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary" id="cnRefreshBtn">Refresh preview</button>
                            <button type="button" class="btn btn-success" id="cnPrintBtn">Download / Print PDF</button>
                            <button type="button" class="btn btn-primary" id="cnSaveBtn">Save Credit Note</button>
                        </div>
                    </div>
                    <div id="printArea" class="border rounded p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="mb-0" id="cnPvBiz">—</h3>
                                <div class="small text-muted" id="cnPvBizMeta">—</div>
                            </div>
                            <div class="text-end">
                                <h4 class="text-danger mb-1">CREDIT NOTE</h4>
                                <div class="small"><strong id="cnPvNumber">—</strong></div>
                                <div class="small text-muted" id="cnPvDate">—</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="small text-muted">Credit to:</div>
                                <div class="fw-semibold" id="cnPvClient">—</div>
                                <div class="small text-muted" id="cnPvClientMeta">—</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="small text-muted">Against invoice:</div>
                                <div class="fw-semibold" id="cnPvInvoice">—</div>
                                <div class="small text-muted">Reason: <span id="cnPvReason">—</span></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr><th>#</th><th>Details</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Tax %</th><th class="text-end">Amount</th></tr>
                                </thead>
                                <tbody id="cnPvItems"></tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="small text-muted" id="cnPvNote"></div>
                            </div>
                            <div class="col-5">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr><td>Subtotal</td><td class="text-end" id="cnPvSubtotal">Rs 0</td></tr>
                                        <tr><td>Tax</td><td class="text-end" id="cnPvTax">Rs 0</td></tr>
                                        <tr class="fw-bold table-danger"><td>Credit Amount</td><td class="text-end" id="cnPvTotal">Rs 0</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">To make the PDF, press "Download / Print PDF", then choose <strong>Save as PDF</strong> in the print dialog.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Saved credit notes</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Number</th><th>Date</th><th>Against invoice</th><th>Party</th><th class="text-end">Amount</th><th></th></tr>
                            </thead>
                            <tbody id="cnSavedRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="cnSavedEmpty">No credit note saved yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the original invoice number or choose from saved invoices, then select the reason.</li>
                <li>Enter the party and the credit items/amount.</li>
                <li>Press <strong>Save Credit Note</strong>, then make the PDF with <strong>Download / Print PDF</strong>.</li>
            </ol>
            <p class="small text-muted">A credit note is a record of a reduction in the client account (less receivable). Data stays only in your browser. Figures are informational, not tax advice.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan_credit_notes';
    var INV_KEY = 'azlaan_invoices';
    var CLIENT_KEY = 'azlaan_clients';
    var LOGO_KEY = 'azlaan_invoice_logo';
    var BIZ_KEY = 'azlaan_invoice_biz';
    var CNT_KEY = 'azlaan_cn_counter';

    var $ = function (id) { return document.getElementById(id); };
    var cnNumber = $('cnNumber'), cnDate = $('cnDate'), cnInvoice = $('cnInvoice'),
        cnInvoiceManual = $('cnInvoiceManual'), cnReason = $('cnReason'),
        clientPick = $('cnClientPick'), clientName = $('cnClientName'),
        clientAddress = $('cnClientAddress'), clientPhone = $('cnClientPhone'),
        itemRows = $('cnItemRows'), noteEl = $('cnNote'), errorBox = $('cnErrorBox'),
        savedRows = $('cnSavedRows'), savedEmpty = $('cnSavedEmpty');

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
        return 'CN-' + new Date().getFullYear() + '-' + ('0000' + c).slice(-4);
    }

    function loadInvoices() {
        var invs = loadJson(INV_KEY, []);
        cnInvoice.innerHTML = '<option value="">— Choose from saved invoices or write manually —</option>';
        invs.forEach(function (inv) {
            var o = document.createElement('option');
            o.value = inv.number;
            o.textContent = inv.number + ' — ' + (inv.client ? (inv.client.name || '') : '') + ' (' + fmt(inv.grandTotal) + ')';
            cnInvoice.appendChild(o);
        });
    }
    cnInvoice.addEventListener('change', function () {
        if (cnInvoice.value) {
            cnInvoiceManual.value = cnInvoice.value;
            // auto-fill party from the invoice
            var invs = loadJson(INV_KEY, []);
            for (var i = 0; i < invs.length; i++) {
                if (invs[i].number === cnInvoice.value && invs[i].client) {
                    clientName.value = invs[i].client.name || '';
                    clientAddress.value = invs[i].client.address || '';
                    clientPhone.value = invs[i].client.phone || '';
                    break;
                }
            }
        }
        recalc();
    });

    function loadClients() {
        var clients = loadJson(CLIENT_KEY, []);
        clientPick.innerHTML = '<option value="">— Write manually —</option>';
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
        recalc();
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
    $('cnAddItemBtn').addEventListener('click', function () { addItemRow('', 1, 0, 0); });

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
        return { sub: sub, tax: taxT, grand: sub + taxT };
    }

    function recalc() {
        var biz = loadJson(BIZ_KEY, {});
        $('cnPvBiz').textContent = biz.name || '—';
        var meta = [];
        if (biz.address) meta.push(biz.address);
        if (biz.phone) meta.push(biz.phone);
        $('cnPvBizMeta').textContent = meta.join(' • ') || '—';
        var items = readItems();
        var t = totals(items);
        var rows = itemRows.querySelectorAll('tr');
        rows.forEach(function (tr, i) {
            var it = items[i];
            if (it) tr.querySelector('.i-amt').textContent = fmt(it.qty * it.rate);
        });
        $('cnPvNumber').textContent = cnNumber.value.trim() || '—';
        $('cnPvDate').textContent = 'Date: ' + (cnDate.value || '—');
        $('cnPvInvoice').textContent = (cnInvoiceManual.value.trim() || cnInvoice.value) || '—';
        $('cnPvReason').textContent = cnReason.value;
        $('cnPvClient').textContent = clientName.value.trim() || '—';
        var cmeta = [];
        if (clientAddress.value.trim()) cmeta.push(clientAddress.value.trim());
        if (clientPhone.value.trim()) cmeta.push(clientPhone.value.trim());
        $('cnPvClientMeta').textContent = cmeta.join(' • ') || '';
        var html = '';
        items.forEach(function (it, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(it.desc || '—') + '</td>' +
                '<td class="text-end">' + it.qty + '</td><td class="text-end">' + fmt(it.rate) + '</td>' +
                '<td class="text-end">' + it.tax + '%</td><td class="text-end">' + fmt(it.qty * it.rate) + '</td></tr>';
        });
        $('cnPvItems').innerHTML = html || '<tr><td colspan="6" class="text-center text-muted">No items</td></tr>';
        $('cnPvSubtotal').textContent = fmt(t.sub);
        $('cnPvTax').textContent = fmt(t.tax);
        $('cnPvTotal').textContent = fmt(t.grand);
        $('cnPvNote').textContent = noteEl.value.trim();
    }
    [cnNumber, cnDate, cnInvoiceManual, cnReason, clientName, clientAddress, clientPhone, noteEl].forEach(function (el) {
        el.addEventListener('input', recalc);
        el.addEventListener('change', recalc);
    });
    $('cnRefreshBtn').addEventListener('click', recalc);
    $('cnPrintBtn').addEventListener('click', function () {
        hideError();
        if (!readItems().length) { showError('Please add at least one item first.'); return; }
        recalc();
        window.print();
    });

    function renderSaved() {
        var list = loadJson(KEY, []);
        savedRows.innerHTML = '';
        savedEmpty.style.display = list.length ? 'none' : '';
        list.slice().reverse().forEach(function (cn) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td'); tdN.textContent = cn.number;
            var tdD = document.createElement('td'); tdD.textContent = cn.date || '—';
            var tdI = document.createElement('td'); tdI.textContent = cn.originalInvoice || '—';
            var tdC = document.createElement('td'); tdC.textContent = cn.party ? (cn.party.name || '—') : '—';
            var tdT = document.createElement('td'); tdT.className = 'text-end fw-semibold text-danger'; tdT.textContent = fmt(cn.grandTotal);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = '×';
            del.setAttribute('aria-label', 'Delete credit note');
            del.addEventListener('click', function () {
                if (!confirm('Delete ' + cn.number + '?')) return;
                saveJson(KEY, loadJson(KEY, []).filter(function (x) { return x.id !== cn.id; }));
                renderSaved();
            });
            tdX.appendChild(del);
            tr.appendChild(tdN); tr.appendChild(tdD); tr.appendChild(tdI);
            tr.appendChild(tdC); tr.appendChild(tdT); tr.appendChild(tdX);
            savedRows.appendChild(tr);
        });
    }

    $('cnSaveBtn').addEventListener('click', function () {
        hideError();
        var items = readItems();
        if (!items.length) { showError('Please add at least one item first.'); return; }
        var invNo = cnInvoiceManual.value.trim() || cnInvoice.value;
        if (!invNo) { showError('Enter or select the original invoice number.'); return; }
        if (!clientName.value.trim()) { showError('Enter the party name.'); return; }
        var t = totals(items);
        var list = loadJson(KEY, []);
        var num = cnNumber.value.trim() || nextNumber();
        list.push({
            id: 'cn' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36),
            number: num,
            date: cnDate.value || todayStr(),
            originalInvoice: invNo,
            reason: cnReason.value,
            party: { name: clientName.value.trim(), address: clientAddress.value.trim(), phone: clientPhone.value.trim() },
            items: items,
            subtotal: Math.round(t.sub * 100) / 100,
            taxTotal: Math.round(t.tax * 100) / 100,
            grandTotal: Math.round(t.grand * 100) / 100,
            note: noteEl.value.trim(),
            createdAt: new Date().toISOString()
        });
        saveJson(KEY, list);
        renderSaved();
        cnNumber.value = nextNumber();
        errorBox.textContent = 'Credit note ' + num + ' saved.';
        errorBox.classList.remove('d-none', 'alert-danger');
        errorBox.classList.add('alert-success');
        setTimeout(function () {
            errorBox.classList.add('alert-danger');
            errorBox.classList.remove('alert-success');
            hideError();
        }, 3000);
    });

    cnDate.value = todayStr();
    cnNumber.value = nextNumber();
    loadInvoices();
    loadClients();
    addItemRow('', 1, 0, 0);
    renderSaved();
    recalc();
})();
</script>
@endsection
