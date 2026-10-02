@extends('layouts.app')

@section('title', 'Solar Price Estimator Pakistan 2026 - Azlaan Tools')
@section('meta_description', 'Free solar system price estimator for Pakistan 2026. Get an estimated price range for 1 to 25 kW on-grid and hybrid solar systems with panels, inverter and batteries.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-3">Solar Price Estimator</h1>
            <p class="lead">Get a cost estimate for installing a solar system in Pakistan — select the system size, panel type, inverter and battery to get an instant price range.</p>
            <p class="text-muted">Rates are based on plausible 2026 Pakistan market rates. The actual price can change with brand, city and site conditions.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <strong>System Options</strong>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="sizeRange" class="form-label fw-bold">System Size: <span id="sizeLabel" class="text-success">5 kW</span></label>
                        <input type="range" class="form-range" id="sizeRange" min="0" max="10" step="1" value="3">
                        <div class="d-flex flex-wrap gap-1 mt-2" id="sizeButtons"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="panelType" class="form-label fw-bold">Panel Type</label>
                            <select class="form-select" id="panelType">
                                <option value="tier1" selected>Tier-1 Chinese (Longi / JA / Jinko)</option>
                                <option value="local">Local / Other Panels</option>
                            </select>
                            <div class="form-text">Tier-1 panels are more efficient and come with a longer warranty.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="inverterType" class="form-label fw-bold">Inverter Type</label>
                            <select class="form-select" id="inverterType">
                                <option value="hybrid" selected>Hybrid (with battery support)</option>
                                <option value="ongrid">On-Grid (net metering, without battery backup)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="batteryType" class="form-label fw-bold">Battery</label>
                            <select class="form-select" id="batteryType">
                                <option value="none" selected>No Battery</option>
                                <option value="tubular">Tubular Battery (12V 200Ah)</option>
                                <option value="lithium">Lithium Battery (48V 100Ah, 5 kWh)</option>
                            </select>
                            <div class="form-text">A battery is only useful with a hybrid system. If you select on-grid, the battery cost will be kept at zero.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="structureType" class="form-label fw-bold">Structure Type</label>
                            <select class="form-select" id="structureType">
                                <option value="standard" selected>Standard Structure (ground / normal roof)</option>
                                <option value="elevated">Elevated / Custom Structure (shed type)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>Estimated Price</strong>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="text-muted small">Estimated Total Price Range</div>
                        <div class="fs-3 fw-bold text-success" id="priceRange">Rs 0 - Rs 0</div>
                        <div class="small text-muted" id="perWattNote"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Item</th><th>Details</th><th class="text-end">Estimated Cost</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Solar Panels</td><td id="panelDetail">-</td><td class="text-end" id="panelCost">-</td></tr>
                                <tr><td>Inverter</td><td id="inverterDetail">-</td><td class="text-end" id="inverterCost">-</td></tr>
                                <tr><td>Batteries</td><td id="batteryDetail">-</td><td class="text-end" id="batteryCost">-</td></tr>
                                <tr><td>Structure &amp; Installation</td><td id="structureDetail">-</td><td class="text-end" id="structureCost">-</td></tr>
                                <tr class="table-light fw-bold"><td colspan="2">Total (approx.)</td><td class="text-end" id="totalCost">-</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0">
                        This is only an estimate. For a free survey and exact rate, contact Azlaan Electric AC Solar Center: Malik Arslan 0300-8987448, Malik Rehan 0314-6332385 (Faisalabad).
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your system size with the slider or buttons (1 to 25 kW).</li>
                <li>Choose the panel type — Tier-1 (Longi, JA, Jinko) is recommended; local panels are cheaper but come with less warranty.</li>
                <li>Select the inverter type: choose Hybrid if you need load-shedding backup, or On-Grid if you only want to reduce your bill and have net metering.</li>
                <li>If you select a battery option, its cost will appear separately in the breakdown.</li>
                <li>Check the price range and breakdown to plan your budget, then book a free survey for the exact rate.</li>
            </ol>

            <h2 class="mt-4">Notes &amp; FAQ</h2>
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6">What are the rates based on?</h3>
                    <p class="small mb-2">On-grid systems are about Rs 95-115 per watt and hybrid systems (without battery) about Rs 120-140 per watt, based on plausible 2026 Pakistan market rates. The per-watt rate is adjusted by panel type: the upper part of the range for Tier-1, the lower part for local panels. A tubular battery is assumed at about Rs 55,000 per 12V 200Ah unit and a lithium 48V 100Ah (5 kWh) unit at about Rs 380,000.</p>
                    <h3 class="h6">How many batteries are needed?</h3>
                    <p class="small mb-2">For a tubular setup, usually 2 batteries up to 3 kW and 4 batteries for 5 kW or more. For lithium, 1 unit (5 kWh) up to 5 kW, with 1 extra unit estimated for every additional 5 kW on bigger systems.</p>
                    <h3 class="h6">Are net metering and WAPDA charges included?</h3>
                    <p class="small mb-0">No. The net metering application, meter charges, extra cabling length or civil work may be outside this estimate. An exact quotation is only given after a survey.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var sizes = [1, 2, 3, 5, 6, 8, 10, 12, 15, 20, 25];
    var sizeRange = document.getElementById('sizeRange');
    var sizeButtons = document.getElementById('sizeButtons');

    sizes.forEach(function (kw, idx) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'btn btn-sm btn-outline-success size-btn';
        b.textContent = kw + ' kW';
        b.setAttribute('data-idx', idx);
        b.addEventListener('click', function () {
            sizeRange.value = idx;
            calculate();
        });
        sizeButtons.appendChild(b);
    });

    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
    function fmtRange(low, high) { return fmt(low) + ' - ' + fmt(high); }

    function calculate() {
        var idx = parseInt(sizeRange.value, 10);
        var kw = sizes[idx];
        var watts = kw * 1000;
        document.getElementById('sizeLabel').textContent = kw + ' kW';
        var btns = sizeButtons.querySelectorAll('.size-btn');
        btns.forEach(function (b, i) {
            if (i === idx) { b.classList.add('active'); } else { b.classList.remove('active'); }
        });

        var panelType = document.getElementById('panelType').value;
        var inverterType = document.getElementById('inverterType').value;
        var batteryType = document.getElementById('batteryType').value;
        var structureType = document.getElementById('structureType').value;

        // Base per-watt system cost (before battery), 2026 plausible PK rates
        var baseLow, baseHigh;
        if (inverterType === 'ongrid') { baseLow = 95; baseHigh = 115; }
        else { baseLow = 120; baseHigh = 140; }
        // Panel type adjustment inside the range
        var panelAdjLow = panelType === 'tier1' ? 5 : -8;
        var panelAdjHigh = panelType === 'tier1' ? 8 : -10;
        var perWattLow = baseLow + panelAdjLow;
        var perWattHigh = baseHigh + panelAdjHigh;

        // Rough breakdown shares of the base system cost
        var panelPerWatt = panelType === 'tier1' ? 42 : 33;
        var panelCostLow = watts * (panelPerWatt - 3);
        var panelCostHigh = watts * (panelPerWatt + 3);
        var baseTotalLow = watts * perWattLow;
        var baseTotalHigh = watts * perWattHigh;

        var structureBase = structureType === 'elevated' ? 0.16 : 0.11;
        var structureCostLow = baseTotalLow * structureBase;
        var structureCostHigh = baseTotalHigh * (structureBase + 0.02);
        var inverterCostLow = Math.max(0, baseTotalLow - panelCostLow - structureCostLow);
        var inverterCostHigh = Math.max(0, baseTotalHigh - panelCostHigh - structureCostHigh);

        // Batteries (only meaningful with hybrid)
        var batteryCostLow = 0, batteryCostHigh = 0, batteryDetail = 'No battery selected';
        var effectiveBattery = inverterType === 'ongrid' ? 'none' : batteryType;
        if (effectiveBattery === 'tubular') {
            var units = kw >= 5 ? 4 : 2;
            batteryCostLow = units * 52000;
            batteryCostHigh = units * 58000;
            batteryDetail = units + ' x Tubular 12V 200Ah (approx. Rs 55,000 per unit)';
        } else if (effectiveBattery === 'lithium') {
            var lUnits = Math.max(1, Math.ceil(kw / 5));
            batteryCostLow = lUnits * 360000;
            batteryCostHigh = lUnits * 400000;
            batteryDetail = lUnits + ' x Lithium 48V 100Ah, 5 kWh (approx. Rs 380,000 per unit)';
        } else if (inverterType === 'ongrid' && batteryType !== 'none') {
            batteryDetail = 'An on-grid system does not use a battery — cost kept at zero';
        }

        var totalLow = baseTotalLow + batteryCostLow;
        var totalHigh = baseTotalHigh + batteryCostHigh;

        document.getElementById('priceRange').textContent = fmtRange(totalLow, totalHigh);
        document.getElementById('perWattNote').textContent = 'Approx. Rs ' + perWattLow + '-' + perWattHigh + ' per watt (system, before battery) for ' + kw + ' kW';
        document.getElementById('panelDetail').textContent = (panelType === 'tier1' ? 'Tier-1 (Longi / JA / Jinko) 580W panels' : 'Local panels') + ', approx. ' + Math.ceil(watts / 580) + ' panels';
        document.getElementById('panelCost').textContent = fmtRange(panelCostLow, panelCostHigh);
        document.getElementById('inverterDetail').textContent = (inverterType === 'hybrid' ? 'Hybrid inverter ' : 'On-grid inverter ') + kw + ' kW';
        document.getElementById('inverterCost').textContent = fmtRange(inverterCostLow, inverterCostHigh);
        document.getElementById('batteryDetail').textContent = batteryDetail;
        document.getElementById('batteryCost').textContent = batteryCostLow === 0 ? fmt(0) : fmtRange(batteryCostLow, batteryCostHigh);
        document.getElementById('structureDetail').textContent = structureType === 'elevated' ? 'Elevated / custom structure, wiring, installation' : 'Standard structure, wiring, installation';
        document.getElementById('structureCost').textContent = fmtRange(structureCostLow, structureCostHigh);
        document.getElementById('totalCost').textContent = fmtRange(totalLow, totalHigh);
    }

    ['panelType', 'inverterType', 'batteryType', 'structureType'].forEach(function (id) {
        document.getElementById(id).addEventListener('change', calculate);
    });
    sizeRange.addEventListener('input', calculate);
    calculate();
})();
</script>
@endsection
