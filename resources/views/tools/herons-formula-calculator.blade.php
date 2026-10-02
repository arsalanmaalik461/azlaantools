@extends('layouts.app')

@section('title', 'Heron Formula Calculator — Free Online Tool')
@section('meta_description', 'Find the area of a triangle from its three sides with Heron formula and the semi-perimeter')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Heron Formula Calculator</h1>
            <p class="lead small text-muted">Enter the three sides of a triangle — get the area from the semi-perimeter and Heron formula with steps. No height needed.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="hfA">Side a</label><input type="number" class="form-control inp" id="hfA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="hfB">Side b</label><input type="number" class="form-control inp" id="hfB" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="hfC">Side c</label><input type="number" class="form-control inp" id="hfC" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter all three sides.</li><li>First the semi-perimeter s is found, then area = √(s(s−a)(s−b)(s−c)).</li></ol>
                    <p class="small text-muted mb-0">Note: The three sides must pass the triangle inequality: the sum of any two sides must be greater than the third. Otherwise no such triangle exists.</p>
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
        var a = num("hfA"), b = num("hfB"), c = num("hfC");
        if ([a, b, c].some(function (v) { return v === null || v <= 0; })) { err("Enter all three sides as positive numbers."); return; }
        if (a + b <= c || a + c <= b || b + c <= a) { err("These sides cannot make a triangle — the sum of two sides must be greater than the third."); return; }
        var s = (a + b + c) / 2; var area = Math.sqrt(s * (s - a) * (s - b) * (s - c));
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Area = " + fmt(area, 4) + " sq units</strong> &nbsp;|&nbsp; Perimeter = " + fmt(a + b + c, 4) + "</div>");
        showSteps(["Semi-perimeter s = (" + fmt(a, 3) + " + " + fmt(b, 3) + " + " + fmt(c, 3) + ") / 2 = " + fmt(s, 4), "s − a = " + fmt(s - a, 4) + ", s − b = " + fmt(s - b, 4) + ", s − c = " + fmt(s - c, 4), "Area = √(" + fmt(s, 3) + " x " + fmt(s - a, 3) + " x " + fmt(s - b, 3) + " x " + fmt(s - c, 3) + ") = √" + fmt(s * (s - a) * (s - b) * (s - c), 4) + " = " + fmt(area, 4)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
