@extends('layouts.app')

@section('title', 'Factors Calculator — Free Online Tool')
@section('meta_description', 'List all the factors and factor pairs of any number.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Factors Calculator</h1>
            <p class="lead small text-muted">Enter a number — you will get all its factors, factor pairs, the count of factors, and whether it is prime or composite.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="fcN">Number</label><input type="number" class="form-control inp" id="fcN" placeholder="e.g. 36" step="1"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a positive whole number (up to 1,000,000).</li><li>See the factors list and the pairs table.</li></ol>
                    <p class="small text-muted mb-0">Note: a factor is a number that divides the given number with no remainder. Multiplying a factor pair gives back the original number.</p>
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
        var n = intv("fcN"); if (n === null || n < 1 || n > 1000000) { err("Enter a positive whole number from 1 to 1,000,000."); return; }
        var f = [], pairs = [];
        for (var i = 1; i * i <= n; i++) { if (n % i === 0) { f.push(i); if (i !== n / i) { f.push(n / i); } pairs.push([i, n / i]); } }
        f.sort(function (x, y) { return x - y; });
        var prime = n > 1 && f.length === 2;
        var rows = pairs.map(function (p) { return "<tr><td>" + p[0] + "</td><td>x</td><td>" + p[1] + "</td></tr>"; }).join("");
        show("<div class=\"alert alert-success mb-0\"><strong>" + fmt(n, 0) + " has these factors (" + f.length + "):</strong> " + f.join(", ") + "<br>Status: <strong>" + (n === 1 ? "Neither prime nor composite" : (prime ? "Prime number" : "Composite number")) + "</strong> &nbsp;|&nbsp; Sum of factors = " + fmt(f.reduce(function (x, y) { return x + y; }, 0), 0) + "</div>");
        el("steps").innerHTML = "<h2 class=\"h6 mt-3\">Factor Pairs</h2><div class=\"table-responsive\"><table class=\"table table-sm w-auto\"><tbody>" + rows + "</tbody></table></div>";
    }
    bind(calc); calc();

})();
</script>
@endsection
