@extends('layouts.app')

@section('title', 'Circle Area Calculator — Free Online Tool')
@section('meta_description', 'Calculate a circle area from radius or diameter with formula steps')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Circle Area Calculator</h1>
            <p class="lead small text-muted">Calculate a circle's area from the radius or diameter, with formula steps</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="mode" class="form-label">You have</label><select class="form-select" id="mode"><option value="r">Radius</option><option value="d">Diameter</option><option value="c">Circumference</option></select></div><div class="col-md-6"><label for="val" class="form-label">Value</label><input type="number" class="form-control" id="val" value="10" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select whether you have the radius, diameter or circumference.</li>
                        <li>Enter the value.</li>
                        <li>The area will show with formula steps.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Formula: Area = pi x r^2, where pi = 3.14159... is used.</p>
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
        var mode = $("mode").value, x = v("val");
        if (x <= 0) { bad("Please enter a valid value."); return; }
        var r = mode === "r" ? x : (mode === "d" ? x / 2 : x / (2 * Math.PI));
        var area = Math.PI * r * r;
        $("res").innerHTML = table([
            ["Radius r", fmt(r)],
            ["Diameter d = 2r", fmt(2 * r)],
            ["Step 1: r squared", fmt(r * r)],
            ["Step 2: Area = pi x r squared", fmt(area)],
            ["Circumference = 2 pi r", fmt(2 * Math.PI * r)]
        ]) + "<p class=\"small text-muted mt-2 mb-0\">Units: the area is in square units, all other values are in linear units.</p>";
    }
  
    bind(["mode", "val"], calc);
    calc();
})();
</script>
@endsection
