@extends('layouts.app')

@section('title', 'Regular Polygon Calculator — Free Online Tool')
@section('meta_description', 'Find the area, perimeter, interior angle and apothem of a regular polygon')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Regular Polygon Calculator</h1>
            <p class="lead small text-muted">Enter the number of sides (n) and one side's length — get the area, perimeter, interior/exterior angle, apothem and circumradius of any regular polygon like pentagon (5), hexagon (6) or octagon (8). Diagram help: the apothem is the straight line from the center to the middle of any side.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rpN">Number of sides (n)</label><input type="number" step="1" class="form-control" id="rpN" placeholder="e.g. 6"></div><div class="col-md-4 mb-3"><label class="form-label" for="rpS">Side length</label><input type="number" step="any" class="form-control" id="rpS" placeholder="e.g. 5"></div></div><div class="alert alert-secondary mt-3 mb-0" id="rpRes">Enter values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the number of sides (at least 3) and one side's length.</li>
                <li>All results appear together: perimeter, apothem, area and all three angles.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Interior angle = (n - 2) x 180 / n, Apothem = side / (2 tan(pi/n)), Area = (perimeter x apothem) / 2. In a regular polygon all sides and angles are equal.</p>
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


function rpCalc() {
    var n = num("rpN"), s = num("rpS");
    if (n === null || s === null) { setT("rpRes", "Enter the sides and side length."); return; }
    if (Math.floor(n) !== n || n < 3) { setT("rpRes", "Sides must be at least 3 and a whole number."); return; }
    if (s <= 0) { setT("rpRes", "Side length must be positive."); return; }
    var per = n * s; var apo = s / (2 * Math.tan(Math.PI / n)); var area = per * apo / 2;
    var interior = (n - 2) * 180 / n; var exterior = 360 / n; var circum = s / (2 * Math.sin(Math.PI / n));
    var names = {3:"Triangle",4:"Square",5:"Pentagon",6:"Hexagon",7:"Heptagon",8:"Octagon",9:"Nonagon",10:"Decagon",12:"Dodecagon"};
    setT("rpRes", "Shape: " + (names[n] || n + "-gon") + " | Perimeter: " + fmt(per, 4) + " | Apothem: " + fmt(apo, 4) + " | Circumradius: " + fmt(circum, 4) + " | Area: " + fmt(area, 4) + " | Interior angle: " + fmt(interior, 2) + "° | Exterior angle: " + fmt(exterior, 2) + "° | Sum of interior angles: " + fmt((n - 2) * 180, 0) + "°");
}
bind(["rpN","rpS"], rpCalc); rpCalc();

})();
</script>
@endsection
