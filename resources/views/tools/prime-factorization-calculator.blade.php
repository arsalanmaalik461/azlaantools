@extends('layouts.app')

@section('title', 'Prime Factorization Calculator — Free Online Tool')
@section('meta_description', 'Break a number into prime factors with a factor tree')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Prime Factorization Calculator</h1>
            <p class="lead small text-muted">Enter a number — get the prime factor list, the exponent form, and the division ladder (factor tree steps). Example: 60 = 2 x 2 x 3 x 5.</p>
<div class="col-md-4 mb-3"><label class="form-label" for="pfaN">Number (2 or more, integer)</label><input type="number" step="1" class="form-control" id="pfaN" placeholder="e.g. 60"></div><div class="alert alert-secondary mt-3 mb-0" id="pfaRes">Enter a value — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter a whole number of 2 or more.</li>
                <li>The result will show the prime factors, their power form (like 2 to the power 2 x 3 x 5) and every division step.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Uses the trial division method: keep dividing by the smallest prime until 1 is left. Input is limited to 1,000,000,000,000.</p>
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


function pfaCalc() {
    var n = num("pfaN");
    if (n === null || Math.floor(n) !== n || n < 2) { setT("pfaRes", "Enter a whole number of 2 or more."); return; }
    if (n > 1000000000000) { setT("pfaRes", "Number is too large — enter up to 1,000,000,000,000."); return; }
    var rem = n; var facs = []; var steps = [];
    for (var p = 2; p * p <= rem; p += (p === 2 ? 1 : 2)) { while (rem % p === 0) { facs.push(p); steps.push(rem + " / " + p + " = " + (rem / p)); rem = rem / p; } }
    if (rem > 1) { facs.push(rem); }
    if (facs.length === 1) { setT("pfaRes", n + " is itself a prime number — its only prime factor is " + n + "."); return; }
    var counts = {}; facs.forEach(function (f) { counts[f] = (counts[f] || 0) + 1; });
    var sup = {"0":"⁰","1":"¹","2":"²","3":"³","4":"⁴","5":"⁵","6":"⁶","7":"⁷","8":"⁸","9":"⁹"};
    var expParts = Object.keys(counts).map(function (k) { if (counts[k] === 1) { return k; } var e = String(counts[k]).split("").map(function (ch) { return sup[ch]; }).join(""); return k + e; });
    setT("pfaRes", n + " = " + facs.join(" x ") + " | Exponent form: " + expParts.join(" x ") + " | Total prime factors: " + facs.length + " | Steps: " + steps.join(" ; "));
}
bind(["pfaN"], pfaCalc); pfaCalc();

})();
</script>
@endsection
