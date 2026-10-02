@extends('layouts.app')

@section('title', 'EMI Calculator PKR — Monthly Installment — Azlaan Tools')
@section('meta_description', 'Free EMI calculator for Pakistan. Loan amount, interest rate and tenure to get monthly EMI, total interest, total payable and full amortization table.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">EMI Calculator</h1>
            <p class="lead text-muted">Calculate the monthly installment (EMI) of your car, bike, personal or home loan — with total interest and the full repayment schedule.</p>
            <div class="alert alert-info">Note: Same calculation as our Loan Calculator — this page uses the EMI terms Pakistani banks use.</div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="emiAmount" class="form-label fw-semibold">Loan Amount (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="emiAmount" min="0" step="any" value="1000000">
                        </div>
                        <div class="col-md-4">
                            <label for="emiRate" class="form-label fw-semibold">Annual Interest Rate (%)</label>
                            <input type="number" class="form-control form-control-lg" id="emiRate" min="0" step="any" value="18">
                        </div>
                        <div class="col-md-4">
                            <label for="emiTenure" class="form-label fw-semibold">Tenure</label>
                            <div class="input-group input-group-lg">
                                <input type="number" class="form-control" id="emiTenure" min="1" step="1" value="36">
                                <select class="form-select" id="emiUnit" style="max-width: 120px;">
                                    <option value="months" selected>Months</option>
                                    <option value="years">Years</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Monthly EMI</div><div class="fs-4 fw-bold text-success" id="emiMonthly">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Interest</div><div class="fs-4 fw-bold text-danger" id="emiInterest">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Payable</div><div class="fs-4 fw-bold" id="emiTotal">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Full Amortization Table</h2>
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead><tr><th>Month</th><th>EMI</th><th>Principal</th><th>Interest</th><th>Remaining Balance</th></tr></thead>
                            <tbody id="emiTable"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mt-2 mb-0">This is an estimate — the actual amount may differ due to bank processing fee, insurance and floating rate.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the loan amount in Rs.</li>
                        <li>Enter the annual interest / markup rate (%) your bank is offering.</li>
                        <li>Choose the tenure in months or years.</li>
                        <li>Monthly EMI, total interest, total payable and the full schedule will show at once.</li>
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
        var P = parseFloat(document.getElementById('emiAmount').value) || 0;
        var rate = parseFloat(document.getElementById('emiRate').value) || 0;
        var t = parseInt(document.getElementById('emiTenure').value, 10) || 0;
        var unit = document.getElementById('emiUnit').value;
        var n = unit === 'years' ? t * 12 : t;
        if (P <= 0 || n <= 0) {
            document.getElementById('emiMonthly').textContent = '—';
            document.getElementById('emiInterest').textContent = '—';
            document.getElementById('emiTotal').textContent = '—';
            document.getElementById('emiTable').innerHTML = '';
            return;
        }
        var r = rate / 100 / 12;
        var emi = r > 0 ? P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1) : P / n;
        var total = emi * n;
        document.getElementById('emiMonthly').textContent = fmt(emi);
        document.getElementById('emiInterest').textContent = fmt(total - P);
        document.getElementById('emiTotal').textContent = fmt(total);
        var balance = P, html = '';
        for (var m = 1; m <= n; m++) {
            var interest = balance * r;
            var principal = emi - interest;
            if (principal > balance) principal = balance;
            var payment = principal + interest;
            balance -= principal;
            if (balance < 0.01) balance = 0;
            html += '<tr><td>' + m + '</td><td>' + fmt(payment) + '</td><td>' + fmt(principal) + '</td><td>' + fmt(interest) + '</td><td>' + fmt(balance) + '</td></tr>';
        }
        document.getElementById('emiTable').innerHTML = html;
    }
    ['emiAmount', 'emiRate', 'emiTenure', 'emiUnit'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
