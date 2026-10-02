@extends('layouts.app')

@section('title', 'Car Insurance Premium Estimator - Azlaan Tools')
@section('meta_description', 'Estimate yearly car insurance cost in Pakistan. Free online car insurance premium calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Car Insurance Premium Estimator</h1>
            <p class="lead text-muted">Estimate the yearly insurance premium for your car in Pakistan — with comprehensive or third-party coverage.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="carValue" class="form-label fw-semibold">Car Value (PKR)</label>
                        <input type="number" class="form-control" id="carValue" placeholder="e.g. 2500000" min="100000" step="10000">
                        <div class="form-text">The car's current market value (sum insured).</div>
                    </div>
                    <div class="mb-3">
                        <label for="carAge" class="form-label fw-semibold">Car Age (years)</label>
                        <input type="number" class="form-control" id="carAge" placeholder="e.g. 3" min="0" max="25" step="1">
                    </div>
                    <div class="mb-3">
                        <label for="coverage" class="form-label fw-semibold">Coverage Type</label>
                        <select class="form-select" id="coverage">
                            <option value="comp">Comprehensive (full cover)</option>
                            <option value="third">Third Party Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Add-ons</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="addonTracker">
                            <label class="form-check-label" for="addonTracker">Tracker device installed (discount)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="addonNcb">
                            <label class="form-check-label" for="addonNcb">No claim in last year (No-Claim Bonus)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="addonPvt">
                            <label class="form-check-label" for="addonPvt">Political violence / terrorism cover (extra)</label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Estimate Premium</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody id="premiumTable"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small">This is only an estimate — every company's rates are different. Before taking a policy, be sure to get a written quote from the insurance company.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the car's current market value and age.</li>
                <li>Select the coverage type and tick the add-ons.</li>
                <li>Press Estimate Premium — you will get the yearly premium estimate.</li>
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
    var premiumTable = document.getElementById('premiumTable');

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
    function fmt(n) {
        return 'Rs ' + Math.round(n).toLocaleString('en-PK');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var value = parseFloat(document.getElementById('carValue').value);
        var age = parseInt(document.getElementById('carAge').value, 10);
        var coverage = document.getElementById('coverage').value;
        if (!(value >= 100000)) { showError('Please enter a valid car value.'); return; }
        if (!(age >= 0 && age <= 25)) { showError('Please enter a valid car age (0-25).'); return; }

        var base, rate;
        if (coverage === 'third') {
            // Third party: fixed bands roughly by car value
            base = value <= 1500000 ? 12000 : value <= 3000000 ? 18000 : 25000;
            rate = 'fixed band';
        } else {
            // Comprehensive: base rate % of sum insured, rises with age
            rate = 3.0 + age * 0.35; // e.g. 3% new, ~7.4% at 12 years
            base = value * (rate / 100);
        }
        var adj = [];
        var premium = base;
        if (coverage === 'comp') {
            if (document.getElementById('addonTracker').checked) {
                premium *= 0.90;
                adj.push('Tracker discount (-10%)');
            }
            if (document.getElementById('addonNcb').checked) {
                premium *= 0.85;
                adj.push('No-Claim Bonus (-15%)');
            }
            if (document.getElementById('addonPvt').checked) {
                premium += value * 0.0025;
                adj.push('Political violence cover (+0.25% of value)');
            }
        } else {
            adj.push('Third party fixed premium — add-ons do not apply');
        }

        premiumTable.innerHTML = '';
        premiumTable.appendChild(row('Sum Insured', fmt(value)));
        premiumTable.appendChild(row('Coverage', coverage === 'comp' ? 'Comprehensive' : 'Third Party Only'));
        if (coverage === 'comp') {
            premiumTable.appendChild(row('Base Rate', rate.toFixed(2) + '% of sum insured'));
        }
        premiumTable.appendChild(row('Adjustments', adj.length ? adj.join(', ') : 'None'));
        premiumTable.appendChild(row('Estimated Yearly Premium', fmt(premium)));
        premiumTable.appendChild(row('Range (approx)', fmt(premium * 0.85) + ' - ' + fmt(premium * 1.2)));
        premiumTable.appendChild(row('Monthly (approx)', fmt(premium / 12)));
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
