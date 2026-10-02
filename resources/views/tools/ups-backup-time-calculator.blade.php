@extends('layouts.app')

@section('title', 'UPS Backup Time Calculator - Battery Backup Hours | Azlaan Tools')
@section('meta_description', 'Calculate UPS / inverter backup time from battery Ah, voltage, battery count, efficiency and load watts. Free, instant, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">UPS Backup Time Calculator</h1>
            <p class="lead text-muted">How long will the battery last? Enter your battery and load — find the backup time instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="ah" class="form-label fw-semibold">Battery Capacity (Ah)</label>
                            <input type="number" class="form-control form-control-lg" id="ah" value="200" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="volt" class="form-label fw-semibold">Battery Voltage (V)</label>
                            <select class="form-select form-select-lg" id="volt">
                                <option value="12" selected>12 V</option>
                                <option value="24">24 V</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="count" class="form-label fw-semibold">Number of Batteries</label>
                            <input type="number" class="form-control form-control-lg" id="count" value="1" min="1" step="1">
                        </div>
                        <div class="col-md-6">
                            <label for="eff" class="form-label fw-semibold">Inverter Efficiency (%)</label>
                            <input type="number" class="form-control form-control-lg" id="eff" value="80" min="10" max="100" step="any">
                        </div>
                        <div class="col-12">
                            <label for="load" class="form-label fw-semibold">Total Load (W)</label>
                            <input type="number" class="form-control form-control-lg" id="load" value="300" min="1" step="any">
                            <div class="form-text">Add load by clicking the chips below:</div>
                            <div class="mt-2 d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm preset" data-w="80">+ Fan 80W</button>
                                <button type="button" class="btn btn-outline-primary btn-sm preset" data-w="12">+ LED Bulb 12W</button>
                                <button type="button" class="btn btn-outline-primary btn-sm preset" data-w="100">+ TV 100W</button>
                                <button type="button" class="btn btn-outline-primary btn-sm preset" data-w="150">+ Fridge 150W</button>
                                <button type="button" class="btn btn-outline-primary btn-sm preset" data-w="65">+ Laptop 65W</button>
                                <button type="button" class="btn btn-outline-primary btn-sm preset" data-w="10">+ WiFi Router 10W</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearLoad">Clear Load</button>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-6"><div class="border rounded p-3 bg-light"><div class="small text-muted">Backup Time</div><div class="fs-4 fw-bold" id="outTime">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light"><div class="small text-muted">Usable Energy</div><div class="fs-4 fw-bold" id="outWh">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Note: Actual backup also depends on the battery age and health. An old battery will give less backup.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the battery Ah (written on the label, e.g. 200Ah), voltage and number of batteries.</li>
                        <li>Add the watts of the things you want to run — click the chips or type the total load yourself.</li>
                        <li>The result will update instantly: backup time (hours + minutes) and usable watt-hours.</li>
                    </ol>
                    <p class="small text-muted mb-0">Formula: Usable Wh = Ah × V × Batteries × Efficiency ÷ 100. Backup hours = Usable Wh ÷ Load (W). For UPS, battery or solar work: Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function val(id) { return parseFloat(document.getElementById(id).value) || 0; }
    function fmt(n) { return n.toLocaleString('en-PK', { maximumFractionDigits: 0 }); }
    function calc() {
        var ah = val('ah'), v = val('volt'), count = val('count'), eff = val('eff'), load = val('load');
        var wh = ah * v * count * (eff / 100);
        document.getElementById('outWh').textContent = fmt(wh) + ' Wh';
        if (load > 0 && wh > 0) {
            var hrs = wh / load;
            var h = Math.floor(hrs), m = Math.round((hrs - h) * 60);
            if (m === 60) { h += 1; m = 0; }
            document.getElementById('outTime').textContent = h + 'h ' + m + 'm';
        } else {
            document.getElementById('outTime').textContent = '—';
        }
    }
    ['ah', 'volt', 'count', 'eff', 'load'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    document.querySelectorAll('.preset').forEach(function (b) {
        b.addEventListener('click', function () {
            var loadEl = document.getElementById('load');
            loadEl.value = (parseFloat(loadEl.value) || 0) + parseFloat(b.getAttribute('data-w'));
            calc();
        });
    });
    document.getElementById('clearLoad').addEventListener('click', function () {
        document.getElementById('load').value = 0; calc();
    });
    calc();
})();
</script>
@endsection
