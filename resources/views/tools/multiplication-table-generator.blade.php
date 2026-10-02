@extends('layouts.app')

@section('title', 'Multiplication Table Generator — Free Online Tool')
@section('meta_description', 'Make the multiplication table of any number and select a practice range.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Multiplication Table Generator</h1>
            <p class="lead small text-muted">Choose a number and a range — the full multiplication table will appear in a clear table, and you can print it for practice too.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="mtNum">Number</label><input type="number" class="form-control inp" id="mtNum" placeholder="e.g. 7" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="mtRange">Range</label><select class="form-select inp" id="mtRange"><option value="10">1 to 10</option><option value="12">1 to 12</option><option value="15">1 to 15</option><option value="20">1 to 20</option><option value="25">1 to 25</option><option value="30">1 to 30</option></select></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a number.</li><li>Select a range — the table appears right away.</li></ol>
                    <p class="small text-muted mb-0">Note: The best way to learn a table is daily practice — first read it, then say it aloud, then write it without looking.</p>
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
        var n = num("mtNum"), r = parseInt(el("mtRange").value, 10);
        if (n === null) { err("Enter a number."); el("steps").innerHTML = ""; return; }
        var rows = ""; for (var i = 1; i <= r; i++) { rows += "<tr><td>" + fmt(n, 2) + "</td><td>x</td><td>" + i + "</td><td>=</td><td><strong>" + fmt(n * i, 4) + "</strong></td></tr>"; }
        show("<h2 class=\"h6\">" + fmt(n, 2) + " Table</h2><div class=\"table-responsive\"><table class=\"table table-striped table-sm w-auto\"><tbody>" + rows + "</tbody></table></div>");
        showSteps([]);
    }
    bind(calc); calc();

})();
</script>
@endsection
