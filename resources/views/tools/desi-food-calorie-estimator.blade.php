@extends('layouts.app')

@section('title', 'Desi Food Calorie Estimator - Azlaan Tools')
@section('meta_description', 'Estimate calories in desi Pakistani foods like biryani, nihari, roti and samosa. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Desi Food Calorie Estimator</h1>
            <p class="lead text-muted">Biryani, nihari, roti, samosa — estimate the calories in your food.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">Choose a food and enter the quantity</label>
                    <div id="foodRows"></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addRowBtn">+ Add Food</button>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Estimate Calories</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4"></div>
                    <p class="text-muted small mt-3 mb-0">This is only an estimate, not medical advice. Calories can change with the cooking method and the quantity.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a food and write how much you ate.</li>
                <li>Add more foods if you want.</li>
                <li>Press the button — you will get total calories and a full breakdown.</li>
            </ol>
            <p class="text-muted small">Values are estimates based on average Pakistani recipes.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    // kcal per standard unit (average desi recipes)
    var FOODS = [
        { name: 'Roti (wheat, medium)', unit: 'roti', kcal: 120 },
        { name: 'Naan (roghni)', unit: 'naan', kcal: 260 },
        { name: 'Paratha (with ghee)', unit: 'paratha', kcal: 290 },
        { name: 'Biryani chicken (1 plate)', unit: 'plate', kcal: 650 },
        { name: 'Biryani beef (1 plate)', unit: 'plate', kcal: 720 },
        { name: 'Nihari (1 bowl + 1 naan)', unit: 'serving', kcal: 850 },
        { name: 'Chicken karahi (1 serving)', unit: 'serving', kcal: 550 },
        { name: 'Daal fry (1 bowl)', unit: 'bowl', kcal: 280 },
        { name: 'Chicken qorma (1 serving)', unit: 'serving', kcal: 480 },
        { name: 'Palak gosht (1 serving)', unit: 'serving', kcal: 420 },
        { name: 'Samosa (1 piece)', unit: 'piece', kcal: 150 },
        { name: 'Pakora (5 pieces)', unit: 'serving', kcal: 320 },
        { name: 'Kebab (seekh, 1 piece)', unit: 'piece', kcal: 170 },
        { name: 'Haleem (1 bowl)', unit: 'bowl', kcal: 380 },
        { name: 'Paya (1 serving)', unit: 'serving', kcal: 600 },
        { name: 'Chana chaat (1 plate)', unit: 'plate', kcal: 300 },
        { name: 'Dahi bhalla (2 piece)', unit: 'serving', kcal: 280 },
        { name: 'Gulab jamun (2 piece)', unit: 'serving', kcal: 300 },
        { name: 'Kheer (1 bowl)', unit: 'bowl', kcal: 250 },
        { name: 'Sweet lassi (1 glass)', unit: 'glass', kcal: 220 },
        { name: 'Doodh patti (1 cup)', unit: 'cup', kcal: 90 },
        { name: 'Aloo paratha (1)', unit: 'paratha', kcal: 340 },
        { name: 'Chana (boiled, 1 cup)', unit: 'cup', kcal: 270 },
        { name: 'White rice (1 cup cooked)', unit: 'cup', kcal: 200 },
        { name: 'Fruit chaat (1 plate)', unit: 'plate', kcal: 180 }
    ];

    var rowsWrap = document.getElementById('foodRows');
    var addRowBtn = document.getElementById('addRowBtn');
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

    function addRow(selIdx, qty) {
        var div = document.createElement('div');
        div.className = 'row g-2 mb-2 food-row';
        var opts = '';
        for (var i = 0; i < FOODS.length; i++) {
            opts += '<option value="' + i + '"' + (i === selIdx ? ' selected' : '') + '>' + FOODS[i].name + ' (~' + FOODS[i].kcal + ' kcal)</option>';
        }
        div.innerHTML =
            '<div class="col-7"><select class="form-select food-sel">' + opts + '</select></div>' +
            '<div class="col-3"><input type="number" class="form-control food-qty" min="0" step="0.5" value="' + (qty || 1) + '" title="Quantity"></div>' +
            '<div class="col-2"><button type="button" class="btn btn-outline-danger w-100 del-row" title="Remove">&times;</button></div>';
        rowsWrap.appendChild(div);
        div.querySelector('.del-row').addEventListener('click', function () { div.remove(); });
    }

    addRowBtn.addEventListener('click', function () { addRow(0, 1); hideError(); });
    addRow(3, 1); // biryani
    addRow(0, 2); // 2 roti

    goBtn.addEventListener('click', function () {
        hideError();
        var rows = rowsWrap.querySelectorAll('.food-row');
        var items = [];
        var total = 0;
        for (var i = 0; i < rows.length; i++) {
            var fi = parseInt(rows[i].querySelector('.food-sel').value, 10);
            var q = parseFloat(rows[i].querySelector('.food-qty').value) || 0;
            if (q > 0 && FOODS[fi]) {
                var k = Math.round(FOODS[fi].kcal * q);
                items.push({ name: FOODS[fi].name, unit: FOODS[fi].unit, qty: q, kcal: k });
                total += k;
            }
        }
        if (!items.length) { showError('Choose at least one food with a quantity.'); return; }

        var html = '<div class="text-center mb-3"><div class="display-5 fw-bold text-primary">' + total.toLocaleString('en-PK') + '</div><div class="text-muted">total estimated calories (kcal)</div></div>';
        html += '<div class="table-responsive"><table class="table table-sm table-bordered"><thead class="table-light"><tr><th>Food</th><th class="text-end">Quantity</th><th class="text-end">Calories</th></tr></thead><tbody>';
        for (var j = 0; j < items.length; j++) {
            html += '<tr><td>' + items[j].name.replace(/</g, '&lt;') + '</td><td class="text-end">' + items[j].qty + ' ' + items[j].unit + '</td><td class="text-end">' + items[j].kcal.toLocaleString('en-PK') + '</td></tr>';
        }
        html += '</tbody></table></div>';

        var note;
        if (total < 400) note = 'Light meal — like a breakfast or snack.';
        else if (total < 800) note = 'A normal one-time meal — fine for an average person.';
        else if (total < 1200) note = 'Heavy meal — if you want to lose weight, eat light next time.';
        else note = 'Too many calories at one time — better to split into smaller portions.';
        html += '<div class="alert alert-info mb-0">' + note + '</div>';

        results.innerHTML = html;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
