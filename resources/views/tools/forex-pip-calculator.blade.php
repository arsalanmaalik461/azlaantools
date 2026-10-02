@extends('layouts.app')
@section('title', 'Forex Pip Calculator - Azlaan Tools')
@section('meta_description', 'Calculate pip value and profit or loss on any forex trade — pip size, lot size, long/short and account currency conversion. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Forex Pip Calculator</h1>
            <p class="lead text-muted">Find the pip value and profit/loss on any forex trade — based on lot size, direction and your account currency.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="pairSel" class="form-label fw-semibold">Currency pair</label>
                            <select class="form-select" id="pairSel">
                                <option value="EURUSD|4">EUR/USD</option>
                                <option value="GBPUSD|4">GBP/USD</option>
                                <option value="AUDUSD|4">AUD/USD</option>
                                <option value="NZDUSD|4">NZD/USD</option>
                                <option value="USDJPY|2">USD/JPY</option>
                                <option value="USDCHF|4">USD/CHF</option>
                                <option value="USDCAD|4">USD/CAD</option>
                                <option value="EURGBP|4">EUR/GBP</option>
                                <option value="EURJPY|2">EUR/JPY</option>
                                <option value="GBPJPY|2">GBP/JPY</option>
                                <option value="OTHER|4">Other pair (4/5 digit quote)</option>
                                <option value="OTHERJPY|2">Other pair (2/3 digit JPY-style quote)</option>
                            </select>
                            <div class="form-text">For JPY pairs a pip = 0.01, for others it is 0.0001.</div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="dirSel" class="form-label fw-semibold">Direction</label>
                            <select class="form-select" id="dirSel">
                                <option value="long">Long (Buy)</option>
                                <option value="short">Short (Sell)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="lotsInput" class="form-label fw-semibold">Trade size (lots)</label>
                            <input type="number" class="form-control" id="lotsInput" value="1" min="0.01" step="0.01">
                            <div class="form-text">1 standard lot = 100,000 units.</div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="openPrice" class="form-label fw-semibold">Open price</label>
                            <input type="number" class="form-control" id="openPrice" step="any" placeholder="e.g. 1.0850">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="closePrice" class="form-label fw-semibold">Close price</label>
                            <input type="number" class="form-control" id="closePrice" step="any" placeholder="e.g. 1.0920">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="acctCur" class="form-label fw-semibold">Account currency</label>
                            <select class="form-select" id="acctCur">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="PKR">PKR</option>
                                <option value="AED">AED</option>
                                <option value="SAR">SAR</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3" id="convWrap">
                            <label for="convRate" class="form-label fw-semibold" id="convLabel">Conversion rate</label>
                            <input type="number" class="form-control" id="convRate" step="any" placeholder="e.g. 1.0850">
                            <div class="form-text" id="convHelp">The rate to change the quote currency into your account currency.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row text-center g-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Pip size</div>
                                    <div class="fs-5 fw-bold" id="rPipSize">—</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Pips moved</div>
                                    <div class="fs-5 fw-bold" id="rPips">—</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Pip value</div>
                                    <div class="fs-5 fw-bold" id="rPipVal">—</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Profit / Loss</div>
                                    <div class="fs-5 fw-bold" id="rPL">—</div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mt-3 mb-0" id="rNote"></p>
                    </div>

                    <div class="alert alert-warning mt-4 mb-0 small">
                        This is an educational estimate, not trading advice. Rates change over time — confirm with your broker.
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the currency pair and trade direction (Long/Short).</li>
                <li>Write the lot size, open price and close price.</li>
                <li>Choose your account currency — if it is different from the quote currency, also enter the conversion rate.</li>
                <li>Press <strong>Calculate</strong> — you will see the pip size, pips, pip value and total profit/loss.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var pairSel = document.getElementById('pairSel');
    var dirSel = document.getElementById('dirSel');
    var lotsInput = document.getElementById('lotsInput');
    var openPrice = document.getElementById('openPrice');
    var closePrice = document.getElementById('closePrice');
    var acctCur = document.getElementById('acctCur');
    var convRate = document.getElementById('convRate');
    var convLabel = document.getElementById('convLabel');
    var convHelp = document.getElementById('convHelp');
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
    function quoteOf(pair) {
        return pair.indexOf('OTHER') === 0 ? 'XXX' : pair.split('/')[1];
    }

    function refreshConv() {
        var q = quoteOf(pairSel.value.split('|')[0]);
        var a = acctCur.value;
        if (q === a || q === 'XXX') {
            convLabel.textContent = 'Conversion rate (1 ' + (q === 'XXX' ? 'quote unit' : q) + ' = ? ' + a + ')';
            convHelp.textContent = q === 'XXX'
                ? 'Write the rate for your pair quote currency.'
                : 'Account and quote currency are the same — no rate is needed, but you can still write 1.';
            if (q === a && !convRate.value) convRate.value = '1';
        } else {
            convLabel.textContent = 'Conversion rate (1 ' + q + ' = ? ' + a + ')';
            convHelp.textContent = 'Write the current market rate, for example 1 USD = how many ' + a + '.';
        }
    }
    pairSel.addEventListener('change', refreshConv);
    acctCur.addEventListener('change', refreshConv);
    refreshConv();

    goBtn.addEventListener('click', function () {
        hideError();
        var parts = pairSel.value.split('|');
        var pair = parts[0], digits = parseInt(parts[1], 10);
        var pipSize = digits === 2 ? 0.01 : 0.0001;
        var lots = parseFloat(lotsInput.value);
        var open = parseFloat(openPrice.value);
        var close = parseFloat(closePrice.value);
        var rate = parseFloat(convRate.value);

        if (!(lots > 0)) { showError('Trade size must be greater than 0.'); return; }
        if (!(open > 0) || !(close > 0)) { showError('Please enter both open and close prices.'); return; }

        var q = quoteOf(pair), a = acctCur.value;
        var needConv = (q !== a && q !== 'XXX');
        if (needConv && !(rate > 0)) { showError('Enter the rate to change the quote currency into your account currency.'); return; }
        if (!needConv && !(rate > 0)) rate = 1;

        var diff = close - open;
        if (dirSel.value === 'short') diff = -diff;
        var pips = diff / pipSize;
        var units = lots * 100000;
        var pipValQuote = pipSize * units;
        var pipValAcct = pipValQuote * rate;
        var plAcct = pips * pipValAcct;

        function money(x, cur) {
            var s = (x < 0 ? '-' : '') + cur + ' ' + Math.abs(x).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return s;
        }

        document.getElementById('rPipSize').textContent = pipSize;
        document.getElementById('rPips').textContent = (pips >= 0 ? '+' : '') + pips.toFixed(1);
        document.getElementById('rPipVal').textContent = money(pipValAcct, a);
        var plEl = document.getElementById('rPL');
        plEl.textContent = (plAcct >= 0 ? '+' : '-') + money(Math.abs(plAcct), a).replace(/^-/, '');
        plEl.classList.remove('text-success', 'text-danger');
        plEl.classList.add(plAcct >= 0 ? 'text-success' : 'text-danger');
        document.getElementById('rNote').textContent =
            '1 standard lot = 100,000 units. Pip value in quote currency (' + (q === 'XXX' ? 'your pair' : q) + ') is ' +
            pipValQuote.toLocaleString('en-US', { maximumFractionDigits: 2 }) + ', converted to ' + a + ' at the rate ' + rate + '.';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
