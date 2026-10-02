@extends('layouts.app')

@section('title', 'Quotation Generator - Azlaan Tools')
@section('meta_description', 'Create neat printable quotations for your customers in seconds. Add items, discount and tax, then print. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Quotation Generator</h1>
            <p class="lead text-muted">Make a clean quotation for your customer: enter items, add discount and tax, then print or save as PDF. Everything in your browser, free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="bizName" class="form-label fw-semibold">Business name</label>
                            <input type="text" class="form-control" id="bizName" placeholder="e.g. Azlaan Electric Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="bizPhone" class="form-label fw-semibold">Business phone</label>
                            <input type="text" class="form-control" id="bizPhone" placeholder="e.g. 0300-1234567">
                        </div>
                        <div class="col-md-6">
                            <label for="custName" class="form-label fw-semibold">Customer name</label>
                            <input type="text" class="form-control" id="custName" placeholder="e.g. Ahmed Khan">
                        </div>
                        <div class="col-md-3">
                            <label for="quoteNo" class="form-label fw-semibold">Quotation no.</label>
                            <input type="text" class="form-control" id="quoteNo" placeholder="Auto">
                        </div>
                        <div class="col-md-3">
                            <label for="quoteDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="quoteDate">
                        </div>
                    </div>

                    <h6>Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr><th style="width:45%">Description</th><th style="width:15%">Qty</th><th style="width:20%">Rate (Rs)</th><th style="width:15%">Amount</th><th style="width:5%"></th></tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addItemBtn">+ Add Item</button>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="discountPct" class="form-label fw-semibold">Discount %</label>
                            <input type="number" class="form-control" id="discountPct" value="0" min="0" max="100">
                        </div>
                        <div class="col-md-6">
                            <label for="taxPct" class="form-label fw-semibold">Tax % (if any)</label>
                            <input type="number" class="form-control" id="taxPct" value="0" min="0">
                        </div>
                        <div class="col-12">
                            <label for="termsText" class="form-label fw-semibold">Terms / Notes</label>
                            <textarea class="form-control" id="termsText" rows="2">Payment due within 15 days. Prices valid for 30 days.</textarea>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Quotation</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Preview</h5>
                            <button type="button" class="btn btn-success btn-sm" id="printBtn">Print / PDF</button>
                        </div>
                        <div id="printArea" class="border rounded p-4 bg-white"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your business and customer names.</li>
                <li>Use <strong>+ Add Item</strong> to add as many items as you want (with qty and rate).</li>
                <li>Set discount and tax, then press <strong>Create Quotation</strong>.</li>
                <li>Check the preview, then use <strong>Print / PDF</strong> to print or save as PDF.</li>
            </ol>
        </div>
    </div>
</div>
<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; border: none !important; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var itemsBody = document.getElementById('itemsBody');
    var addItemBtn = document.getElementById('addItemBtn');
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var printArea = document.getElementById('printArea');

    function addRow(desc, qty, rate) {
        var tr = document.createElement('tr');
        var tdD = document.createElement('td');
        var inD = document.createElement('input');
        inD.type = 'text'; inD.className = 'form-control form-control-sm item-desc';
        inD.placeholder = 'Item name'; inD.value = desc || '';
        tdD.appendChild(inD);
        var tdQ = document.createElement('td');
        var inQ = document.createElement('input');
        inQ.type = 'number'; inQ.className = 'form-control form-control-sm item-qty';
        inQ.min = '0'; inQ.value = qty || 1;
        tdQ.appendChild(inQ);
        var tdR = document.createElement('td');
        var inR = document.createElement('input');
        inR.type = 'number'; inR.className = 'form-control form-control-sm item-rate';
        inR.min = '0'; inR.value = rate || '';
        tdR.appendChild(inR);
        var tdA = document.createElement('td');
        tdA.className = 'item-amt fw-semibold';
        tdA.textContent = '0';
        var tdX = document.createElement('td');
        var bx = document.createElement('button');
        bx.type = 'button'; bx.className = 'btn btn-sm btn-outline-danger';
        bx.textContent = 'x';
        bx.addEventListener('click', function () { tr.remove(); recalc(); });
        tdX.appendChild(bx);
        tr.appendChild(tdD); tr.appendChild(tdQ); tr.appendChild(tdR); tr.appendChild(tdA); tr.appendChild(tdX);
        [inQ, inR].forEach(function (el) { el.addEventListener('input', recalc); });
        itemsBody.appendChild(tr);
        recalc();
    }

    function recalc() {
        var rows = itemsBody.querySelectorAll('tr');
        rows.forEach(function (tr) {
            var q = parseFloat(tr.querySelector('.item-qty').value) || 0;
            var r = parseFloat(tr.querySelector('.item-rate').value) || 0;
            tr.querySelector('.item-amt').textContent = fmt(q * r);
        });
    }

    function fmt(n) {
        return 'Rs ' + (Math.round(n * 100) / 100).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    addItemBtn.addEventListener('click', function () { addRow('', 1, ''); });
    addRow('', 1, '');
    addRow('', 1, '');

    document.getElementById('quoteDate').valueAsDate = new Date();

    goBtn.addEventListener('click', function () {
        hideError();
        var biz = document.getElementById('bizName').value.trim();
        var cust = document.getElementById('custName').value.trim();
        if (!biz) { showError('Please enter the business name.'); return; }
        if (!cust) { showError('Please enter the customer name.'); return; }
        var items = [];
        itemsBody.querySelectorAll('tr').forEach(function (tr) {
            var d = tr.querySelector('.item-desc').value.trim();
            var q = parseFloat(tr.querySelector('.item-qty').value) || 0;
            var r = parseFloat(tr.querySelector('.item-rate').value) || 0;
            if (d && q > 0 && r >= 0) items.push({ d: d, q: q, r: r, a: q * r });
        });
        if (!items.length) { showError('Add at least one item (name, qty, rate).'); return; }
        var sub = items.reduce(function (s, it) { return s + it.a; }, 0);
        var dPct = parseFloat(document.getElementById('discountPct').value) || 0;
        var tPct = parseFloat(document.getElementById('taxPct').value) || 0;
        var disc = sub * dPct / 100;
        var tax = (sub - disc) * tPct / 100;
        var total = sub - disc + tax;
        var qno = document.getElementById('quoteNo').value.trim() || ('Q-' + Date.now().toString().slice(-6));
        var qdate = document.getElementById('quoteDate').value || new Date().toISOString().slice(0, 10);
        var phone = document.getElementById('bizPhone').value.trim();
        var terms = document.getElementById('termsText').value.trim();

        var rowsHtml = items.map(function (it, i) {
            return '<tr><td>' + (i + 1) + '</td><td>' + esc(it.d) + '</td><td>' + it.q + '</td><td>' + fmt(it.r) + '</td><td class="text-end">' + fmt(it.a) + '</td></tr>';
        }).join('');

        printArea.innerHTML =
            '<div class="text-center mb-3"><h3 class="mb-1">' + esc(biz) + '</h3>' +
            (phone ? '<div class="text-muted">' + esc(phone) + '</div>' : '') +
            '<h5 class="mt-2 text-primary">QUOTATION</h5></div>' +
            '<div class="d-flex justify-content-between mb-3"><div><strong>Customer:</strong> ' + esc(cust) + '</div>' +
            '<div><strong>No:</strong> ' + esc(qno) + '<br><strong>Date:</strong> ' + esc(qdate) + '</div></div>' +
            '<table class="table table-bordered"><thead class="table-light"><tr><th>#</th><th>Description</th><th>Qty</th><th>Rate</th><th class="text-end">Amount</th></tr></thead><tbody>' +
            rowsHtml + '</tbody></table>' +
            '<table class="table table-sm ms-auto" style="max-width:280px"><tbody>' +
            '<tr><td>Subtotal</td><td class="text-end">' + fmt(sub) + '</td></tr>' +
            '<tr><td>Discount (' + dPct + '%)</td><td class="text-end">- ' + fmt(disc) + '</td></tr>' +
            '<tr><td>Tax (' + tPct + '%)</td><td class="text-end">' + fmt(tax) + '</td></tr>' +
            '<tr class="table-light fw-bold"><td>Grand Total</td><td class="text-end">' + fmt(total) + '</td></tr>' +
            '</tbody></table>' +
            (terms ? '<p class="small text-muted"><strong>Terms:</strong> ' + esc(terms) + '</p>' : '') +
            '<div class="d-flex justify-content-between mt-5"><div>Customer Signature<br>______________</div><div>For ' + esc(biz) + '<br>______________</div></div>';
        results.classList.remove('d-none');
    });

    printBtn.addEventListener('click', function () { window.print(); });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
})();
</script>
@endsection
