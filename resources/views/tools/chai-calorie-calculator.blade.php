@extends('layouts.app')

@section('title', 'Chai Calorie Calculator - Azlaan Tools')
@section('meta_description', 'Find the calories in doodh patti, elaichi tea or milk tea — calculate from your tea ingredients, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Chai Calorie Calculator</h1>
            <p class="lead text-muted">Select your tea ingredients — cup size, milk, sugar and spices — and instantly find out how many calories are in one cup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="chaiType" class="form-label fw-semibold">Tea Type</label>
                        <select class="form-select" id="chaiType">
                            <option value="simple">Simple milk tea</option>
                            <option value="patti" selected>Doodh patti (strong tea)</option>
                            <option value="elaichi">Elaichi tea</option>
                            <option value="adrak">Ginger tea</option>
                            <option value="kashmiri">Kashmiri chai (salty)</option>
                            <option value="qahwa">Qahwa / Green tea (without milk)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cupSize" class="form-label fw-semibold">Cup Size</label>
                            <select class="form-select" id="cupSize">
                                <option value="120">Small cup (120 ml)</option>
                                <option value="170" selected>Regular cup (170 ml)</option>
                                <option value="250">Large mug (250 ml)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="milkType" class="form-label fw-semibold">Milk Type</label>
                            <select class="form-select" id="milkType">
                                <option value="whole" selected>Full cream milk</option>
                                <option value="lowfat">Low fat milk</option>
                                <option value="evap">Dry / evaporated milk</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sugar" class="form-label fw-semibold">Sugar (teaspoons)</label>
                            <input type="number" class="form-control" id="sugar" min="0" max="10" step="0.5" value="2">
                            <div class="form-text">1 teaspoon of sugar is about 16 calories.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="milkShare" class="form-label fw-semibold">Milk Share in Cup</label>
                            <select class="form-select" id="milkShare">
                                <option value="0.25">1/4 cup milk</option>
                                <option value="0.5" selected>1/2 cup milk</option>
                                <option value="0.7">3/4 cup milk</option>
                                <option value="0.9">Almost a full cup of milk</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Extra Ingredients</label>
                        <div class="form-check"><input class="form-check-input extra" type="checkbox" id="exElaichi" value="elaichi"><label class="form-check-label" for="exElaichi">Elaichi (2 pods) — about 6 cal</label></div>
                        <div class="form-check"><input class="form-check-input extra" type="checkbox" id="exAdrak" value="adrak"><label class="form-check-label" for="exAdrak">Ginger (small piece) — about 2 cal</label></div>
                        <div class="form-check"><input class="form-check-input extra" type="checkbox" id="exMalai" value="malai"><label class="form-check-label" for="exMalai">Malai (1 teaspoon) — about 45 cal</label></div>
                        <div class="form-check"><input class="form-check-input extra" type="checkbox" id="exHoney" value="honey"><label class="form-check-label" for="exHoney">Honey (1 teaspoon, instead of sugar) — about 21 cal</label></div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Find Calories</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center mb-3">
                            <div class="display-5 fw-bold text-primary" id="totalCal">0</div>
                            <div class="text-muted">calories per cup (estimate)</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light"><tr><th>Ingredient</th><th>Amount</th><th>Calories</th></tr></thead>
                                <tbody id="breakBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">This is only an estimate, not medical advice. If you have weight or sugar problems, talk to a doctor or dietitian.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the tea type, cup size and milk type.</li>
                <li>Enter the teaspoons of sugar and extra ingredients (elaichi, malai etc).</li>
                <li>Press "Find Calories" — the full detail will show in the table.</li>
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
    var totalCal = document.getElementById('totalCal');
    var breakBody = document.getElementById('breakBody');

    // calories per 100 ml of milk
    var MILK_CAL = { whole: 66, lowfat: 42, evap: 130 };
    var MILK_LABEL = { whole: 'Full cream milk', lowfat: 'Low fat milk', evap: 'Dry milk' };
    var EXTRA_CAL = { elaichi: 6, adrak: 2, malai: 45, honey: 21 };
    var EXTRA_LABEL = { elaichi: 'Elaichi', adrak: 'Ginger', malai: 'Malai', honey: 'Honey' };
    var SUGAR_CAL = 16; // per teaspoon

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function addRow(item, qty, cal) {
        var tr = document.createElement('tr');
        var a = document.createElement('td'); a.textContent = item;
        var b = document.createElement('td'); b.textContent = qty;
        var c = document.createElement('td'); c.textContent = Math.round(cal) + ' cal';
        tr.appendChild(a); tr.appendChild(b); tr.appendChild(c);
        breakBody.appendChild(tr);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var type = document.getElementById('chaiType').value;
        var cup = parseFloat(document.getElementById('cupSize').value);
        var milk = document.getElementById('milkType').value;
        var sugar = parseFloat(document.getElementById('sugar').value);
        var share = parseFloat(document.getElementById('milkShare').value);
        if (isNaN(sugar) || sugar < 0 || sugar > 10) {
            showError('Enter sugar between 0 and 10 teaspoons.');
            return;
        }

        breakBody.innerHTML = '';
        var total = 0;

        // doodh patti and kashmiri chai have a little more milk
        var effectiveShare = share;
        if (type === 'patti') { effectiveShare = Math.min(0.95, share + 0.15); }
        if (type === 'kashmiri') { effectiveShare = Math.min(0.95, share + 0.1); }

        var milkMl = 0, milkCal = 0;
        if (type !== 'qahwa') {
            milkMl = cup * effectiveShare;
            milkCal = milkMl * (MILK_CAL[milk] / 100);
            total += milkCal;
            addRow(MILK_LABEL[milk], Math.round(milkMl) + ' ml', milkCal);
        } else {
            addRow('Qahwa base (water + tea)', cup + ' ml', 2);
            total += 2;
        }

        var sugarCal = sugar * SUGAR_CAL;
        total += sugarCal;
        addRow('Sugar', sugar + ' teaspoons', sugarCal);

        var extras = document.querySelectorAll('.extra:checked');
        for (var i = 0; i < extras.length; i++) {
            var key = extras[i].value;
            total += EXTRA_CAL[key];
            addRow(EXTRA_LABEL[key], '1 serving', EXTRA_CAL[key]);
        }

        // tea base for chai patti
        if (type !== 'qahwa') {
            total += 2;
            addRow('Tea leaves + water', 'base', 2);
        }

        totalCal.textContent = Math.round(total);
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
