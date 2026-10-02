@extends('layouts.app')

@section('title', 'Normal Distribution Calculator — Free Online Tool')
@section('meta_description', 'Find the area and probability of the normal distribution from the Z score, with steps')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Normal Distribution Calculator</h1>
            <p class="lead small text-muted">Enter the mean (μ), standard deviation (σ) and your value — you will get the Z-score and the probability below / above / between the value.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="ndMean">Mean (μ)</label><input type="number" class="form-control inp" id="ndMean" placeholder="e.g. 100" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="ndSd">Standard Deviation (σ)</label><input type="number" class="form-control inp" id="ndSd" placeholder="e.g. 15" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="ndMode">Probability wanted</label><select class="form-select inp" id="ndMode"><option value="below">Below X, P(X &lt; x)</option><option value="above">Above X, P(X &gt; x)</option><option value="between">Between two values</option></select></div>                    <div class="col-md-4"><label class="form-label" for="ndX">X (first value)</label><input type="number" class="form-control inp" id="ndX" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="ndX2">X₂ (only for between)</label><input type="number" class="form-control inp" id="ndX2" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the mean and standard deviation.</li><li>Choose the mode and enter the X value(s).</li><li>Steps will be shown for Z-score = (X − μ) / σ.</li></ol>
                    <p class="small text-muted mb-0">Note: Probability is calculated from standard normal table (Z-table) values. Empirical rule: about 68% of the data lies within μ ± 1σ, about 95% within ± 2σ, and about 99.7% within ± 3σ.</p>
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

    function phi(z) { var t = 1 / (1 + 0.2316419 * Math.abs(z)); var d = 0.3989423 * Math.exp(-z * z / 2); var p = d * t * (0.3193815 + t * (-0.3565638 + t * (1.781478 + t * (-1.821256 + t * 1.330274)))); return z > 0 ? 1 - p : p; }
    function calc() {
        var mu = num("ndMean"), sd = num("ndSd"), x = num("ndX"), mode = el("ndMode").value;
        if (mu === null || sd === null || x === null) { err("Please enter the Mean, SD and X."); return; }
        if (sd <= 0) { err("Standard deviation must be positive."); return; }
        var z1 = (x - mu) / sd, p1 = phi(z1), st = ["Z₁ = (" + fmt(x, 3) + " − " + fmt(mu, 3) + ") / " + fmt(sd, 3) + " = " + fmt(z1, 4), "P(Z < " + fmt(z1, 3) + ") = " + fmt(p1, 6) + " (from Z-table)"];
        var res;
        if (mode === "below") { res = p1; st.push("P(X < " + fmt(x, 3) + ") = " + fmt(res * 100, 3) + "%"); }
        else if (mode === "above") { res = 1 - p1; st.push("P(X > " + fmt(x, 3) + ") = 1 − " + fmt(p1, 5) + " = " + fmt(res * 100, 3) + "%"); }
        else { var x2 = num("ndX2"); if (x2 === null) { err("For between mode, please also enter the second value X2."); return; } var z2 = (x2 - mu) / sd, p2 = phi(z2); res = Math.abs(p2 - p1); st.push("Z₂ = (" + fmt(x2, 3) + " − " + fmt(mu, 3) + ") / " + fmt(sd, 3) + " = " + fmt(z2, 4)); st.push("Probability = |" + fmt(p2, 5) + " − " + fmt(p1, 5) + "| = " + fmt(res * 100, 3) + "%"); }
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Probability = " + fmt(res, 6) + " (" + fmt(res * 100, 3) + "%)</strong></div>"); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
