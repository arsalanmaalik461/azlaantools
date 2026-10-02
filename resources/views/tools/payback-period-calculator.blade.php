@extends('layouts.app')

@section('title', 'Payback Period Calculator — Free Online Tool')
@section('meta_description', 'Calculate how many years and months an investment takes to recover its cost from yearly cash inflows')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Payback Period Calculator</h1>
            <p class="lead small text-muted">Calculate how many years and months an investment takes to recover its cost from yearly cash inflows</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="initial">Initial Investment (Rs)</label><input type="number" step="any" class="form-control" id="initial" value="1000000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf1">Year 1 Cash Inflow (Rs)</label><input type="number" step="any" class="form-control" id="cf1" value="250000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf2">Year 2 Cash Inflow (Rs)</label><input type="number" step="any" class="form-control" id="cf2" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf3">Year 3 Cash Inflow (Rs)</label><input type="number" step="any" class="form-control" id="cf3" value="350000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf4">Year 4 Cash Inflow (Rs)</label><input type="number" step="any" class="form-control" id="cf4" value="400000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf5">Year 5 Cash Inflow (Rs)</label><input type="number" step="any" class="form-control" id="cf5" value="450000"></div>
                    </div>
                    
                    <div id="msg" class="alert alert-warning mt-3 d-none"></div>
                    <div class="border rounded p-3 mt-3">
                        <div class="small text-muted mb-1">Result</div>
                        <div id="result" class="fw-semibold">Enter values to see the result.</div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Fill in the fields above with your own figures — results update live as you type.</li>
                        <li>Inflows are added year by year until they cover the investment. The part year is estimated by the fraction of that years inflow still needed.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Payback ignores cash flows after recovery and the time value of money — use NPV alongside it for big decisions.</p>
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
    function n(id) { var v = parseFloat(document.getElementById(id).value); return isNaN(v) ? 0 : v; }
    function t(id) { return document.getElementById(id).value; }
    function fmt(x) { return "Rs " + Math.round(x).toLocaleString("en-US"); }
    function fmt2(x) { return "Rs " + x.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function pct(x) { return x.toFixed(2) + "%"; }
    function num(x) { return x.toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function showMsg(m) { var b = document.getElementById("msg"); if (m) { b.textContent = m; b.classList.remove("d-none"); } else { b.classList.add("d-none"); } }
    function setResult(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var cfs = [n("cf1"), n("cf2"), n("cf3"), n("cf4"), n("cf5")]; var cum = 0, ans = "Not recovered within 5 years"; for (var i = 0; i < 5; i++) { if (cum + cfs[i] >= n("initial") && cfs[i] > 0) { var frac = (n("initial") - cum) / cfs[i]; var yrs = i + frac; ans = num(yrs) + " years (about " + Math.floor(yrs) + " years and " + Math.round((yrs - Math.floor(yrs)) * 12) + " months)"; break; } cum += cfs[i]; } var totalIn = cfs[0] + cfs[1] + cfs[2] + cfs[3] + cfs[4]; setResult("Total inflows in 5 years: " + fmt(totalIn) + "<br><strong>Payback period: " + ans + "</strong>"); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
