@extends('layouts.app')

@section('title', 'CPC Calculator - Azlaan Tools')
@section('meta_description', 'Find cost per click (CPC) from ad spend and clicks, and plan your budget. Free online calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">CPC Calculator</h1>
            <p class="lead text-muted">Find your cost per click (CPC) from ad spend and clicks — make budget planning for Google Ads, Facebook or TikTok ads easy.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="cpcTabs">
                        <li class="nav-item"><button type="button" class="nav-link active" data-tab="cpc">Find CPC</button></li>
                        <li class="nav-item"><button type="button" class="nav-link" data-tab="budget">Budget planner</button></li>
                        <li class="nav-item"><button type="button" class="nav-link" data-tab="target">Target CPC</button></li>
                    </ul>

                    <div id="tab-cpc">
                        <div class="mb-3">
                            <label for="spend" class="form-label fw-semibold">Total ad spend (Rs)</label>
                            <input type="number" class="form-control" id="spend" min="0" placeholder="e.g. 5000">
                        </div>
                        <div class="mb-3">
                            <label for="clicks" class="form-label fw-semibold">Total clicks</label>
                            <input type="number" class="form-control" id="clicks" min="1" placeholder="e.g. 400">
                        </div>
                    </div>

                    <div id="tab-budget" class="d-none">
                        <div class="mb-3">
                            <label for="bCpc" class="form-label fw-semibold">Average CPC (Rs per click)</label>
                            <input type="number" class="form-control" id="bCpc" min="0.01" step="0.01" placeholder="e.g. 12.5">
                        </div>
                        <div class="mb-3">
                            <label for="bClicks" class="form-label fw-semibold">How many clicks do you need?</label>
                            <input type="number" class="form-control" id="bClicks" min="1" placeholder="e.g. 1000">
                        </div>
                    </div>

                    <div id="tab-target" class="d-none">
                        <div class="mb-3">
                            <label for="tConv" class="form-label fw-semibold">Conversion rate (%)</label>
                            <input type="number" class="form-control" id="tConv" min="0.01" step="0.01" placeholder="e.g. 2">
                            <div class="form-text">Out of 100 clicks, how many people buy? (e.g. 2%)</div>
                        </div>
                        <div class="mb-3">
                            <label for="tCpa" class="form-label fw-semibold">Target cost per sale/order (Rs)</label>
                            <input type="number" class="form-control" id="tCpa" min="1" placeholder="e.g. 800">
                            <div class="form-text">How much ad cost can you afford per sale?</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-light border" id="resultBox"></div>
                        <p class="small text-muted mb-0">This is an estimate — real CPC depends on the ad platform, competition and targeting. Rates can change.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a tab: Find CPC, budget planner, or target CPC.</li>
                <li>Enter the values and press Calculate.</li>
                <li>Decide your ad budget from the result.</li>
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
    var resultBox = document.getElementById('resultBox');
    var activeTab = 'cpc';

    document.querySelectorAll('#cpcTabs .nav-link').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#cpcTabs .nav-link').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            activeTab = btn.getAttribute('data-tab');
            var tabIds = { cpc: 'tab-cpc', budget: 'tab-budget', target: 'tab-target' };
            Object.keys(tabIds).forEach(function (t) {
                document.getElementById(tabIds[t]).classList.toggle('d-none', t !== activeTab);
            });
            errorBox.classList.add('d-none');
            results.classList.add('d-none');
        });
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function money(n) { return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2 }); }

    goBtn.addEventListener('click', function () {
        hideError();
        var html = '';
        if (activeTab === 'cpc') {
            var spend = parseFloat(document.getElementById('spend').value);
            var clicks = parseFloat(document.getElementById('clicks').value);
            if (isNaN(spend) || spend <= 0) { showError('Enter a valid ad spend (more than 0).'); return; }
            if (isNaN(clicks) || clicks <= 0) { showError('Enter a valid number of clicks.'); return; }
            var cpc = spend / clicks;
            html = '<div class="h4 mb-1">Your CPC: <span class="text-primary">' + money(cpc) + '</span> per click</div>' +
                '<div class="small">Formula: ' + money(spend) + ' &divide; ' + clicks.toLocaleString() + ' clicks</div>' +
                '<div class="small mt-2 text-muted">1,000 clicks at this rate will cost: <strong>' + money(cpc * 1000) + '</strong></div>';
        } else if (activeTab === 'budget') {
            var bcpc = parseFloat(document.getElementById('bCpc').value);
            var bclicks = parseFloat(document.getElementById('bClicks').value);
            if (isNaN(bcpc) || bcpc <= 0) { showError('Enter a valid average CPC.'); return; }
            if (isNaN(bclicks) || bclicks <= 0) { showError('Enter how many clicks you need.'); return; }
            var budget = bcpc * bclicks;
            html = '<div class="h4 mb-1">Budget needed: <span class="text-primary">' + money(budget) + '</span></div>' +
                '<div class="small">Formula: ' + money(bcpc) + ' &times; ' + bclicks.toLocaleString() + ' clicks</div>' +
                '<div class="small mt-2 text-muted">Daily budget (over 30 days): <strong>' + money(budget / 30) + '/day</strong></div>';
        } else {
            var conv = parseFloat(document.getElementById('tConv').value);
            var cpa = parseFloat(document.getElementById('tCpa').value);
            if (isNaN(conv) || conv <= 0 || conv > 100) { showError('Enter the conversion rate between 0 and 100.'); return; }
            if (isNaN(cpa) || cpa <= 0) { showError('Enter a valid target cost per sale.'); return; }
            var targetCpc = cpa * (conv / 100);
            html = '<div class="h4 mb-1">Your target CPC: <span class="text-primary">' + money(targetCpc) + '</span> or less</div>' +
                '<div class="small">Formula: ' + money(cpa) + ' &times; ' + conv + '% conversion</div>' +
                '<div class="small mt-2 text-muted">If your real CPC is higher than this, either improve the conversion rate (landing page, offer) or tighten your ad targeting.</div>';
        }
        resultBox.innerHTML = html;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
