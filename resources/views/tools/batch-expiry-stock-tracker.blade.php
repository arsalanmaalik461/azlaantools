@extends('layouts.app')

@section('title', 'Batch & Expiry Stock Tracker - Azlaan Tools')
@section('meta_description', 'Track batch/lot stock with expiry dates — for medicines, milk, cosmetics. With FIFO hints.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Batch &amp; Expiry Stock Tracker</h1>
            <p class="lead text-muted">Stock with batch/lot and expiry date — for medicines, milk, cosmetics. Your data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="urgentBox" role="alert"></div>

                    <div class="row text-center g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Total Batches</small>
                                <div class="fw-bold" id="statBatches">0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Expiring in 30 days</small>
                                <div class="fw-bold text-danger" id="stat30">0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Expiring in 90 days</small>
                                <div class="fw-bold text-warning" id="stat90">0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <small class="text-muted">Already expired</small>
                                <div class="fw-bold" style="color:#6a1b9a" id="statExp">0</div>
                            </div></div>
                        </div>
                    </div>

                    <h2 class="h5 mb-3">Add a batch</h2>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="beName" class="form-label fw-semibold">Item name</label>
                            <input type="text" class="form-control" id="beName" placeholder="e.g. Disprin">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="beBatch" class="form-label fw-semibold">Batch / Lot no.</label>
                            <input type="text" class="form-control" id="beBatch" placeholder="e.g. B-1024">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="beQty" class="form-label fw-semibold">Qty</label>
                            <input type="number" class="form-control" id="beQty" placeholder="0" min="0" step="1">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="beExpiry" class="form-label fw-semibold">Expiry date</label>
                            <input type="date" class="form-control" id="beExpiry">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="beAddBtn">Add Batch</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <hr class="my-4">
                    <h2 class="h5 mb-3">Batches (FIFO order — the batch that expires first is on top)</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Item</th><th>Batch</th><th class="text-end">Qty</th><th>Expiry</th><th class="text-end">Days Left</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody id="rows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="emptyMsg">No batches yet. Add one from above.</p>
                    <div class="alert alert-info small mb-0">
                        <strong>FIFO hint:</strong> Always sell from the top batch (the one that expires first) so you do not lose stock to expiry.
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add the <strong>batch number</strong>, <strong>qty</strong> and <strong>expiry date</strong> with every new stock delivery.</li>
                <li>The list stays sorted in <strong>FIFO order</strong> — the batch that expires first is on top.</li>
                <li>You get <strong>alerts</strong> 30/60/90 days in advance — plan clearance or returns for batches that are close to expiry.</li>
                <li>When a batch is sold out, remove it with <strong>×</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_batches';
    var beName = document.getElementById('beName');
    var beBatch = document.getElementById('beBatch');
    var beQty = document.getElementById('beQty');
    var beExpiry = document.getElementById('beExpiry');
    var beAddBtn = document.getElementById('beAddBtn');
    var errorBox = document.getElementById('errorBox');
    var rows = document.getElementById('rows');
    var emptyMsg = document.getElementById('emptyMsg');
    var urgentBox = document.getElementById('urgentBox');
    var statBatches = document.getElementById('statBatches');
    var stat30 = document.getElementById('stat30');
    var stat90 = document.getElementById('stat90');
    var statExp = document.getElementById('statExp');

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) return p;
            }
        } catch (e) {}
        return [];
    }
    function save(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function uid() {
        return 'b' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function daysLeft(expiry) {
        if (!expiry) return null;
        var d = new Date(expiry + 'T00:00:00');
        if (isNaN(d.getTime())) return null;
        var now = new Date();
        now.setHours(0, 0, 0, 0);
        return Math.floor((d - now) / 86400000);
    }
    function statusOf(days) {
        if (days === null) return { label: 'No date', cls: 'bg-secondary' };
        if (days < 0) return { label: 'EXPIRED', cls: 'bg-dark' };
        if (days <= 30) return { label: 'Near (≤30)', cls: 'bg-danger' };
        if (days <= 60) return { label: 'Soon (≤60)', cls: 'bg-warning text-dark' };
        if (days <= 90) return { label: 'Keep an eye (≤90)', cls: 'bg-info text-dark' };
        return { label: 'Safe', cls: 'bg-success' };
    }

    function render() {
        hideError();
        var data = load();
        // FIFO sort: earliest expiry first; items without expiry go last
        data.sort(function (a, b) {
            if (!a.expiry && !b.expiry) return 0;
            if (!a.expiry) return 1;
            if (!b.expiry) return -1;
            return a.expiry < b.expiry ? -1 : 1;
        });
        var c30 = 0, c90 = 0, cExp = 0;
        var urgent = [];
        data.forEach(function (b) {
            var days = daysLeft(b.expiry);
            if (days === null) return;
            if (days < 0) cExp++;
            else if (days <= 30) { c30++; urgent.push(b); }
            else if (days <= 90) c90++;
        });
        statBatches.textContent = data.length;
        stat30.textContent = c30;
        stat90.textContent = c90;
        statExp.textContent = cExp;
        emptyMsg.style.display = data.length ? 'none' : '';

        if (urgent.length) {
            urgentBox.classList.remove('d-none');
            urgentBox.innerHTML = '<strong>Alert!</strong> ' + urgent.length + ' batches expire within 30 days: ' +
                esc(urgent.slice(0, 5).map(function (b) { return b.name + ' (' + b.batch + ')'; }).join(', ')) +
                (urgent.length > 5 ? ' ...and ' + (urgent.length - 5) + ' more' : '');
        } else {
            urgentBox.classList.add('d-none');
        }

        rows.innerHTML = '';
        data.forEach(function (b) {
            var days = daysLeft(b.expiry);
            var st = statusOf(days);
            var tr = document.createElement('tr');
            if (days !== null && days <= 30) tr.className = 'table-danger';
            var tdN = document.createElement('td');
            tdN.innerHTML = '<span class="fw-semibold">' + esc(b.name) + '</span>';
            var tdB = document.createElement('td');
            tdB.textContent = b.batch || '—';
            var tdQ = document.createElement('td');
            tdQ.className = 'text-end';
            tdQ.textContent = b.qty;
            var tdE = document.createElement('td');
            tdE.textContent = b.expiry || '—';
            var tdD = document.createElement('td');
            tdD.className = 'text-end fw-semibold';
            tdD.textContent = days === null ? '—' : (days < 0 ? Math.abs(days) + ' days ago' : days + ' days');
            var tdS = document.createElement('td');
            tdS.innerHTML = '<span class="badge ' + st.cls + '">' + st.label + '</span>';
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete batch');
            del.addEventListener('click', function () {
                if (!confirm('Delete ' + b.name + ' (' + b.batch + ')?')) return;
                save(load().filter(function (x) { return x.id !== b.id; }));
                render();
            });
            tdX.appendChild(del);
            tr.appendChild(tdN); tr.appendChild(tdB); tr.appendChild(tdQ);
            tr.appendChild(tdE); tr.appendChild(tdD); tr.appendChild(tdS); tr.appendChild(tdX);
            rows.appendChild(tr);
        });
    }

    beAddBtn.addEventListener('click', function () {
        hideError();
        var name = beName.value.trim();
        if (!name) { showError('Enter the item name.'); return; }
        if (!beBatch.value.trim()) { showError('Enter the batch / lot number.'); return; }
        if (!beExpiry.value) { showError('Select the expiry date.'); return; }
        var data = load();
        data.push({
            id: uid(),
            name: name,
            batch: beBatch.value.trim(),
            qty: Number(beQty.value) || 0,
            expiry: beExpiry.value
        });
        save(data);
        beName.value = '';
        beBatch.value = '';
        beQty.value = '';
        beExpiry.value = '';
        render();
    });

    render();
})();
</script>
@endsection
