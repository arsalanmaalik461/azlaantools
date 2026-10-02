@extends('layouts.app')

@section('title', 'House Construction Cost Estimator — Free Online Tool')
@section('meta_description', 'Enter covered area and per sq ft rates to estimate grey structure and finishing costs separately.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">House Construction Cost Estimator</h1>
            <p class="lead small text-muted">Enter covered area and per sq ft rates to estimate grey structure and finishing costs separately.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="area" class="form-label">Covered area (sq ft)</label><input type="number" class="form-control" id="area" value="1800" step="any"></div><div class="col-md-4"><label for="greyRate" class="form-label">Grey structure rate (Rs per sq ft)</label><input type="number" class="form-control" id="greyRate" value="2800" step="any"></div><div class="col-md-4"><label for="finRate" class="form-label">Finishing rate (Rs per sq ft)</label><input type="number" class="form-control" id="finRate" value="1800" step="any"></div><div class="col-md-4"><label for="extra" class="form-label">Extra costs: boundary, gate, tank etc (Rs)</label><input type="number" class="form-control" id="extra" value="300000" step="any"></div><div class="col-md-4"><label for="cont" class="form-label">Contingency (%)</label><input type="number" class="form-control" id="cont" value="5" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the covered area of the house in sq ft (the built area, not the plot area).</li>
                        <li>Enter the current grey structure and finishing rates in your city.</li>
                        <li>Add extra costs like boundary wall, gate and water tank separately.</li>
                        <li>Add a contingency amount to see your total budget.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> This estimator is based on the rates you enter — material and labour rates change over time and vary by city. Rates change — verify with the official source before relying on this.</p>
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
        var a = v("area"), g = v("greyRate"), f = v("finRate"), e = v("extra"), c = v("cont");
        if (a <= 0) { bad("Please enter a valid covered area."); return; }
        var grey = a * g, fin = a * f, sub = grey + fin + e, contAmt = sub * c / 100, total = sub + contAmt;
        $("res").innerHTML = table([
            ["Grey structure cost", "Rs " + fmt(grey, 0)],
            ["Finishing cost", "Rs " + fmt(fin, 0)],
            ["Extra costs", "Rs " + fmt(e, 0)],
            ["Subtotal", "Rs " + fmt(sub, 0)],
            ["Contingency (" + fmt(c, 0) + "%)", "Rs " + fmt(contAmt, 0)],
            ["Total estimated cost", "Rs " + fmt(total, 0)],
            ["Blended rate per sq ft", "Rs " + fmt(total / a, 0)]
        ]);
    }
  
    bind(["area", "greyRate", "finRate", "extra", "cont"], calc);
    calc();
})();
</script>
@endsection
