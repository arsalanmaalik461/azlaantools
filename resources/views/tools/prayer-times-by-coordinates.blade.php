@extends('layouts.app')

@section('title', 'Prayer Times by Coordinates — Free Online Tool')
@section('meta_description', 'Get prayer times for any place from its latitude, longitude and calculation method.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Prayer Times by Coordinates</h1>
            <p class="lead small text-muted">Choose a city or enter your latitude/longitude — a standard solar calculation gives Fajr, Sunrise, Dhuhr, Asr, Maghrib and Isha times.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="ptCity">City preset</label><select class="form-select" id="ptCity"><option value="">Custom coordinates</option><option value="31.4504,73.135">Faisalabad</option><option value="24.8608,67.0011">Karachi</option><option value="31.5497,74.3436">Lahore</option><option value="33.6844,73.0479">Islamabad</option><option value="30.1575,71.5249">Multan</option><option value="34.0151,71.5849">Peshawar</option><option value="30.1798,66.975">Quetta</option><option value="21.4225,39.8262">Makkah</option><option value="51.5074,-0.1278">London</option><option value="25.2048,55.2708">Dubai</option></select></div>
                <div class="col-md-4"><label class="form-label" for="ptDate">Date</label><input type="date" class="form-control pt-in" id="ptDate"></div>
                <div class="col-md-4"><label class="form-label" for="ptMethod">Calculation method</label><select class="form-select pt-in" id="ptMethod"><option value="karachi" selected>Univ. of Islamic Sciences, Karachi (Fajr 18, Isha 18)</option><option value="mwl">Muslim World League (18, 17)</option><option value="isna">ISNA North America (15, 15)</option><option value="egypt">Egyptian General Authority (19.5, 17.5)</option><option value="makkah">Umm al-Qura Makkah (18.5, Isha 90 min after Maghrib)</option></select></div>
                <div class="col-md-3"><label class="form-label" for="ptLat">Latitude</label><input type="number" class="form-control pt-in" id="ptLat" value="31.4504" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="ptLng">Longitude</label><input type="number" class="form-control pt-in" id="ptLng" value="73.135" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="ptTz">Timezone (UTC offset, hours)</label><input type="number" class="form-control pt-in" id="ptTz" value="5" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="ptAsr">Asr method</label><select class="form-select pt-in" id="ptAsr"><option value="2" selected>Hanafi (factor 2)</option><option value="1">Shafi / Maliki / Hanbali (factor 1)</option></select></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="ptMsg"></div>
            <div class="row g-3 mt-2" id="ptResults"></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose a city preset or enter latitude/longitude yourself (Pakistan timezone is +5).</li><li>Choose the calculation method and Asr method that match your school of thought.</li><li>Change the date to see times for any day.</li></ol>
            <p class="small text-muted mb-0">Note: Times come from a standard astronomical solar calculation (NOAA solar equations) — as accurate as this standard algorithm allows, usually within 1-2 minutes. At very high latitudes some times may fall outside the calculation; always treat your mosque announcement as final.</p>
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
    function deg(x) { return x * Math.PI / 180; }
    function fmtTime(mins) { if (!isFinite(mins)) return "—"; mins = ((Math.round(mins) % 1440) + 1440) % 1440; var h = Math.floor(mins / 60), m = mins % 60; var ap = h >= 12 ? "PM" : "AM"; var h12 = h % 12; if (h12 === 0) h12 = 12; return h12 + ":" + ("0" + m).slice(-2) + " " + ap; }
    function calc() {
        var lat = num("ptLat"), lng = num("ptLng"), tz = num("ptTz"), msg = el("ptMsg");
        var dv = el("ptDate").value; if (!dv) { msg.textContent = "Please select a date."; msg.classList.remove("d-none"); return; }
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) { msg.textContent = "Latitude must be between -90 and 90, and longitude between -180 and 180."; msg.classList.remove("d-none"); return; }
        msg.classList.add("d-none");
        var p = dv.split("-"); var date = new Date(+p[0], +p[1] - 1, +p[2]);
        var start = new Date(date.getFullYear(), 0, 0); var doy = Math.floor((date - start) / 86400000);
        var gamma = 2 * Math.PI / 365 * (doy - 1 + 0.5);
        var eqtime = 229.18 * (0.000075 + 0.001868 * Math.cos(gamma) - 0.032077 * Math.sin(gamma) - 0.014615 * Math.cos(2 * gamma) - 0.040849 * Math.sin(2 * gamma));
        var decl = 0.006918 - 0.399912 * Math.cos(gamma) + 0.070257 * Math.sin(gamma) - 0.006758 * Math.cos(2 * gamma) + 0.000907 * Math.sin(2 * gamma) - 0.002697 * Math.cos(3 * gamma) + 0.00148 * Math.sin(3 * gamma);
        function haForZenith(z) { var cosH = (Math.cos(deg(z)) / (Math.cos(deg(lat)) * Math.cos(decl)) - Math.tan(deg(lat)) * Math.tan(decl)); if (cosH < -1 || cosH > 1) return NaN; return Math.acos(cosH) * 180 / Math.PI; }
        var noonUtc = 720 - 4 * lng - eqtime;
        function morning(z) { var ha = haForZenith(z); return isNaN(ha) ? NaN : noonUtc - 4 * ha + tz * 60; }
        function evening(z) { var ha = haForZenith(z); return isNaN(ha) ? NaN : noonUtc + 4 * ha + tz * 60; }
        var methods = { karachi: [18, 18], mwl: [18, 17], isna: [15, 15], egypt: [19.5, 17.5], makkah: [18.5, -1] };
        var mm = methods[el("ptMethod").value] || methods.karachi;
        var fajr = morning(90 + mm[0]);
        var sunrise = morning(90.833);
        var dhuhr = noonUtc + tz * 60;
        var factor = parseInt(el("ptAsr").value, 10);
        var asrAlt = Math.atan(1 / (factor + Math.tan(deg(Math.abs(lat - decl * 180 / Math.PI))))) * 180 / Math.PI;
        var asr = evening(90 - asrAlt);
        var maghrib = evening(90.833);
        var isha = mm[1] < 0 ? maghrib + 90 : evening(90 + mm[1]);
        var items = [["Fajr", fajr], ["Sunrise", sunrise], ["Dhuhr", dhuhr], ["Asr", asr], ["Maghrib", maghrib], ["Isha", isha]];
        var html = "";
        items.forEach(function (it) { html += "<div class=\"col-6 col-md-4\"><div class=\"border rounded p-3 text-center\"><div class=\"text-muted small\">" + it[0] + "</div><div class=\"fs-5 fw-bold\">" + fmtTime(it[1]) + "</div></div></div>"; });
        el("ptResults").innerHTML = html;
    }
    el("ptCity").addEventListener("change", function () { var v = el("ptCity").value; if (!v) return; var p = v.split(","); el("ptLat").value = p[0]; el("ptLng").value = p[1]; calc(); });
    document.querySelectorAll(".pt-in").forEach(function (f) { f.addEventListener("input", calc); f.addEventListener("change", calc); });
    el("ptDate").value = new Date().toISOString().slice(0, 10);
    calc();
})();
</script>
@endsection
