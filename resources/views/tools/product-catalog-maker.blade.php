@extends('layouts.app')
@section('title', 'Product Catalog Maker - Shop Price List Free | Azlaan Tools')
@section('meta_description', 'Create a beautiful product catalog with photos, names and prices for free. Add items, preview live, then download or print — perfect for WhatsApp business sharing.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Product Catalog Maker</h1>
            <p class="lead text-muted">Make a catalog of your shop or business — with photos, names, rates and details. Download and share it on WhatsApp.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="shopName" class="form-label fw-semibold">Shop / Business name</label>
                        <input type="text" class="form-control" id="shopName" placeholder="e.g. Azlaan Electric Store">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pName" class="form-label fw-semibold">Product name</label>
                            <input type="text" class="form-control" id="pName" placeholder="e.g. Solar Panel 550W">
                        </div>
                        <div class="col-md-3">
                            <label for="pPrice" class="form-label fw-semibold">Price (Rs)</label>
                            <input type="number" class="form-control" id="pPrice" placeholder="e.g. 28000" min="0" step="any">
                        </div>
                        <div class="col-md-3">
                            <label for="pPhoto" class="form-label fw-semibold">Photo (optional)</label>
                            <input type="file" class="form-control" id="pPhoto" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label for="pDesc" class="form-label fw-semibold">Short description (optional)</label>
                            <input type="text" class="form-control" id="pDesc" placeholder="e.g. A-grade, 25 year warranty" maxlength="120">
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary mt-3" id="addBtn">+ Add Product</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="successBox" role="alert"></div>
                </div>
            </div>

            <div id="results" class="d-none">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" class="btn btn-primary" id="dlBtn">⬇ Download Catalog (HTML)</button>
                    <button type="button" class="btn btn-outline-secondary" id="printBtn">🖨 Print Catalog</button>
                    <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear All</button>
                </div>
                <div id="catalogPreview"></div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Write your shop name.</li>
                <li>For each product, enter its name, rate, photo and details, then click <strong>Add Product</strong>.</li>
                <li>See your live catalog below — remove any item that is wrong.</li>
                <li>Get the file with <strong>Download Catalog</strong> and share it on WhatsApp, or <strong>Print</strong> it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var shopName = document.getElementById('shopName');
    var pName = document.getElementById('pName');
    var pPrice = document.getElementById('pPrice');
    var pPhoto = document.getElementById('pPhoto');
    var pDesc = document.getElementById('pDesc');
    var addBtn = document.getElementById('addBtn');
    var dlBtn = document.getElementById('dlBtn');
    var printBtn = document.getElementById('printBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var successBox = document.getElementById('successBox');
    var results = document.getElementById('results');
    var catalogPreview = document.getElementById('catalogPreview');

    var products = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function hideAlerts() {
        errorBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }
    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function fmtRs(n) {
        return 'Rs ' + Number(n).toLocaleString('en-US', { maximumFractionDigits: 2 });
    }

    function resizeImage(file, cb) {
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var max = 500;
            var w = img.naturalWidth, h = img.naturalHeight;
            if (w > max || h > max) {
                var k = Math.min(max / w, max / h);
                w = Math.round(w * k); h = Math.round(h * k);
            }
            var c = document.createElement('canvas');
            c.width = w; c.height = h;
            c.getContext('2d').drawImage(img, 0, 0, w, h);
            URL.revokeObjectURL(url);
            cb(c.toDataURL('image/jpeg', 0.82));
        };
        img.onerror = function () { URL.revokeObjectURL(url); cb(null); };
        img.src = url;
    }

    function renderCatalog() {
        if (!products.length) {
            results.classList.add('d-none');
            return;
        }
        results.classList.remove('d-none');
        var name = esc(shopName.value.trim() || 'Product Catalog');
        var html = '<div class="card shadow-sm mb-4"><div class="card-body">';
        html += '<h2 class="text-center mb-1">' + name + '</h2>';
        html += '<p class="text-center text-muted mb-4">' + products.length + ' products • ' + esc(new Date().toLocaleDateString()) + '</p>';
        html += '<div class="row g-3">';
        products.forEach(function (p, i) {
            html += '<div class="col-6 col-md-4 col-lg-3"><div class="card h-100">';
            if (p.photo) {
                html += '<img src="' + p.photo + '" class="card-img-top" alt="' + esc(p.name) + '" style="height:150px;object-fit:cover;">';
            } else {
                html += '<div class="card-img-top d-flex align-items-center justify-content-center bg-light text-muted" style="height:150px;font-size:2.5rem;">📦</div>';
            }
            html += '<div class="card-body p-2"><h6 class="card-title mb-1">' + esc(p.name) + '</h6>';
            html += '<p class="fw-bold text-success mb-1">' + fmtRs(p.price) + '</p>';
            if (p.desc) html += '<p class="small text-muted mb-2">' + esc(p.desc) + '</p>';
            html += '<button type="button" class="btn btn-sm btn-outline-danger w-100" data-rm="' + i + '">Remove</button>';
            html += '</div></div></div>';
        });
        html += '</div></div></div>';
        catalogPreview.innerHTML = html;
        var btns = catalogPreview.querySelectorAll('[data-rm]');
        btns.forEach(function (b) {
            b.addEventListener('click', function () {
                products.splice(parseInt(b.getAttribute('data-rm'), 10), 1);
                renderCatalog();
            });
        });
    }

    addBtn.addEventListener('click', function () {
        hideAlerts();
        var name = pName.value.trim();
        var price = parseFloat(pPrice.value);
        if (!name) { showError('Please enter the product name.'); return; }
        if (isNaN(price) || price < 0) { showError('Please enter a valid price.'); return; }
        var desc = pDesc.value.trim();
        var file = pPhoto.files[0];
        function push(photo) {
            products.push({ name: name, price: price, desc: desc, photo: photo });
            pName.value = ''; pPrice.value = ''; pDesc.value = ''; pPhoto.value = '';
            renderCatalog();
            showSuccess('Product added: ' + name);
        }
        if (file) {
            resizeImage(file, function (dataUrl) {
                if (!dataUrl) { showError('Could not read the photo.'); return; }
                push(dataUrl);
            });
        } else {
            push(null);
        }
    });

    shopName.addEventListener('input', renderCatalog);

    function buildStandaloneHtml() {
        var name = esc(shopName.value.trim() || 'Product Catalog');
        var css = 'body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;color:#222}' +
            '.wrap{max-width:900px;margin:0 auto;background:#fff;border-radius:10px;padding:24px}' +
            'h1{text-align:center;margin:0 0 4px} .sub{text-align:center;color:#777;margin:0 0 20px}' +
            '.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px}' +
            '.card{border:1px solid #ddd;border-radius:8px;overflow:hidden}' +
            '.card img{width:100%;height:150px;object-fit:cover;display:block}' +
            '.body{padding:10px} .price{color:#198754;font-weight:bold;margin:4px 0}' +
            '.desc{font-size:12px;color:#666;margin:0} .foot{text-align:center;color:#999;font-size:12px;margin-top:20px}';
        var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' + name + '</title><style>' + css + '</style></head><body>';
        html += '<div class="wrap"><h1>' + name + '</h1><p class="sub">' + products.length + ' products</p><div class="grid">';
        products.forEach(function (p) {
            html += '<div class="card">';
            if (p.photo) html += '<img src="' + p.photo + '" alt="' + esc(p.name) + '">';
            html += '<div class="body"><strong>' + esc(p.name) + '</strong><div class="price">' + fmtRs(p.price) + '</div>';
            if (p.desc) html += '<p class="desc">' + esc(p.desc) + '</p>';
            html += '</div></div>';
        });
        html += '</div><p class="foot">Made with Azlaan Tools</p></div></body></html>';
        return html;
    }

    dlBtn.addEventListener('click', function () {
        if (!products.length) { showError('Add at least one product first.'); return; }
        var blob = new Blob([buildStandaloneHtml()], { type: 'text/html' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'product-catalog.html';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 2000);
        showSuccess('Catalog downloaded.');
    });

    printBtn.addEventListener('click', function () {
        if (!products.length) { showError('Add at least one product first.'); return; }
        var w = window.open('', '_blank');
        if (!w) { showError('Popup blocked — please allow popups to print.'); return; }
        w.document.write(buildStandaloneHtml());
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); }, 500);
    });

    clearBtn.addEventListener('click', function () {
        products = [];
        renderCatalog();
        hideAlerts();
    });
})();
</script>
@endsection
