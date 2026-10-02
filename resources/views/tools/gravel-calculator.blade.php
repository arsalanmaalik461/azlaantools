@extends('layouts.app')

@section('title', 'Gravel Calculator — Free Online Tool')
@section('meta_description', 'Calculate gravel and crush volume and weight needed for an area and depth.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Gravel Calculator</h1>
            <p class="lead small text-muted">Calculate gravel and crush volume and weight needed for an area and depth.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="len" class="form-label">Length (ft)</label><input type="number" class="form-control" id="len" value="20" step="any"></div><div class="col-md-4"><label for="wid" class="form-label">Width (ft)</label><input type="number" class="form-control" id="wid" value="10" step="any"></div><div class="col-md-4"><label for="dep" class="form-label">Depth (in)</label><input type="number" class="form-control" id="dep" value="3" step="any"></div><div class="col-md-4"><label for="density" class="form-label">Density (tonnes per m3)</label><input type="number" class="form-control" id="density" value="1.6" step="any"></div><div class="col-md-4"><label for="priceTon" class="form-label">Price per tonne (Rs)</label><input type="number" class="form-control" id="priceTon" value="6500" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the area length and width.</li>
                        <li>Enter the gravel / crush depth in inches.</li>
                        <li>Confirm the density (about 1.6 tonnes per m3 for common crush).</li>
                        <li>The volume, weight and cost will be shown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> The layer becomes thinner after compaction — ordering 10% extra is common practice for a deep base. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("len"), W = v("wid"), D = v("dep"), den = v("density");
        if (L <= 0 || W <= 0 || D <= 0 || den <= 0) { bad("Enter correct size and density."); return; }
        var ft3 = L * W * (D / 12), m3 = ft3 / 35.3147, tonnes = m3 * den;
        $("res").innerHTML = table([
            ["Volume", fmt(ft3) + " ft3 (" + fmt(m3) + " m3)"],
            ["Weight", fmt(tonnes) + " tonnes (" + fmt(tonnes * 1000, 0) + " kg)"],
            ["Estimated cost", "Rs " + fmt(tonnes * v("priceTon"), 0)]
        ]);
    }
  
    bind(["len", "wid", "dep", "density", "priceTon"], calc);
    calc();
})();
</script>
@endsection
