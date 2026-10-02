@extends('layouts.app')

@section('title', 'Cubic Equation Solver — Free Online Tool')
@section('meta_description', 'Find all the roots of a degree-three cubic equation')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Cubic Equation Solver</h1>
            <p class="lead small text-muted">Find all the roots of a degree-three cubic equation.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-3"><label for="ca" class="form-label">a (x^3 coefficient)</label><input type="number" class="form-control" id="ca" value="1" step="any"></div><div class="col-md-3"><label for="cb" class="form-label">b (x^2 coefficient)</label><input type="number" class="form-control" id="cb" value="-6" step="any"></div><div class="col-md-3"><label for="cc" class="form-label">c (x coefficient)</label><input type="number" class="form-control" id="cc" value="11" step="any"></div><div class="col-md-3"><label for="cd" class="form-label">d (constant)</label><input type="number" class="form-control" id="cd" value="-6" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the coefficients of the cubic ax^3 + bx^2 + cx + d (a must not be zero).</li>
                        <li>Roots are found with the Cardano method — if all three are real, the trigonometric form is used.</li>
                        <li>An example is already entered: x^3 - 6x^2 + 11x - 6, with roots 1, 2 and 3 — use it to check.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Vieta check: the sum of all three roots is always -b/a and the product is -d/a. Complex roots always come in conjugate pairs when the coefficients are real.</p>
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
        var A = v("ca"), B = v("cb"), C = v("cc"), D = v("cd");
        if (A === 0) { bad("a cannot be zero — otherwise the equation is not cubic."); return; }
        var p = (3 * A * C - B * B) / (3 * A * A);
        var q = (2 * B * B * B - 9 * A * B * C + 27 * A * A * D) / (27 * A * A * A);
        var off = B / (3 * A);
        var disc = -(4 * p * p * p + 27 * q * q);
        var rowsArr = [["Depressed cubic p", fmt(p, 4)], ["Depressed cubic q", fmt(q, 4)], ["Discriminant", fmt(disc, 4)]];
        function rootRow(name, val) { rowsArr.push([name, fmtd(val, 4)]); }
        if (disc > 1e-9 && p < 0) {
            var m = 2 * Math.sqrt(-p / 3);
            var arg = (3 * q) / (2 * p) * Math.sqrt(-3 / p);
            if (arg > 1) { arg = 1; } if (arg < -1) { arg = -1; }
            var theta = Math.acos(arg) / 3;
            for (var kI = 0; kI < 3; kI++) { rootRow("Root " + (kI + 1) + " (real)", m * Math.cos(theta - 2 * Math.PI * kI / 3) - off); }
            rowsArr.push(["Nature", "All three roots are real"]);
        } else {
            var sd = q * q / 4 + p * p * p / 27; if (sd < 0) { sd = 0; }
            var sq = Math.sqrt(sd);
            var u = Math.cbrt(-q / 2 + sq), w = Math.cbrt(-q / 2 - sq);
            var x1 = u + w - off;
            var re = -(u + w) / 2 - off, im = Math.abs((u - w) * Math.sqrt(3) / 2);
            rootRow("Root 1 (real)", x1);
            if (im > 1e-9) {
                rowsArr.push(["Root 2 (complex)", fmt(re, 4) + " + " + fmt(im, 4) + "i"]);
                rowsArr.push(["Root 3 (complex)", fmt(re, 4) + " - " + fmt(im, 4) + "i"]);
                rowsArr.push(["Nature", "One real and two complex conjugate roots"]);
            } else {
                rootRow("Root 2 (real, repeated)", re);
                rootRow("Root 3 (real, repeated)", re);
                rowsArr.push(["Nature", "Repeated real roots"]);
            }
        }
        rowsArr.push(["Check: sum of roots = -b/a", fmt(-B / A, 4)]);
        $("res").innerHTML = table(rowsArr);
    }
  
    bind(["ca", "cb", "cc", "cd"], calc);
    calc();
})();
</script>
@endsection
