@extends('layouts.app')

@section('title', 'Flat vs Reducing Interest - Azlaan Tools')
@section('meta_description', 'Compare flat-rate markup vs reducing-balance interest: total cost and monthly installment of both, side by side.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Flat vs Reducing Interest</h1>
            <p class="lead text-muted">Compare flat-rate markup vs reducing-balance interest side by side — see which method charges you more. This is only an educational comparison, not financial advice.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="principal" class="form-label fw-semibold">Principal / amount (Rs)</label>
                            <input type="number" class="form-control" id="principal" placeholder="100000" min="1" step="1">
                        </div>
                        <div class="col-md-4">
                            <label for="rate" class="form-label fw-semibold">Yearly rate (% per year)</label>
                            <input type="number" class="form-control" id="rate" placeholder="15" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="months" class="form-label fw-semibold">Term (months)</label>
                            <input type="number" class="form-control" id="months" placeholder="24" min="1" max="360" step="1">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="calcBtn">Compare</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="resultWrap" class="d-none">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 border-danger">
                            <div class="card-header bg-danger text-white fw-semibold">Flat Rate Markup</div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-1"><span>Total markup</span><strong class="text-danger" id="flatInt">-</strong></div>
                                <div class="d-flex justify-content-between mb-1"><span>Total payable</span><strong id="flatTotal">-</strong></div>
                                <div class="d-flex justify-content-between mb-1"><span>Monthly installment</span><strong id="flatEmi">-</strong></div>
                                <div class="form-text mt-2">Formula: markup = principal × rate × years. The installment is exactly the same every month.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 border-success">
                            <div class="card-header bg-success text-white fw-semibold">Reducing Balance</div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-1"><span>Total interest</span><strong class="text-success" id="redInt">-</strong></div>
                                <div class="d-flex justify-content-between mb-1"><span>Total payable</span><strong id="redTotal">-</strong></div>
                                <div class="d-flex justify-content-between mb-1"><span>Monthly installment (EMI)</span><strong id="redEmi">-</strong></div>
                                <div class="form-text mt-2">Formula: interest is charged only on the remaining balance each month — each installment slowly reduces the principal.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert" id="verdictBox" role="alert"></div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Monthly comparison table</h2>
                        <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light sticky-top">
                                    <tr><th>Month</th><th class="text-end">Flat installment</th><th class="text-end">Reducing installment</th><th class="text-end">Reducing principal</th><th class="text-end">Reducing interest</th><th class="text-end">Reducing balance</th></tr>
                                </thead>
                                <tbody id="compBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Key point to understand</h2>
                        <p class="mb-0">With flat markup, interest is charged on the <strong>full original amount</strong> — even if half the installments are already paid. That is why the real cost of a flat rate is usually <strong>nearly double</strong> reducing. In reducing-balance, the remaining amount gets smaller every month, so the interest keeps shrinking too. For every loan offer, do not ask only about the "rate" — always ask the method (flat or reducing); that is where the real difference is.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the amount (principal), yearly rate and term (months).</li>
                <li>Press <strong>Compare</strong> — you will see the total cost and monthly installment of both methods side by side.</li>
                <li>See the detail of each month in the table below.</li>
            </ol>
            <p class="text-muted small">Disclaimer: this calculator is only an educational estimate — not financial advice or a lending service. Always confirm the real loan terms with the lender.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var principal = document.getElementById('principal');
    var rate = document.getElementById('rate');
    var months = document.getElementById('months');
    var calcBtn = document.getElementById('calcBtn');
    var errorBox = document.getElementById('errorBox');
    var resultWrap = document.getElementById('resultWrap');
    var flatInt = document.getElementById('flatInt');
    var flatTotal = document.getElementById('flatTotal');
    var flatEmi = document.getElementById('flatEmi');
    var redInt = document.getElementById('redInt');
    var redTotal = document.getElementById('redTotal');
    var redEmi = document.getElementById('redEmi');
    var verdictBox = document.getElementById('verdictBox');
    var compBody = document.getElementById('compBody');

    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function fmt(n) {
        return 'Rs ' + Number(n).toLocaleString('en-PK', { maximumFractionDigits: 2, minimumFractionDigits: 2 });
    }

    function calculate() {
        hideError();
        var P = Number(principal.value);
        var R = Number(rate.value);
        var N = Math.floor(Number(months.value));
        if (!P || P <= 0) { showError('Enter an amount (principal) above 0.'); return; }
        if (isNaN(R) || R < 0 || R > 100) { showError('Enter a yearly rate between 0 and 100.'); return; }
        if (!N || N < 1 || N > 360) { showError('Enter a term between 1 and 360 months.'); return; }

        // Flat markup: markup on full principal for the whole term
        var flatMarkup = P * (R / 100) * (N / 12);
        var flatTot = P + flatMarkup;
        var flatInst = flatTot / N;

        // Reducing balance EMI (monthly amortization)
        var i = R / 1200; // monthly rate
        var redTot, emi, redInterest;
        if (i === 0) {
            emi = P / N;
            redTot = P;
            redInterest = 0;
        } else {
            var f = Math.pow(1 + i, N);
            emi = P * i * f / (f - 1);
            redTot = emi * N;
            redInterest = redTot - P;
        }

        flatInt.textContent = fmt(flatMarkup);
        flatTotal.textContent = fmt(flatTot);
        flatEmi.textContent = fmt(flatInst);
        redInt.textContent = fmt(redInterest);
        redTotal.textContent = fmt(redTot);
        redEmi.textContent = fmt(emi);

        var diff = flatMarkup - redInterest;
        var multiple = redInterest > 0 ? (flatMarkup / redInterest) : 0;
        verdictBox.className = 'alert alert-warning';
        if (diff > 0.005) {
            verdictBox.innerHTML = 'Flat markup charges you <strong>' + fmt(diff) + ' more</strong> — ' +
                (multiple ? multiple.toFixed(1) : '—') + 'x more cost than reducing-balance interest. So before taking a loan, always ask the method (flat or reducing).';
        } else if (diff < -0.005) {
            verdictBox.className = 'alert alert-success';
            verdictBox.innerHTML = 'For this term, reducing-balance is <strong>' + fmt(-diff) + ' cheaper</strong> than flat markup.';
        } else {
            verdictBox.className = 'alert alert-info';
            verdictBox.innerHTML = 'At zero or very small rates, both methods cost the same.';
        }

        // Monthly amortization table for reducing balance
        compBody.innerHTML = '';
        var bal = P;
        for (var m = 1; m <= N; m++) {
            var intPart = bal * i;
            var princPart = emi - intPart;
            if (m === N) { princPart = bal; } // final adjustment
            bal -= princPart;
            if (bal < 0.005 && bal > -0.005) bal = 0;
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + m + '</td>' +
                '<td class="text-end">' + fmt(flatInst) + '</td>' +
                '<td class="text-end">' + fmt(emi) + '</td>' +
                '<td class="text-end text-success">' + fmt(princPart) + '</td>' +
                '<td class="text-end text-danger">' + fmt(intPart) + '</td>' +
                '<td class="text-end text-muted">' + fmt(Math.max(bal, 0)) + '</td>';
            compBody.appendChild(tr);
        }
        resultWrap.classList.remove('d-none');
    }

    calcBtn.addEventListener('click', calculate);
})();
</script>
@endsection
