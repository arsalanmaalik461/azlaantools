@extends('layouts.app')

@section('title', 'Solar Inverter Size Calculator - Azlaan Tools')
@section('meta_description', 'Calculate the right solar inverter size for your home or shop load. Free inverter size guide for Pakistan.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Solar Inverter Size Calculator</h1>
            <p class="lead text-muted">Type your home or shop load and find the right inverter size (kW) — not too small, not too big.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Step 1: Add appliances</h5>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="appSel" class="form-label fw-semibold">Appliance</label>
                            <select class="form-select" id="appSel"></select>
                        </div>
                        <div class="col-md-3">
                            <label for="appQty" class="form-label fw-semibold">Quantity</label>
                            <input type="number" class="form-control" id="appQty" value="1" min="1" max="50">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary w-100" id="addBtn">Add</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="customW" class="form-label fw-semibold">Or add a custom load (watts)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="customW" placeholder="e.g. 150">
                            <button type="button" class="btn btn-outline-secondary" id="customBtn">Add Watts</button>
                        </div>
                    </div>
                    <ul class="list-group mb-3" id="loadList"></ul>
                    <p class="fw-semibold">Total load: <span id="totalW">0</span> watts</p>

                    <h5 class="card-title mt-4">Step 2: Options</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="futureSel" class="form-label fw-semibold">Future expansion</label>
                            <select class="form-select" id="futureSel">
                                <option value="1.25">25% extra capacity (recommended)</option>
                                <option value="1.0">No extra</option>
                                <option value="1.5">50% extra capacity</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="phaseSel" class="form-label fw-semibold">Connection type</label>
                            <select class="form-select" id="phaseSel">
                                <option value="single">Single phase</option>
                                <option value="three">Three phase</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Inverter Size</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div id="invOut"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add all your appliances from the list (fan, fridge, AC etc.) — add anything not in the list as custom watts.</li>
                <li>Select future expansion (25% is recommended so you have room to add load later).</li>
                <li>Press "Calculate Inverter Size" — see the recommended inverter kW and category.</li>
            </ol>
            <p class="text-muted small">This is an estimate. Talk to your installer before the final selection — motor loads (pump, fridge compressor) have a higher starting surge.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var appSel = document.getElementById('appSel');
    var appQty = document.getElementById('appQty');
    var addBtn = document.getElementById('addBtn');
    var customW = document.getElementById('customW');
    var customBtn = document.getElementById('customBtn');
    var loadList = document.getElementById('loadList');
    var totalW = document.getElementById('totalW');
    var futureSel = document.getElementById('futureSel');
    var phaseSel = document.getElementById('phaseSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var invOut = document.getElementById('invOut');

    var APPLIANCES = [
        ['Ceiling fan', 75], ['Energy saver / LED bulb', 12], ['LED tube light', 20],
        ['Fridge (normal)', 200], ['Deep freezer', 250], ['1 ton inverter AC', 1200],
        ['1.5 ton inverter AC', 1800], ['1 ton non-inverter AC', 1500], ['1.5 ton non-inverter AC', 2200],
        ['Water pump (1 HP)', 750], ['Washing machine', 500], ['Iron', 1000],
        ['LED TV (32 inch)', 60], ['LED TV (55 inch)', 150], ['Laptop', 65],
        ['Desktop computer', 300], ['UPS charger load', 400], ['Microwave oven', 1200],
        ['Water dispenser (hot+cold)', 500], ['CCTV DVR + cameras', 80]
    ];
    var loads = [];

    APPLIANCES.forEach(function (a) {
        var o = document.createElement('option');
        o.value = a[1];
        o.textContent = a[0] + ' — ' + a[1] + 'W';
        appSel.appendChild(o);
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

    function renderList() {
        loadList.innerHTML = '';
        var total = 0;
        loads.forEach(function (l, i) {
            total += l.watts;
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = '<span>' + esc(l.name) + ' <span class="text-muted">x' + l.qty + ' = ' + l.watts + 'W</span></span>';
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'btn btn-sm btn-outline-danger';
            b.textContent = 'Remove';
            b.setAttribute('data-i', i);
            b.addEventListener('click', function () {
                loads.splice(parseInt(this.getAttribute('data-i'), 10), 1);
                renderList();
            });
            li.appendChild(b);
            loadList.appendChild(li);
        });
        totalW.textContent = total;
        return total;
    }

    addBtn.addEventListener('click', function () {
        hideError();
        var q = parseInt(appQty.value, 10);
        if (isNaN(q) || q < 1 || q > 50) { showError('Keep the quantity between 1 and 50.'); return; }
        var watts = parseInt(appSel.value, 10);
        var name = appSel.options[appSel.selectedIndex].text.split(' — ')[0];
        loads.push({ name: name, qty: q, watts: watts * q });
        renderList();
    });

    customBtn.addEventListener('click', function () {
        hideError();
        var w = parseInt(customW.value, 10);
        if (isNaN(w) || w < 1 || w > 20000) { showError('Enter watts between 1 and 20000.'); return; }
        loads.push({ name: 'Custom load', qty: 1, watts: w });
        customW.value = '';
        renderList();
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var total = loads.reduce(function (s, l) { return s + l.watts; }, 0);
        if (total <= 0) { showError('Please add at least one appliance or load first.'); return; }
        var factor = parseFloat(futureSel.value);
        var needed = total * factor;
        var kw = needed / 1000;

        var sizes = [1.2, 1.6, 2.2, 3, 3.2, 3.5, 5, 6, 8, 10, 12, 15, 20];
        var pick = sizes[sizes.length - 1];
        for (var i = 0; i < sizes.length; i++) {
            if (sizes[i] >= kw) { pick = sizes[i]; break; }
        }
        var phase = phaseSel.value === 'three' ? 'Three phase' : 'Single phase';
        var category = pick <= 3.5 ? 'Small home system' : (pick <= 6 ? 'Medium home system' : 'Large home / commercial system');

        var html = '<div class="alert alert-success">';
        html += '<h5 class="alert-heading">Recommended inverter: ' + pick + ' kW</h5>';
        html += '<p class="mb-1">Your total load: <strong>' + total + 'W</strong> | With headroom: <strong>' + Math.round(needed) + 'W (' + kw.toFixed(2) + ' kW)</strong></p>';
        html += '<p class="mb-1">System category: <strong>' + category + '</strong></p>';
        html += '<p class="mb-0">Connection: choose a <strong>' + phase + '</strong> inverter.</p>';
        html += '</div>';
        html += '<div class="alert alert-warning mb-0">Tip: in the market it is better to buy an inverter a little bigger than ' + pick + ' kW (the next standard size) so performance stays stable at peak load and in heat.</div>';
        invOut.innerHTML = html;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
@endsection
