@extends('layouts.app')

@section('title', 'Currency Converter — USD to PKR & More — Azlaan Tools')
@section('meta_description', 'Free currency converter. Convert PKR, USD, EUR, GBP, AED, SAR, INR, CNY and TRY with live rates and offline fallback rates.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">Currency Converter</h1>
            <p class="text-muted mb-4">Convert instantly between PKR, dollar, pound, dirham and other currencies — with live rates, and offline rates when there is no internet.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="alert alert-info py-2 small" id="rateStatus">Loading rates…</div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control form-control-lg" id="amount" value="1" step="any">
                    </div>
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-5">
                            <label for="fromCur" class="form-label">From</label>
                            <select class="form-select" id="fromCur"></select>
                        </div>
                        <div class="col-2 text-center">
                            <button type="button" class="btn btn-outline-secondary w-100" id="swapBtn" title="Swap currencies">&#8646;</button>
                        </div>
                        <div class="col-5">
                            <label for="toCur" class="form-label">To</label>
                            <select class="form-select" id="toCur"></select>
                        </div>
                    </div>
                    <div class="text-center border rounded p-3 bg-light">
                        <div class="fs-3 fw-bold" id="resultOut">—</div>
                        <div class="small text-muted" id="rateOut"></div>
                        <div class="small text-muted" id="updatedOut"></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Rates are for information only. Your bank / exchange shop's actual buying/selling rate may differ.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the amount.</li>
                        <li>Choose the "From" and "To" currencies — the middle button swaps both.</li>
                        <li>The converted amount shows instantly, with its rate and last updated time.</li>
                        <li>If live rates do not load, offline (approximate) rates are used — clearly shown on screen.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var currencies = ['PKR', 'USD', 'EUR', 'GBP', 'AED', 'SAR', 'INR', 'CNY', 'TRY'];
// Static fallback rates: units per 1 USD. Labelled offline / approximate in the UI when used.
var fallbackRates = { USD: 1, PKR: 278.50, EUR: 0.92, GBP: 0.79, AED: 3.6725, SAR: 3.75, INR: 83.50, CNY: 7.25, TRY: 34.50 };
var rates = null;
var ratesSource = '';
var lastUpdated = '';

var fromSel = document.getElementById('fromCur');
var toSel = document.getElementById('toCur');
currencies.forEach(function(c){
    fromSel.innerHTML += '<option value="' + c + '">' + c + '</option>';
    toSel.innerHTML += '<option value="' + c + '">' + c + '</option>';
});
fromSel.value = 'USD';
toSel.value = 'PKR';

function convert() {
    if (!rates) return;
    var amount = parseFloat(document.getElementById('amount').value) || 0;
    var from = fromSel.value, to = toSel.value;
    if (!rates[from] || !rates[to]) return;
    var inUsd = amount / rates[from];
    var result = inUsd * rates[to];
    document.getElementById('resultOut').textContent =
        amount.toLocaleString('en-PK', { maximumFractionDigits: 2 }) + ' ' + from + ' = ' +
        result.toLocaleString('en-PK', { maximumFractionDigits: 2 }) + ' ' + to;
    var unitRate = rates[to] / rates[from];
    document.getElementById('rateOut').textContent = '1 ' + from + ' = ' + unitRate.toLocaleString('en-PK', { maximumFractionDigits: 4 }) + ' ' + to;
    document.getElementById('updatedOut').textContent = lastUpdated ? ('Last updated: ' + lastUpdated + ' (' + ratesSource + ')') : ratesSource;
}
function useFallback(message) {
    rates = fallbackRates;
    ratesSource = 'offline rates (approximate)';
    lastUpdated = '';
    var el = document.getElementById('rateStatus');
    el.className = 'alert alert-warning py-2 small';
    el.textContent = message + ' Offline rates (approximate) are being used.';
    convert();
}
function loadRates() {
    fetch('https://open.er-api.com/v6/latest/USD')
        .then(function(res){ if (!res.ok) throw new Error('bad response'); return res.json(); })
        .then(function(data){
            if (!data || data.result !== 'success' || !data.rates) throw new Error('bad data');
            rates = data.rates;
            ratesSource = 'live rates';
            lastUpdated = data.time_last_update_utc || '';
            var el = document.getElementById('rateStatus');
            el.className = 'alert alert-success py-2 small';
            el.textContent = 'Live rates loaded.' + (lastUpdated ? ' Last updated: ' + lastUpdated : '');
            convert();
        })
        .catch(function(){ useFallback('Live rates could not be loaded.'); });
}
document.getElementById('amount').addEventListener('input', convert);
fromSel.addEventListener('change', convert);
toSel.addEventListener('change', convert);
document.getElementById('swapBtn').addEventListener('click', function(){
    var t = fromSel.value; fromSel.value = toSel.value; toSel.value = t; convert();
});
loadRates();
</script>
@endsection
