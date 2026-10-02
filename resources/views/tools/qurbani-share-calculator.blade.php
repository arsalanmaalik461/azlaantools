@extends('layouts.app')

@section('title', 'Qurbani Share Calculator — Free Online Tool')
@section('meta_description', 'Enter the animal price, split it into 7 shares, and find the price and costs of each share.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Qurbani Share Calculator</h1>
            <p class="lead small text-muted">Enter the animal price and extra costs — you will get the price of each share and the total cost of your shares.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="qsType">Animal type</label><select class="form-select qs-in" id="qsType"><option value="7">Cow / Bull / Camel — 7 shares</option><option value="1">Goat / Sheep — 1 share</option></select></div>
                <div class="col-md-4"><label class="form-label" for="qsPrice">Animal price (Rs)</label><input type="number" class="form-control qs-in" id="qsPrice" value="350000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="qsExtra">Extra costs (Rs) — butcher, transport etc.</label><input type="number" class="form-control qs-in" id="qsExtra" value="12000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="qsMine">How many shares are you taking?</label><input type="number" class="form-control qs-in" id="qsMine" value="1" step="1"></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="qsMsg"></div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-3"><div class="text-muted small">Total cost (animal + extra)</div><div class="fs-5 fw-bold" id="qsTotalCost">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Per share</div><div class="fs-5 fw-bold" id="qsPer">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Your cost</div><div class="fs-5 fw-bold" id="qsYours">—</div></div>
                <div class="col-md-3"><div class="text-muted small">Total shares</div><div class="fs-5 fw-bold" id="qsShares">—</div></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose the animal type — big animals have 7 shares, goats have 1.</li><li>Enter the animal price and extra costs for the butcher and transport.</li><li>Enter the number of your shares — your total cost will appear.</li></ol>
            <p class="small text-muted mb-0">Note: confirm the rules about the animal's age and health for Qurbani (cow 2 years, goat 1 year etc.) with a scholar before buying — this tool only calculates share costs.</p>
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
        var shares = parseInt(el("qsType").value, 10), price = num("qsPrice"), extra = num("qsExtra"), mine = Math.floor(num("qsMine")), msg = el("qsMsg");
        if (mine < 1 || mine > shares) { msg.textContent = "Your shares must be between 1 and " + shares + "."; msg.classList.remove("d-none"); } else { msg.classList.add("d-none"); }
        mine = Math.min(Math.max(1, mine), shares);
        var total = price + extra, per = total / shares;
        el("qsTotalCost").textContent = rs(total); el("qsPer").textContent = rs(per);
        el("qsYours").textContent = rs(per * mine); el("qsShares").textContent = shares;
    }
    document.querySelectorAll(".qs-in").forEach(function (f) { f.addEventListener("input", calc); f.addEventListener("change", calc); }); calc();
})();
</script>
@endsection
