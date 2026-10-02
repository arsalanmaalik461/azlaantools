@extends('layouts.app')

@section('title', 'Prize Bond Winnings Calculator - Azlaan Tools')
@section('meta_description', 'Calculate your net prize bond winnings in Pakistan after filer and non-filer tax deductions, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Prize Bond Winnings Calculator</h1>
            <p class="lead text-muted">Calculate your prize bond winnings — with filer and non-filer tax deduction. Select your bond and prize, find the net amount.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="bondSel" class="form-label fw-semibold">Prize bond amount</label>
                        <select class="form-select" id="bondSel">
                            <option value="100">Rs. 100</option>
                            <option value="200">Rs. 200</option>
                            <option value="750">Rs. 750</option>
                            <option value="1500" selected>Rs. 1,500</option>
                            <option value="7500">Rs. 7,500</option>
                            <option value="15000">Rs. 15,000</option>
                            <option value="25000">Rs. 25,000</option>
                            <option value="40000">Rs. 40,000</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="prizeSel" class="form-label fw-semibold">Which prize did you win?</label>
                        <select class="form-select" id="prizeSel">
                            <option value="1">1st prize</option>
                            <option value="2">2nd prize</option>
                            <option value="3">3rd prize</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filerSel" class="form-label fw-semibold">Tax filer status</label>
                        <select class="form-select" id="filerSel">
                            <option value="15">Filer (15% withholding tax)</option>
                            <option value="30">Non-Filer (30% withholding tax)</option>
                        </select>
                        <div class="form-text">Withholding tax applies to the prize — filers pay less tax.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-3">Result</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr><td class="fw-semibold">Full prize amount (Gross)</td><td class="text-end" id="grossRow">-</td></tr>
                                <tr><td class="fw-semibold">Withholding tax (<span id="taxPct">-</span>)</td><td class="text-end text-danger" id="taxRow">-</td></tr>
                                <tr class="table-success"><td class="fw-bold">Amount you get (Net)</td><td class="text-end fw-bold" id="netRow">-</td></tr>
                            </tbody>
                        </table>
                        <div class="alert alert-info mb-0">
                            <small><strong>Note:</strong> Rates can change — confirm on the official website (savings.gov.pk / FBR). This calculation is only a guide.</small>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your prize bond amount.</li>
                <li>Select which prize you won (1st, 2nd or 3rd).</li>
                <li>Select Filer or Non-Filer status and press "Calculate".</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var bondSel = document.getElementById('bondSel');
    var prizeSel = document.getElementById('prizeSel');
    var filerSel = document.getElementById('filerSel');

    // Official-style published prize amounts (gross), keyed by denomination then prize position
    var PRIZES = {
        '100':   { 1: 700000,    2: 200000,    3: 1000 },
        '200':   { 1: 750000,    2: 250000,    3: 5000 },
        '750':   { 1: 1500000,   2: 500000,    3: 9300 },
        '1500':  { 1: 3000000,   2: 1000000,   3: 18500 },
        '7500':  { 1: 15000000,  2: 5000000,   3: 93000 },
        '15000': { 1: 30000000,  2: 10000000,  3: 185000 },
        '25000': { 1: 50000000,  2: 15000000,  3: 312000 },
        '40000': { 1: 75000000,  2: 25000000,  3: 500000 }
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) {
        return 'Rs. ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var denom = bondSel.value;
        var pos = parseInt(prizeSel.value, 10);
        var taxRate = parseInt(filerSel.value, 10);

        if (!PRIZES[denom] || !PRIZES[denom][pos]) {
            showError('No data for this combination.');
            return;
        }
        var gross = PRIZES[denom][pos];
        var tax = Math.round(gross * taxRate / 100);
        var net = gross - tax;

        document.getElementById('grossRow').textContent = fmt(gross);
        document.getElementById('taxPct').textContent = taxRate + '%';
        document.getElementById('taxRow').textContent = '- ' + fmt(tax);
        document.getElementById('netRow').textContent = fmt(net);
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
