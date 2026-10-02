@extends('layouts.app')

@section('title', 'Ushr Calculator — Free Online Tool')
@section('meta_description', 'Calculate ushr on your crop harvest: 10 percent for rain-fed crops, 5 percent for irrigated crops, 7.5 percent for mixed irrigation.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Ushr Calculator</h1>
            <p class="lead small text-muted">Enter the total harvest value or weight and select the irrigation method — 10% for rain, 5% for tube-well irrigation, 7.5% for mixed.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="usValue">Total harvest value (Rs)</label><input type="number" class="form-control us-in" id="usValue" value="800000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="usIrr">Irrigation type</label><select class="form-select us-in" id="usIrr"><option value="10">Rain / river (no cost) — 10%</option><option value="5">Well / tube well (paid irrigation) — 5%</option><option value="7.5">Mixed (both) — 7.5%</option></select></div>
                <div class="col-md-4"><label class="form-label" for="usQty">Harvest quantity (kg, optional — for nisab check)</label><input type="number" class="form-control us-in" id="usQty" value="0" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="usNisabKg">Nisab quantity (kg) — 5 wasq, editable</label><input type="number" class="form-control us-in" id="usNisabKg" value="653" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-4"><div class="text-muted small">Ushr rate</div><div class="fs-4 fw-bold" id="usRate">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Ushr (in cash)</div><div class="fs-4 fw-bold" id="usAmt">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Ushr (in quantity)</div><div class="fs-4 fw-bold" id="usQtyOut">—</div></div>
            </div><p class="small text-muted mb-0 mt-2" id="usNote"></p></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the total sale value of your harvest.</li><li>Select the irrigation method — 10% (one-tenth) for rain/river, 5% (one-twentieth) for paid irrigation.</li><li>If you also entered weight, the nisab (5 wasq) check and the quantity ushr will also show.</li></ol>
            <p class="small text-muted mb-0">Note: Scholars differ on ushr nisab and some details (for example, vegetables and some crops). The nisab quantity is editable here (the commonly published value is 5 wasq, about 653 kg of grain) — confirm with a mufti for your crop. Rates change — verify with the official source before relying on this.</p>
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
        var v = num("usValue"), rate = parseFloat(el("usIrr").value, 10), qty = num("usQty"), nisab = num("usNisabKg");
        var below = qty > 0 && nisab > 0 && qty < nisab;
        el("usRate").textContent = rate + "%";
        el("usAmt").textContent = below ? rs(0) : rs(v * rate / 100);
        el("usQtyOut").textContent = qty > 0 ? (below ? "0 kg" : fmt(qty * rate / 100) + " kg") : "—";
        el("usNote").textContent = below ? "Quantity is below the nisab (" + nisab + " kg) — according to this sheet no ushr is due (a debated issue, confirm with a mufti)." : (qty > 0 ? "Quantity is above the nisab — pay ushr." : "Enter a quantity to also get the nisab check.");
    }
    document.querySelectorAll(".us-in").forEach(function (f) { f.addEventListener("input", calc); f.addEventListener("change", calc); }); calc();
})();
</script>
@endsection
