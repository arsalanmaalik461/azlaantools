@extends('layouts.app')

@section('title', 'Carpet Calculator — Free Online Tool')
@section('meta_description', 'Calculate carpet area and roll length needed for rooms with fitting waste.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Carpet Calculator</h1>
            <p class="lead small text-muted">Calculate carpet area and roll length needed for rooms with fitting waste.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="roomL" class="form-label">Room length (ft)</label><input type="number" class="form-control" id="roomL" value="14" step="any"></div><div class="col-md-4"><label for="roomW" class="form-label">Room width (ft)</label><input type="number" class="form-control" id="roomW" value="12" step="any"></div><div class="col-md-4"><label for="rollW" class="form-label">Carpet roll width (ft)</label><input type="number" class="form-control" id="rollW" value="12" step="any"></div><div class="col-md-4"><label for="waste" class="form-label">Fitting waste (%)</label><input type="number" class="form-control" id="waste" value="10" step="any"></div><div class="col-md-4"><label for="priceSqft" class="form-label">Carpet rate (Rs per sq ft of roll)</label><input type="number" class="form-control" id="priceSqft" value="250" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the room's length and width in feet.</li>
                        <li>Confirm the roll width from the shop and enter it.</li>
                        <li>The calculator compares both cutting directions and selects the best (shorter roll length) option.</li>
                        <li>See the total roll length and cost including wastage.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Carpet is laid from a roll, so the real cost depends on the number of strips and the roll length, not the full area. Leftover (offcut) area can sometimes be used in another room. Rates change — verify with the official source before relying on this.</p>
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
        var L = v("roomL"), W = v("roomW"), rw = v("rollW"), waste = v("waste");
        if (L <= 0 || W <= 0 || rw <= 0) { bad("Enter valid room and roll sizes."); return; }
        var stripsA = Math.ceil(W / rw), lenA = stripsA * L;
        var stripsB = Math.ceil(L / rw), lenB = stripsB * W;
        var useA = lenA <= lenB;
        var strips = useA ? stripsA : stripsB, rollLen = useA ? lenA : lenB;
        var roomArea = L * W;
        var rollArea = rollLen * rw;
        var buyArea = rollArea * (1 + waste / 100);
        var buyLen = buyArea / rw;
        $("res").innerHTML = table([
            ["Room area", fmt(roomArea) + " sq ft"],
            ["Option A: strips across width", fmt(stripsA, 0) + " strips, roll length " + fmt(lenA) + " ft"],
            ["Option B: strips across length", fmt(stripsB, 0) + " strips, roll length " + fmt(lenB) + " ft"],
            ["Best option", (useA ? "Option A" : "Option B") + " — " + fmt(strips, 0) + " strips"],
            ["Roll length to buy (with " + fmt(waste, 0) + "% waste)", fmt(buyLen) + " ft"],
            ["Roll area charged", fmt(buyArea) + " sq ft"],
            ["Offcut / extra area", fmt(Math.max(0, buyArea - roomArea)) + " sq ft"],
            ["Estimated cost", "Rs " + fmt(buyArea * v("priceSqft"), 0)]
        ]);
    }
  
    bind(["roomL", "roomW", "rollW", "waste", "priceSqft"], calc);
    calc();
})();
</script>
@endsection
