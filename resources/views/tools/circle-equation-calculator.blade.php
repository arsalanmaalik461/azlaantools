@extends('layouts.app')

@section('title', 'Circle Equation Calculator — Free Online Tool')
@section('meta_description', 'Build a circle equation from the center and radius, or find the center and radius from an equation')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Circle Equation Calculator</h1>
            <p class="lead small text-muted">Build a circle equation from the center and radius, or find the center and radius from an equation</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<h3 class="h6">A. Equation from center and radius</h3><div class="row g-3"><div class="col-md-4"><label for="h" class="form-label">Center h</label><input type="number" class="form-control" id="h" value="2" step="any"></div><div class="col-md-4"><label for="k" class="form-label">Center k</label><input type="number" class="form-control" id="k" value="-3" step="any"></div><div class="col-md-4"><label for="r" class="form-label">Radius r</label><input type="number" class="form-control" id="r" value="5" step="any"></div></div><div id="resA" class="alert alert-secondary mt-3 mb-4">—</div><h3 class="h6">B. Center / radius from general form: x^2 + y^2 + Dx + Ey + F = 0</h3><div class="row g-3"><div class="col-md-4"><label for="cd" class="form-label">D</label><input type="number" class="form-control" id="cd" value="-4" step="any"></div><div class="col-md-4"><label for="ce" class="form-label">E</label><input type="number" class="form-control" id="ce" value="6" step="any"></div><div class="col-md-4"><label for="cf" class="form-label">F</label><input type="number" class="form-control" id="cf" value="4" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>In Section A, enter the center (h, k) and radius — both standard and general forms are created.</li>
                        <li>In Section B, enter D, E, F of the general form — the center and radius come back out.</li>
                        <li>Both sections can also be used to check each other.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Standard form: (x - h)^2 + (y - k)^2 = r^2. Expanding the general form and comparing coefficients gives D = -2h, E = -2k, F = h^2 + k^2 - r^2.</p>
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

    function num(n) { return fmt(n); }
    function signed(n) { return (n < 0 ? "- " : "+ ") + fmt(Math.abs(n)); }
    function calc() {
        var h = v("h"), k = v("k"), r = v("r");
        if (r > 0) {
            var hs = h === 0 ? "x^2" : "(x " + signed(-h) + ")^2";
            var ks = k === 0 ? "y^2" : "(y " + signed(-k) + ")^2";
            var D = -2 * h, E = -2 * k, F = h * h + k * k - r * r;
            $("resA").innerHTML = table([
                ["Standard form", hs + " + " + ks + " = " + fmt(r * r)],
                ["General form", "x^2 + y^2 " + signed(D) + "x " + signed(E) + "y " + signed(F) + " = 0"],
                ["Center", "(" + num(h) + ", " + num(k) + ")"],
                ["Diameter", fmt(2 * r)], ["Area", fmt(Math.PI * r * r)], ["Circumference", fmt(2 * Math.PI * r)]
            ]);
        } else { $("resA").innerHTML = "<span class=\"text-danger fw-semibold\">Enter a radius greater than 0.</span>"; }
        var d2 = v("cd"), e2 = v("ce"), f2 = v("cf");
        var cx = -d2 / 2, cy = -e2 / 2, inside = cx * cx + cy * cy - f2;
        if (inside < 0) { $("res").innerHTML = "<span class=\"text-danger fw-semibold\">This equation does not make a real circle — radius squared is negative (" + fmt(inside) + ").</span>"; }
        else if (inside === 0) { $("res").innerHTML = table([["Center", "(" + num(cx) + ", " + num(cy) + ")"], ["Radius", "0 — this is only a point circle"]]); }
        else { $("res").innerHTML = table([["Center (-D/2, -E/2)", "(" + num(cx) + ", " + num(cy) + ")"], ["Radius", fmt(Math.sqrt(inside))], ["Standard form check", "(x " + signed(-cx) + ")^2 + (y " + signed(-cy) + ")^2 = " + fmt(inside)]]); }
    }
  
    bind(["h", "k", "r", "cd", "ce", "cf"], calc);
    calc();
})();
</script>
@endsection
