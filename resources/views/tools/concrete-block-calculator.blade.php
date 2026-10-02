@extends('layouts.app')

@section('title', 'Concrete Block Calculator — Free Online Tool')
@section('meta_description', 'Calculate concrete blocks and mortar needed for a block wall.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Concrete Block Calculator</h1>
            <p class="lead small text-muted">Calculate concrete blocks and mortar needed for a block wall.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="wallL" class="form-label">Wall length (ft)</label><input type="number" class="form-control" id="wallL" value="20" step="any"></div><div class="col-md-4"><label for="wallH" class="form-label">Wall height (ft)</label><input type="number" class="form-control" id="wallH" value="10" step="any"></div><div class="col-md-4"><label for="wallT" class="form-label">Block width / wall thickness (in)</label><select class="form-select" id="wallT"><option value="6">6 in</option><option value="4">4 in</option><option value="8">8 in</option></select></div><div class="col-md-4"><label for="blockL" class="form-label">Block length (in)</label><input type="number" class="form-control" id="blockL" value="16" step="any"></div><div class="col-md-4"><label for="blockH" class="form-label">Block height (in)</label><input type="number" class="form-control" id="blockH" value="8" step="any"></div><div class="col-md-4"><label for="joint" class="form-label">Mortar joint (in)</label><input type="number" class="form-control" id="joint" value="0.5" step="any"></div><div class="col-md-4"><label for="mixC" class="form-label">Mortar cement part</label><input type="number" class="form-control" id="mixC" value="1" step="any"></div><div class="col-md-4"><label for="mixS" class="form-label">Mortar sand part</label><input type="number" class="form-control" id="mixS" value="4" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="5" step="any"></div><div class="col-md-4"><label for="priceBlock" class="form-label">Price per block (Rs)</label><input type="number" class="form-control" id="priceBlock" value="180" step="any"></div><div class="col-md-4"><label for="priceBag" class="form-label">Price per cement bag (Rs)</label><input type="number" class="form-control" id="priceBag" value="1250" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the wall length, height and block width.</li>
                        <li>Enter the block face size (usually 16 x 8 inch) and the mortar joint.</li>
                        <li>Enter the mix ratio and wastage.</li>
                        <li>The full breakdown of blocks, mortar cement and sand will appear.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> The block count is calculated with the face-area method (including the joint), while mortar is calculated as the wall volume minus the actual volume of the blocks. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("wallL"), H = v("wallH"), th = v("wallT");
        var bl = v("blockL"), bh = v("blockH"), j = v("joint");
        var mc = v("mixC"), ms = v("mixS"), waste = v("waste");
        if (L <= 0 || H <= 0 || bl <= 0 || bh <= 0 || (mc + ms) <= 0) { bad("Please enter the wall and block sizes correctly."); return; }
        var face = (bl + j) * (bh + j);
        var raw = (L * H * 144) / face;
        var blocks = Math.ceil(raw * (1 + waste / 100));
        var wallVol = L * H * (th / 12);
        var blockVol = bl * bh * th / 1728;
        var wet = wallVol - raw * blockVol; if (wet < 0) { wet = 0; }
        var dry = wet * 1.33;
        var bags = (dry * mc / (mc + ms)) * 1440 / (35.3147 * 50);
        var sand = dry * ms / (mc + ms);
        var cost = blocks * v("priceBlock") + Math.ceil(bags) * v("priceBag");
        $("res").innerHTML = table([
            ["Wall area", fmt(L * H) + " sq ft"],
            ["Blocks needed (with wastage)", fmt(blocks, 0)],
            ["Wet mortar", fmt(wet) + " ft3"],
            ["Cement bags (50 kg)", fmt(bags) + " — buy " + fmt(Math.ceil(bags), 0)],
            ["Sand", fmt(sand) + " ft3"],
            ["Estimated block + cement cost", "Rs " + fmt(cost, 0)]
        ]);
    }
  
    bind(["wallL", "wallH", "wallT", "blockL", "blockH", "joint", "mixC", "mixS", "waste", "priceBlock", "priceBag"], calc);
    calc();
})();
</script>
@endsection
