@extends('layouts.app')

@section('title', 'Mudarabah Profit Calculator — Free Online Tool')
@section('meta_description', 'Enter the capital and profit ratio, and find the shares of rabb-ul-maal and mudarib.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Mudarabah Profit Calculator</h1>
            <p class="lead small text-muted">In a mudarabah, one side (rabb-ul-maal) gives the capital and the other side (mudarib) runs the business with their work. Profit is shared by a ratio fixed in advance — enter that ratio and the actual profit.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="mdCapital">Capital (Rs)</label><input type="number" class="form-control md-in" id="mdCapital" value="1000000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="mdProfit">Profit earned (Rs) — write minus if there is a loss</label><input type="number" class="form-control md-in" id="mdProfit" value="200000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="mdMudaribPct">Mudarib share of profit (%)</label><input type="number" class="form-control md-in" id="mdMudaribPct" value="40" step="any"></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="mdMsg"></div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-3"><div class="text-muted small">Mudarib profit share</div><div class="fs-5 fw-bold" id="mdMudarib">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Rabb-ul-maal profit share</div><div class="fs-5 fw-bold" id="mdRabb">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Capital returned</div><div class="fs-5 fw-bold" id="mdCapBack">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Rabb-ul-maal total receives</div><div class="fs-5 fw-bold" id="mdTotal">—</div></div>
            </div><p class="small text-muted mb-0 mt-2" id="mdNote"></p></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the capital that rabb-ul-maal invested.</li><li>Enter the actual profit (write a minus number if there is a loss).</li><li>Enter the agreed percent of the mudarib — both shares will be shown.</li></ol>
            <p class="small text-muted mb-0">Note: Under mudarabah rules, loss falls on the capital (the mudarib loses only their work), except when the loss is due to the mudarib's negligence or violation. The profit ratio should be fixed at the start; this tool is only a worksheet, not an agreement.</p>
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
        var cap = num("mdCapital"), prof = num("mdProfit"), pct = num("mdMudaribPct"), msg = el("mdMsg");
        if (cap <= 0 || pct < 0 || pct > 100) { msg.textContent = "Keep capital positive and mudarib percent between 0 and 100."; msg.classList.remove("d-none"); return; }
        msg.classList.add("d-none");
        if (prof >= 0) {
            var mShare = prof * pct / 100, rShare = prof - mShare;
            el("mdMudarib").textContent = rs(mShare); el("mdRabb").textContent = rs(rShare);
            el("mdCapBack").textContent = rs(cap); el("mdTotal").textContent = rs(cap + rShare);
            el("mdNote").textContent = "Profit shared by the agreed ratio: mudarib " + pct + "%, rabb-ul-maal " + (100 - pct) + "%.";
        } else {
            el("mdMudarib").textContent = rs(0); el("mdRabb").textContent = rs(0);
            el("mdCapBack").textContent = rs(Math.max(0, cap + prof)); el("mdTotal").textContent = rs(Math.max(0, cap + prof));
            el("mdNote").textContent = "In case of loss, no profit is shared — the loss (" + rs(-prof) + ") reduced the capital (rules differ if negligence is proven).";
        }
    }
    document.querySelectorAll(".md-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
