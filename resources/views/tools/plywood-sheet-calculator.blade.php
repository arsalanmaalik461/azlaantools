@extends('layouts.app')

@section('title', 'Plywood Sheet Calculator — Free Online Tool')
@section('meta_description', 'Calculate plywood sheets needed to cover any floor, wall or furniture area.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Plywood Sheet Calculator</h1>
            <p class="lead small text-muted">Calculate plywood sheets needed to cover any floor, wall or furniture area.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="area" class="form-label">Project area (sq ft)</label><input type="number" class="form-control" id="area" value="200" step="any"></div><div class="col-md-4"><label for="sheetL" class="form-label">Sheet length (ft)</label><input type="number" class="form-control" id="sheetL" value="8" step="any"></div><div class="col-md-4"><label for="sheetW" class="form-label">Sheet width (ft)</label><input type="number" class="form-control" id="sheetW" value="4" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Cutting waste (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div><div class="col-md-4"><label for="priceSheet" class="form-label">Price per sheet (Rs)</label><input type="number" class="form-control" id="priceSheet" value="6500" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the total project area in sq ft.</li>
                        <li>Enter the sheet size — a standard sheet is 8 x 4 ft.</li>
                        <li>Enter the cutting waste.</li>
                        <li>The number of sheets and the cost will show.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Make a cutting plan before cutting big sheets — a good layout can reduce waste below 10%. Thickness (mm) affects price, not area. Rates change — verify with the official source before relying on this.</p>
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
        var a = v("area"), sl = v("sheetL"), sw = v("sheetW"), waste = v("waste");
        if (a <= 0 || sl <= 0 || sw <= 0) { bad("Please enter the area and sheet size correctly."); return; }
        var sa = sl * sw, raw = a / sa;
        var sheets = Math.ceil(raw * (1 + waste / 100));
        $("res").innerHTML = table([
            ["Sheet area", fmt(sa) + " sq ft"],
            ["Sheets (exact)", fmt(raw)],
            ["Sheets to buy (with " + fmt(waste, 0) + "% waste)", fmt(sheets, 0)],
            ["Total area bought", fmt(sheets * sa) + " sq ft"],
            ["Estimated cost", "Rs " + fmt(sheets * v("priceSheet"), 0)]
        ]);
    }
  
    bind(["area", "sheetL", "sheetW", "waste", "priceSheet"], calc);
    calc();
})();
</script>
@endsection
