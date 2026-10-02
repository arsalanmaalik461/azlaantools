@extends('layouts.app')

@section('title', 'YouTube Earnings Calculator — Free Online Tool')
@section('meta_description', 'Estimate YouTube earnings from daily views and the RPM or CPM rate entered by the creator')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">YouTube Earnings Calculator</h1>
            <p class="lead small text-muted">Estimate YouTube earnings from daily views and the RPM or CPM rate entered by the creator</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="views">Daily Views</label><input type="number" step="any" class="form-control" id="views" value="10000"></div>
                    <div class="col-md-6"><label class="form-label" for="rpm">RPM (Rs or $ earned per 1000 views, your own rate)</label><input type="number" step="any" class="form-control" id="rpm" value="150"></div>
                    <div class="col-md-6"><label class="form-label" for="days">Days</label><input type="number" step="any" class="form-control" id="days" value="30"></div>
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
                        <li>Earnings are views divided by 1000, multiplied by your RPM. Use the RPM from your own YouTube Studio for a realistic figure.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Estimate only, in the same currency as the RPM you enter. RPM changes with niche, country of viewers and season — no live data is used.</p>
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
        var daily = n("views") / 1000 * n("rpm"); setResult("Estimated daily earnings: " + num(daily) + "<br>Estimated earnings in " + num(n("days")) + " days: " + num(daily * n("days")) + "<br>Estimated yearly earnings at same pace: " + num(daily * 365)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
