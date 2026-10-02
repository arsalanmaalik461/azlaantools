@extends('layouts.app')

@section('title', 'House Paint Cost Estimator — Free Online Tool')
@section('meta_description', 'Enter wall area and per sq ft paint rate to estimate your total house painting cost.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">House Paint Cost Estimator</h1>
            <p class="lead small text-muted">Enter the wall area and per sq ft paint rate to get the total cost of painting your home.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="area" class="form-label">Wall paint area (sq ft)</label><input type="number" class="form-control" id="area" value="2000" step="any"></div><div class="col-md-4"><label for="rate" class="form-label">Paint rate (Rs per sq ft)</label><input type="number" class="form-control" id="rate" value="85" step="any"></div><div class="col-md-4"><label for="ceilArea" class="form-label">Ceiling area (sq ft)</label><input type="number" class="form-control" id="ceilArea" value="800" step="any"></div><div class="col-md-4"><label for="ceilRate" class="form-label">Ceiling rate (Rs per sq ft)</label><input type="number" class="form-control" id="ceilRate" value="65" step="any"></div><div class="col-md-4"><label for="cont" class="form-label">Contingency (%)</label><input type="number" class="form-control" id="cont" value="5" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the wall paint area in sq ft.</li>
                        <li>Enter the per sq ft rate — check whether the rate includes labor and putty or not.</li>
                        <li>Enter the ceiling area and rate separately.</li>
                        <li>The total cost with contingency will be shown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Rates vary a lot by painter and paint brand — get a written quotation before starting the work. Rates change — verify with the official source before relying on this.</p>
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
        var a = v("area"), r = v("rate"), ca = v("ceilArea"), cr = v("ceilRate"), c = v("cont");
        if (a < 0 || (a + ca) <= 0) { bad("Enter the paint area correctly."); return; }
        var walls = a * r, ceil = ca * cr, sub = walls + ceil, contAmt = sub * c / 100;
        $("res").innerHTML = table([
            ["Wall painting cost", "Rs " + fmt(walls, 0)],
            ["Ceiling painting cost", "Rs " + fmt(ceil, 0)],
            ["Subtotal", "Rs " + fmt(sub, 0)],
            ["Contingency (" + fmt(c, 0) + "%)", "Rs " + fmt(contAmt, 0)],
            ["Total estimated cost", "Rs " + fmt(sub + contAmt, 0)]
        ]);
    }
  
    bind(["area", "rate", "ceilArea", "ceilRate", "cont"], calc);
    calc();
})();
</script>
@endsection
