@extends('layouts.app')

@section('title', 'Eid Countdown Calculator — Free Online Tool')
@section('meta_description', 'See how many days are left for Eid ul Fitr and Eid ul Adha, with the expected date.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Eid Countdown Calculator</h1>
            <p class="lead small text-muted">How many days are left for the next Eid ul Fitr (1 Shawwal) and Eid ul Adha (10 Dhul-Hijjah) — with the expected date. Dates are calculated from the arithmetical Hijri calendar.</p>
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded p-4 text-center"><div class="text-muted small">Eid ul Fitr — 1 Shawwal</div><div class="display-6 fw-bold" id="eidFitrDays">—</div><div class="small text-muted">days left</div><div class="fw-semibold mt-1" id="eidFitrDate">—</div></div></div>
                <div class="col-md-6"><div class="border rounded p-4 text-center"><div class="text-muted small">Eid ul Adha — 10 Dhul-Hijjah</div><div class="display-6 fw-bold" id="eidAdhaDays">—</div><div class="small text-muted">days left</div><div class="fw-semibold mt-1" id="eidAdhaDate">—</div></div></div>
            </div>
            <p class="small text-muted mt-3 mb-0" id="eidToday"></p>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>When the page opens, the countdown to both next Eids shows from today's date.</li><li>If an Eid has passed recently, the countdown will show for next year's Eid.</li></ol>
            <p class="small text-muted mb-0">Note: The expected dates are based on the arithmetical (Kuwaiti) Hijri calendar. The real Eid is announced after moon sighting, so the real date can differ by 1 or 2 days — the official announcement is final.</p>
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
    function nextHijri(month, day, today, todayJdn) {
        for (var i = 0; i < 430; i++) {
            var g = jdnToGreg(todayJdn + i);
            var h = g2h(g.y, g.m, g.d);
            if (h.m === month && h.d === day) return { days: i, g: g, h: h };
        }
        return null;
    }
    function run() {
        var now = new Date(); var y = now.getFullYear(), m = now.getMonth() + 1, d = now.getDate();
        var jdn = gregToJdn(y, m, d); var h = g2h(y, m, d);
        el("eidToday").textContent = "Today: " + d + "/" + m + "/" + y + " — Hijri: " + h.d + " " + HMONTHS[h.m - 1] + " " + h.y + " AH";
        var f = nextHijri(10, 1, now, jdn), a = nextHijri(12, 10, now, jdn);
        if (f) { el("eidFitrDays").textContent = f.days === 0 ? "Eid is today!" : f.days; el("eidFitrDate").textContent = "Expected: " + fmtGreg(f.g) + " — " + f.h.d + " " + HMONTHS[f.h.m - 1] + " " + f.h.y + " AH"; }
        if (a) { el("eidAdhaDays").textContent = a.days === 0 ? "Eid is today!" : a.days; el("eidAdhaDate").textContent = "Expected: " + fmtGreg(a.g) + " — " + a.h.d + " " + HMONTHS[a.h.m - 1] + " " + a.h.y + " AH"; }
    }
    run();
})();
</script>
@endsection
