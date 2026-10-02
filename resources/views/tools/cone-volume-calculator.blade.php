@extends('layouts.app')

@section('title', 'Cone Volume Calculator — Free Online Tool')
@section('meta_description', 'Find the volume, slant height and surface area of a cone')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Cone Volume Calculator</h1>
            <p class="lead small text-muted">Find the volume, slant height and surface area of a cone</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="r" class="form-label">Radius r</label><input type="number" class="form-control" id="r" value="5" step="any"></div><div class="col-md-6"><label for="h" class="form-label">Height h</label><input type="number" class="form-control" id="h" value="12" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the radius and vertical height.</li>
                        <li>Slant height is found automatically with Pythagoras.</li>
                        <li>Volume and both surface areas are shown below.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> A cone's volume is exactly one-third (1/3) of a cylinder with the same radius and height.</p>
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
        var r = v("r"), h = v("h");
        if (r <= 0 || h <= 0) { bad("Please enter a valid radius and height."); return; }
        var slant = Math.sqrt(r * r + h * h);
        $("res").innerHTML = table([
            ["Slant height l = sqrt(r^2 + h^2)", fmt(slant)],
            ["Volume = (1/3) pi r^2 h", fmt(Math.PI * r * r * h / 3)],
            ["Base area = pi r^2", fmt(Math.PI * r * r)],
            ["Lateral surface = pi r l", fmt(Math.PI * r * slant)],
            ["Total surface area = pi r (l + r)", fmt(Math.PI * r * (slant + r))]
        ]);
    }
  
    bind(["r", "h"], calc);
    calc();
})();
</script>
@endsection
