@extends('layouts.app')

@section('title', 'Significant Figures Calculator — Free Online Tool')
@section('meta_description', 'Count the significant figures in a number and round it to any target sig figs.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Significant Figures Calculator</h1>
            <p class="lead small text-muted">Type the number exactly as written (for example 0.00420) — it counts how many significant figures it has, and also shows the rounded form for any target sig figs. Zero rules are important, so the number is taken as text.</p>
<div class="mb-3"><label class="form-label" for="sfNum">Number (e.g. 0.00420 or 12300)</label><input type="text" class="form-control" id="sfNum" placeholder=""></div><div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="sfTo">Round to sig figs (optional)</label><input type="number" step="1" class="form-control" id="sfTo" placeholder="e.g. 2"></div></div><div class="alert alert-secondary mt-3 mb-0" id="sfRes">Enter values — the result will appear here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Write the number exactly as it is written — trailing zeros are also counted when there is a decimal point.</li>
                <li>Optional: also enter how many sig figs to round to.</li>
            </ol>
            <p class="small text-muted mb-0">Rules: non-zero digits are always significant; zeros between two significant digits are significant; leading zeros are never significant; in a decimal number, trailing zeros are significant. Scientific notation also works (for example 4.20e3).</p>
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


function sfCount(s) {
    s = s.trim().toLowerCase();
    if (!s) { return null; }
    var mant = s.split("e")[0].replace("-", "").replace("+", "");
    if (!/^[0-9.]+$/.test(mant)) { return null; }
    var hasDot = mant.indexOf(".") !== -1;
    var digits = mant.replace(".", "");
    var i = 0; while (i < digits.length && digits.charAt(i) === "0") { i++; }
    if (i === digits.length) { return 0; }
    var rest = digits.slice(i);
    if (!hasDot) { rest = rest.replace(/0+$/, ""); if (rest === "") { rest = digits.slice(i, i + 1); } }
    return rest.length;
}
function sfCalc() {
    var raw = txt("sfNum"); var target = num("sfTo");
    var c = sfCount(raw);
    if (c === null) { setT("sfRes", "Enter a valid number, for example 0.00420 or 12300."); return; }
    var v = parseFloat(raw);
    var msg = "Significant figures: " + c + " | Value: " + raw;
    if (target !== null && Math.floor(target) === target && target >= 1 && target <= 15 && isFinite(v)) { msg += " | Rounded to " + target + " sig figs: " + v.toPrecision(target); }
    setT("sfRes", msg);
}
bind(["sfNum","sfTo"], sfCalc); sfCalc();

})();
</script>
@endsection
