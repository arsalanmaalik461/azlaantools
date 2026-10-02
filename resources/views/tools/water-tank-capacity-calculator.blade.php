@extends('layouts.app')

@section('title', 'Water Tank Capacity Calculator — Free Online Tool')
@section('meta_description', 'Calculate water tank capacity in liters and gallons from tank dimensions.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Water Tank Capacity Calculator</h1>
            <p class="lead small text-muted">Calculate water tank capacity in liters and gallons from tank dimensions.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="shape" class="form-label">Tank shape</label><select class="form-select" id="shape"><option value="rect">Rectangular / cuboid</option><option value="cyl">Cylindrical</option></select></div><div class="col-md-4"><label for="len" class="form-label">Length (ft)</label><input type="number" class="form-control" id="len" value="4" step="any"></div><div class="col-md-4"><label for="wid" class="form-label">Width (ft)</label><input type="number" class="form-control" id="wid" value="4" step="any"></div><div class="col-md-4"><label for="hgt" class="form-label">Height (ft)</label><input type="number" class="form-control" id="hgt" value="4" step="any"></div><div class="col-md-4"><label for="diam" class="form-label">Diameter (for cylinder, ft)</label><input type="number" class="form-control" id="diam" value="4" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select the tank shape.</li>
                        <li>Enter the inner (internal) dimensions in feet — using the outer size gives a higher estimate.</li>
                        <li>The capacity will be shown in liters, US gallons and UK gallons.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> 1 liter of water = 1 kg, so the full tank weight matters when checking the roof strength. Tanks are usually filled to 90–95%, so the actual usable capacity will be slightly less.</p>
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
        var shape = $("shape").value, ft3 = 0;
        if (shape === "rect") { if (v("len") <= 0 || v("wid") <= 0 || v("hgt") <= 0) { bad("Please enter the length, width and height."); return; } ft3 = v("len") * v("wid") * v("hgt"); }
        else { if (v("diam") <= 0 || v("hgt") <= 0) { bad("Please enter the diameter and height."); return; } var r = v("diam") / 2; ft3 = Math.PI * r * r * v("hgt"); }
        var liters = ft3 * 28.3168;
        $("res").innerHTML = table([
            ["Volume", fmt(ft3) + " ft3"],
            ["Capacity", fmt(liters, 0) + " liters"],
            ["US gallons", fmt(liters / 3.785, 0)],
            ["UK (imperial) gallons", fmt(liters / 4.546, 0)],
            ["Weight of full tank (water)", fmt(liters, 0) + " kg approx"]
        ]);
    }
  
    bind(["shape", "len", "wid", "hgt", "diam"], calc);
    calc();
})();
</script>
@endsection
