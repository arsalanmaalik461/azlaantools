@extends('layouts.app')

@section('title', 'Prime Number Generator — Free Online Tool')
@section('meta_description', 'Generate a list of all prime numbers in a given range')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Prime Number Generator</h1>
            <p class="lead small text-muted">Enter a start and end — get all prime numbers in that range with the count and the largest/smallest prime.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="pgStart">From</label><input type="number" step="1" class="form-control" id="pgStart" placeholder="e.g. 1"></div><div class="col-md-4 mb-3"><label class="form-label" for="pgEnd">To</label><input type="number" step="1" class="form-control" id="pgEnd" placeholder="e.g. 100"></div></div><div class="alert alert-secondary mt-3 mb-0" id="pgRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the start and end values of the range.</li>
                <li>All primes will be listed in the result box below, separated by commas.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Uses the Sieve of Eratosthenes method. End is capped at 1,000,000 and the range at 100,000 numbers so the page stays fast.</p>
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


function pgCalc() {
    var s = num("pgStart"), e = num("pgEnd");
    if (s === null || e === null) { setT("pgRes", "Enter start and end."); return; }
    s = Math.floor(s); e = Math.floor(e);
    if (e < s) { setT("pgRes", "End is smaller than start — check the values."); return; }
    if (e > 1000000) { setT("pgRes", "Keep end at 1,000,000 or below."); return; }
    if (e - s > 100000) { setT("pgRes", "Range is too large — use a range of up to 100,000 numbers."); return; }
    var sieve = new Array(e + 1).fill(true); sieve[0] = false; if (e >= 1) { sieve[1] = false; }
    for (var i = 2; i * i <= e; i++) { if (sieve[i]) { for (var j = i * i; j <= e; j += i) { sieve[j] = false; } } }
    var primes = []; for (var k = Math.max(2, s); k <= e; k++) { if (sieve[k]) { primes.push(k); } }
    setT("pgRes", "Count: " + primes.length + (primes.length ? " | Smallest: " + primes[0] + " | Largest: " + primes[primes.length - 1] + " | Primes: " + primes.join(", ") : " | No primes in this range."));
}
bind(["pgStart","pgEnd"], pgCalc); pgCalc();

})();
</script>
@endsection
