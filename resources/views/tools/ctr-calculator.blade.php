@extends('layouts.app')

@section('title', 'CTR Calculator - Azlaan Tools')
@section('meta_description', 'Calculate click-through rate from impressions and clicks for free. For ads, email and SEO.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">CTR Calculator</h1>
            <p class="lead text-muted">Find your <strong>Click-Through Rate (CTR)</strong> in percent from impressions and clicks — for ads, email campaigns or SEO.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Calculate CTR</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="impInput" class="form-label fw-semibold">Impressions</label>
                            <input type="number" class="form-control" id="impInput" placeholder="e.g. 10000" min="0">
                            <div class="form-text">How many people saw it</div>
                        </div>
                        <div class="col-md-4">
                            <label for="clickInput" class="form-label fw-semibold">Clicks</label>
                            <input type="number" class="form-control" id="clickInput" placeholder="e.g. 350" min="0">
                            <div class="form-text">How many people clicked</div>
                        </div>
                        <div class="col-md-4">
                            <label for="channelSel" class="form-label fw-semibold">Channel (benchmark)</label>
                            <select class="form-select" id="channelSel">
                                <option value="search">Google Search Ads</option>
                                <option value="display">Display Ads</option>
                                <option value="social">Social Media Ads</option>
                                <option value="email">Email Campaign</option>
                                <option value="seo">SEO / Organic</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate CTR</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center p-4 bg-light rounded">
                            <div class="display-4 fw-bold text-primary" id="ctrOut">0%</div>
                            <div class="text-muted">Click-Through Rate</div>
                            <div class="mt-2 fw-semibold" id="verdictOut"></div>
                            <div class="progress mt-3" style="height: 12px;">
                                <div class="progress-bar" id="ctrBar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="form-text mt-2">Formula: (Clicks / Impressions) x 100</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Reverse: how many clicks do you need?</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tImp" class="form-label fw-semibold">Impressions</label>
                            <input type="number" class="form-control" id="tImp" placeholder="e.g. 50000" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="tCtr" class="form-label fw-semibold">Target CTR (%)</label>
                            <input type="number" class="form-control" id="tCtr" placeholder="e.g. 2" min="0" step="any">
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary w-100 mt-3" id="needClicksBtn">Clicks Needed</button>
                    <div class="alert alert-success mt-3 d-none" id="needClicksOut" role="status"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Reverse: How many impressions do you need?</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tClicks" class="form-label fw-semibold">Clicks (target)</label>
                            <input type="number" class="form-control" id="tClicks" placeholder="e.g. 1000" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="tCtr2" class="form-label fw-semibold">Expected CTR (%)</label>
                            <input type="number" class="form-control" id="tCtr2" placeholder="e.g. 1.5" min="0" step="any">
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary w-100 mt-3" id="needImpBtn">Impressions Needed</button>
                    <div class="alert alert-success mt-3 d-none" id="needImpOut" role="status"></div>
                </div>
            </div>

            <h2>Typical CTR Benchmarks</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light"><tr><th>Channel</th><th>Typical CTR Range</th></tr></thead>
                    <tbody>
                        <tr><td>Google Search Ads</td><td>6% - 7%</td></tr>
                        <tr><td>Display Ads</td><td>0.4% - 0.6%</td></tr>
                        <tr><td>Social Media Ads</td><td>0.8% - 1.2%</td></tr>
                        <tr><td>Email Campaign</td><td>2% - 3%</td></tr>
                        <tr><td>SEO / Organic Results</td><td>1% - 2% (depends on position)</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="text-muted small">These are typical industry ranges — they can vary based on your business, audience and creative.</p>

            <h2>How to use</h2>
            <ol>
                <li>Enter impressions and clicks, then select a channel.</li>
                <li>Press <strong>Calculate CTR</strong> — you will also get a benchmark comparison with the result.</li>
                <li>Use the reverse calculators for target planning.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var impInput = document.getElementById('impInput');
    var clickInput = document.getElementById('clickInput');
    var channelSel = document.getElementById('channelSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var ctrOut = document.getElementById('ctrOut');
    var verdictOut = document.getElementById('verdictOut');
    var ctrBar = document.getElementById('ctrBar');
    var tImp = document.getElementById('tImp');
    var tCtr = document.getElementById('tCtr');
    var needClicksBtn = document.getElementById('needClicksBtn');
    var needClicksOut = document.getElementById('needClicksOut');
    var tClicks = document.getElementById('tClicks');
    var tCtr2 = document.getElementById('tCtr2');
    var needImpBtn = document.getElementById('needImpBtn');
    var needImpOut = document.getElementById('needImpOut');

    var benchmarks = {
        search: { label: 'Google Search Ads', low: 6, high: 7 },
        display: { label: 'Display Ads', low: 0.4, high: 0.6 },
        social: { label: 'Social Media Ads', low: 0.8, high: 1.2 },
        email: { label: 'Email Campaign', low: 2, high: 3 },
        seo: { label: 'SEO / Organic', low: 1, high: 2 }
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
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var imp = parseFloat(impInput.value);
        var clk = parseFloat(clickInput.value);
        if (isNaN(imp) || isNaN(clk) || imp <= 0 || clk < 0) {
            showError('Enter valid impressions (more than 0) and clicks.');
            return;
        }
        if (clk > imp) { showError('Clicks cannot be more than impressions.'); return; }
        var ctr = (clk / imp) * 100;
        var ctrStr = (Math.round(ctr * 100) / 100) + '%';
        ctrOut.textContent = ctrStr;

        var b = benchmarks[channelSel.value];
        var verdict, color;
        if (ctr < b.low) { verdict = 'Your CTR is BELOW the benchmark (' + b.label + ' typical: ' + b.low + '%-' + b.high + '%) — improve your ad copy or targeting.'; color = '#dc3545'; }
        else if (ctr > b.high) { verdict = 'Your CTR is ABOVE the benchmark (' + b.label + ' typical: ' + b.low + '%-' + b.high + '%) — great work!'; color = '#198754'; }
        else { verdict = 'Your CTR is in the benchmark range (' + b.label + ' typical: ' + b.low + '%-' + b.high + '%) — you are on track.'; color = '#0d6efd'; }
        verdictOut.textContent = verdict;
        verdictOut.style.color = color;

        var barW = Math.min(100, (ctr / (b.high * 2)) * 100);
        ctrBar.style.width = Math.max(2, barW) + '%';
        ctrBar.style.backgroundColor = color;
        results.classList.remove('d-none');
    });

    needClicksBtn.addEventListener('click', function () {
        var imp = parseFloat(tImp.value);
        var target = parseFloat(tCtr.value);
        if (isNaN(imp) || isNaN(target) || imp <= 0 || target <= 0 || target > 100) {
            needClicksOut.className = 'alert alert-danger mt-3';
            needClicksOut.textContent = 'Enter valid impressions and target CTR (1-100).';
            return;
        }
        var needed = Math.ceil((target / 100) * imp);
        needClicksOut.className = 'alert alert-success mt-3';
        needClicksOut.textContent = fmt(imp) + ' impressions: for ' + target + '% CTR you need ' + fmt(needed) + ' clicks.';
    });

    needImpBtn.addEventListener('click', function () {
        var clk = parseFloat(tClicks.value);
        var target = parseFloat(tCtr2.value);
        if (isNaN(clk) || isNaN(target) || clk <= 0 || target <= 0 || target > 100) {
            needImpOut.className = 'alert alert-danger mt-3';
            needImpOut.textContent = 'Enter valid clicks and expected CTR (1-100).';
            return;
        }
        var needed = Math.ceil(clk / (target / 100));
        needImpOut.className = 'alert alert-success mt-3';
        needImpOut.textContent = fmt(clk) + ' clicks: at ' + target + '% CTR you need ' + fmt(needed) + ' impressions.';
    });
})();
</script>
@endsection
