@extends('layouts.app')

@section('title', 'Dice Probability Calculator — Free Online Tool')
@section('meta_description', 'Find the probability of any number or sum on one or two dice, with a table')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Dice Probability Calculator</h1>
            <p class="lead small text-muted">Choose the number of dice and enter your target number or sum — you will see the probability, favourable outcomes and the full sum table for two dice.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="dcCount">Number of dice</label><select class="form-select inp" id="dcCount"><option value="1">1 die</option><option value="2">2 dice</option></select></div>                    <div class="col-md-4"><label class="form-label" for="dcTarget">Target (1 die: number 1-6, 2 dice: sum 2-12)</label><input type="number" class="form-control inp" id="dcTarget" placeholder="e.g. 7" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Select the number of dice.</li><li>Enter the target number or sum.</li><li>You will get the probability in both percent and fraction, along with the outcomes table.</li></ol>
                    <p class="small text-muted mb-0">Note: Every outcome is treated as equally likely (fair dice). Two dice have 36 total outcomes.</p>
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
        var n = parseInt(el("dcCount").value, 10); var t = intv("dcTarget");
        var counts = {}; var total = n === 1 ? 6 : 36;
        if (n === 1) { for (var i = 1; i <= 6; i++) { counts[i] = 1; } }
        else { for (var a = 1; a <= 6; a++) { for (var b = 1; b <= 6; b++) { var s = a + b; counts[s] = (counts[s] || 0) + 1; } } }
        var rows = Object.keys(counts).map(function (k) { var hl = (t !== null && parseInt(k, 10) === t) ? " class=\"table-active fw-bold\"" : ""; return "<tr" + hl + "><td>" + k + "</td><td>" + counts[k] + "</td><td>" + fmt(counts[k] / total * 100, 2) + "%</td></tr>"; }).join("");
        el("steps").innerHTML = "<h2 class=\"h6\">Outcomes Table (" + (n === 1 ? "number" : "sum") + " | favourable outcomes | probability)</h2><div class=\"table-responsive\"><table class=\"table table-sm\"><thead><tr><th>" + (n === 1 ? "Number" : "Sum") + "</th><th>Outcomes</th><th>Probability</th></tr></thead><tbody>" + rows + "</tbody></table></div>";
        if (t === null) { show("<div class=\"alert alert-info mb-0\">Enter a target to show its probability big. The table below shows the probability of all sums.</div>"); return; }
        var min = n === 1 ? 1 : 2, max = n === 1 ? 6 : 12;
        if (t < min || t > max) { err("Target must be between " + min + " and " + max + "."); return; }
        var fav = counts[t] || 0;
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>P(" + (n === 1 ? "number" : "sum") + " = " + t + ") = " + fav + "/" + total + " = " + fmt(fav / total * 100, 2) + "%</strong></div>");
    }
    bind(calc); calc();

})();
</script>
@endsection
