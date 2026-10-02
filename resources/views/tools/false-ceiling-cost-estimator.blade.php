@extends('layouts.app')

@section('title', 'False Ceiling Cost Estimator — Free Online Tool')
@section('meta_description', 'Enter the room area and per sq ft rate to find the total cost of a false ceiling.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">False Ceiling Cost Estimator</h1>
            <p class="lead small text-muted">Enter the room area and per sq ft rate to find the total cost of a false ceiling.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="len" class="form-label">Room length (ft)</label><input type="number" class="form-control" id="len" value="14" step="any"></div><div class="col-md-4"><label for="wid" class="form-label">Room width (ft)</label><input type="number" class="form-control" id="wid" value="12" step="any"></div><div class="col-md-4"><label for="rate" class="form-label">Ceiling rate (Rs per sq ft)</label><input type="number" class="form-control" id="rate" value="220" step="any"></div><div class="col-md-4"><label for="extraPct" class="form-label">Design / border extra (%)</label><input type="number" class="form-control" id="extraPct" value="10" step="any"></div><div class="col-md-4"><label for="lights" class="form-label">Lights / fans points (count)</label><input type="number" class="form-control" id="lights" value="4" step="any"></div><div class="col-md-4"><label for="lightRate" class="form-label">Rate per point (Rs)</label><input type="number" class="form-control" id="lightRate" value="2500" step="any"></div><div class="col-md-4"><label for="fixed" class="form-label">Fixed charges: wiring etc (Rs)</label><input type="number" class="form-control" id="fixed" value="5000" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the room length and width.</li>
                        <li>Enter the per sq ft rate for gypsum or POP ceiling.</li>
                        <li>Include design extra, light points and fixed charges.</li>
                        <li>The total cost will show with a full breakdown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Rates usually include the frame, board and putty; paint and lights are counted separately. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("len"), W = v("wid");
        if (L <= 0 || W <= 0) { bad("Please enter the correct room size."); return; }
        var area = L * W, base = area * v("rate"), extra = base * v("extraPct") / 100;
        var lights = v("lights") * v("lightRate"), total = base + extra + lights + v("fixed");
        $("res").innerHTML = table([
            ["Ceiling area", fmt(area) + " sq ft"],
            ["Base ceiling cost", "Rs " + fmt(base, 0)],
            ["Design extra", "Rs " + fmt(extra, 0)],
            ["Light points cost", "Rs " + fmt(lights, 0)],
            ["Fixed charges", "Rs " + fmt(v("fixed"), 0)],
            ["Total estimated cost", "Rs " + fmt(total, 0)]
        ]);
    }
  
    bind(["len", "wid", "rate", "extraPct", "lights", "lightRate", "fixed"], calc);
    calc();
})();
</script>
@endsection
