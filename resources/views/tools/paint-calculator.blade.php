@extends('layouts.app')

@section('title', 'Paint Calculator — Free Online Tool')
@section('meta_description', 'Calculate wall paint needed in liters from room size minus doors and windows.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Paint Calculator</h1>
            <p class="lead small text-muted">Calculate wall paint needed in liters from room size minus doors and windows.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="roomL" class="form-label">Room length (ft)</label><input type="number" class="form-control" id="roomL" value="14" step="any"></div><div class="col-md-4"><label for="roomW" class="form-label">Room width (ft)</label><input type="number" class="form-control" id="roomW" value="12" step="any"></div><div class="col-md-4"><label for="roomH" class="form-label">Room height (ft)</label><input type="number" class="form-control" id="roomH" value="10" step="any"></div><div class="col-md-4"><label for="ceiling" class="form-label">Paint the ceiling?</label><select class="form-select" id="ceiling"><option value="1">Yes</option><option value="0">No</option></select></div><div class="col-md-4"><label for="doors" class="form-label">Doors (count)</label><input type="number" class="form-control" id="doors" value="2" step="any"></div><div class="col-md-4"><label for="doorArea" class="form-label">Area per door (sq ft)</label><input type="number" class="form-control" id="doorArea" value="20" step="any"></div><div class="col-md-4"><label for="wins" class="form-label">Windows (count)</label><input type="number" class="form-control" id="wins" value="2" step="any"></div><div class="col-md-4"><label for="winArea" class="form-label">Area per window (sq ft)</label><input type="number" class="form-control" id="winArea" value="12" step="any"></div><div class="col-md-4"><label for="coats" class="form-label">Paint coats</label><input type="number" class="form-control" id="coats" value="2" step="any"></div><div class="col-md-4"><label for="coverage" class="form-label">Coverage per liter per coat (sq ft)</label><input type="number" class="form-control" id="coverage" value="140" step="any"></div><div class="col-md-4"><label for="primerCov" class="form-label">Primer coverage per liter (sq ft)</label><input type="number" class="form-control" id="primerCov" value="160" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div><div class="col-md-4"><label for="priceLiter" class="form-label">Paint price (Rs per liter)</label><input type="number" class="form-control" id="priceLiter" value="1800" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the room length, width and height.</li>
                        <li>Enter the number and size of doors and windows — this area will be subtracted.</li>
                        <li>Enter the number of coats and the paint coverage (written on the paint box).</li>
                        <li>Paint and primer will show in liters.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Coverage depends on paint quality and how much the wall absorbs — old or raw walls take more paint. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("roomL"), W = v("roomW"), H = v("roomH");
        if (L <= 0 || W <= 0 || H <= 0 || v("coverage") <= 0) { bad("Please enter correct room size and coverage."); return; }
        var walls = 2 * (L + W) * H;
        var ceil = v("ceiling") === 1 ? L * W : 0;
        var open = v("doors") * v("doorArea") + v("wins") * v("winArea");
        var net = walls + ceil - open; if (net < 0) { net = 0; }
        var w = 1 + v("waste") / 100;
        var paint = net * v("coats") / v("coverage") * w;
        var primer = v("primerCov") > 0 ? net / v("primerCov") * w : 0;
        $("res").innerHTML = table([
            ["Gross wall area", fmt(walls) + " sq ft"],
            ["Ceiling area", fmt(ceil) + " sq ft"],
            ["Minus doors / windows", "-" + fmt(open) + " sq ft"],
            ["Net paintable area", fmt(net) + " sq ft"],
            ["Paint needed (" + fmt(v("coats"), 0) + " coats)", fmt(paint) + " liters"],
            ["Primer needed (1 coat)", fmt(primer) + " liters"],
            ["Estimated paint cost", "Rs " + fmt(paint * v("priceLiter"), 0)]
        ]);
    }
  
    bind(["roomL", "roomW", "roomH", "ceiling", "doors", "doorArea", "wins", "winArea", "coats", "coverage", "primerCov", "waste", "priceLiter"], calc);
    calc();
})();
</script>
@endsection
