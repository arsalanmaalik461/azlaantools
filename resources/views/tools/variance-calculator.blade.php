@extends('layouts.app')

@section('title', 'Variance Calculator — Free Online Tool')
@section('meta_description', 'Build a variance table for grouped frequency data and find variance step by step')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Variance Calculator</h1>
            <p class="lead small text-muted">On each line, write one value (x) and its frequency (f), separated by a comma — a full frequency table (fx, deviations, f x deviation²) is built and the variance (grouped data) comes out. For ungrouped data, write frequency 1 for each value.</p>
<div class="mb-3"><label class="form-label" for="vaData">Value, Frequency — one per line</label><textarea class="form-control" id="vaData" rows="5" placeholder="e.g.
10, 3
20, 5
30, 2"></textarea></div><div class="alert alert-secondary mt-3 mb-0" id="vaRes">Enter values — the result will show here live.</div><div class="table-responsive mt-3"><table class="table table-sm table-bordered"><thead><tr><th>x</th><th>f</th><th>fx</th><th>x - mean</th><th>(x - mean)²</th><th>f (x - mean)²</th></tr></thead><tbody id="vaTable"><tr><td colspan="6" class="text-muted">Enter data.</td></tr></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Write a value and frequency on each line, for example: 10, 3 means value 10 appears 3 times.</li>
                <li>The table shows each row's fx, difference from the mean, its square, and the frequency-multiplied value.</li>
                <li>The summary below shows N, sum fx, mean, population variance and sample variance.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Population variance = sum of f(x - mean)² / N. Sample variance has the denominator (N - 1). For class intervals, write the class mid-point (mark) instead of x.</p>
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


function vaCalc() {
    var raw = txt("vaData"); var tb = document.getElementById("vaTable");
    var lines = raw.split(/\n/); var pairs = [];
    lines.forEach(function (ln) { var p = parseList(ln); if (p.length >= 2) { pairs.push([p[0], p[1]]); } else if (p.length === 1) { pairs.push([p[0], 1]); } });
    if (!pairs.length) { setT("vaRes", "Write a value and frequency on each line, for example: 10, 3"); tb.innerHTML = "<tr><td colspan=\"6\" class=\"text-muted\">Enter data.</td></tr>"; return; }
    var N = 0, sfx = 0;
    pairs.forEach(function (pr) { N += pr[1]; sfx += pr[0] * pr[1]; });
    if (N <= 0) { setT("vaRes", "The sum of frequencies must be positive."); return; }
    var mean = sfx / N; var sfd = 0;
    var rows = pairs.map(function (pr) { var dev = pr[0] - mean; var fd = pr[1] * dev * dev; sfd += fd; return "<tr><td>" + fmt(pr[0], 4) + "</td><td>" + fmt(pr[1], 2) + "</td><td>" + fmt(pr[0] * pr[1], 4) + "</td><td>" + fmt(dev, 4) + "</td><td>" + fmt(dev * dev, 4) + "</td><td>" + fmt(fd, 4) + "</td></tr>"; }).join("");
    tb.innerHTML = rows;
    var popVar = sfd / N;
    var msg = "N (total frequency): " + fmt(N, 2) + " | Sum fx: " + fmt(sfx, 4) + " | Mean: " + fmt(mean, 4) + " | Sum f(x-mean)²: " + fmt(sfd, 4) + " | Population variance: " + fmt(popVar, 4) + " | Population SD: " + fmt(Math.sqrt(popVar), 4);
    if (N > 1) { msg += " | Sample variance: " + fmt(sfd / (N - 1), 4) + " | Sample SD: " + fmt(Math.sqrt(sfd / (N - 1)), 4); }
    setT("vaRes", msg);
}
bind(["vaData"], vaCalc); vaCalc();

})();
</script>
@endsection
