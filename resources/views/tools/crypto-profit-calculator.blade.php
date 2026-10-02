@extends('layouts.app')
@section('title', 'Crypto Profit Calculator - Gains, Loss & ROI | Azlaan Tools')
@section('meta_description', 'Calculate crypto investment profit, loss and ROI for free. Enter buy and sell prices with fees to see your exact gain — no signup, instant results.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Crypto Profit Calculator</h1>
            <p class="lead text-muted">Calculate your crypto profit or loss — find your exact profit and ROI with buy/sell price, quantity and fees.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="coinName" class="form-label fw-semibold">Coin name (optional)</label>
                            <input type="text" class="form-control" id="coinName" placeholder="e.g. Bitcoin">
                        </div>
                        <div class="col-md-6">
                            <label for="currencySel" class="form-label fw-semibold">Currency</label>
                            <select class="form-select" id="currencySel">
                                <option value="PKR" selected>PKR (Rs)</option>
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="AED">AED (د.إ)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="buyPrice" class="form-label fw-semibold">Buy price per coin</label>
                            <input type="number" class="form-control" id="buyPrice" placeholder="e.g. 8500000" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="qty" class="form-label fw-semibold">Quantity (how many coins)</label>
                            <input type="number" class="form-control" id="qty" placeholder="e.g. 0.05" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="buyFee" class="form-label fw-semibold">Buy fee %</label>
                            <input type="number" class="form-control" id="buyFee" placeholder="0" min="0" max="100" step="any" value="0">
                            <div class="form-text">Exchange fee, for example 0.1</div>
                        </div>
                        <div class="col-md-6">
                            <label for="sellPrice" class="form-label fw-semibold">Sell price per coin</label>
                            <input type="number" class="form-control" id="sellPrice" placeholder="e.g. 9200000" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="sellFee" class="form-label fw-semibold">Sell fee %</label>
                            <input type="number" class="form-control" id="sellFee" placeholder="0" min="0" max="100" step="any" value="0">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate Profit</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5 mb-3" id="resTitle">Result</h2>
                        <div class="alert" id="verdictBox" role="status"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr><th scope="row">Total invested (buy)</th><td id="rInvested"></td></tr>
                                    <tr><th scope="row">Buy fee</th><td id="rBuyFee"></td></tr>
                                    <tr><th scope="row">Total cost</th><td id="rCost"></td></tr>
                                    <tr><th scope="row">Sale proceeds (before fee)</th><td id="rProceeds"></td></tr>
                                    <tr><th scope="row">Sell fee</th><td id="rSellFee"></td></tr>
                                    <tr><th scope="row">Net received</th><td id="rNet"></td></tr>
                                    <tr class="table-active"><th scope="row">Net profit / loss</th><td id="rProfit" class="fw-bold"></td></tr>
                                    <tr class="table-active"><th scope="row">ROI (return on investment)</th><td id="rRoi" class="fw-bold"></td></tr>
                                    <tr><th scope="row">Break-even sell price</th><td id="rBreakEven"></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Disclaimer:</strong> This is only a calculation tool, not financial advice. Crypto rates change fast — confirm the official exchange rate before making a decision. Rates can change.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the coin name and currency.</li>
                <li>Enter the buy price, quantity and buy fee %.</li>
                <li>Enter the sell price and sell fee %.</li>
                <li>Press <strong>Calculate Profit</strong> — profit, loss and ROI will show right away.</li>
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
    var verdictBox = document.getElementById('verdictBox');

    var symbols = { PKR: 'Rs ', USD: '$', EUR: '€', AED: 'AED ' };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function num(id) {
        var v = parseFloat(document.getElementById(id).value);
        return isNaN(v) ? NaN : v;
    }
    function fmt(n, cur) {
        var sign = n < 0 ? '-' : '';
        var a = Math.abs(n);
        var s = a >= 1000 ? a.toLocaleString('en-US', { maximumFractionDigits: 2 }) : a.toFixed(2);
        return sign + symbols[cur] + s;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var coin = document.getElementById('coinName').value.trim();
        var cur = document.getElementById('currencySel').value;
        var bp = num('buyPrice'), q = num('qty'), bf = num('buyFee');
        var sp = num('sellPrice'), sf = num('sellFee');
        if (isNaN(bp) || bp <= 0) { showError('Please enter a valid buy price.'); return; }
        if (isNaN(q) || q <= 0) { showError('Please enter a valid quantity.'); return; }
        if (isNaN(sp) || sp < 0) { showError('Please enter a valid sell price.'); return; }
        bf = isNaN(bf) ? 0 : bf;
        sf = isNaN(sf) ? 0 : sf;
        if (bf < 0 || bf > 100 || sf < 0 || sf > 100) { showError('Fee % must be between 0 and 100.'); return; }

        var invested = bp * q;
        var buyFeeAmt = invested * bf / 100;
        var totalCost = invested + buyFeeAmt;
        var proceeds = sp * q;
        var sellFeeAmt = proceeds * sf / 100;
        var net = proceeds - sellFeeAmt;
        var profit = net - totalCost;
        var roi = totalCost > 0 ? (profit / totalCost) * 100 : 0;
        var breakEven = q > 0 ? totalCost / (q * (1 - sf / 100)) : 0;

        document.getElementById('resTitle').textContent = coin ? 'Result — ' + coin : 'Result';
        document.getElementById('rInvested').textContent = fmt(invested, cur);
        document.getElementById('rBuyFee').textContent = fmt(buyFeeAmt, cur) + ' (' + bf + '%)';
        document.getElementById('rCost').textContent = fmt(totalCost, cur);
        document.getElementById('rProceeds').textContent = fmt(proceeds, cur);
        document.getElementById('rSellFee').textContent = fmt(sellFeeAmt, cur) + ' (' + sf + '%)';
        document.getElementById('rNet').textContent = fmt(net, cur);
        var rp = document.getElementById('rProfit');
        rp.textContent = fmt(profit, cur);
        rp.className = 'fw-bold ' + (profit >= 0 ? 'text-success' : 'text-danger');
        var rr = document.getElementById('rRoi');
        rr.textContent = (roi >= 0 ? '+' : '') + roi.toFixed(2) + '%';
        rr.className = 'fw-bold ' + (roi >= 0 ? 'text-success' : 'text-danger');
        document.getElementById('rBreakEven').textContent = fmt(breakEven, cur) + ' per coin';

        verdictBox.className = 'alert ' + (profit >= 0 ? 'alert-success' : 'alert-danger');
        if (profit >= 0) {
            verdictBox.innerHTML = '<strong>Profit! 🎉</strong> You gain <strong>' + fmt(profit, cur) + '</strong> (' + roi.toFixed(2) + '% ROI).';
        } else {
            verdictBox.innerHTML = '<strong>Loss ⚠️</strong> You lose <strong>' + fmt(profit, cur) + '</strong> (' + roi.toFixed(2) + '% ROI).';
        }

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
