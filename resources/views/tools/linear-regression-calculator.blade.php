@extends('layouts.app')

@section('title', 'Linear Regression Calculator — Free Online Tool')
@section('meta_description', 'Get the regression line y = a + bx and predictions from your data points')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Linear Regression Calculator</h1>
            <p class="lead small text-muted">Enter X and Y values separated by commas, in equal counts — get the regression line, correlation and a prediction for any X.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-12"><label class="form-label" for="lrX">X values (comma separated)</label><input type="text" class="form-control inp" id="lrX" placeholder="None" step="any"></div>                    <div class="col-md-12"><label class="form-label" for="lrY">Y values (comma separated)</label><input type="text" class="form-control inp" id="lrY" placeholder="None" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lrPred">X for prediction (optional)</label><input type="number" class="form-control inp" id="lrPred" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter X and Y values separated by commas (at least 3 points).</li><li>The regression line y = a + bx and correlation r will show.</li><li>Optional: you can also get the predicted Y for a new X.</li></ol>
                    <p class="small text-muted mb-0">Note: This is least squares regression — the line is the one where the sum of squared distances from all points is smallest. Correlation is between −1 and 1.</p>
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
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    function calc() {
        var xr = el("lrX").value.trim(), yr = el("lrY").value.trim();
        if (xr === "" || yr === "") { err("Enter both X and Y values."); return; }
        var xs = xr.split(",").map(function (v) { return parseFloat(v); }), ys = yr.split(",").map(function (v) { return parseFloat(v); });
        if (xs.length !== ys.length || xs.length < 3 || xs.some(isNaN) || ys.some(isNaN)) { err("Both lists must have equal counts (at least 3) with numbers only."); return; }
        var n = xs.length, mx = xs.reduce(function (s, v) { return s + v; }, 0) / n, my = ys.reduce(function (s, v) { return s + v; }, 0) / n;
        var sxy = 0, sxx = 0, syy = 0;
        for (var i = 0; i < n; i++) { sxy += (xs[i] - mx) * (ys[i] - my); sxx += Math.pow(xs[i] - mx, 2); syy += Math.pow(ys[i] - my, 2); }
        if (sxx === 0) { err("All X values are the same — regression is not possible."); return; }
        var b = sxy / sxx, a = my - b * mx, r = syy === 0 ? 0 : sxy / Math.sqrt(sxx * syy);
        var pred = num("lrPred"); var ptxt = "";
        if (pred !== null) { ptxt = "<div class=\"mt-2\">For X = " + fmt(pred, 3) + ", predicted Y = <strong>" + fmt(a + b * pred, 6) + "</strong></div>"; }
        show("<div class=\"alert alert-success mb-0\"><strong>Regression line: y = " + fmt(a, 4) + " + " + fmt(b, 4) + "x</strong><br>Correlation r = <strong>" + fmt(r, 4) + "</strong> &nbsp;|&nbsp; R² = " + fmt(r * r, 4) + ptxt + "</div>");
        showSteps(["n = " + n + ", mean of X = " + fmt(mx, 4) + ", mean of Y = " + fmt(my, 4), "Sxy = " + fmt(sxy, 4) + ", Sxx = " + fmt(sxx, 4) + ", Syy = " + fmt(syy, 4), "Slope b = Sxy / Sxx = " + fmt(b, 6), "Intercept a = mean of Y − b x mean of X = " + fmt(a, 6)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
