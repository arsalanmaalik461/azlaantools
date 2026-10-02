@extends('layouts.app')

@section('title', 'Triangular Prism Calculator — Free Online Tool')
@section('meta_description', 'Find the volume and surface area of a triangular prism from all its sides')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Triangular Prism Calculator</h1>
            <p class="lead small text-muted">Enter the base, height and other two sides of the triangular part, and the length of the prism — you get the volume and total surface area. Diagram help: there are identical triangles at both ends, and the length is the distance between them.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="tpBase">Triangle base (b)</label><input type="number" step="any" class="form-control" id="tpBase" placeholder="e.g. 6"></div><div class="col-md-4 mb-3"><label class="form-label" for="tpH">Triangle height (h)</label><input type="number" step="any" class="form-control" id="tpH" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="tpS2">Triangle side 2</label><input type="number" step="any" class="form-control" id="tpS2" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="tpS3">Triangle side 3</label><input type="number" step="any" class="form-control" id="tpS3" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="tpL">Prism length (L)</label><input type="number" step="any" class="form-control" id="tpL" placeholder="e.g. 10"></div></div><div class="alert alert-secondary mt-3 mb-0" id="tpRes">Enter values — the result shows live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the base and height of the triangle.</li>
                <li>Enter the other two sides of the triangle and the length of the prism.</li>
                <li>You get the volume and surface area (two triangular parts + three rectangular parts).</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Volume = triangle area x length = (1/2 x b x h) x L. Surface area = 2 x triangle area + (b + side2 + side3) x L.</p>
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


function tpCalc() {
    var b = num("tpBase"), h = num("tpH"), s2 = num("tpS2"), s3 = num("tpS3"), L = num("tpL");
    if ([b,h,s2,s3,L].indexOf(null) !== -1) { setT("tpRes", "Enter all five values."); return; }
    if (b <= 0 || h <= 0 || s2 <= 0 || s3 <= 0 || L <= 0) { setT("tpRes", "All values must be positive."); return; }
    var tri = 0.5 * b * h; var vol = tri * L; var sa = 2 * tri + (b + s2 + s3) * L;
    setT("tpRes", "Triangle area: " + fmt(tri, 4) + " | Volume: " + fmt(vol, 4) + " cubic units | Lateral surface area: " + fmt((b + s2 + s3) * L, 4) + " | Total surface area: " + fmt(sa, 4) + " sq units");
}
bind(["tpBase","tpH","tpS2","tpS3","tpL"], tpCalc); tpCalc();

})();
</script>
@endsection
