@extends('layouts.app')

@section('title', 'Ramadan Countdown Calculator — Free Online Tool')
@section('meta_description', 'See how many days are left until Ramadan starts, with the expected date.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Ramadan Countdown Calculator</h1>
            <p class="lead small text-muted">How many days are left until the next Ramadan (1 Ramadan) starts — with the expected Gregorian date and Hijri year.</p>
            <div class="border rounded p-4 text-center">
                <div class="text-muted small">Until the next Ramadan starts</div>
                <div class="display-4 fw-bold" id="rmDays">—</div>
                <div class="small text-muted">days left</div>
                <div class="fs-5 fw-semibold mt-2" id="rmDate">—</div>
                <p class="small text-muted mt-2 mb-0" id="rmToday"></p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>As soon as you open the page, the Ramadan countdown from today's date appears.</li><li>If Ramadan is going on, the screen shows a note about it, and the countdown moves to the next Ramadan.</li></ol>
            <p class="small text-muted mb-0">Note: the expected date is calculated from the arithmetical (Kuwaiti) Hijri calendar. The real start of Ramadan depends on the moon sighting — the actual date may differ by 1 or 2 days; the official announcement is final.</p>
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
    function run() {
        var now = new Date(); var y = now.getFullYear(), m = now.getMonth() + 1, d = now.getDate();
        var jdn = gregToJdn(y, m, d); var h = g2h(y, m, d);
        el("rmToday").textContent = "Today: " + d + "/" + m + "/" + y + " — Hijri: " + h.d + " " + HMONTHS[h.m - 1] + " " + h.y + " AH";
        if (h.m === 9) { el("rmDays").textContent = "Ramadan is ongoing"; el("rmDate").textContent = "Today is " + h.d + " Ramadan " + h.y + " AH — Mubarak!"; return; }
        for (var i = 0; i < 400; i++) {
            var g = jdnToGreg(jdn + i); var hh = g2h(g.y, g.m, g.d);
            if (hh.m === 9 && hh.d === 1) { el("rmDays").textContent = i; el("rmDate").textContent = "Expected first fast: " + g.d + "/" + g.m + "/" + g.y + " — 1 Ramadan " + hh.y + " AH"; return; }
        }
    }
    run();
})();
</script>
@endsection
