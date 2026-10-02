@extends('layouts.app')

@section('title', 'Marks to Grade Calculator — Free Online Tool')
@section('meta_description', 'Find Pakistan board grade, grade point and remarks from obtained marks')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Marks to Grade Calculator</h1>
            <p class="lead small text-muted">Enter obtained and total marks — get percent, board grade, grade point and remarks instantly. The full grading table is given below.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="mgObt">Obtained marks</label><input type="number" class="form-control inp" id="mgObt" placeholder="e.g. 850" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="mgTot">Total marks</label><input type="number" class="form-control inp" id="mgTot" placeholder="e.g. 1100" step="any"></div></div>
                    <div class="table-responsive mt-3"><table class="table table-sm"><thead><tr><th>Percent</th><th>Grade</th><th>Grade Point</th><th>Remarks</th></tr></thead><tbody>
                    <tr><td>80% or more</td><td>A+</td><td>5.0</td><td>Outstanding</td></tr>
                    <tr><td>70% - 79%</td><td>A</td><td>4.0</td><td>Excellent</td></tr>
                    <tr><td>60% - 69%</td><td>B</td><td>3.0</td><td>Very Good</td></tr>
                    <tr><td>50% - 59%</td><td>C</td><td>2.0</td><td>Good</td></tr>
                    <tr><td>40% - 49%</td><td>D</td><td>1.0</td><td>Satisfactory</td></tr>
                    <tr><td>33% - 39%</td><td>E</td><td>0.5</td><td>Pass</td></tr>
                    <tr><td>Below 33%</td><td>F</td><td>0.0</td><td>Fail</td></tr>
                    </tbody></table></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter obtained and total marks.</li><li>Percent is calculated and the grade is shown from the table.</li></ol>
                    <p class="small text-muted mb-0">Note: This is the grading scale commonly used by Pakistan boards (BISE): A+ 80%, A 70%, B 60%, C 50%, D 40%, E 33%, below that F. Some boards or universities may use a different scale — verify with your board's official scale. Rates change — verify with the official source before relying on this.</p>
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
        var o = num("mgObt"), t = num("mgTot");
        if (o === null || t === null || t <= 0) { err("Enter correct obtained and total marks."); return; }
        if (o < 0 || o > t) { err("Obtained marks cannot be more than total."); return; }
        var p = o / t * 100;
        var scales = [[80, "A+", 5.0, "Outstanding"], [70, "A", 4.0, "Excellent"], [60, "B", 3.0, "Very Good"], [50, "C", 2.0, "Good"], [40, "D", 1.0, "Satisfactory"], [33, "E", 0.5, "Pass"], [0, "F", 0.0, "Fail"]];
        var g = scales.find(function (s) { return p >= s[0]; });
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Percent = " + fmt(p, 2) + "% &nbsp;|&nbsp; Grade = " + g[1] + " &nbsp;|&nbsp; Grade Point = " + fmt(g[2], 1) + " &nbsp;|&nbsp; " + g[3] + "</strong></div>");
        showSteps(["Percent = obtained ÷ total x 100 = " + fmt(o, 1) + " ÷ " + fmt(t, 1) + " x 100 = " + fmt(p, 2) + "%", fmt(p, 2) + "% comes in the " + g[1] + " grade range of the table"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
