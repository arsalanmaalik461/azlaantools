@extends('layouts.app')

@section('title', 'Restaurant Bill Splitter - Azlaan Tools')
@section('meta_description', 'Split a dinner or party bill item-wise — assign each dish to a person, tax and tip are shared in proportion.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Restaurant Bill Splitter</h1>
            <p class="lead text-muted">Assign each dish to a person — tax and tip are split automatically in proportion to each person's dishes. Keep shared dishes on "Shared".</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">People</h5>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="personName" placeholder="Enter a name">
                                <button type="button" class="btn btn-primary" id="addPersonBtn">Add</button>
                            </div>
                            <div id="personList" class="list-group mb-2"></div>
                            <p class="text-muted small mb-0">Add at least 2 people.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Dishes / Items</h5>
                            <div class="row g-2 mb-3">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="itemName" placeholder="Dish name (e.g. Chicken Karahi)">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="itemPrice" placeholder="Price Rs" min="0" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="itemOwner"></select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary w-100 mb-3" id="addItemBtn">Add Dish</button>
                            <div id="itemList" class="list-group mb-4"></div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label for="taxPct" class="form-label fw-semibold">Tax %</label>
                                    <input type="number" class="form-control" id="taxPct" placeholder="e.g. 16" min="0" max="100" step="0.1">
                                </div>
                                <div class="col-md-6">
                                    <label for="tipPct" class="form-label fw-semibold">Tip % (optional)</label>
                                    <input type="number" class="form-control" id="tipPct" placeholder="e.g. 5" min="0" max="100" step="0.1">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="calcBtn">Calculate</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                            <div id="results" class="mt-4 d-none">
                                <div class="row text-center g-2 mb-3">
                                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Dishes Total</small><div class="fw-bold" id="foodTotal">Rs 0</div></div></div></div>
                                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Tax</small><div class="fw-bold" id="taxTotal">Rs 0</div></div></div></div>
                                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Tip</small><div class="fw-bold" id="tipTotal">Rs 0</div></div></div></div>
                                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Grand Total</small><div class="fw-bold text-primary" id="grandTotal">Rs 0</div></div></div></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead class="table-light">
                                            <tr><th>Person</th><th class="text-end">Dishes</th><th class="text-end">Tax+Tip</th><th class="text-end">Total Share</th></tr>
                                        </thead>
                                        <tbody id="resultTable"></tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success w-100 mt-2" id="waBtn">Copy for WhatsApp</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How does it work?</h2>
                    <p class="mb-0 text-muted small">Shared dishes are split equally among everyone. For the rest, each person's own dishes are added up and tax and tip are applied in the same proportion — whoever ate more pays more.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var people = [];
    var items = [];
    var personSeq = 0;

    var personName = document.getElementById('personName');
    var addPersonBtn = document.getElementById('addPersonBtn');
    var personList = document.getElementById('personList');
    var itemName = document.getElementById('itemName');
    var itemPrice = document.getElementById('itemPrice');
    var itemOwner = document.getElementById('itemOwner');
    var addItemBtn = document.getElementById('addItemBtn');
    var itemList = document.getElementById('itemList');
    var taxPct = document.getElementById('taxPct');
    var tipPct = document.getElementById('tipPct');
    var calcBtn = document.getElementById('calcBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var foodTotal = document.getElementById('foodTotal');
    var taxTotal = document.getElementById('taxTotal');
    var tipTotal = document.getElementById('tipTotal');
    var grandTotal = document.getElementById('grandTotal');
    var resultTable = document.getElementById('resultTable');
    var waBtn = document.getElementById('waBtn');

    var lastSummary = '';

    function fmt(n) {
        return 'Rs ' + (Math.round(n * 100) / 100).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function refreshOwnerOptions() {
        itemOwner.innerHTML = '';
        var optShared = document.createElement('option');
        optShared.value = 'shared';
        optShared.textContent = 'Shared (split equally)';
        itemOwner.appendChild(optShared);
        people.forEach(function (p) {
            var o = document.createElement('option');
            o.value = p.id;
            o.textContent = p.name;
            itemOwner.appendChild(o);
        });
    }

    function renderPeople() {
        personList.innerHTML = '';
        people.forEach(function (p) {
            var div = document.createElement('div');
            div.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
            var span = document.createElement('span');
            span.textContent = p.name;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Remove';
            btn.addEventListener('click', function () {
                people = people.filter(function (x) { return x.id !== p.id; });
                items.forEach(function (it) { if (it.ownerId === p.id) it.ownerId = 'shared'; });
                renderPeople();
                refreshOwnerOptions();
                renderItems();
            });
            div.appendChild(span);
            div.appendChild(btn);
            personList.appendChild(div);
        });
    }

    function renderItems() {
        itemList.innerHTML = '';
        if (items.length === 0) {
            var empty = document.createElement('div');
            empty.className = 'list-group-item text-muted';
            empty.textContent = 'No dishes added yet.';
            itemList.appendChild(empty);
            return;
        }
        items.forEach(function (it, idx) {
            var ownerLabel = 'Shared';
            if (it.ownerId !== 'shared') {
                var owner = people.find(function (p) { return p.id === it.ownerId; });
                if (owner) ownerLabel = owner.name;
            }
            var div = document.createElement('div');
            div.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
            var span = document.createElement('span');
            span.innerHTML = '<strong>' + esc(it.name) + '</strong> — ' + fmt(it.price) + ' <span class="badge bg-secondary">' + esc(ownerLabel) + '</span>';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Remove';
            btn.addEventListener('click', function () {
                items.splice(idx, 1);
                renderItems();
            });
            div.appendChild(span);
            div.appendChild(btn);
            itemList.appendChild(div);
        });
    }

    addPersonBtn.addEventListener('click', function () {
        var name = personName.value.trim();
        if (!name) { showError('Please enter the name first.'); return; }
        clearError();
        personSeq += 1;
        people.push({ id: 'p' + personSeq, name: name });
        personName.value = '';
        personName.focus();
        renderPeople();
        refreshOwnerOptions();
    });

    personName.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addPersonBtn.click(); }
    });

    addItemBtn.addEventListener('click', function () {
        var name = itemName.value.trim();
        var price = parseFloat(itemPrice.value);
        if (!name) { showError('Please enter the dish name.'); return; }
        if (isNaN(price) || price <= 0) { showError('Please enter the correct dish price.'); return; }
        clearError();
        items.push({ name: name, price: price, ownerId: itemOwner.value });
        itemName.value = '';
        itemPrice.value = '';
        itemName.focus();
        renderItems();
    });

    calcBtn.addEventListener('click', function () {
        clearError();
        if (people.length < 2) { showError('Please add at least 2 people.'); return; }
        if (items.length === 0) { showError('Please add at least 1 dish.'); return; }

        var tax = parseFloat(taxPct.value) || 0;
        var tip = parseFloat(tipPct.value) || 0;
        if (tax < 0 || tax > 100 || tip < 0 || tip > 100) { showError('Enter tax/tip between 0 and 100 %.'); return; }

        var n = people.length;
        var sub = {};
        people.forEach(function (p) { sub[p.id] = 0; });

        var food = 0;
        items.forEach(function (it) {
            food += it.price;
            if (it.ownerId === 'shared') {
                var share = it.price / n;
                people.forEach(function (p) { sub[p.id] += share; });
            } else if (sub.hasOwnProperty(it.ownerId)) {
                sub[it.ownerId] += it.price;
            } else {
                var share2 = it.price / n;
                people.forEach(function (p) { sub[p.id] += share2; });
            }
        });

        var taxAmt = food * tax / 100;
        var tipAmt = food * tip / 100;
        var extra = taxAmt + tipAmt;

        var rows = people.map(function (p) {
            var s = sub[p.id];
            var extraShare = food > 0 ? extra * (s / food) : 0;
            var total = Math.round((s + extraShare) * 100) / 100;
            return { name: p.name, sub: Math.round(s * 100) / 100, extra: Math.round(extraShare * 100) / 100, total: total };
        });

        var grand = Math.round((food + extra) * 100) / 100;

        foodTotal.textContent = fmt(food);
        taxTotal.textContent = fmt(taxAmt);
        tipTotal.textContent = fmt(tipAmt);
        grandTotal.textContent = fmt(grand);

        resultTable.innerHTML = '';
        rows.forEach(function (r) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(r.name) + '</td>' +
                '<td class="text-end">' + fmt(r.sub) + '</td>' +
                '<td class="text-end">' + fmt(r.extra) + '</td>' +
                '<td class="text-end fw-bold text-primary">' + fmt(r.total) + '</td>';
            resultTable.appendChild(tr);
        });

        var lines = ['Restaurant Bill Split:'];
        rows.forEach(function (r) { lines.push(r.name + ': ' + fmt(r.total)); });
        lines.push('Grand total: ' + fmt(grand) + ' (tax ' + tax + '%, tip ' + tip + '%)');
        lines.push('- Azlaan Tools');
        lastSummary = lines.join('\n');

        results.classList.remove('d-none');
    });

    waBtn.addEventListener('click', function () {
        if (!lastSummary) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastSummary).then(function () {
                waBtn.textContent = 'Copied! Paste it in WhatsApp';
                setTimeout(function () { waBtn.textContent = 'Copy for WhatsApp'; }, 2500);
            }, function () {
                window.open('https://wa.me/?text=' + encodeURIComponent(lastSummary), '_blank');
            });
        } else {
            window.open('https://wa.me/?text=' + encodeURIComponent(lastSummary), '_blank');
        }
    });

    refreshOwnerOptions();
    renderItems();
})();
</script>
@endsection
