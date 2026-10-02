@extends('layouts.app')

@section('title', 'Split by Shares Calculator - Azlaan Tools')
@section('meta_description', 'Split a bill by shares — room 2:1, food 3:2, with preset ratios and WhatsApp share.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Split by Shares Calculator</h1>
            <p class="lead text-muted">Split a bill by shares — whether it is a room 2:1 or food 3:2, each person pays by their shares. Start fast with preset ratios.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="totalAmount" class="form-label fw-semibold">Total amount (Rs)</label>
                            <input type="number" class="form-control" id="totalAmount" placeholder="e.g. 30000" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preset ratios</label>
                            <div class="d-flex flex-wrap gap-2" id="presetRow">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-shares="1,1">1 : 1</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-shares="2,1">2 : 1</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-shares="3,2">3 : 2</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-shares="1,1,1">1 : 1 : 1</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-shares="2,2,1">2 : 2 : 1</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-shares="3,2,1">3 : 2 : 1</button>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-2">People and their shares</h5>
                    <div id="peopleRows" class="mb-2"></div>
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addPersonBtn">+ Add Person</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="removePersonBtn">- Remove Last</button>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="calcBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4 d-none">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">Total shares</small>
                                    <div class="fw-bold" id="totalShares">0</div>
                                </div></div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">1 share =</small>
                                    <div class="fw-bold text-primary" id="perShare">Rs 0</div>
                                </div></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr><th>Person</th><th class="text-end">Shares</th><th class="text-end">Share (Rs)</th><th class="text-end">%</th></tr>
                                </thead>
                                <tbody id="resultTable"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-success w-100 mt-2" id="waBtn">Copy for WhatsApp</button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How does it work?</h2>
                    <p class="mb-0 text-muted small">The total amount is divided by total shares to get the "price of 1 share", then each person's share count is multiplied to find their amount. For example, Rs 30000 in a 2:1 ratio means the bigger share pays Rs 20000 and the smaller pays Rs 10000.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var totalAmount = document.getElementById('totalAmount');
    var presetRow = document.getElementById('presetRow');
    var peopleRows = document.getElementById('peopleRows');
    var addPersonBtn = document.getElementById('addPersonBtn');
    var removePersonBtn = document.getElementById('removePersonBtn');
    var calcBtn = document.getElementById('calcBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var totalShares = document.getElementById('totalShares');
    var perShare = document.getElementById('perShare');
    var resultTable = document.getElementById('resultTable');
    var waBtn = document.getElementById('waBtn');

    var lastSummary = '';
    var defaultNames = ['Ahmed', 'Ali', 'Sana', 'Bilal', 'Fatima', 'Usman', 'Ayesha', 'Hamza'];

    function fmt(n) {
        return 'Rs ' + (Math.round(n * 100) / 100).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function personCount() {
        return peopleRows.querySelectorAll('.person-row').length;
    }

    function addPerson(name, shares) {
        var idx = personCount();
        var row = document.createElement('div');
        row.className = 'row g-2 mb-2 person-row';
        row.innerHTML =
            '<div class="col-7"><input type="text" class="form-control p-name" placeholder="Name" value="' + esc(name || (defaultNames[idx % defaultNames.length])) + '"></div>' +
            '<div class="col-5"><input type="number" class="form-control p-share" placeholder="Shares" min="0" step="0.5" value="' + (shares !== undefined ? shares : 1) + '"></div>';
        peopleRows.appendChild(row);
    }

    function setPeople(sharesArr) {
        peopleRows.innerHTML = '';
        sharesArr.forEach(function (s, i) {
            addPerson(defaultNames[i % defaultNames.length], s);
        });
    }

    addPersonBtn.addEventListener('click', function () {
        if (personCount() >= 8) { showError('Maximum 8 people.'); return; }
        clearError();
        addPerson('', 1);
    });

    removePersonBtn.addEventListener('click', function () {
        var rows = peopleRows.querySelectorAll('.person-row');
        if (rows.length <= 2) { showError('At least 2 people are needed.'); return; }
        clearError();
        rows[rows.length - 1].remove();
    });

    presetRow.addEventListener('click', function (e) {
        var btn = e.target.closest('button[data-shares]');
        if (!btn) return;
        clearError();
        var arr = btn.getAttribute('data-shares').split(',').map(function (s) { return parseFloat(s); });
        setPeople(arr);
    });

    calcBtn.addEventListener('click', function () {
        clearError();
        var total = parseFloat(totalAmount.value);
        if (isNaN(total) || total <= 0) { showError('Please enter a correct total amount.'); return; }

        var entries = [];
        var sumShares = 0;
        var rows = peopleRows.querySelectorAll('.person-row');
        var ok = true;
        rows.forEach(function (row) {
            var name = row.querySelector('.p-name').value.trim();
            var sh = parseFloat(row.querySelector('.p-share').value);
            if (!name) { showError('Please write the name of every person.'); ok = false; return; }
            if (isNaN(sh) || sh <= 0) { showError('Shares for each person must be more than 0.'); ok = false; return; }
            entries.push({ name: name, shares: sh });
            sumShares += sh;
        });
        if (!ok) return;
        if (entries.length < 2) { showError('At least 2 people are needed.'); return; }

        var oneShare = total / sumShares;
        totalShares.textContent = (Math.round(sumShares * 100) / 100).toString();
        perShare.textContent = fmt(oneShare);

        resultTable.innerHTML = '';
        var lines = ['Bill Split by Shares (total ' + fmt(total) + '):'];
        entries.forEach(function (en) {
            var amt = Math.round(en.shares * oneShare * 100) / 100;
            var pct = Math.round(en.shares / sumShares * 1000) / 10;
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(en.name) + '</td>' +
                '<td class="text-end">' + en.shares + '</td>' +
                '<td class="text-end fw-bold text-primary">' + fmt(amt) + '</td>' +
                '<td class="text-end">' + pct + '%</td>';
            resultTable.appendChild(tr);
            lines.push(en.name + ' (' + en.shares + ' shares): ' + fmt(amt));
        });
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

    setPeople([1, 1]);
})();
</script>
@endsection
