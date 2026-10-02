@extends('layouts.app')

@section('title', 'AWG Wire Size Converter - Azlaan Tools')
@section('meta_description', 'Convert AWG wire gauge to mm, mm2, amps and resistance - free online tool for electricians.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">AWG Wire Size Converter</h1>
            <p class="lead text-muted">Convert AWG gauge to mm, mm², amps and resistance. An easy tool for electricians to find wire size.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="modeTabs">
                        <li class="nav-item"><button type="button" class="nav-link active" id="tabAwg">Find from AWG</button></li>
                        <li class="nav-item"><button type="button" class="nav-link" id="tabMm2">Find AWG from mm²</button></li>
                    </ul>

                    <div id="paneAwg">
                        <div class="mb-3">
                            <label for="awgInput" class="form-label fw-semibold">AWG size (0000 to 40)</label>
                            <input type="text" class="form-control" id="awgInput" placeholder="e.g. 12">
                            <div class="form-text">You can also write 0000, 000, 00, 0 (or 4/0, 3/0, 2/0, 1/0).</div>
                        </div>
                    </div>
                    <div id="paneMm2" class="d-none">
                        <div class="mb-3">
                            <label for="mm2Input" class="form-label fw-semibold">Cross-section area (mm²)</label>
                            <input type="number" class="form-control" id="mm2Input" placeholder="e.g. 4" step="any" min="0">
                            <div class="form-text">The nearest standard AWG size will be shown.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Convert</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 id="resTitle"></h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody id="resBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">Ampacity and resistance are approximate values for copper wire — for open wiring in air. Always confirm with a certified electrician for the actual load.</p>
                    </div>
                </div>
            </div>

            <h2>AWG Size Chart</h2>
            <p class="text-muted">Full chart of common wire sizes (diameter, area, approximate amps):</p>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-sm" id="chartTable">
                    <thead class="table-light"><tr><th>AWG</th><th>Diameter (mm)</th><th>Area (mm²)</th><th>~Amps (Cu)</th><th>Ω / km</th></tr></thead>
                    <tbody id="chartBody"></tbody>
                </table>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the AWG number (e.g. 12) or enter the mm² value.</li>
                <li>Press <strong>Convert</strong> — you get the diameter, area, approximate current capacity and resistance.</li>
                <li>Compare all standard sizes in the chart below.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var awgInput = document.getElementById('awgInput');
    var mm2Input = document.getElementById('mm2Input');
    var tabAwg = document.getElementById('tabAwg');
    var tabMm2 = document.getElementById('tabMm2');
    var paneAwg = document.getElementById('paneAwg');
    var paneMm2 = document.getElementById('paneMm2');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resTitle = document.getElementById('resTitle');
    var resBody = document.getElementById('resBody');
    var chartBody = document.getElementById('chartBody');
    var mode = 'awg';

    // Approximate copper ampacity (free-air, conservative values)
    var AMPACITY = {
        '-3': 260, '-2': 225, '-1': 195, '0': 170, '1': 150, '2': 130, '3': 110, '4': 95,
        '5': 80, '6': 70, '7': 60, '8': 55, '9': 45, '10': 40, '11': 35, '12': 30,
        '13': 25, '14': 25, '15': 20, '16': 18, '17': 15, '18': 14, '19': 12, '20': 11,
        '21': 9, '22': 7, '23': 6, '24': 5, '25': 4, '26': 3.5, '27': 3, '28': 2.5,
        '29': 2, '30': 1.8, '31': 1.5, '32': 1.3, '33': 1.1, '34': 1, '35': 0.9,
        '36': 0.8, '37': 0.7, '38': 0.6, '39': 0.5, '40': 0.45
    };
    var LABELS = { '-3': '0000 (4/0)', '-2': '000 (3/0)', '-1': '00 (2/0)', '0': '0 (1/0)' };

    function label(n) {
        return LABELS[n] !== undefined ? LABELS[n] : String(n);
    }
    function awgToMm(n) {
        // d_inches = 0.005 * 92^((36-n)/39)
        return 0.005 * Math.pow(92, (36 - n) / 39) * 25.4;
    }
    function specs(n) {
        var d = awgToMm(n);
        var area = Math.PI * d * d / 4;
        var ohmKm = 17.24 / area; // copper resistivity
        return { d: d, area: area, amps: AMPACITY[n], ohmKm: ohmKm };
    }
    function parseAwg(s) {
        s = s.trim().toLowerCase().replace(/\s+/g, '');
        var map = { '4/0': -3, '3/0': -2, '2/0': -1, '1/0': 0, '0000': -3, '000': -2, '00': -1 };
        if (map[s] !== undefined) { return map[s]; }
        if (!/^\d{1,2}$/.test(s)) { return null; }
        var n = parseInt(s, 10);
        if (n > 40) { return null; }
        return n;
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function addRow(k, v) {
        var tr = document.createElement('tr');
        var th = document.createElement('th'); th.textContent = k; th.style.width = '45%';
        var td = document.createElement('td'); td.textContent = v;
        tr.appendChild(th); tr.appendChild(td);
        resBody.appendChild(tr);
    }

    tabAwg.addEventListener('click', function () {
        mode = 'awg';
        tabAwg.classList.add('active'); tabMm2.classList.remove('active');
        paneAwg.classList.remove('d-none'); paneMm2.classList.add('d-none');
    });
    tabMm2.addEventListener('click', function () {
        mode = 'mm2';
        tabMm2.classList.add('active'); tabAwg.classList.remove('active');
        paneMm2.classList.remove('d-none'); paneAwg.classList.add('d-none');
    });

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        resBody.innerHTML = '';
        var n;
        if (mode === 'awg') {
            n = parseAwg(awgInput.value);
            if (n === null || n === undefined) { showError('Enter a valid AWG size: 0000 to 40 (e.g. 12).'); return; }
        } else {
            var mm2 = parseFloat(mm2Input.value);
            if (isNaN(mm2) || mm2 <= 0) { showError('Enter a valid mm² value (a number above 0).'); return; }
            var best = null, bestDiff = Infinity;
            for (var c = -3; c <= 40; c++) {
                var a = specs(c).area;
                var diff = Math.abs(a - mm2) / mm2;
                if (diff < bestDiff) { bestDiff = diff; best = c; }
            }
            n = best;
        }
        var s = specs(n);
        resTitle.textContent = 'AWG ' + label(n) + ' details';
        addRow('Diameter', s.d.toFixed(3) + ' mm  (' + (s.d / 25.4).toFixed(4) + ' inch)');
        addRow('Cross-section area', s.area.toFixed(3) + ' mm²');
        addRow('Approximate current (copper)', s.amps + ' A');
        addRow('Resistance', s.ohmKm.toFixed(2) + ' Ω per km');
        if (mode === 'mm2') {
            addRow('Nearest size', 'AWG ' + label(n));
        }
        results.classList.remove('d-none');
    });

    // Build full chart for common sizes
    var chartSizes = [-3, -2, -1, 0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30];
    chartSizes.forEach(function (n) {
        var s = specs(n);
        var tr = document.createElement('tr');
        [label(n), s.d.toFixed(2), s.area.toFixed(2), s.amps + ' A', s.ohmKm.toFixed(1)].forEach(function (v) {
            var td = document.createElement('td'); td.textContent = v; tr.appendChild(td);
        });
        chartBody.appendChild(tr);
    });
})();
</script>
@endsection
