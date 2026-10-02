@extends('layouts.app')

@section('title', 'Zakat on Salary Calculator — Free Online Tool')
@section('meta_description', 'Calculate zakat on your monthly salary and yearly savings separately. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Zakat on Salary Calculator</h1>
            <p class="lead small text-muted">A special worksheet for salaried people: estimated yearly savings from your monthly savings, actual bank savings, and 2.5% zakat with a nisab check.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="zsSalary">Monthly salary (Rs)</label><input type="number" class="form-control zs-in" id="zsSalary" value="150000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zsSavePct">Monthly saving (%)</label><input type="number" class="form-control zs-in" id="zsSavePct" value="20" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zsBalance">Current total savings / bank balance (Rs)</label><input type="number" class="form-control zs-in" id="zsBalance" value="600000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zsOther">Other zakatable assets (Rs) — gold, cash, etc.</label><input type="number" class="form-control zs-in" id="zsOther" value="0" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zsDebts">Immediate debts / due payments (Rs)</label><input type="number" class="form-control zs-in" id="zsDebts" value="0" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zsSilver">Silver price per tola (Rs) — for nisab</label><input type="number" class="form-control zs-in" id="zsSilver" value="0" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-3"><div class="text-muted small">Estimated yearly saving</div><div class="fs-5 fw-bold" id="zsYearly">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Net zakatable savings</div><div class="fs-5 fw-bold" id="zsNet">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Nisab (52.5 tola silver)</div><div class="fs-5 fw-bold" id="zsNisab">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Zakat payable (2.5%)</div><div class="fs-5 fw-bold" id="zsZakat">—</div></div>
            </div><p class="small text-muted mb-0 mt-2" id="zsStatus"></p></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter your monthly salary and saving percent — you will see your estimated yearly savings.</li><li>Zakat is calculated on your actual current savings (balance) — enter your real balance, other assets, and due loans.</li><li>Enter today's silver rate per tola for nisab and see your zakat.</li></ol>
            <p class="small text-muted mb-0">Note: Zakat is not on salary itself but on saved wealth that reaches nisab and stays for a full lunar year. This worksheet is for a common salary case; if your savings moved above and below nisab during the year, ask a mufti. Rates change — verify with the official source before relying on this.</p>
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
        var yearly = num("zsSalary") * num("zsSavePct") / 100 * 12;
        var net = Math.max(0, num("zsBalance") + num("zsOther") - num("zsDebts"));
        var nisab = 52.5 * num("zsSilver");
        el("zsYearly").textContent = rs(yearly); el("zsNet").textContent = rs(net);
        el("zsNisab").textContent = nisab > 0 ? rs(nisab) : "Enter the silver rate";
        if (nisab <= 0) { el("zsZakat").textContent = "—"; el("zsStatus").textContent = "Enter the silver rate today for the nisab check."; return; }
        var above = net >= nisab;
        el("zsZakat").textContent = rs(above ? net * 0.025 : 0);
        el("zsStatus").textContent = above ? "Savings are above nisab — pay 2.5% zakat (after a full lunar year)." : "Savings are below nisab — no zakat is due by this calculation.";
    }
    document.querySelectorAll(".zs-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
