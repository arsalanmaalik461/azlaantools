@extends('layouts.app')

@section('title', 'Profit / Margin Calculator - Business Profit in PKR | Azlaan Tools')
@section('meta_description', 'Free profit and margin calculator in PKR: find profit, markup percent and margin percent, plus the selling price needed for your target margin. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Profit / Margin Calculator</h1>
            <p class="lead text-muted">Work out profit, markup and margin for your shop or business in rupees — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Profit from Cost &amp; Selling Price</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="costPrice" class="form-label fw-semibold">Cost Price (PKR)</label>
                            <input type="number" class="form-control" id="costPrice" placeholder="e.g. 1000" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="sellPrice" class="form-label fw-semibold">Selling Price (PKR)</label>
                            <input type="number" class="form-control" id="sellPrice" placeholder="e.g. 1250" min="0" step="any">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="calcBtn">Calculate Profit</button>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="errorBox"></div>
                    <div id="resultWrap" class="d-none mt-3">
                        <div class="row g-2 text-center">
                            <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="small text-muted">Profit / Loss</div><div class="fw-bold" id="resProfit">-</div></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="small text-muted">Markup % (on cost)</div><div class="fw-bold" id="resMarkup">-</div></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="small text-muted">Margin % (on selling)</div><div class="fw-bold" id="resMargin">-</div></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="small text-muted">Result</div><div class="fw-bold" id="resVerdict">-</div></div></div>
                        </div>
                        <p class="small text-muted mt-2 mb-0">Markup is profit divided by cost price. Margin is profit divided by selling price. They are different — most businesses quote margin on the selling price.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Reverse: Selling Price for a Target Margin</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="revCost" class="form-label fw-semibold">Cost Price (PKR)</label>
                            <input type="number" class="form-control" id="revCost" placeholder="e.g. 1000" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="revMargin" class="form-label fw-semibold">Desired Margin % (on selling price)</label>
                            <input type="number" class="form-control" id="revMargin" placeholder="e.g. 20" min="0" max="99" step="any">
                        </div>
                    </div>
                    <button type="button" class="btn btn-success mt-3" id="revBtn">Find Selling Price</button>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="revError"></div>
                    <div class="alert alert-success mt-3 mb-0 d-none" id="revResult"></div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter cost price and selling price, then click <strong>Calculate Profit</strong>.</li>
                        <li>See your profit in PKR plus both markup percent and margin percent explained.</li>
                        <li>For pricing, use the reverse calculator: enter cost and your target margin percent.</li>
                        <li>Get the exact selling price you should charge to hit that margin.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function fmt(n) {
        var sign = n < 0 ? '-' : '';
        var abs = Math.abs(n);
        var s = abs.toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return sign + 'Rs ' + s;
    }
    var errorBox = document.getElementById('errorBox');
    document.getElementById('calcBtn').addEventListener('click', function () {
        errorBox.classList.add('d-none');
        var cost = parseFloat(document.getElementById('costPrice').value);
        var sell = parseFloat(document.getElementById('sellPrice').value);
        if (isNaN(cost) || isNaN(sell) || cost < 0 || sell < 0) {
            errorBox.textContent = 'Please enter valid cost and selling prices (0 or more).';
            errorBox.classList.remove('d-none');
            return;
        }
        if (cost === 0 && sell === 0) {
            errorBox.textContent = 'Both prices cannot be zero.';
            errorBox.classList.remove('d-none');
            return;
        }
        var profit = sell - cost;
        var markup = cost > 0 ? (profit / cost) * 100 : 0;
        var margin = sell > 0 ? (profit / sell) * 100 : 0;
        document.getElementById('resProfit').textContent = fmt(profit);
        document.getElementById('resMarkup').textContent = cost > 0 ? markup.toFixed(2) + '%' : '—';
        document.getElementById('resMargin').textContent = margin.toFixed(2) + '%';
        var verdict = 'Break-even';
        if (profit > 0) { verdict = 'Profit'; }
        if (profit < 0) { verdict = 'Loss'; }
        var vEl = document.getElementById('resVerdict');
        vEl.textContent = verdict;
        vEl.className = 'fw-bold ' + (profit > 0 ? 'text-success' : (profit < 0 ? 'text-danger' : 'text-muted'));
        document.getElementById('resultWrap').classList.remove('d-none');
    });
    var revError = document.getElementById('revError');
    var revResult = document.getElementById('revResult');
    document.getElementById('revBtn').addEventListener('click', function () {
        revError.classList.add('d-none');
        revResult.classList.add('d-none');
        var cost = parseFloat(document.getElementById('revCost').value);
        var margin = parseFloat(document.getElementById('revMargin').value);
        if (isNaN(cost) || cost <= 0) {
            revError.textContent = 'Please enter a valid cost price greater than zero.';
            revError.classList.remove('d-none');
            return;
        }
        if (isNaN(margin) || margin < 0 || margin >= 100) {
            revError.textContent = 'Margin must be between 0% and 99.99%. A 100% margin is impossible.';
            revError.classList.remove('d-none');
            return;
        }
        var sell = cost / (1 - margin / 100);
        var profit = sell - cost;
        revResult.innerHTML = 'Required selling price: <strong>' + fmt(sell) + '</strong><br>Profit at this price: <strong>' + fmt(profit) + '</strong>';
        revResult.classList.remove('d-none');
    });
})();
</script>
@endsection
