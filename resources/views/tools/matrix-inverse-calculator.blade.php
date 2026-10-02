@extends('layouts.app')

@section('title', 'Matrix Inverse Calculator — Free Online Tool')
@section('meta_description', 'Find the inverse of a matrix by the adjoint method and verify it')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Matrix Inverse Calculator</h1>
            <p class="lead small text-muted">Enter the matrix — get the determinant, adjugate and inverse along with the A x A⁻¹ = I verification. If the determinant is 0, no inverse exists.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="col-md-4"><label class="form-label" for="ivSize">Matrix size</label><select class="form-select inp" id="ivSize"><option value="2">2 x 2</option><option value="3">3 x 3</option></select></div><div class="mt-3">                    <h2 class="h6">Matrix <span class="text-muted">(for 2x2, leave the third row/column empty and select size 2)</span></h2><div class="row g-1" style="max-width:260px"><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv00" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv01" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv02" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv10" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv11" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv12" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv20" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv21" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="iv22" step="any" placeholder="0"></div></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Select the size and enter the matrix values.</li><li>In the result, see the inverse matrix and the verification (identity matrix).</li></ol>
                    <p class="small text-muted mb-0">Note: The inverse only exists when the determinant is not 0. Method: A⁻¹ = adj(A) / det(A), where adj is the transpose of the cofactor matrix.</p>
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

    function getM() { var k = parseInt(el("ivSize").value, 10); var m = []; for (var i = 0; i < k; i++) { m.push([]); for (var j = 0; j < 3; j++) { var v = parseFloat(el("iv" + i + j).value); m[i].push(isNaN(v) ? 0 : v); } } return { m: m.slice(0, k).map(function (r) { return r.slice(0, k); }), k: k }; }
    function mtable(m, title) { var rows = m.map(function (r) { return "<tr>" + r.map(function (v) { return "<td class=\"text-center fw-semibold\">" + fmt(v, 4) + "</td>"; }).join("") + "</tr>"; }).join(""); return "<h2 class=\"h6\">" + title + "</h2><div class=\"table-responsive\"><table class=\"table table-bordered table-sm w-auto\"><tbody>" + rows + "</tbody></table></div>"; }
    function minor(m, ri, ci) { return m.filter(function (_, i) { return i !== ri; }).map(function (r) { return r.filter(function (_, j) { return j !== ci; }); }); }
    function det2(m) { return m[0][0] * m[1][1] - m[0][1] * m[1][0]; }
    function calc() {
        var g = getM(), m = g.m, k = g.k, det, inv = [], st = [], i, j;
        if (k === 2) { det = det2(m); if (det === 0) { err("Determinant = 0 — this matrix has no inverse (singular matrix)."); return; } inv = [[m[1][1] / det, -m[0][1] / det], [-m[1][0] / det, m[0][0] / det]]; st.push("det = " + fmt(det, 4)); st.push("2x2 method: swap the diagonal entries, flip the sign of the other diagonal, then divide each entry by det"); }
        else {
            var cof = []; det = 0;
            for (i = 0; i < 3; i++) { cof.push([]); for (j = 0; j < 3; j++) { var cf = Math.pow(-1, i + j) * det2(minor(m, i, j)); cof[i].push(cf); if (i === 0) { det += m[0][j] * cf; } } }
            if (det === 0) { err("Determinant = 0 — this matrix has no inverse (singular matrix)."); return; }
            for (i = 0; i < 3; i++) { inv.push([]); for (j = 0; j < 3; j++) { inv[i].push(cof[j][i] / det); } }
            st.push("Cofactor matrix was made, its transpose = adjugate, det = " + fmt(det, 4)); st.push("Inverse = adjugate ÷ " + fmt(det, 4));
        }
        var prod = []; for (i = 0; i < k; i++) { prod.push([]); for (j = 0; j < k; j++) { var s = 0; for (var t = 0; t < k; t++) { s += m[i][t] * inv[t][j]; } prod[i].push(s); } }
        show(mtable(inv, "Inverse Matrix A⁻¹")); el("steps").innerHTML = "<ol>" + st.map(function (x) { return "<li>" + x + "</li>"; }).join("") + "</ol>" + mtable(prod, "Verification: A x A⁻¹ (should be identity)");
    }
    bind(calc); calc();

})();
</script>
@endsection
