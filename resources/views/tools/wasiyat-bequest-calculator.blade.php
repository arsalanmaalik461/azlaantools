@extends('layouts.app')

@section('title', 'Wasiyat Bequest Calculator — Free Online Tool')
@section('meta_description', 'Enter the total estate to find the maximum one-third bequest limit and the remaining inheritance.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Wasiyat Bequest Calculator</h1>
            <p class="lead small text-muted">In Islam, the bequest limit is one-third (1/3) of the estate left after the heirs' shares. Enter the total estate — you will get the maximum allowed bequest amount and the remaining share for the heirs.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="wsEstate">Total estate (Rs)</label><input type="number" class="form-control ws-in" id="wsEstate" value="3000000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="wsDebts">Debts + funeral expenses (Rs)</label><input type="number" class="form-control ws-in" id="wsDebts" value="200000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="wsPlanned">Planned bequest (Rs)</label><input type="number" class="form-control ws-in" id="wsPlanned" value="500000" step="any"></div>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="wsOver"></div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-3"><div class="text-muted small">Net estate (after debts)</div><div class="fs-5 fw-bold" id="wsNet">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Max allowed bequest (1/3)</div><div class="fs-5 fw-bold" id="wsMax">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Allowed from planned</div><div class="fs-5 fw-bold" id="wsAllowed">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Left for heirs</div><div class="fs-5 fw-bold" id="wsHeirs">—</div></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the total value of the estate.</li><li>Subtract debts and funeral expenses — the bequest is always calculated after these.</li><li>Enter your planned bequest — if it is more than one-third, the tool will show how much is allowed, and the extra amount goes to the heirs (unless all heirs agree).</li></ol>
            <p class="small text-muted mb-0">Note: The order is: first funeral expenses, then debts, then the bequest (maximum 1/3), and the rest is divided among the heirs according to faraid. A bequest in favour of an heir is usually not valid without the consent of the other heirs. Consult both a lawyer and a mufti for a legal will and religious details.</p>
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
        var estate = num("wsEstate"), debts = num("wsDebts"), planned = num("wsPlanned");
        var net = Math.max(0, estate - debts);
        var maxB = net / 3, allowed = Math.min(planned, maxB), over = planned - allowed;
        var box = el("wsOver");
        if (over > 0.5) { box.textContent = "Your planned bequest is " + rs(over) + " over the limit — this extra amount will not be valid without the consent of the heirs."; box.classList.remove("d-none"); } else { box.classList.add("d-none"); }
        el("wsNet").textContent = rs(net); el("wsMax").textContent = rs(maxB);
        el("wsAllowed").textContent = rs(allowed); el("wsHeirs").textContent = rs(net - allowed);
    }
    document.querySelectorAll(".ws-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
