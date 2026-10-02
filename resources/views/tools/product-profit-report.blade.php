@extends('layouts.app')

@section('title', 'Product-wise Profit Report - Azlaan Tools')
@section('meta_description', 'See the margin on each item — purchase price vs sale price, with margin % and monthly profit in a ranked report.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Product-wise Profit Report</h1>
            <p class="lead text-muted">See the margin on each item — purchase price vs sale price. The report is ranked by margin %. Your data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Add a product</h5>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label for="ppName" class="form-label fw-semibold">Product name</label>
                            <input type="text" class="form-control" id="ppName" placeholder="e.g. Surf Excel 1kg">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="ppBuy" class="form-label fw-semibold">Purchase (Rs)</label>
                            <input type="number" class="form-control" id="ppBuy" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="ppSell" class="form-label fw-semibold">Sale (Rs)</label>
                            <input type="number" class="form-control" id="ppSell" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="ppUnits" class="form-label fw-semibold">Monthly units <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="number" class="form-control" id="ppUnits" placeholder="0" min="0" step="1">
                        </div>
                        <div class="col-6 col-md-2">
                            <button type="button" class="btn btn-primary w-100" id="addPpBtn">Add</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="ppError" role="alert"></div>
                </div>
            </div>

            <div class="row g-3 mb-4" id="ppSummary">
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center"><div class="card-body py-2"><small class="text-muted">Products</small><div class="fw-bold fs-5" id="ppCount">0</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center"><div class="card-body py-2"><small class="text-muted">Avg margin %</small><div class="fw-bold fs-5" id="ppAvg">—</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center"><div class="card-body py-2"><small class="text-muted">Top margin product</small><div class="fw-bold fs-6 text-success" id="ppTop">—</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center"><div class="card-body py-2"><small class="text-muted">Est. monthly profit</small><div class="fw-bold fs-5" id="ppMonthly">—</div></div></div></div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Ranked report <small class="text-muted">(by margin %)</small></h5>
                        <button type="button" class="btn btn-outline-success btn-sm" id="ppCsv">CSV Download</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm align-middle">
                            <thead class="table-light">
                                <tr><th>#</th><th>Product</th><th class="text-end">Purchase</th><th class="text-end">Sale</th><th class="text-end">Margin Rs</th><th class="text-end">Margin %</th><th class="text-end">Monthly profit</th><th></th></tr>
                            </thead>
                            <tbody id="ppRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="ppEmpty">No products yet. Add one from above.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the product name, purchase price and sale price, then click <strong>Add</strong>. Adding monthly units also shows estimated monthly profit.</li>
                <li>The report is ranked by margin % — the highest-margin product is on top.</li>
                <li>For low-margin items, think about revising the rate or changing the supplier.</li>
            </ol>
            <p class="text-muted small">Margin % = (Sale − Purchase) ÷ Sale × 100. Your data is saved only in your browser.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_product_profit';
    var ppName = document.getElementById('ppName');
    var ppBuy = document.getElementById('ppBuy');
    var ppSell = document.getElementById('ppSell');
    var ppUnits = document.getElementById('ppUnits');
    var addPpBtn = document.getElementById('addPpBtn');
    var ppError = document.getElementById('ppError');
    var ppRows = document.getElementById('ppRows');
    var ppEmpty = document.getElementById('ppEmpty');
    var ppCount = document.getElementById('ppCount');
    var ppAvg = document.getElementById('ppAvg');
    var ppTop = document.getElementById('ppTop');
    var ppMonthly = document.getElementById('ppMonthly');
    var ppCsv = document.getElementById('ppCsv');

    var products = [];
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) { var p = JSON.parse(raw); if (Array.isArray(p)) products = p; }
    } catch (e) { products = []; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(products)); } catch (e) {}
    }
    function uid() {
        return 'p' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36);
    }
    function showError(msg) {
        ppError.textContent = msg;
        ppError.classList.remove('d-none');
    }
    function hideError() {
        ppError.classList.add('d-none');
        ppError.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }
    function marginRs(x) {
        return Math.round((x.sell - x.buy) * 100) / 100;
    }
    function marginPct(x) {
        if (x.sell <= 0) return 0;
        return Math.round((marginRs(x) / x.sell) * 1000) / 10;
    }
    function monthlyProfit(x) {
        var u = x.units || 0;
        if (!u) return null;
        return Math.round(marginRs(x) * u * 100) / 100;
    }

    function render() {
        hideError();
        ppRows.innerHTML = '';
        ppEmpty.style.display = products.length ? 'none' : '';
        var ranked = products.slice().sort(function (a, b) { return marginPct(b) - marginPct(a); });
        ranked.forEach(function (x, i) {
            var tr = document.createElement('tr');
            if (i === 0 && products.length > 1) tr.classList.add('table-success');
            var mp = marginPct(x);
            var mRs = marginRs(x);
            var mp2 = monthlyProfit(x);
            var tds = [];
            var tdR = document.createElement('td');
            tdR.innerHTML = i === 0 ? '<span class="badge bg-success">1</span>' : (i + 1);
            tds.push(tdR);
            var tdN = document.createElement('td');
            tdN.innerHTML = '<strong>' + esc(x.name) + '</strong>' + (x.units ? '<br><small class="text-muted">' + esc(String(x.units)) + ' units/month</small>' : '');
            tds.push(tdN);
            [['buy'], ['sell']].forEach(function (k) {
                var td = document.createElement('td');
                td.className = 'text-end';
                td.textContent = fmt(x[k[0]]);
                tds.push(td);
            });
            var tdM = document.createElement('td');
            tdM.className = 'text-end fw-bold ' + (mRs >= 0 ? 'text-success' : 'text-danger');
            tdM.textContent = fmt(mRs);
            tds.push(tdM);
            var tdP = document.createElement('td');
            tdP.className = 'text-end fw-bold ' + (mp >= 0 ? 'text-success' : 'text-danger');
            tdP.textContent = mp + '%';
            tds.push(tdP);
            var tdMp = document.createElement('td');
            tdMp.className = 'text-end';
            tdMp.textContent = mp2 === null ? '—' : fmt(mp2);
            tds.push(tdMp);
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete product');
            del.addEventListener('click', function () {
                if (!confirm('Delete ' + x.name + '?')) return;
                products = products.filter(function (y) { return y.id !== x.id; });
                save(); render();
            });
            tdX.appendChild(del);
            tds.push(tdX);
            tds.forEach(function (td) { tr.appendChild(td); });
            ppRows.appendChild(tr);
        });

        ppCount.textContent = products.length;
        if (products.length) {
            var sum = 0;
            ranked.forEach(function (x) { sum += marginPct(x); });
            var avg = Math.round((sum / products.length) * 10) / 10;
            ppAvg.textContent = avg + '%';
            ppAvg.className = 'fw-bold fs-5 ' + (avg >= 20 ? 'text-success' : (avg >= 10 ? 'text-warning' : 'text-danger'));
            ppTop.textContent = ranked[0].name + ' (' + marginPct(ranked[0]) + '%)';
            var totM = 0, hasUnits = false;
            products.forEach(function (x) {
                var mp2 = monthlyProfit(x);
                if (mp2 !== null) { totM += mp2; hasUnits = true; }
            });
            ppMonthly.textContent = hasUnits ? fmt(Math.round(totM * 100) / 100) : '—';
        } else {
            ppAvg.textContent = '—'; ppAvg.className = 'fw-bold fs-5';
            ppTop.textContent = '—';
            ppMonthly.textContent = '—';
        }
    }

    addPpBtn.addEventListener('click', function () {
        hideError();
        var name = ppName.value.trim();
        var buy = parseFloat(ppBuy.value);
        var sell = parseFloat(ppSell.value);
        var units = parseInt(ppUnits.value, 10);
        if (!name) { showError('Please enter the product name.'); return; }
        if (isNaN(buy) || buy < 0) { showError('Enter a purchase price of 0 or more.'); return; }
        if (isNaN(sell) || sell <= 0) { showError('Enter a sale price above 0.'); return; }
        products.push({
            id: uid(),
            name: name,
            buy: Math.round(buy * 100) / 100,
            sell: Math.round(sell * 100) / 100,
            units: isNaN(units) || units < 0 ? 0 : units
        });
        save();
        ppName.value = ''; ppBuy.value = ''; ppSell.value = ''; ppUnits.value = '';
        ppName.focus();
        render();
    });

    ppCsv.addEventListener('click', function () {
        hideError();
        if (!products.length) { showError('Add at least one product before downloading CSV.'); return; }
        var ranked = products.slice().sort(function (a, b) { return marginPct(b) - marginPct(a); });
        var lines = ['Rank,Product,Purchase,Sale,Margin Rs,Margin %,Monthly Units,Monthly Profit'];
        ranked.forEach(function (x, i) {
            var nm = '"' + x.name.replace(/"/g, '""') + '"';
            var mp2 = monthlyProfit(x);
            lines.push([i + 1, nm, x.buy, x.sell, marginRs(x), marginPct(x), x.units || 0, mp2 === null ? '' : mp2].join(','));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'product-profit-report.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    render();
})();
</script>
@endsection
