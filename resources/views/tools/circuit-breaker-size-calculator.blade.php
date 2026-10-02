@extends('layouts.app')
@section('title', 'Circuit Breaker Size Calculator — Azlaan Tools')
@section('meta_description', 'Choose the right MCB circuit breaker size for your circuit. Enter load and voltage to get the recommended breaker rating, wire size and breaker type — free online.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Circuit Breaker Size Calculator</h1>
            <p class="lead text-muted">Find the right MCB size for your circuit. Enter load and voltage — get the recommended breaker rating, wire size and breaker type.</p>
            <div class="alert alert-danger">
                <strong>Safety:</strong> This calculator is only a guide. Always have electrical work done by a <strong>qualified electrician</strong> — a wrong breaker or wire can cause a fire.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="loadType" class="form-label fw-semibold">How do you know your load?</label>
                            <select class="form-select" id="loadType">
                                <option value="watts">In watts (total load)</option>
                                <option value="amps">In amperes (known current)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="loadVal" class="form-label fw-semibold" id="loadValLabel">Total load (watts)</label>
                            <input type="number" class="form-control" id="loadVal" placeholder="e.g. 2000" min="1" step="any">
                        </div>
                        <div class="col-6">
                            <label for="voltage" class="form-label fw-semibold">Voltage</label>
                            <select class="form-select" id="voltage">
                                <option value="220">220 V (single phase)</option>
                                <option value="380">380 V (three phase)</option>
                                <option value="110">110 V</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="pf" class="form-label fw-semibold">Power factor</label>
                            <select class="form-select" id="pf">
                                <option value="1">1.0 (heater, light — resistive)</option>
                                <option value="0.8" selected>0.8 (normal motor / mixed)</option>
                                <option value="0.7">0.7 (heavy motor)</option>
                            </select>
                            <div class="form-text">Applies to the watts option.</div>
                        </div>
                        <div class="col-6">
                            <label for="circuitUse" class="form-label fw-semibold">Circuit use</label>
                            <select class="form-select" id="circuitUse">
                                <option value="general">General / sockets</option>
                                <option value="lighting">Lighting</option>
                                <option value="motor">Motor / AC / pump</option>
                                <option value="heater">Heater / geyser</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="continuous" checked>
                                <label class="form-check-label" for="continuous">Load runs continuously for more than 3 hours (125% safety factor applies)</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="small text-muted">Recommended MCB</div>
                                    <div class="fs-2 fw-bold" id="rBreaker">—</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">Breaker type</div>
                                    <div class="fs-4 fw-bold" id="rType">—</div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <tbody>
                                <tr><th style="width:45%">Load current</th><td id="rCurrent">—</td></tr>
                                <tr><th>Design current (with safety factor)</th><td id="rDesign">—</td></tr>
                                <tr><th>Recommended copper wire</th><td id="rWire">—</td></tr>
                                <tr><th>Breaking capacity</th><td>6 kA (home) / 10 kA (commercial) — should be printed on the breaker</td></tr>
                            </tbody>
                        </table>
                        <div class="alert alert-info" id="rNote"></div>
                    </div>
                </div>
            </div>

            <h2>Standard MCB sizes</h2>
            <p class="text-muted">6A · 10A · 16A · 20A · 25A · 32A · 40A · 50A · 63A — always choose the standard size <em>above</em> the design current, never below it.</p>
            <h2>How to use</h2>
            <ol>
                <li>Enter total load in watts or amperes.</li>
                <li>Select the voltage and circuit use.</li>
                <li>Press <strong>Calculate</strong> — see the recommended MCB, wire size and type.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var SIZES = [6, 10, 16, 20, 25, 32, 40, 50, 63, 80, 100];
    // MCB rating -> minimum copper wire size (sq mm), conservative guidance
    var WIRE = [
        [6, '1.5 mm²'], [10, '1.5 mm²'], [16, '2.5 mm²'], [20, '2.5 mm²'],
        [25, '4 mm²'], [32, '4 mm²'], [40, '6 mm²'], [50, '10 mm²'],
        [63, '10 mm²'], [80, '16 mm²'], [100, '25 mm²']
    ];
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var loadType = document.getElementById('loadType');
    var loadValLabel = document.getElementById('loadValLabel');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    loadType.addEventListener('change', function () {
        if (loadType.value === 'amps') {
            loadValLabel.textContent = 'Load current (amperes)';
            document.getElementById('loadVal').placeholder = 'e.g. 12';
        } else {
            loadValLabel.textContent = 'Total load (watts)';
            document.getElementById('loadVal').placeholder = 'e.g. 2000';
        }
    });

    function wireFor(size) {
        for (var i = 0; i < WIRE.length; i++) {
            if (WIRE[i][0] === size) { return WIRE[i][1]; }
        }
        return 'Ask an electrician';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var val = parseFloat(document.getElementById('loadVal').value);
        if (isNaN(val) || val <= 0) { showError('Enter a correct load value (a number greater than 0).'); return; }
        var volts = parseFloat(document.getElementById('voltage').value);
        var pf = parseFloat(document.getElementById('pf').value);
        var use = document.getElementById('circuitUse').value;
        var cont = document.getElementById('continuous').checked;

        var current;
        if (loadType.value === 'amps') {
            current = val;
        } else if (volts === 380) {
            current = val / (1.732 * volts * pf); // three phase
        } else {
            current = val / (volts * pf); // single phase
        }
        var factor = cont ? 1.25 : 1.0;
        var design = current * factor;

        var pick = null;
        for (var i = 0; i < SIZES.length; i++) {
            if (SIZES[i] >= design) { pick = SIZES[i]; break; }
        }
        if (pick === null) {
            showError('Load is too high (above 100A) — outside the domestic MCB range, contact an electrician.');
            return;
        }

        var btype = 'Type C';
        var note = 'A Type C breaker is suitable for common home circuits (lights, sockets, small appliances).';
        if (use === 'motor') {
            btype = 'Type D';
            note = 'Motors / AC / pumps draw more current at startup — so choose a Type D breaker so it does not trip at startup.';
        } else if (use === 'lighting') {
            btype = 'Type B or C';
            note = 'For lighting only, Type B can also work; for a mixed circuit Type C is better.';
        } else if (use === 'heater') {
            btype = 'Type C';
            note = 'A heater / geyser is a continuous load — always keep the 125% factor and match the wire size.';
        } else {
            note = 'A Type C breaker is suitable for common home circuits (lights, sockets, small appliances).';
        }

        document.getElementById('rBreaker').textContent = pick + ' A MCB';
        document.getElementById('rType').textContent = btype;
        document.getElementById('rCurrent').textContent = current.toFixed(2) + ' A';
        document.getElementById('rDesign').textContent = design.toFixed(2) + ' A' + (cont ? ' (includes 125% factor)' : '');
        document.getElementById('rWire').textContent = wireFor(pick) + ' copper (minimum)';
        document.getElementById('rNote').textContent = note + ' The wire should never be smaller than the breaker — the breaker protects the wire.';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
