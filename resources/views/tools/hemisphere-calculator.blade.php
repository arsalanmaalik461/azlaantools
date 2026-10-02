@extends('layouts.app')

@section('title', 'Hemisphere Calculator — Free Online Tool')
@section('meta_description', 'Find the volume, curved and total surface area of a hemisphere')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Hemisphere Calculator</h1>
            <p class="lead small text-muted">Enter the radius — get the volume, curved surface area and total surface area (including the base) of the hemisphere with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="hsR">Radius (r)</label><input type="number" class="form-control inp" id="hsR" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the radius.</li><li>All three results appear with their formulas.</li></ol>
                    <p class="small text-muted mb-0">Note: Formulas: Volume = (2/3)πr³, Curved surface area = 2πr², Total surface area = 3πr² (curved + base circle πr²).</p>
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
        var r = num("hsR"); if (r === null || r <= 0) { err("Please enter a positive radius."); return; }
        var vol = 2 / 3 * Math.PI * Math.pow(r, 3), csa = 2 * Math.PI * r * r, tsa = 3 * Math.PI * r * r;
        show("<div class=\"alert alert-success mb-0\">Volume = <strong>" + fmt(vol, 4) + " cubic units</strong><br>Curved Surface Area = <strong>" + fmt(csa, 4) + " sq units</strong><br>Total Surface Area = <strong>" + fmt(tsa, 4) + " sq units</strong></div>");
        showSteps(["Volume = (2/3)πr³ = (2/3) x π x " + fmt(r, 3) + "³ = " + fmt(vol, 4), "CSA = 2πr² = 2 x π x " + fmt(r * r, 3) + " = " + fmt(csa, 4), "TSA = CSA + base area (πr²) = " + fmt(csa, 4) + " + " + fmt(Math.PI * r * r, 4) + " = " + fmt(tsa, 4)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
