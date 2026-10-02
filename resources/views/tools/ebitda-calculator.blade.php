@extends('layouts.app')

@section('title', 'EBITDA Calculator — Free Online Tool')
@section('meta_description', 'Calculate EBITDA and EBITDA margin from revenue, operating expenses, depreciation and amortisation figures')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">EBITDA Calculator</h1>
            <p class="lead small text-muted">Calculate EBITDA and EBITDA margin from revenue, operating expenses, depreciation and amortisation figures</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="revenue">Revenue / Sales (Rs)</label><input type="number" step="any" class="form-control" id="revenue" value="5000000"></div>
                    <div class="col-md-6"><label class="form-label" for="cogs">Cost of Goods Sold (Rs)</label><input type="number" step="any" class="form-control" id="cogs" value="3000000"></div>
                    <div class="col-md-6"><label class="form-label" for="opex">Operating Expenses excl depreciation (Rs)</label><input type="number" step="any" class="form-control" id="opex" value="1000000"></div>
                    <div class="col-md-6"><label class="form-label" for="dep">Depreciation (Rs)</label><input type="number" step="any" class="form-control" id="dep" value="200000"></div>
                    <div class="col-md-6"><label class="form-label" for="amort">Amortisation (Rs)</label><input type="number" step="any" class="form-control" id="amort" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="interest">Interest (Rs)</label><input type="number" step="any" class="form-control" id="interest" value="150000"></div>
                    <div class="col-md-6"><label class="form-label" for="tax">Tax (Rs)</label><input type="number" step="any" class="form-control" id="tax" value="100000"></div>
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
                        <li>EBITDA is revenue minus cost of goods and operating expenses, before depreciation, amortisation, interest and tax are deducted.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Enter operating expenses without depreciation and amortisation — those are added back in the EBITDA definition.</p>
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
        var gross = n("revenue") - n("cogs"); var ebitda = gross - n("opex"); var ebit = ebitda - n("dep") - n("amort"); var net = ebit - n("interest") - n("tax"); var margin = n("revenue") > 0 ? ebitda / n("revenue") * 100 : 0; setResult("Gross profit: " + fmt(gross) + "<br><strong>EBITDA: " + fmt(ebitda) + "</strong><br>EBITDA margin: " + pct(margin) + "<br>EBIT: " + fmt(ebit) + "<br>Net profit: " + fmt(net)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
