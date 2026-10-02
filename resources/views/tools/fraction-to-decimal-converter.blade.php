@extends('layouts.app')

@section('title', 'Fraction to Decimal Converter — Free Online Tool')
@section('meta_description', 'Convert a fraction to decimal with long division steps.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fraction to Decimal Converter</h1>
            <p class="lead small text-muted">Write the numerator and denominator — get the decimal answer with long division steps. Repeating parts are also detected.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="fdNum">Numerator</label><input type="number" class="form-control inp" id="fdNum" placeholder="e.g. 3" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="fdDen">Denominator</label><input type="number" class="form-control inp" id="fdDen" placeholder="e.g. 8" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the numerator and denominator.</li><li>See the long division steps and the decimal result.</li></ol>
                    <p class="small text-muted mb-0">Note: If a remainder repeats, the decimal is repeating (recurring) — the repeating digits are shown in brackets instead of an underline.</p>
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
        var n = intv("fdNum"), d = intv("fdDen");
        if (n === null || d === null) { err("Write both as whole numbers."); return; }
        if (d === 0) { err("The denominator cannot be 0."); return; }
        var sign = (n < 0) !== (d < 0) ? "-" : ""; n = Math.abs(n); d = Math.abs(d);
        var whole = Math.floor(n / d); var rem = n % d; var st = [n + " ÷ " + d + ": whole part of quotient = " + whole + ", remainder = " + rem];
        var digits = "", seen = {}, repStart = -1, count = 0;
        while (rem !== 0 && count < 30) {
            if (seen[rem] !== undefined) { repStart = seen[rem]; break; }
            seen[rem] = count; rem = rem * 10; var q = Math.floor(rem / d); var nr = rem % d;
            st.push("Step " + (count + 1) + ": " + rem + " ÷ " + d + " = " + q + ", remainder = " + nr);
            digits += q; rem = nr; count++;
        }
        var dec;
        if (repStart >= 0) { dec = whole + "." + digits.slice(0, repStart) + "(" + digits.slice(repStart) + ")"; st.push("The remainder started repeating — the bracketed digits repeat again and again (recurring decimal)."); }
        else { dec = digits === "" ? String(whole) : whole + "." + digits; st.push(rem === 0 ? "Remainder is 0 — the decimal ends here (terminating)." : "Steps shown up to 30 digits."); }
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + sign + dec + "</strong> &nbsp;(value ≈ " + fmt((sign === "-" ? -1 : 1) * (whole + (digits === "" ? 0 : parseFloat("0." + digits))), 8) + ")</div>"); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
