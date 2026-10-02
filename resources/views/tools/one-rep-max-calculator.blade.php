@extends('layouts.app')

@section('title', 'One Rep Max Calculator — Free Online Tool')
@section('meta_description', 'Estimate your one rep max from any set using Epley, Brzycki and Lombardi formulas plus a percentage chart')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">One Rep Max Calculator</h1>
            <p class="lead small text-muted">Enter a weight you lifted and the reps you managed, and get your estimated one-rep max from three established formulas plus a training percentage chart.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Weight lifted (kg)</label><input type="number" class="form-control" id="weight" value="100" step="any"></div>                    <div class="mb-3"><label class="form-label" for="reps">Reps completed (1 to 15 works best)</label><input type="number" class="form-control" id="reps" value="5" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the weight you lifted, in kilograms or pounds — the result is in the same unit.</li><li>Enter the reps you completed with good form.</li><li>Read the three estimates and the percentage chart.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Epley: weight x (1 + reps/30). Brzycki: weight x 36 / (37 - reps). Lombardi: weight x reps^0.10. Estimates are most accurate under about 10 reps and vary by exercise — never test a true max without proper setup and spotters. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var w = num("weight"), r = num("reps");
        if (isNaN(w) || isNaN(r) || w <= 0 || r < 1 || r > 30) { out("Please enter a valid weight and reps between 1 and 30."); return; }
        var epley = w * (1 + r / 30);
        var brzycki = w * 36 / (37 - r);
        var lombardi = w * Math.pow(r, 0.10);
        var avg = (epley + brzycki + lombardi) / 3;
        var pcts = [100, 95, 90, 85, 80, 75, 70, 65, 60, 55, 50];
        var rows = "";
        pcts.forEach(function (p) { rows += "<tr><td>" + p + "%</td><td>" + fmt(avg * p / 100, 1) + "</td></tr>"; });
        out("<strong>Epley:</strong> " + fmt(epley, 1) + "<br><strong>Brzycki:</strong> " + fmt(brzycki, 1) + "<br><strong>Lombardi:</strong> " + fmt(lombardi, 1) + "<br><strong>Average estimate:</strong> " + fmt(avg, 1) + "<table class=\"table table-sm mt-2\"><thead><tr><th>% of 1RM (average)</th><th>Weight</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["weight", "reps"], calc); calc();
})();
</script>
@endsection
