@extends('layouts.app')

@section('title', 'Dollar Rate Today Guide - Azlaan Tools')
@section('meta_description', 'Dollar rate today Pakistan: the difference between interbank and open market, and a guide to checking rates from official sources.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Dollar Rate Today Guide</h1>
            <p class="lead text-muted">Why the dollar rate differs between interbank and open market, and where to check the daily rate from official sources — the full guide.</p>

            <div class="alert alert-info">
                <strong>Note:</strong> This tool does not show live rates. Rates change all the time — check today's rate yourself from the official sources below.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Rate Spread Calculator</h2>
                    <p class="text-muted small">Type today's interbank and open market rates here (from official sources) — the difference and spread % will calculate automatically.</p>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="interbankRate" class="form-label fw-semibold">Interbank Rate (PKR)</label>
                            <input type="number" class="form-control" id="interbankRate" placeholder="e.g. 282.50" min="1" step="0.01">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="openRate" class="form-label fw-semibold">Open Market Rate (PKR)</label>
                            <input type="number" class="form-control" id="openRate" placeholder="e.g. 284.75" min="1" step="0.01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="amountPkr" class="form-label fw-semibold">Amount in PKR (optional)</label>
                        <input type="number" class="form-control" id="amountPkr" placeholder="e.g. 100000" min="1">
                        <div class="form-text">See how many dollars you get for your rupees — compare both rates.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Spread</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody id="spreadTable"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small" id="spreadNote"></p>
                    </div>
                </div>
            </div>

            <h2>Interbank vs Open Market — What Is the Difference?</h2>
            <ul>
                <li><strong>Interbank rate:</strong> The price of currency between banks. The State Bank of Pakistan (SBP) publishes it daily.</li>
                <li><strong>Open market rate:</strong> The retail rate of exchange companies and forex dealers. It is usually a little higher than interbank because it includes the dealer's margin.</li>
                <li><strong>Difference (spread):</strong> The smaller the gap between the two rates, the more stable the market is considered.</li>
            </ul>

            <h2>Official Sources — Where to Check the Rate</h2>
            <ol>
                <li><strong>State Bank of Pakistan</strong> — daily interbank rates are published on sbp.org.pk. Open the website and look at the exchange rates section.</li>
                <li><strong>Forex Association of Pakistan (FAP)</strong> — both open market and interbank closing rates are on fap.org.pk.</li>
                <li><strong>Your bank or exchange company</strong> — confirm the counter rate before buying or selling; rates can differ slightly between dealers.</li>
            </ol>
            <ul>
                <li><a href="https://www.sbp.org.pk" target="_blank" rel="noopener">sbp.org.pk</a> — State Bank of Pakistan (official interbank rates)</li>
                <li><a href="https://www.fap.org.pk" target="_blank" rel="noopener">fap.org.pk</a> — Forex Association of Pakistan (official market rates)</li>
            </ul>

            <h2>Keep These in Mind When Checking Rates</h2>
            <ol>
                <li>Always check the <strong>date</strong> — an old news rate is not today's rate.</li>
                <li>Buying and selling rates are different; look at the rate for your side.</li>
                <li>Do not trust social media screenshots — only use official sources.</li>
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
    var spreadTable = document.getElementById('spreadTable');
    var spreadNote = document.getElementById('spreadNote');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function row(label, value) {
        var tr = document.createElement('tr');
        var th = document.createElement('th');
        th.textContent = label;
        th.scope = 'row';
        var td = document.createElement('td');
        td.textContent = value;
        tr.appendChild(th);
        tr.appendChild(td);
        return tr;
    }
    function fmt(n, d) {
        return Number(n).toLocaleString('en-PK', { minimumFractionDigits: d, maximumFractionDigits: d });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var ib = parseFloat(document.getElementById('interbankRate').value);
        var om = parseFloat(document.getElementById('openRate').value);
        var amt = parseFloat(document.getElementById('amountPkr').value);
        if (!(ib > 0) || !(om > 0)) {
            showError('Please enter both rates.');
            return;
        }
        var diff = om - ib;
        var spreadPct = (diff / ib) * 100;
        spreadTable.innerHTML = '';
        spreadTable.appendChild(row('Interbank Rate', 'Rs ' + fmt(ib, 2)));
        spreadTable.appendChild(row('Open Market Rate', 'Rs ' + fmt(om, 2)));
        spreadTable.appendChild(row('Difference (Open - Interbank)', 'Rs ' + fmt(Math.abs(diff), 2) + (diff >= 0 ? ' more' : ' less')));
        spreadTable.appendChild(row('Spread', fmt(spreadPct, 2) + '%'));
        if (amt > 0) {
            spreadTable.appendChild(row('USD @ Interbank (Rs ' + fmt(amt, 0) + ')', '$' + fmt(amt / ib, 2)));
            spreadTable.appendChild(row('USD @ Open Market (Rs ' + fmt(amt, 0) + ')', '$' + fmt(amt / om, 2)));
        }
        spreadNote.textContent = 'This calculation uses the rates you entered. Rates can change — confirm on the official website.';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
