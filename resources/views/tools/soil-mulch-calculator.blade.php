@extends('layouts.app')

@section('title', 'Soil and Mulch Calculator — Free Online Tool')
@section('meta_description', 'Calculate soil or mulch volume and bags needed for garden beds and planters.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Soil and Mulch Calculator</h1>
            <p class="lead small text-muted">Calculate soil or mulch volume and bags needed for garden beds and planters.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="bedL" class="form-label">Bed / planter length (ft)</label><input type="number" class="form-control" id="bedL" value="10" step="any"></div><div class="col-md-4"><label for="bedW" class="form-label">Width (ft)</label><input type="number" class="form-control" id="bedW" value="4" step="any"></div><div class="col-md-4"><label for="dep" class="form-label">Depth (in)</label><input type="number" class="form-control" id="dep" value="3" step="any"></div><div class="col-md-4"><label for="bagL" class="form-label">Bag size (liters)</label><input type="number" class="form-control" id="bagL" value="50" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Extra (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div><div class="col-md-4"><label for="priceBag" class="form-label">Price per bag (Rs)</label><input type="number" class="form-control" id="priceBag" value="600" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the bed or planter's length and width.</li>
                        <li>Enter the depth of soil / mulch in inches.</li>
                        <li>Enter the bag size in liters (written on the bag).</li>
                        <li>The number of bags and the cost will show.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Soil settles after filling and becomes less — that is why the extra % is included. For large amounts, a trolley or truck load is often cheaper than bags. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("bedL"), W = v("bedW"), D = v("dep"), bag = v("bagL");
        if (L <= 0 || W <= 0 || D <= 0 || bag <= 0) { bad("Please enter a valid size and bag size."); return; }
        var ft3 = L * W * (D / 12), liters = ft3 * 28.3168;
        var need = liters * (1 + v("waste") / 100);
        var bags = Math.ceil(need / bag);
        $("res").innerHTML = table([
            ["Volume", fmt(ft3) + " ft3 (" + fmt(ft3 / 35.3147) + " m3)"],
            ["Volume in liters", fmt(liters, 0) + " liters"],
            ["Bags needed (with extra)", fmt(bags, 0)],
            ["Estimated cost", "Rs " + fmt(bags * v("priceBag"), 0)]
        ]);
    }
  
    bind(["bedL", "bedW", "dep", "bagL", "waste", "priceBag"], calc);
    calc();
})();
</script>
@endsection
