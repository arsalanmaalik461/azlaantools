@extends('layouts.app')

@section('title', 'Hijri Calendar Month Generator — Free Online Tool')
@section('meta_description', 'Make a full calendar view of any Hijri month, with Gregorian dates.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Hijri Calendar Month Generator</h1>
            <p class="lead small text-muted">Choose a Hijri year and month — a calendar grid of the whole month will appear, with the Gregorian date under each Hijri day.</p>
            <div class="row g-3 align-items-end">
                <div class="col-md-4"><label class="form-label" for="hcMonth">Hijri month</label><select class="form-select" id="hcMonth"><option value="1">1 - Muharram</option><option value="2">2 - Safar</option><option value="3">3 - Rabi al-Awwal</option><option value="4">4 - Rabi al-Thani</option><option value="5">5 - Jumada al-Awwal</option><option value="6">6 - Jumada al-Thani</option><option value="7">7 - Rajab</option><option value="8">8 - Shaban</option><option value="9">9 - Ramadan</option><option value="10">10 - Shawwal</option><option value="11">11 - Dhul-Qadah</option><option value="12">12 - Dhul-Hijjah</option></select></div>
                <div class="col-md-4"><label class="form-label" for="hcYear">Hijri year</label><input type="number" class="form-control" id="hcYear" value="1448"></div>
                <div class="col-md-4"><button type="button" class="btn btn-primary w-100" id="hcBtn">Generate calendar</button></div>
            </div>
            <h2 class="h5 mt-4 text-center" id="hcTitle">—</h2>
            <div class="table-responsive"><table class="table table-bordered text-center"><thead><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr></thead><tbody id="hcBody"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose a Hijri month and year.</li><li>Press <strong>Generate calendar</strong>.</li><li>In the grid, the big number is the Hijri day and the small number is that day's Gregorian date.</li></ol>
            <p class="small text-muted mb-0">Note: the calendar is built with the arithmetical (Kuwaiti) Hijri algorithm: odd months have 30 days, even months 29 days, and Dhul-Hijjah has 30 days in a leap year. Real moon dates can differ by 1 day.</p>
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
    var HMONTHS = ["Muharram","Safar","Rabi al-Awwal","Rabi al-Thani","Jumada al-Awwal","Jumada al-Thani","Rajab","Shaban","Ramadan","Shawwal","Dhul-Qadah","Dhul-Hijjah"];
    function gregToJdn(y, m, d) { var a = Math.floor((14 - m) / 12); var y2 = y + 4800 - a; var m2 = m + 12 * a - 3; return d + Math.floor((153 * m2 + 2) / 5) + 365 * y2 + Math.floor(y2 / 4) - Math.floor(y2 / 100) + Math.floor(y2 / 400) - 32045; }
    function jdnToGreg(jd) { var a = jd + 32044; var b = Math.floor((4 * a + 3) / 146097); var c = a - Math.floor(146097 * b / 4); var d2 = Math.floor((4 * c + 3) / 1461); var e = c - Math.floor(1461 * d2 / 4); var m = Math.floor((5 * e + 2) / 153); var day = e - Math.floor((153 * m + 2) / 5) + 1; var month = m + 3 - 12 * Math.floor(m / 10); var year = 100 * b + d2 - 4800 + Math.floor(m / 10); return { y: year, m: month, d: day }; }
    function g2h(y, m, d) {
        var jd = gregToJdn(y, m, d);
        var l = jd - 1948440 + 10632; var n = Math.floor((l - 1) / 10631); l = l - 10631 * n + 354;
        var j = Math.floor((10985 - l) / 5316) * Math.floor((50 * l) / 17719) + Math.floor(l / 5670) * Math.floor((43 * l) / 15238);
        l = l - Math.floor((30 - j) / 15) * Math.floor((17719 * j) / 50) - Math.floor(j / 16) * Math.floor((15238 * j) / 43) + 29;
        var hm = Math.floor((24 * l) / 709); var hd = l - Math.floor((709 * hm) / 24); var hy = 30 * n + j - 30;
        return { d: hd, m: hm, y: hy };
    }
    function h2g(hd, hm, hy) { var jd = Math.floor((11 * hy + 3) / 30) + 354 * hy + 30 * hm - Math.floor((hm - 1) / 2) + hd + 1948440 - 385; return jdnToGreg(jd); }
    function hijriMonthDays(hy, hm) { if (hm % 2 === 1) return 30; if (hm === 12) return ((11 * hy + 14) % 30) < 11 ? 30 : 29; return 29; }
    function fmtGreg(g) { var days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"]; var dt = new Date(g.y, g.m - 1, g.d); return g.d + "/" + g.m + "/" + g.y + " (" + days[dt.getDay()] + ")"; }
    function gen() {
        var hm = parseInt(el("hcMonth").value, 10), hy = parseInt(el("hcYear").value, 10);
        if (!hy || hy < 1) return;
        var days = hijriMonthDays(hy, hm);
        var first = h2g(1, hm, hy);
        var startDow = new Date(first.y, first.m - 1, first.d).getDay();
        el("hcTitle").textContent = HMONTHS[hm - 1] + " " + hy + " AH (" + days + " days) — starts " + fmtGreg(first);
        var html = "", cell = 0, d;
        for (var week = 0; week < 6; week++) {
            html += "<tr>";
            for (var dow = 0; dow < 7; dow++) {
                d = cell - startDow + 1;
                if (d >= 1 && d <= days) { var g = h2g(d, hm, hy); html += "<td><div class=\"fw-bold fs-5\">" + d + "</div><div class=\"small text-muted\">" + g.d + "/" + g.m + "</div></td>"; }
                else html += "<td></td>";
                cell++;
            }
            html += "</tr>";
            if (cell - startDow >= days) break;
        }
        el("hcBody").innerHTML = html;
    }
    el("hcBtn").addEventListener("click", gen);
    el("hcMonth").addEventListener("change", gen); el("hcYear").addEventListener("input", gen);
    gen();
})();
</script>
@endsection
