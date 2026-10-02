@extends('layouts.app')

@section('title', 'Reorder Point Calculator - Azlaan Tools')
@section('meta_description', 'Calculate from daily sales and lead time — when to order and how much. Free reorder point calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Reorder Point Calculator</h1>
            <p class="lead text-muted">From daily sales and lead time — when to order, how much to order. No data is saved, just the calculation.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Enter your numbers</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label for="rpDaily" class="form-label fw-semibold">Avg daily sales (units)</label>
                            <input type="number" class="form-control" id="rpDaily" placeholder="e.g. 5" min="0" step="0.1">
                            <div class="form-text">Average units sold per day.</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="rpLead" class="form-label fw-semibold">Lead time (days)</label>
                            <input type="number" class="form-control" id="rpLead" placeholder="e.g. 7" min="0" step="1">
                            <div class="form-text">Days from placing the order to receiving the goods.</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="rpSafety" class="form-label fw-semibold">Safety stock (days)</label>
                            <input type="number" class="form-control" id="rpSafety" placeholder="e.g. 3" min="0" step="1">
                            <div class="form-text">Extra days of stock for emergencies.</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="rpOnHand" class="form-label fw-semibold">Current stock (units)</label>
                            <input type="number" class="form-control" id="rpOnHand" placeholder="e.g. 20" min="0" step="1">
                            <div class="form-text">Units you have on hand right now.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="rpCalcBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4 d-none">
                        <hr>
                        <h2 class="h5 mb-3">Result</h2>
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted">Reorder Point</small>
                                    <div class="fw-bold fs-4" id="resROP">0</div>
                                    <small class="text-muted">units — order at this point</small>
                                </div></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted">Order Quantity</small>
                                    <div class="fw-bold fs-4" id="resOrder">0</div>
                                    <small class="text-muted">units to order</small>
                                </div></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card"><div class="card-body py-3" id="verdictCard">
                                    <small class="text-muted">Status</small>
                                    <div class="fw-bold fs-5" id="resVerdict">—</div>
                                </div></div>
                            </div>
                        </div>
                        <div class="alert alert-info small" id="resExplain"></div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="table-light"><tr><th>Part</th><th class="text-end">Value</th></tr></thead>
                                <tbody id="resBreakdown"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li><strong>Avg daily sales</strong> — enter last 1-2 months of sales per day.</li>
                <li><strong>Lead time</strong> = days from ordering with the supplier to receiving the goods.</li>
                <li><strong>Safety stock</strong> = buffer for delays or sudden sales increases (usually 2-5 days).</li>
                <li>Press <strong>Calculate</strong> — you will get the reorder point, order qty and whether to order now.</li>
            </ol>
            <p class="small text-muted">Formula: Reorder Point = (Daily Sales &times; Lead Time) + (Daily Sales &times; Safety Days). Suggested Order = Reorder Point &minus; Current Stock. This is an estimate, not a guarantee.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var rpDaily = document.getElementById('rpDaily');
    var rpLead = document.getElementById('rpLead');
    var rpSafety = document.getElementById('rpSafety');
    var rpOnHand = document.getElementById('rpOnHand');
    var rpCalcBtn = document.getElementById('rpCalcBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resROP = document.getElementById('resROP');
    var resOrder = document.getElementById('resOrder');
    var resVerdict = document.getElementById('resVerdict');
    var verdictCard = document.getElementById('verdictCard');
    var resExplain = document.getElementById('resExplain');
    var resBreakdown = document.getElementById('resBreakdown');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function r2(n) {
        return Math.round(n * 100) / 100;
    }

    rpCalcBtn.addEventListener('click', function () {
        hideError();
        var daily = Number(rpDaily.value);
        var lead = Number(rpLead.value);
        var safetyDays = Number(rpSafety.value);
        var onHand = Number(rpOnHand.value);
        if (isNaN(daily) || daily <= 0) { showError('Avg daily sales must be above zero.'); return; }
        if (isNaN(lead) || lead < 0) { showError('Lead time must be zero or more.'); return; }
        if (isNaN(safetyDays) || safetyDays < 0) { showError('Safety stock must be zero or more.'); return; }
        if (isNaN(onHand) || onHand < 0) { showError('Current stock must be zero or more.'); return; }

        var leadDemand = daily * lead;
        var safetyStock = daily * safetyDays;
        var rop = leadDemand + safetyStock;
        var orderQty = Math.max(0, rop - onHand);
        var daysOfStock = daily > 0 ? onHand / daily : 0;

        resROP.textContent = r2(rop).toLocaleString('en-PK') + ' units';
        resOrder.textContent = Math.ceil(orderQty).toLocaleString('en-PK') + ' units';

        verdictCard.className = 'card-body py-3';
        if (onHand <= rop) {
            resVerdict.textContent = 'ORDER NOW';
            resVerdict.className = 'fw-bold fs-5 text-danger';
            verdictCard.classList.add('bg-danger', 'bg-opacity-10');
        } else {
            resVerdict.textContent = 'Stock is fine for now';
            resVerdict.className = 'fw-bold fs-5 text-success';
            verdictCard.classList.add('bg-success', 'bg-opacity-10');
        }

        resExplain.innerHTML = 'Your reorder point is <strong>' + r2(rop).toLocaleString('en-PK') +
            '</strong> units. You have <strong>' + r2(onHand).toLocaleString('en-PK') +
            '</strong> units — that is about <strong>' + r2(daysOfStock).toLocaleString('en-PK') +
            ' days</strong> of stock. After ordering, the goods will arrive in <strong>' + lead + ' days</strong>.';

        var rows = [
            ['Lead time demand (daily &times; lead)', r2(leadDemand) + ' units'],
            ['Safety stock (daily &times; safety days)', r2(safetyStock) + ' units'],
            ['Reorder point (sum of both)', r2(rop) + ' units'],
            ['Current stock', r2(onHand) + ' units'],
            ['Suggested order qty (round up)', Math.ceil(orderQty) + ' units']
        ];
        resBreakdown.innerHTML = '';
        rows.forEach(function (r) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td');
            td1.innerHTML = r[0];
            var td2 = document.createElement('td');
            td2.className = 'text-end fw-semibold';
            td2.textContent = r[1];
            tr.appendChild(td1);
            tr.appendChild(td2);
            resBreakdown.appendChild(tr);
        });

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
@endsection
