@extends('layouts.app')

@section('title', 'Staircase Calculator — Free Online Tool')
@section('meta_description', 'Calculate stair steps, riser height and tread depth from total floor height.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Staircase Calculator</h1>
            <p class="lead small text-muted">Calculate stair steps, riser height and tread depth from total floor height.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="rise" class="form-label">Total floor to floor height (in)</label><input type="number" class="form-control" id="rise" value="120" step="any"></div><div class="col-md-4"><label for="riser" class="form-label">Target riser height (in)</label><input type="number" class="form-control" id="riser" value="7" step="any"></div><div class="col-md-4"><label for="tread" class="form-label">Tread depth (in)</label><input type="number" class="form-control" id="tread" value="10" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the total floor-to-floor height in inches.</li>
                        <li>Enter the target riser (usually 6.5-7.5 inches) and tread depth (usually 10-11 inches).</li>
                        <li>The step count is chosen so all risers are exactly equal — that is the mark of a safe staircase.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Blondel formula: 2 x riser + tread = 23-26 inches is considered comfortable. Confirm local building codes and space limits.</p>
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
        var total = v("rise"), target = v("riser"), tread = v("tread");
        if (total <= 0 || target <= 0 || tread <= 0) { bad("Please enter a valid height, riser and tread."); return; }
        var steps = Math.ceil(total / target);
        var actual = total / steps;
        var treads = steps - 1;
        var run = treads * tread;
        var angle = Math.atan(actual / tread) * 180 / Math.PI;
        var blondel = 2 * actual + tread;
        var comfort = (blondel >= 23 && blondel <= 26) ? "in the comfortable range (23-26 in)" : "outside the comfortable range (23-26 in) — adjust tread or riser";
        var stringer = Math.sqrt(Math.pow(total, 2) + Math.pow(run, 2));
        $("res").innerHTML = table([
            ["Steps (risers)", fmt(steps, 0)],
            ["Actual riser height", fmtd(actual, 2) + " in"],
            ["Treads (steps - 1)", fmt(treads, 0)],
            ["Total run (horizontal length)", fmt(run) + " in (" + fmt(run / 12) + " ft)"],
            ["Stair angle", fmtd(angle, 1) + " degrees"],
            ["2 x riser + tread (Blondel)", fmtd(blondel, 2) + " in — " + comfort],
            ["Approx stringer length", fmt(stringer) + " in"]
        ]);
    }
  
    bind(["rise", "riser", "tread"], calc);
    calc();
})();
</script>
@endsection
