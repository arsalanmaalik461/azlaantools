@extends('layouts.app')

@section('title', 'Plot Rate Calculator — Free Online Tool')
@section('meta_description', 'Enter the total plot price and size to get per marla and per sq ft rates.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Plot Rate Calculator</h1>
                    <p class="lead small text-muted">Enter the total plot price and size — get per marla, per kanal, per square foot and per square yard rates instantly, so you can compare two plots correctly.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="plPrice">Total plot price (Rs)</label><input type="number" class="form-control" id="plPrice" value="15000000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="plSize">Plot size</label><input type="number" class="form-control" id="plSize" value="10" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="plUnit">Size unit</label><select class="form-select" id="plUnit"><option value="marla">Marla</option><option value="kanal">Kanal</option><option value="sqft">Square feet</option><option value="sqyd">Square yard (gaz)</option><option value="acre">Acre</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="plMarla">1 marla = sq ft (editable)</label><input type="number" class="form-control" id="plMarla" value="272.25" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="plOut">Enter the price and size to see the rates.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the total demanded price of the plot in Rupees.</li>
                        <li>Choose the plot size and its unit (marla, kanal, sq ft, gaz or acre).</li>
                        <li>Compare the per marla, per kanal, per sq ft and per gaz rates.</li>
                    </ol>
                    <p class="small text-muted mb-0">Default 1 marla = 272.25 sq ft (Punjab standard); in some areas a 225 sq ft or 250 sq ft marla is also used, so the field is editable. 1 kanal = 20 marla, 1 acre = 8 kanal, 1 sq yard = 9 sq ft.</p>
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
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function money(n) { return "Rs " + Math.round(n).toLocaleString("en-PK"); }
    function calc() {
        var price = num("plPrice"), size = num("plSize"), marlaSqft = num("plMarla");
        var out = el("plOut");
        if (price <= 0 || size <= 0 || marlaSqft <= 0) { out.textContent = "Please enter the correct price, size and marla size."; return; }
        var unitSqft = { marla: marlaSqft, kanal: 20 * marlaSqft, sqft: 1, sqyd: 9, acre: 160 * marlaSqft };
        var totalSqft = size * unitSqft[el("plUnit").value];
        var perSqft = price / totalSqft;
        out.innerHTML = "<strong>Per sq ft:</strong> " + money(perSqft) + " &nbsp; <strong>Per marla:</strong> " + money(perSqft * marlaSqft) + " &nbsp; <strong>Per kanal:</strong> " + money(perSqft * 20 * marlaSqft) + " &nbsp; <strong>Per sq yard:</strong> " + money(perSqft * 9) + "<br>Total area: " + totalSqft.toLocaleString("en-US") + " sq ft = " + (totalSqft / marlaSqft).toFixed(2) + " marla = " + (totalSqft / (20 * marlaSqft)).toFixed(3) + " kanal.";
    }
    ["plPrice", "plSize", "plUnit", "plMarla"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
