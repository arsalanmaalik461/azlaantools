@extends('layouts.app')

@section('title', 'Inequality Solver — Free Online Tool')
@section('meta_description', 'Solve linear inequalities and show the answer on a number line')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Inequality Solver</h1>
            <p class="lead small text-muted">Enter values for a, b and c and choose the inequality sign — the solution comes with steps, interval notation and a number line.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="iqA">a (x ka coefficient)</label><input type="number" class="form-control inp" id="iqA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="iqB">b</label><input type="number" class="form-control inp" id="iqB" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="iqOp">Sign</label><select class="form-select inp" id="iqOp"><option value="<"><</option><option value="<=">≤</option><option value=">">></option><option value=">=">≥</option></select></div>                    <div class="col-md-4"><label class="form-label" for="iqC">c (right side)</label><input type="number" class="form-control inp" id="iqC" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a, b, c and the sign.</li><li>When you divide by a negative number the sign flips — this step is shown clearly.</li><li>See where the solution sits on the number line.</li></ol>
                    <p class="small text-muted mb-0">Note: If a = 0, the inequality is either always true (infinite solutions) or never true — the result will say which one.</p>
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
        var a = num("iqA"), b = num("iqB"), c = num("iqC"), op = el("iqOp").value;
        if ([a, b, c].some(function (v) { return v === null; })) { err("Enter all three: a, b and c."); return; }
        var os = { "<": "<", "<=": "≤", ">": ">", ">=": "≥" }; var flip = { "<": ">", "<=": ">=", ">": "<", ">=": "<=" };
        var st = [fmt(a, 3) + "x + " + fmt(b, 3) + " " + os[op] + " " + fmt(c, 3)];
        if (a === 0) { var truth = op === "<" ? b < c : op === "<=" ? b <= c : op === ">" ? b > c : b >= c; show("<div class=\"alert alert-info mb-0 fs-5\"><strong>" + (truth ? "Every real number satisfies this inequality (infinite solutions)." : "No solution — this inequality is never true.") + "</strong></div>"); showSteps(st.concat(["The x term cancelled out; only " + fmt(b, 3) + " " + os[op] + " " + fmt(c, 3) + " remains, which is " + (truth ? "true" : "false") + "."])); return; }
        var rhs = c - b; st.push("Subtract " + fmt(b, 3) + " from both sides: " + fmt(a, 3) + "x " + os[op] + " " + fmt(rhs, 3));
        var x = rhs / a; var finalOp = op;
        if (a < 0) { finalOp = flip[op]; st.push("Divided by a negative number (" + fmt(a, 3) + ") — SIGN FLIPPED: " + os[op] + " → " + os[finalOp]); }
        st.push("x " + os[finalOp] + " " + fmt(x, 6));
        var interval = finalOp === "<" ? "(−∞, " + fmt(x, 4) + ")" : finalOp === "<=" ? "(−∞, " + fmt(x, 4) + "]" : finalOp === ">" ? "(" + fmt(x, 4) + ", ∞)" : "[" + fmt(x, 4) + ", ∞)";
        var closed = finalOp === "<=" || finalOp === ">="; var right = finalOp === ">" || finalOp === ">=";
        var svg = "<svg width=\"100%\" height=\"60\" viewBox=\"0 0 300 60\" class=\"mt-2\"><line x1=\"10\" y1=\"35\" x2=\"290\" y2=\"35\" stroke=\"#888\" stroke-width=\"2\"/><line x1=\"" + (right ? 150 : 20) + "\" y1=\"35\" x2=\"" + (right ? 285 : 150) + "\" y2=\"35\" stroke=\"#198754\" stroke-width=\"5\"/><circle cx=\"150\" cy=\"35\" r=\"7\" fill=\"" + (closed ? "#198754" : "none") + "\" stroke=\"#198754\" stroke-width=\"3\"/><text x=\"150\" y=\"18\" text-anchor=\"middle\" fill=\"currentColor\" font-size=\"13\">" + fmt(x, 3) + "</text><text x=\"150\" y=\"55\" text-anchor=\"middle\" fill=\"currentColor\" font-size=\"10\">" + (closed ? "filled circle = this point is included" : "open circle = this point is not included") + "</text></svg>";
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>x " + os[finalOp] + " " + fmt(x, 6) + "</strong> &nbsp;|&nbsp; Interval: <strong>" + interval + "</strong>" + svg + "</div>");
        showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
