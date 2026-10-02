@extends('layouts.app')

@section('title', 'Cylinder Volume Calculator — Free Online Tool')
@section('meta_description', 'Find the volume and curved surface area of a cylinder from its radius and height.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Cylinder Volume Calculator</h1>
            <p class="lead small text-muted">Find the volume and curved surface area of a cylinder from its radius and height.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="r" class="form-label">Radius r</label><input type="number" class="form-control" id="r" value="5" step="any"></div><div class="col-md-6"><label for="h" class="form-label">Height h</label><input type="number" class="form-control" id="h" value="10" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the radius (half of the diameter) and the height.</li>
                        <li>The volume and curved / total surface area will be shown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> For a tank or drum: if the volume is in cubic feet, multiply by 28.3168 to get liters.</p>
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
        if (r <= 0 || h <= 0) { bad("Enter radius and height correctly."); return; }
        var base = Math.PI * r * r;
        $("res").innerHTML = table([
            ["Base area = pi r^2", fmt(base)],
            ["Volume = pi r^2 h", fmt(base * h)],
            ["Curved surface area = 2 pi r h", fmt(2 * Math.PI * r * h)],
            ["Total surface area = 2 pi r (h + r)", fmt(2 * Math.PI * r * (h + r))],
            ["Base circumference = 2 pi r", fmt(2 * Math.PI * r)]
        ]);
    }
  
    bind(["r", "h"], calc);
    calc();
})();
</script>
@endsection
