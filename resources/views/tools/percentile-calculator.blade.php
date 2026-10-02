@extends('layouts.app')

@section('title', 'Percentile Calculator — Free Online Tool')
@section('meta_description', 'Find the percentile rank of any value in your data, and the value at any percentile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Percentile Calculator</h1>
            <p class="lead small text-muted">Find the percentile rank of any value in your data, and the value at any percentile</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-12"><label for="data" class="form-label">Data values (separate with space or comma)</label><textarea class="form-control" id="data" rows="3">45 52 60 67 71 75 78 82 85 90 93 97</textarea></div><div class="col-md-6"><label for="xval" class="form-label">Value X (for percentile rank)</label><input type="number" class="form-control" id="xval" value="82" step="any"></div><div class="col-md-6"><label for="pval" class="form-label">Percentile P (to find the value)</label><input type="number" class="form-control" id="pval" value="90" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter your numbers separated by space or comma.</li>
                        <li>Type value X — its percentile rank will appear.</li>
                        <li>Type percentile P (for example 90) — the value at that position will appear.</li>
                        <li>Percentile rank and percentile value are two different things; both are shown in separate rows.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Percentile value uses linear interpolation (the modern standard method, not nearest-rank). Formula for rank: (below count + 0.5 x equal count) / n x 100.</p>
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

    function pctValue(sorted, p) {
        var pos = (p / 100) * (sorted.length - 1);
        var lo = Math.floor(pos), hi = Math.ceil(pos);
        if (lo === hi) { return sorted[lo]; }
        return sorted[lo] + (sorted[hi] - sorted[lo]) * (pos - lo);
    }
    function calc() {
        var d = parseList("data");
        var x = v("xval"), p = v("pval");
        if (d.length < 2) { bad("Enter at least 2 values."); return; }
        if (p < 0 || p > 100) { bad("Percentile must be between 0 and 100."); return; }
        var s = d.slice().sort(function (a, b) { return a - b; });
        var below = 0, equal = 0, sum = 0;
        s.forEach(function (n) { if (n < x) { below++; } if (n === x) { equal++; } sum += n; });
        var rank = (below + 0.5 * equal) / s.length * 100;
        $("res").innerHTML = table([
            ["Values count (n)", fmt(s.length, 0)],
            ["Sorted data", s.join(", ")],
            ["Mean", fmt(sum / s.length)],
            ["Percentile rank of " + fmt(x), fmt(rank) + "th percentile"],
            ["Values below X", fmt(below, 0) + " (equal: " + fmt(equal, 0) + ")"],
            [fmt(p, 0) + "th percentile value", fmt(pctValue(s, p))],
            ["Median (50th percentile)", fmt(pctValue(s, 50))]
        ]);
    }
  
    bind(["data", "xval", "pval"], calc);
    calc();
})();
</script>
@endsection
