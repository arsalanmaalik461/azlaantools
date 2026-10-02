@extends('layouts.app')

@section('title', 'Qibla Compass Bearing Calculator - Azlaan Tools')
@section('meta_description', 'Find the exact Qibla compass degree — by city or coordinates, with magnetic declination, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Qibla Compass Bearing Calculator</h1>
            <p class="lead text-muted">Find the exact compass degree from your location to the Khana Kaaba — with magnetic declination adjustment.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="citySelect" class="form-label fw-semibold">City (or write your coordinates below)</label>
                        <select class="form-select" id="citySelect">
                            <option value="custom">Custom location…</option>
                            <option value="24.8607,67.0011">Karachi</option>
                            <option value="31.5497,74.3436" selected>Lahore</option>
                            <option value="33.6844,73.0479">Islamabad</option>
                            <option value="33.6006,73.0679">Rawalpindi</option>
                            <option value="31.4181,73.0779">Faisalabad</option>
                            <option value="30.1575,71.5249">Multan</option>
                            <option value="34.0151,71.5249">Peshawar</option>
                            <option value="30.1798,66.9750">Quetta</option>
                            <option value="25.3969,68.3772">Hyderabad</option>
                            <option value="32.4945,74.5229">Sialkot</option>
                            <option value="32.1617,74.1883">Gujranwala</option>
                            <option value="29.3956,71.6722">Bahawalpur</option>
                            <option value="32.0836,72.6711">Sargodha</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="latInput" class="form-label fw-semibold">Latitude</label>
                            <input type="number" step="any" class="form-control" id="latInput" value="31.5497">
                        </div>
                        <div class="col-6">
                            <label for="lngInput" class="form-label fw-semibold">Longitude</label>
                            <input type="number" step="any" class="form-control" id="lngInput" value="74.3436">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="declInput" class="form-label fw-semibold">Magnetic declination (degrees East, +)</label>
                        <input type="number" step="0.1" class="form-control" id="declInput" value="3">
                        <div class="form-text">In Pakistan it is usually +2° to +4° East. Search Google for "magnetic declination [your city]" to check the exact value for your city.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Find Qibla Direction</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center mb-3">
                                <canvas id="compassCanvas" width="280" height="280" class="img-fluid"></canvas>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr><th>True Qibla bearing</th><td id="trueBearing" class="fw-bold">—</td></tr>
                                        <tr><th>Magnetic Qibla bearing</th><td id="magBearing" class="fw-bold">—</td></tr>
                                        <tr><th>Direction</th><td id="bearingDir">—</td></tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-info small mb-0">
                                    Point the compass needle (red) towards <strong>North</strong>, then face the <strong class="text-primary">blue (magnetic)</strong> arrow to pray. Keep the compass away from metal objects.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your city or write your latitude/longitude.</li>
                <li>Enter the magnetic declination (default 3° East is fine for Pakistan).</li>
                <li>Press <strong>Find Qibla Direction</strong> — the blue arrow on the compass dial is the magnetic Qibla.</li>
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
    var citySelect = document.getElementById('citySelect');
    var latInput = document.getElementById('latInput');
    var lngInput = document.getElementById('lngInput');

    var KAABA_LAT = 21.4225, KAABA_LNG = 39.8262;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    citySelect.addEventListener('change', function () {
        var v = citySelect.value;
        if (v !== 'custom') {
            var parts = v.split(',');
            latInput.value = parts[0];
            lngInput.value = parts[1];
        }
    });

    function qiblaBearing(lat1, lng1) {
        var phi1 = lat1 * Math.PI / 180;
        var phi2 = KAABA_LAT * Math.PI / 180;
        var dLng = (KAABA_LNG - lng1) * Math.PI / 180;
        var y = Math.sin(dLng);
        var x = Math.cos(phi1) * Math.tan(phi2) - Math.sin(phi1) * Math.cos(dLng);
        var brng = Math.atan2(y, x) * 180 / Math.PI;
        return (brng + 360) % 360;
    }

    function compassPoint(deg) {
        var points = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        return points[Math.round(deg / 22.5) % 16];
    }

    function drawCompass(trueBrg, magBrg) {
        var cv = document.getElementById('compassCanvas');
        var ctx = cv.getContext('2d');
        var cx = 140, cy = 140, R = 120;
        ctx.clearRect(0, 0, 280, 280);
        // outer dial
        ctx.beginPath(); ctx.arc(cx, cy, R, 0, 2 * Math.PI);
        ctx.fillStyle = '#ffffff'; ctx.fill();
        ctx.lineWidth = 3; ctx.strokeStyle = '#212529'; ctx.stroke();
        // ticks + cardinal labels
        ctx.fillStyle = '#212529'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        var i, a;
        for (i = 0; i < 360; i += 15) {
            a = i * Math.PI / 180;
            var inner = i % 90 === 0 ? R - 18 : R - 10;
            ctx.beginPath();
            ctx.moveTo(cx + inner * Math.sin(a), cy - inner * Math.cos(a));
            ctx.lineTo(cx + R * Math.sin(a), cy - R * Math.cos(a));
            ctx.lineWidth = i % 90 === 0 ? 3 : 1; ctx.stroke();
        }
        ctx.font = 'bold 16px sans-serif';
        var cards = [['N', 0, '#dc3545'], ['E', 90, '#212529'], ['S', 180, '#212529'], ['W', 270, '#212529']];
        cards.forEach(function (c) {
            a = c[1] * Math.PI / 180;
            ctx.fillStyle = c[2];
            ctx.fillText(c[0], cx + (R - 30) * Math.sin(a), cy - (R - 30) * Math.cos(a));
        });
        // true bearing arrow (green)
        a = trueBrg * Math.PI / 180;
        ctx.strokeStyle = '#198754'; ctx.lineWidth = 4;
        ctx.beginPath(); ctx.moveTo(cx, cy);
        ctx.lineTo(cx + (R - 22) * Math.sin(a), cy - (R - 22) * Math.cos(a)); ctx.stroke();
        // magnetic bearing arrow (blue, thicker)
        a = magBrg * Math.PI / 180;
        ctx.strokeStyle = '#0d6efd'; ctx.lineWidth = 5;
        ctx.beginPath(); ctx.moveTo(cx, cy);
        ctx.lineTo(cx + (R - 22) * Math.sin(a), cy - (R - 22) * Math.cos(a)); ctx.stroke();
        ctx.fillStyle = '#0d6efd';
        ctx.beginPath();
        ctx.arc(cx + (R - 22) * Math.sin(a), cy - (R - 22) * Math.cos(a), 6, 0, 2 * Math.PI); ctx.fill();
        // legend
        ctx.font = '12px sans-serif'; ctx.textAlign = 'left';
        ctx.fillStyle = '#198754'; ctx.fillText('— True Qibla', 10, 268);
        ctx.fillStyle = '#0d6efd'; ctx.fillText('— Magnetic Qibla (compass)', 110, 268);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var lat = parseFloat(latInput.value);
        var lng = parseFloat(lngInput.value);
        var decl = parseFloat(document.getElementById('declInput').value);
        if (isNaN(lat) || isNaN(lng)) { showError('Please enter latitude and longitude correctly.'); return; }
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            showError('Latitude must be between -90..90 and longitude between -180..180.');
            return;
        }
        if (isNaN(decl)) decl = 0;
        var trueBrg = qiblaBearing(lat, lng);
        var magBrg = (trueBrg + decl + 360) % 360;
        document.getElementById('trueBearing').textContent = trueBrg.toFixed(2) + '° (' + compassPoint(trueBrg) + ')';
        document.getElementById('magBearing').textContent = magBrg.toFixed(2) + '° (' + compassPoint(magBrg) + ')';
        document.getElementById('bearingDir').textContent = compassPoint(trueBrg) + ' direction (' + trueBrg.toFixed(0) + '° clockwise from North)';
        drawCompass(trueBrg, magBrg);
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
