@extends('layouts.app')

@section('title', 'Matrix Calculator — Free Online Tool')
@section('meta_description', 'Add, subtract, multiply and transpose matrices with steps')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Matrix Calculator</h1>
            <p class="lead small text-muted">Choose the matrix size and operation, enter the values of A and B — the result matrix and the dot-product steps for multiplication are shown.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="col-md-4"><label class="form-label" for="mxSize">Matrix size</label><select class="form-select inp" id="mxSize"><option value="2">2 x 2</option><option value="3">3 x 3</option></select></div>                    <div class="col-md-4"><label class="form-label" for="mxOp">Operation</label><select class="form-select inp" id="mxOp"><option value="add">A + B</option><option value="sub">A − B</option><option value="mul">A x B</option><option value="transA">Transpose of A</option><option value="transB">Transpose of B</option></select></div><div id="mxAwrap" class="mt-3">                    <h2 class="h6">Matrix A <span class="text-muted">(for 2x2, leave the third row/column empty and select size 2)</span></h2><div class="row g-1" style="max-width:260px"><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA00" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA01" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA02" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA10" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA11" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA12" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA20" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA21" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxA22" step="any" placeholder="0"></div></div></div><div id="mxBwrap" class="mt-3">                    <h2 class="h6">Matrix B <span class="text-muted">(for 2x2, leave the third row/column empty and select size 2)</span></h2><div class="row g-1" style="max-width:260px"><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB00" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB01" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB02" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB10" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB11" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB12" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB20" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB21" step="any" placeholder="0"></div><div class="col-4 px-1"><input type="number" class="form-control inp text-center" id="mxB22" step="any" placeholder="0"></div></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>First select the size (2x2 or 3x3) and the operation.</li><li>Enter values in the Matrix A and B boxes; an empty box is treated as 0.</li><li>The result appears below as a matrix.</li></ol>
                    <p class="small text-muted mb-0">Note: In multiplication, the rows of A multiply the columns of B, so A x B and B x A are usually not equal. In 2x2 mode the third row/column is ignored.</p>
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

    function getM(p) { var k = parseInt(el("mxSize").value, 10); var m = []; for (var i = 0; i < k; i++) { m.push([]); for (var j = 0; j < k; j++) { var v = parseFloat(el(p + i + j).value); m[i].push(isNaN(v) ? 0 : v); } } return m; }
    function mtable(m, title) { var rows = m.map(function (r) { return "<tr>" + r.map(function (v) { return "<td class=\"text-center fw-semibold\">" + fmt(v, 4) + "</td>"; }).join("") + "</tr>"; }).join(""); return "<h2 class=\"h6\">" + title + "</h2><div class=\"table-responsive\"><table class=\"table table-bordered table-sm w-auto\"><tbody>" + rows + "</tbody></table></div>"; }
    function calc() {
        var k = parseInt(el("mxSize").value, 10); var op = el("mxOp").value; var A = getM("mxA"), B = getM("mxB"); var R = [], st = [], i, j, t;
        if (op === "add" || op === "sub") { var sgn = op === "add" ? 1 : -1; for (i = 0; i < k; i++) { R.push([]); for (j = 0; j < k; j++) { R[i].push(A[i][j] + sgn * B[i][j]); } } st.push("In add/subtract, each entry adds or subtracts the entry at its own place: R[i][j] = A[i][j] " + (sgn === 1 ? "+" : "−") + " B[i][j]"); show(mtable(R, "Result")); }
        else if (op === "transA" || op === "transB") { var M = op === "transA" ? A : B; for (i = 0; i < k; i++) { R.push([]); for (j = 0; j < k; j++) { R[i].push(M[j][i]); } } st.push("In transpose, rows become columns: R[i][j] = M[j][i]"); show(mtable(R, "Transpose")); }
        else { for (i = 0; i < k; i++) { R.push([]); for (j = 0; j < k; j++) { var sum = 0, parts = []; for (t = 0; t < k; t++) { sum += A[i][t] * B[t][j]; parts.push(fmt(A[i][t], 2) + "x" + fmt(B[t][j], 2)); } R[i].push(sum); st.push("R[" + (i + 1) + "][" + (j + 1) + "] = " + parts.join(" + ") + " = " + fmt(sum, 4)); } } show(mtable(R, "Result (A x B)")); }
        showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
