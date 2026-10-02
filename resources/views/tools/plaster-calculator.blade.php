@extends('layouts.app')

@section('title', 'Plaster Calculator — Free Online Tool')
@section('meta_description', 'Calculate plaster volume, cement and sand needed from wall area and thickness.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Plaster Calculator</h1>
            <p class="lead small text-muted">Calculate plaster volume, cement and sand needed from wall area and thickness.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="area" class="form-label">Plaster area (sq ft)</label><input type="number" class="form-control" id="area" value="500" step="any"></div><div class="col-md-4"><label for="thick" class="form-label">Thickness (mm)</label><input type="number" class="form-control" id="thick" value="12" step="any"></div><div class="col-md-4"><label for="mixC" class="form-label">Cement part</label><input type="number" class="form-control" id="mixC" value="1" step="any"></div><div class="col-md-4"><label for="mixS" class="form-label">Sand part</label><input type="number" class="form-control" id="mixS" value="4" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div><div class="col-md-4"><label for="priceBag" class="form-label">Price per cement bag (Rs)</label><input type="number" class="form-control" id="priceBag" value="1250" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the plaster area in sq ft.</li>
                        <li>Enter the thickness in mm — inner walls are usually 12 mm, outer walls 15-20 mm.</li>
                        <li>Enter the mix ratio (usually 1:4 or 1:6).</li>
                        <li>Cement bags and sand needed will show below.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Uneven walls can increase the actual volume, so keep some wastage. Rates change — verify with the official source before relying on this.</p>
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
        var a = v("area"), t = v("thick"), mc = v("mixC"), ms = v("mixS"), sum = mc + ms;
        if (a <= 0 || t <= 0 || sum <= 0) { bad("Enter area, thickness and mix correctly."); return; }
        var wet = a * (t / 25.4 / 12);
        var dry = wet * 1.33;
        var bagsExact = (dry * mc / sum) * 1440 / (35.3147 * 50);
        var bagsBuy = Math.ceil(bagsExact * (1 + v("waste") / 100));
        var sand = dry * ms / sum * (1 + v("waste") / 100);
        $("res").innerHTML = table([
            ["Wet plaster volume", fmt(wet) + " ft3"],
            ["Dry volume (wet x 1.33)", fmt(dry) + " ft3"],
            ["Cement bags (50 kg)", fmt(bagsExact) + " — buy " + fmt(bagsBuy, 0) + " with wastage"],
            ["Sand (with wastage)", fmt(sand) + " ft3"],
            ["Cement cost", "Rs " + fmt(bagsBuy * v("priceBag"), 0)]
        ]);
    }
  
    bind(["area", "thick", "mixC", "mixS", "waste", "priceBag"], calc);
    calc();
})();
</script>
@endsection
