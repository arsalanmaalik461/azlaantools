@extends('layouts.app')

@section('title', 'Umrah Packing Checklist - Azlaan Tools')
@section('meta_description', 'Complete Umrah packing checklist for men and women with documents, health and travel items, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Umrah Packing Checklist</h1>
            <p class="lead text-muted">Complete packing checklist for your Umrah trip — separate items for men and women, documents and health items. Tick items, see progress, and print the list too.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Packing progress</h5>
                        <strong id="pctLabel">0%</strong>
                    </div>
                    <div class="progress mb-3" style="height: 22px;">
                        <div class="progress-bar bg-success" id="progBar" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">0 / 0</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-secondary w-100" id="resetBtn">Reset All</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-primary w-100" id="printBtn">Print List</button>
                        </div>
                    </div>
                    <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>

                    <div id="checklistWrap"></div>

                    <div class="mt-4">
                        <label for="customInput" class="form-label fw-semibold">Add your own item</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="customInput" placeholder="example: power bank">
                            <button type="button" class="btn btn-primary" id="addBtn">Add</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Tick each item as you pack it — progress will update itself.</li>
                <li>Your list stays saved in the browser (even if you close the phone and come back).</li>
                <li>Use "Print List" to print the list.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var errorBox = document.getElementById('errorBox');
    var wrap = document.getElementById('checklistWrap');
    var progBar = document.getElementById('progBar');
    var pctLabel = document.getElementById('pctLabel');
    var LS_KEY = 'umrah_checklist_v1';

    var SECTIONS = [
        {
            title: 'For men',
            items: ['Ihram (2 sheets)', 'Belt to wear under ihram / money belt', 'Slippers (soft, unstitched)', 'Prayer cap', 'White kurta shalwar (2-3 suits)', 'Prayer beads', 'Small prayer mat', 'Nail cutter and scissors (for before ihram)']
        },
        {
            title: 'For women',
            items: ['Abaya (2-3, light colors)', 'Hijab / scarf (4-5)', 'Prayer dress (loose, full covering)', 'Slippers / soft shoes', 'Socks', 'Prayer beads', 'Small prayer mat', 'Safety pins and extra dupatta']
        },
        {
            title: 'Documents (most important)',
            items: ['Passport (more than 6 months validity)', 'Umrah visa / Nusuk confirmation', 'Flight tickets (print + mobile)', 'Hotel booking confirmation', 'CNIC copy', 'Passport size photos (4)', 'Vaccination certificate (if required)', 'Travel insurance documents']
        },
        {
            title: 'Health and medicines',
            items: ['Daily medicines (full quantity)', 'Panadol / Brufen', 'ORS packets', 'Bandage and Dettol', 'Vaseline / moisturizer', 'Sunscreen', 'Mask (for crowds)', 'Small first-aid pouch']
        },
        {
            title: 'Travel and general',
            items: ['Mobile charger + power bank', 'Saudi SIM / roaming plan', 'Small backpack (for Haram)', 'Water bottle', 'Dates / dry fruits (for travel)', 'Toiletries (unscented, for ihram)', 'Towel', 'Laundry bag and extra plastic bags', 'Empty bottle for Zamzam (allowed packing for return)']
        }
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        setTimeout(function () { errorBox.classList.add('d-none'); }, 3000);
    }

    function loadState() {
        try {
            var raw = localStorage.getItem(LS_KEY);
            return raw ? JSON.parse(raw) : {};
        } catch (e) { return {}; }
    }
    function saveState(state) {
        try { localStorage.setItem(LS_KEY, JSON.stringify(state)); } catch (e) {}
    }

    var state = loadState();
    var custom = state.__custom || [];

    function itemKey(si, ii) { return 's' + si + '_i' + ii; }

    function render() {
        wrap.innerHTML = '';
        var total = 0, done = 0;
        for (var si = 0; si < SECTIONS.length; si++) {
            var sec = SECTIONS[si];
            var h = document.createElement('h5');
            h.className = 'mt-4 mb-2';
            h.textContent = sec.title;
            wrap.appendChild(h);
            var ul = document.createElement('ul');
            ul.className = 'list-group';
            for (var ii = 0; ii < sec.items.length; ii++) {
                (function (s, i) {
                    var key = itemKey(s, i);
                    total++;
                    var checked = !!state[key];
                    if (checked) { done++; }
                    var li = document.createElement('li');
                    li.className = 'list-group-item';
                    var label = document.createElement('label');
                    label.className = 'form-check d-flex align-items-center gap-2 mb-0';
                    label.style.cursor = 'pointer';
                    var cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.className = 'form-check-input';
                    cb.checked = checked;
                    cb.addEventListener('change', function () {
                        state[key] = cb.checked;
                        saveState(state);
                        updateProgress();
                        span.style.textDecoration = cb.checked ? 'line-through' : 'none';
                        span.classList.toggle('text-muted', cb.checked);
                    });
                    var span = document.createElement('span');
                    span.textContent = SECTIONS[s].items[i];
                    if (checked) { span.style.textDecoration = 'line-through'; span.classList.add('text-muted'); }
                    label.appendChild(cb);
                    label.appendChild(span);
                    li.appendChild(label);
                    ul.appendChild(li);
                })(si, ii);
            }
            wrap.appendChild(ul);
        }
        // custom items
        if (custom.length) {
            var hc = document.createElement('h5');
            hc.className = 'mt-4 mb-2';
            hc.textContent = 'My own items';
            wrap.appendChild(hc);
            var ulc = document.createElement('ul');
            ulc.className = 'list-group';
            for (var ci = 0; ci < custom.length; ci++) {
                (function (idx) {
                    var key = 'custom_' + idx;
                    total++;
                    var checked = !!state[key];
                    if (checked) { done++; }
                    var li = document.createElement('li');
                    li.className = 'list-group-item d-flex align-items-center justify-content-between';
                    var label = document.createElement('label');
                    label.className = 'form-check d-flex align-items-center gap-2 mb-0';
                    label.style.cursor = 'pointer';
                    var cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.className = 'form-check-input';
                    cb.checked = checked;
                    var span = document.createElement('span');
                    span.textContent = custom[idx];
                    if (checked) { span.style.textDecoration = 'line-through'; span.classList.add('text-muted'); }
                    cb.addEventListener('change', function () {
                        state[key] = cb.checked;
                        saveState(state);
                        updateProgress();
                        span.style.textDecoration = cb.checked ? 'line-through' : 'none';
                        span.classList.toggle('text-muted', cb.checked);
                    });
                    label.appendChild(cb);
                    label.appendChild(span);
                    li.appendChild(label);
                    var del = document.createElement('button');
                    del.type = 'button';
                    del.className = 'btn btn-sm btn-outline-danger';
                    del.textContent = 'x';
                    del.addEventListener('click', function () {
                        custom.splice(idx, 1);
                        state.__custom = custom;
                        delete state[key];
                        saveState(state);
                        render();
                    });
                    li.appendChild(del);
                    ulc.appendChild(li);
                })(ci);
            }
            wrap.appendChild(ulc);
        }
        updateProgress(total, done);
    }

    function updateProgress(total, done) {
        if (typeof total === 'undefined') {
            var boxes = wrap.querySelectorAll('input[type="checkbox"]');
            total = boxes.length; done = 0;
            for (var i = 0; i < boxes.length; i++) { if (boxes[i].checked) { done++; } }
        }
        var pct = total ? Math.round(done / total * 100) : 0;
        progBar.style.width = pct + '%';
        progBar.setAttribute('aria-valuenow', pct);
        progBar.textContent = done + ' / ' + total;
        pctLabel.textContent = pct + '%';
    }

    document.getElementById('addBtn').addEventListener('click', function () {
        var inp = document.getElementById('customInput');
        var v = inp.value.trim();
        if (!v) { showError('Write the item name first.'); return; }
        if (v.length > 80) { showError('Keep the name under 80 characters.'); return; }
        custom.push(v);
        state.__custom = custom;
        saveState(state);
        inp.value = '';
        render();
    });

    document.getElementById('resetBtn').addEventListener('click', function () {
        state = { __custom: custom };
        saveState(state);
        render();
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        window.print();
    });

    render();
})();
</script>
@endsection
