@extends('layouts.app')

@section('title', 'Net Metering Bill Calculator — Free Online Tool')
@section('meta_description', 'Enter import and export units and estimate your net bill on a green meter (net metering).')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Net Metering Bill Calculator</h1>
                    <p class="lead small text-muted">Enter the import and export units from your green meter (net metering) — you will get an instant estimate of net units, import charges, export credit, and the payable bill.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="nmImport">Import units (taken from the grid, kWh)</label><input type="number" class="form-control" id="nmImport" value="450" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="nmExport">Export units (sent to the grid, kWh)</label><input type="number" class="form-control" id="nmExport" value="300" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="nmImportRate">Import rate per unit (Rs, editable)</label><input type="number" class="form-control" id="nmImportRate" value="55" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="nmExportRate">Export rate per unit (Rs, editable)</label><input type="number" class="form-control" id="nmExportRate" value="27" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="nmFixed">Fixed / meter charges (Rs, editable)</label><input type="number" class="form-control" id="nmFixed" value="500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="nmTaxPct">Taxes and surcharges on import amount (%)</label><input type="number" class="form-control" id="nmTaxPct" value="18" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="nmOut">Enter import and export units — the net bill estimate will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the import units and export units from your green meter readings.</li>
                        <li>Adjust the import and export rates according to your DISCO bill — the export rate is usually lower than the import rate.</li>
                        <li>See the estimated net bill, export credit, and — if exports are higher — the carry-forward units.</li>
                    </ol>
                    <p class="small text-muted mb-0">This is only an estimate: an actual net metering bill also includes peak/off-peak units, quarterly adjustments, GST, electricity duty, and your DISCO's specific netting policy (in some areas exports are only netted against off-peak imports). Rates change — verify with the official source (your DISCO bill) before relying on this.</p>
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
        var imp = num("nmImport"), exp = num("nmExport"), ir = num("nmImportRate"), er = num("nmExportRate"), fixed = num("nmFixed"), taxPct = num("nmTaxPct");
        var netUnits = imp - exp;
        var importAmt = imp * ir;
        var exportCredit = exp * er;
        var energyNet = importAmt - exportCredit;
        var taxes = Math.max(0, energyNet) * taxPct / 100;
        var total = energyNet + taxes + fixed;
        var out = el("nmOut");
        var surplus = exp > imp ? ("<br>Exports are higher than imports — " + (exp - imp).toLocaleString("en-US") + " extra units are usually carried forward to the next bill or adjusted as per policy.") : "";
        out.innerHTML = "<strong>Estimated payable bill: " + money(Math.max(0, total)) + "</strong><br>Net units: " + netUnits.toLocaleString("en-US") + " kWh &nbsp; Import charges: " + money(importAmt) + " &nbsp; Export credit: − " + money(exportCredit) + " &nbsp; Taxes (estimate): " + money(taxes) + " &nbsp; Fixed: " + money(fixed) + surplus;
    }
    ["nmImport", "nmExport", "nmImportRate", "nmExportRate", "nmFixed", "nmTaxPct"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
