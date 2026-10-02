@extends('layouts.app')

@section('title', 'Concrete Calculator — Free Online Tool')
@section('meta_description', 'Calculate concrete volume and cement bags for slabs, beams and footings.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Concrete Calculator</h1>
            <p class="lead small text-muted">Calculate concrete volume and cement bags for slabs, beams and footings.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="len" class="form-label">Length (ft)</label><input type="number" class="form-control" id="len" value="10" step="any"></div><div class="col-md-4"><label for="wid" class="form-label">Width (ft)</label><input type="number" class="form-control" id="wid" value="10" step="any"></div><div class="col-md-4"><label for="dep" class="form-label">Thickness / depth (in)</label><input type="number" class="form-control" id="dep" value="4" step="any"></div><div class="col-md-4"><label for="mixC" class="form-label">Mix cement part (M15 = 1)</label><input type="number" class="form-control" id="mixC" value="1" step="any"></div><div class="col-md-4"><label for="mixS" class="form-label">Mix sand part (M15 = 2)</label><input type="number" class="form-control" id="mixS" value="2" step="any"></div><div class="col-md-4"><label for="mixA" class="form-label">Mix aggregate part (M15 = 4)</label><input type="number" class="form-control" id="mixA" value="4" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="5" step="any"></div><div class="col-md-4"><label for="waterBag" class="form-label">Water per bag (liters)</label><input type="number" class="form-control" id="waterBag" value="27" step="any"></div><div class="col-md-4"><label for="priceBag" class="form-label">Price per cement bag (Rs)</label><input type="number" class="form-control" id="priceBag" value="1250" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the length, width and depth (in inches) of the slab, beam or footing.</li>
                        <li>Enter the mix ratio — M15 = 1:2:4, M20 = 1:1.5:3, M25 = 1:1:2.</li>
                        <li>Enter the wastage.</li>
                        <li>The full breakdown of cement bags, sand, crush and water will appear.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> The standard 1.54 factor is used for dry volume. This is based on a 50 kg cement bag and a density of 1440 kg per m3. For structural work, always follow the design mix of your engineer. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("len"), W = v("wid"), D = v("dep");
        var mc = v("mixC"), ms = v("mixS"), ma = v("mixA"), waste = v("waste");
        var sum = mc + ms + ma;
        if (L <= 0 || W <= 0 || D <= 0 || sum <= 0) { bad("Please enter the size and mix ratio correctly."); return; }
        var wet = L * W * (D / 12);
        var wetM3 = wet / 35.3147;
        var dry = wet * 1.54;
        var cemVol = dry * mc / sum;
        var bags = cemVol * 1440 / (35.3147 * 50);
        var bagsBuy = Math.ceil(bags * (1 + waste / 100));
        var sand = dry * ms / sum, agg = dry * ma / sum;
        $("res").innerHTML = table([
            ["Wet concrete volume", fmt(wet) + " ft3 (" + fmt(wetM3) + " m3)"],
            ["Dry material volume (wet x 1.54)", fmt(dry) + " ft3"],
            ["Cement bags (50 kg)", fmt(bags) + " — buy " + fmt(bagsBuy, 0) + " with wastage"],
            ["Sand", fmt(sand) + " ft3"],
            ["Aggregate / crush", fmt(agg) + " ft3"],
            ["Water (approx)", fmt(bags * v("waterBag"), 0) + " liters"],
            ["Cement cost", "Rs " + fmt(bagsBuy * v("priceBag"), 0)]
        ]);
    }
  
    bind(["len", "wid", "dep", "mixC", "mixS", "mixA", "waste", "waterBag", "priceBag"], calc);
    calc();
})();
</script>
@endsection
