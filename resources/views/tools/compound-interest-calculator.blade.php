@extends('layouts.app')

@section('title', 'Compound Interest Calculator PKR — Maturity Value — Azlaan Tools')
@section('meta_description', 'Free compound interest calculator for Pakistan. Principal, rate, years, compounding frequency and monthly deposit to get maturity value, total interest and yearly growth table.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Compound Interest Calculator</h1>
            <p class="lead text-muted">Calculate the compound profit on a saving, term deposit or investment — see how money grows with time, year by year.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="ciPrincipal" class="form-label fw-semibold">Principal Amount (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="ciPrincipal" min="0" step="any" value="500000">
                        </div>
                        <div class="col-md-6">
                            <label for="ciRate" class="form-label fw-semibold">Annual Interest Rate (%)</label>
                            <input type="number" class="form-control form-control-lg" id="ciRate" min="0" step="any" value="12">
                        </div>
                        <div class="col-md-6">
                            <label for="ciYears" class="form-label fw-semibold">Time Period (Years)</label>
                            <input type="number" class="form-control form-control-lg" id="ciYears" min="0" step="any" value="5">
                        </div>
                        <div class="col-md-6">
                            <label for="ciFreq" class="form-label fw-semibold">Compounding Frequency</label>
                            <select class="form-select form-select-lg" id="ciFreq">
                                <option value="1">Yearly</option>
                                <option value="2">Half-Yearly</option>
                                <option value="4">Quarterly</option>
                                <option value="12" selected>Monthly</option>
                                <option value="365">Daily</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ciDeposit" class="form-label">Monthly Deposit (Rs) — optional</label>
                            <input type="number" class="form-control form-control-lg" id="ciDeposit" min="0" step="any" value="10000">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Maturity Value</div><div class="fs-4 fw-bold text-success" id="ciMaturity">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Interest Earned</div><div class="fs-4 fw-bold" id="ciInterest">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Deposited</div><div class="fs-5 fw-bold" id="ciDeposited">—</div><div class="small text-muted" id="ciYield"></div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">This is an estimate — the rate is your own assumption, not a guaranteed return.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Year-by-Year Growth</h2>
                    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead><tr><th>Year</th><th>Deposited</th><th>Interest (Year)</th><th>Balance</th></tr></thead>
                            <tbody id="ciTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the principal amount in Rs — the money you invest or save today.</li>
                        <li>Enter the annual rate (%) and the years.</li>
                        <li>Choose the compounding frequency — monthly is the most common.</li>
                        <li>If you deposit more money every month, also enter the monthly deposit.</li>
                        <li>Results update instantly — see the maturity value, total interest and the year-by-year table.</li>
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
        var P = parseFloat(document.getElementById('ciPrincipal').value) || 0;
        var rate = parseFloat(document.getElementById('ciRate').value) || 0;
        var years = parseFloat(document.getElementById('ciYears').value) || 0;
        var n = parseFloat(document.getElementById('ciFreq').value) || 12;
        var dep = parseFloat(document.getElementById('ciDeposit').value) || 0;
        if (P <= 0 && dep <= 0 || years <= 0) {
            document.getElementById('ciMaturity').textContent = '—';
            document.getElementById('ciInterest').textContent = '—';
            document.getElementById('ciDeposited').textContent = '—';
            document.getElementById('ciYield').textContent = '';
            document.getElementById('ciTable').innerHTML = '';
            return;
        }
        var r = rate / 100;
        var totalPeriods = n * years;
        var perRate = r / n;
        var fvPrincipal = P * Math.pow(1 + perRate, totalPeriods);
        var fvDeposits = 0;
        if (dep > 0) {
            var months = Math.round(years * 12);
            var rm = Math.pow(1 + perRate, n / 12) - 1;
            if (rm > 0) { fvDeposits = dep * ((Math.pow(1 + rm, months) - 1) / rm); }
            else { fvDeposits = dep * months; }
        }
        var maturity = fvPrincipal + fvDeposits;
        var deposited = P + dep * Math.round(years * 12);
        var interest = maturity - deposited;
        var effYield = r > 0 ? (Math.pow(1 + perRate, n) - 1) * 100 : 0;
        document.getElementById('ciMaturity').textContent = fmt(maturity);
        document.getElementById('ciInterest').textContent = fmt(interest);
        document.getElementById('ciDeposited').textContent = fmt(deposited);
        document.getElementById('ciYield').textContent = 'Effective annual yield: ' + effYield.toFixed(2) + '%';
        var html = '';
        var prevBal = P;
        var runningDep = P;
        for (var y = 1; y <= Math.ceil(years); y++) {
            var frac = Math.min(1, years - (y - 1));
            var periods = n * frac;
            var bal = prevBal * Math.pow(1 + perRate, periods);
            var depYear = dep * 12 * frac;
            if (dep > 0) {
                var rm2 = Math.pow(1 + perRate, n / 12) - 1;
                var mths = Math.round(12 * frac);
                if (rm2 > 0) { bal += dep * ((Math.pow(1 + rm2, mths) - 1) / rm2); }
                else { bal += dep * mths; }
            }
            runningDep += depYear;
            var intYear = bal - prevBal - depYear;
            html += '<tr><td>' + y + '</td><td>' + fmt(runningDep) + '</td><td>' + fmt(intYear) + '</td><td>' + fmt(bal) + '</td></tr>';
            prevBal = bal;
        }
        document.getElementById('ciTable').innerHTML = html;
    }
    ['ciPrincipal', 'ciRate', 'ciYears', 'ciFreq', 'ciDeposit'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
