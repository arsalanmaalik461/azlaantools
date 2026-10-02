@extends('layouts.app')

@section('title', 'Divisibility Checker — Free Online Tool')
@section('meta_description', 'Check which numbers a number divides perfectly, with rules')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Divisibility Checker</h1>
            <p class="lead small text-muted">Type any whole number — the rule, test, and result for each divisor from 2 to 12 will show in a table.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-6"><label class="form-label" for="dvNum">Number</label><input type="number" class="form-control inp" id="dvNum" placeholder="e.g. 1248" step="1"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a number (a whole number).</li><li>See the rule and PASS or FAIL result for each divisor in the table.</li></ol>
                    <p class="small text-muted mb-0">Note: These are standard school rules: digit sum for 3 and 9, last 2 digits for 4, last 3 digits for 8, both 2 and 3 for 6, difference of alternate digits for 11, both 3 and 4 for 12.</p>
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
        var n = intv("dvNum"); if (n === null) { err("Please type a whole number."); return; }
        var a = Math.abs(n); var digits = String(a).split("").map(Number); var dsum = digits.reduce(function (x, y) { return x + y; }, 0);
        var alt = 0; digits.forEach(function (d, i) { alt += (i % 2 === 0 ? d : -d); });
        var last2 = a % 100, last3 = a % 1000;
        var tests = [
            [2, "Last digit is even", a % 10, a % 2 === 0, "last digit = " + (a % 10)],
            [3, "Sum of digits divides by 3", dsum, dsum % 3 === 0, "digits sum = " + dsum],
            [4, "Last 2 digits divide by 4", last2, last2 % 4 === 0, "last 2 digits = " + last2],
            [5, "Last digit is 0 or 5", a % 10, (a % 10) === 0 || (a % 10) === 5, "last digit = " + (a % 10)],
            [6, "Divides by both 2 and 3", null, a % 2 === 0 && dsum % 3 === 0, "even = " + (a % 2 === 0) + ", digits sum = " + dsum],
            [7, "Divide directly to check", a % 7, a % 7 === 0, "remainder = " + (a % 7)],
            [8, "Last 3 digits divide by 8", last3, last3 % 8 === 0, "last 3 digits = " + last3],
            [9, "Sum of digits divides by 9", dsum, dsum % 9 === 0, "digits sum = " + dsum],
            [10, "Last digit is 0", a % 10, a % 10 === 0, "last digit = " + (a % 10)],
            [11, "Difference of alternate digits is 0 or a multiple of 11", alt, ((alt % 11) + 11) % 11 === 0, "alternate difference = " + alt],
            [12, "Divides by both 3 and 4", null, dsum % 3 === 0 && last2 % 4 === 0, "digits sum = " + dsum + ", last 2 = " + last2]
        ];
        var ok = tests.filter(function (t) { return t[3]; }).map(function (t) { return t[0]; });
        var rows = tests.map(function (t) { return "<tr><td><strong>" + t[0] + "</strong></td><td>" + t[1] + "</td><td>" + t[4] + "</td><td>" + (t[3] ? "<span class=\"badge bg-success\">PASS</span>" : "<span class=\"badge bg-secondary\">FAIL</span>") + "</td></tr>"; }).join("");
        show("<div class=\"alert alert-success mb-0\"><strong>" + fmt(n, 0) + "</strong> divides perfectly by these numbers: <strong>" + (ok.length ? ok.join(", ") : "none (of 2 to 12)") + "</strong></div>");
        el("steps").innerHTML = "<h2 class=\"h6 mt-3\">Rules Table</h2><div class=\"table-responsive\"><table class=\"table table-sm\"><thead><tr><th>Divisor</th><th>Rule</th><th>Test</th><th>Result</th></tr></thead><tbody>" + rows + "</tbody></table></div>";
        if (n === 0) { show("<div class=\"alert alert-warning mb-0\">0 divides by every number, but dividing by 0 is undefined.</div>"); }
    }
    bind(calc); calc();

})();
</script>
@endsection
