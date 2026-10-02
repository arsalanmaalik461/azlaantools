@extends('layouts.app')

@section('title', 'Long Division Calculator — Free Online Tool')
@section('meta_description', 'Show the full long division solution step by step with remainder')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Long Division Calculator</h1>
            <p class="lead small text-muted">Enter the dividend and divisor — get the quotient and remainder with every step of school-style long division (divide, multiply, subtract, bring down).</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="ldDvd">Dividend</label><input type="number" class="form-control inp" id="ldDvd" placeholder="e.g. 945" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="ldDvs">Divisor</label><input type="number" class="form-control inp" id="ldDvs" placeholder="e.g. 15" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter two whole numbers (steps are cleaner when the dividend is bigger or equal).</li><li>Each step shows the partial dividend, quotient digit and remainder.</li><li>You also get the decimal answer at the end.</li></ol>
                    <p class="small text-muted mb-0">Note: If the dividend is smaller than the divisor, the quotient is 0 and the remainder is the dividend; the decimal calculation still shows.</p>
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
        var dvd = intv("ldDvd"), dvs = intv("ldDvs");
        if (dvd === null || dvs === null) { err("Enter the dividend and divisor as whole numbers."); return; }
        if (dvs === 0) { err("Divisor cannot be 0 — dividing by zero is undefined."); return; }
        var neg = (dvd < 0) !== (dvs < 0); dvd = Math.abs(dvd); dvs = Math.abs(dvs);
        var digits = String(dvd).split(""), st = [], cur = 0, quotient = "", started = false;
        digits.forEach(function (dg, idx) {
            cur = cur * 10 + parseInt(dg, 10);
            var q = Math.floor(cur / dvs); var prod = q * dvs; var rem = cur - prod;
            if (q > 0 || started) { quotient += q; started = true; st.push("Step " + (idx + 1) + ": partial dividend " + cur + " → " + dvs + " x " + q + " = " + prod + ", subtract → remainder " + rem + (idx < digits.length - 1 ? ", bring down the next digit (" + digits[idx + 1] + ")" : "")); }
            else { st.push("Step " + (idx + 1) + ": " + cur + " is smaller than " + dvs + " — no quotient digit yet, bring down the next digit"); }
            cur = rem;
        });
        var qFinal = Math.floor(dvd / dvs), rFinal = dvd % dvs;
        var dec = (neg ? "-" : "") + fmt(dvd / dvs, 8);
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + (neg ? "-" : "") + "Quotient = " + qFinal + ", Remainder = " + rFinal + "</strong><br><span class=\"fs-6\">Decimal answer = " + dec + " &nbsp;|&nbsp; Check: " + dvs + " x " + qFinal + " + " + rFinal + " = " + (dvs * qFinal + rFinal) + "</span></div>");
        showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
