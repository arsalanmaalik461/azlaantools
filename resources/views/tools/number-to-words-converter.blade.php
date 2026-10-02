@extends('layouts.app')

@section('title', 'Number to Words Converter — Free Online Tool')
@section('meta_description', 'Convert a number to English words — for cheques and writing amounts')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Number to Words Converter</h1>
            <p class="lead small text-muted">Enter a number (up to 999,999,999,999) and choose the system — International (million/billion) or Pakistani (lakh/crore) — and the amount will appear in words.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-6"><label class="form-label" for="nwNum">Number</label><input type="number" class="form-control inp" id="nwNum" placeholder="e.g. 125000" step="1"></div>                    <div class="col-md-6"><label class="form-label" for="nwSys">System</label><select class="form-select inp" id="nwSys"><option value="intl">International (Thousand, Million)</option><option value="pk">Pakistani (Lakh, Crore)</option></select></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the number and select the system.</li><li>For writing on a cheque, it is common to add Only at the end.</li></ol>
                    <p class="small text-muted mb-0">Note: For decimal numbers only the whole (integer) part is converted. On a cheque the amount must be written in both words and numbers.</p>
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

    var ONES = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    var TENS = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
    function two(n) { return n < 20 ? ONES[n] : TENS[Math.floor(n / 10)] + (n % 10 ? "-" + ONES[n % 10] : ""); }
    function three(n) { return (n >= 100 ? ONES[Math.floor(n / 100)] + " Hundred" + (n % 100 ? " " : "") : "") + (n % 100 ? two(n % 100) : ""); }
    function intl(n) { if (n === 0) { return "Zero"; } var scales = [[1000000000, "Billion"], [1000000, "Million"], [1000, "Thousand"]]; var out = ""; scales.forEach(function (s) { if (n >= s[0]) { out += (out ? " " : "") + three(Math.floor(n / s[0])) + " " + s[1]; n = n % s[0]; } }); if (n) { out += (out ? " " : "") + three(n); } return out; }
    function pk(n) { if (n === 0) { return "Zero"; } var out = ""; var cr = Math.floor(n / 10000000); if (cr) { out += pk(cr) + " Crore"; n = n % 10000000; } var lk = Math.floor(n / 100000); if (lk) { out += (out ? " " : "") + two(lk) + " Lakh"; n = n % 100000; } var th = Math.floor(n / 1000); if (th) { out += (out ? " " : "") + two(th) + " Thousand"; n = n % 1000; } if (n) { out += (out ? " " : "") + three(n); } return out; }
    function calc() {
        var n = intv("nwNum");
        if (n === null || n < 0 || n > 999999999999) { err("Please enter a whole number from 0 to 999,999,999,999."); return; }
        var w = el("nwSys").value === "pk" ? pk(n) : intl(n);
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + fmt(n, 0) + " = " + w + " Only</strong></div>"); showSteps([]);
    }
    bind(calc); calc();

})();
</script>
@endsection
