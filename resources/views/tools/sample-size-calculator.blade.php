@extends('layouts.app')

@section('title', 'Sample Size Calculator — Free Online Tool')
@section('meta_description', 'Work out the required sample size for your survey from the confidence level and margin of error')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Sample Size Calculator</h1>
            <p class="lead small text-muted">Enter the confidence level, margin of error and expected proportion — you will get the sample size, i.e. how many people to ask for the survey. If you enter a population, the finite population correction is applied too.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="ssConf">Confidence level</label><select class="form-select" id="ssConf"><option value="1.645">90%</option><option value="1.96" selected>95%</option><option value="2.576">99%</option></select></div><div class="col-md-4 mb-3"><label class="form-label" for="ssMargin">Margin of error (%)</label><input type="number" step="any" class="form-control" id="ssMargin" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="ssProp">Expected proportion (%)</label><input type="number" step="any" class="form-control" id="ssProp" placeholder="e.g. 50"></div><div class="col-md-4 mb-3"><label class="form-label" for="ssPop">Population (optional — leave empty if very large)</label><input type="number" step="any" class="form-control" id="ssPop" placeholder="e.g. 10000"></div></div><div class="alert alert-secondary mt-3 mb-0" id="ssRes">Enter the values — the result shows live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Choose the confidence level (usually 95%).</li>
                <li>Write the margin of error in percent, for example 5 means ±5%.</li>
                <li>If you do not know the proportion, keep it at 50 — this gives the safest (largest) sample.</li>
                <li>If you know the total population, enter it too.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula (Cochran): n₀ = z² x p(1-p) / e², then the population correction n = n₀ / (1 + (n₀ - 1)/N). Sample size is always rounded up (ceil). This is only an estimate; the actual survey design matters too.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";

function fmt(n, d) { if (n === null || n === undefined || !isFinite(n)) { return "\u2014"; } var dec = (d === undefined ? 4 : d); return Number(n.toFixed(dec)).toLocaleString("en-US", { maximumFractionDigits: dec }); }
function num(id) { var e = document.getElementById(id); if (!e) { return null; } var v = parseFloat(e.value); return isNaN(v) ? null : v; }
function txt(id) { var e = document.getElementById(id); return e ? e.value : ""; }
function setT(id, t) { var e = document.getElementById(id); if (e) { e.textContent = t; } }
function setH(id, t) { var e = document.getElementById(id); if (e) { e.innerHTML = t; } }
function bind(ids, fn) { ids.forEach(function (id) { var e = document.getElementById(id); if (e) { e.addEventListener("input", fn); e.addEventListener("change", fn); } }); }
function parseList(s) { if (!s) { return []; } var parts = s.split(/[\s,;]+/); var out = []; for (var i = 0; i < parts.length; i++) { if (parts[i] === "") { continue; } var v = parseFloat(parts[i]); if (!isNaN(v) && isFinite(v)) { out.push(v); } } return out; }
function esc(s) { return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }


function ssCalc() {
    var z = parseFloat(txt("ssConf")); var e = num("ssMargin"); var pPct = num("ssProp"); var N = num("ssPop");
    if (e === null || pPct === null) { setT("ssRes", "Enter the margin of error and proportion."); return; }
    if (e <= 0 || e >= 100) { setT("ssRes", "Margin of error must be between 0 and 100."); return; }
    if (pPct <= 0 || pPct >= 100) { setT("ssRes", "Proportion must be between 0 and 100 (50 is the most common)."); return; }
    var p = pPct / 100; var em = e / 100;
    var n0 = z * z * p * (1 - p) / (em * em);
    var msg = "Required sample size (for a large population): " + Math.ceil(n0) + " (exact: " + fmt(n0, 2) + ")";
    if (N !== null && N > 0) { var n = n0 / (1 + (n0 - 1) / N); msg += " | Corrected with population " + fmt(N, 0) + ": " + Math.ceil(n) + " (exact: " + fmt(n, 2) + ")"; }
    setT("ssRes", msg);
}
bind(["ssConf","ssMargin","ssProp","ssPop"], ssCalc); ssCalc();

})();
</script>
@endsection
