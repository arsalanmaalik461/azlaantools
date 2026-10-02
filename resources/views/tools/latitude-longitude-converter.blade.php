@extends('layouts.app')

@section('title', 'Latitude Longitude Converter — Free Online Tool')
@section('meta_description', 'Enter coordinates and instantly convert degrees minutes seconds to decimal degrees.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Latitude Longitude Converter</h1>
            <p class="lead small text-muted mb-4">Enter coordinates and instantly convert degrees minutes seconds to decimal degrees.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">DMS to Decimal Degrees</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-3"><label for="dmsD" class="form-label">Degrees</label><input type="number" class="form-control" id="dmsD" value="31" step="any"></div>
                        <div class="col-3"><label for="dmsM" class="form-label">Minutes</label><input type="number" class="form-control" id="dmsM" value="32" step="any"></div>
                        <div class="col-3"><label for="dmsS" class="form-label">Seconds</label><input type="number" class="form-control" id="dmsS" value="12" step="any"></div>
                        <div class="col-3"><label for="dmsSign" class="form-label">Direction</label><select class="form-select" id="dmsSign"><option value="1">N / E (+)</option><option value="-1">S / W (-)</option></select></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">Decimal Degrees: <strong id="ddOut">—</strong></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Decimal Degrees to DMS</h2>
                    <div class="mb-3"><label for="ddIn" class="form-label">Decimal Degrees</label><input type="number" class="form-control" id="ddIn" value="31.536667" step="any"></div>
                    <div class="alert alert-info mb-0">DMS: <strong id="dmsOut">—</strong></div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live — no button needed.</li>
                        <li>Change the value or units and the new result shows automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Decimal = degrees + minutes/60 + seconds/3600. South and West coordinates use a minus sign. Latitude is between -90 and 90, longitude between -180 and 180.</p>
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
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    function toDD() {
        var d = parseFloat(document.getElementById("dmsD").value);
        var m = parseFloat(document.getElementById("dmsM").value);
        var s = parseFloat(document.getElementById("dmsS").value);
        var sign = parseFloat(document.getElementById("dmsSign").value);
        var out = document.getElementById("ddOut");
        if (isNaN(d) || isNaN(m) || isNaN(s)) { out.textContent = "—"; return; }
        var dd = sign * (Math.abs(d) + m / 60 + s / 3600);
        out.textContent = dd.toFixed(6);
    }
    function toDMS() {
        var v = parseFloat(document.getElementById("ddIn").value);
        var out = document.getElementById("dmsOut");
        if (isNaN(v)) { out.textContent = "—"; return; }
        var sign = v < 0 ? "-" : "";
        var a = Math.abs(v);
        var d = Math.floor(a), rem = (a - d) * 60;
        var m = Math.floor(rem), s = (rem - m) * 60;
        out.textContent = sign + d + "° " + m + "′ " + s.toFixed(2) + "″";
    }
    ["dmsD", "dmsM", "dmsS"].forEach(function (id) { document.getElementById(id).addEventListener("input", toDD); });
    document.getElementById("dmsSign").addEventListener("change", toDD);
    document.getElementById("ddIn").addEventListener("input", toDMS);
    toDD(); toDMS();
})();
</script>
@endsection
