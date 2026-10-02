@extends('layouts.app')
@section('title', 'PDF Invoice Generator - Azlaan Tools')
@section('meta_description', 'Create clean professional invoices and download them as PDF for free, no signup. Make your business invoice free.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">PDF Invoice Generator</h1>
            <p class="lead text-muted">Make a professional invoice for your business and download the PDF in one click. No signup — everything stays in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Business Details</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="bizName" class="form-label fw-semibold">Your business / name</label>
                            <input type="text" class="form-control" id="bizName" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="bizInfo" class="form-label fw-semibold">Address / phone / email</label>
                            <input type="text" class="form-control" id="bizInfo" placeholder="e.g. Faisalabad, 0300-0000000">
                        </div>
                    </div>

                    <h5 class="mb-3">Client &amp; Invoice</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="clientName" class="form-label fw-semibold">Bill to (client name)</label>
                            <input type="text" class="form-control" id="clientName" placeholder="Client name">
                        </div>
                        <div class="col-md-6">
                            <label for="clientInfo" class="form-label fw-semibold">Client address / phone</label>
                            <input type="text" class="form-control" id="clientInfo" placeholder="Client address or phone">
                        </div>
                        <div class="col-md-3">
                            <label for="invNo" class="form-label fw-semibold">Invoice #</label>
                            <input type="text" class="form-control" id="invNo" value="INV-001">
                        </div>
                        <div class="col-md-3">
                            <label for="invDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="invDate">
                        </div>
                        <div class="col-md-3">
                            <label for="dueDate" class="form-label fw-semibold">Due date</label>
                            <input type="date" class="form-control" id="dueDate">
                        </div>
                        <div class="col-md-3">
                            <label for="currencySel" class="form-label fw-semibold">Currency</label>
                            <select id="currencySel" class="form-select">
                                <option value="Rs" selected>PKR (Rs)</option>
                                <option value="$">USD ($)</option>
                                <option value="£">GBP (£)</option>
                                <option value="€">EUR (€)</option>
                                <option value="₹">INR (₹)</option>
                                <option value="AED">AED</option>
                                <option value="SAR">SAR</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3">Items</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:46%">Description</th>
                                    <th style="width:16%">Qty</th>
                                    <th style="width:20%">Unit price</th>
                                    <th style="width:14%">Amount</th>
                                    <th style="width:4%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-4" id="addItemBtn">+ Add Item</button>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="discountPct" class="form-label fw-semibold">Discount %</label>
                            <input type="number" class="form-control" id="discountPct" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-3">
                            <label for="taxPct" class="form-label fw-semibold">Tax %</label>
                            <input type="number" class="form-control" id="taxPct" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-6">
                            <label for="notesInput" class="form-label fw-semibold">Notes / payment terms</label>
                            <input type="text" class="form-control" id="notesInput" placeholder="e.g. Payment due within 7 days. Thank you!">
                        </div>
                    </div>

                    <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button type="button" class="btn btn-primary btn-lg" id="previewBtn">Preview Invoice</button>
                        <button type="button" class="btn btn-success btn-lg" id="pdfBtn">Download PDF</button>
                        <button type="button" class="btn btn-outline-secondary" id="resetBtn">Reset</button>
                    </div>

                    <div id="results" class="d-none">
                        <h5>Invoice Preview</h5>
                        <div id="invoicePreview" class="border rounded p-4 bg-white"></div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Note:</strong> This tool only creates a document — the amounts and tax you enter are printed as-is. This is not tax or legal advice.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your business and client details.</li>
                <li>Use <strong>+ Add Item</strong> to add items — enter the description, quantity and unit price.</li>
                <li>Add discount or tax % (if any), then press <strong>Preview Invoice</strong>.</li>
                <li>If the preview looks right, press <strong>Download PDF</strong> to save the PDF.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script>
(function () {
    'use strict';
    var itemsBody = document.getElementById('itemsBody');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var preview = document.getElementById('invoicePreview');
    var itemCount = 0;

    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    document.getElementById('invDate').value = todayStr();

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function fmtNum(n) {
        return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function addItem(desc, qty, price) {
        itemCount++;
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" class="form-control form-control-sm item-desc" placeholder="Service / product" value="' + esc(desc || '') + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm item-qty" min="0" step="any" value="' + (qty != null ? qty : 1) + '"></td>' +
            '<td><input type="number" class="form-control form-control-sm item-price" min="0" step="any" value="' + (price != null ? price : '') + '"></td>' +
            '<td class="item-amount text-end fw-semibold">0.00</td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger del-item" title="Remove">&times;</button></td>';
        itemsBody.appendChild(tr);
        tr.querySelector('.del-item').addEventListener('click', function () {
            if (itemsBody.rows.length > 1) { tr.remove(); recalc(); }
            else { showError('At least one item is needed.'); }
        });
        var inputs = tr.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener('input', recalc);
        }
        recalc();
    }

    function getItems() {
        var rows = itemsBody.querySelectorAll('tr');
        var out = [];
        for (var i = 0; i < rows.length; i++) {
            var d = rows[i].querySelector('.item-desc').value.trim();
            var q = parseFloat(rows[i].querySelector('.item-qty').value) || 0;
            var p = parseFloat(rows[i].querySelector('.item-price').value) || 0;
            out.push({ desc: d, qty: q, price: p, amount: q * p });
        }
        return out;
    }

    function totals() {
        var items = getItems();
        var sub = 0, i;
        for (i = 0; i < items.length; i++) { sub += items[i].amount; }
        var discPct = Math.min(100, Math.max(0, parseFloat(document.getElementById('discountPct').value) || 0));
        var taxPct = Math.min(100, Math.max(0, parseFloat(document.getElementById('taxPct').value) || 0));
        var disc = sub * discPct / 100;
        var tax = (sub - disc) * taxPct / 100;
        return { items: items, sub: sub, discPct: discPct, disc: disc, taxPct: taxPct, tax: tax, total: sub - disc + tax };
    }

    function recalc() {
        hideError();
        var t = totals();
        var rows = itemsBody.querySelectorAll('tr');
        for (var i = 0; i < rows.length; i++) {
            rows[i].querySelector('.item-amount').textContent = fmtNum(t.items[i].amount);
        }
    }

    function val(id) { return document.getElementById(id).value.trim(); }

    function buildPreviewHTML() {
        var t = totals();
        var cur = document.getElementById('currencySel').value;
        var rowsHtml = '';
        for (var i = 0; i < t.items.length; i++) {
            var it = t.items[i];
            rowsHtml += '<tr><td style="padding:8px;border:1px solid #ddd;">' + esc(it.desc || 'Item ' + (i + 1)) +
                '</td><td style="padding:8px;border:1px solid #ddd;text-align:center;">' + it.qty +
                '</td><td style="padding:8px;border:1px solid #ddd;text-align:right;">' + cur + ' ' + fmtNum(it.price) +
                '</td><td style="padding:8px;border:1px solid #ddd;text-align:right;">' + cur + ' ' + fmtNum(it.amount) + '</td></tr>';
        }
        var discRow = t.disc > 0 ? '<tr><td colspan="3" style="padding:6px;text-align:right;">Discount (' + t.discPct + '%)</td><td style="padding:6px;text-align:right;">- ' + cur + ' ' + fmtNum(t.disc) + '</td></tr>' : '';
        var taxRow = t.tax > 0 ? '<tr><td colspan="3" style="padding:6px;text-align:right;">Tax (' + t.taxPct + '%)</td><td style="padding:6px;text-align:right;">' + cur + ' ' + fmtNum(t.tax) + '</td></tr>' : '';
        var notes = esc(val('notesInput'));
        return '<div style="font-family:Arial,Helvetica,sans-serif;color:#222;max-width:640px;margin:0 auto;">' +
            '<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;">' +
            '<div><div style="font-size:22px;font-weight:bold;">' + esc(val('bizName') || 'Your Business') + '</div>' +
            '<div style="color:#555;font-size:13px;">' + esc(val('bizInfo')) + '</div></div>' +
            '<div style="text-align:right;"><div style="font-size:26px;font-weight:bold;color:#1a73e8;">INVOICE</div>' +
            '<div style="font-size:13px;">#' + esc(val('invNo')) + '</div></div></div>' +
            '<div style="display:flex;justify-content:space-between;margin-bottom:16px;font-size:14px;">' +
            '<div><strong>Bill To:</strong><br>' + esc(val('clientName') || '—') + '<br><span style="color:#555;">' + esc(val('clientInfo')) + '</span></div>' +
            '<div style="text-align:right;"><strong>Date:</strong> ' + esc(val('invDate')) + '<br><strong>Due:</strong> ' + esc(val('dueDate') || '—') + '</div></div>' +
            '<table style="width:100%;border-collapse:collapse;font-size:14px;margin-bottom:14px;">' +
            '<thead><tr style="background:#f1f5f9;"><th style="padding:8px;border:1px solid #ddd;text-align:left;">Description</th>' +
            '<th style="padding:8px;border:1px solid #ddd;">Qty</th><th style="padding:8px;border:1px solid #ddd;text-align:right;">Unit Price</th>' +
            '<th style="padding:8px;border:1px solid #ddd;text-align:right;">Amount</th></tr></thead><tbody>' + rowsHtml + '</tbody></table>' +
            '<table style="width:100%;font-size:14px;"><tr><td style="text-align:right;padding:6px;">Subtotal</td>' +
            '<td style="text-align:right;padding:6px;width:180px;">' + cur + ' ' + fmtNum(t.sub) + '</td></tr>' + discRow + taxRow +
            '<tr><td style="text-align:right;padding:8px;font-size:18px;font-weight:bold;">Total</td>' +
            '<td style="text-align:right;padding:8px;font-size:18px;font-weight:bold;color:#1a73e8;">' + cur + ' ' + fmtNum(t.total) + '</td></tr></table>' +
            (notes ? '<div style="margin-top:14px;font-size:13px;color:#555;"><strong>Notes:</strong> ' + notes + '</div>' : '') +
            '</div>';
    }

    function validate() {
        hideError();
        var items = getItems();
        for (var i = 0; i < items.length; i++) {
            if (items[i].qty < 0 || items[i].price < 0) { showError('Qty and price cannot be less than 0.'); return false; }
        }
        if (totals().sub <= 0) { showError('Add at least one item — the subtotal is 0. First enter the description, qty and price.'); return false; }
        return true;
    }

    document.getElementById('addItemBtn').addEventListener('click', function () { hideError(); addItem('', 1, ''); });
    document.getElementById('previewBtn').addEventListener('click', function () {
        if (!validate()) { results.classList.add('d-none'); return; }
        preview.innerHTML = buildPreviewHTML();
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    document.getElementById('pdfBtn').addEventListener('click', function () {
        if (!validate()) { results.classList.add('d-none'); return; }
        preview.innerHTML = buildPreviewHTML();
        results.classList.remove('d-none');
        if (typeof html2pdf === 'undefined') { showError('The PDF library did not load — check your internet and try again.'); return; }
        var fileName = (val('invNo') || 'invoice').replace(/[^\w\-]+/g, '_') + '.pdf';
        html2pdf().set({
            margin: 12,
            filename: fileName,
            image: { type: 'jpeg', quality: 0.95 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(preview).save();
    });
    document.getElementById('resetBtn').addEventListener('click', function () {
        hideError();
        itemsBody.innerHTML = '';
        document.getElementById('bizName').value = '';
        document.getElementById('bizInfo').value = '';
        document.getElementById('clientName').value = '';
        document.getElementById('clientInfo').value = '';
        document.getElementById('invNo').value = 'INV-001';
        document.getElementById('invDate').value = todayStr();
        document.getElementById('dueDate').value = '';
        document.getElementById('discountPct').value = 0;
        document.getElementById('taxPct').value = 0;
        document.getElementById('notesInput').value = '';
        addItem('', 1, '');
        results.classList.add('d-none');
    });

    addItem('', 1, '');
})();
</script>
@endsection
