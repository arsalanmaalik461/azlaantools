@extends('layouts.app')

@section('title', 'Discount Scheme Planner - Azlaan Tools')
@section('meta_description', 'Set a discount scheme on an item or category with a date range — active and expired status is automatic.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Discount Scheme Planner</h1>
            <p class="lead text-muted">Set a scheme/offer on an item or category with a date range. Active, upcoming and expired status is worked out automatically. Data is saved only in your browser, it is never uploaded anywhere.</p>

            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">New scheme</h5>
                            <div class="mb-3">
                                <label for="scName" class="form-label fw-semibold">Scheme name</label>
                                <input type="text" class="form-control" id="scName" placeholder="e.g. Eid Sale">
                            </div>
                            <div class="mb-3">
                                <label for="scType" class="form-label fw-semibold">Discount type</label>
                                <select class="form-select" id="scType">
                                    <option value="percent">% off (percent)</option>
                                    <option value="flat">Flat Rs off</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="scValue" class="form-label fw-semibold">Discount value</label>
                                <input type="number" class="form-control" id="scValue" placeholder="e.g. 10" min="0.01" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="scApplies" class="form-label fw-semibold">Applies to (item / category)</label>
                                <input type="text" class="form-control" id="scApplies" placeholder="e.g. All soaps / Surf Excel">
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="scStart" class="form-label fw-semibold">Start date</label>
                                    <input type="date" class="form-control" id="scStart">
                                </div>
                                <div class="col-6">
                                    <label for="scEnd" class="form-label fw-semibold">End date</label>
                                    <input type="date" class="form-control" id="scEnd">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="addSchemeBtn">Add Scheme</button>
                            <div class="alert alert-danger mt-3 d-none" id="scError" role="alert"></div>
                        </div>
                    </div>
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Effective price preview</h5>
                            <div class="mb-3">
                                <label for="pvPrice" class="form-label fw-semibold">Item actual price (Rs)</label>
                                <input type="number" class="form-control" id="pvPrice" placeholder="0" min="0" step="0.01">
                            </div>
                            <div id="pvResults" class="small text-muted">Enter a price — the final price with active schemes will appear here.</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Schemes (<span id="scCount">0</span>)</h5>
                            <div class="btn-group btn-group-sm mb-3" role="group" aria-label="Filter">
                                <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                                <button type="button" class="btn btn-outline-primary" data-filter="active">Active</button>
                                <button type="button" class="btn btn-outline-primary" data-filter="upcoming">Upcoming</button>
                                <button type="button" class="btn btn-outline-primary" data-filter="expired">Expired</button>
                            </div>
                            <div id="scList"></div>
                            <p class="small text-muted mb-0" id="scEmpty">No scheme yet. Add from the left side.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Write the scheme name, discount type, value and date range, then add it.</li>
                <li>The status (active/upcoming/expired) is set automatically from the date.</li>
                <li>In <strong>Effective price preview</strong> enter the actual price — you will see the final price and savings for every active scheme.</li>
            </ol>
            <p class="text-muted small">Note: your data stays in this browser. You can delete expired schemes to keep the list clean.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_schemes';
    var scName = document.getElementById('scName');
    var scType = document.getElementById('scType');
    var scValue = document.getElementById('scValue');
    var scApplies = document.getElementById('scApplies');
    var scStart = document.getElementById('scStart');
    var scEnd = document.getElementById('scEnd');
    var addSchemeBtn = document.getElementById('addSchemeBtn');
    var scError = document.getElementById('scError');
    var scList = document.getElementById('scList');
    var scEmpty = document.getElementById('scEmpty');
    var scCount = document.getElementById('scCount');
    var pvPrice = document.getElementById('pvPrice');
    var pvResults = document.getElementById('pvResults');
    var filterBtns = document.querySelectorAll('[data-filter]');

    var schemes = [];
    var filter = 'all';
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) { var p = JSON.parse(raw); if (Array.isArray(p)) schemes = p; }
    } catch (e) { schemes = []; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(schemes)); } catch (e) {}
    }
    function uid() {
        return 'd' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36);
    }
    function showError(msg) {
        scError.textContent = msg;
        scError.classList.remove('d-none');
    }
    function hideError() {
        scError.classList.add('d-none');
        scError.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }
    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function statusOf(s) {
        var t = todayStr();
        if (s.start > t) return 'upcoming';
        if (s.end < t) return 'expired';
        return 'active';
    }
    function statusBadge(st) {
        if (st === 'active') return '<span class="badge bg-success">Active</span>';
        if (st === 'upcoming') return '<span class="badge bg-info text-dark">Upcoming</span>';
        return '<span class="badge bg-secondary">Expired</span>';
    }
    function discountLabel(s) {
        return s.type === 'percent' ? s.value + '% off' : fmt(s.value) + ' off';
    }
    function finalPrice(price, s) {
        var p = Number(price);
        if (s.type === 'percent') {
            return Math.max(0, Math.round(p * (1 - s.value / 100) * 100) / 100);
        }
        return Math.max(0, Math.round((p - s.value) * 100) / 100);
    }

    function render() {
        hideError();
        scList.innerHTML = '';
        scCount.textContent = schemes.length;
        var shown = 0;
        var ordered = schemes.slice().sort(function (a, b) {
            var sa = statusOf(a), sb = statusOf(b);
            var rank = { active: 0, upcoming: 1, expired: 2 };
            if (rank[sa] !== rank[sb]) return rank[sa] - rank[sb];
            return a.start < b.start ? 1 : -1;
        });
        ordered.forEach(function (s) {
            var st = statusOf(s);
            if (filter !== 'all' && st !== filter) return;
            shown++;
            var card = document.createElement('div');
            card.className = 'border rounded p-3 mb-2 ' + (st === 'active' ? 'border-success' : '');
            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-start gap-2 mb-1';
            var title = document.createElement('div');
            title.innerHTML = '<strong>' + esc(s.name) + '</strong> ' + statusBadge(st) +
                '<br><small class="text-muted">' + esc(s.applies || 'All items') + ' • ' + esc(s.start) + ' to ' + esc(s.end) + '</small>';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = 'Delete';
            del.addEventListener('click', function () {
                if (!confirm('Delete this scheme?')) return;
                schemes = schemes.filter(function (x) { return x.id !== s.id; });
                save(); render(); renderPreview();
            });
            head.appendChild(title);
            head.appendChild(del);
            card.appendChild(head);
            var disc = document.createElement('div');
            disc.className = 'fs-5 fw-bold text-primary';
            disc.textContent = discountLabel(s);
            card.appendChild(disc);
            scList.appendChild(card);
        });
        scEmpty.style.display = shown ? 'none' : '';
        if (shown === 0 && schemes.length) scEmpty.textContent = 'No scheme in this filter.';
        else if (schemes.length === 0) scEmpty.textContent = 'No scheme yet. Add from the left side.';
    }

    function renderPreview() {
        var price = parseFloat(pvPrice.value);
        if (isNaN(price) || price <= 0) {
            pvResults.className = 'small text-muted';
            pvResults.textContent = 'Enter a price — the final price with active schemes will appear here.';
            return;
        }
        var active = schemes.filter(function (s) { return statusOf(s) === 'active'; });
        if (!active.length) {
            pvResults.className = 'small text-muted';
            pvResults.textContent = 'No active scheme — the price stays the same: ' + fmt(price);
            return;
        }
        pvResults.className = '';
        pvResults.innerHTML = '';
        active.forEach(function (s) {
            var fp = finalPrice(price, s);
            var row = document.createElement('div');
            row.className = 'border rounded p-2 mb-2 bg-light';
            row.innerHTML = '<strong>' + esc(s.name) + '</strong> <small class="text-muted">(' + esc(discountLabel(s)) + ')</small><br>' +
                'Final price: <span class="fw-bold text-success">' + fmt(fp) + '</span> &nbsp;•&nbsp; You save: <span class="text-primary">' + fmt(price - fp) + '</span>';
            pvResults.appendChild(row);
        });
    }

    addSchemeBtn.addEventListener('click', function () {
        hideError();
        var name = scName.value.trim();
        var type = scType.value;
        var value = parseFloat(scValue.value);
        var applies = scApplies.value.trim();
        var start = scStart.value;
        var end = scEnd.value;
        if (!name) { showError('Write the scheme name.'); return; }
        if (isNaN(value) || value <= 0) { showError('Enter a discount value above 0.'); return; }
        if (type === 'percent' && value > 100) { showError('% discount cannot be more than 100.'); return; }
        if (!start || !end) { showError('Select both the start and end date.'); return; }
        if (end < start) { showError('End date cannot be before the start date.'); return; }
        schemes.push({
            id: uid(),
            name: name,
            type: type,
            value: Math.round(value * 100) / 100,
            applies: applies,
            start: start,
            end: end
        });
        save();
        scName.value = ''; scValue.value = ''; scApplies.value = ''; scStart.value = ''; scEnd.value = '';
        render(); renderPreview();
    });

    filterBtns.forEach(function (b) {
        b.addEventListener('click', function () {
            filterBtns.forEach(function (x) { x.classList.remove('active'); });
            b.classList.add('active');
            filter = b.getAttribute('data-filter');
            render();
        });
    });

    pvPrice.addEventListener('input', renderPreview);

    render();
    renderPreview();
})();
</script>
@endsection
