@extends('layouts.app')

@section('title', 'Warranty Slip Maker - Azlaan Tools')
@section('meta_description', 'Make printable product warranty slips with item details, duration and terms for your shop. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Warranty Slip Maker</h1>
            <p class="lead text-muted">Make a warranty slip for your shop: enter the customer, product, duration and terms, then print it and give it to the customer.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="shopName" class="form-label fw-semibold">Shop / Business name</label>
                            <input type="text" class="form-control" id="shopName" placeholder="e.g. Azlaan Electronics">
                        </div>
                        <div class="col-md-6">
                            <label for="shopPhone" class="form-label fw-semibold">Shop phone</label>
                            <input type="text" class="form-control" id="shopPhone" placeholder="e.g. 0300-1234567">
                        </div>
                        <div class="col-md-6">
                            <label for="custName" class="form-label fw-semibold">Customer name</label>
                            <input type="text" class="form-control" id="custName" placeholder="e.g. Ahmed Khan">
                        </div>
                        <div class="col-md-6">
                            <label for="custPhone" class="form-label fw-semibold">Customer phone</label>
                            <input type="text" class="form-control" id="custPhone" placeholder="e.g. 0321-7654321">
                        </div>
                        <div class="col-md-6">
                            <label for="productName" class="form-label fw-semibold">Product name</label>
                            <input type="text" class="form-control" id="productName" placeholder="e.g. Dawlance Fridge">
                        </div>
                        <div class="col-md-6">
                            <label for="serialNo" class="form-label fw-semibold">Model / Serial No.</label>
                            <input type="text" class="form-control" id="serialNo" placeholder="e.g. DW-115CHZ / SN12345">
                        </div>
                        <div class="col-md-4">
                            <label for="buyDate" class="form-label fw-semibold">Purchase date</label>
                            <input type="date" class="form-control" id="buyDate">
                        </div>
                        <div class="col-md-4">
                            <label for="warrNum" class="form-label fw-semibold">Warranty duration</label>
                            <input type="number" class="form-control" id="warrNum" value="12" min="1">
                        </div>
                        <div class="col-md-4">
                            <label for="warrUnit" class="form-label fw-semibold">Unit</label>
                            <select class="form-select" id="warrUnit">
                                <option value="months">Months</option>
                                <option value="years">Years</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="termsText" class="form-label fw-semibold">Warranty terms</label>
                            <textarea class="form-control" id="termsText" rows="3">1. Warranty applies to manufacturing defects only.
2. Damage from breaking, water or electric shock is not covered.
3. Bring this slip and the purchase receipt when making a claim.
4. Claims are not accepted after the warranty period ends.</textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Warranty Slip</button>
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
                <li>Enter the shop and customer details.</li>
                <li>Enter the product, serial number, purchase date and warranty duration.</li>
                <li>Press <strong>Make Warranty Slip</strong> — the expiry date will be calculated automatically.</li>
                <li>Use <strong>Print / PDF</strong> to print the slip and give it to the customer.</li>
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
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var printArea = document.getElementById('printArea');

    document.getElementById('buyDate').valueAsDate = new Date();

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function fmtDate(d) {
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var shop = document.getElementById('shopName').value.trim();
        var cust = document.getElementById('custName').value.trim();
        var prod = document.getElementById('productName').value.trim();
        var buyVal = document.getElementById('buyDate').value;
        var wnum = parseInt(document.getElementById('warrNum').value, 10);
        if (!shop) { showError('Please enter the shop name.'); return; }
        if (!cust) { showError('Please enter the customer name.'); return; }
        if (!prod) { showError('Please enter the product name.'); return; }
        if (!buyVal) { showError('Please select the purchase date.'); return; }
        if (!wnum || wnum < 1) { showError('The warranty duration must be at least 1.'); return; }
        var bp = buyVal.split('-');
        var buy = new Date(parseInt(bp[0], 10), parseInt(bp[1], 10) - 1, parseInt(bp[2], 10));
        var exp = new Date(buy.getFullYear(), buy.getMonth(), buy.getDate());
        var unit = document.getElementById('warrUnit').value;
        if (unit === 'months') exp.setMonth(exp.getMonth() + wnum);
        else exp.setFullYear(exp.getFullYear() + wnum);
        var shopPhone = document.getElementById('shopPhone').value.trim();
        var custPhone = document.getElementById('custPhone').value.trim();
        var custLine = esc(cust);
        if (custPhone) { custLine += ' - ' + esc(custPhone); }
        var serial = document.getElementById('serialNo').value.trim();
        var terms = document.getElementById('termsText').value.trim();
        var slipNo = 'W-' + Date.now().toString().slice(-6);
        var muddat = wnum + (unit === 'months' ? ' Months' : ' Years');

        var termsHtml = '';
        if (terms) {
            termsHtml = '<h6 class="mt-3">Terms:</h6><ol class="small">';
            terms.split('\n').forEach(function (line) {
                line = line.trim();
                var nm = line.match(/^\d+/);
                if (nm) {
                    line = line.slice(nm[0].length);
                    while (line.length && ') (.'.indexOf(line.charAt(0)) > -1) {
                        line = line.slice(1);
                    }
                }
                if (line) termsHtml += '<li>' + esc(line) + '</li>';
            });
            termsHtml += '</ol>';
        }

        printArea.innerHTML =
            '<div class="text-center mb-3"><h4 class="mb-0">' + esc(shop) + '</h4>' +
            (shopPhone ? '<div class="text-muted small">' + esc(shopPhone) + '</div>' : '') +
            '<h5 class="mt-2"><span class="badge bg-primary">WARRANTY SLIP</span></h5></div>' +
            '<table class="table table-sm table-bordered"><tbody>' +
            '<tr><th style="width:35%">Slip No.</th><td>' + slipNo + '</td></tr>' +
            '<tr><th>Customer</th><td>' + custLine + '</td></tr>' +
            '<tr><th>Product</th><td>' + esc(prod) + '</td></tr>' +
            (serial ? '<tr><th>Model / Serial</th><td>' + esc(serial) + '</td></tr>' : '') +
            '<tr><th>Purchase date</th><td>' + fmtDate(buy) + '</td></tr>' +
            '<tr><th>Warranty duration</th><td>' + esc(muddat) + '</td></tr>' +
            '<tr class="table-light"><th>Warranty ends</th><td class="fw-bold">' + fmtDate(exp) + '</td></tr>' +
            '</tbody></table>' +
            termsHtml +
            '<div class="d-flex justify-content-between mt-5"><div class="small">Shop stamp / Signature<br><br>______________</div><div class="small">Customer Signature<br><br>______________</div></div>';
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
