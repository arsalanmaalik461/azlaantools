@extends('layouts.app')

@section('title', 'Restaurant Menu Maker - Azlaan Tools')
@section('meta_description', 'Design a printable restaurant or dhaba menu card with your dishes and prices. Free online menu maker.')

@section('content')
<style>
@media print {
    body * { visibility: hidden; }
    #menuPrintArea, #menuPrintArea * { visibility: visible; }
    #menuPrintArea { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Restaurant Menu Maker</h1>
            <p class="lead text-muted">Make a menu card for your hotel or dhaba — beautiful design with your dishes and rates, ready to print.</p>

            <div class="card shadow-sm mb-4 no-print-zone">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="restName" class="form-label fw-semibold">Restaurant / dhaba name</label>
                            <input type="text" class="form-control" id="restName" placeholder="e.g. Lahore Food Point">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="restTag" class="form-label fw-semibold">Tagline (optional)</label>
                            <input type="text" class="form-control" id="restTag" placeholder="e.g. Desi food, home-style taste">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="themeColor" class="form-label fw-semibold">Theme color</label>
                            <select class="form-select" id="themeColor">
                                <option value="primary">Blue</option>
                                <option value="success">Green</option>
                                <option value="danger">Maroon</option>
                                <option value="warning">Golden</option>
                                <option value="dark">Black</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="restPhone" class="form-label fw-semibold">Phone (optional)</label>
                            <input type="text" class="form-control" id="restPhone" placeholder="e.g. 0300-1234567">
                        </div>
                    </div>

                    <h5 class="mt-2">Add a dish</h5>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" id="dishName" placeholder="Dish name, e.g. Chicken Karahi">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="dishPrice" placeholder="Price, e.g. 850">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="dishCat">
                                <option>Desi</option>
                                <option>Fast Food</option>
                                <option>BBQ</option>
                                <option>Drinks</option>
                                <option>Dessert</option>
                                <option>Breakfast</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary w-100 mt-2" id="addBtn">Add Dish</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div class="mt-3">
                        <h6>Added dishes</h6>
                        <ul class="list-group" id="dishList">
                            <li class="list-group-item text-muted" id="emptyDish">No dishes added yet.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Menu preview</h5>
                    <div id="menuPrintArea" class="border rounded p-4 bg-white">
                        <div id="menuCard"></div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary flex-fill" id="refreshBtn">Update Preview</button>
                        <button type="button" class="btn btn-success flex-fill" id="printBtn">Print / Make PDF</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the restaurant name, tagline and theme color.</li>
                <li>Enter the dish name, price and category, then press "Add Dish".</li>
                <li>Use "Update Preview" to see the menu, then "Print / Make PDF" to print or save as PDF.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var dishes = [];
    var errorBox = document.getElementById('errorBox');
    var dishList = document.getElementById('dishList');
    var emptyDish = document.getElementById('emptyDish');
    var menuCard = document.getElementById('menuCard');

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

    function renderList() {
        dishList.innerHTML = '';
        if (dishes.length === 0) {
            dishList.appendChild(emptyDish);
            return;
        }
        dishes.forEach(function (d, i) {
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            var span = document.createElement('span');
            span.textContent = d.name + ' — Rs. ' + d.price + ' (' + d.cat + ')';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Delete';
            btn.setAttribute('data-idx', String(i));
            btn.addEventListener('click', function () {
                dishes.splice(parseInt(btn.getAttribute('data-idx'), 10), 1);
                renderList();
                renderMenu();
            });
            li.appendChild(span);
            li.appendChild(btn);
            dishList.appendChild(li);
        });
    }

    function renderMenu() {
        var name = document.getElementById('restName').value.trim() || 'My Restaurant';
        var tag = document.getElementById('restTag').value.trim();
        var phone = document.getElementById('restPhone').value.trim();
        var theme = document.getElementById('themeColor').value;
        var html = '';
        html += '<div class="text-center border-bottom pb-3 mb-3">';
        html += '<h2 class="mb-1 text-' + esc(theme) + '">' + esc(name) + '</h2>';
        if (tag) { html += '<p class="text-muted mb-1">' + esc(tag) + '</p>'; }
        if (phone) { html += '<p class="mb-0 fw-semibold">' + esc(phone) + '</p>'; }
        html += '</div>';
        var cats = {};
        dishes.forEach(function (d) {
            if (!cats[d.cat]) { cats[d.cat] = []; }
            cats[d.cat].push(d);
        });
        var catNames = Object.keys(cats);
        if (catNames.length === 0) {
            html += '<p class="text-muted text-center">Add dishes above — your menu will appear here.</p>';
        } else {
            catNames.forEach(function (c) {
                html += '<h5 class="mt-3 text-' + esc(theme) + '">' + esc(c) + '</h5>';
                html += '<table class="table table-sm"><tbody>';
                cats[c].forEach(function (d) {
                    html += '<tr><td>' + esc(d.name) + '</td><td class="text-end fw-semibold" style="white-space:nowrap">Rs. ' + esc(d.price) + '</td></tr>';
                });
                html += '</tbody></table>';
            });
        }
        menuCard.innerHTML = html;
    }

    document.getElementById('addBtn').addEventListener('click', function () {
        hideError();
        var name = document.getElementById('dishName').value.trim();
        var price = document.getElementById('dishPrice').value.trim();
        var cat = document.getElementById('dishCat').value;
        if (!name) { showError('Please enter the dish name.'); return; }
        if (!price) { showError('Please enter the price.'); return; }
        dishes.push({ name: name, price: price, cat: cat });
        document.getElementById('dishName').value = '';
        document.getElementById('dishPrice').value = '';
        renderList();
        renderMenu();
    });

    document.getElementById('refreshBtn').addEventListener('click', function () {
        hideError();
        renderMenu();
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        hideError();
        if (dishes.length === 0) { showError('Please add at least one dish first.'); return; }
        renderMenu();
        window.print();
    });

    ['restName', 'restTag', 'restPhone'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', renderMenu);
    });
    document.getElementById('themeColor').addEventListener('change', renderMenu);

    renderList();
    renderMenu();
})();
</script>
@endsection
