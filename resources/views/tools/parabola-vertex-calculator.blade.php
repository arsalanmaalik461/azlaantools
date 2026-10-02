@extends('layouts.app')

@section('title', 'Parabola Vertex Calculator — Free Online Tool')
@section('meta_description', 'Find the vertex, axis of symmetry and intercepts of a parabola')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Parabola Vertex Calculator</h1>
            <p class="lead small text-muted">Enter a, b and c of the quadratic y = ax² + bx + c — you will get the vertex, axis, discriminant, roots (x-intercepts) and y-intercept with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="pvA">a</label><input type="number" class="form-control inp" id="pvA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="pvB">b</label><input type="number" class="form-control inp" id="pvB" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="pvC">c</label><input type="number" class="form-control inp" id="pvC" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a (not 0), b and c.</li><li>Vertex x is found from x = −b/(2a), then y is calculated.</li></ol>
                    <p class="small text-muted mb-0">Note: If a is positive, the parabola opens upward and the vertex is the minimum point; if a is negative, the vertex is the maximum. If the discriminant is negative, there are no x-intercepts (no real roots).</p>
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
        var a = num("pvA"), b = num("pvB"), c = num("pvC");
        if ([a, b, c].some(function (v) { return v === null; })) { err("Please enter all three values: a, b and c."); return; }
        if (a === 0) { err("If a = 0 this is not a parabola, it is a straight line."); return; }
        var vx = -b / (2 * a), vy = a * vx * vx + b * vx + c, D = b * b - 4 * a * c;
        var st = ["Vertex x = −b / (2a) = −(" + fmt(b, 3) + ") / (2 x " + fmt(a, 3) + ") = " + fmt(vx, 6), "Vertex y = f(" + fmt(vx, 3) + ") = " + fmt(vy, 6), "Axis of symmetry: x = " + fmt(vx, 6), "Parabola " + (a > 0 ? "opens upward — vertex is the MINIMUM" : "opens downward — vertex is the MAXIMUM"), "y-intercept (at x=0): y = c = " + fmt(c, 4), "Discriminant D = b² − 4ac = " + fmt(D, 4)];
        var roots;
        if (D < 0) { roots = "No real roots (no x-intercepts)"; st.push("D < 0, so the graph does not cross the x-axis"); }
        else if (D === 0) { roots = "One repeated root: x = " + fmt(vx, 6) + " (repeated)"; st.push("D = 0 → one repeated root x = " + fmt(vx, 6)); }
        else { var r1 = (-b + Math.sqrt(D)) / (2 * a), r2 = (-b - Math.sqrt(D)) / (2 * a); roots = "x-intercepts: x = " + fmt(r1, 6) + " and x = " + fmt(r2, 6); st.push("Roots = (−b ± √D) / 2a = " + fmt(r1, 6) + ", " + fmt(r2, 6)); }
        st.push("Vertex form: y = " + fmt(a, 3) + "(x − " + fmt(vx, 3) + ")² + " + fmt(vy, 3) + " (read the signs as per the values)");
        show("<div class=\"alert alert-success mb-0\"><strong>Vertex = (" + fmt(vx, 4) + ", " + fmt(vy, 4) + ")</strong><br>Axis of symmetry: x = " + fmt(vx, 4) + " &nbsp;|&nbsp; " + roots + "</div>");
        showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
