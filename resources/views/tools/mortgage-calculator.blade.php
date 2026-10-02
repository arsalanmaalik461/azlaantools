@extends('layouts.app')

@section('title', 'Home Loan / Mortgage Calculator PKR — Azlaan Tools')
@section('meta_description', 'Free home loan and mortgage calculator for Pakistan. House price, down payment, interest rate and tenure to get monthly payment, total interest and total cost.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Home Loan / Mortgage Calculator</h1>
            <p class="lead text-muted">Planning to buy a home? Find the monthly payment, total interest and total cost in advance from the house price, down payment and bank rate.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="mtPrice" class="form-label fw-semibold">House Price (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="mtPrice" min="0" step="any" value="15000000">
                        </div>
                        <div class="col-md-6">
                            <label for="mtDownPct" class="form-label fw-semibold">Down Payment (%)</label>
                            <input type="number" class="form-control form-control-lg" id="mtDownPct" min="0" max="100" step="any" value="20">
                            <div class="small text-muted mt-1" id="mtDownAmt"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="mtRate" class="form-label fw-semibold">Annual Interest Rate (%)</label>
                            <input type="number" class="form-control form-control-lg" id="mtRate" min="0" step="any" value="18">
                        </div>
                        <div class="col-md-6">
                            <label for="mtYears" class="form-label fw-semibold">Tenure (Years)</label>
                            <input type="number" class="form-control form-control-lg" id="mtYears" min="1" step="1" value="20">
                        </div>
                        <div class="col-md-6">
                            <label for="mtFee" class="form-label">Processing Fee (Rs) — optional</label>
                            <input type="number" class="form-control form-control-lg" id="mtFee" min="0" step="any" value="50000">
                        </div>
                        <div class="col-md-6">
                            <label for="mtOther" class="form-label">Other Charges (Rs) — optional</label>
                            <input type="number" class="form-control form-control-lg" id="mtOther" min="0" step="any" value="0">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Loan Amount</div><div class="fs-5 fw-bold" id="mtLoan">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Monthly Payment</div><div class="fs-5 fw-bold text-success" id="mtMonthly">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Interest</div><div class="fs-5 fw-bold text-danger" id="mtInterest">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Cost of House</div><div class="fs-5 fw-bold" id="mtTotal">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Total cost includes the down payment, all payments, the processing fee and other charges. This is an estimate — actual bank terms may differ.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Amortization Summary — Year by Year</h2>
                    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead><tr><th>Year</th><th>Paid</th><th>Principal</th><th>Interest</th><th>Remaining Balance</th></tr></thead>
                            <tbody id="mtTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the house price in Rs and the down payment % — the loan amount will be calculated automatically.</li>
                        <li>Enter the bank's annual interest rate (%) and the tenure in years.</li>
                        <li>Add the processing fee and any other charges if there are any.</li>
                        <li>Monthly payment, total interest, total cost and the year-by-year schedule will show right away.</li>
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
        var price = parseFloat(document.getElementById('mtPrice').value) || 0;
        var downPct = parseFloat(document.getElementById('mtDownPct').value) || 0;
        var rate = parseFloat(document.getElementById('mtRate').value) || 0;
        var years = parseInt(document.getElementById('mtYears').value, 10) || 0;
        var fee = parseFloat(document.getElementById('mtFee').value) || 0;
        var other = parseFloat(document.getElementById('mtOther').value) || 0;
        var downAmt = price * downPct / 100;
        document.getElementById('mtDownAmt').textContent = price > 0 ? 'Down payment: ' + fmt(downAmt) : '';
        var loan = price - downAmt;
        var n = years * 12;
        if (loan <= 0 || n <= 0) {
            document.getElementById('mtLoan').textContent = '—';
            document.getElementById('mtMonthly').textContent = '—';
            document.getElementById('mtInterest').textContent = '—';
            document.getElementById('mtTotal').textContent = '—';
            document.getElementById('mtTable').innerHTML = '';
            return;
        }
        var r = rate / 100 / 12;
        var emi = r > 0 ? loan * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1) : loan / n;
        var totalPayable = emi * n;
        var totalInterest = totalPayable - loan;
        var totalCost = price + totalInterest + fee + other;
        document.getElementById('mtLoan').textContent = fmt(loan);
        document.getElementById('mtMonthly').textContent = fmt(emi);
        document.getElementById('mtInterest').textContent = fmt(totalInterest);
        document.getElementById('mtTotal').textContent = fmt(totalCost);
        var balance = loan, html = '', yPaid = 0, yPrin = 0, yInt = 0;
        for (var m = 1; m <= n; m++) {
            var interest = balance * r;
            var principal = emi - interest;
            if (principal > balance) principal = balance;
            balance -= principal;
            if (balance < 0.01) balance = 0;
            yPaid += emi; yPrin += principal; yInt += interest;
            if (m % 12 === 0 || m === n) {
                html += '<tr><td>' + Math.ceil(m / 12) + '</td><td>' + fmt(yPaid) + '</td><td>' + fmt(yPrin) + '</td><td>' + fmt(yInt) + '</td><td>' + fmt(balance) + '</td></tr>';
                yPaid = 0; yPrin = 0; yInt = 0;
            }
        }
        document.getElementById('mtTable').innerHTML = html;
    }
    ['mtPrice', 'mtDownPct', 'mtRate', 'mtYears', 'mtFee', 'mtOther'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
