@extends('layouts.app')

@section('title', 'Gold Rate Today Guide - Azlaan Tools')
@section('meta_description', 'Official ways to check today gold rate and the tola gram masha calculation — free online guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Gold Rate Today Guide</h1>
            <p class="lead text-muted">Learn the <strong>official ways</strong> to check today's gold rate, and understand the tola, gram, masha calculation. <span class="text-danger">This page does NOT show a live rate</span> — get today's rate from the official sources below and calculate here.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Gold Rate Calculator</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="rateTola" class="form-label fw-semibold">Today's rate — per tola (PKR)</label>
                            <input type="number" class="form-control" id="rateTola" placeholder="e.g. 285000" min="1">
                            <div class="form-text">Check the 24K per-tola rate from an official source and enter it.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="karatSel" class="form-label fw-semibold">Gold purity</label>
                            <select class="form-select" id="karatSel">
                                <option value="1">24 Karat (99.9% pure)</option>
                                <option value="0.916">22 Karat (91.6%)</option>
                                <option value="0.875">21 Karat (87.5%)</option>
                                <option value="0.75">18 Karat (75%)</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="calcBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4">
                        <table class="table table-bordered table-striped mb-0">
                            <tbody id="rateTable"></tbody>
                        </table>
                        <div class="form-text mt-2">This is a jeweller's estimated rate — making charges and shop profit are not included. Rates can change — confirm on the official website.</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Weight Converter (Tola / Gram / Masha)</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="convVal" class="form-label fw-semibold">Weight</label>
                            <input type="number" class="form-control" id="convVal" placeholder="e.g. 2" min="0" step="any">
                        </div>
                        <div class="col-md-4">
                            <label for="convFrom" class="form-label fw-semibold">From unit</label>
                            <select class="form-select" id="convFrom">
                                <option value="11.6638">Tola</option>
                                <option value="1">Gram</option>
                                <option value="0.972">Masha</option>
                                <option value="116.638">10 Tola</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="convTo" class="form-label fw-semibold">To unit</label>
                            <select class="form-select" id="convTo">
                                <option value="1">Gram</option>
                                <option value="11.6638">Tola</option>
                                <option value="0.972">Masha</option>
                                <option value="116.638">10 Tola</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary w-100 mt-3" id="convBtn">Convert</button>
                    <div class="alert alert-success mt-3 d-none" id="convResult" role="status"></div>
                </div>
            </div>

            <h2>Where to check today's rate (official ways)</h2>
            <ol>
                <li><strong>Pakistan Mercantile Exchange (PMEX):</strong> <a href="https://www.pmex.com.pk" target="_blank" rel="noopener">pmex.com.pk</a> — see daily gold futures rates there; it is Pakistan's official commodity exchange.</li>
                <li><strong>State Bank of Pakistan:</strong> official economy-related information on <a href="https://www.sbp.org.pk" target="_blank" rel="noopener">sbp.org.pk</a>.</li>
                <li><strong>Local sarafa market:</strong> call the sarafa association or well-known jewellers in Karachi, Lahore or your own city to ask today's rate — compare the rate at 2-3 shops before buying.</li>
                <li><strong>Business news:</strong> TV business bulletins report the daily gold rate.</li>
            </ol>

            <h2>Remember the weights</h2>
            <ul>
                <li>1 Tola = <strong>11.6638 gram</strong> = 12 masha</li>
                <li>1 Masha = <strong>0.972 gram</strong></li>
                <li>10 Tola = 116.638 gram</li>
            </ul>

            <h2>Tips when buying gold</h2>
            <ul>
                <li>Always take a <strong>receipt</strong> that shows the weight (in grams), karat and rate.</li>
                <li>Ask <strong>making charges</strong> separately — they differ from shop to shop.</li>
                <li>When selling old gold, work out the weight and today's rate yourself before going.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var rateTola = document.getElementById('rateTola');
    var karatSel = document.getElementById('karatSel');
    var calcBtn = document.getElementById('calcBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var rateTable = document.getElementById('rateTable');
    var convVal = document.getElementById('convVal');
    var convFrom = document.getElementById('convFrom');
    var convTo = document.getElementById('convTo');
    var convBtn = document.getElementById('convBtn');
    var convResult = document.getElementById('convResult');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pkr(n) {
        return 'Rs ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    function row(label, value) {
        return '<tr><td class="fw-semibold">' + label + '</td><td class="text-end">' + value + '</td></tr>';
    }

    calcBtn.addEventListener('click', function () {
        hideError();
        var rate = parseFloat(rateTola.value);
        if (isNaN(rate) || rate <= 0) { showError('Please enter a valid per-tola rate.'); return; }
        var purity = parseFloat(karatSel.value);
        var karatName = karatSel.options[karatSel.selectedIndex].text;
        var perTola = rate * purity;
        var perGram = perTola / 11.6638;
        var per10Gram = perGram * 10;
        var perMasha = perGram * 0.972;
        var html = '';
        html += row('Per tola (' + karatName + ')', pkr(perTola));
        html += row('Per 10 gram', pkr(per10Gram));
        html += row('Per gram', pkr(perGram));
        html += row('Per masha', pkr(perMasha));
        html += row('24K per tola (pure)', pkr(rate));
        rateTable.innerHTML = html;
        results.classList.remove('d-none');
    });

    convBtn.addEventListener('click', function () {
        var v = parseFloat(convVal.value);
        if (isNaN(v) || v < 0) {
            convResult.className = 'alert alert-danger mt-3';
            convResult.textContent = 'Please enter a valid weight.';
            return;
        }
        var fromName = convFrom.options[convFrom.selectedIndex].text;
        var toName = convTo.options[convTo.selectedIndex].text;
        var grams = v * parseFloat(convFrom.value);
        var out = grams / parseFloat(convTo.value);
        var rounded = Math.round(out * 10000) / 10000;
        convResult.className = 'alert alert-success mt-3';
        convResult.textContent = v + ' ' + fromName + ' = ' + rounded + ' ' + toName;
    });
})();
</script>
@endsection
