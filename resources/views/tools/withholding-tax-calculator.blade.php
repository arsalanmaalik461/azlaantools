@extends('layouts.app')

@section('title', 'Withholding Tax Calculator — Free Online Tool')
@section('meta_description', 'Choose a transaction type and calculate the filer and non-filer withholding tax on the amount.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Withholding Tax Calculator</h1>
            <p class="lead small text-muted">Choose a transaction type and calculate the filer and non-filer withholding tax on the amount.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="type">Transaction Type</label><select class="form-select" id="type"><option value="goods" selected>Supply of goods</option><option value="services">Services</option><option value="dividend">Dividend</option><option value="bankprofit">Bank profit / interest</option><option value="cash">Bank cash withdrawal</option><option value="custom">Custom rate</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="amount">Transaction Amount (Rs)</label><input type="number" step="any" class="form-control" id="amount" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="filerRate">Filer Rate % (editable)</label><input type="number" step="any" class="form-control" id="filerRate" value="4"></div>
                    <div class="col-md-6"><label class="form-label" for="nonRate">Non-Filer Rate % (editable)</label><input type="number" step="any" class="form-control" id="nonRate" value="8"></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Indicative defaults only — always overwrite the two rate fields with the current FBR rate for your exact section.</p>
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
                        <li>Pick a transaction type, then edit the filer and non-filer rates to the current published rates for that section before calculating.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Withholding sections and rates change with every Finance Act and by taxpayer type. Rates change — verify with the official source before relying on this.</p>
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
        var f = n("amount") * n("filerRate") / 100; var nf = n("amount") * n("nonRate") / 100; setResult("Withholding tax as filer: " + fmt(f) + "<br>Withholding tax as non-filer: " + fmt(nf) + "<br>Extra cost of being a non-filer: " + fmt(nf - f)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
