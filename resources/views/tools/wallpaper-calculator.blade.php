@extends('layouts.app')

@section('title', 'Wallpaper Calculator — Free Online Tool')
@section('meta_description', 'Calculate wallpaper rolls needed from wall size, roll size and pattern repeat.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Wallpaper Calculator</h1>
            <p class="lead small text-muted">Calculate wallpaper rolls needed from wall size, roll size and pattern repeat.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="perim" class="form-label">Total wall length / perimeter (ft)</label><input type="number" class="form-control" id="perim" value="48" step="any"></div><div class="col-md-4"><label for="wallH" class="form-label">Wall height (ft)</label><input type="number" class="form-control" id="wallH" value="9" step="any"></div><div class="col-md-4"><label for="rollW" class="form-label">Roll width (in)</label><input type="number" class="form-control" id="rollW" value="21" step="any"></div><div class="col-md-4"><label for="rollL" class="form-label">Roll length (ft)</label><input type="number" class="form-control" id="rollL" value="33" step="any"></div><div class="col-md-4"><label for="repeat" class="form-label">Pattern repeat (in, 0 = plain)</label><input type="number" class="form-control" id="repeat" value="0" step="any"></div><div class="col-md-4"><label for="trim" class="form-label">Trim allowance per strip (in)</label><input type="number" class="form-control" id="trim" value="4" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the total length of all walls (perimeter) — do not subtract doors and windows, because the strips also run over them.</li>
                        <li>Enter the roll width and length from the roll label.</li>
                        <li>Enter the pattern repeat — keep 0 for plain paper.</li>
                        <li>The number of rolls will be shown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Each strip is lengthened to the next multiple of the pattern repeat so the design aligns on every strip — this is the real waste of patterned wallpaper.</p>
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
        var per = v("perim"), H = v("wallH"), rw = v("rollW"), rl = v("rollL"), rep = v("repeat"), trim = v("trim");
        if (per <= 0 || H <= 0 || rw <= 0 || rl <= 0) { bad("Please enter the wall and roll sizes correctly."); return; }
        var stripIn = H * 12 + trim;
        if (rep > 0) { stripIn = Math.ceil(stripIn / rep) * rep; }
        var strips = Math.ceil(per * 12 / rw);
        var perRoll = Math.floor(rl * 12 / stripIn);
        if (perRoll < 1) { bad("The roll length is shorter than one strip — you need a bigger roll."); return; }
        var rolls = Math.ceil(strips / perRoll);
        $("res").innerHTML = table([
            ["Strip length (pattern aligned)", fmt(stripIn) + " in (" + fmt(stripIn / 12) + " ft)"],
            ["Strips needed", fmt(strips, 0)],
            ["Strips per roll", fmt(perRoll, 0)],
            ["Rolls needed", fmt(rolls, 0)]
        ]);
    }
  
    bind(["perim", "wallH", "rollW", "rollL", "repeat", "trim"], calc);
    calc();
})();
</script>
@endsection
