@extends('layouts.app')

@section('title', 'Baking Pan Size Converter — Free Online Tool')
@section('meta_description', 'Adjust recipe amounts from one pan size to another, with a volume chart.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Baking Pan Size Converter</h1>
            <p class="lead small text-muted">Adjust recipe amounts from one pan size to another, with a volume chart.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="fShape" class="form-label">Recipe pan shape</label><select class="form-select" id="fShape"><option value="round">Round</option><option value="square">Square</option><option value="rect">Rectangle</option></select></div><div class="col-md-6"><label for="tShape" class="form-label">Your pan shape</label><select class="form-select" id="tShape"><option value="round">Round</option><option value="square">Square</option><option value="rect">Rectangle</option></select></div><div class="col-md-4"><label for="fD1" class="form-label">Recipe pan size 1 (diameter / side / length, in)</label><input type="number" class="form-control" id="fD1" value="9" step="any"></div><div class="col-md-4"><label for="fD2" class="form-label">Recipe pan size 2 (rect width, in)</label><input type="number" class="form-control" id="fD2" value="13" step="any"></div><div class="col-md-4"><label for="fDep" class="form-label">Recipe pan depth (in)</label><input type="number" class="form-control" id="fDep" value="2" step="any"></div><div class="col-md-4"><label for="tD1" class="form-label">Your pan size 1 (in)</label><input type="number" class="form-control" id="tD1" value="8" step="any"></div><div class="col-md-4"><label for="tD2" class="form-label">Your pan size 2 (rect width, in)</label><input type="number" class="form-control" id="tD2" value="8" step="any"></div><div class="col-md-4"><label for="tDep" class="form-label">Your pan depth (in)</label><input type="number" class="form-control" id="tDep" value="2" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the shape and size of the recipe pan and your pan.</li>
                        <li>For a round pan, size 1 = diameter; for a square pan, the side; for a rectangle, both length and width.</li>
                        <li>You get an ingredient multiplier — multiply everything by that number.</li>
                    </ol>
<h3 class="h6 mt-3">Common pan volumes (approx)</h3><table class="table table-sm"><thead><tr><th>Pan</th><th>Volume</th></tr></thead><tbody>
<tr><td>Round 8 x 2 in</td><td>7 cups</td></tr>
<tr><td>Round 9 x 2 in</td><td>8.5 cups</td></tr>
<tr><td>Square 8 x 8 x 2 in</td><td>8.5 cups</td></tr>
<tr><td>Rectangle 9 x 13 x 2 in</td><td>16 cups</td></tr>
<tr><td>Loaf 9 x 5 x 3 in</td><td>8 cups</td></tr>
<tr><td>Bundt 10 in</td><td>12 cups</td></tr>
</tbody></table>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Volume is area x depth. 1 cup = 14.4375 cubic inches. It is safest to fill the pan only up to 2/3.</p>
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

    function vol(shape, d1, d2, dep) {
        var area = 0;
        if (shape === "round") { area = Math.PI * Math.pow(d1 / 2, 2); }
        else if (shape === "square") { area = d1 * d1; }
        else { area = d1 * d2; }
        return area * dep;
    }
    function calc() {
        var fv = vol($("fShape").value, v("fD1"), v("fD2"), v("fDep"));
        var tv = vol($("tShape").value, v("tD1"), v("tD2"), v("tDep"));
        if (fv <= 0 || tv <= 0) { bad("Enter both pan sizes correctly. Size 2 is not needed for round and square pans."); return; }
        var scale = tv / fv;
        var note = scale > 1 ? "Your pan is bigger — keep the baking time a little shorter and check early." : (scale < 1 ? "Your pan is smaller — do not fill the batter above 2/3, time may be a little longer, make cupcakes with the extra batter." : "Both pans are the same — use the recipe as-is.");
        $("res").innerHTML = table([
            ["Recipe pan volume", fmt(fv) + " cubic in (" + fmt(fv / 14.4375) + " cups)"],
            ["Your pan volume", fmt(tv) + " cubic in (" + fmt(tv / 14.4375) + " cups)"],
            ["Multiply every ingredient by", fmtd(scale, 3) + " (" + fmt(scale * 100, 0) + "%)"],
            ["Example: 200 g flour becomes", fmt(200 * scale, 0) + " g"],
            ["Example: 3 eggs become", fmt(3 * scale) + " (round it yourself for practical use)"],
            ["Baking note", note]
        ]);
    }
  
    bind(["fShape", "tShape", "fD1", "fD2", "fDep", "tD1", "tD2", "tDep"], calc);
    calc();
})();
</script>
@endsection
