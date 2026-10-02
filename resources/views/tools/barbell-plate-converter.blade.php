@extends('layouts.app')

@section('title', 'Barbell Plate Calculator — Free Online Tool')
@section('meta_description', 'Enter your target weight and instantly see which plates go on each side of the barbell, in kg and lb.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Barbell Plate Calculator</h1>
            <p class="lead small text-muted">Enter your target weight and instantly see which plates go on each side of the barbell, in kg and lb.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="unit" class="form-label">Unit</label><select class="form-select" id="unit"><option value="kg">Kilograms (kg)</option><option value="lb">Pounds (lb)</option></select></div><div class="col-md-4"><label for="target" class="form-label">Target total weight</label><input type="number" class="form-control" id="target" value="100" step="any"></div><div class="col-md-4"><label for="bar" class="form-label">Bar weight (same unit)</label><input type="number" class="form-control" id="bar" value="20" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select the unit — kg or lb.</li>
                        <li>Enter the target total weight (including the bar).</li>
                        <li>Enter the bar weight — an Olympic bar is usually 20 kg / 45 lb, a smaller bar 15 kg / 35 lb.</li>
                        <li>The list of plates for each side will be shown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Plates are chosen from largest to smallest (greedy method), which is the common gym practice. Collar weight (usually 2.5 kg per pair) is not included.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function $(id) { return document.getElementById(id); }
    function v(id) { var n = parseFloat($(id).value); return isFinite(n) ? n : 0; }
    function fmt(n, d) { if (!isFinite(n)) { return "—"; } if (d === undefined) { d = 2; } return n.toLocaleString("en-US", { maximumFractionDigits: d }); }
    function fmtd(n, d) { if (!isFinite(n)) { return "—"; } return n.toLocaleString("en-US", { maximumFractionDigits: d, minimumFractionDigits: d }); }
    function table(rowsArr) { var h = "<table class=\"table table-sm align-middle mb-0\"><tbody>"; rowsArr.forEach(function (r) { h += "<tr><td>" + r[0] + "</td><td class=\"text-end fw-semibold\">" + r[1] + "</td></tr>"; }); return h + "</tbody></table>"; }
    function bad(msg) { $("res").innerHTML = "<span class=\"text-danger fw-semibold\">" + msg + "</span>"; }
    function parseList(id) { return $(id).value.split(/[\s,;]+/).map(function (x) { return parseFloat(x); }).filter(function (x) { return isFinite(x); }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = $(id); if (el) { el.addEventListener("input", fn); el.addEventListener("change", fn); } }); }

    function calc() {
        var unit = $("unit").value, target = v("target"), bar = v("bar");
        if (target <= 0 || bar < 0) { bad("Enter valid target and bar weights."); return; }
        if (target < bar) { bad("Target is less than the bar — the bar alone already weighs this much."); return; }
        var plates = unit === "kg" ? [25, 20, 15, 10, 5, 2.5, 1.25, 0.5] : [45, 35, 25, 10, 5, 2.5];
        var perSide = (target - bar) / 2, rem = perSide, used = [];
        plates.forEach(function (p) { var c = Math.floor(rem / p); if (c > 0) { used.push(fmt(c, 0) + " x " + fmt(p) + " " + unit); rem -= c * p; } });
        var loaded = perSide - rem;
        var actual = bar + 2 * loaded;
        $("res").innerHTML = table([
            ["Per side load needed", fmt(perSide) + " " + unit],
            ["Plates per side (load from inside out)", used.length ? used.join(", ") : "No plates — bar only"],
            ["Actual loaded weight", fmt(actual) + " " + unit],
            ["Difference from target", fmt(actual - target) + " " + unit + (rem > 0 ? " (not exact with the smallest available plate)" : " — exact match")]
        ]);
    }
  
    bind(["unit", "target", "bar"], calc);
    calc();
})();
</script>
@endsection
