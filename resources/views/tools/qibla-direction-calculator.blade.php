@extends('layouts.app')

@section('title', 'Qibla Direction Calculator — Free Online Tool')
@section('meta_description', 'Get the exact Qibla degree and compass direction from your city or latitude/longitude.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Qibla Direction Calculator</h1>
            <p class="lead small text-muted">Choose a city or enter your latitude/longitude — get the great-circle bearing (degrees) towards the Khana Kaaba, the compass direction and the distance.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="qbCity">City preset</label><select class="form-select" id="qbCity"><option value="">Custom coordinates</option><option value="31.4504,73.135" selected>Faisalabad</option><option value="24.8608,67.0011">Karachi</option><option value="31.5497,74.3436">Lahore</option><option value="33.6844,73.0479">Islamabad</option><option value="30.1575,71.5249">Multan</option><option value="34.0151,71.5849">Peshawar</option><option value="30.1798,66.975">Quetta</option><option value="25.396,68.3578">Hyderabad (Sindh)</option><option value="32.4945,74.5229">Sialkot</option></select></div>
                <div class="col-md-4"><label class="form-label" for="qbLat">Your latitude</label><input type="number" class="form-control qb-in" id="qbLat" value="31.4504" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="qbLng">Your longitude</label><input type="number" class="form-control qb-in" id="qbLng" value="73.135" step="any"></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="qbMsg"></div>
            <div class="border rounded p-4 mt-3 text-center">
                <div class="text-muted small">Qibla bearing (from true north, clockwise)</div>
                <div class="display-5 fw-bold" id="qbDeg">—</div>
                <div class="fs-5" id="qbCompass">—</div>
                <div class="small text-muted mt-1">Distance to the Khana Kaaba: <span id="qbDist">—</span></div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose a city or enter your location latitude/longitude (you can get them in Google Maps by long-pressing your location).</li><li>Set the bearing degree on your compass — it is measured from true north.</li><li>From Pakistan, the Qibla is usually towards west-northwest, about 255 to 262 degrees.</li></ol>
            <p class="small text-muted mb-0">Note: The bearing uses the great-circle (spherical trigonometry) formula, which gives the shortest and most correct path. A phone compass shows magnetic north — in Pakistan the magnetic difference is small (about 1-2 degrees), but still follow the direction of your mosque mihrab as final.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    var KAABA_LAT = 21.4225, KAABA_LNG = 39.8262;
    function calc() {
        var lat = num("qbLat"), lng = num("qbLng"), msg = el("qbMsg");
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180 || el("qbLat").value === "" || el("qbLng").value === "") { msg.textContent = "Please enter a correct latitude (-90 to 90) and longitude (-180 to 180)."; msg.classList.remove("d-none"); return; }
        msg.classList.add("d-none");
        var phi = lat * Math.PI / 180, lam = lng * Math.PI / 180, phiK = KAABA_LAT * Math.PI / 180, dLam = (KAABA_LNG - lng) * Math.PI / 180;
        var y = Math.sin(dLam);
        var x = Math.cos(phi) * Math.tan(phiK) - Math.sin(phi) * Math.cos(dLam);
        var bearing = Math.atan2(y, x) * 180 / Math.PI;
        bearing = (bearing + 360) % 360;
        var dPhi = phiK - phi;
        var a = Math.sin(dPhi / 2) * Math.sin(dPhi / 2) + Math.cos(phi) * Math.cos(phiK) * Math.sin(dLam / 2) * Math.sin(dLam / 2);
        var dist = 6371 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var dirs = ["North", "North-Northeast", "Northeast", "East-Northeast", "East", "East-Southeast", "Southeast", "South-Southeast", "South", "South-Southwest", "Southwest", "West-Southwest", "West", "West-Northwest", "Northwest", "North-Northwest"];
        el("qbDeg").textContent = bearing.toFixed(1) + "°";
        el("qbCompass").textContent = dirs[Math.round(bearing / 22.5) % 16];
        el("qbDist").textContent = fmt(Math.round(dist)) + " km";
    }
    el("qbCity").addEventListener("change", function () { var v = el("qbCity").value; if (!v) return; var p = v.split(","); el("qbLat").value = p[0]; el("qbLng").value = p[1]; calc(); });
    document.querySelectorAll(".qb-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
