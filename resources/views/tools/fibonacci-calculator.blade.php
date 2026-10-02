@extends('layouts.app')

@section('title', 'Fibonacci Calculator — Free Online Tool')
@section('meta_description', 'Generate the first n numbers of the Fibonacci sequence and any specific term')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fibonacci Calculator</h1>
            <p class="lead small text-muted">Enter how many terms you need and which term (nth) you want to see separately — you get the full sequence and the nth term.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="fbTerms">Number of terms (1 - 100)</label><input type="number" class="form-control inp" id="fbTerms" placeholder="20" step="1" value="20"></div>                    <div class="col-md-4"><label class="form-label" for="fbNth">nth term (optional)</label><input type="number" class="form-control inp" id="fbNth" placeholder="e.g. 10" step="1"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the number of terms.</li><li>If you need the value of a specific term, also enter the nth term.</li></ol>
                    <p class="small text-muted mb-0">Note: The sequence starts with 0, 1 and each term is the sum of the previous two terms. For large terms, the exact value is calculated with BigInt.</p>
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
        var t = intv("fbTerms"); if (t === null || t < 1 || t > 100) { err("Please enter the number of terms between 1 and 100."); return; }
        var seq = [0n, 1n]; while (seq.length < Math.max(t, 2)) { seq.push(seq[seq.length - 1] + seq[seq.length - 2]); }
        var shown = seq.slice(0, t).map(function (x) { return x.toString(); });
        var nth = intv("fbNth"); var extra = "";
        if (nth !== null && nth >= 1 && nth <= 500) { var a = 0n, b = 1n; for (var i = 1; i < nth; i++) { var tmp = a + b; a = b; b = tmp; } extra = "<div class=\"mt-2\">Term #" + nth + " = <strong>" + a.toString() + "</strong> (here the first term F₁ = 0 is counted)</div>"; }
        show("<div class=\"alert alert-success mb-0\"><strong>First " + t + " terms:</strong><br>" + shown.join(", ") + "</div>" + extra);
        showSteps(["F₁ = 0, F₂ = 1", "Each next term = sum of the previous two terms: F(n) = F(n-1) + F(n-2)", "The ratio of consecutive terms moves toward the golden ratio φ ≈ 1.618"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
