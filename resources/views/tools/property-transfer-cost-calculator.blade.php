@extends('layouts.app')

@section('title', 'Property Transfer Cost Calculator — Free Online Tool')
@section('meta_description', 'Enter your plot or house price and get the total of stamp duty, CVT and transfer fees.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Property Transfer Cost Calculator</h1>
            <p class="lead small text-muted">Enter your plot or house price and get the total of stamp duty, CVT and transfer fees.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="price">Property / DC Value (Rs)</label><input type="number" step="any" class="form-control" id="price" value="10000000"></div>
                    <div class="col-md-6"><label class="form-label" for="stampPct">Stamp Duty % (editable)</label><input type="number" step="any" class="form-control" id="stampPct" value="1"></div>
                    <div class="col-md-6"><label class="form-label" for="cvtPct">CVT % (editable)</label><input type="number" step="any" class="form-control" id="cvtPct" value="1"></div>
                    <div class="col-md-6"><label class="form-label" for="whtRate">Withholding Tax % 236K/236C (editable)</label><input type="number" step="any" class="form-control" id="whtRate" value="3"></div>
                    <div class="col-md-6"><label class="form-label" for="registry">Registry / Registration Fee (Rs)</label><input type="number" step="any" class="form-control" id="registry" value="50000"></div>
                    <div class="col-md-6"><label class="form-label" for="commPct">Agent Commission % (editable)</label><input type="number" step="any" class="form-control" id="commPct" value="1"></div>
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
                        <li>Each percentage head is charged on the property value. Filer and non-filer withholding rates differ, so edit the rate field for your case.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Estimate only. DC rates, filer status and provincial charges change the final bill. Rates change — verify with the official source before relying on this.</p>
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
        var p = n("price"); var stamp = p * n("stampPct") / 100, cvt = p * n("cvtPct") / 100, wht = p * n("whtRate") / 100, comm = p * n("commPct") / 100; var total = stamp + cvt + wht + n("registry") + comm; setResult("Stamp duty: " + fmt(stamp) + "<br>CVT: " + fmt(cvt) + "<br>Withholding tax: " + fmt(wht) + "<br>Registry fee: " + fmt(n("registry")) + "<br>Agent commission: " + fmt(comm) + "<br><strong>Total transfer cost: " + fmt(total) + "</strong><br>Total with property price: " + fmt(p + total)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
