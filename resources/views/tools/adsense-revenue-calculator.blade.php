@extends('layouts.app')

@section('title', 'AdSense Revenue Calculator - Azlaan Tools')
@section('meta_description', 'Estimate AdSense earnings from pageviews and CPC. Free online AdSense revenue calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">AdSense Revenue Calculator</h1>
            <p class="lead text-muted">Estimate your AdSense earnings from monthly pageviews and CPC — to plan your monetization.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pageviews" class="form-label fw-semibold">Monthly pageviews</label>
                        <input type="number" class="form-control" id="pageviews" placeholder="Example: 100000" min="0">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cpc" class="form-label fw-semibold">Average CPC (USD $)</label>
                            <input type="number" class="form-control" id="cpc" placeholder="Example: 0.25" min="0" step="0.01">
                            <div class="form-text">The average amount earned per click (in dollars).</div>
                        </div>
                        <div class="col-md-6">
                            <label for="ctr" class="form-label fw-semibold">Page CTR (%)</label>
                            <input type="number" class="form-control" id="ctr" placeholder="Example: 1.5" min="0" max="100" step="0.1">
                            <div class="form-text">What % of pageviews get a click (usually 0.5-3%).</div>
                        </div>
                    </div>
                    <div class="mt-3 mb-3">
                        <span class="small fw-semibold text-muted me-2">Ready estimates:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary preset" data-pv="10000" data-cpc="0.15" data-ctr="1">Small blog</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary preset" data-pv="100000" data-cpc="0.25" data-ctr="1.5">Medium site</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary preset" data-pv="1000000" data-cpc="0.40" data-ctr="2">Big site</button>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Estimate Earnings</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body"><div class="small text-muted">Daily</div><div class="fs-5 fw-bold text-success" id="rDaily">-</div></div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body"><div class="small text-muted">Monthly</div><div class="fs-5 fw-bold text-success" id="rMonthly">-</div></div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body"><div class="small text-muted">Yearly</div><div class="fs-5 fw-bold text-success" id="rYearly">-</div></div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-light"><div class="card-body"><div class="small text-muted">RPM</div><div class="fs-5 fw-bold" id="rRpm">-</div></div></div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 small mb-0" id="explainBox"></div>
                    </div>
                </div>
            </div>

            <p class="text-muted small">Disclaimer: This is only an estimate — real earnings depend a lot on niche, country, ad placement and season. Rates can change — confirm on the official AdSense policies.</p>

            <h2>How to use</h2>
            <ol>
                <li>Enter your website's monthly pageviews.</li>
                <li>Enter average CPC (in dollars) and page CTR % — or press a ready preset.</li>
                <li>Press the button — you will get estimates for daily, monthly, yearly earnings and RPM.</li>
            </ol>
            <p class="text-muted small">Formula: Earning = Pageviews × (CTR ÷ 100) × CPC. RPM = (Earning ÷ Pageviews) × 1000.</p>
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
    function money(x) {
        return '$' + x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    var presets = document.querySelectorAll('.preset');
    for (var i = 0; i < presets.length; i++) {
        presets[i].addEventListener('click', function () {
            document.getElementById('pageviews').value = this.getAttribute('data-pv');
            document.getElementById('cpc').value = this.getAttribute('data-cpc');
            document.getElementById('ctr').value = this.getAttribute('data-ctr');
        });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var pv = parseFloat(document.getElementById('pageviews').value);
        var cpc = parseFloat(document.getElementById('cpc').value);
        var ctr = parseFloat(document.getElementById('ctr').value);

        if (isNaN(pv) || pv <= 0) { showError('Enter monthly pageviews (more than 0).'); return; }
        if (isNaN(cpc) || cpc <= 0) { showError('Enter average CPC (in dollars).'); return; }
        if (isNaN(ctr) || ctr <= 0 || ctr > 100) { showError('Enter CTR between 0 and 100.'); return; }

        var monthly = pv * (ctr / 100) * cpc;
        var daily = monthly / 30;
        var yearly = monthly * 12;
        var rpm = (monthly / pv) * 1000;

        document.getElementById('rDaily').textContent = money(daily);
        document.getElementById('rMonthly').textContent = money(monthly);
        document.getElementById('rYearly').textContent = money(yearly);
        document.getElementById('rRpm').textContent = money(rpm);

        var clicks = Math.round(pv * (ctr / 100));
        document.getElementById('explainBox').textContent =
            'Calculation: ' + pv.toLocaleString('en-US') + ' pageviews x ' + ctr + '% CTR = about ' +
            clicks.toLocaleString('en-US') + ' clicks, x $' + cpc + ' CPC = ' + money(monthly) + ' monthly (estimate).';

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
