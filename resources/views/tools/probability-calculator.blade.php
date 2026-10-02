@extends('layouts.app')

@section('title', 'Probability Calculator — Free Online Tool')
@section('meta_description', 'Find the probability of simple events from favorable and total outcomes, with percent')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Probability Calculator</h1>
            <p class="lead small text-muted">Enter favorable outcomes (in how many ways the event can happen) and total outcomes — get probability as fraction, decimal and percent.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="probFav">Favorable outcomes</label><input type="number" step="1" class="form-control" id="probFav" placeholder="e.g. 1"></div><div class="col-md-4 mb-3"><label class="form-label" for="probTot">Total outcomes</label><input type="number" step="1" class="form-control" id="probTot" placeholder="e.g. 6"></div></div><div class="alert alert-secondary mt-3 mb-0" id="probRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the total possible outcomes, like 6 on a dice.</li>
                <li>From these, enter the favorable outcomes, like one specific number = 1.</li>
                <li>You will get the probability, its percent, and the probability of the opposite event (complement).</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: P(event) = favorable / total. Favorable cannot be more than total. This is simple (theoretical) probability.</p>
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


function probCalc() {
    var f = num("probFav"), t = num("probTot");
    if (f === null || t === null) { setT("probRes", "Enter both values."); return; }
    if (t <= 0) { setT("probRes", "Total outcomes must be more than zero."); return; }
    if (f < 0 || f > t) { setT("probRes", "Favorable outcomes must be between 0 and total."); return; }
    function gcd(a, b) { return b === 0 ? a : gcd(b, a % b); }
    var whole = Math.floor(f) === f && Math.floor(t) === t;
    var g = whole ? gcd(f, t) : 1;
    var frac = whole ? (f / g) + "/" + (t / g) : fmt(f / t, 4);
    var p = f / t;
    setT("probRes", "Probability: " + frac + " = " + fmt(p, 4) + " = " + fmt(p * 100, 2) + "% | Complement (event not happening): " + fmt(1 - p, 4) + " = " + fmt((1 - p) * 100, 2) + "%");
}
bind(["probFav","probTot"], probCalc); probCalc();

})();
</script>
@endsection
