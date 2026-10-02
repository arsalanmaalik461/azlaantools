@extends('layouts.app')

@section('title', 'Permutations and Combinations Calculator — Free Online Tool')
@section('meta_description', 'Calculate nPr and nCr together with formula and steps')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Permutations and Combinations Calculator</h1>
            <p class="lead small text-muted">Enter the total items (n) and chosen items (r) — get nPr (order matters) and nCr (order does not matter) with steps.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="ncN">n — total items</label><input type="number" step="1" class="form-control" id="ncN" placeholder="e.g. 10"></div><div class="col-md-4 mb-3"><label class="form-label" for="ncR">r — chosen items</label><input type="number" step="1" class="form-control" id="ncR" placeholder="e.g. 3"></div></div><div class="alert alert-secondary mt-3 mb-0" id="ncRes">Enter values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter n (total items) and r (how many to choose). r cannot be bigger than n.</li>
                <li>Both nPr arrangements (permutations) and nCr selections (combinations) will appear.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: nPr = n! / (n-r)! and nCr = n! / (r! x (n-r)!). For very large n the result gets too big, so n is limited to 170 here to avoid overflow.</p>
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


function fact(k) { var r = 1; for (var i = 2; i <= k; i++) { r *= i; } return r; }
function ncCalc() {
    var n = num("ncN"), r = num("ncR");
    if (n === null || r === null) { setT("ncRes", "Enter n and r."); return; }
    if (n < 0 || r < 0 || Math.floor(n) !== n || Math.floor(r) !== r) { setT("ncRes", "n and r must be non-negative whole numbers (integers)."); return; }
    if (r > n) { setT("ncRes", "r cannot be bigger than n."); return; }
    if (n > 170) { setT("ncRes", "n is too big — enter up to 170 to keep the result in range."); return; }
    var npr = fact(n) / fact(n - r); var ncr = npr / fact(r);
    setT("ncRes", "nPr = " + fmt(npr, 0) + " (steps: " + n + "! / " + (n - r) + "! ) | nCr = " + fmt(ncr, 0) + " (steps: nPr / " + r + "! ) | n! = " + fmt(fact(n), 0));
}
bind(["ncN","ncR"], ncCalc); ncCalc();

})();
</script>
@endsection
