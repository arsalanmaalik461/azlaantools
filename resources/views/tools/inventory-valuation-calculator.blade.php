@extends('layouts.app')

@section('title', 'Inventory Valuation Calculator - Azlaan Tools')
@section('meta_description', 'Find your stock value: calculate COGS and closing inventory with the FIFO and weighted average cost methods. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Inventory Valuation Calculator</h1>
            <p class="lead text-muted">Calculate the value of your stock — get COGS and closing inventory with both the FIFO and Weighted Average Cost methods.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Stock purchases — enter in order from oldest to newest</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle" id="purchTable">
                            <thead>
                                <tr><th>#</th><th>Details (optional)</th><th>Qty (units)</th><th>Unit cost (Rs)</th><th></th></tr>
                            </thead>
                            <tbody id="purchBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addRowBtn">+ Add row</button>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="soldQty" class="form-label fw-semibold">Units sold</label>
                            <input type="number" class="form-control" id="soldQty" min="0" step="1" placeholder="e.g. 120">
                            <div class="form-text">Or enter the remaining stock below — one of the two is enough.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="closingQty" class="form-label fw-semibold">Remaining stock (closing units)</label>
                            <input type="number" class="form-control" id="closingQty" min="0" step="1" placeholder="e.g. 80">
                        </div>
                        <div class="col-md-6">
                            <label for="sellPrice" class="form-label fw-semibold">Selling price per unit (optional)</label>
                            <input type="number" class="form-control" id="sellPrice" min="0" step="any" placeholder="for gross profit">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate Valuation</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h6">Result — comparison of both methods</h2>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr><th>Item</th><th>FIFO</th><th>Weighted Average</th></tr>
                                </thead>
                                <tbody id="resultBody"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-info small" id="explainBox"></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Disclaimer: this is an estimate — please confirm with your accountant for final accounts or tax.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter each purchase lot in one row (qty + unit cost), oldest lot first.</li>
                <li>Enter the units sold or the remaining stock.</li>
                <li>Press "Calculate Valuation" — you will get results for both FIFO and Average.</li>
            </ol>
            <h2>What is the difference between FIFO and Average?</h2>
            <p><strong>FIFO (First In, First Out):</strong> the stock bought first is treated as sold first — when prices rise, the closing stock value is higher. <strong>Weighted Average:</strong> the average unit cost of all purchases is applied to both COGS and closing stock.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var purchBody = document.getElementById('purchBody');
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

    function addRow(qty, cost, label) {
        rowCount++;
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td>' + rowCount + '</td>' +
            '<td><input type="text" class="form-control form-control-sm" data-f="label" placeholder="Lot 1"></td>' +
            '<td><input type="number" class="form-control form-control-sm" data-f="qty" min="0" step="1" placeholder="100"></td>' +
            '<td><input type="number" class="form-control form-control-sm" data-f="cost" min="0" step="any" placeholder="50"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger" data-act="del">×</button></td>';
        if (qty) tr.querySelector('[data-f="qty"]').value = qty;
        if (cost) tr.querySelector('[data-f="cost"]').value = cost;
        if (label) tr.querySelector('[data-f="label"]').value = label;
        tr.querySelector('[data-act="del"]').addEventListener('click', function () {
            tr.remove();
            renumber();
        });
        purchBody.appendChild(tr);
    }

    function renumber() {
        var rows = purchBody.querySelectorAll('tr');
        rows.forEach(function (tr, i) {
            tr.children[0].textContent = i + 1;
        });
    }

    document.getElementById('addRowBtn').addEventListener('click', function () { addRow(); });

    function fmt(n) {
        return 'Rs ' + Math.round(n * 100) / 100;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var lots = [];
        purchBody.querySelectorAll('tr').forEach(function (tr) {
            var q = parseFloat(tr.querySelector('[data-f="qty"]').value);
            var c = parseFloat(tr.querySelector('[data-f="cost"]').value);
            if (q > 0 && c >= 0) lots.push({ qty: q, cost: c });
        });
        if (lots.length === 0) { showError('Please enter qty and unit cost in at least one purchase row.'); return; }

        var totalUnits = 0, totalCost = 0;
        lots.forEach(function (l) { totalUnits += l.qty; totalCost += l.qty * l.cost; });

        var sold = parseFloat(document.getElementById('soldQty').value);
        var closing = parseFloat(document.getElementById('closingQty').value);
        var hasSold = !isNaN(sold) && sold >= 0;
        var hasClosing = !isNaN(closing) && closing >= 0;
        if (!hasSold && !hasClosing) { showError('Please enter either sold units or closing units.'); return; }
        if (hasSold && hasClosing && Math.abs(sold + closing - totalUnits) > 0.001) {
            showError('Sold + closing (' + (sold + closing) + ') must equal total purchases (' + totalUnits + ').');
            return;
        }
        var soldUnits = hasSold ? sold : totalUnits - closing;
        var closingUnits = hasClosing ? closing : totalUnits - sold;
        if (soldUnits < 0 || closingUnits < 0 || soldUnits > totalUnits) {
            showError('The unit numbers are not correct — sold or closing cannot be more than total purchases.');
            return;
        }

        // FIFO: consume oldest lots first for COGS
        var fifoCOGS = 0, remaining = soldUnits;
        for (var i = 0; i < lots.length && remaining > 0; i++) {
            var take = Math.min(lots[i].qty, remaining);
            fifoCOGS += take * lots[i].cost;
            remaining -= take;
        }
        var fifoClosing = totalCost - fifoCOGS;

        // Weighted average
        var avgUnit = totalCost / totalUnits;
        var avgCOGS = soldUnits * avgUnit;
        var avgClosing = closingUnits * avgUnit;

        var sellPrice = parseFloat(document.getElementById('sellPrice').value);
        var hasSell = !isNaN(sellPrice) && sellPrice >= 0;
        var revenue = hasSell ? soldUnits * sellPrice : null;

        var tbody = document.getElementById('resultBody');
        tbody.innerHTML = '';
        var rows = [
            ['Total purchases (units)', totalUnits, totalUnits],
            ['Total purchase cost', fmt(totalCost), fmt(totalCost)],
            ['Units sold', soldUnits, soldUnits],
            ['COGS (cost of goods sold)', fmt(fifoCOGS), fmt(avgCOGS)],
            ['Closing stock (units)', closingUnits, closingUnits],
            ['Closing stock value', fmt(fifoClosing), fmt(avgClosing)]
        ];
        if (hasSell) {
            rows.push(['Sales (revenue)', fmt(revenue), fmt(revenue)]);
            rows.push(['Gross profit', fmt(revenue - fifoCOGS), fmt(revenue - avgCOGS)]);
        }
        rows.forEach(function (r) {
            var tr = document.createElement('tr');
            [r[0], r[1], r[2]].forEach(function (cell, ci) {
                var td = document.createElement(ci === 0 ? 'th' : 'td');
                td.scope = ci === 0 ? 'row' : null;
                td.textContent = cell;
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });

        var explain = document.getElementById('explainBox');
        var diff = fifoClosing - avgClosing;
        var txt = 'Weighted average unit cost: ' + fmt(avgUnit) + '. ';
        if (Math.abs(diff) < 0.01) {
            txt += 'Both methods give the same result (all lots had the same cost).';
        } else if (diff > 0) {
            txt += 'FIFO closing stock is ' + fmt(diff) + ' higher — when prices rise, FIFO shows a higher closing value.';
        } else {
            txt += 'The average method closing stock is ' + fmt(-diff) + ' higher — when prices fall, the average method shows a higher closing value.';
        }
        explain.textContent = txt;

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // seed two example rows
    addRow(100, 50, 'Opening stock');
    addRow(80, 60, 'Purchase March');
})();
</script>
@endsection
