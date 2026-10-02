@extends('layouts.app')

@section('title', 'Cube Volume Calculator — Free Online Tool')
@section('meta_description', 'Calculate a cube volume, surface area and diagonal from the side length')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Cube Volume Calculator</h1>
            <p class="lead small text-muted">Find a cube's volume, surface area and diagonal from its side length.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="s" class="form-label">Side a</label><input type="number" class="form-control" id="s" value="5" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter one side of the cube — all sides of a cube are equal.</li>
                        <li>Volume, surface area and both diagonals will show instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Units: volume in cubic units, area in square units and diagonal in linear units.</p>
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
        var s = v("s");
        if (s <= 0) { bad("Enter a valid side."); return; }
        $("res").innerHTML = table([
            ["Volume = a^3", fmt(s * s * s)],
            ["Surface area = 6a^2", fmt(6 * s * s)],
            ["Face diagonal = a sqrt(2)", fmt(s * Math.sqrt(2))],
            ["Space diagonal = a sqrt(3)", fmt(s * Math.sqrt(3))],
            ["Perimeter (12 edges)", fmt(12 * s)],
            ["One face area", fmt(s * s)]
        ]);
    }
  
    bind(["s"], calc);
    calc();
})();
</script>
@endsection
