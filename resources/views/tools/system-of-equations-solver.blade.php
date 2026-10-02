@extends('layouts.app')

@section('title', 'System of Equations Solver — Free Online Tool')
@section('meta_description', 'Solve two or three equations together with substitution and elimination')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">System of Equations Solver</h1>
            <p class="lead small text-muted">Enter the coefficients of equations with 2 variables (x, y) or 3 variables (x, y, z) — write each equation in the form a x + b y (+ c z) = d. You will get the solution using determinants (Cramer rule) with steps, which is a short form of elimination.</p>
<div class="mb-3"><label class="form-label" for="seMode">System</label><select class="form-select" id="seMode"><option value="2">2 equations, 2 variables (x, y)</option><option value="3">3 equations, 3 variables (x, y, z)</option></select></div><div id="se2"><div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="seA1">Eq1: a1 (x)</label><input type="number" step="any" class="form-control" id="seA1" placeholder="e.g. 2"></div><div class="col-md-4 mb-3"><label class="form-label" for="seB1">Eq1: b1 (y)</label><input type="number" step="any" class="form-control" id="seB1" placeholder="e.g. 3"></div><div class="col-md-4 mb-3"><label class="form-label" for="seD1">Eq1: d1 (value after =)</label><input type="number" step="any" class="form-control" id="seD1" placeholder="e.g. 12"></div><div class="col-md-4 mb-3"><label class="form-label" for="seA2">Eq2: a2 (x)</label><input type="number" step="any" class="form-control" id="seA2" placeholder="e.g. 1"></div><div class="col-md-4 mb-3"><label class="form-label" for="seB2">Eq2: b2 (y)</label><input type="number" step="any" class="form-control" id="seB2" placeholder="e.g. -1"></div><div class="col-md-4 mb-3"><label class="form-label" for="seD2">Eq2: d2</label><input type="number" step="any" class="form-control" id="seD2" placeholder="e.g. 1"></div></div></div><div id="se3" class="d-none"><div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="se3A1">Eq1: a (x)</label><input type="number" step="any" class="form-control" id="se3A1" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3B1">Eq1: b (y)</label><input type="number" step="any" class="form-control" id="se3B1" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3C1">Eq1: c (z)</label><input type="number" step="any" class="form-control" id="se3C1" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3D1">Eq1: d</label><input type="number" step="any" class="form-control" id="se3D1" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3A2">Eq2: a (x)</label><input type="number" step="any" class="form-control" id="se3A2" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3B2">Eq2: b (y)</label><input type="number" step="any" class="form-control" id="se3B2" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3C2">Eq2: c (z)</label><input type="number" step="any" class="form-control" id="se3C2" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3D2">Eq2: d</label><input type="number" step="any" class="form-control" id="se3D2" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3A3">Eq3: a (x)</label><input type="number" step="any" class="form-control" id="se3A3" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3B3">Eq3: b (y)</label><input type="number" step="any" class="form-control" id="se3B3" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3C3">Eq3: c (z)</label><input type="number" step="any" class="form-control" id="se3C3" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="se3D3">Eq3: d</label><input type="number" step="any" class="form-control" id="se3D3" placeholder=""></div></div></div><div class="alert alert-secondary mt-3 mb-0" id="seRes">Enter the values — the result will appear here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>First choose a 2-variable or 3-variable system.</li>
                <li>Split each equation into the form a x + b y (+ c z) = d and enter the coefficients — negative values are fine too.</li>
                <li>The result will show the values of x, y (and z) and the determinants.</li>
            </ol>
            <p class="small text-muted mb-0">Note: This uses the Cramer rule (if determinant D is zero, there is either no solution or infinitely many solutions — the tool will tell you). To check your answer, put the values back into the original equations — both sides must be equal.</p>
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


function det3(m) { return m[0][0] * (m[1][1] * m[2][2] - m[1][2] * m[2][1]) - m[0][1] * (m[1][0] * m[2][2] - m[1][2] * m[2][0]) + m[0][2] * (m[1][0] * m[2][1] - m[1][1] * m[2][0]); }
function seCalc() {
    var mode = txt("seMode");
    document.getElementById("se2").classList.toggle("d-none", mode !== "2");
    document.getElementById("se3").classList.toggle("d-none", mode !== "3");
    if (mode === "2") {
        var a1 = num("seA1"), b1 = num("seB1"), d1 = num("seD1"), a2 = num("seA2"), b2 = num("seB2"), d2 = num("seD2");
        if ([a1,b1,d1,a2,b2,d2].indexOf(null) !== -1) { setT("seRes", "Enter all 6 coefficients of the 2-variable system."); return; }
        var D = a1 * b2 - a2 * b1;
        if (D === 0) { setT("seRes", "Determinant D = 0 — this system has no unique solution (either no solution, or infinitely many solutions)."); return; }
        var x = (d1 * b2 - d2 * b1) / D; var y = (a1 * d2 - a2 * d1) / D;
        setT("seRes", "D = " + fmt(D, 4) + " | x = " + fmt(x, 4) + " | y = " + fmt(y, 4) + " | Check Eq1: " + fmt(a1 * x + b1 * y, 4) + " (should be " + fmt(d1, 4) + "), Eq2: " + fmt(a2 * x + b2 * y, 4) + " (should be " + fmt(d2, 4) + ")");
        return;
    }
    var ids = ["se3A1","se3B1","se3C1","se3D1","se3A2","se3B2","se3C2","se3D2","se3A3","se3B3","se3C3","se3D3"];
    var v = ids.map(function (id) { return num(id); });
    if (v.indexOf(null) !== -1) { setT("seRes", "Enter all 12 coefficients of the 3-variable system."); return; }
    var M = [[v[0],v[1],v[2]],[v[4],v[5],v[6]],[v[8],v[9],v[10]]]; var dd = [v[3],v[7],v[11]];
    var D3 = det3(M);
    if (D3 === 0) { setT("seRes", "Determinant D = 0 — this system has no unique solution."); return; }
    function repl(col) { var m = M.map(function (row) { return row.slice(); }); for (var i = 0; i < 3; i++) { m[i][col] = dd[i]; } return det3(m); }
    var Dx = repl(0), Dy = repl(1), Dz = repl(2);
    setT("seRes", "D = " + fmt(D3, 4) + ", Dx = " + fmt(Dx, 4) + ", Dy = " + fmt(Dy, 4) + ", Dz = " + fmt(Dz, 4) + " | x = " + fmt(Dx / D3, 4) + " | y = " + fmt(Dy / D3, 4) + " | z = " + fmt(Dz / D3, 4));
}
bind(["seMode","seA1","seB1","seD1","seA2","seB2","seD2","se3A1","se3B1","se3C1","se3D1","se3A2","se3B2","se3C2","se3D2","se3A3","se3B3","se3C3","se3D3"], seCalc); seCalc();

})();
</script>
@endsection
