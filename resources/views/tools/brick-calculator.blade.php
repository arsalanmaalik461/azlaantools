@extends('layouts.app')

@section('title', 'Brick Calculator — Free Online Tool')
@section('meta_description', 'Calculate bricks and mortar needed for a wall of any size and thickness.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Brick Calculator</h1>
            <p class="lead small text-muted">Calculate bricks and mortar needed for a wall of any size and thickness.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="wallL" class="form-label">Wall length (ft)</label><input type="number" class="form-control" id="wallL" value="20" step="any"></div><div class="col-md-4"><label for="wallH" class="form-label">Wall height (ft)</label><input type="number" class="form-control" id="wallH" value="10" step="any"></div><div class="col-md-4"><label for="wallT" class="form-label">Wall thickness (in)</label><select class="form-select" id="wallT"><option value="9">9 in (single brick)</option><option value="4.5">4.5 in (half brick)</option><option value="13.5">13.5 in (thick)</option></select></div><div class="col-md-4"><label for="brickL" class="form-label">Brick length (in)</label><input type="number" class="form-control" id="brickL" value="9" step="any"></div><div class="col-md-4"><label for="brickW" class="form-label">Brick width (in)</label><input type="number" class="form-control" id="brickW" value="4.5" step="any"></div><div class="col-md-4"><label for="brickH" class="form-label">Brick height (in)</label><input type="number" class="form-control" id="brickH" value="3" step="any"></div><div class="col-md-4"><label for="joint" class="form-label">Mortar joint (in)</label><input type="number" class="form-control" id="joint" value="0.5" step="any"></div><div class="col-md-4"><label for="mixC" class="form-label">Mortar cement part</label><input type="number" class="form-control" id="mixC" value="1" step="any"></div><div class="col-md-4"><label for="mixS" class="form-label">Mortar sand part</label><input type="number" class="form-control" id="mixS" value="4" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Wastage (%)</label><input type="number" class="form-control" id="waste" value="5" step="any"></div><div class="col-md-4"><label for="priceBrick" class="form-label">Price per brick (Rs)</label><input type="number" class="form-control" id="priceBrick" value="15" step="any"></div><div class="col-md-4"><label for="priceBag" class="form-label">Price per cement bag (Rs)</label><input type="number" class="form-control" id="priceBag" value="1250" step="any"></div><div class="col-md-4"><label for="priceSand" class="form-label">Sand rate (Rs per ft3)</label><input type="number" class="form-control" id="priceSand" value="120" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the wall length, height, and thickness.</li>
                        <li>Confirm the brick size and mortar joint — a common brick in Pakistan is 9 x 4.5 x 3 inches.</li>
                        <li>Enter the mix ratio (for example 1:4) and wastage.</li>
                        <li>The breakdown of bricks, cement bags, sand, and total cost will show instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Standard volume method is used: each brick is counted with its mortar joint and divided into the wall volume. Wet volume is multiplied by 1.33 for dry mortar, and the cement bag is 50 kg (density 1440 kg per m3) based. Rates change — verify with the official source before relying on this.</p>
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
        var bl = v("brickL"), bw = v("brickW"), bh = v("brickH"), j = v("joint");
        var mc = v("mixC"), ms = v("mixS"), waste = v("waste");
        if (L <= 0 || H <= 0 || bl <= 0 || bw <= 0 || bh <= 0 || (mc + ms) <= 0) { bad("Enter all wall and brick sizes correctly."); return; }
        var wallVol = L * H * (th / 12);
        var brickMortVol = (bl + j) * (bw + j) * (bh + j) / 1728;
        var raw = wallVol / brickMortVol;
        var bricks = Math.ceil(raw * (1 + waste / 100));
        var brickVol = bl * bw * bh / 1728;
        var wet = wallVol - raw * brickVol; if (wet < 0) { wet = 0; }
        var dry = wet * 1.33;
        var cemVol = dry * mc / (mc + ms);
        var bags = cemVol * 1440 / (35.3147 * 50);
        var sand = dry * ms / (mc + ms);
        var cost = bricks * v("priceBrick") + Math.ceil(bags) * v("priceBag") + sand * v("priceSand");
        $("res").innerHTML = table([
            ["Wall volume", fmt(wallVol) + " ft3"],
            ["Bricks needed (with " + fmt(waste, 0) + "% wastage)", fmt(bricks, 0)],
            ["Wet mortar", fmt(wet) + " ft3"],
            ["Dry mortar (wet x 1.33)", fmt(dry) + " ft3"],
            ["Cement bags (50 kg)", fmt(bags) + " — buy " + fmt(Math.ceil(bags), 0)],
            ["Sand", fmt(sand) + " ft3"],
            ["Estimated material cost", "Rs " + fmt(cost, 0)]
        ]);
    }
  
    bind(["wallL", "wallH", "wallT", "brickL", "brickW", "brickH", "joint", "mixC", "mixS", "waste", "priceBrick", "priceBag", "priceSand"], calc);
    calc();
})();
</script>
@endsection
