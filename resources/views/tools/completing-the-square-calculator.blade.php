@extends('layouts.app')

@section('title', 'Completing the Square Calculator — Free Online Tool')
@section('meta_description', 'Solve a quadratic with the completing the square method, with every step shown')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Completing the Square Calculator</h1>
            <p class="lead small text-muted">Solve a quadratic with the completing the square method, with every step shown</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="a" class="form-label">a (x^2 coefficient)</label><input type="number" class="form-control" id="a" value="1" step="any"></div><div class="col-md-4"><label for="b" class="form-label">b (x coefficient)</label><input type="number" class="form-control" id="b" value="-6" step="any"></div><div class="col-md-4"><label for="c" class="form-label">c (constant)</label><input type="number" class="form-control" id="c" value="5" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter a, b and c of the quadratic ax^2 + bx + c.</li>
                        <li>Each step will show in a separate row — divide, shift, complete the square, vertex form.</li>
                        <li>Roots are also given as a cross-check with the quadratic formula.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Vertex form is a(x - h)^2 + k where h = -b/(2a) and k = c - b^2/(4a). This form is used to find the vertex of the parabola and to sketch the graph.</p>
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
        var a = v("a"), b = v("b"), c = v("c");
        if (a === 0) { bad("a cannot be zero — then the equation is no longer quadratic."); return; }
        function sgn(x) { return x < 0 ? "- " + fmt(-x) : "+ " + fmt(x); }
        var ba = b / a, ca = c / a;
        var half = ba / 2, sq = half * half;
        var h = -b / (2 * a), kk = c - (b * b) / (4 * a);
        var disc = b * b - 4 * a * c;
        var rowsArr = [
            ["Original", fmt(a) + "x^2 " + sgn(b) + "x " + sgn(c) + " = 0"],
            ["Step 1: divide by a", "x^2 " + sgn(ba) + "x " + sgn(ca) + " = 0"],
            ["Step 2: move the constant to the other side", "x^2 " + sgn(ba) + "x = " + fmt(-ca)],
            ["Step 3: add (b/2a)^2 = " + fmt(sq), "x^2 " + sgn(ba) + "x + " + fmt(sq) + " = " + fmt(sq - ca)],
            ["Step 4: perfect square", "(x " + sgn(half) + ")^2 = " + fmt(sq - ca)],
            ["Vertex form", fmt(a) + "(x " + sgn(-h === 0 ? 0 : -h) + ")^2 " + sgn(kk)],
            ["Vertex (h, k)", "(" + fmt(h) + ", " + fmt(kk) + ")"]
        ];
        if (disc >= 0) {
            var sqd = Math.sqrt(disc);
            rowsArr.push(["Roots (quadratic formula check)", "x = " + fmt((-b + sqd) / (2 * a)) + " and x = " + fmt((-b - sqd) / (2 * a))]);
        } else {
            rowsArr.push(["Roots", "No real roots — complex roots: x = " + fmt(-b / (2 * a)) + " +/- " + fmt(Math.sqrt(-disc) / (2 * a)) + "i"]);
        }
        rowsArr.push(["Discriminant (b^2 - 4ac)", fmt(disc)]);
        $("res").innerHTML = table(rowsArr);
    }
  
    bind(["a", "b", "c"], calc);
    calc();
})();
</script>
@endsection
