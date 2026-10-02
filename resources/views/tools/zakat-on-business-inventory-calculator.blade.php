@extends('layouts.app')

@section('title', 'Zakat on Business Inventory Calculator — Free Online Tool')
@section('meta_description', 'Calculate zakat on business goods from shop stock, cash, and loans — a free online worksheet.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Zakat on Business Inventory Calculator</h1>
            <p class="lead small text-muted">Value your business stock (inventory) at sale price, add cash and money people owe you, subtract money you owe — a special worksheet for shops and businesses.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="zbStock">Stock / inventory at sale price (Rs)</label><input type="number" class="form-control zb-in" id="zbStock" value="1500000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zbCash">Cash + bank balance (Rs)</label><input type="number" class="form-control zb-in" id="zbCash" value="300000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zbRecv">Receivables — money people owe you (Rs)</label><input type="number" class="form-control zb-in" id="zbRecv" value="200000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zbPay">Payables — money you owe / due bills (Rs)</label><input type="number" class="form-control zb-in" id="zbPay" value="250000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="zbSilverRate">Silver price per tola (Rs) — for nisab</label><input type="number" class="form-control zb-in" id="zbSilverRate" value="0" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-3"><div class="text-muted small">Net zakatable business wealth</div><div class="fs-5 fw-bold" id="zbNet">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Nisab (52.5 tola silver)</div><div class="fs-5 fw-bold" id="zbNisab">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Zakat payable (2.5%)</div><div class="fs-5 fw-bold" id="zbZakat">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Status</div><div class="fs-5 fw-bold" id="zbStatus">—</div></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the stock value at the price you can sell it for (sale/market price) — not the purchase price.</li><li>Add cash, bank balance, and money people owe you; subtract money you owe and due expenses.</li><li>Enter today's silver rate per tola for the nisab check.</li></ol>
            <p class="small text-muted mb-0">Note: This worksheet is for business goods only, separate from the general zakat tool. The shop building, furniture, and machinery (fixed assets) are usually not included in zakat. Zakat is due on wealth held for a full lunar year; ask a mufti for detailed questions. Rates change — verify with the official source before relying on this.</p>
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
        var net = Math.max(0, num("zbStock") + num("zbCash") + num("zbRecv") - num("zbPay"));
        var nisab = 52.5 * num("zbSilverRate");
        el("zbNet").textContent = rs(net);
        el("zbNisab").textContent = nisab > 0 ? rs(nisab) : "Enter the silver rate";
        if (nisab <= 0) { el("zbZakat").textContent = "—"; el("zbStatus").textContent = "Nisab check pending"; return; }
        var above = net >= nisab;
        el("zbZakat").textContent = rs(above ? net * 0.025 : 0);
        el("zbStatus").textContent = above ? "Above nisab — zakat is due" : "Below nisab — no zakat due";
    }
    document.querySelectorAll(".zb-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
