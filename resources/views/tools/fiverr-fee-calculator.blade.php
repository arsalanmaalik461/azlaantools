@extends('layouts.app')

@section('title', 'Fiverr Fee Calculator - Azlaan Tools')
@section('meta_description', 'Calculate Fiverr 20 percent seller fee and see your real earnings, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Fiverr Fee Calculator</h1>
            <p class="lead text-muted">How much will you actually earn after Fiverr takes its 20% fee? Enter the order amount and find out instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="amount" class="form-label fw-semibold">Order amount</label>
                            <input type="number" class="form-control" id="amount" min="0" step="0.01" placeholder="e.g. 100">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="currency" class="form-label fw-semibold">Currency</label>
                            <select class="form-select" id="currency">
                                <option value="$">USD ($)</option>
                                <option value="Rs">PKR (Rs)</option>
                                <option value="€">EUR (€)</option>
                                <option value="£">GBP (£)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="feePct" class="form-label fw-semibold">Fiverr fee %</label>
                            <input type="number" class="form-control" id="feePct" min="0" max="100" step="0.1" value="20">
                            <div class="form-text">Fiverr takes 20% from sellers.</div>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="withdrawFee" class="form-label fw-semibold">Withdrawal fee (optional)</label>
                            <input type="number" class="form-control" id="withdrawFee" min="0" step="0.01" value="0" placeholder="e.g. 3">
                            <div class="form-text">Subtract Payoneer/PayPal charges here.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Earnings</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <table class="table table-bordered">
                            <tbody>
                                <tr><th>Order amount</th><td id="rOrder" class="text-end"></td></tr>
                                <tr><th>Fiverr fee (<span id="rFeePct"></span>%)</th><td id="rFee" class="text-end text-danger"></td></tr>
                                <tr><th>You keep</th><td id="rKeep" class="text-end text-success fw-bold"></td></tr>
                                <tr><th>Withdrawal fee</th><td id="rWd" class="text-end"></td></tr>
                                <tr class="table-light"><th>Final in your pocket</th><td id="rNet" class="text-end fw-bold fs-5 text-success"></td></tr>
                            </tbody>
                        </table>
                        <div class="alert alert-info small mb-0">Tip: <span id="rTip"></span></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Disclaimer: Rates can change — confirm on the official website. This is an estimate, not tax advice.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the order amount and select the currency.</li>
                <li>Enter the Fiverr fee % (default 20) and the optional withdrawal fee.</li>
                <li>Press "Calculate Earnings" — see your real earnings.</li>
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

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n, cur) {
        return cur + ' ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var amount = parseFloat(document.getElementById('amount').value);
        var cur = document.getElementById('currency').value;
        var feePct = parseFloat(document.getElementById('feePct').value);
        var wd = parseFloat(document.getElementById('withdrawFee').value);

        if (isNaN(amount) || amount <= 0) { showError('Please enter a valid order amount.'); return; }
        if (isNaN(feePct) || feePct < 0 || feePct > 100) { showError('Fee % must be between 0 and 100.'); return; }
        if (isNaN(wd) || wd < 0) { wd = 0; }

        var fee = amount * feePct / 100;
        var keep = amount - fee;
        var net = Math.max(0, keep - wd);

        document.getElementById('rOrder').textContent = fmt(amount, cur);
        document.getElementById('rFeePct').textContent = feePct;
        document.getElementById('rFee').textContent = '- ' + fmt(fee, cur);
        document.getElementById('rKeep').textContent = fmt(keep, cur);
        document.getElementById('rWd').textContent = '- ' + fmt(wd, cur);
        document.getElementById('rNet').textContent = fmt(net, cur);

        var tip = 'For every $10 order you get ' + fmt(10 * (1 - feePct / 100), cur) + '. ';
        if (net < amount * 0.7) {
            tip += 'Little is left after the fee — keep the fee in mind when setting your gig rates.';
        } else {
            tip += 'Multiply by your number of orders to estimate monthly earnings.';
        }
        document.getElementById('rTip').textContent = tip;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
