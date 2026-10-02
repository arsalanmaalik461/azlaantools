@extends('layouts.app')

@section('title', 'Decimal to Fraction Converter — Free Online Tool')
@section('meta_description', 'Convert a decimal number to a simplified fraction with steps')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Decimal to Fraction Converter</h1>
            <p class="lead small text-muted">Enter a decimal number — if digits repeat, also enter how many digits repeat. You will get the fraction in simplified form with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-6"><label class="form-label" for="dVal">Decimal number</label><input type="text" class="form-control inp" id="dVal" placeholder="e.g. 0.75" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="dRep">Repeating digits (0 = none)</label><input type="number" class="form-control inp" id="dRep" placeholder="0" step="1" value="0"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a decimal number.</li><li>If the last digits repeat again and again, enter how many repeat; otherwise leave it 0.</li><li>The result will show the simplified fraction and all the steps.</li></ol>
                    <p class="small text-muted mb-0">Note: for terminating decimals the denominator is a power of 10. For repeating decimals the standard algebra method (the difference of x and 10x) is used.</p>
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
        var raw = el("dVal").value.trim(); var rep = intv("dRep") || 0;
        if (raw === "") { err("Please enter a decimal number first."); return; }
        var neg = raw.charAt(0) === "-"; var s = raw.replace("-", "").replace("+", "");
        if (!/^[0-9]*(\.[0-9]*)?$/.test(s) || s === "" || s === ".") { err("Please enter a valid decimal number, e.g. 0.75 or 1.333."); return; }
        var parts = s.split("."); var intPart = parts[0] === "" ? 0 : parseInt(parts[0], 10);
        var frac = parts.length > 1 ? parts[1] : "";
        var st = [], numerator, denominator;
        if (rep > 0) {
            if (frac.length <= rep) { err("Please enter at least that many digits in the decimal for repeating digits."); return; }
            var nonRep = frac.slice(0, frac.length - rep); var repPart = frac.slice(frac.length - rep);
            var a = parseInt((intPart + nonRep + repPart) || "0", 10); var b = parseInt((intPart + nonRep) || "0", 10);
            numerator = a - b; denominator = parseInt("9".repeat(rep) + "0".repeat(nonRep.length), 10);
            st.push("x = " + raw + " (last " + rep + " digits repeat)");
            st.push("Multiply both sides and subtract to get numerator = " + a + " - " + b + " = " + numerator);
            st.push("Denominator = " + denominator + " (one 9 for each repeating digit, one 0 for each non-repeating decimal digit)");
        } else if (frac === "") {
            numerator = intPart; denominator = 1; st.push("This is a whole number, so the fraction = " + intPart + "/1");
        } else {
            denominator = Math.pow(10, frac.length); numerator = intPart * denominator + parseInt(frac, 10);
            st.push("Decimal digits = " + frac.length + ", so denominator = 10^" + frac.length + " = " + fmt(denominator, 0));
            st.push("Fraction = " + fmt(numerator, 0) + " / " + fmt(denominator, 0));
        }
        var g = gcd(numerator, denominator); var n2 = numerator / g, d2 = denominator / g;
        st.push("GCD = " + fmt(g, 0) + " — divide both the numerator and denominator by the GCD");
        st.push("Simplified fraction = " + (neg ? "-" : "") + fmt(n2, 0) + " / " + fmt(d2, 0));
        if (d2 !== 1 && n2 >= d2) { st.push("Mixed number = " + (neg ? "-" : "") + Math.floor(n2 / d2) + " " + (n2 % d2) + "/" + fmt(d2, 0)); }
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + raw + " = " + (neg ? "-" : "") + fmt(n2, 0) + " / " + fmt(d2, 0) + "</strong></div>");
        showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
