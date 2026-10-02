@extends('layouts.app')

@section('title', 'Vehicle Registration Cost Calculator — Free Online Tool')
@section('meta_description', 'Enter your vehicle price and engine cc to estimate registration and number plate costs.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Vehicle Registration Cost Calculator</h1>
                    <p class="lead small text-muted">Enter your vehicle price and engine cc to get an estimate of the total cost, including registration fee, withholding tax, number plate and smart card — all rates are editable.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="vrPrice">Vehicle price (Rs)</label><input type="number" class="form-control" id="vrPrice" value="4500000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vrCc">Engine (cc)</label><input type="number" class="form-control" id="vrCc" value="1300" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vrRegPct">Registration fee (% of price, editable)</label><input type="number" class="form-control" id="vrRegPct" value="2" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vrWhtPct">Withholding tax (% of price, editable)</label><input type="number" class="form-control" id="vrWhtPct" value="2" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vrPlate">Number plate fee (Rs, editable)</label><input type="number" class="form-control" id="vrPlate" value="2000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vrCard">Smart card / registration book (Rs, editable)</label><input type="number" class="form-control" id="vrCard" value="950" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vrMisc">Inspection and misc charges (Rs, editable)</label><input type="number" class="form-control" id="vrMisc" value="1500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vrFiler">Buyer status</label><select class="form-select" id="vrFiler"><option value="1">Filer (standard WHT)</option><option value="2">Non-filer (WHT x2 estimate)</option></select></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="vrOut">Enter your vehicle price — the registration cost will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter your vehicle invoice price and engine cc.</li>
                        <li>Adjust the registration fee and withholding tax percentages to match your province schedule, and choose your filer / non-filer status.</li>
                        <li>See the number plate, smart card and total cost details below.</li>
                    </ol>
                    <p class="small text-muted mb-0">Registration charges vary by province, and filer / non-filer withholding tax changes with every budget — that is why all rates are editable. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup. Official source: your provincial Excise and Taxation department.</p>
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
    function calc() {
        var price = num("vrPrice");
        var reg = price * num("vrRegPct") / 100;
        var wht = price * num("vrWhtPct") / 100 * parseFloat(el("vrFiler").value);
        var total = reg + wht + num("vrPlate") + num("vrCard") + num("vrMisc");
        var out = el("vrOut");
        if (price <= 0) { out.textContent = "Please enter a valid vehicle price."; return; }
        out.innerHTML = "<strong>Total registration cost (estimated): " + money(total) + "</strong><br>Registration fee: " + money(reg) + " &nbsp; Withholding tax: " + money(wht) + " &nbsp; Number plate: " + money(num("vrPlate")) + " &nbsp; Smart card: " + money(num("vrCard")) + " &nbsp; Misc: " + money(num("vrMisc")) + "<br>Total including vehicle price: " + money(price + total);
    }
    ["vrPrice", "vrCc", "vrRegPct", "vrWhtPct", "vrPlate", "vrCard", "vrMisc", "vrFiler"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
