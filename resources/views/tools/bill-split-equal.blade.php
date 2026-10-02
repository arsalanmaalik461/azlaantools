@extends('layouts.app')

@section('title', 'Equal Bill Split Calculator - Azlaan Tools')
@section('meta_description', 'Split any bill equally among friends in seconds — divide the total bill evenly, with service charge.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Equal Bill Split Calculator</h1>
            <p class="lead text-muted">Split the total bill equally among friends — see each person's share instantly. Add a service charge if there is one.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="totalBill" class="form-label fw-semibold">Total bill (Rs)</label>
                            <input type="number" class="form-control" id="totalBill" placeholder="e.g. 4500" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="numPeople" class="form-label fw-semibold">How many people?</label>
                            <input type="number" class="form-control" id="numPeople" placeholder="e.g. 5" min="1" step="1">
                        </div>
                        <div class="col-md-4">
                            <label for="serviceCharge" class="form-label fw-semibold">Service charge % (optional)</label>
                            <input type="number" class="form-control" id="serviceCharge" placeholder="0" min="0" max="100" step="0.5">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="roundMode" class="form-label fw-semibold">Rounding</label>
                            <select class="form-select" id="roundMode">
                                <option value="exact">Exact, down to paisa</option>
                                <option value="nearest10">Nearest Rs 10</option>
                                <option value="nearest100">Nearest Rs 100</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="includeMe">
                                <label class="form-check-label" for="includeMe">Include me in the split</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="calcBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4 d-none">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">Bill + Service Charge</small>
                                    <div class="fw-bold" id="grandTotal">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">Per Person</small>
                                    <div class="fw-bold text-primary" id="perPerson">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-2">
                                    <small class="text-muted">Leftover adjustment</small>
                                    <div class="fw-bold" id="adjustment">Rs 0</div>
                                </div></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr><th>Person</th><th class="text-end">Share (Rs)</th></tr>
                                </thead>
                                <tbody id="shareTable"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small" id="adjustNote"></p>
                        <button type="button" class="btn btn-success w-100 mt-2" id="waBtn">Copy for WhatsApp</button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How does it work?</h2>
                    <p class="mb-0 text-muted small">The service charge is added to the total bill and then divided by the number of people. If you choose rounding, each share is rounded to the nearest 10 or 100, and the leftover difference is shown in "adjustment" so the total always matches.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var totalBill = document.getElementById('totalBill');
    var numPeople = document.getElementById('numPeople');
    var serviceCharge = document.getElementById('serviceCharge');
    var roundMode = document.getElementById('roundMode');
    var includeMe = document.getElementById('includeMe');
    var calcBtn = document.getElementById('calcBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var grandTotal = document.getElementById('grandTotal');
    var perPerson = document.getElementById('perPerson');
    var adjustment = document.getElementById('adjustment');
    var shareTable = document.getElementById('shareTable');
    var adjustNote = document.getElementById('adjustNote');
    var waBtn = document.getElementById('waBtn');

    var lastSummary = '';

    function fmt(n) {
        return 'Rs ' + (Math.round(n * 100) / 100).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
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

    function roundShare(x, mode) {
        if (mode === 'nearest10') return Math.round(x / 10) * 10;
        if (mode === 'nearest100') return Math.round(x / 100) * 100;
        return Math.round(x * 100) / 100;
    }

    calcBtn.addEventListener('click', function () {
        clearError();
        var bill = parseFloat(totalBill.value);
        var people = parseInt(numPeople.value, 10);
        var sc = parseFloat(serviceCharge.value) || 0;

        if (isNaN(bill) || bill <= 0) { showError('Enter a valid total bill amount.'); return; }
        if (isNaN(people) || people < 1) { showError('The number of people must be at least 1.'); return; }
        if (sc < 0 || sc > 100) { showError('Enter the service charge between 0 and 100 %.'); return; }

        var grand = bill * (1 + sc / 100);
        var raw = grand / people;
        var rounded = roundShare(raw, roundMode.value);

        // Distribute so the sum always equals the grand total (largest remainder on rounded shares)
        var shares = [];
        var i, sum = 0;
        for (i = 0; i < people; i++) { shares.push(rounded); sum += rounded; }
        var diff = Math.round((grand - sum) * 100) / 100;
        if (roundMode.value !== 'exact' && diff !== 0) {
            shares[0] = Math.round((shares[0] + diff) * 100) / 100;
            sum = Math.round((sum + diff) * 100) / 100;
        }

        grandTotal.textContent = fmt(grand);
        perPerson.textContent = fmt(rounded);
        adjustment.textContent = fmt(Math.round((grand - rounded * people) * 100) / 100);

        shareTable.innerHTML = '';
        for (i = 0; i < people; i++) {
            var tr = document.createElement('tr');
            var tdName = document.createElement('td');
            tdName.textContent = 'Person ' + (i + 1) + (includeMe.checked && i === 0 ? ' (You)' : '');
            var tdAmt = document.createElement('td');
            tdAmt.className = 'text-end fw-semibold';
            tdAmt.textContent = fmt(shares[i]);
            tr.appendChild(tdName);
            tr.appendChild(tdAmt);
            shareTable.appendChild(tr);
        }

        if (roundMode.value !== 'exact' && diff !== 0) {
            adjustNote.textContent = 'Due to rounding, ' + fmt(Math.abs(diff)) + ' was adjusted in Person 1 share so the total matches.';
        } else {
            adjustNote.textContent = '';
        }

        var lines = [];
        lines.push('Bill Split (' + people + ' people):');
        lines.push('Total bill: Rs ' + bill.toLocaleString('en-PK'));
        if (sc > 0) lines.push('Service charge (' + sc + '%): Rs ' + (Math.round((grand - bill) * 100) / 100).toLocaleString('en-PK'));
        lines.push('Grand total: ' + fmt(grand));
        lines.push('Share per person: ' + fmt(rounded));
        lines.push('- Azlaan Tools');
        lastSummary = lines.join('\n');

        results.classList.remove('d-none');
    });

    waBtn.addEventListener('click', function () {
        if (!lastSummary) return;
        var done = function () {
            waBtn.textContent = 'Copied! Paste it in WhatsApp';
            setTimeout(function () { waBtn.textContent = 'Copy for WhatsApp'; }, 2500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastSummary).then(done, function () {
                window.open('https://wa.me/?text=' + encodeURIComponent(lastSummary), '_blank');
            });
        } else {
            window.open('https://wa.me/?text=' + encodeURIComponent(lastSummary), '_blank');
        }
    });
})();
</script>
@endsection
