@extends('layouts.app')

@section('title', 'Barcode Generator & Printer - Azlaan Tools')
@section('meta_description', 'Generate CODE128 barcodes for items without barcodes, keep a batch list and print a label sheet — free.')

@section('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #printSheet, #printSheet * { visibility: visible; }
    #printSheet { position: absolute; top: 0; left: 0; width: 100%; display: block !important; }
    .label-card { page-break-inside: avoid; }
}
.label-card {
    border: 1px dashed #999;
    border-radius: 6px;
    padding: 8px;
    text-align: center;
    width: 220px;
}
.label-card svg { max-width: 100%; height: auto; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Barcode Generator &amp; Printer</h1>
            <p class="lead text-muted">Generate barcodes for items without barcodes, keep them in a batch list and print a label sheet. Your data is saved only in your browser, nothing is uploaded.</p>

            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">New barcode</h5>
                            <div class="mb-3">
                                <label for="bcName" class="form-label fw-semibold">Item name</label>
                                <input type="text" class="form-control" id="bcName" placeholder="e.g. Surf Excel 1kg">
                            </div>
                            <div class="mb-3">
                                <label for="bcCode" class="form-label fw-semibold">SKU / Code (barcode text)</label>
                                <input type="text" class="form-control" id="bcCode" placeholder="e.g. AZL-0001">
                                <div class="form-text">Use only English letters, digits and - . $ / + % *.</div>
                            </div>
                            <div class="mb-3">
                                <label for="bcPrice" class="form-label fw-semibold">Price (Rs, optional)</label>
                                <input type="number" class="form-control" id="bcPrice" placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="bcCopies" class="form-label fw-semibold">Print copies (labels)</label>
                                <input type="number" class="form-control" id="bcCopies" value="1" min="1" max="50">
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="addBcBtn">Add Barcode</button>
                            <div class="alert alert-danger mt-3 d-none" id="bcError" role="alert"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Batch list (<span id="bcCount">0</span>)</h5>
                                <button type="button" class="btn btn-success btn-sm" id="printBtn">Print Label Sheet</button>
                            </div>
                            <div id="bcList"></div>
                            <p class="small text-muted mb-0" id="bcEmpty">No barcodes yet. Add one from the left side.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Enter the item name and SKU/code, then press <strong>Add Barcode</strong>.</li>
                <li>A barcode will appear under each item. Increase the copies to print more labels.</li>
                <li>Press <strong>Print Label Sheet</strong> — only the labels will print, not the rest of the page.</li>
            </ol>
            <p class="text-muted small">Barcodes are made in CODE128 format, which common scanners read. Your data is saved only in your browser.</p>
        </div>
    </div>
</div>

<div id="printSheet" style="display:none;">
    <div class="d-flex flex-wrap gap-2 p-2" id="labelGrid"></div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_barcodes';
    var bcName = document.getElementById('bcName');
    var bcCode = document.getElementById('bcCode');
    var bcPrice = document.getElementById('bcPrice');
    var bcCopies = document.getElementById('bcCopies');
    var addBcBtn = document.getElementById('addBcBtn');
    var bcError = document.getElementById('bcError');
    var bcList = document.getElementById('bcList');
    var bcEmpty = document.getElementById('bcEmpty');
    var bcCount = document.getElementById('bcCount');
    var printBtn = document.getElementById('printBtn');
    var printSheet = document.getElementById('printSheet');
    var labelGrid = document.getElementById('labelGrid');

    var items = [];
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) { var p = JSON.parse(raw); if (Array.isArray(p)) items = p; }
    } catch (e) { items = []; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(items)); } catch (e) {}
    }
    function uid() {
        return 'b' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36);
    }
    function showError(msg) {
        bcError.textContent = msg;
        bcError.classList.remove('d-none');
    }
    function hideError() {
        bcError.classList.add('d-none');
        bcError.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }
    function validCode(code) {
        return /^[A-Za-z0-9\-\.\$\/\+\%\* ]+$/.test(code);
    }
    function drawBarcode(svg, code) {
        if (typeof JsBarcode === 'undefined') return false;
        try {
            JsBarcode(svg, code, { format: 'CODE128', width: 2, height: 60, displayValue: true, fontSize: 14, margin: 6 });
            return true;
        } catch (e) { return false; }
    }

    function render() {
        hideError();
        bcList.innerHTML = '';
        bcEmpty.style.display = items.length ? 'none' : '';
        bcCount.textContent = items.length;
        if (typeof JsBarcode === 'undefined') {
            showError('The barcode library could not load (check your internet). The list is saved; try again after reloading the page.');
        }
        items.forEach(function (it, idx) {
            var row = document.createElement('div');
            row.className = 'border rounded p-3 mb-3 bg-light';
            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-start flex-wrap gap-2 mb-2';
            var title = document.createElement('div');
            title.innerHTML = '<strong>' + esc(it.name) + '</strong><br><small class="text-muted">Code: ' + esc(it.code) +
                (it.price ? ' &nbsp;•&nbsp; ' + fmt(it.price) : '') + '</small>';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = 'Delete';
            del.addEventListener('click', function () {
                if (!confirm('Delete this barcode?')) return;
                items.splice(idx, 1);
                save(); render();
            });
            head.appendChild(title);
            head.appendChild(del);
            row.appendChild(head);

            var svgWrap = document.createElement('div');
            svgWrap.className = 'bg-white border rounded p-2 text-center mb-2';
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('id', 'bcsvg' + idx);
            svgWrap.appendChild(svg);
            row.appendChild(svgWrap);

            var foot = document.createElement('div');
            foot.className = 'd-flex align-items-center gap-2';
            var lbl = document.createElement('label');
            lbl.className = 'small text-muted mb-0';
            lbl.textContent = 'Labels:';
            var num = document.createElement('input');
            num.type = 'number';
            num.min = '1';
            num.max = '50';
            num.value = it.copies || 1;
            num.className = 'form-control form-control-sm';
            num.style.width = '80px';
            num.addEventListener('change', function () {
                var v = parseInt(num.value, 10);
                if (isNaN(v) || v < 1) v = 1;
                if (v > 50) v = 50;
                it.copies = v;
                num.value = v;
                save();
            });
            foot.appendChild(lbl);
            foot.appendChild(num);
            row.appendChild(foot);

            bcList.appendChild(row);
            drawBarcode(svg, it.code);
        });
    }

    addBcBtn.addEventListener('click', function () {
        hideError();
        var name = bcName.value.trim();
        var code = bcCode.value.trim();
        var price = parseFloat(bcPrice.value);
        var copies = parseInt(bcCopies.value, 10);
        if (!name) { showError('Enter the item name.'); return; }
        if (!code) { showError('Enter the SKU / code.'); return; }
        if (!validCode(code)) { showError('Code may only contain letters, digits and - . $ / + % *.'); return; }
        if (isNaN(copies) || copies < 1) copies = 1;
        if (copies > 50) copies = 50;
        items.push({
            id: uid(),
            name: name,
            code: code,
            price: isNaN(price) ? 0 : Math.round(price * 100) / 100,
            copies: copies
        });
        save();
        bcName.value = '';
        bcCode.value = '';
        bcPrice.value = '';
        bcCopies.value = '1';
        bcName.focus();
        render();
    });

    printBtn.addEventListener('click', function () {
        hideError();
        if (!items.length) { showError('Add a barcode first to print.'); return; }
        if (typeof JsBarcode === 'undefined') { showError('The barcode library did not load — check your internet and reload the page.'); return; }
        labelGrid.innerHTML = '';
        var n = 0;
        items.forEach(function (it) {
            var copies = it.copies || 1;
            for (var i = 0; i < copies; i++) {
                var card = document.createElement('div');
                card.className = 'label-card';
                var nm = document.createElement('div');
                nm.style.fontWeight = '700';
                nm.style.fontSize = '13px';
                nm.textContent = it.name;
                var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                var pr = document.createElement('div');
                pr.style.fontSize = '12px';
                pr.textContent = it.price ? fmt(it.price) : '';
                card.appendChild(nm);
                card.appendChild(svg);
                card.appendChild(pr);
                labelGrid.appendChild(card);
                drawBarcode(svg, it.code);
                n++;
            }
        });
        if (!n) { showError('No labels were made.'); return; }
        setTimeout(function () { window.print(); }, 300);
    });

    render();
})();
</script>
@endsection
