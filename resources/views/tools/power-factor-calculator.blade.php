@extends('layouts.app')

@section('title', 'Power Factor Calculator - Azlaan Tools')
@section('meta_description', 'Calculate power factor, reactive power and the correction capacitor size for your load. Free online electrical calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Power Factor Calculator</h1>
            <p class="lead text-muted">Find the power factor of your electrical load and the capacitor size needed for correction.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="calcMode" class="form-label fw-semibold">Calculation mode</label>
                        <select class="form-select" id="calcMode">
                            <option value="pq">PF from Real power (kW) + Apparent power (kVA)</option>
                            <option value="vi">kW / kVA / kVAR from Voltage + Current + PF</option>
                        </select>
                    </div>
                    <div id="pqInputs">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="realPower" class="form-label fw-semibold">Real power P (kW)</label>
                                <input type="number" class="form-control" id="realPower" placeholder="e.g. 8" min="0" step="any">
                            </div>
                            <div class="col-6 mb-3">
                                <label for="appPower" class="form-label fw-semibold">Apparent power S (kVA)</label>
                                <input type="number" class="form-control" id="appPower" placeholder="e.g. 10" min="0" step="any">
                            </div>
                        </div>
                    </div>
                    <div id="viInputs" class="d-none">
                        <div class="row">
                            <div class="col-4 mb-3">
                                <label for="volts" class="form-label fw-semibold">Voltage (V)</label>
                                <input type="number" class="form-control" id="volts" placeholder="e.g. 230" min="0" step="any">
                            </div>
                            <div class="col-4 mb-3">
                                <label for="amps" class="form-label fw-semibold">Current (A)</label>
                                <input type="number" class="form-control" id="amps" placeholder="e.g. 40" min="0" step="any">
                            </div>
                            <div class="col-4 mb-3">
                                <label for="pfKnown" class="form-label fw-semibold">Power factor</label>
                                <input type="number" class="form-control" id="pfKnown" placeholder="e.g. 0.8" min="0" max="1" step="any">
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="threePhase">
                            <label class="form-check-label" for="threePhase">Three-phase supply</label>
                        </div>
                    </div>

                    <h5 class="mt-4">Power factor correction</h5>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="pfNow" class="form-label fw-semibold">Current PF</label>
                            <input type="number" class="form-control" id="pfNow" placeholder="e.g. 0.75" min="0.01" max="0.999" step="any">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="pfTarget" class="form-label fw-semibold">Target PF</label>
                            <input type="number" class="form-control" id="pfTarget" placeholder="e.g. 0.95" min="0.01" max="0.999" step="any">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="loadKw" class="form-label fw-semibold">Load (kW)</label>
                            <input type="number" class="form-control" id="loadKw" placeholder="e.g. 8" min="0" step="any">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="capVolts" class="form-label fw-semibold">System voltage (V)</label>
                            <input type="number" class="form-control" id="capVolts" placeholder="e.g. 230" min="0" step="any">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="freq" class="form-label fw-semibold">Frequency (Hz)</label>
                            <input type="number" class="form-control" id="freq" value="50" min="1" step="any">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Result</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr><th style="width:55%">Power factor (cos phi)</th><td id="rPf"></td></tr>
                                <tr><th>Phase angle</th><td id="rAngle"></td></tr>
                                <tr><th>Real power</th><td id="rKw"></td></tr>
                                <tr><th>Apparent power</th><td id="rKva"></td></tr>
                                <tr><th>Reactive power</th><td id="rKvar"></td></tr>
                            </tbody>
                        </table>
                        <div id="corrBox" class="d-none">
                            <h5>Capacitor correction</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr><th style="width:55%">Required capacitor (kVAR)</th><td id="rCapKvar"></td></tr>
                                    <tr><th>Capacitance</th><td id="rCapUf"></td></tr>
                                </tbody>
                            </table>
                            <p class="text-muted small">Formula: Qc = P &times; (tan&phi;1 &minus; tan&phi;2), then C = Qc &divide; (2&pi;fV&sup2;). Always use a capacitor rated above the voltage and have an electrician install it.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the known values of your load (kW/kVA or V/A/PF).</li>
                <li>For correction, enter the current PF, target PF, load and voltage.</li>
                <li>Press "Calculate" — you will get the PF, kVAR and capacitor size.</li>
            </ol>
            <p class="text-muted small">The closer PF is to 1, the better. With a low PF, the power company may add a surcharge — confirm rates on the official website.</p>
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
    var calcMode = document.getElementById('calcMode');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function num(id) {
        var v = parseFloat(document.getElementById(id).value);
        return isNaN(v) ? null : v;
    }

    calcMode.addEventListener('change', function () {
        var isVi = calcMode.value === 'vi';
        document.getElementById('viInputs').classList.toggle('d-none', !isVi);
        document.getElementById('pqInputs').classList.toggle('d-none', isVi);
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var P = null, S = null, pf = null;

        if (calcMode.value === 'pq') {
            P = num('realPower');
            S = num('appPower');
            if (P === null || S === null || P < 0 || S <= 0) {
                showError('Please enter valid kW and kVA values.');
                return;
            }
            if (P > S) {
                showError('Real power (kW) cannot be more than apparent power (kVA).');
                return;
            }
            pf = P / S;
        } else {
            var V = num('volts'), I = num('amps');
            pf = num('pfKnown');
            if (V === null || I === null || pf === null || V <= 0 || I < 0 || pf <= 0 || pf > 1) {
                showError('Please enter valid Voltage, Current and PF (0 to 1).');
                return;
            }
            var three = document.getElementById('threePhase').checked;
            S = V * I / 1000 * (three ? Math.sqrt(3) : 1);
            P = S * pf;
        }

        var angle = Math.acos(Math.min(1, Math.max(-1, pf))) * 180 / Math.PI;
        var Q = Math.sqrt(Math.max(0, S * S - P * P));

        document.getElementById('rPf').textContent = pf.toFixed(3);
        document.getElementById('rAngle').textContent = angle.toFixed(1) + ' degrees';
        document.getElementById('rKw').textContent = P.toFixed(2) + ' kW';
        document.getElementById('rKva').textContent = S.toFixed(2) + ' kVA';
        document.getElementById('rKvar').textContent = Q.toFixed(2) + ' kVAR';

        var corrBox = document.getElementById('corrBox');
        var pf1 = num('pfNow'), pf2 = num('pfTarget');
        var loadKw = num('loadKw'), capV = num('capVolts'), f = num('freq');
        if (pf1 !== null && pf2 !== null && loadKw !== null && capV !== null && f !== null &&
            pf1 > 0 && pf1 < 1 && pf2 > 0 && pf2 < 1 && loadKw > 0 && capV > 0 && f > 0) {
            if (pf2 <= pf1) {
                showError('Target PF must be higher than the current PF.');
                return;
            }
            var a1 = Math.acos(pf1), a2 = Math.acos(pf2);
            var Qc = loadKw * (Math.tan(a1) - Math.tan(a2));
            var C = (Qc * 1000) / (2 * Math.PI * f * capV * capV);
            var uF = C * 1e6;
            document.getElementById('rCapKvar').textContent = Qc.toFixed(2) + ' kVAR';
            document.getElementById('rCapUf').textContent = uF.toFixed(1) + ' uF';
            corrBox.classList.remove('d-none');
        } else {
            corrBox.classList.add('d-none');
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
