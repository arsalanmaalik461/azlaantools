@extends('layouts.app')

@section('title', 'Matrix Determinant Calculator — Free Online Tool')
@section('meta_description', 'Find the determinant of 2x2 and 3x3 matrices with expansion steps')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Matrix Determinant Calculator</h1>
            <p class="lead small text-muted">Choose the size and enter the matrix values — for 2x2 you get the ad − bc formula and for 3x3 the cofactor expansion steps with the determinant.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="col-md-4"><label class="form-label" for="dtSize">Matrix size</label><select class="form-select inp" id="dtSize"><option value="2">2 x 2</option><option value="3">3 x 3</option></select></div><div class="mt-3">                    <h2 class="h6">Matrix <span class="text-muted">(for 2x2, leave the third row/column empty and select size 2)</span></h2><div class="row g-1" style="max-width:260px"><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt00" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt01" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt02" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt10" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt11" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt12" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt20" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt21" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="dt22" step="any" placeholder="0"></div></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Select the size and enter values in all cells.</li><li>For 3x3, the expansion steps by the first row are shown.</li></ol>
                    <p class="small text-muted mb-0">Note: If the determinant is 0, the matrix is singular — it has no inverse and the system is either inconsistent or has infinite solutions.</p>
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

    function getM() { var k = parseInt(el("dtSize").value, 10); var m = []; for (var i = 0; i < k; i++) { m.push([]); for (var j = 0; j < 3; j++) { var v = parseFloat(el("dt" + i + j).value); m[i].push(isNaN(v) ? 0 : v); } } return { m: m.slice(0, k).map(function (r) { return r.slice(0, k); }), k: k }; }
    function calc() {
        var g = getM(), m = g.m, k = g.k, st = [], det;
        if (k === 2) { det = m[0][0] * m[1][1] - m[0][1] * m[1][0]; st.push("det = ad − bc = (" + fmt(m[0][0], 3) + " x " + fmt(m[1][1], 3) + ") − (" + fmt(m[0][1], 3) + " x " + fmt(m[1][0], 3) + ")"); st.push("= " + fmt(m[0][0] * m[1][1], 4) + " − " + fmt(m[0][1] * m[1][0], 4) + " = " + fmt(det, 6)); }
        else {
            var t1 = m[0][0] * (m[1][1] * m[2][2] - m[1][2] * m[2][1]); var t2 = m[0][1] * (m[1][0] * m[2][2] - m[1][2] * m[2][0]); var t3 = m[0][2] * (m[1][0] * m[2][1] - m[1][1] * m[2][0]); det = t1 - t2 + t3;
            st.push("Expansion by the first row: det = a(ei − fh) − b(di − fg) + c(dh − eg)");
            st.push("Term 1: " + fmt(m[0][0], 3) + " x (" + fmt(m[1][1], 3) + "x" + fmt(m[2][2], 3) + " − " + fmt(m[1][2], 3) + "x" + fmt(m[2][1], 3) + ") = " + fmt(t1, 4));
            st.push("Term 2: − " + fmt(m[0][1], 3) + " x (" + fmt(m[1][0], 3) + "x" + fmt(m[2][2], 3) + " − " + fmt(m[1][2], 3) + "x" + fmt(m[2][0], 3) + ") → result = −(" + fmt(t2, 4) + ")");
            st.push("Term 3: " + fmt(m[0][2], 3) + " x (" + fmt(m[1][0], 3) + "x" + fmt(m[2][1], 3) + " − " + fmt(m[1][1], 3) + "x" + fmt(m[2][0], 3) + ") = " + fmt(t3, 4));
            st.push("det = " + fmt(t1, 4) + " − " + fmt(t2, 4) + " + " + fmt(t3, 4) + " = " + fmt(det, 6));
        }
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Determinant = " + fmt(det, 6) + "</strong>" + (det === 0 ? " &nbsp;(singular matrix)" : "") + "</div>"); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
