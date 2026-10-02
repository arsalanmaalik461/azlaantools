@extends('layouts.app')

@section('title', 'Roof Area Calculator — Free Online Tool')
@section('meta_description', 'Calculate true roof surface area from building size and roof pitch angle.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Roof Area Calculator</h1>
            <p class="lead small text-muted">Calculate true roof surface area from building size and roof pitch angle.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="bL" class="form-label">Building length (ft)</label><input type="number" class="form-control" id="bL" value="40" step="any"></div><div class="col-md-4"><label for="bW" class="form-label">Building width (ft)</label><input type="number" class="form-control" id="bW" value="30" step="any"></div><div class="col-md-4"><label for="pitch" class="form-label">Roof pitch angle (degrees)</label><input type="number" class="form-control" id="pitch" value="30" step="any"></div><div class="col-md-4"><label for="overhang" class="form-label">Overhang on all sides (in)</label><input type="number" class="form-control" id="overhang" value="12" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the building length and width.</li>
                        <li>Enter the roof pitch angle — take it from the design or measure it on the existing roof.</li>
                        <li>Enter the overhang (eaves) on all sides in inches.</li>
                        <li>The true sloped area, including wastage, appears automatically.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> This formula gives a good estimate for both gable and hip roofs because their sloped area equals plan area x slope factor. For complex roofs (with many valleys), split the roof into sections and calculate each one.</p>
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
        var L = v("bL"), W = v("bW"), ang = v("pitch"), ov = v("overhang");
        if (L <= 0 || W <= 0 || ang < 0 || ang >= 85) { bad("Enter the building size and pitch (0-84 degrees) correctly."); return; }
        var planL = L + 2 * (ov / 12), planW = W + 2 * (ov / 12);
        var plan = planL * planW;
        var factor = 1 / Math.cos(ang * Math.PI / 180);
        var roof = plan * factor;
        var rise12 = Math.tan(ang * Math.PI / 180) * 12;
        $("res").innerHTML = table([
            ["Plan area (including overhang)", fmt(plan) + " sq ft"],
            ["Slope factor (1 / cos angle)", fmt(factor, 4)],
            ["Pitch as rise per 12 in run", fmt(rise12) + " in"],
            ["True roof area", fmt(roof) + " sq ft"],
            ["Roof area with " + fmt(v("waste"), 0) + "% waste", fmt(roof * (1 + v("waste") / 100)) + " sq ft"]
        ]);
    }

    bind(["bL", "bW", "pitch", "overhang", "waste"], calc);
    calc();
})();
</script>
@endsection
