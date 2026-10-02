@extends('layouts.app')

@section('title', 'Fertilizer Calculator — Free Online Tool')
@section('meta_description', 'Calculate fertilizer amount needed for a lawn or field from area and dose rate.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fertilizer Calculator</h1>
            <p class="lead small text-muted">Calculate fertilizer amount needed for a lawn or field from area and dose rate.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="areaVal" class="form-label">Area</label><input type="number" class="form-control" id="areaVal" value="1" step="any"></div><div class="col-md-4"><label for="areaUnit" class="form-label">Area unit</label><select class="form-select" id="areaUnit"><option value="43560">Acre</option><option value="5445">Kanal</option><option value="272.25">Marla</option><option value="107639">Hectare</option><option value="1">Sq ft</option><option value="10.7639">Sq meter</option></select></div><div class="col-md-4"><label for="dose" class="form-label">Dose (kg per acre)</label><input type="number" class="form-control" id="dose" value="50" step="any"></div><div class="col-md-4"><label for="bagKg" class="form-label">Bag size (kg)</label><input type="number" class="form-control" id="bagKg" value="50" step="any"></div><div class="col-md-4"><label for="priceBag" class="form-label">Price per bag (Rs)</label><input type="number" class="form-control" id="priceBag" value="4500" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select your area and its unit.</li>
                        <li>Enter the dose in kg per acre (from the bag or company guidance).</li>
                        <li>Enter the bag size.</li>
                        <li>The total fertilizer and bag count will appear.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> A high dose can burn both lawn and crops — always treat the product label dose as final. Also confirm the timing before or after rain from the label. Rates change — verify with the official source before relying on this.</p>
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
    function $(id) { return document.getElementById(id); }
    function v(id) { var n = parseFloat($(id).value); return isFinite(n) ? n : 0; }
    function fmt(n, d) { if (!isFinite(n)) { return "—"; } if (d === undefined) { d = 2; } return n.toLocaleString("en-US", { maximumFractionDigits: d }); }
    function fmtd(n, d) { if (!isFinite(n)) { return "—"; } return n.toLocaleString("en-US", { maximumFractionDigits: d, minimumFractionDigits: d }); }
    function table(rowsArr) { var h = "<table class=\"table table-sm align-middle mb-0\"><tbody>"; rowsArr.forEach(function (r) { h += "<tr><td>" + r[0] + "</td><td class=\"text-end fw-semibold\">" + r[1] + "</td></tr>"; }); return h + "</tbody></table>"; }
    function bad(msg) { $("res").innerHTML = "<span class=\"text-danger fw-semibold\">" + msg + "</span>"; }
    function parseList(id) { return $(id).value.split(/[\s,;]+/).map(function (x) { return parseFloat(x); }).filter(function (x) { return isFinite(x); }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = $(id); if (el) { el.addEventListener("input", fn); el.addEventListener("change", fn); } }); }

    function calc() {
        var areaSqft = v("areaVal") * v("areaUnit");
        var dose = v("dose"), bagKg = v("bagKg");
        if (areaSqft <= 0 || dose <= 0 || bagKg <= 0) { bad("Please enter valid area, dose and bag size."); return; }
        var acres = areaSqft / 43560;
        var kg = dose * acres;
        var bags = kg / bagKg;
        $("res").innerHTML = table([
            ["Area", fmt(areaSqft, 0) + " sq ft (" + fmt(acres) + " acre)"],
            ["Fertilizer needed", fmt(kg) + " kg"],
            ["Bags needed", fmt(bags) + " — buy " + fmt(Math.ceil(bags), 0)],
            ["Estimated cost", "Rs " + fmt(Math.ceil(bags) * v("priceBag"), 0)]
        ]);
    }
  
    bind(["areaVal", "areaUnit", "dose", "bagKg", "priceBag"], calc);
    calc();
})();
</script>
@endsection
