@extends('layouts.app')

@section('title', 'Pool Volume Calculator — Free Online Tool')
@section('meta_description', 'Calculate pool water volume in liters and fill time from pool dimensions.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Pool Volume Calculator</h1>
            <p class="lead small text-muted">Calculate pool water volume in liters and fill time from pool dimensions.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="shape" class="form-label">Pool shape</label><select class="form-select" id="shape"><option value="rect">Rectangular</option><option value="round">Round</option><option value="oval">Oval</option></select></div><div class="col-md-4"><label for="len" class="form-label">Length (ft)</label><input type="number" class="form-control" id="len" value="30" step="any"></div><div class="col-md-4"><label for="wid" class="form-label">Width (ft)</label><input type="number" class="form-control" id="wid" value="15" step="any"></div><div class="col-md-4"><label for="diam" class="form-label">Diameter (for round pool, ft)</label><input type="number" class="form-control" id="diam" value="20" step="any"></div><div class="col-md-4"><label for="dep" class="form-label">Average depth (ft)</label><input type="number" class="form-control" id="dep" value="5" step="any"></div><div class="col-md-4"><label for="flow" class="form-label">Pump / supply flow (liters per minute)</label><input type="number" class="form-control" id="flow" value="50" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select the pool shape.</li>
                        <li>Enter length, width / diameter and average depth — for a sloped floor, take the average of the deep and shallow parts.</li>
                        <li>Enter the pump or supply flow to also get the fill time.</li>
                        <li>Capacity shows in liters and gallons.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> 1 ft3 = 28.3168 liters. Chemical dosing is always based on the actual capacity, so this calculation also works for dosing.</p>
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
        var shape = $("shape").value, D = v("dep"), ft3 = 0;
        if (D <= 0) { bad("Please enter a valid depth."); return; }
        if (shape === "rect") { if (v("len") <= 0 || v("wid") <= 0) { bad("Please enter length and width."); return; } ft3 = v("len") * v("wid") * D; }
        else if (shape === "round") { if (v("diam") <= 0) { bad("Please enter the diameter."); return; } var r = v("diam") / 2; ft3 = Math.PI * r * r * D; }
        else { if (v("len") <= 0 || v("wid") <= 0) { bad("Please enter length and width."); return; } ft3 = Math.PI * (v("len") / 2) * (v("wid") / 2) * D; }
        var liters = ft3 * 28.3168;
        var hours = v("flow") > 0 ? liters / v("flow") / 60 : 0;
        $("res").innerHTML = table([
            ["Volume", fmt(ft3) + " ft3 (" + fmt(ft3 / 35.3147) + " m3)"],
            ["Water capacity", fmt(liters, 0) + " liters"],
            ["US gallons", fmt(liters / 3.785, 0)],
            ["Fill time at given flow", v("flow") > 0 ? fmt(hours) + " hours" : "Enter the flow"]
        ]);
    }
  
    bind(["shape", "len", "wid", "diam", "dep", "flow"], calc);
    calc();
})();
</script>
@endsection
