@extends('layouts.app')

@section('title', 'Quartile Calculator — Free Online Tool')
@section('meta_description', 'Calculate Q1, Q2, Q3 and the interquartile range (IQR) of your data. Free online quartile calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Quartile Calculator</h1>
            <p class="lead small text-muted">Calculate Q1, Q2, Q3 and the interquartile range (IQR) of your data.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-12"><label for="data" class="form-label">Data values (separate with spaces or commas)</label><textarea class="form-control" id="data" rows="3">12 15 18 21 24 27 30 33 36 40 44 48</textarea></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter your numbers separated by spaces or commas.</li>
                        <li>The result instantly shows the five-number summary (min, Q1, median, Q3, max).</li>
                        <li>The IQR and the 1.5 &times; IQR rule also identify outliers.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Quartiles are calculated with the linear interpolation method — the same method commonly used for box plots. Some textbooks use the exclusive method, which may give slightly different values.</p>
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

    function pct(sorted, p) {
        var pos = (p / 100) * (sorted.length - 1);
        var lo = Math.floor(pos), hi = Math.ceil(pos);
        if (lo === hi) { return sorted[lo]; }
        return sorted[lo] + (sorted[hi] - sorted[lo]) * (pos - lo);
    }
    function calc() {
        var d = parseList("data");
        if (d.length < 4) { bad("Enter at least 4 values to find the quartiles."); return; }
        var s = d.slice().sort(function (a, b) { return a - b; });
        var q1 = pct(s, 25), q2 = pct(s, 50), q3 = pct(s, 75), iqr = q3 - q1;
        var lf = q1 - 1.5 * iqr, uf = q3 + 1.5 * iqr;
        var out = s.filter(function (n) { return n < lf || n > uf; });
        $("res").innerHTML = table([
            ["Sorted data", s.join(", ")],
            ["Minimum", fmt(s[0])],
            ["Q1 (25th percentile)", fmt(q1)],
            ["Q2 / Median (50th)", fmt(q2)],
            ["Q3 (75th percentile)", fmt(q3)],
            ["Maximum", fmt(s[s.length - 1])],
            ["IQR (Q3 - Q1)", fmt(iqr)],
            ["Range (max - min)", fmt(s[s.length - 1] - s[0])],
            ["Outlier fences", fmt(lf) + " to " + fmt(uf)],
            ["Outliers", out.length ? out.join(", ") : "None"]
        ]);
    }
  
    bind(["data"], calc);
    calc();
})();
</script>
@endsection
