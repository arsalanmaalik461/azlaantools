@extends('layouts.app')

@section('title', 'Daylight Hours Calculator — Free Online Tool')
@section('meta_description', 'Calculate total daylight hours and minutes for any place and date.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Daylight Hours Calculator</h1>
                    <p class="lead small text-muted">For gardening, solar planning and photography: get the exact length of day, sunrise and sunset, and how the day compares with the longest and shortest days of the year.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="dlDate">Date</label><input type="date" class="form-control" id="dlDate"></div>
                        <div class="col-md-3"><label class="form-label" for="dlLat">Latitude (e.g. Karachi 24.86)</label><input type="number" class="form-control" id="dlLat" value="24.86" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="dlLng">Longitude (e.g. Karachi 67.00)</label><input type="number" class="form-control" id="dlLng" value="67.00" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="dlTz">UTC offset (Pakistan = 5)</label><input type="number" class="form-control" id="dlTz" value="5" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="dlCity">Quick city</label><select class="form-select" id="dlCity"><option value="">Custom</option><option value="24.86,67.00,5">Karachi</option><option value="31.55,74.35,5">Lahore</option><option value="33.73,73.09,5">Islamabad</option><option value="31.42,73.08,5">Faisalabad</option><option value="25.32,68.37,5">Hyderabad</option><option value="30.16,71.52,5">Multan</option><option value="34.02,71.58,5">Peshawar</option><option value="30.18,66.99,5">Quetta</option><option value="51.51,-0.13,0">London (offset varies with BST)</option><option value="25.20,55.27,4">Dubai</option></select></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="dlOut">Pick a date and location to calculate daylight.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick the date and enter latitude and longitude, or choose a preset city.</li>
                        <li>Set the UTC offset (Pakistan is UTC+5 all year).</li>
                        <li>Read the daylight length, sunrise, sunset, and the comparison with the solstices.</li>
                    </ol>
                    <p class="small text-muted mb-0">Uses the standard NOAA solar calculation, the same math as the sunrise and sunset tool. Day length is measured between sunrise and sunset at the official zenith of 90.833 degrees and excludes twilight; civil twilight adds roughly 25 to 35 minutes of usable light at each end in Pakistan latitudes.</p>
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
    function el(id) { return document.getElementById(id); }
    function parseD(v) { if (!v) { return null; } var p = v.split("-"); var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10)); return isNaN(d.getTime()) ? null : d; }
    function todayStr() { var d = new Date(); function pad(n) { return (n < 10 ? "0" : "") + n; } return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
    function deg2rad(d) { return d * Math.PI / 180; }
    function rad2deg(r) { return r * 180 / Math.PI; }
    function norm360(v) { v = v % 360; return v < 0 ? v + 360 : v; }
    function sunTime(date, lat, lng, tz, isSunrise) {
        var start = new Date(date.getFullYear(), 0, 1);
        var N = Math.round((date - start) / 86400000) + 1;
        var lngHour = lng / 15;
        var t = N + (((isSunrise ? 6 : 18) - lngHour) / 24);
        var M = (0.9856 * t) - 3.289;
        var L = norm360(M + 1.916 * Math.sin(deg2rad(M)) + 0.020 * Math.sin(deg2rad(2 * M)) + 282.634);
        var RA = rad2deg(Math.atan(0.91764 * Math.tan(deg2rad(L))));
        RA = norm360(RA);
        RA += (Math.floor(L / 90) * 90) - (Math.floor(RA / 90) * 90);
        RA = RA / 15;
        var sinDec = 0.39782 * Math.sin(deg2rad(L));
        var cosDec = Math.cos(Math.asin(sinDec));
        var cosH = (Math.cos(deg2rad(90.833)) - (sinDec * Math.sin(deg2rad(lat)))) / (cosDec * Math.cos(deg2rad(lat)));
        if (cosH > 1) { return { polar: "night" }; }
        if (cosH < -1) { return { polar: "day" }; }
        var H = isSunrise ? 360 - rad2deg(Math.acos(cosH)) : rad2deg(Math.acos(cosH));
        H = H / 15;
        var T = H + RA - (0.06571 * t) - 6.622;
        var UT = T - lngHour;
        var local = UT + tz;
        local = ((local % 24) + 24) % 24;
        return { hours: local };
    }
    function fmtHours(h) { var hh = Math.floor(h), mm = Math.floor((h - hh) * 60), ss = Math.round(((h - hh) * 60 - mm) * 60); if (ss === 60) { ss = 0; mm++; } if (mm === 60) { mm = 0; hh++; } function pad(n) { return (n < 10 ? "0" : "") + n; } var h12 = hh % 12 === 0 ? 12 : hh % 12; return pad(hh) + ":" + pad(mm) + " (" + h12 + ":" + pad(mm) + " " + (hh < 12 ? "AM" : "PM") + ")"; }
    function fmtDur(hours) { var totalMin = Math.round(hours * 60); return Math.floor(totalMin / 60) + " h " + (totalMin % 60) + " min"; }
    el("dlDate").value = todayStr();
    el("dlCity").addEventListener("change", function () { var v = el("dlCity").value; if (!v) { return; } var p = v.split(","); el("dlLat").value = p[0]; el("dlLng").value = p[1]; el("dlTz").value = p[2]; calc(); });
    function dayLenFor(date, lat, lng, tz) { var sr = sunTime(date, lat, lng, tz, true), ss = sunTime(date, lat, lng, tz, false); if (sr.polar) { return sr.polar === "day" ? 24 : 0; } var d = ss.hours - sr.hours; return d < 0 ? d + 24 : d; }
    function calc() {
        var date = parseD(el("dlDate").value);
        var lat = parseFloat(el("dlLat").value), lng = parseFloat(el("dlLng").value), tz = parseFloat(el("dlTz").value);
        var out = el("dlOut");
        if (!date || isNaN(lat) || isNaN(lng) || isNaN(tz) || lat < -90 || lat > 90) { out.textContent = "Please enter a valid date and location."; return; }
        var sr = sunTime(date, lat, lng, tz, true), ss = sunTime(date, lat, lng, tz, false);
        if (sr.polar) { out.textContent = sr.polar === "day" ? "24 hours of daylight on this date (midnight sun)." : "No daylight on this date (polar night)."; return; }
        var dl = dayLenFor(date, lat, lng, tz);
        var y = date.getFullYear();
        var longest = dayLenFor(new Date(y, 5, 21), lat, lng, tz);
        var shortest = dayLenFor(new Date(y, 11, 21), lat, lng, tz);
        out.innerHTML = "<strong>Daylight:</strong> " + fmtDur(dl) + " &nbsp; (sunrise " + fmtHours(sr.hours) + ", sunset " + fmtHours(ss.hours) + ")<br>Longest day this year here: " + fmtDur(longest) + " (21 June) &nbsp; Shortest day: " + fmtDur(shortest) + " (21 December) &nbsp; This date gets " + (dl / longest * 100).toFixed(1) + "% of the maximum daylight.";
    }
    ["dlDate", "dlLat", "dlLng", "dlTz"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
