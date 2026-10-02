@extends('layouts.app')

@section('title', 'Solar Panels Calculator - How Many Panels Do I Need? - Azlaan Tools')
@section('meta_description', 'Free solar panels calculator for Pakistan. Enter daily units or system kW and find how many 580W solar panels you need, roof area required and total DC capacity.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-3">Solar Panels Calculator</h1>
            <p class="lead">How many solar panels do you need? Enter your daily units or system size (kW) — the number of panels, roof area and total DC capacity will be calculated instantly.</p>
            <p class="text-muted">Default calculation: one 580W panel in Pakistan produces about 2.3 kWh (units) per day. You can also edit the generation and roof area values yourself.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <strong>Choose Calculation Mode</strong>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="modeTabs">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" id="tabUnits">Mode A: By Daily Units</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" id="tabKw">Mode B: By System kW</button>
                        </li>
                    </ul>

                    <div id="modeUnits">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="dailyUnits" class="form-label fw-bold">Daily Units Needed (kWh / day)</label>
                                <input type="number" class="form-control" id="dailyUnits" min="0" step="0.1" value="20">
                                <div class="form-text">If your monthly bill is in units, divide it by 30 to get daily units.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="genPerPanel" class="form-label fw-bold">Generation per 580W Panel (kWh / day)</label>
                                <input type="number" class="form-control" id="genPerPanel" min="0.1" step="0.1" value="2.3">
                                <div class="form-text">Default 2.3 kWh/day — based on about 4 effective sun hours.</div>
                            </div>
                        </div>
                    </div>

                    <div id="modeKw" class="d-none">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="systemKw" class="form-label fw-bold">System Size (kW)</label>
                                <input type="number" class="form-control" id="systemKw" min="0" step="0.1" value="5">
                                <div class="form-text">Panels = kW x 1000 / 580</div>
                            </div>
                            <div class="col-md-6">
                                <label for="effectiveSun" class="form-label fw-bold">Effective Sun Hours (for generation estimate)</label>
                                <input type="number" class="form-control" id="effectiveSun" min="0" step="0.1" value="4">
                                <div class="form-text">In Pakistan, 4 effective sun hours is commonly assumed.</div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="panelWatt" class="form-label fw-bold">Panel Wattage (W)</label>
                            <input type="number" class="form-control" id="panelWatt" min="1" step="1" value="580">
                        </div>
                        <div class="col-md-4">
                            <label for="areaPerPanel" class="form-label fw-bold">Roof Area per Panel (sq ft)</label>
                            <input type="number" class="form-control" id="areaPerPanel" min="1" step="1" value="28">
                            <div class="form-text">Includes spacing. Default 28 sq ft per panel.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="lossFactor" class="form-label fw-bold">System Buffer / Losses (%)</label>
                            <input type="number" class="form-control" id="lossFactor" min="0" max="50" step="1" value="10">
                            <div class="form-text">Buffer for dust, wiring and inverter losses.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>Results</strong>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small">Panels Required</div>
                                <div class="fs-3 fw-bold text-success" id="panelCount">0</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Total DC Capacity</div>
                                <div class="fs-4 fw-bold" id="dcCapacity">0 kW</div>
                                <div class="small text-muted" id="dcWatts"></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Roof Area Needed</div>
                                <div class="fs-4 fw-bold" id="roofArea">0 sq ft</div>
                                <div class="small text-muted" id="roofMarla"></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Expected Generation</div>
                                <div class="fs-4 fw-bold" id="expectedGen">0 units/day</div>
                                <div class="small text-muted" id="expectedMonthly"></div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small" id="resultNote"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the mode: if you know your daily units (kWh), use Mode A; if the system size (kW) is already decided, use Mode B.</li>
                <li>In Mode A, enter daily units — dividing your monthly bill by 30 is an easy way to find this.</li>
                <li>Panel wattage defaults to 580W; if you are using a different panel, enter its rating.</li>
                <li>You can edit the generation-per-panel and roof-area-per-panel values based on your city and roof.</li>
                <li>Check the results for panel count, DC capacity, roof area and expected daily/monthly generation.</li>
            </ol>

            <h2 class="mt-4">Notes &amp; FAQ</h2>
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6">How much electricity does one 580W panel make per day?</h3>
                    <p class="small mb-2">In Pakistan, with about 4 effective sun hours: 580W x 4 hours = 2.32 kWh, i.e. about 2.3 units per day. It can be lower in winter and higher in summer.</p>
                    <h3 class="h6">Why is roof area 28 sq ft per panel?</h3>
                    <p class="small mb-2">A large panel is about 18-20 sq ft in size, but for planning, 28 sq ft per panel is a safe estimate including spacing between rows, shadow gaps and walking space.</p>
                    <h3 class="h6">Why is the panel count always rounded up?</h3>
                    <p class="small mb-0">Half a panel cannot be installed, so the required count is always rounded up to the next full number. This makes the actual DC capacity slightly higher than your requirement, which helps cover losses.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var mode = 'units';
    var tabUnits = document.getElementById('tabUnits');
    var tabKw = document.getElementById('tabKw');
    var modeUnitsDiv = document.getElementById('modeUnits');
    var modeKwDiv = document.getElementById('modeKw');

    function setMode(m) {
        mode = m;
        if (m === 'units') {
            tabUnits.classList.add('active');
            tabKw.classList.remove('active');
            modeUnitsDiv.classList.remove('d-none');
            modeKwDiv.classList.add('d-none');
        } else {
            tabKw.classList.add('active');
            tabUnits.classList.remove('active');
            modeKwDiv.classList.remove('d-none');
            modeUnitsDiv.classList.add('d-none');
        }
        calculate();
    }
    tabUnits.addEventListener('click', function () { setMode('units'); });
    tabKw.addEventListener('click', function () { setMode('kw'); });

    function val(id) { return parseFloat(document.getElementById(id).value) || 0; }

    function calculate() {
        var panelWatt = val('panelWatt') || 580;
        var areaPerPanel = val('areaPerPanel') || 28;
        var lossPct = val('lossFactor') || 0;
        var buffer = 1 + (lossPct / 100);
        var panels = 0;
        var dailyGenPerPanel = 0;
        var note = '';

        if (mode === 'units') {
            var dailyUnits = val('dailyUnits');
            var gen = val('genPerPanel') || 2.3;
            dailyGenPerPanel = gen;
            if (dailyUnits > 0 && gen > 0) {
                panels = Math.ceil((dailyUnits * buffer) / gen);
            }
            note = 'Mode A: for ' + dailyUnits + ' units/day, one panel makes ' + gen + ' units/day (' + lossPct + '% buffer included).';
        } else {
            var kw = val('systemKw');
            var sun = val('effectiveSun') || 4;
            // Gross generation per panel, derated by the shared loss buffer so the
            // expected (net) generation matches what the system actually delivers.
            dailyGenPerPanel = ((panelWatt / 1000) * sun) / buffer;
            if (kw > 0) {
                panels = Math.ceil((kw * 1000) / panelWatt);
            }
            note = 'Mode B: for a ' + kw + ' kW system, panels = kW x 1000 / ' + panelWatt + 'W, rounded up. Expected generation includes a ' + lossPct + '% buffer for system losses.';
        }

        var dcWatts = panels * panelWatt;
        var dcKw = dcWatts / 1000;
        var roofSqft = panels * areaPerPanel;
        var expectedDaily = panels * dailyGenPerPanel;

        document.getElementById('panelCount').textContent = panels.toLocaleString();
        document.getElementById('dcCapacity').textContent = dcKw.toFixed(2) + ' kW';
        document.getElementById('dcWatts').textContent = dcWatts.toLocaleString() + ' W DC';
        document.getElementById('roofArea').textContent = roofSqft.toLocaleString() + ' sq ft';
        document.getElementById('roofMarla').textContent = 'approx. ' + (roofSqft / 272.25).toFixed(2) + ' marla area';
        document.getElementById('expectedGen').textContent = expectedDaily.toFixed(1) + ' units/day';
        document.getElementById('expectedMonthly').textContent = 'approx. ' + Math.round(expectedDaily * 30).toLocaleString() + ' units / month';
        document.getElementById('resultNote').textContent = note;
    }

    ['dailyUnits', 'genPerPanel', 'systemKw', 'effectiveSun', 'panelWatt', 'areaPerPanel', 'lossFactor'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calculate);
    });
    calculate();
})();
</script>
@endsection
