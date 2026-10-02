@extends('layouts.app')

@section('title', 'Current Ratio Calculator — Free Online Tool')
@section('meta_description', 'Calculate current ratio and quick ratio from current assets, inventory and current liabilities')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Current Ratio Calculator</h1>
            <p class="lead small text-muted">Calculate current ratio and quick ratio from current assets, inventory and current liabilities</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="assets">Current Assets (Rs)</label><input type="number" step="any" class="form-control" id="assets" value="800000"></div>
                    <div class="col-md-6"><label class="form-label" for="inventory">Inventory Included in Assets (Rs)</label><input type="number" step="any" class="form-control" id="inventory" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="liab">Current Liabilities (Rs)</label><input type="number" step="any" class="form-control" id="liab" value="400000"></div>
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
                        <li>Current ratio divides all current assets by current liabilities. Quick ratio removes inventory because it sells slowly.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> A current ratio near 2 and quick ratio near 1 are common comfort levels, but healthy levels differ by trade.</p>
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
        if (n("liab") <= 0) { showMsg("Enter current liabilities above zero."); return; } showMsg(""); var cr = n("assets") / n("liab"); var qr = (n("assets") - n("inventory")) / n("liab"); setResult("Current ratio: " + num(cr) + " : 1<br>Quick ratio (acid test): " + num(qr) + " : 1<br>Working capital: " + fmt(n("assets") - n("liab")));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
