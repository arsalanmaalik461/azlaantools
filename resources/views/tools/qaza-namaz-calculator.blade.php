@extends('layouts.app')

@section('title', 'Qaza Namaz Calculator — Free Online Tool')
@section('meta_description', 'Count how many years of prayers are missed, total qaza prayers, and make a daily prayer plan.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Qaza Namaz Calculator</h1>
            <p class="lead small text-muted">How many years of prayers are missed and how many qaza you can pray daily — get the total number of qaza prayers and the expected finish date.</p>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="qzYears">Years missed</label><input type="number" class="form-control qz-in" id="qzYears" value="5" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="qzExtraDays">Extra days</label><input type="number" class="form-control qz-in" id="qzExtraDays" value="0" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="qzPerDay">How many qaza will you pray daily? (1 qaza of each prayer = 1 set)</label><input type="number" class="form-control qz-in" id="qzPerDay" value="1" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="qzWitr">Include Witr?</label><select class="form-select qz-in" id="qzWitr"><option value="1" selected>Yes — 6 per day (5 farz + Witr)</option><option value="0">No — only 5 farz per day</option></select></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-3"><div class="text-muted small">Total missed days</div><div class="fs-4 fw-bold" id="qzDays">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Qaza of each prayer (each)</div><div class="fs-4 fw-bold" id="qzEach">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Total qaza prayers</div><div class="fs-4 fw-bold" id="qzTotal">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Plan will take</div><div class="fs-4 fw-bold" id="qzPlan">—</div></div>
            </div><p class="small text-muted mb-0 mt-2" id="qzFinish"></p></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter how many years of prayers are missed, plus extra days (1 year = 365 days, to keep the math simple).</li><li>Choose whether to include Witr — in the Hanafi school Witr is wajib, so it is usually included in qaza.</li><li>Enter how many you will pray daily and see the finish date — for example, 1 qaza of each prayer along with that daily prayer = 1 set per day.</li></ol>
            <p class="small text-muted mb-0">Note: This calculation is only for planning. For the rulings on prayers missed by mistake or sleep, and those missed on purpose, and the order of qaza, please ask your mufti. Intention and regularity are what matter most — a small but steady plan is best.</p>
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
    function calc() {
        var days = Math.round(num("qzYears") * 365 + num("qzExtraDays"));
        var perDay = Math.max(1, Math.floor(num("qzPerDay")));
        var sets = el("qzWitr").value === "1" ? 6 : 5;
        var planDays = Math.ceil(days / perDay);
        var finish = new Date(); finish.setDate(finish.getDate() + planDays);
        el("qzDays").textContent = fmt(days);
        el("qzEach").textContent = fmt(days) + " (Fajr, Dhuhr, Asr, Maghrib, Isha" + (sets === 6 ? ", Witr" : "") + " — each this many)";
        el("qzTotal").textContent = fmt(days * sets);
        el("qzPlan").textContent = fmt(planDays) + " days (about " + (planDays / 365).toFixed(1) + " years)";
        el("qzFinish").textContent = "At " + perDay + " set per day, expected finish: " + finish.toDateString() + ".";
    }
    document.querySelectorAll(".qz-in").forEach(function (f) { f.addEventListener("input", calc); f.addEventListener("change", calc); }); calc();
})();
</script>
@endsection
