@extends('layouts.app')

@section('title', 'Flooring Calculator — Free Online Tool')
@section('meta_description', 'Calculate laminate or wooden flooring packs needed for any room size.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Flooring Calculator</h1>
            <p class="lead small text-muted">Calculate laminate or wooden flooring packs needed for any room size.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="roomL" class="form-label">Room length (ft)</label><input type="number" class="form-control" id="roomL" value="14" step="any"></div><div class="col-md-4"><label for="roomW" class="form-label">Room width (ft)</label><input type="number" class="form-control" id="roomW" value="12" step="any"></div><div class="col-md-4"><label for="packCov" class="form-label">Coverage per pack (sq ft)</label><input type="number" class="form-control" id="packCov" value="24" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Cutting waste (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div><div class="col-md-4"><label for="pricePack" class="form-label">Price per pack (Rs)</label><input type="number" class="form-control" id="pricePack" value="8500" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the room length and width.</li>
                        <li>Enter how many sq ft one pack covers — check the box.</li>
                        <li>Enter the cutting waste (usually 10%).</li>
                        <li>The number of packs and the cost will appear.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> For diagonal or herringbone patterns, waste of up to 15% is better. Buy packs from the same batch so the shade does not vary. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("roomL"), W = v("roomW"), cov = v("packCov"), waste = v("waste");
        if (L <= 0 || W <= 0 || cov <= 0) { bad("Enter room size and pack coverage correctly."); return; }
        var area = L * W, need = area * (1 + waste / 100);
        var packs = Math.ceil(need / cov);
        $("res").innerHTML = table([
            ["Room area", fmt(area) + " sq ft"],
            ["Area with " + fmt(waste, 0) + "% waste", fmt(need) + " sq ft"],
            ["Packs needed", fmt(packs, 0)],
            ["Total coverage bought", fmt(packs * cov) + " sq ft"],
            ["Estimated cost", "Rs " + fmt(packs * v("pricePack"), 0)]
        ]);
    }
  
    bind(["roomL", "roomW", "packCov", "waste", "pricePack"], calc);
    calc();
})();
</script>
@endsection
