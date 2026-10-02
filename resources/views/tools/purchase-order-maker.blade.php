@extends('layouts.app')

@section('title', 'Purchase Order Maker - Azlaan Tools')
@section('meta_description', 'Create a professional purchase order for suppliers with items, rates and delivery terms, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Purchase Order Maker</h1>
            <p class="lead text-muted">Create a professional purchase order to send to your supplier — with items, rates, tax and delivery terms. Then print it or save as PDF.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="buyer" class="form-label fw-semibold">Your business name (buyer)</label>
                            <input type="text" class="form-control" id="buyer" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="buyerAddr" class="form-label fw-semibold">Buyer address</label>
                            <input type="text" class="form-control" id="buyerAddr" placeholder="Shop address, city">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier" class="form-label fw-semibold">Supplier name</label>
                            <input type="text" class="form-control" id="supplier" placeholder="e.g. XYZ Traders">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="supplierAddr" class="form-label fw-semibold">Supplier address</label>
                            <input type="text" class="form-control" id="supplierAddr" placeholder="Supplier address, city">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="poNumber" class="form-label fw-semibold">PO number</label>
                            <input type="text" class="form-control" id="poNumber" placeholder="e.g. PO-2026-001">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="poDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="poDate">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="deliveryDate" class="form-label fw-semibold">Delivery by</label>
                            <input type="date" class="form-control" id="deliveryDate">
                        </div>
                    </div>

                    <h5 class="mt-2">Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr><th>Item / description</th><th style="width:90px">Qty</th><th style="width:130px">Rate (Rs)</th><th style="width:40px"></th></tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addItemBtn">+ Add item</button>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="taxPct" class="form-label fw-semibold">Tax % (optional)</label>
                            <input type="number" class="form-control" id="taxPct" min="0" max="100" step="0.1" value="0">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="terms" class="form-label fw-semibold">Delivery / payment terms</label>
                            <input type="text" class="form-control" id="terms" placeholder="e.g. Payment within 15 days of delivery">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Purchase Order</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="border rounded p-4 bg-white" id="poPreview"></div>
                        <button type="button" class="btn btn-success w-100 mt-3" id="printBtn">Print / Save as PDF</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the buyer and supplier details.</li>
                <li>Add items — quantity and rate for each item.</li>
                <li>Press "Generate Purchase Order", check the preview, and print or save as PDF.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var itemsBody = document.getElementById('itemsBody');
    var addItemBtn = document.getElementById('addItemBtn');

    document.getElementById('poDate').value = new Date().toISOString().slice(0, 10);

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function addItemRow() {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" class="form-control form-control-sm item-name" placeholder="Item name"></td>' +
            '<td><input type="number" class="form-control form-control-sm item-qty" min="0" step="1" value="1"></td>' +
            '<td><input type="number" class="form-control form-control-sm item-rate" min="0" step="0.01" placeholder="0.00"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row">&times;</button></td>';
        tr.querySelector('.rm-row').addEventListener('click', function () { tr.remove(); });
        itemsBody.appendChild(tr);
    }
    addItemBtn.addEventListener('click', addItemRow);
    addItemRow(); addItemRow(); addItemRow();

    goBtn.addEventListener('click', function () {
        hideError();
        var buyer = document.getElementById('buyer').value.trim();
        var buyerAddr = document.getElementById('buyerAddr').value.trim();
        var supplier = document.getElementById('supplier').value.trim();
        var supplierAddr = document.getElementById('supplierAddr').value.trim();
        var poNumber = document.getElementById('poNumber').value.trim() || ('PO-' + Date.now().toString().slice(-6));
        var poDate = document.getElementById('poDate').value;
        var deliveryDate = document.getElementById('deliveryDate').value;
        var terms = document.getElementById('terms').value.trim();
        var taxPct = parseFloat(document.getElementById('taxPct').value) || 0;

        if (!buyer) { showError('Please enter your business name.'); return; }
        if (!supplier) { showError('Please enter the supplier name.'); return; }

        var rows = itemsBody.querySelectorAll('tr');
        var items = [];
        for (var i = 0; i < rows.length; i++) {
            var name = rows[i].querySelector('.item-name').value.trim();
            var qty = parseFloat(rows[i].querySelector('.item-qty').value) || 0;
            var rate = parseFloat(rows[i].querySelector('.item-rate').value) || 0;
            if (name && qty > 0 && rate >= 0) items.push({ name: name, qty: qty, rate: rate });
        }
        if (items.length === 0) { showError('Add at least one item (with name, qty and rate).'); return; }

        var subtotal = 0, html = '';
        for (var j = 0; j < items.length; j++) {
            var line = items[j].qty * items[j].rate;
            subtotal += line;
            html += '<tr><td>' + (j + 1) + '</td><td>' + esc(items[j].name) + '</td>' +
                '<td class="text-center">' + items[j].qty + '</td>' +
                '<td class="text-end">' + fmt(items[j].rate) + '</td>' +
                '<td class="text-end">' + fmt(line) + '</td></tr>';
        }
        var tax = subtotal * taxPct / 100;
        var total = subtotal + tax;

        var preview =
            '<div class="text-center mb-3"><h3 class="mb-0">PURCHASE ORDER</h3>' +
            '<div class="text-muted">' + esc(poNumber) + (poDate ? ' &nbsp;|&nbsp; ' + esc(poDate) : '') + '</div></div>' +
            '<div class="row mb-3"><div class="col-6"><strong>Buyer:</strong><br>' + esc(buyer) + (buyerAddr ? '<br>' + esc(buyerAddr) : '') + '</div>' +
            '<div class="col-6"><strong>Supplier:</strong><br>' + esc(supplier) + (supplierAddr ? '<br>' + esc(supplierAddr) : '') + '</div></div>' +
            (deliveryDate ? '<p><strong>Delivery by:</strong> ' + esc(deliveryDate) + '</p>' : '') +
            '<table class="table table-bordered"><thead class="table-light"><tr><th>#</th><th>Item</th><th>Qty</th><th>Rate</th><th>Amount</th></tr></thead>' +
            '<tbody>' + html + '</tbody><tfoot>' +
            '<tr><th colspan="4" class="text-end">Subtotal</th><th class="text-end">' + fmt(subtotal) + '</th></tr>' +
            (taxPct > 0 ? '<tr><th colspan="4" class="text-end">Tax (' + taxPct + '%)</th><th class="text-end">' + fmt(tax) + '</th></tr>' : '') +
            '<tr class="table-light"><th colspan="4" class="text-end">Grand Total</th><th class="text-end">' + fmt(total) + '</th></tr>' +
            '</tfoot></table>' +
            (terms ? '<p><strong>Terms:</strong> ' + esc(terms) + '</p>' : '') +
            '<div class="row mt-5"><div class="col-6 text-center"><br><br>__________________<br>Authorized signature</div>' +
            '<div class="col-6 text-center"><br><br>__________________<br>Supplier acceptance</div></div>';

        document.getElementById('poPreview').innerHTML = preview;
        results.classList.remove('d-none');
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        var w = window.open('', '_blank');
        w.document.write('<!DOCTYPE html><html><head><title>Purchase Order</title>' +
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>' +
            '<body><div class="container py-4">' + document.getElementById('poPreview').innerHTML + '</div>' +
            '<script>window.onload=function(){window.print();}<\/script></body></html>');
        w.document.close();
    });
})();
</script>
@endsection
