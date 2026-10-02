@extends('layouts.app')

@section('title', 'LDL Cholesterol Calculator — Free Online Tool')
@section('meta_description', 'Estimate LDL cholesterol from total cholesterol, HDL and triglycerides with the Friedewald formula')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">LDL Cholesterol Calculator</h1>
            <p class="lead small text-muted">Most lab LDL values are calculated, not measured directly. This is the Friedewald formula they use.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="tc">Total cholesterol (mg/dL)</label><input type="number" class="form-control" id="tc" value="200" step="any"></div>                    <div class="mb-3"><label class="form-label" for="hdl">HDL cholesterol (mg/dL)</label><input type="number" class="form-control" id="hdl" value="50" step="any"></div>                    <div class="mb-3"><label class="form-label" for="tg">Triglycerides (mg/dL)</label><input type="number" class="form-control" id="tg" value="150" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter total cholesterol and HDL.</li><li>Enter triglycerides.</li><li>Your estimated LDL appears instantly, with a warning if triglycerides are too high for the formula.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Friedewald: LDL = total cholesterol - HDL - triglycerides/5 (all mg/dL). The formula is unreliable when triglycerides are 400 mg/dL or higher, and also less accurate at very low LDL or after a non-fasting sample — in those cases labs use direct measurement or newer equations. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var tc = num("tc"), hdl = num("hdl"), tg = num("tg");
        if ([tc, hdl, tg].some(isNaN) || tc <= 0 || hdl <= 0 || tg < 0) { out("Please enter valid lipid values."); return; }
        var ldl = tc - hdl - tg / 5;
        var warn = tg >= 400 ? "<br><strong>Warning:</strong> triglycerides are 400 mg/dL or higher, so the Friedewald estimate is unreliable — a direct LDL measurement is preferred." : "";
        if (ldl < 0) { out("These values give a negative LDL, which suggests one of the inputs is wrong. Please check the figures."); return; }
        out("<strong>Estimated LDL:</strong> " + fmt(ldl, 0) + " mg/dL (" + fmt(ldl / 38.67, 2) + " mmol/L)" + warn);
    }
    bind(["tc", "hdl", "tg"], calc); calc();
})();
</script>
@endsection
