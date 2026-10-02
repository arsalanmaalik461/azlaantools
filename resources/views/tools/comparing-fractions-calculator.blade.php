@extends('layouts.app')

@section('title', 'Comparing Fractions Calculator — Free Online Tool')
@section('meta_description', 'Compare two or more fractions and see which one is bigger or smaller')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Comparing Fractions Calculator</h1>
            <p class="lead small text-muted">Compare two or more fractions and find out which one is bigger or smaller</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-3"><label for="n1" class="form-label">Fraction 1 numerator</label><input type="number" class="form-control" id="n1" value="3" step="any"></div><div class="col-md-3"><label for="d1" class="form-label">Fraction 1 denominator</label><input type="number" class="form-control" id="d1" value="4" step="any"></div><div class="col-md-3"><label for="n2" class="form-label">Fraction 2 numerator</label><input type="number" class="form-control" id="n2" value="2" step="any"></div><div class="col-md-3"><label for="d2" class="form-label">Fraction 2 denominator</label><input type="number" class="form-control" id="d2" value="3" step="any"></div><div class="col-md-3"><label for="n3" class="form-label">Fraction 3 numerator</label><input type="number" class="form-control" id="n3" value="5" step="any"></div><div class="col-md-3"><label for="d3" class="form-label">Fraction 3 denominator</label><input type="number" class="form-control" id="d3" value="8" step="any"></div><div class="col-md-3"><label for="n4" class="form-label">Fraction 4 numerator</label><input type="number" class="form-control" id="n4" value="" step="any"></div><div class="col-md-3"><label for="d4" class="form-label">Fraction 4 denominator</label><input type="number" class="form-control" id="d4" value="" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the numerator (top) and denominator (bottom) of each fraction.</li>
                        <li>The third and fourth fractions are optional — leave them empty and they will be skipped.</li>
                        <li>The result will show decimal values, the ascending order and a cross multiplication check.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> The most reliable way to compare is cross multiplication: for a/b vs c/d, compare a x d and c x b — the side that is bigger is the bigger fraction.</p>
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
        var items = [];
        for (var i = 1; i <= 4; i++) {
            var ns = $("n" + i).value, ds = $("d" + i).value;
            if (ns === "" && ds === "") { continue; }
            var n = parseFloat(ns), d = parseFloat(ds);
            if (!isFinite(n) || !isFinite(d) || d === 0) { bad("Please enter Fraction " + i + " correctly — the denominator cannot be zero."); return; }
            items.push({ label: fmt(n) + "/" + fmt(d), val: n / d, n: n, d: d });
        }
        if (items.length < 2) { bad("Enter at least 2 fractions to compare."); return; }
        var sorted = items.slice().sort(function (a, b) { return a.val - b.val; });
        var rowsArr = items.map(function (it, idx) { return ["Fraction " + (idx + 1) + ": " + it.label, fmtd(it.val, 4) + " (decimal)"]; });
        rowsArr.push(["Smallest", sorted[0].label + " = " + fmtd(sorted[0].val, 4)]);
        rowsArr.push(["Largest", sorted[sorted.length - 1].label + " = " + fmtd(sorted[sorted.length - 1].val, 4)]);
        rowsArr.push(["Ascending order", sorted.map(function (it) { return it.label; }).join("  <  ")]);
        var f1 = items[0], f2 = items[1], cross1 = f1.n * f2.d, cross2 = f2.n * f1.d;
        var verdict = cross1 === cross2 ? "both are equal" : (cross1 > cross2 ? f1.label + " is bigger" : f2.label + " is bigger");
        rowsArr.push(["Cross multiplication (Fraction 1 vs 2)", fmt(cross1) + " vs " + fmt(cross2) + " — " + verdict]);
        $("res").innerHTML = table(rowsArr);
    }
  
    bind(["n1", "d1", "n2", "d2", "n3", "d3", "n4", "d4"], calc);
    calc();
})();
</script>
@endsection
