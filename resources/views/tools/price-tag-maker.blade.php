@extends('layouts.app')

@section('title', 'Price Tag Maker - Azlaan Tools')
@section('meta_description', 'Make printable price tags with barcodes for your shop or products, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Price Tag Maker</h1>
            <p class="lead text-muted">Make printable price tags for your shop — shelf and product labels with barcodes.</p>

            <div class="card shadow-sm mb-4 d-print-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="shopIn" class="form-label fw-semibold">Shop name</label>
                            <input type="text" class="form-control" id="shopIn" placeholder="e.g. Azlaan Store">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="prodIn" class="form-label fw-semibold">Product name</label>
                            <input type="text" class="form-control" id="prodIn" placeholder="e.g. LED Bulb 12W">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="priceIn" class="form-label fw-semibold">Price</label>
                            <input type="number" class="form-control" id="priceIn" min="0" step="any" placeholder="e.g. 250">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="currIn" class="form-label fw-semibold">Currency</label>
                            <input type="text" class="form-control" id="currIn" value="Rs">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="unitIn" class="form-label fw-semibold">Unit / note</label>
                            <input type="text" class="form-control" id="unitIn" placeholder="e.g. per piece">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="skuIn" class="form-label fw-semibold">Barcode text (SKU)</label>
                            <input type="text" class="form-control" id="skuIn" placeholder="e.g. AZL-001">
                            <div class="form-text">Only A-Z, 0-9, - . and space are allowed.</div>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="perPageSel" class="form-label fw-semibold">Tags per page</label>
                            <select class="form-select" id="perPageSel">
                                <option value="8">8 (large)</option>
                                <option value="12" selected>12 (medium)</option>
                                <option value="24">24 (small)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="barcodeChk" checked>
                        <label class="form-check-label" for="barcodeChk">Show barcode on tags</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn">Generate Tags</button>
                        <button type="button" class="btn btn-success flex-fill d-none" id="printBtn">Print Tags</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="results" class="d-none">
                <h2 class="d-print-none">Preview</h2>
                <div id="printArea"></div>
            </div>

            <h2 class="d-print-none mt-4">How to use</h2>
            <ol class="d-print-none">
                <li>Enter the shop and product name, price and barcode text.</li>
                <li>Press Generate Tags — you will see the tags in the preview.</li>
                <li>Press Print Tags and print on A4, then cut them out.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var shopIn = document.getElementById('shopIn');
    var prodIn = document.getElementById('prodIn');
    var priceIn = document.getElementById('priceIn');
    var currIn = document.getElementById('currIn');
    var unitIn = document.getElementById('unitIn');
    var skuIn = document.getElementById('skuIn');
    var perPageSel = document.getElementById('perPageSel');
    var barcodeChk = document.getElementById('barcodeChk');
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var printArea = document.getElementById('printArea');

    // Code39 patterns: 9 elements alternating bar/space, n=narrow w=wide
    var C39 = {
        '0':'nnnwwnwnn','1':'wnnwnnnnw','2':'nnwwnnnnw','3':'wnwwnnnnn','4':'nnnwwnnnw',
        '5':'wnnwwnnnn','6':'nnwwwnnnn','7':'nnnwnnwnw','8':'wnnwnnwnn','9':'nnwwnnwnn',
        'A':'wnnnnwnnw','B':'nnwnnwnnw','C':'wnwnnwnnn','D':'nnnnwwnnw','E':'wnnnwwnnn',
        'F':'nnwnwwnnn','G':'nnnnnwwnw','H':'wnnnnwwnn','I':'nnwnnwwnn','J':'nnnnwwwnn',
        'K':'wnnnnnnww','L':'nnwnnnnww','M':'wnwnnnnwn','N':'nnnnwnnww','O':'wnnnwnnwn',
        'P':'nnwnwnnwn','Q':'nnnnnnwww','R':'wnnnnnwwn','S':'nnwnnnwwn','T':'nnnnwnwwn',
        'U':'wwnnnnnnw','V':'nwwnnnnnw','W':'wwwnnnnnn','X':'nwnnwnnnw','Y':'wwnnwnnnn',
        'Z':'nwwnwnnnn','-':'nwnnnnwnw','.':'wwnnnnwnn',' ':'nwwnnnwnn','*':'nwnnwnwnn',
        '$':'nwnwnwnnn','/':'nwnwnnnwn','+':'nwnnnwnwn','%':'nnnwnwnwn'
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function drawBarcode(canvas, text) {
        var clean = ('*' + text.toUpperCase() + '*').split('').filter(function (c) { return C39[c]; }).join('');
        var unit = 2; // narrow bar px
        var totalUnits = 0;
        for (var i = 0; i < clean.length; i++) {
            var pat = C39[clean[i]];
            for (var j = 0; j < 9; j++) totalUnits += (pat[j] === 'w' ? 3 : 1);
            totalUnits += 1; // inter-char gap
        }
        canvas.width = Math.max(120, totalUnits * unit);
        canvas.height = 54;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';
        var x = 0;
        for (var c = 0; c < clean.length; c++) {
            var p = C39[clean[c]];
            for (var k = 0; k < 9; k++) {
                var wdt = (p[k] === 'w' ? 3 : 1) * unit;
                if (k % 2 === 0) ctx.fillRect(x, 2, wdt, 36);
                x += wdt;
            }
            x += unit;
        }
        ctx.font = '11px monospace';
        ctx.textAlign = 'center';
        ctx.fillText(text.toUpperCase(), canvas.width / 2, 50);
    }

    function esc(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var shop = shopIn.value.trim();
        var prod = prodIn.value.trim();
        var price = priceIn.value.trim();
        var curr = (currIn.value.trim() || 'Rs');
        var unit = unitIn.value.trim();
        var sku = skuIn.value.trim().toUpperCase();
        var perPage = parseInt(perPageSel.value, 10);
        var showBc = barcodeChk.checked;

        if (!prod) { showError('Please enter the product name.'); return; }
        if (price === '' || isNaN(parseFloat(price)) || parseFloat(price) < 0) {
            showError('Please enter a valid price.'); return;
        }
        if (showBc && sku) {
            var bad = sku.split('').filter(function (c) { return !C39[c]; });
            if (bad.length) { showError('This character is not allowed in the barcode: ' + bad[0]); return; }
        }

        var priceTxt = curr + ' ' + parseFloat(price).toLocaleString();
        printArea.innerHTML = '';
        var grid = document.createElement('div');
        grid.className = 'tag-grid tag-' + perPage;

        for (var i = 0; i < perPage; i++) {
            var tag = document.createElement('div');
            tag.className = 'price-tag';
            var html = '';
            if (shop) html += '<div class="tag-shop">' + esc(shop) + '</div>';
            html += '<div class="tag-prod">' + esc(prod) + '</div>';
            html += '<div class="tag-price">' + esc(priceTxt) + '</div>';
            if (unit) html += '<div class="tag-unit">' + esc(unit) + '</div>';
            tag.innerHTML = html;
            if (showBc && sku) {
                var cv = document.createElement('canvas');
                cv.className = 'tag-barcode';
                drawBarcode(cv, sku);
                tag.appendChild(cv);
            }
            grid.appendChild(tag);
        }
        printArea.appendChild(grid);
        results.classList.remove('d-none');
        printBtn.classList.remove('d-none');
        printBtn.focus();
    });

    printBtn.addEventListener('click', function () { window.print(); });
})();
</script>
<style>
.tag-grid { display: grid; gap: 10px; }
.tag-8 { grid-template-columns: repeat(2, 1fr); }
.tag-12 { grid-template-columns: repeat(3, 1fr); }
.tag-24 { grid-template-columns: repeat(4, 1fr); }
.price-tag {
    border: 2px dashed #333; border-radius: 8px; padding: 10px;
    text-align: center; background: #fff; page-break-inside: avoid;
}
.tag-shop { font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
.tag-prod { font-weight: 700; font-size: 16px; margin: 2px 0; }
.tag-price { font-weight: 800; font-size: 26px; color: #0d6efd; }
.tag-24 .tag-price { font-size: 20px; }
.tag-unit { font-size: 12px; color: #666; }
.tag-barcode { max-width: 100%; height: auto; margin-top: 4px; }
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
    .tag-grid { gap: 6px; }
    .price-tag { break-inside: avoid; }
}
</style>
@endsection
