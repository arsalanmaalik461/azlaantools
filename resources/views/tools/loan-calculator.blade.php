@extends('layouts.app')

@section('title', 'Loan Calculator PKR — Monthly Installment EMI — Azlaan Tools')
@section('meta_description', 'Free loan calculator for Pakistan. Enter loan amount in PKR, annual interest/markup and tenure in months to get monthly installment (EMI), total payable and total interest.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="mb-2">Loan Calculator</h1>
            <p class="text-muted mb-4">Bank loan, car finance or personal loan — calculate the monthly installment (EMI), total payable and total interest in advance to make a better decision.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="loanAmount" class="form-label">Loan Amount (PKR)</label>
                            <input type="number" class="form-control" id="loanAmount" placeholder="e.g. 1000000" step="any">
                        </div>
                        <div class="col-md-4">
                            <label for="loanRate" class="form-label">Annual Interest / Markup (%)</label>
                            <input type="number" class="form-control" id="loanRate" placeholder="e.g. 18" step="any">
                        </div>
                        <div class="col-md-4">
                            <label for="loanMonths" class="form-label">Tenure (months)</label>
                            <input type="number" class="form-control" id="loanMonths" placeholder="e.g. 36" step="1">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="calcLoanBtn">Calculate Installment</button>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Monthly Installment (EMI)</div><div class="fs-4 fw-bold" id="emiOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Payable</div><div class="fs-4 fw-bold" id="totalOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Interest / Markup</div><div class="fs-4 fw-bold text-danger" id="interestOut">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 d-none" id="scheduleCard">
                <div class="card-body">
                    <h2 class="h5 card-title">Amortization Summary — First 12 Months</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead><tr><th>Month</th><th>Payment</th><th>Principal</th><th>Interest</th><th>Remaining Balance</th></tr></thead>
                            <tbody id="scheduleBody"></tbody>
                        </table>
                    </div>
                    <h3 class="h6 mt-4">Yearly Totals</h3>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Year</th><th>Paid</th><th>Principal</th><th>Interest</th><th>Balance at Year End</th></tr></thead>
                            <tbody id="yearlyBody"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0">This estimate is based on the standard EMI formula. The actual amount can differ due to bank charges, processing fee, insurance and KIBOR-linked floating rates.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the loan amount in PKR.</li>
                        <li>Enter the annual interest / markup rate (%) — the one the bank tells you.</li>
                        <li>Enter the tenure in months (e.g. 3 years = 36 months).</li>
                        <li>Press &quot;Calculate Installment&quot; — EMI, total payable, total interest and the schedule will show.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function pkr(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
document.getElementById('calcLoanBtn').addEventListener('click', function(){
    var P = parseFloat(document.getElementById('loanAmount').value) || 0;
    var annual = parseFloat(document.getElementById('loanRate').value) || 0;
    var n = parseInt(document.getElementById('loanMonths').value, 10) || 0;
    if (P <= 0 || n <= 0) {
        document.getElementById('emiOut').textContent = '—';
        document.getElementById('totalOut').textContent = '—';
        document.getElementById('interestOut').textContent = '—';
        document.getElementById('scheduleCard').classList.add('d-none');
        return;
    }
    var r = annual / 100 / 12;
    var emi = r > 0 ? P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1) : P / n;
    var total = emi * n;
    document.getElementById('emiOut').textContent = pkr(emi);
    document.getElementById('totalOut').textContent = pkr(total);
    document.getElementById('interestOut').textContent = pkr(total - P);

    var balance = P, sHtml = '', yHtml = '';
    var yPaid = 0, yPrin = 0, yInt = 0;
    for (var m = 1; m <= n; m++) {
        var interest = balance * r;
        var principal = emi - interest;
        if (principal > balance) { principal = balance; }
        balance -= principal;
        if (balance < 0.01) balance = 0;
        yPaid += emi; yPrin += principal; yInt += interest;
        if (m <= 12) {
            sHtml += '<tr><td>' + m + '</td><td>' + pkr(emi) + '</td><td>' + pkr(principal) + '</td><td>' + pkr(interest) + '</td><td>' + pkr(balance) + '</td></tr>';
        }
        if (m % 12 === 0 || m === n) {
            var yr = Math.ceil(m / 12);
            yHtml += '<tr><td>Year ' + yr + '</td><td>' + pkr(yPaid) + '</td><td>' + pkr(yPrin) + '</td><td>' + pkr(yInt) + '</td><td>' + pkr(balance) + '</td></tr>';
            yPaid = 0; yPrin = 0; yInt = 0;
        }
    }
    document.getElementById('scheduleBody').innerHTML = sHtml;
    document.getElementById('yearlyBody').innerHTML = yHtml;
    document.getElementById('scheduleCard').classList.remove('d-none');
});
</script>
@endsection
