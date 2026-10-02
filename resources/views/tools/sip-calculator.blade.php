@extends('layouts.app')

@section('title', 'SIP Calculator PKR — Monthly Investment Growth — Azlaan Tools')
@section('meta_description', 'Free SIP calculator for Pakistan. Monthly investment, expected return and years to estimate maturity value, total invested and gains for mutual funds and savings plans.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">SIP Calculator</h1>
            <p class="lead text-muted">Small investments every month — estimate how much your monthly amount will grow in a mutual fund or savings plan.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="sipMonthly" class="form-label fw-semibold">Monthly Investment (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="sipMonthly" min="0" step="any" value="25000">
                        </div>
                        <div class="col-md-4">
                            <label for="sipReturn" class="form-label fw-semibold">Expected Annual Return (%)</label>
                            <input type="number" class="form-control form-control-lg" id="sipReturn" min="0" step="any" value="12">
                        </div>
                        <div class="col-md-4">
                            <label for="sipYears" class="form-label fw-semibold">Time Period (Years)</label>
                            <input type="number" class="form-control form-control-lg" id="sipYears" min="1" step="1" value="10">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Invested</div><div class="fs-5 fw-bold" id="sipInvested">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Estimated Gains</div><div class="fs-5 fw-bold text-success" id="sipGains">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Maturity Value (Estimate)</div><div class="fs-4 fw-bold" id="sipMaturity">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Note: This is only an estimate — the returns are your own assumption, not a guarantee. Mutual funds have market risk, so the actual return can be higher or lower.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Yearly Growth Table</h2>
                    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead><tr><th>Year</th><th>Invested So Far</th><th>Value at Year End</th><th>Gains</th></tr></thead>
                            <tbody id="sipTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the monthly investment in Rs — what you want to invest every month.</li>
                        <li>Write the expected annual return (%) — this is your own assumption.</li>
                        <li>Choose the years — for how long you will invest.</li>
                        <li>Total invested, estimated gains, maturity value and the yearly table will show instantly.</li>
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
        var monthly = parseFloat(document.getElementById('sipMonthly').value) || 0;
        var annual = parseFloat(document.getElementById('sipReturn').value) || 0;
        var years = parseInt(document.getElementById('sipYears').value, 10) || 0;
        if (monthly <= 0 || years <= 0) {
            document.getElementById('sipInvested').textContent = '—';
            document.getElementById('sipGains').textContent = '—';
            document.getElementById('sipMaturity').textContent = '—';
            document.getElementById('sipTable').innerHTML = '';
            return;
        }
        var rm = annual / 100 / 12;
        var totalMonths = years * 12;
        var fv = rm !== 0 ? monthly * ((Math.pow(1 + rm, totalMonths) - 1) / rm) * (1 + rm) : monthly * totalMonths;
        var invested = monthly * totalMonths;
        document.getElementById('sipInvested').textContent = fmt(invested);
        document.getElementById('sipGains').textContent = fmt(fv - invested);
        document.getElementById('sipMaturity').textContent = fmt(fv);
        var html = '';
        for (var y = 1; y <= years; y++) {
            var m = y * 12;
            var val = rm !== 0 ? monthly * ((Math.pow(1 + rm, m) - 1) / rm) * (1 + rm) : monthly * m;
            var inv = monthly * m;
            html += '<tr><td>' + y + '</td><td>' + fmt(inv) + '</td><td>' + fmt(val) + '</td><td>' + fmt(val - inv) + '</td></tr>';
        }
        document.getElementById('sipTable').innerHTML = html;
    }
    ['sipMonthly', 'sipReturn', 'sipYears'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
