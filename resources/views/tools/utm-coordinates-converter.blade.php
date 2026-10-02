@extends('layouts.app')

@section('title', 'UTM Coordinates Converter — Free Online Tool')
@section('meta_description', 'Enter a UTM zone, easting and northing and convert to latitude longitude instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">UTM Coordinates Converter</h1>
            <p class="lead small text-muted mb-4">Enter a UTM zone, easting and northing and convert to latitude longitude instantly.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Lat / Lon to UTM</h2>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="uLat" class="form-label">Latitude</label><input type="number" class="form-control" id="uLat" value="31.5204" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="uLon" class="form-label">Longitude</label><input type="number" class="form-control" id="uLon" value="74.3587" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0"><div class="fw-bold" id="utmOut">—</div></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">UTM to Lat / Lon</h2>
                    <div class="row g-2">
                        <div class="col-md-3 mb-3"><label for="uZone" class="form-label">Zone (1–60)</label><input type="number" class="form-control" id="uZone" value="43" step="1"></div>
                        <div class="col-md-3 mb-3"><label for="uHemi" class="form-label">Hemisphere</label><select class="form-select" id="uHemi"><option value="N" selected>North</option><option value="S">South</option></select></div>
                        <div class="col-md-3 mb-3"><label for="uEast" class="form-label">Easting (m)</label><input type="number" class="form-control" id="uEast" value="320000" step="any"></div>
                        <div class="col-md-3 mb-3"><label for="uNorth" class="form-label">Northing (m)</label><input type="number" class="form-control" id="uNorth" value="3488000" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0"><div class="fw-bold" id="latLonOut">—</div></div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your values in the boxes above or select an option.</li>
                        <li>The result updates live — no button press needed.</li>
                        <li>Change a value or unit and the new result shows by itself.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">The math uses the WGS84 datum and standard transverse Mercator formulas. Pakistan lies in zones 42 and 43 (Lahore / Faisalabad are zone 43). For official survey work use verified coordinates from a licensed surveyor.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    var a = 6378137.0, f = 1 / 298.257223563, k0 = 0.9996;
    var e2 = f * (2 - f), ep2 = e2 / (1 - e2);
    function latLonToUtm() {
        var lat = parseFloat(document.getElementById("uLat").value);
        var lon = parseFloat(document.getElementById("uLon").value);
        var out = document.getElementById("utmOut");
        if (isNaN(lat) || isNaN(lon) || lat < -80 || lat > 84 || lon < -180 || lon > 180) { out.textContent = "Enter latitude -80 to 84 and longitude -180 to 180."; return; }
        var zone = Math.floor((lon + 180) / 6) + 1;
        var latR = lat * Math.PI / 180, lonR = lon * Math.PI / 180;
        var lon0 = (zone - 1) * 6 - 180 + 3, lon0R = lon0 * Math.PI / 180;
        var N = a / Math.sqrt(1 - e2 * Math.pow(Math.sin(latR), 2));
        var T = Math.pow(Math.tan(latR), 2), C = ep2 * Math.pow(Math.cos(latR), 2);
        var A = Math.cos(latR) * (lonR - lon0R);
        var rho = a * (1 - e2) / Math.pow(1 - e2 * Math.pow(Math.sin(latR), 2), 1.5);
        var nu2 = N / rho - 1;
        var M = a * ((1 - e2 / 4 - 3 * e2 * e2 / 64 - 5 * Math.pow(e2, 3) / 256) * latR
            - (3 * e2 / 8 + 3 * e2 * e2 / 32 + 45 * Math.pow(e2, 3) / 1024) * Math.sin(2 * latR)
            + (15 * e2 * e2 / 256 + 45 * Math.pow(e2, 3) / 1024) * Math.sin(4 * latR)
            - (35 * Math.pow(e2, 3) / 3072) * Math.sin(6 * latR));
        var x = k0 * N * (A + (1 - T + C) * Math.pow(A, 3) / 6 + (5 - 18 * T + T * T + 72 * C - 58 * ep2) * Math.pow(A, 5) / 120) + 500000;
        var y = k0 * (M + N * Math.tan(latR) * (A * A / 2 + (5 - T + 9 * C + 4 * C * C) * Math.pow(A, 4) / 24 + (61 - 58 * T + T * T + 600 * C - 330 * ep2) * Math.pow(A, 6) / 720));
        if (lat < 0) { y += 10000000; }
        out.textContent = "Zone " + zone + (lat >= 0 ? "N" : "S") + " — Easting: " + x.toFixed(1) + " m — Northing: " + y.toFixed(1) + " m";
    }
    function utmToLatLon() {
        var zone = parseInt(document.getElementById("uZone").value, 10);
        var hemi = document.getElementById("uHemi").value;
        var x = parseFloat(document.getElementById("uEast").value);
        var y = parseFloat(document.getElementById("uNorth").value);
        var out = document.getElementById("latLonOut");
        if (isNaN(zone) || zone < 1 || zone > 60 || isNaN(x) || isNaN(y)) { out.textContent = "Enter a valid zone, easting and northing."; return; }
        var yAdj = hemi === "S" ? y - 10000000 : y;
        var M = yAdj / k0;
        var mu = M / (a * (1 - e2 / 4 - 3 * e2 * e2 / 64 - 5 * Math.pow(e2, 3) / 256));
        var e1 = (1 - Math.sqrt(1 - e2)) / (1 + Math.sqrt(1 - e2));
        var fp = mu + (3 * e1 / 2 - 27 * Math.pow(e1, 3) / 32) * Math.sin(2 * mu)
            + (21 * e1 * e1 / 16 - 55 * Math.pow(e1, 4) / 32) * Math.sin(4 * mu)
            + (151 * Math.pow(e1, 3) / 96) * Math.sin(6 * mu)
            + (1097 * Math.pow(e1, 4) / 512) * Math.sin(8 * mu);
        var sinFp = Math.sin(fp), cosFp = Math.cos(fp), tanFp = Math.tan(fp);
        var C1 = ep2 * cosFp * cosFp, T1 = tanFp * tanFp;
        var N1 = a / Math.sqrt(1 - e2 * sinFp * sinFp);
        var R1 = a * (1 - e2) / Math.pow(1 - e2 * sinFp * sinFp, 1.5);
        var D = (x - 500000) / (N1 * k0);
        var lat = fp - (N1 * tanFp / R1) * (D * D / 2 - (5 + 3 * T1 + 10 * C1 - 4 * C1 * C1 - 9 * ep2) * Math.pow(D, 4) / 24 + (61 + 90 * T1 + 298 * C1 + 45 * T1 * T1 - 252 * ep2 - 3 * C1 * C1) * Math.pow(D, 6) / 720);
        var lon0 = (zone - 1) * 6 - 180 + 3;
        var lon = (lon0 * Math.PI / 180) + (D - (1 + 2 * T1 + C1) * Math.pow(D, 3) / 6 + (5 - 2 * C1 + 28 * T1 - 8 * ep2 + 8 * C1 + 3 * C1 * C1 + 11 * ep2 + 24 * T1 * T1) * Math.pow(D, 5) / 120) / cosFp;
        out.textContent = "Latitude: " + (lat * 180 / Math.PI).toFixed(6) + " — Longitude: " + (lon * 180 / Math.PI).toFixed(6);
    }
    ["uLat", "uLon"].forEach(function (id) { document.getElementById(id).addEventListener("input", latLonToUtm); });
    ["uZone", "uEast", "uNorth"].forEach(function (id) { document.getElementById(id).addEventListener("input", utmToLatLon); });
    document.getElementById("uHemi").addEventListener("change", utmToLatLon);
    latLonToUtm(); utmToLatLon();
})();
</script>
@endsection
