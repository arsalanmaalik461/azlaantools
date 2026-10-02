@extends('layouts.app')

@section('title', 'Shop Rate List Maker - Azlaan Tools')
@section('meta_description', 'Free shop rate list maker: create a printable price list for your barber shop, tailor, workshop or services with your shop name and phone.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Shop Rate List Maker</h1>
            <p class="lead text-muted">Make a rate list for your services — for a barber, tailor, workshop or any work. Print it or send it on WhatsApp.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="shopName" class="form-label fw-semibold">Shop name</label>
                            <input type="text" class="form-control" id="shopName" placeholder="Mashallah Barber Shop">
                        </div>
                        <div class="col-md-6">
                            <label for="shopPhone" class="form-label fw-semibold">Phone number</label>
                            <input type="text" class="form-control" id="shopPhone" placeholder="0300-0000000" dir="ltr">
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label fw-semibold d-block">Card color</span>
                        <div class="d-flex gap-2 flex-wrap" id="themeRow">
                            <button type="button" class="btn btn-sm theme-btn border" data-c1="#6d28d9" data-c2="#4c1d95" style="background:#6d28d9;color:#fff;" aria-label="Violet">Violet</button>
                            <button type="button" class="btn btn-sm theme-btn border" data-c1="#15803d" data-c2="#14532d" style="background:#15803d;color:#fff;" aria-label="Green">Green</button>
                            <button type="button" class="btn btn-sm theme-btn border" data-c1="#b45309" data-c2="#78350f" style="background:#b45309;color:#fff;" aria-label="Amber">Amber</button>
                            <button type="button" class="btn btn-sm theme-btn border" data-c1="#1d4ed8" data-c2="#1e3a8a" style="background:#1d4ed8;color:#fff;" aria-label="Blue">Blue</button>
                            <button type="button" class="btn btn-sm theme-btn border" data-c1="#be123c" data-c2="#881337" style="background:#be123c;color:#fff;" aria-label="Rose">Rose</button>
                            <button type="button" class="btn btn-sm theme-btn border" data-c1="#0f766e" data-c2="#134e4a" style="background:#0f766e;color:#fff;" aria-label="Teal">Teal</button>
                        </div>
                    </div>
                    <hr>
                    <h5>Add a service</h5>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" id="itemName" placeholder="Service name (example: Hair Cut)">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" id="itemPrice" placeholder="Price (Rs)" min="0" dir="ltr">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="itemCat" placeholder="Category (optional, example: Hair)">
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-success" id="addBtn">+ Add Service</button>
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Sample Data</button>
                        <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear All</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div class="table-responsive mt-3 d-none" id="itemsWrap">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light"><tr><th>Service</th><th>Category</th><th>Price</th><th></th></tr></thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>

                    <hr>
                    <h5>Preview</h5>
                    <div id="printArea">
                        <div id="rateCard" class="border rounded overflow-hidden mx-auto" style="max-width:520px;">
                            <div id="cardHead" class="text-white text-center p-4" style="background:linear-gradient(135deg,#6d28d9,#4c1d95);">
                                <div class="fs-4 fw-bold" id="pvShop">Your Shop</div>
                                <div class="fs-6 fw-bold mt-1">RATE LIST</div>
                                <div class="small opacity-75" id="pvPhone"></div>
                            </div>
                            <div id="cardBody" class="p-3 bg-white"></div>
                            <div class="text-center small text-muted py-2 border-top" id="cardFoot">Thank You!</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="printBtn">Print / Make PDF</button>
                        <button type="button" class="btn btn-outline-success" id="waBtn">Copy WhatsApp Text</button>
                        <button type="button" class="btn btn-outline-primary" id="dlBtn">Download HTML File</button>
                    </div>
                    <div class="small text-muted mt-2" id="actMsg"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Set the shop name, phone and card color.</li>
                <li>Add services and their prices — they appear live in the preview.</li>
                <li>Press Print to print on paper, or copy the WhatsApp text and send it to customers.</li>
            </ol>
        </div>
    </div>
</div>
<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
    #rateCard { max-width: 100% !important; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var shopName = document.getElementById('shopName');
    var shopPhone = document.getElementById('shopPhone');
    var itemName = document.getElementById('itemName');
    var itemPrice = document.getElementById('itemPrice');
    var itemCat = document.getElementById('itemCat');
    var addBtn = document.getElementById('addBtn');
    var sampleBtn = document.getElementById('sampleBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var itemsWrap = document.getElementById('itemsWrap');
    var itemsBody = document.getElementById('itemsBody');
    var pvShop = document.getElementById('pvShop');
    var pvPhone = document.getElementById('pvPhone');
    var cardHead = document.getElementById('cardHead');
    var cardBody = document.getElementById('cardBody');
    var printBtn = document.getElementById('printBtn');
    var waBtn = document.getElementById('waBtn');
    var dlBtn = document.getElementById('dlBtn');
    var actMsg = document.getElementById('actMsg');

    var items = [];
    var theme = { c1: '#6d28d9', c2: '#4c1d95' };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmtRs(n) { return 'Rs ' + Number(n).toLocaleString('en-PK'); }

    function render() {
        hideError();
        actMsg.textContent = '';
        pvShop.textContent = shopName.value.trim() || 'Your Shop';
        pvPhone.textContent = shopPhone.value.trim() ? ('Phone: ' + shopPhone.value.trim()) : '';
        cardHead.style.background = 'linear-gradient(135deg,' + theme.c1 + ',' + theme.c2 + ')';

        itemsWrap.classList.toggle('d-none', items.length === 0);
        itemsBody.innerHTML = '';
        items.forEach(function (it, i) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = it.name;
            var td2 = document.createElement('td'); td2.textContent = it.cat || '—';
            var td3 = document.createElement('td'); td3.className = 'fw-bold'; td3.textContent = fmtRs(it.price);
            var td4 = document.createElement('td');
            var b = document.createElement('button');
            b.type = 'button'; b.className = 'btn btn-sm btn-outline-danger'; b.textContent = 'Delete';
            b.setAttribute('data-i', i);
            b.addEventListener('click', function () { items.splice(i, 1); render(); });
            td4.appendChild(b);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4);
            itemsBody.appendChild(tr);
        });

        // preview card grouped by category
        cardBody.innerHTML = '';
        if (!items.length) {
            var p = document.createElement('p');
            p.className = 'text-muted text-center mb-0';
            p.textContent = 'No service added yet.';
            cardBody.appendChild(p);
            return;
        }
        var groups = {}, order = [];
        items.forEach(function (it) {
            var c = it.cat || 'Services';
            if (!groups[c]) { groups[c] = []; order.push(c); }
            groups[c].push(it);
        });
        order.forEach(function (c) {
            var h = document.createElement('div');
            h.className = 'fw-bold mt-2 mb-1';
            h.style.color = theme.c1;
            h.textContent = c;
            cardBody.appendChild(h);
            groups[c].forEach(function (it) {
                var row = document.createElement('div');
                row.className = 'd-flex justify-content-between border-bottom py-1';
                var nm = document.createElement('span'); nm.textContent = it.name;
                var pr = document.createElement('span'); pr.className = 'fw-bold'; pr.textContent = fmtRs(it.price);
                row.appendChild(nm); row.appendChild(pr);
                cardBody.appendChild(row);
            });
        });
    }

    document.querySelectorAll('.theme-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            theme = { c1: b.getAttribute('data-c1'), c2: b.getAttribute('data-c2') };
            document.querySelectorAll('.theme-btn').forEach(function (x) { x.classList.remove('ring'); });
            render();
        });
    });

    addBtn.addEventListener('click', function () {
        var n = itemName.value.trim(), pr = itemPrice.value.trim(), c = itemCat.value.trim();
        if (!n) { showError('Write the service name.'); return; }
        if (pr === '' || isNaN(+pr) || +pr < 0) { showError('Enter a valid price (in Rs).'); return; }
        items.push({ name: n, price: +pr, cat: c });
        itemName.value = ''; itemPrice.value = ''; itemCat.value = '';
        itemName.focus();
        render();
    });
    itemName.addEventListener('keydown', function (e) { if (e.key === 'Enter') addBtn.click(); });
    itemPrice.addEventListener('keydown', function (e) { if (e.key === 'Enter') addBtn.click(); });

    sampleBtn.addEventListener('click', function () {
        shopName.value = 'Mashallah Barber Shop';
        shopPhone.value = '0300-1234567';
        items = [
            { name: 'Hair Cut', price: 200, cat: 'Hair' },
            { name: 'Beard Trim', price: 150, cat: 'Hair' },
            { name: 'Hair Cut + Beard', price: 300, cat: 'Hair' },
            { name: 'Facial', price: 500, cat: 'Skin' },
            { name: 'Head Massage', price: 250, cat: 'Skin' }
        ];
        render();
    });
    clearBtn.addEventListener('click', function () {
        items = [];
        render();
    });
    shopName.addEventListener('input', render);
    shopPhone.addEventListener('input', render);

    printBtn.addEventListener('click', function () {
        if (!items.length) { showError('Add a service first.'); return; }
        window.print();
    });

    function waText() {
        var lines = [];
        lines.push('*' + (shopName.value.trim() || 'Rate List') + '*');
        lines.push('RATE LIST');
        if (shopPhone.value.trim()) lines.push('Phone: ' + shopPhone.value.trim());
        lines.push('--------------------------');
        items.forEach(function (it) {
            lines.push(it.name + ' — Rs ' + Number(it.price).toLocaleString('en-PK'));
        });
        lines.push('--------------------------');
        lines.push('Thank You!');
        return lines.join('\n');
    }
    waBtn.addEventListener('click', function () {
        if (!items.length) { showError('Add a service first.'); return; }
        var t = waText();
        function done() { actMsg.textContent = 'Text copied — paste it in WhatsApp.'; }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(done, function () { actMsg.textContent = 'Could not copy.'; });
        } else actMsg.textContent = 'Copy is not supported in this browser.';
    });

    dlBtn.addEventListener('click', function () {
        if (!items.length) { showError('Add a service first.'); return; }
        var rows = items.map(function (it) {
            return '<div style="display:flex;justify-content:space-between;border-bottom:1px solid #eee;padding:6px 0;">' +
                '<span>' + esc(it.name) + '</span><strong>Rs ' + Number(it.price).toLocaleString('en-PK') + '</strong></div>';
        }).join('');
        var html = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">' +
            '<title>' + esc(shopName.value.trim() || 'Rate List') + ' - Rate List</title></head>' +
            '<body style="font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:20px;">' +
            '<div style="max-width:520px;margin:auto;background:#fff;border-radius:12px;overflow:hidden;">' +
            '<div style="background:linear-gradient(135deg,' + theme.c1 + ',' + theme.c2 + ');color:#fff;text-align:center;padding:28px;">' +
            '<div style="font-size:24px;font-weight:bold;">' + esc(shopName.value.trim() || 'Your Shop') + '</div>' +
            '<div style="font-weight:bold;margin-top:6px;">RATE LIST</div>' +
            (shopPhone.value.trim() ? '<div style="opacity:.8;font-size:14px;">Phone: ' + esc(shopPhone.value.trim()) + '</div>' : '') +
            '</div><div style="padding:16px;">' + rows + '</div>' +
            '<div style="text-align:center;color:#888;font-size:13px;padding:10px;border-top:1px solid #eee;">Thank You!</div>' +
            '</div></body></html>';
        var blob = new Blob([html], { type: 'text/html' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'rate-list.html';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); }, 500);
        actMsg.textContent = 'The HTML file has been downloaded.';
    });

    render();
})();
</script>
@endsection
