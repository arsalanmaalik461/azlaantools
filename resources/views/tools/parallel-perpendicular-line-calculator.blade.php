@extends('layouts.app')

@section('title', 'Parallel and Perpendicular Lines Calculator — Free Online Tool')
@section('meta_description', 'Find the equation of a line parallel or perpendicular to a given line')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Parallel and Perpendicular Lines Calculator</h1>
            <p class="lead small text-muted">Enter the slope of the given line and the point the new line passes through — you will get the parallel (same slope) or perpendicular (negative reciprocal) line equation with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="ppM">Slope of given line (m₁)</label><input type="number" class="form-control inp" id="ppM" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="ppX">Point x</label><input type="number" class="form-control inp" id="ppX" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="ppY">Point y</label><input type="number" class="form-control inp" id="ppY" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="ppType">New line</label><select class="form-select inp" id="ppType"><option value="par">Parallel</option><option value="perp">Perpendicular</option></select></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the slope and point, then select the relation.</li><li>Special cases for perpendicular lines are also handled (horizontal/vertical, slope 0 or undefined).</li></ol>
                    <p class="small text-muted mb-0">Note: Parallel lines have equal slopes. The slopes of perpendicular lines multiply to −1, that is m₂ = −1/m₁.</p>
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
        var m = num("ppM"), x = num("ppX"), y = num("ppY"), type = el("ppType").value;
        if ([m, x, y].some(function (v) { return v === null; })) { err("Please enter both the slope and the point."); return; }
        var st = ["Given line slope m₁ = " + fmt(m, 4) + ", point = (" + fmt(x, 3) + ", " + fmt(y, 3) + ")"];
        if (type === "perp" && m === 0) { show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Perpendicular line: x = " + fmt(x, 4) + " (vertical line)</strong></div>"); showSteps(st.concat(["The given line is horizontal (slope 0), so the perpendicular is vertical: x = the point x-coordinate"])); return; }
        var m2 = type === "par" ? m : -1 / m;
        st.push(type === "par" ? "Parallel line has the same slope: m₂ = " + fmt(m2, 4) : "Perpendicular slope = −1/m₁ = −1/" + fmt(m, 3) + " = " + fmt(m2, 6));
        var c = y - m2 * x; st.push("Point-slope: y − " + fmt(y, 3) + " = " + fmt(m2, 4) + "(x − " + fmt(x, 3) + ")");
        st.push("c = y − m₂x = " + fmt(y, 3) + " − " + fmt(m2, 3) + " x " + fmt(x, 3) + " = " + fmt(c, 6));
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>y = " + fmt(m2, 4) + "x " + (c < 0 ? "− " + fmt(-c, 4) : "+ " + fmt(c, 4)) + "</strong></div>"); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
