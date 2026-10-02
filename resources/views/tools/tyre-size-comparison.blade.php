@extends('layouts.app')

@section('title', 'Tyre Size Comparison — Free Online Tool')
@section('meta_description', 'Enter two tyre sizes and compare diameter, circumference and speedometer difference in percent.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Tyre Size Comparison</h1>
            <p class="lead small text-muted">Tyre size is read like this: 195/65 R15 means width 195 mm, sidewall height 65% of the width, and a 15 inch rim. Enter the old and new size — you get the diameter, circumference and speedometer difference in percent.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="tyW1">Old tyre width (mm)</label><input type="number" step="any" class="form-control" id="tyW1" placeholder="e.g. 195"></div><div class="col-md-4 mb-3"><label class="form-label" for="tyA1">Old aspect ratio (%)</label><input type="number" step="any" class="form-control" id="tyA1" placeholder="e.g. 65"></div><div class="col-md-4 mb-3"><label class="form-label" for="tyR1">Old rim (inch)</label><input type="number" step="any" class="form-control" id="tyR1" placeholder="e.g. 15"></div><div class="col-md-4 mb-3"><label class="form-label" for="tyW2">New tyre width (mm)</label><input type="number" step="any" class="form-control" id="tyW2" placeholder="e.g. 205"></div><div class="col-md-4 mb-3"><label class="form-label" for="tyA2">New aspect ratio (%)</label><input type="number" step="any" class="form-control" id="tyA2" placeholder="e.g. 60"></div><div class="col-md-4 mb-3"><label class="form-label" for="tyR2">New rim (inch)</label><input type="number" step="any" class="form-control" id="tyR2" placeholder="e.g. 16"></div></div><div class="alert alert-secondary mt-3 mb-0" id="tyRes">Enter values — the result shows live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the old tyre width, aspect ratio and rim size (written on the side of the tyre).</li>
                <li>Enter all three parts of the new tyre.</li>
                <li>If the diameter difference is over 3%, it is usually avoided — a warning appears in the result.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Sidewall = width x aspect/100 (mm). Diameter = rim x 25.4 + 2 x sidewall (mm). The speedometer is calibrated for the old size; if the new diameter is bigger, your real speed will be higher than the meter shows.</p>
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


function tyDia(w, a, r) { return r * 25.4 + 2 * (w * a / 100); }
function tyCalc() {
    var w1 = num("tyW1"), a1 = num("tyA1"), r1 = num("tyR1"), w2 = num("tyW2"), a2 = num("tyA2"), r2 = num("tyR2");
    if ([w1,a1,r1,w2,a2,r2].indexOf(null) !== -1) { setT("tyRes", "Enter all parts of both tyres."); return; }
    var d1 = tyDia(w1, a1, r1), d2 = tyDia(w2, a2, r2);
    var c1 = Math.PI * d1, c2 = Math.PI * d2;
    var diffPct = (d2 - d1) / d1 * 100;
    var rev1 = 1000000 / c1, rev2 = 1000000 / c2;
    var warn = Math.abs(diffPct) > 3 ? " | WARNING: diameter difference is over 3% — usually this is avoided." : " | Diameter difference is within the safe 3% range.";
    setT("tyRes", "Old: diameter " + fmt(d1, 1) + " mm, circumference " + fmt(c1, 0) + " mm, " + fmt(rev1, 0) + " revolutions/km | New: diameter " + fmt(d2, 1) + " mm, circumference " + fmt(c2, 0) + " mm, " + fmt(rev2, 0) + " revolutions/km | Diameter difference: " + fmt(diffPct, 2) + "% | If the speedometer shows 100, the real speed is: " + fmt(100 * d2 / d1, 1) + warn);
}
bind(["tyW1","tyA1","tyR1","tyW2","tyA2","tyR2"], tyCalc); tyCalc();

})();
</script>
@endsection
