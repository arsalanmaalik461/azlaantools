@extends('layouts.app')

@section('title', 'Rent & Utility Bill Splitter - Azlaan Tools')
@section('meta_description', 'Split expenses with roommates — rent by room share, electricity/gas split equally or custom, with saved monthly records.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Rent &amp; Utility Bill Splitter</h1>
            <p class="lead text-muted">Rent by room share, electricity/gas split equally — split monthly expenses with your roommates in seconds and keep saved records.</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Roommates</h5>
                            <p class="text-muted small">Room share: big room = 2, small room = 1, or whatever split you agree on.</p>
                            <div class="row g-2 mb-2">
                                <div class="col-7">
                                    <input type="text" class="form-control" id="rmName" placeholder="Name">
                                </div>
                                <div class="col-5">
                                    <input type="number" class="form-control" id="rmShare" placeholder="Room share" min="0" step="0.5" value="1">
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary w-100 mb-3" id="addRmBtn">Add Roommate</button>
                            <div id="rmList" class="list-group"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="rentTotal" class="form-label fw-semibold">Monthly rent (Rs)</label>
                                    <input type="number" class="form-control" id="rentTotal" placeholder="e.g. 25000" min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label for="monthLabel" class="form-label fw-semibold">Month (for records)</label>
                                    <input type="month" class="form-control" id="monthLabel">
                                </div>
                            </div>

                            <h5 class="mb-2">Utility bills</h5>
                            <div id="utilRows" class="mb-2"></div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-util="Electricity">+ Electricity</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-util="Gas">+ Gas</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-util="Water">+ Water</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-util="Internet">+ Internet</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-util="other">+ Other</button>
                            </div>

                            <button type="button" class="btn btn-primary w-100" id="calcBtn">Calculate</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                            <div id="results" class="mt-4 d-none">
                                <div class="row text-center g-2 mb-3">
                                    <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Rent</small><div class="fw-bold" id="rentOut">Rs 0</div></div></div></div>
                                    <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Utilities</small><div class="fw-bold" id="utilOut">Rs 0</div></div></div></div>
                                    <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total Monthly</small><div class="fw-bold text-primary" id="grandOut">Rs 0</div></div></div></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead class="table-light">
                                            <tr><th>Roommate</th><th class="text-end">Rent Share</th><th class="text-end">Utility Share</th><th class="text-end">Total (Rs)</th></tr>
                                        </thead>
                                        <tbody id="resultTable"></tbody>
                                    </table>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-success flex-fill" id="waBtn">Copy for WhatsApp</button>
                                    <button type="button" class="btn btn-outline-primary flex-fill" id="saveBtn">Save Month</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Saved monthly records</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Month</th><th class="text-end">Total (Rs)</th><th>Details</th><th></th></tr>
                            </thead>
                            <tbody id="histTable"></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0" id="histEmpty">No saved records yet — this data stays in your browser only.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How does it work?</h2>
                    <p class="mb-0 text-muted small">Rent is split in proportion to room shares (big room = bigger share). Utility bills are split equally by default; choose "custom" to enter a different amount for each person. "Save Month" stores the record in your browser.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var STORE_KEY = 'azlaan7_rent_splits';

    var roommates = []; // {id, name, share}
    var utils = [];     // {id, name, amount, mode: 'equal'|'custom', custom: {rmId: amt}}
    var rmSeq = 0, utilSeq = 0;
    var lastResult = null;

    var rmName = document.getElementById('rmName');
    var rmShare = document.getElementById('rmShare');
    var addRmBtn = document.getElementById('addRmBtn');
    var rmList = document.getElementById('rmList');
    var rentTotal = document.getElementById('rentTotal');
    var monthLabel = document.getElementById('monthLabel');
    var utilRows = document.getElementById('utilRows');
    var calcBtn = document.getElementById('calcBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var rentOut = document.getElementById('rentOut');
    var utilOut = document.getElementById('utilOut');
    var grandOut = document.getElementById('grandOut');
    var resultTable = document.getElementById('resultTable');
    var waBtn = document.getElementById('waBtn');
    var saveBtn = document.getElementById('saveBtn');
    var histTable = document.getElementById('histTable');
    var histEmpty = document.getElementById('histEmpty');

    function fmt(n) {
        return 'Rs ' + (Math.round(n * 100) / 100).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function loadHist() {
        try {
            var raw = localStorage.getItem(STORE_KEY);
            var arr = raw ? JSON.parse(raw) : [];
            return Array.isArray(arr) ? arr : [];
        } catch (e) { return []; }
    }

    function saveHist(arr) {
        try { localStorage.setItem(STORE_KEY, JSON.stringify(arr)); } catch (e) {}
    }

    function renderRoommates() {
        rmList.innerHTML = '';
        if (roommates.length === 0) {
            var d = document.createElement('div');
            d.className = 'list-group-item text-muted';
            d.textContent = 'No roommates yet.';
            rmList.appendChild(d);
            return;
        }
        roommates.forEach(function (r) {
            var div = document.createElement('div');
            div.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
            var span = document.createElement('span');
            span.innerHTML = '<strong>' + esc(r.name) + '</strong> <span class="badge bg-secondary">share ' + r.share + '</span>';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Remove';
            btn.addEventListener('click', function () {
                roommates = roommates.filter(function (x) { return x.id !== r.id; });
                renderRoommates();
                renderUtils();
            });
            div.appendChild(span);
            div.appendChild(btn);
            rmList.appendChild(div);
        });
    }

    function renderUtils() {
        utilRows.innerHTML = '';
        if (utils.length === 0) {
            var d = document.createElement('div');
            d.className = 'text-muted small mb-2';
            d.textContent = 'No utility bills yet — add one with the buttons above (or split just the rent).';
            utilRows.appendChild(d);
            return;
        }
        utils.forEach(function (u, idx) {
            var card = document.createElement('div');
            card.className = 'card mb-2';
            var body = document.createElement('div');
            body.className = 'card-body py-2';

            var head = document.createElement('div');
            head.className = 'row g-2 align-items-center';
            head.innerHTML =
                '<div class="col-4"><input type="text" class="form-control form-control-sm u-name" value="' + esc(u.name) + '"></div>' +
                '<div class="col-3"><input type="number" class="form-control form-control-sm u-amt" placeholder="Rs" min="0" step="0.01" value="' + (u.amount || '') + '"></div>' +
                '<div class="col-3"><select class="form-select form-select-sm u-mode">' +
                '<option value="equal"' + (u.mode === 'equal' ? ' selected' : '') + '>Equal</option>' +
                '<option value="custom"' + (u.mode === 'custom' ? ' selected' : '') + '>Custom</option></select></div>' +
                '<div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 u-del">X</button></div>';
            body.appendChild(head);

            var customBox = document.createElement('div');
            customBox.className = 'row g-2 mt-1 u-custom' + (u.mode === 'custom' ? '' : ' d-none');
            roommates.forEach(function (r) {
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4';
                var prev = (u.custom && u.custom[r.id]) ? u.custom[r.id] : '';
                col.innerHTML = '<label class="form-label small mb-0">' + esc(r.name) + '</label>' +
                    '<input type="number" class="form-control form-control-sm u-camt" data-rm="' + r.id + '" min="0" step="0.01" value="' + prev + '">';
                customBox.appendChild(col);
            });
            body.appendChild(customBox);

            head.querySelector('.u-name').addEventListener('input', function (e) { u.name = e.target.value; });
            head.querySelector('.u-amt').addEventListener('input', function (e) { u.amount = parseFloat(e.target.value) || 0; });
            head.querySelector('.u-mode').addEventListener('change', function (e) {
                u.mode = e.target.value;
                customBox.classList.toggle('d-none', u.mode !== 'custom');
            });
            head.querySelector('.u-del').addEventListener('click', function () {
                utils.splice(idx, 1);
                renderUtils();
            });
            customBox.addEventListener('input', function (e) {
                if (e.target.classList.contains('u-camt')) {
                    u.custom = u.custom || {};
                    u.custom[e.target.getAttribute('data-rm')] = parseFloat(e.target.value) || 0;
                }
            });

            card.appendChild(body);
            utilRows.appendChild(card);
        });
    }

    document.querySelectorAll('[data-util]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var name = btn.getAttribute('data-util');
            if (name === 'other') name = 'Utility ' + (utils.length + 1);
            utilSeq += 1;
            utils.push({ id: 'u' + utilSeq, name: name, amount: 0, mode: 'equal', custom: {} });
            renderUtils();
        });
    });

    addRmBtn.addEventListener('click', function () {
        var name = rmName.value.trim();
        var share = parseFloat(rmShare.value);
        if (!name) { showError('Please enter the roommate name.'); return; }
        if (isNaN(share) || share <= 0) { showError('Room share must be above zero.'); return; }
        clearError();
        rmSeq += 1;
        roommates.push({ id: 'r' + rmSeq, name: name, share: share });
        rmName.value = '';
        rmShare.value = '1';
        rmName.focus();
        renderRoommates();
        renderUtils();
    });

    calcBtn.addEventListener('click', function () {
        clearError();
        if (roommates.length < 2) { showError('Please add at least 2 roommates.'); return; }
        var rent = parseFloat(rentTotal.value) || 0;

        var badUtil = null;
        utils.forEach(function (u) {
            if (!u.name.trim()) { badUtil = 'Please enter a name for every utility bill.'; return; }
            if (!(u.amount > 0)) { badUtil = 'Every utility bill amount must be above zero.'; return; }
            if (u.mode === 'custom') {
                var csum = 0;
                roommates.forEach(function (r) { csum += (u.custom && u.custom[r.id]) || 0; });
                if (Math.abs(csum - u.amount) > 0.01) {
                    badUtil = 'The custom total for "' + u.name + '" (' + fmt(csum) + ') does not match the bill (' + fmt(u.amount) + ').';
                }
            }
        });
        if (badUtil) { showError(badUtil); return; }

        var sumShares = roommates.reduce(function (s, r) { return s + r.share; }, 0 );

        var rows = roommates.map(function (r) {
            var rentShare = sumShares > 0 ? rent * r.share / sumShares : 0;
            var utilShare = 0;
            utils.forEach(function (u) {
                if (u.mode === 'equal') utilShare += u.amount / roommates.length;
                else utilShare += (u.custom && u.custom[r.id]) || 0;
            });
            return {
                name: r.name,
                rent: Math.round(rentShare * 100) / 100,
                util: Math.round(utilShare * 100) / 100,
                total: Math.round((rentShare + utilShare) * 100) / 100
            };
        });

        var utilTotal = utils.reduce(function (s, u) { return s + u.amount; }, 0);
        var grand = Math.round((rent + utilTotal) * 100) / 100;

        rentOut.textContent = fmt(rent);
        utilOut.textContent = fmt(utilTotal);
        grandOut.textContent = fmt(grand);

        resultTable.innerHTML = '';
        rows.forEach(function (rw) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(rw.name) + '</td>' +
                '<td class="text-end">' + fmt(rw.rent) + '</td>' +
                '<td class="text-end">' + fmt(rw.util) + '</td>' +
                '<td class="text-end fw-bold text-primary">' + fmt(rw.total) + '</td>';
            resultTable.appendChild(tr);
        });

        var month = monthLabel.value || new Date().toISOString().slice(0, 7);
        lastResult = {
            month: month,
            rent: rent,
            utilTotal: Math.round(utilTotal * 100) / 100,
            grand: grand,
            rows: rows
        };

        results.classList.remove('d-none');
    });

    function shareText() {
        if (!lastResult) return '';
        var lines = ['Roommates Monthly Split (' + lastResult.month + '):'];
        lastResult.rows.forEach(function (rw) {
            lines.push(rw.name + ': Rent ' + fmt(rw.rent) + ' + Utility ' + fmt(rw.util) + ' = ' + fmt(rw.total));
        });
        lines.push('Total: ' + fmt(lastResult.grand));
        lines.push('- Azlaan Tools');
        return lines.join('\n');
    }

    waBtn.addEventListener('click', function () {
        var text = shareText();
        if (!text) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                waBtn.textContent = 'Copied! Paste it in WhatsApp';
                setTimeout(function () { waBtn.textContent = 'Copy for WhatsApp'; }, 2500);
            }, function () {
                window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
            });
        } else {
            window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
        }
    });

    saveBtn.addEventListener('click', function () {
        if (!lastResult) { showError('Please calculate first.'); return; }
        clearError();
        var hist = loadHist();
        hist = hist.filter(function (h) { return h.month !== lastResult.month; });
        hist.unshift(lastResult);
        saveHist(hist);
        renderHist();
        saveBtn.textContent = 'Saved!';
        setTimeout(function () { saveBtn.textContent = 'Save Month'; }, 2000);
    });

    function renderHist() {
        var hist = loadHist();
        histTable.innerHTML = '';
        histEmpty.classList.toggle('d-none', hist.length > 0);
        hist.forEach(function (h) {
            var tr = document.createElement('tr');
            var detail = h.rows.map(function (rw) { return rw.name + ': ' + fmt(rw.total); }).join(' | ');
            tr.innerHTML = '<td class="fw-semibold">' + esc(h.month) + '</td>' +
                '<td class="text-end">' + fmt(h.grand) + '</td>' +
                '<td class="small text-muted">' + esc(detail) + '</td>';
            var tdAct = document.createElement('td');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Delete';
            btn.addEventListener('click', function () {
                saveHist(loadHist().filter(function (x) { return x.month !== h.month; }));
                renderHist();
            });
            tdAct.appendChild(btn);
            tr.appendChild(tdAct);
            histTable.appendChild(tr);
        });
    }

    monthLabel.value = new Date().toISOString().slice(0, 7);
    renderRoommates();
    renderUtils();
    renderHist();
})();
</script>
@endsection
