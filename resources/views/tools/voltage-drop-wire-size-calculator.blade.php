@extends('layouts.app')

@section('title', 'Voltage Drop & Wire Size Calculator - Cable mm² Selector | Azlaan Tools')
@section('meta_description', 'Calculate voltage drop and recommended cable size (mm²) for copper and aluminium wires in Pakistan. Free, instant, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Voltage Drop &amp; Wire Size Calculator</h1>
            <p class="lead text-muted">How many volts will drop on a long wire, and which mm² cable is right? Check instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="mode" class="form-label fw-semibold">Enter the load as</label>
                            <select class="form-select form-select-lg" id="mode">
                                <option value="a" selected>Current (A)</option>
                                <option value="w">Power (W) + Voltage</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="ampsWrap">
                            <label for="amps" class="form-label fw-semibold">Load Current (A)</label>
                            <input type="number" class="form-control form-control-lg" id="amps" value="20" min="0.1" step="any">
                        </div>
                        <div class="col-md-6 d-none" id="wattsWrap">
                            <label for="watts" class="form-label fw-semibold">Load Power (W)</label>
                            <input type="number" class="form-control form-control-lg" id="watts" value="4400" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="dist" class="form-label fw-semibold">One-Way Distance</label>
                            <div class="input-group input-group-lg">
                                <input type="number" class="form-control" id="dist" value="30" min="0.1" step="any">
                                <select class="form-select" id="distUnit" style="max-width:110px">
                                    <option value="m" selected>meters</option>
                                    <option value="ft">feet</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="volt" class="form-label fw-semibold">Voltage (V)</label>
                            <select class="form-select form-select-lg" id="volt">
                                <option value="12">12 V</option>
                                <option value="24">24 V</option>
                                <option value="220" selected>220 V</option>
                                <option value="380">380 V</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="mat" class="form-label fw-semibold">Cable Material</label>
                            <select class="form-select form-select-lg" id="mat">
                                <option value="0.0175" selected>Copper (0.0175 Ω·mm²/m)</option>
                                <option value="0.0285">Aluminium (0.0285 Ω·mm²/m)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="size" class="form-label fw-semibold">Check Cable Size (mm²) — optional</label>
                            <select class="form-select form-select-lg" id="size">
                                <option value="">Auto (recommended)</option>
                                <option>1.5</option><option>2.5</option><option>4</option><option>6</option>
                                <option>10</option><option>16</option><option>25</option><option>35</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Recommended Cable</div><div class="fs-4 fw-bold" id="outSize">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Voltage Drop</div><div class="fs-4 fw-bold" id="outDrop">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Drop %</div><div class="fs-4 fw-bold" id="outPct">—</div></div></div>
                    </div>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="warn">⚠ Voltage drop is more than 5% — use a thicker cable, otherwise the appliance will run weak and the wire can get hot.</div>
                    <p class="small text-muted mt-3 mb-0">Note: The recommended size is the smallest standard cable on which the drop stays ≤ 5% and the ampacity is also enough for the load (indicative ampacity is used).</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the load in amperes, or select watts mode and enter watts + voltage.</li>
                        <li>Select the one-way distance, voltage and cable material.</li>
                        <li>The recommended mm², voltage drop and percentage show instantly. You can also select a cable of your choice and check its drop.</li>
                    </ol>
                    <p class="small text-muted mb-0">Formula: Drop (V) = 2 × Distance (m) × Current (A) × Resistivity ÷ Cable Area (mm²). For wiring work, call Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var sizes = [1.5, 2.5, 4, 6, 10, 16, 25, 35];
    var ampacity = { '1.5': 15, '2.5': 20, '4': 28, '6': 36, '10': 50, '16': 66, '25': 84, '35': 104 };
    function val(id) { return parseFloat(document.getElementById(id).value) || 0; }
    document.getElementById('mode').addEventListener('change', function () {
        var isW = this.value === 'w';
        document.getElementById('ampsWrap').classList.toggle('d-none', isW);
        document.getElementById('wattsWrap').classList.toggle('d-none', !isW);
        calc();
    });
    function current() {
        if (document.getElementById('mode').value === 'w') {
            var v = val('volt'); return v > 0 ? val('watts') / v : 0;
        }
        return val('amps');
    }
    function dropFor(size, distM, amps, rho) { return 2 * distM * amps * rho / size; }
    function calc() {
        var distM = val('dist') * (document.getElementById('distUnit').value === 'ft' ? 0.3048 : 1);
        var amps = current(), v = val('volt'), rho = val('mat');
        var chosen = document.getElementById('size').value;
        var rec = null;
        for (var i = 0; i < sizes.length; i++) {
            var s = sizes[i];
            if (ampacity[String(s)] >= amps && v > 0 && (dropFor(s, distM, amps, rho) / v * 100) <= 5) { rec = s; break; }
        }
        if (rec === null) { rec = 35; }
        var useSize = chosen ? parseFloat(chosen) : rec;
        var drop = dropFor(useSize, distM, amps, rho);
        var pct = v > 0 ? drop / v * 100 : 0;
        document.getElementById('outSize').textContent = rec + ' mm²' + (chosen ? ' (checking ' + useSize + ' mm²)' : '');
        document.getElementById('outDrop').textContent = drop.toFixed(2) + ' V';
        document.getElementById('outPct').textContent = pct.toFixed(2) + ' %';
        document.getElementById('warn').classList.toggle('d-none', pct <= 5);
    }
    ['amps', 'watts', 'dist', 'distUnit', 'volt', 'mat', 'size'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
