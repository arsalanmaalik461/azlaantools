@extends('layouts.app')

@section('title', 'Hijri Age Calculator — Free Online Tool')
@section('meta_description', 'Find your real age in Hijri years from your date of birth.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Hijri Age Calculator</h1>
            <p class="lead small text-muted">Enter your date of birth — your age in Hijri years, months and days will appear, along with a comparison to your Gregorian age.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="haDob">Date of birth</label><input type="date" class="form-control" id="haDob"></div>
                <div class="col-md-6"><label class="form-label" for="haToday">Age at date</label><input type="date" class="form-control" id="haToday"></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="haMsg"></div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-4"><div class="text-muted small">Hijri age</div><div class="fs-4 fw-bold" id="haHijri">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Gregorian age</div><div class="fs-4 fw-bold" id="haGreg">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Date of birth (Hijri)</div><div class="fs-5 fw-bold" id="haDobH">—</div></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose your date of birth.</li><li>The second date is today by default — you can change it.</li><li>See your Hijri and Gregorian ages side by side.</li></ol>
            <p class="small text-muted mb-0">Note: the Hijri year is about 11 days shorter than the Gregorian year, so your Hijri age always looks a little higher. The conversion uses the arithmetical (Kuwaiti) calendar; the real Hijri date can differ by 1 day because of the moon.</p>
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
    function parseDateVal(id) { var v = el(id).value; if (!v) return null; var p = v.split("-"); return { y: +p[0], m: +p[1], d: +p[2] }; }
    function calc() {
        var dob = parseDateVal("haDob"), at = parseDateVal("haToday"), msg = el("haMsg");
        if (!dob || !at) { msg.textContent = "Please choose both dates."; msg.classList.remove("d-none"); return; }
        var jdDob = gregToJdn(dob.y, dob.m, dob.d), jdAt = gregToJdn(at.y, at.m, at.d);
        if (jdAt < jdDob) { msg.textContent = "The second date is before the date of birth."; msg.classList.remove("d-none"); return; }
        msg.classList.add("d-none");
        var hb = g2h(dob.y, dob.m, dob.d), ha = g2h(at.y, at.m, at.d);
        el("haDobH").textContent = hb.d + " " + HMONTHS[hb.m - 1] + " " + hb.y + " AH";
        var hy = ha.y - hb.y, hm = ha.m - hb.m, hd = ha.d - hb.d;
        if (hd < 0) { hm--; hd += hijriMonthDays(ha.m === 1 ? ha.y - 1 : ha.y, ha.m === 1 ? 12 : ha.m - 1); }
        if (hm < 0) { hy--; hm += 12; }
        el("haHijri").textContent = hy + " years, " + hm + " months, " + hd + " days";
        var gy = at.y - dob.y, gm = at.m - dob.m, gd = at.d - dob.d;
        if (gd < 0) { gm--; gd += new Date(at.y, at.m - 1, 0).getDate(); }
        if (gm < 0) { gy--; gm += 12; }
        el("haGreg").textContent = gy + " years, " + gm + " months, " + gd + " days";
    }
    var now = new Date();
    el("haToday").value = now.toISOString().slice(0, 10);
    el("haDob").addEventListener("change", calc); el("haToday").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
