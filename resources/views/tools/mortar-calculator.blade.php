@extends('layouts.app')

@section('title', 'Mortar Calculator — Free Online Tool')
@section('meta_description', 'Calculate mortar volume and cement sand amounts for brick and block laying.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Mortar Calculator</h1>
            <p class="lead small text-muted">Calculate mortar volume and cement sand amounts for brick and block laying.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="wetVol" class="form-label">Wet mortar volume (ft3)</label><input type="number" class="form-control" id="wetVol" value="100" step="any"></div><div class="col-md-4"><label for="mixC" class="form-label">Cement part</label><input type="number" class="form-control" id="mixC" value="1" step="any"></div><div class="col-md-4"><label for="mixS" class="form-label">Sand part</label><input type="number" class="form-control" id="mixS" value="4" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="5" step="any"></div><div class="col-md-4"><label for="waterBag" class="form-label">Water per bag (liters)</label><input type="number" class="form-control" id="waterBag" value="27" step="any"></div><div class="col-md-4"><label for="priceBag" class="form-label">Price per cement bag (Rs)</label><input type="number" class="form-control" id="priceBag" value="1250" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the required wet mortar volume in ft3.</li>
                        <li>Enter the mix ratio (common brick mortar 1:4 or 1:6).</li>
                        <li>Enter the wastage.</li>
                        <li>The cement bags and sand amounts will be shown below.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Mortar shrinks as it dries, so dry volume is calculated from wet volume with a 1.33 factor. You can also find a wall's mortar volume with the brick calculator. Rates change — verify with the official source before relying on this.</p>
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
        var wet = v("wetVol"), mc = v("mixC"), ms = v("mixS"), waste = v("waste"), sum = mc + ms;
        if (wet <= 0 || sum <= 0) { bad("Enter a correct volume and mix ratio."); return; }
        var dry = wet * 1.33 * (1 + waste / 100);
        var cemVol = dry * mc / sum;
        var bags = cemVol * 1440 / (35.3147 * 50);
        var sand = dry * ms / sum;
        $("res").innerHTML = table([
            ["Dry mortar volume (wet x 1.33, with wastage)", fmt(dry) + " ft3"],
            ["Cement bags (50 kg)", fmt(bags) + " — buy " + fmt(Math.ceil(bags), 0)],
            ["Sand", fmt(sand) + " ft3"],
            ["Water (approx)", fmt(bags * v("waterBag"), 0) + " liters"],
            ["Cement cost", "Rs " + fmt(Math.ceil(bags) * v("priceBag"), 0)]
        ]);
    }
  
    bind(["wetVol", "mixC", "mixS", "waste", "waterBag", "priceBag"], calc);
    calc();
})();
</script>
@endsection
