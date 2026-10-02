@extends('layouts.app')

@section('title', 'PTA Mobile Tax Calculator — Free Online Tool')
@section('meta_description', 'Enter your phone dollar value and Passport or CNIC category to estimate the PTA tax.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">PTA Mobile Tax Calculator</h1>
                    <p class="lead small text-muted">Enter your phone dollar (USD) value and choose the Passport or CNIC category — get an estimate of PTA mobile tax based on the DIRBS slab. All slab rates are editable.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="ptaUsd">Phone value (USD)</label><input type="number" class="form-control" id="ptaUsd" value="250" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaCat">Registration category</label><select class="form-select" id="ptaCat"><option value="P">Passport (traveler)</option><option value="C">CNIC</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="ptaExtra">Additional charges (Rs, editable — other fees such as passport fee)</label><input type="number" class="form-control" id="ptaExtra" value="0" min="0" step="any"></div>
                    </div>
                    <h2 class="h6 mt-4">Slab table (every rate is editable)</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="ptaP0">Slab Up to $30 — Passport (Rs)</label><input type="number" class="form-control pta-slab" id="ptaP0" value="430" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaC0">Slab Up to $30 — CNIC (Rs)</label><input type="number" class="form-control pta-slab" id="ptaC0" value="855" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaP1">Slab $31 to $100 — Passport (Rs)</label><input type="number" class="form-control pta-slab" id="ptaP1" value="3200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaC1">Slab $31 to $100 — CNIC (Rs)</label><input type="number" class="form-control pta-slab" id="ptaC1" value="4323" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaP2">Slab $101 to $200 — Passport (Rs)</label><input type="number" class="form-control pta-slab" id="ptaP2" value="9580" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaC2">Slab $101 to $200 — CNIC (Rs)</label><input type="number" class="form-control pta-slab" id="ptaC2" value="13323" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaP3">Slab $201 to $350 — Passport (Rs)</label><input type="number" class="form-control pta-slab" id="ptaP3" value="12200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaC3">Slab $201 to $350 — CNIC (Rs)</label><input type="number" class="form-control pta-slab" id="ptaC3" value="17850" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaP4">Slab $351 to $500 — Passport (Rs)</label><input type="number" class="form-control pta-slab" id="ptaP4" value="17800" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaC4">Slab $351 to $500 — CNIC (Rs)</label><input type="number" class="form-control pta-slab" id="ptaC4" value="26150" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaP5">Slab Above $500 — Passport (Rs)</label><input type="number" class="form-control pta-slab" id="ptaP5" value="31500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ptaC5">Slab Above $500 — CNIC (Rs)</label><input type="number" class="form-control pta-slab" id="ptaC5" value="46150" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="ptaOut">Enter the phone USD value — the tax estimate will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the phone market value in US dollars (it is written on the box or invoice).</li>
                        <li>Choose the Passport or CNIC category — both have different slab rates.</li>
                        <li>See the applicable slab and total tax estimate below; all slab rates are editable.</li>
                    </ol>
                    <p class="small text-muted mb-0">Slab values are taken from the published DIRBS table. The assessed value of the mobile is decided by PTA / Customs and may differ from the value you enter, and taxes change over time. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup. Official source: PTA DIRBS portal.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) || v < 0 ? 0 : v; }
    function money(n) { return "Rs " + Math.round(n).toLocaleString("en-PK"); }
    var bounds = [30, 100, 200, 350, 500, Infinity];
    var names = ["Up to $30", "$31 to $100", "$101 to $200", "$201 to $350", "$351 to $500", "Above $500"];
    function calc() {
        var usd = num("ptaUsd");
        var cat = el("ptaCat").value;
        var out = el("ptaOut");
        if (usd <= 0) { out.textContent = "Please enter the phone USD value correctly."; return; }
        var idx = 0;
        while (usd > bounds[idx]) { idx++; }
        var tax = num("pta" + cat + idx);
        var total = tax + num("ptaExtra");
        out.innerHTML = "<strong>Estimated PTA tax: " + money(total) + "</strong><br>Applicable slab: " + names[idx] + " (" + (cat === "P" ? "Passport" : "CNIC") + ") — slab tax " + money(tax) + (num("ptaExtra") ? " + additional " + money(num("ptaExtra")) : "") + ". The real tax is confirmed only after entering the IMEI in DIRBS.";
    }
    ["ptaUsd", "ptaCat", "ptaExtra"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    var slabs = document.querySelectorAll(".pta-slab");
    for (var i = 0; i < slabs.length; i++) { slabs[i].addEventListener("input", calc); }
    calc();
})();
</script>
@endsection
