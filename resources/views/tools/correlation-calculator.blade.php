@extends('layouts.app')

@section('title', 'Correlation Calculator — Free Online Tool')
@section('meta_description', 'Calculate the correlation coefficient r between two data sets, with steps shown')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Correlation Calculator</h1>
            <p class="lead small text-muted">Calculate the correlation coefficient r between two data sets, with steps shown</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="xs" class="form-label">X values (separate with space or comma)</label><textarea class="form-control" id="xs" rows="3">10 20 30 40 50 60</textarea></div><div class="col-md-6"><label for="ys" class="form-label">Y values (same order, same count)</label><textarea class="form-control" id="ys" rows="3">12 25 28 45 49 62</textarea></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the X and Y values in the same order — each X pairs with the Y in the same position.</li>
                        <li>Both lists must have the same number of values.</li>
                        <li>The r value, its strength and the regression line will appear with steps.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Pearson correlation only measures a linear relation. Correlation does not mean causation — the two variables may be linked through a third cause.</p>
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
        var X = parseList("xs"), Y = parseList("ys");
        if (X.length !== Y.length || X.length < 2) { bad("X and Y must have the same count, at least 2."); return; }
        var n = X.length, sx = 0, sy = 0, i;
        for (i = 0; i < n; i++) { sx += X[i]; sy += Y[i]; }
        var mx = sx / n, my = sy / n, sxy = 0, sxx = 0, syy = 0;
        for (i = 0; i < n; i++) { var dx = X[i] - mx, dy = Y[i] - my; sxy += dx * dy; sxx += dx * dx; syy += dy * dy; }
        if (sxx === 0 || syy === 0) { bad("All X or all Y values are the same — correlation is undefined."); return; }
        var r = sxy / Math.sqrt(sxx * syy);
        var slope = sxy / sxx, intercept = my - slope * mx;
        var ar = Math.abs(r);
        var strength = ar >= 0.9 ? "Very strong" : (ar >= 0.7 ? "Strong" : (ar >= 0.5 ? "Moderate" : (ar >= 0.3 ? "Weak" : "Very weak / almost none")));
        var dir = r > 0 ? "positive (as X rises, Y rises too)" : (r < 0 ? "negative (as X rises, Y falls)" : "no linear relation");
        $("res").innerHTML = table([
            ["Pairs (n)", fmt(n, 0)],
            ["Mean X", fmt(mx)], ["Mean Y", fmt(my)],
            ["Sum of products (Sxy)", fmt(sxy)], ["Sxx", fmt(sxx)], ["Syy", fmt(syy)],
            ["Correlation coefficient r", fmtd(r, 4)],
            ["r squared (variance explained)", fmt(r * r * 100) + "%"],
            ["Strength", strength], ["Direction", dir],
            ["Regression line Y = a + bX", "Y = " + fmt(intercept) + (slope < 0 ? " - " : " + ") + fmt(Math.abs(slope)) + " X"]
        ]);
    }
  
    bind(["xs", "ys"], calc);
    calc();
})();
</script>
@endsection
