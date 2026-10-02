@extends('layouts.app')

@section('title', 'Solar Load Calculator - Azlaan Tools')
@section('meta_description', 'Free solar load calculator for Pakistan. Add your home appliances, check total watts and daily units, and find the recommended solar system size in kW.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-3">Solar Load Calculator</h1>
            <p class="lead">Select your home appliances, calculate total load and daily units (kWh), and find out how many kW of solar system you need.</p>
            <p class="text-muted">You can edit the wattage, quantity and hours for each appliance. This calculation is based on Pakistan's average peak sun hours (5 hours).</p>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <strong>Your Appliances</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle" id="applianceTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Appliance</th>
                                    <th style="min-width:110px;">Watts (W)</th>
                                    <th style="min-width:90px;">Qty</th>
                                    <th style="min-width:110px;">Hours / Day</th>
                                    <th class="text-end">Total W</th>
                                    <th class="text-end">Units / Day</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="applianceBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm" id="addCustomBtn">+ Add custom appliance</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="resetBtn">Reset defaults</button>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>Results</strong>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Total Connected Load</div>
                                <div class="fs-4 fw-bold" id="totalWatts">0 W</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Daily Consumption</div>
                                <div class="fs-4 fw-bold" id="dailyUnits">0 units</div>
                                <div class="small text-muted" id="dailyWh">0 Wh / day</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Monthly Consumption</div>
                                <div class="fs-4 fw-bold" id="monthlyUnits">0 units</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small">Recommended Solar System</div>
                                <div class="fs-3 fw-bold text-success" id="recommendedSize">0 kW</div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small" id="recommendationNote">
                        Size is calculated from your daily energy need (with 25% buffer) and your peak connected load — whichever is higher — then rounded up to a standard size (1, 3, 5, 10, 15, 20, 25 kW).
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>In the table, write the quantity of each appliance that you want to run on solar.</li>
                <li>Set the hours per day for each appliance — for example fan 10 hours, fridge 24 hours (because of compressor duty, effective use may be less).</li>
                <li>If the wattage is different, write your appliance's real rating in the Watts box.</li>
                <li>If an appliance is not in the list, add your own row with "Add custom appliance".</li>
                <li>Results update automatically — see the recommended system size, and you can also check the cost with the price estimator.</li>
            </ol>

            <h2 class="mt-4">Notes &amp; FAQ</h2>
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6">How is the recommended size calculated?</h3>
                    <p class="small mb-2">Formula: Daily Wh / (5 peak sun hours x 1000) x 1.25 buffer. The total connected load is also checked so the system can handle peak load. Whichever is higher is rounded up to the next standard size.</p>
                    <h3 class="h6">Do all appliances run at the same time?</h3>
                    <p class="small mb-2">Usually not. That is why the inverter size often works even below the total connected load, but this calculator suggests a size on the safe side. A free survey is best for the exact design.</p>
                    <h3 class="h6">Any special note for AC?</h3>
                    <p class="small mb-0">Inverter AC uses less power than its rating while running, but it is safer to count the full load for starting and peak hours. Non-inverter AC can use even more load.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var defaults = [
        { name: 'LED Bulb', watts: 12, qty: 0, hours: 6 },
        { name: 'Ceiling Fan', watts: 80, qty: 0, hours: 10 },
        { name: 'LED TV', watts: 100, qty: 0, hours: 5 },
        { name: 'Fridge', watts: 150, qty: 0, hours: 24 },
        { name: 'Washing Machine', watts: 500, qty: 0, hours: 1 },
        { name: 'Iron', watts: 1000, qty: 0, hours: 1 },
        { name: 'Water Pump', watts: 750, qty: 0, hours: 1 },
        { name: 'AC Inverter 1 ton', watts: 1200, qty: 0, hours: 8 },
        { name: 'AC 1.5 ton', watts: 1800, qty: 0, hours: 8 },
        { name: 'Microwave', watts: 1200, qty: 0, hours: 0.5 },
        { name: 'Computer / Laptop', watts: 100, qty: 0, hours: 6 }
    ];
    var standardSizes = [1, 3, 5, 10, 15, 20, 25];
    var tbody = document.getElementById('applianceBody');

    function createRow(item, isCustom) {
        var tr = document.createElement('tr');
        var nameCell = document.createElement('td');
        if (isCustom) {
            var nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.className = 'form-control form-control-sm appliance-name';
            nameInput.placeholder = 'Appliance name';
            nameInput.value = item.name || '';
            nameCell.appendChild(nameInput);
        } else {
            nameCell.textContent = item.name;
        }
        tr.appendChild(nameCell);

        function numCell(val, cls, step) {
            var td = document.createElement('td');
            var input = document.createElement('input');
            input.type = 'number';
            input.className = 'form-control form-control-sm ' + cls;
            input.min = '0';
            input.step = step || '1';
            input.value = val;
            input.addEventListener('input', calculate);
            td.appendChild(input);
            return td;
        }
        tr.appendChild(numCell(item.watts, 'inp-watts'));
        tr.appendChild(numCell(item.qty, 'inp-qty'));
        tr.appendChild(numCell(item.hours, 'inp-hours', '0.5'));

        var totalTd = document.createElement('td');
        totalTd.className = 'text-end row-total-w';
        totalTd.textContent = '0';
        tr.appendChild(totalTd);

        var unitsTd = document.createElement('td');
        unitsTd.className = 'text-end row-units';
        unitsTd.textContent = '0';
        tr.appendChild(unitsTd);

        var removeTd = document.createElement('td');
        if (isCustom) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'x';
            btn.addEventListener('click', function () { tr.remove(); calculate(); });
            removeTd.appendChild(btn);
        }
        tr.appendChild(removeTd);
        return tr;
    }

    function renderDefaults() {
        tbody.innerHTML = '';
        defaults.forEach(function (item) { tbody.appendChild(createRow(item, false)); });
        calculate();
    }

    function calculate() {
        var totalWatts = 0;
        var dailyWh = 0;
        var rows = tbody.querySelectorAll('tr');
        rows.forEach(function (row) {
            var w = Math.max(0, parseFloat(row.querySelector('.inp-watts').value) || 0);
            var q = Math.max(0, parseFloat(row.querySelector('.inp-qty').value) || 0);
            var h = Math.max(0, parseFloat(row.querySelector('.inp-hours').value) || 0);
            var rowW = w * q;
            var rowWh = rowW * h;
            totalWatts += rowW;
            dailyWh += rowWh;
            row.querySelector('.row-total-w').textContent = rowW.toLocaleString() + ' W';
            row.querySelector('.row-units').textContent = (rowWh / 1000).toFixed(2);
        });
        var dailyUnits = dailyWh / 1000;
        document.getElementById('totalWatts').textContent = totalWatts.toLocaleString() + ' W';
        document.getElementById('dailyUnits').textContent = dailyUnits.toFixed(2) + ' units';
        document.getElementById('dailyWh').textContent = Math.round(dailyWh).toLocaleString() + ' Wh / day';
        document.getElementById('monthlyUnits').textContent = (dailyUnits * 30).toFixed(1) + ' units';

        var byEnergy = dailyWh / (5 * 1000) * 1.25;
        var byLoad = totalWatts / 1000;
        var needed = Math.max(byEnergy, byLoad);
        var recommended = 0;
        for (var i = 0; i < standardSizes.length; i++) {
            if (needed <= standardSizes[i]) { recommended = standardSizes[i]; break; }
        }
        if (needed > 25) { recommended = Math.ceil(needed / 5) * 5; }
        document.getElementById('recommendedSize').textContent = (totalWatts === 0 && dailyWh === 0) ? '0 kW' : recommended + ' kW';
    }

    document.getElementById('addCustomBtn').addEventListener('click', function () {
        tbody.appendChild(createRow({ name: '', watts: 100, qty: 1, hours: 1 }, true));
        calculate();
    });
    document.getElementById('resetBtn').addEventListener('click', renderDefaults);
    renderDefaults();
})();
</script>
@endsection
