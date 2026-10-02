@extends('layouts.app')

@section('title', 'Cuboid Volume Calculator — Free Online Tool')
@section('meta_description', 'Calculate the volume, surface area and diagonal of a cuboid (rectangular box)')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Cuboid Volume Calculator</h1>
            <p class="lead small text-muted">Find the volume, surface area and diagonal of a cuboid (rectangular box).</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="l" class="form-label">Length l</label><input type="number" class="form-control" id="l" value="10" step="any"></div><div class="col-md-4"><label for="w" class="form-label">Width w</label><input type="number" class="form-control" id="w" value="6" step="any"></div><div class="col-md-4"><label for="h" class="form-label">Height h</label><input type="number" class="form-control" id="h" value="4" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the cuboid's length, width and height in the same unit.</li>
                        <li>Volume, surface areas and diagonal will show instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> If you enter dimensions in feet, the volume comes in cubic feet — for liters, multiply cubic feet by 28.3168 (the water tank calculator does this by itself).</p>
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
        var l = v("l"), w = v("w"), h = v("h");
        if (l <= 0 || w <= 0 || h <= 0) { bad("Enter all three dimensions correctly."); return; }
        $("res").innerHTML = table([
            ["Volume = l x w x h", fmt(l * w * h)],
            ["Total surface area = 2(lw + wh + lh)", fmt(2 * (l * w + w * h + l * h))],
            ["Lateral surface area = 2h(l + w)", fmt(2 * h * (l + w))],
            ["Space diagonal = sqrt(l^2 + w^2 + h^2)", fmt(Math.sqrt(l * l + w * w + h * h))],
            ["Base area", fmt(l * w)]
        ]);
    }
  
    bind(["l", "w", "h"], calc);
    calc();
})();
</script>
@endsection
