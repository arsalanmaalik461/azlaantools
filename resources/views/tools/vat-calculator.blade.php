@extends('layouts.app')

@section('title', 'VAT Calculator — Free Online Tool')
@section('meta_description', 'Add VAT to a net price or extract VAT from a gross price at any rate for Gulf, UK and other VAT countries')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">VAT Calculator</h1>
            <p class="lead small text-muted">Add VAT to a net price or extract VAT from a gross price at any rate for Gulf, UK and other VAT countries</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="mode">Direction</label><select class="form-select" id="mode"><option value="add" selected>Add VAT to a net amount</option><option value="extract">Extract VAT from a gross amount</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="amount">Amount</label><input type="number" step="any" class="form-control" id="amount" value="1000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">VAT Rate % (editable: UAE 5, Saudi 15, UK 20)</label><input type="number" step="any" class="form-control" id="rate" value="5"></div>
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
                        <li>Add mode puts VAT on top of a net amount. Extract mode backs VAT out of an amount that already includes it. Edit the rate for your country.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> VAT rates and rules differ by country and by goods category. Rates change — verify with the official source before relying on this.</p>
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
        var tax, net, gross; if (t("mode") === "add") { net = n("amount"); tax = net * n("rate") / 100; gross = net + tax; } else { gross = n("amount"); net = gross / (1 + n("rate") / 100); tax = gross - net; } setResult("Net amount (without VAT): " + num(net) + "<br>VAT amount: " + num(tax) + "<br>Gross amount (with VAT): " + num(gross)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
