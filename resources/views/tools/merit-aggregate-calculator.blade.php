@extends('layouts.app')

@section('title', 'Merit Aggregate Calculator — Free Online Tool')
@section('meta_description', 'Find your university merit aggregate percent from Matric, Inter and entry test marks.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Merit Aggregate Calculator</h1>
            <p class="lead small text-muted">Enter your Matric, Inter and entry test marks — you will get each part's weighted contribution and the total aggregate percent with steps. Weights are editable.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                                        <div class="col-md-4"><label class="form-label" for="maMO">Matric obtained</label><input type="number" class="form-control inp" id="maMO" placeholder="e.g. 950" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="maMT">Matric total</label><input type="number" class="form-control inp" id="maMT" placeholder="1100" step="any" value="1100"></div>
                                        <div class="col-md-4"><label class="form-label" for="maIO">Inter obtained</label><input type="number" class="form-control inp" id="maIO" placeholder="e.g. 980" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="maIT">Inter total</label><input type="number" class="form-control inp" id="maIT" placeholder="1100" step="any" value="1100"></div>
                                        <div class="col-md-4"><label class="form-label" for="maEO">Entry test obtained</label><input type="number" class="form-control inp" id="maEO" placeholder="e.g. 170" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="maET">Entry test total</label><input type="number" class="form-control inp" id="maET" placeholder="200" step="any" value="200"></div>
                    </div>
                    <div class="row g-3 mt-1">
                                        <div class="col-md-4"><label class="form-label" for="maPreset">Formula preset</label><select class="form-select inp" id="maPreset"><option value="mdcat">MDCAT / PMC (10 / 40 / 50)</option><option value="uet">UET ECAT (17 / 50 / 33)</option><option value="custom">Custom weights</option></select></div>                    <div class="col-md-4"><label class="form-label" for="maWM">Matric weight %</label><input type="number" class="form-control inp" id="maWM" placeholder="10" step="any" value="10"></div>                    <div class="col-md-4"><label class="form-label" for="maWI">Inter weight %</label><input type="number" class="form-control inp" id="maWI" placeholder="40" step="any" value="40"></div>                    <div class="col-md-4"><label class="form-label" for="maWE">Entry test weight %</label><input type="number" class="form-control inp" id="maWE" placeholder="50" step="any" value="50"></div>
                    </div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter obtained and total marks for all three stages.</li><li>Choose your university/test preset or enter your own weights.</li><li>Aggregate = the sum of the three weighted percents.</li></ol>
                    <p class="small text-muted mb-0">Note: Default weights are from well-known published formulas (MDCAT/PMC: Matric 10%, Inter 40%, Entry 50%; UET: 17/50/33). The formula can differ every year and for every university. Always verify with the official source before relying on this.</p>
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
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    function applyPreset() { var p = el("maPreset").value; if (p === "mdcat") { el("maWM").value = 10; el("maWI").value = 40; el("maWE").value = 50; } if (p === "uet") { el("maWM").value = 17; el("maWI").value = 50; el("maWE").value = 33; } calc(); }
    function calc() {
        var mo = num("maMO"), mt = num("maMT"), io = num("maIO"), it = num("maIT"), eo = num("maEO"), et = num("maET");
        var wm = num("maWM"), wi = num("maWI"), we = num("maWE");
        if ([mo, mt, io, it, eo, et, wm, wi, we].some(function (v) { return v === null; })) { err("Please fill in all fields."); return; }
        if (mt <= 0 || it <= 0 || et <= 0) { err("Totals must be greater than 0."); return; }
        if (mo > mt || io > it || eo > et) { err("Obtained marks cannot be more than the total."); return; }
        var cm = mo / mt * wm, ci = io / it * wi, ce = eo / et * we, total = cm + ci + ce;
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Merit Aggregate = " + fmt(total, 4) + "%</strong></div>");
        showSteps(["Matric: " + fmt(mo, 0) + "/" + fmt(mt, 0) + " = " + fmt(mo / mt * 100, 2) + "% x weight " + fmt(wm, 1) + "% → contribution = " + fmt(cm, 4), "Inter: " + fmt(io, 0) + "/" + fmt(it, 0) + " = " + fmt(io / it * 100, 2) + "% x weight " + fmt(wi, 1) + "% → contribution = " + fmt(ci, 4), "Entry test: " + fmt(eo, 0) + "/" + fmt(et, 0) + " = " + fmt(eo / et * 100, 2) + "% x weight " + fmt(we, 1) + "% → contribution = " + fmt(ce, 4), "Total aggregate = " + fmt(cm, 3) + " + " + fmt(ci, 3) + " + " + fmt(ce, 3) + " = " + fmt(total, 4) + "%", "Total of weights = " + fmt(wm + wi + we, 1) + "%" + (Math.abs(wm + wi + we - 100) > 0.01 ? " — note: weights should usually add up to 100" : "")]);
    }
    el("maPreset").addEventListener("change", applyPreset);
    bind(calc); calc();

})();
</script>
@endsection
