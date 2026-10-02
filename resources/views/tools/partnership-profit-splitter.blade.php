@extends('layouts.app')

@section('title', 'Partnership Profit Splitter - Azlaan Tools')
@section('meta_description', 'Split business profit or loss fairly between partners by investment ratio. Free online calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Partnership Profit Splitter</h1>
            <p class="lead text-muted">Split profit (or loss) between partners by investment ratio. Enter each partner name and investment, enter the total profit, and get the result.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="partnerRows"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addPartnerBtn">+ Add Partner</button>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="totalProfit" class="form-label fw-semibold">Total profit (PKR)</label>
                            <input type="number" class="form-control" id="totalProfit" placeholder="e.g. 500000" min="0" step="any">
                            <div class="form-text">If there is a loss, enter a negative value, e.g. -100000.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="splitMode" class="form-label fw-semibold">Split method</label>
                            <select class="form-select" id="splitMode">
                                <option value="investment">By investment ratio</option>
                                <option value="equal">Equal shares</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Split</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-3" id="resultTitle"></h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Partner</th>
                                        <th class="text-end">Investment</th>
                                        <th class="text-end">Share %</th>
                                        <th class="text-end">Gets</th>
                                    </tr>
                                </thead>
                                <tbody id="resultBody"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-info" id="summaryLine"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter each partner name and investment (use "+ Add Partner" to add more).</li>
                <li>Enter the total profit (or loss as a negative number) and choose the split method.</li>
                <li>Press "Calculate Split" — the table will show each partner share.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var partnerRows = document.getElementById('partnerRows');
    var addPartnerBtn = document.getElementById('addPartnerBtn');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultBody = document.getElementById('resultBody');
    var resultTitle = document.getElementById('resultTitle');
    var summaryLine = document.getElementById('summaryLine');
    var rowCount = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) {
        return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function addRow(name, inv) {
        rowCount++;
        var wrap = document.createElement('div');
        wrap.className = 'row g-2 mb-2 align-items-end partner-row';
        wrap.innerHTML =
            '<div class="col-5">' +
            '<label class="form-label fw-semibold small">Partner name</label>' +
            '<input type="text" class="form-control pname" placeholder="e.g. Partner ' + rowCount + '">' +
            '</div>' +
            '<div class="col-5">' +
            '<label class="form-label fw-semibold small">Investment (PKR)</label>' +
            '<input type="number" class="form-control pinv" placeholder="e.g. 200000" min="0" step="any">' +
            '</div>' +
            '<div class="col-2">' +
            '<button type="button" class="btn btn-outline-danger w-100 rm">x</button>' +
            '</div>';
        if (name) wrap.querySelector('.pname').value = name;
        if (inv) wrap.querySelector('.pinv').value = inv;
        wrap.querySelector('.rm').addEventListener('click', function () {
            if (partnerRows.querySelectorAll('.partner-row').length <= 2) {
                showError('At least 2 partners are required.');
                return;
            }
            wrap.remove();
            hideError();
        });
        partnerRows.appendChild(wrap);
    }

    addRow();
    addRow();
    addPartnerBtn.addEventListener('click', function () { hideError(); addRow(); });

    goBtn.addEventListener('click', function () {
        hideError();
        var rows = partnerRows.querySelectorAll('.partner-row');
        var partners = [];
        for (var i = 0; i < rows.length; i++) {
            var nm = rows[i].querySelector('.pname').value.trim() || ('Partner ' + (i + 1));
            var inv = parseFloat(rows[i].querySelector('.pinv').value);
            if (isNaN(inv) || inv < 0) {
                showError('"' + nm + '" — please enter a valid investment (0 or more).');
                return;
            }
            partners.push({ name: nm, inv: inv });
        }
        var profitRaw = document.getElementById('totalProfit').value.trim();
        var profit = parseFloat(profitRaw);
        if (profitRaw === '' || isNaN(profit)) {
            showError('Please enter the total profit (negative for a loss).');
            return;
        }
        var mode = document.getElementById('splitMode').value;
        var totalInv = partners.reduce(function (s, p) { return s + p.inv; }, 0);
        if (mode === 'investment' && totalInv <= 0) {
            showError('For investment ratio, at least one partner investment must be more than 0.');
            return;
        }

        resultBody.innerHTML = '';
        var totalShare = 0;
        partners.forEach(function (p) {
            var pct = mode === 'equal' ? (100 / partners.length) : (p.inv / totalInv * 100);
            var share = profit * pct / 100;
            totalShare += share;
            var tr = document.createElement('tr');
            var tdName = document.createElement('td');
            tdName.textContent = p.name;
            var tdInv = document.createElement('td');
            tdInv.className = 'text-end';
            tdInv.textContent = fmt(p.inv);
            var tdPct = document.createElement('td');
            tdPct.className = 'text-end';
            tdPct.textContent = pct.toFixed(2) + '%';
            var tdShare = document.createElement('td');
            tdShare.className = 'text-end fw-bold ' + (share < 0 ? 'text-danger' : 'text-success');
            tdShare.textContent = fmt(share);
            tr.appendChild(tdName);
            tr.appendChild(tdInv);
            tr.appendChild(tdPct);
            tr.appendChild(tdShare);
            resultBody.appendChild(tr);
        });

        resultTitle.textContent = profit < 0 ? 'Loss Split Result' : 'Profit Split Result';
        summaryLine.textContent = 'Total ' + (profit < 0 ? 'loss' : 'profit') + ': ' + fmt(profit) +
            ' — split: ' + fmt(totalShare) + ' (' + partners.length + ' partners, ' +
            (mode === 'equal' ? 'equal shares' : 'investment ratio') + ').';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
