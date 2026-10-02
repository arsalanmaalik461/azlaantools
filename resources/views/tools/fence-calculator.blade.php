@extends('layouts.app')

@section('title', 'Fence Calculator — Free Online Tool')
@section('meta_description', 'Calculate fence posts, panels and rails needed from total fence length.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fence Calculator</h1>
            <p class="lead small text-muted">Calculate fence posts, panels and rails needed from total fence length.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="flen" class="form-label">Total fence length (ft)</label><input type="number" class="form-control" id="flen" value="120" step="any"></div><div class="col-md-4"><label for="spacing" class="form-label">Post spacing (ft)</label><input type="number" class="form-control" id="spacing" value="8" step="any"></div><div class="col-md-4"><label for="panelW" class="form-label">Panel width (ft)</label><input type="number" class="form-control" id="panelW" value="8" step="any"></div><div class="col-md-4"><label for="rails" class="form-label">Rails per panel</label><input type="number" class="form-control" id="rails" value="2" step="any"></div><div class="col-md-4"><label for="pricePost" class="form-label">Price per post (Rs)</label><input type="number" class="form-control" id="pricePost" value="900" step="any"></div><div class="col-md-4"><label for="pricePanel" class="form-label">Price per panel (Rs)</label><input type="number" class="form-control" id="pricePanel" value="3500" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the total boundary length in feet.</li>
                        <li>Enter the post spacing and panel width.</li>
                        <li>Enter the number of rails per panel.</li>
                        <li>The calculation for posts, panels and rails will appear.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> For a closed boundary, corner posts are shared — the total posts may equal the number of segments. Subtract the gate area separately. Rates change — verify with the official source before relying on this.</p>
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
        var len = v("flen"), sp = v("spacing"), pw = v("panelW"), rails = v("rails");
        if (len <= 0 || sp <= 0 || pw <= 0) { bad("Please enter valid length, spacing and panel width."); return; }
        var segments = Math.ceil(len / sp);
        var posts = segments + 1;
        var panels = Math.ceil(len / pw);
        var railCount = panels * rails;
        var railLen = len * rails;
        var cost = posts * v("pricePost") + panels * v("pricePanel");
        $("res").innerHTML = table([
            ["Segments (bays)", fmt(segments, 0)],
            ["Posts needed (segments + 1)", fmt(posts, 0)],
            ["Panels needed", fmt(panels, 0)],
            ["Rails needed", fmt(railCount, 0)],
            ["Total rail length", fmt(railLen) + " ft"],
            ["Estimated posts + panels cost", "Rs " + fmt(cost, 0)]
        ]);
    }
  
    bind(["flen", "spacing", "panelW", "rails", "pricePost", "pricePanel"], calc);
    calc();
})();
</script>
@endsection
