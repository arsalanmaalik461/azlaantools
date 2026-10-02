@extends('layouts.app')

@section('title', 'Break-Even Calculator — Business Break Even Point — Azlaan Tools')
@section('meta_description', 'Free break-even calculator. Fixed costs, selling price and variable cost to find break-even units, break-even revenue and profit at expected sales.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Break-Even Calculator</h1>
            <p class="lead text-muted">Find the sales point where there is no profit and no loss. Enter fixed costs, price, and variable cost to find your break-even point.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="beFixed" class="form-label fw-semibold">Fixed Costs (Rs / month)</label>
                            <input type="number" class="form-control form-control-lg" id="beFixed" min="0" step="any" value="200000">
                            <div class="small text-muted">Rent, salaries, bills etc. — whether you sell or not.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="bePrice" class="form-label fw-semibold">Selling Price per Unit (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="bePrice" min="0" step="any" value="1500">
                        </div>
                        <div class="col-md-6">
                            <label for="beVariable" class="form-label fw-semibold">Variable Cost per Unit (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="beVariable" min="0" step="any" value="900">
                            <div class="small text-muted">Cost per unit — material, packing etc.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="beExpected" class="form-label">Expected Units Sold (per month) — optional</label>
                            <input type="number" class="form-control form-control-lg" id="beExpected" min="0" step="any" value="500">
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="beMsg"></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Margin per Unit</div><div class="fs-5 fw-bold" id="beMargin">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Break-Even Units / Month</div><div class="fs-4 fw-bold text-primary" id="beUnits">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Break-Even Revenue</div><div class="fs-5 fw-bold" id="beRevenue">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Profit at Expected Sales</div><div class="fs-5 fw-bold text-success" id="beProfit">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter monthly fixed costs — rent, staff, utilities etc.</li>
                        <li>Write the selling price of one unit and the variable cost per unit.</li>
                        <li>Break-even units and revenue show instantly — selling less than that means a loss.</li>
                        <li>Enter expected units sold to also see the expected profit at that sales level.</li>
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
    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
    function calc() {
        var fixed = parseFloat(document.getElementById('beFixed').value) || 0;
        var price = parseFloat(document.getElementById('bePrice').value) || 0;
        var variable = parseFloat(document.getElementById('beVariable').value) || 0;
        var expected = parseFloat(document.getElementById('beExpected').value) || 0;
        var msg = document.getElementById('beMsg');
        var margin = price - variable;
        if (price <= 0 || margin <= 0) {
            msg.textContent = 'Selling price must be higher than the variable cost - otherwise there is a loss on every unit and break-even is not possible.';
            msg.classList.remove('d-none');
            document.getElementById('beMargin').textContent = fmt(margin);
            document.getElementById('beUnits').textContent = '—';
            document.getElementById('beRevenue').textContent = '—';
            document.getElementById('beProfit').textContent = '—';
            return;
        }
        msg.classList.add('d-none');
        var unitsExact = fixed / margin;
        var units = Math.ceil(unitsExact);
        var revenue = unitsExact * price;
        document.getElementById('beMargin').textContent = fmt(margin);
        document.getElementById('beUnits').textContent = units.toLocaleString('en-PK') + ' units';
        document.getElementById('beRevenue').textContent = fmt(revenue);
        if (expected > 0) {
            var profit = expected * margin - fixed;
            var el = document.getElementById('beProfit');
            el.textContent = fmt(profit);
            el.className = profit >= 0 ? 'fs-5 fw-bold text-success' : 'fs-5 fw-bold text-danger';
        } else {
            document.getElementById('beProfit').textContent = '—';
        }
    }
    ['beFixed', 'bePrice', 'beVariable', 'beExpected'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
