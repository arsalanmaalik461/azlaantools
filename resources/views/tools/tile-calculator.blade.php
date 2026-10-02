@extends('layouts.app')

@section('title', 'Tile Calculator — Free Online Tool')
@section('meta_description', 'Calculate tiles needed for a floor or wall from area, tile size and wastage.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Tile Calculator</h1>
            <p class="lead small text-muted">Calculate tiles needed for a floor or wall from area, tile size and wastage.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="roomL" class="form-label">Area length (ft)</label><input type="number" class="form-control" id="roomL" value="12" step="any"></div><div class="col-md-4"><label for="roomW" class="form-label">Area width (ft)</label><input type="number" class="form-control" id="roomW" value="10" step="any"></div><div class="col-md-4"><label for="tileL" class="form-label">Tile length (in)</label><input type="number" class="form-control" id="tileL" value="24" step="any"></div><div class="col-md-4"><label for="tileW" class="form-label">Tile width (in)</label><input type="number" class="form-control" id="tileW" value="12" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div><div class="col-md-4"><label for="perBox" class="form-label">Tiles per box</label><input type="number" class="form-control" id="perBox" value="6" step="any"></div><div class="col-md-4"><label for="priceBox" class="form-label">Price per box (Rs)</label><input type="number" class="form-control" id="priceBox" value="2800" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the floor or wall area as length x width.</li>
                        <li>Enter the tile size in inches.</li>
                        <li>Enter wastage — 10% for a straight layout, 15% for diagonal.</li>
                        <li>It will show the number of tiles and boxes.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Count skirting and border tiles separately. Always keep one extra box — matching the same batch later is hard. Rates change — verify with the official source before relying on this.</p>
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
        var a = v("roomL") * v("roomW"), tl = v("tileL"), tw = v("tileW");
        if (a <= 0 || tl <= 0 || tw <= 0 || v("perBox") <= 0) { bad("Enter the area, tile size and box count correctly."); return; }
        var tileArea = (tl * tw) / 144;
        var raw = a / tileArea;
        var tiles = Math.ceil(raw * (1 + v("waste") / 100));
        var boxes = Math.ceil(tiles / v("perBox"));
        $("res").innerHTML = table([
            ["Total area", fmt(a) + " sq ft"],
            ["Tile area", fmt(tileArea, 3) + " sq ft"],
            ["Tiles needed (with wastage)", fmt(tiles, 0)],
            ["Boxes needed", fmt(boxes, 0)],
            ["Estimated cost", "Rs " + fmt(boxes * v("priceBox"), 0)]
        ]);
    }
  
    bind(["roomL", "roomW", "tileL", "tileW", "waste", "perBox", "priceBox"], calc);
    calc();
})();
</script>
@endsection
