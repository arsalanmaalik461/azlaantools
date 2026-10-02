@extends('layouts.app')

@section('title', 'Place Value Calculator — Free Online Tool')
@section('meta_description', 'See the place value and face value of every digit with a chart')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Place Value Calculator</h1>
            <p class="lead small text-muted">Enter any whole number — the chart shows each digit's place (ones, tens, hundreds...), place value and face value. How to read: start from the right, and each place is 10 times bigger than the one before it.</p>
<div class="mb-3"><label class="form-label" for="pvNum">Number (digits only, e.g. 47253)</label><input type="text" class="form-control" id="pvNum" placeholder=""></div><div class="alert alert-secondary mt-3 mb-0" id="pvRes">Enter values - the result will show live here.</div><div class="table-responsive mt-3"><table class="table table-sm table-bordered"><thead><tr><th>Digit</th><th>Place</th><th>Place value</th><th>Face value</th></tr></thead><tbody id="pvTable"><tr><td colspan="4" class="text-muted">Enter a number.</td></tr></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Write a number in the box above (digits only).</li>
                <li>The chart will show each digit's place, its place value (digit x place value) and its face value (the digit itself).</li>
            </ol>
            <p class="small text-muted mb-0">Note: Face value is always the digit itself. Place value = digit x its place (1, 10, 100, 1000...). Decimals are not used here.</p>
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


var PLACES = ["Ones","Tens","Hundreds","Thousands","Ten Thousands","Hundred Thousands","Millions","Ten Millions","Hundred Millions","Billions","Ten Billions","Hundred Billions","Trillions","Ten Trillions","Hundred Trillions"];
function pvCalc() {
    var s = txt("pvNum").replace(/[^0-9]/g, "");
    var tb = document.getElementById("pvTable");
    if (!s) { setT("pvRes", "Enter a number."); tb.innerHTML = "<tr><td colspan=\"4\" class=\"text-muted\">Enter a number.</td></tr>"; return; }
    if (s.length > 15) { setT("pvRes", "Number is too long - enter up to 15 digits."); return; }
    var rows = ""; var expanded = [];
    for (var i = 0; i < s.length; i++) {
        var pos = s.length - 1 - i; var d = parseInt(s.charAt(i), 10); var pv = d * Math.pow(10, pos);
        rows += "<tr><td>" + d + "</td><td>" + PLACES[pos] + "</td><td>" + fmt(pv, 0) + "</td><td>" + d + "</td></tr>";
        if (d !== 0) { expanded.push(fmt(pv, 0)); }
    }
    tb.innerHTML = rows;
    setT("pvRes", "Expanded form: " + (expanded.length ? expanded.join(" + ") : "0") + " | Digits: " + s.length);
}
bind(["pvNum"], pvCalc); pvCalc();

})();
</script>
@endsection
