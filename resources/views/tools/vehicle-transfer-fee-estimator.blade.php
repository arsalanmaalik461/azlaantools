@extends('layouts.app')

@section('title', 'Vehicle Transfer Fee Estimator — Free Online Tool')
@section('meta_description', 'Enter your vehicle type and cc to estimate the transfer fee and total charges.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Vehicle Transfer Fee Estimator</h1>
                    <p class="lead small text-muted">Estimate the total for car, jeep or bike transfer fee, withholding component, smart card and service charges — all rates are editable.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="vtType">Vehicle type</label><select class="form-select" id="vtType"><option value="car">Car / Jeep</option><option value="bike">Motorcycle</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="vtCc">Engine (cc)</label><input type="number" class="form-control" id="vtCc" value="1300" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vtFee">Transfer fee (Rs, editable)</label><input type="number" class="form-control" id="vtFee" value="2500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vtWht">Withholding tax (Rs, editable)</label><input type="number" class="form-control" id="vtWht" value="0" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vtCard">Smart card / new book (Rs, editable)</label><input type="number" class="form-control" id="vtCard" value="950" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vtService">Service / agent charges (Rs, editable)</label><input type="number" class="form-control" id="vtService" value="1000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="vtFiler">Buyer status</label><select class="form-select" id="vtFiler"><option value="1">Filer (standard WHT)</option><option value="2">Non-filer (WHT x2 estimate)</option></select></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="vtOut">Choose a vehicle type — the transfer cost will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the vehicle type (car or motorcycle) and engine cc — the commonly published transfer fee fills in automatically.</li>
                        <li>Adjust the withholding tax, smart card and service charges to match your situation.</li>
                        <li>See the total transfer cost details below.</li>
                    </ol>
                    <p class="small text-muted mb-0">Transfer fee changes with the cc slab and province; withholding tax depends on the vehicle value and filer status, so it is kept as a separate editable field. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup. Official source: provincial Excise and Taxation department.</p>
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
    function presetFee() {
        var cc = num("vtCc");
        if (el("vtType").value === "bike") { return 500; }
        if (cc <= 1000) { return 1200; }
        if (cc <= 1300) { return 2500; }
        if (cc <= 1600) { return 4000; }
        if (cc <= 2000) { return 6000; }
        return 10000;
    }
    function applyPreset() { el("vtFee").value = presetFee(); }
    el("vtType").addEventListener("change", function () { applyPreset(); calc(); });
    el("vtCc").addEventListener("change", function () { applyPreset(); calc(); });
    function calc() {
        var wht = num("vtWht") * parseFloat(el("vtFiler").value);
        var total = num("vtFee") + wht + num("vtCard") + num("vtService");
        el("vtOut").innerHTML = "<strong>Total transfer cost (estimated): " + money(total) + "</strong><br>Transfer fee: " + money(num("vtFee")) + " &nbsp; Withholding tax: " + money(wht) + " &nbsp; Smart card: " + money(num("vtCard")) + " &nbsp; Service charges: " + money(num("vtService")) + ". Note: biometric verification and the presence of the seller and buyer are usually required.";
    }
    ["vtFee", "vtWht", "vtCard", "vtService", "vtFiler"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
