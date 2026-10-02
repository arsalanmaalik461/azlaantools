@extends('layouts.app')

@section('title', 'Prize Bond Result Check Guide - Azlaan Tools')
@section('meta_description', 'Learn how to check Pakistan prize bond draw results on the official National Savings website and how to claim prize money. Free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Prize Bond Result Check Guide</h1>
            <p class="lead text-muted">The right way to check your prize bond draw result on the official National Savings list, and the claim process if you win a prize.</p>

            <div class="alert alert-info mb-4">
                <strong>Note:</strong> This tool does NOT verify live results. Always check results on the official website <a href="https://www.savings.gov.pk" target="_blank" rel="noopener">savings.gov.pk</a>.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Bond Number Format Check</h5>
                    <p class="text-muted small">Enter your bond number to check its format (format check only, not the result).</p>
                    <div class="mb-3">
                        <label for="bondDenom" class="form-label fw-semibold">Bond denomination</label>
                        <select class="form-select" id="bondDenom">
                            <option value="100">Rs. 100</option>
                            <option value="200">Rs. 200</option>
                            <option value="750">Rs. 750</option>
                            <option value="1500">Rs. 1500</option>
                            <option value="7500">Rs. 7500</option>
                            <option value="15000">Rs. 15000</option>
                            <option value="25000">Rs. 25000</option>
                            <option value="40000">Rs. 40000</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bondNo" class="form-label fw-semibold">Bond number</label>
                        <input type="text" class="form-control" id="bondNo" placeholder="e.g. 482913" inputmode="numeric">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Check Format</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div id="formatOut"></div>
                    </div>
                </div>
            </div>

            <h2>Steps to check the result</h2>
            <ol>
                <li>Open the official website <a href="https://www.savings.gov.pk" target="_blank" rel="noopener">savings.gov.pk</a>.</li>
                <li>Go to the "Prize Bond Draw Results" / "Draws" section.</li>
                <li>Select your bond denomination (Rs. 100, 200, 750 etc.) and the draw date.</li>
                <li>Download the official draw list (PDF) or find your 6-digit bond number in the online search.</li>
                <li>If your number matches, note the list date and draw number — you will need them for the claim.</li>
            </ol>

            <h2>Prize claim process</h2>
            <ol>
                <li>Take the winning original bond and your CNIC to any National Savings Centre or a designated State Bank of Pakistan branch.</li>
                <li>Fill in the claim form there and submit the bond.</li>
                <li>Payment is made after deducting the applicable tax on the prize money (tax rates can change over time — confirm on the official website).</li>
                <li>Claim the prize within the claim period; old lists are available in the official website archive.</li>
            </ol>

            <h2>Important official links</h2>
            <ul>
                <li><a href="https://www.savings.gov.pk" target="_blank" rel="noopener">National Savings — savings.gov.pk</a> (draw results and schedule)</li>
                <li><a href="https://www.sbp.org.pk" target="_blank" rel="noopener">State Bank of Pakistan — sbp.org.pk</a></li>
            </ul>
            <p class="text-muted small">Draw schedule and tax rates can change over time — confirm on the official website.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var bondDenom = document.getElementById('bondDenom');
    var bondNo = document.getElementById('bondNo');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var formatOut = document.getElementById('formatOut');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var denom = bondDenom.value;
        var num = bondNo.value.trim().replace(/[\s-]/g, '');
        if (!num) { showError('Enter the bond number first.'); return; }
        if (!/^\d+$/.test(num)) {
            formatOut.innerHTML = '<div class="alert alert-warning mb-0"><strong>Wrong format:</strong> the bond number should have only digits, no letters or symbols.</div>';
            results.classList.remove('d-none');
            return;
        }
        var html;
        if (num.length === 6) {
            html = '<div class="alert alert-success mb-0"><strong>Correct format:</strong> Rs. ' + denom + ' bond number <strong>' + num + '</strong> has 6 digits — the format looks right. Now find this number in the official list (<a href="https://www.savings.gov.pk" target="_blank" rel="noopener">savings.gov.pk</a>) to confirm the result.</div>';
        } else {
            html = '<div class="alert alert-warning mb-0"><strong>Format check:</strong> you entered ' + num.length + ' digits. Prize bond numbers are usually 6 digits — check the number again.</div>';
        }
        formatOut.innerHTML = html;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
