@extends('layouts.app')

@section('title', 'Marketing ROI Calculator - Azlaan Tools')
@section('meta_description', 'Calculate your campaign ROI percent from ad spend and revenue, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Marketing ROI Calculator</h1>
            <p class="lead text-muted">Enter your ad campaign spend and revenue — get ROI %, ROAS and net profit instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="adSpend" class="form-label fw-semibold">Ad Spend (Rs)</label>
                            <input type="number" class="form-control" id="adSpend" min="0" step="1" placeholder="e.g. 50000">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="adRevenue" class="form-label fw-semibold">Revenue from Ads (Rs)</label>
                            <input type="number" class="form-control" id="adRevenue" min="0" step="1" placeholder="e.g. 180000">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="convInput" class="form-label fw-semibold">Conversions (optional)</label>
                            <input type="number" class="form-control" id="convInput" min="0" step="1" placeholder="e.g. 120">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="otherCost" class="form-label fw-semibold">Other Campaign Costs (Rs, optional)</label>
                            <input type="number" class="form-control" id="otherCost" min="0" step="1" placeholder="e.g. 5000">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate ROI</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-2 text-center mb-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">ROI</div>
                                    <div class="fw-bold fs-5" id="roiOut">-</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">ROAS</div>
                                    <div class="fw-bold fs-5" id="roasOut">-</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">Net Profit</div>
                                    <div class="fw-bold fs-5" id="profitOut">-</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">Total Cost</div>
                                    <div class="fw-bold fs-5" id="costOut">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr><td>Break-even revenue (0% ROI)</td><td id="beOut" class="fw-semibold">-</td></tr>
                                    <tr><td>Profit per rupee spent</td><td id="pprOut" class="fw-semibold">-</td></tr>
                                    <tr id="cpcRow" class="d-none"><td>Cost per conversion</td><td id="cpcOut" class="fw-semibold">-</td></tr>
                                    <tr id="rpcRow" class="d-none"><td>Revenue per conversion</td><td id="rpcOut" class="fw-semibold">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert" id="verdictBox"></div>
                        <small class="text-muted d-block">This is an estimate, not a guarantee. Actual results depend on the platform, audience and market.</small>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the ad spend and the revenue from that campaign.</li>
                <li>You can also add conversions and extra costs if you want.</li>
                <li>Click Calculate ROI — see ROI %, ROAS and profit.</li>
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
    var adSpend = document.getElementById('adSpend');
    var adRevenue = document.getElementById('adRevenue');
    var convInput = document.getElementById('convInput');
    var otherCost = document.getElementById('otherCost');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var roiOut = document.getElementById('roiOut');
    var roasOut = document.getElementById('roasOut');
    var profitOut = document.getElementById('profitOut');
    var costOut = document.getElementById('costOut');
    var beOut = document.getElementById('beOut');
    var pprOut = document.getElementById('pprOut');
    var cpcRow = document.getElementById('cpcRow');
    var rpcRow = document.getElementById('rpcRow');
    var cpcOut = document.getElementById('cpcOut');
    var rpcOut = document.getElementById('rpcOut');
    var verdictBox = document.getElementById('verdictBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }

    goBtn.addEventListener('click', function () {
        hideError();
        var spend = parseFloat(adSpend.value);
        var rev = parseFloat(adRevenue.value);
        var conv = parseFloat(convInput.value);
        var other = parseFloat(otherCost.value) || 0;
        if (isNaN(spend) || spend < 0) { showError('Please enter the ad spend amount.'); return; }
        if (isNaN(rev) || rev < 0) { showError('Please enter the revenue amount.'); return; }
        if (spend === 0) { showError('Ad spend must be more than 0.'); return; }

        var totalCost = spend + other;
        var profit = rev - totalCost;
        var roi = (profit / totalCost) * 100;
        var roas = rev / totalCost;

        roiOut.textContent = roi.toFixed(1) + '%';
        roiOut.className = 'fw-bold fs-5 ' + (roi >= 0 ? 'text-success' : 'text-danger');
        roasOut.textContent = roas.toFixed(2) + 'x';
        roasOut.className = 'fw-bold fs-5 ' + (roas >= 1 ? 'text-success' : 'text-danger');
        profitOut.textContent = fmt(profit);
        profitOut.className = 'fw-bold fs-5 ' + (profit >= 0 ? 'text-success' : 'text-danger');
        costOut.textContent = fmt(totalCost);
        beOut.textContent = fmt(totalCost);
        pprOut.textContent = 'Rs ' + (profit / totalCost).toFixed(2);

        if (!isNaN(conv) && conv > 0) {
            cpcRow.classList.remove('d-none');
            rpcRow.classList.remove('d-none');
            cpcOut.textContent = fmt(totalCost / conv);
            rpcOut.textContent = fmt(rev / conv);
        } else {
            cpcRow.classList.add('d-none');
            rpcRow.classList.add('d-none');
        }

        verdictBox.className = 'alert';
        if (roi >= 100) {
            verdictBox.classList.add('alert-success');
            verdictBox.innerHTML = '<strong>Excellent!</strong> The campaign is very profitable (' + roi.toFixed(1) + '% ROI). You can think about increasing the budget.';
        } else if (roi >= 0) {
            verdictBox.classList.add('alert-info');
            verdictBox.innerHTML = '<strong>Profitable.</strong> The campaign is in profit (' + roi.toFixed(1) + '% ROI). Improve targeting to raise ROI.';
        } else {
            verdictBox.classList.add('alert-warning');
            verdictBox.innerHTML = '<strong>Loss.</strong> The campaign is at a loss of ' + fmt(-profit) + '. Review your ad creative, targeting or offer again.';
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
