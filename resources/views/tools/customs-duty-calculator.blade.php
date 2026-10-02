@extends('layouts.app')

@section('title', 'Customs Duty Calculator — Free Online Tool')
@section('meta_description', 'Enter import value, duty and sales tax percentages to get total customs charges and landed cost.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Customs Duty Calculator</h1>
            <p class="lead small text-muted">Enter import value, duty and sales tax percentages to get total customs charges and landed cost.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="value">Import / CIF Value (Rs)</label><input type="number" step="any" class="form-control" id="value" value="1000000"></div>
                    <div class="col-md-6"><label class="form-label" for="dutyPct">Customs Duty % (editable)</label><input type="number" step="any" class="form-control" id="dutyPct" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="addPct">Additional / Regulatory Duty % (editable)</label><input type="number" step="any" class="form-control" id="addPct" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="stPct">Sales Tax % (editable)</label><input type="number" step="any" class="form-control" id="stPct" value="18"></div>
                    <div class="col-md-6"><label class="form-label" for="whtPct">Income Tax / WHT % (editable)</label><input type="number" step="any" class="form-control" id="whtPct" value="5.5"></div>
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
                        <li>Duties are charged on the import value, sales tax is charged on value plus duties, and income tax on the duty and tax paid value — the standard layered order.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Estimate only. HS code, valuation and exemptions change the real assessment. Rates change — verify with the official source before relying on this.</p>
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
        var v = n("value"); var duty = v * n("dutyPct") / 100; var add = v * n("addPct") / 100; var stBase = v + duty + add; var st = stBase * n("stPct") / 100; var wht = (stBase + st) * n("whtPct") / 100; var totalTax = duty + add + st + wht; setResult("Customs duty: " + fmt(duty) + "<br>Additional duty: " + fmt(add) + "<br>Sales tax (on value + duties): " + fmt(st) + "<br>Income tax: " + fmt(wht) + "<br>Total taxes: " + fmt(totalTax) + "<br><strong>Landed cost: " + fmt(v + totalTax) + "</strong>"); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
