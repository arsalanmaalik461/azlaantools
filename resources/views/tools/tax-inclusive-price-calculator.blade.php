@extends('layouts.app')

@section('title', 'Tax Inclusive / Exclusive Price Calculator - Azlaan Tools')
@section('meta_description', 'Calculate sales tax both ways — check if a price includes tax or not. 18% default, adjustable tax rate, reverse calculation.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Tax Inclusive / Exclusive Calculator</h1>
            <p class="lead text-muted">Does the price include tax or not? Work out the price both ways — with and without sales tax.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="txAmount" class="form-label fw-semibold">Amount (Rs) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg" id="txAmount" placeholder="Example: 1000" min="0" step="0.01">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="txRate" class="form-label fw-semibold">Tax rate (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-lg" id="txRate" value="18" min="0" max="100" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary tx-preset" data-rate="0">0%</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary tx-preset" data-rate="1">1%</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary tx-preset" data-rate="5">5%</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary tx-preset" data-rate="16">16%</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary tx-preset" data-rate="18">18%</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">In the given amount, tax is...</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="txMode" id="txModeExcl" value="excl" checked>
                            <label class="form-check-label" for="txModeExcl">NOT included (exclusive) — add tax</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="txMode" id="txModeIncl" value="incl">
                            <label class="form-check-label" for="txModeIncl">INCLUDED (inclusive) — take tax out</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="txCalc">Calculate</button>
                        <button type="button" class="btn btn-outline-secondary" id="txSwap">Reverse (change mode)</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="txError" role="alert"></div>

                    <div id="txResult" class="mt-4 d-none">
                        <div class="row text-center g-2">
                            <div class="col-12 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted" id="txLblNet">Price (before tax)</small>
                                    <div class="fw-bold fs-4" id="txNet">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted" id="txLblTax">Tax amount</small>
                                    <div class="fw-bold fs-4 text-primary" id="txTax">Rs 0</div>
                                </div></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card bg-light"><div class="card-body py-3">
                                    <small class="text-muted" id="txLblGross">Price (with tax)</small>
                                    <div class="fw-bold fs-4 text-success" id="txGross">Rs 0</div>
                                </div></div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 mb-0" id="txExplain" role="note"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Formula (how it is worked out)</h5>
                    <ul class="mb-0">
                        <li><strong>Exclusive &rarr; Inclusive:</strong> Tax = Amount &times; Rate &divide; 100 &nbsp;|&nbsp; Total = Amount + Tax</li>
                        <li><strong>Inclusive &rarr; Exclusive:</strong> Net = Total &divide; (1 + Rate &divide; 100) &nbsp;|&nbsp; Tax = Total &minus; Net</li>
                    </ul>
                    <p class="text-muted small mt-2 mb-0">Example: at 18%, Rs 1,000 exclusive = Rs 1,180 inclusive. And Rs 1,180 inclusive = Rs 1,000 net + Rs 180 tax.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the amount and set the tax rate (default 18%).</li>
                <li>Tell whether the given amount includes tax or not.</li>
                <li>Press <strong>Calculate</strong> — you will get all three figures (net, tax, gross). Use <strong>Reverse</strong> to change the mode in one click.</li>
            </ol>
            <p class="small text-muted">Disclaimer: These are informational estimates only — not official FBR filing or tax advice. Confirm the actual tax rate for your case.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var txAmount = document.getElementById('txAmount');
    var txRate = document.getElementById('txRate');
    var txCalc = document.getElementById('txCalc');
    var txSwap = document.getElementById('txSwap');
    var txError = document.getElementById('txError');
    var txResult = document.getElementById('txResult');
    var txNet = document.getElementById('txNet');
    var txTax = document.getElementById('txTax');
    var txGross = document.getElementById('txGross');
    var txExplain = document.getElementById('txExplain');
    var txLblTax = document.getElementById('txLblTax');
    var txModeExcl = document.getElementById('txModeExcl');
    var txModeIncl = document.getElementById('txModeIncl');
    var presets = document.querySelectorAll('.tx-preset');

    function fmt(n) {
        return 'Rs ' + Number(n).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function r2(n) { return Math.round(n * 100) / 100; }
    function showError(msg) {
        txError.textContent = msg;
        txError.classList.remove('d-none');
        txResult.classList.add('d-none');
    }
    function hideError() {
        txError.classList.add('d-none');
        txError.textContent = '';
    }
    function mode() {
        return txModeIncl.checked ? 'incl' : 'excl';
    }

    function calc() {
        hideError();
        var amount = parseFloat(txAmount.value);
        var rate = parseFloat(txRate.value);
        if (isNaN(amount) || amount < 0) { showError('Enter a valid amount (0 or more).'); txAmount.focus(); return; }
        if (isNaN(rate) || rate < 0 || rate > 100) { showError('Enter the tax rate between 0 and 100.'); txRate.focus(); return; }
        var net, tax, gross;
        if (mode() === 'excl') {
            net = r2(amount);
            tax = r2(amount * rate / 100);
            gross = r2(net + tax);
        } else {
            gross = r2(amount);
            net = r2(amount / (1 + rate / 100));
            tax = r2(gross - net);
        }
        txNet.textContent = fmt(net);
        txTax.textContent = fmt(tax);
        txGross.textContent = fmt(gross);
        txLblTax.textContent = 'Tax amount (' + rate + '%)';
        if (mode() === 'excl') {
            txExplain.textContent = 'Adding ' + rate + '% tax to Rs ' + amount.toLocaleString('en-PK') + ' gives a total of ' + fmt(gross) + ' (tax: ' + fmt(tax) + ').';
        } else {
            txExplain.textContent = 'Taking ' + rate + '% tax out of Rs ' + amount.toLocaleString('en-PK') + ' (with tax) gives a net price of ' + fmt(net) + ' and tax of ' + fmt(tax) + '.';
        }
        txResult.classList.remove('d-none');
    }

    txCalc.addEventListener('click', calc);
    txAmount.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); calc(); }
    });
    txModeExcl.addEventListener('change', function () { if (txAmount.value) calc(); });
    txModeIncl.addEventListener('change', function () { if (txAmount.value) calc(); });
    txSwap.addEventListener('click', function () {
        if (mode() === 'excl') txModeIncl.checked = true;
        else txModeExcl.checked = true;
        if (txAmount.value) calc();
    });
    presets.forEach(function (b) {
        b.addEventListener('click', function () {
            txRate.value = b.getAttribute('data-rate');
            if (txAmount.value) calc();
        });
    });
    txRate.addEventListener('input', function () {
        if (txAmount.value && !txResult.classList.contains('d-none')) calc();
    });
})();
</script>
@endsection
