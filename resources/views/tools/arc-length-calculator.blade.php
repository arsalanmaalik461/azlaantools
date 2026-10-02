@extends('layouts.app')

@section('title', 'Arc Length Calculator — Free Online Tool')
@section('meta_description', 'Find the arc length and chord length of a circle from the angle')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Arc Length Calculator</h1>
            <p class="lead small text-muted">Find the arc length and chord length of a circle from the angle</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="radius" class="form-label">Radius r</label><input type="number" class="form-control" id="radius" value="10" step="any"></div><div class="col-md-6"><label for="angle" class="form-label">Central angle (degrees)</label><input type="number" class="form-control" id="angle" value="60" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the radius.</li>
                        <li>Enter the central angle in degrees.</li>
                        <li>The arc length, chord and sector area will be shown with steps.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Formula: arc length s = r x theta, where theta is in radians. To convert degrees to radians, multiply by pi / 180.</p>
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
        var r = v("radius"), a = v("angle");
        if (r <= 0 || a <= 0 || a > 360) { bad("Enter the radius and angle (1-360 degrees) correctly."); return; }
        var rad = a * Math.PI / 180;
        var arc = r * rad;
        var chord = 2 * r * Math.sin(rad / 2);
        var sector = 0.5 * r * r * rad;
        var circ = 2 * Math.PI * r;
        $("res").innerHTML = table([
            ["Angle in radians", fmtd(rad, 4)],
            ["Arc length (s = r x theta)", fmt(arc)],
            ["Step: s = " + fmt(r) + " x " + fmtd(rad, 4), fmt(arc)],
            ["Chord length (2r sin(theta/2))", fmt(chord)],
            ["Sector area", fmt(sector)],
            ["Full circumference (check)", fmt(circ) + " — arc is " + fmt(a / 360 * 100) + "% of it"]
        ]);
    }
  
    bind(["radius", "angle"], calc);
    calc();
})();
</script>
@endsection
