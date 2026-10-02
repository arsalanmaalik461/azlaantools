@extends('layouts.app')

@section('title', 'Crop Profit Calculator — Free Online Tool')
@section('meta_description', 'Enter per acre cost, yield and rate to find your crop profit or loss.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Crop Profit Calculator</h1>
            <p class="lead small text-muted">Enter per acre cost, yield and rate to find your crop profit or loss.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="acres">Area (acres)</label><input type="number" step="any" class="form-control" id="acres" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="yieldAcre">Yield per Acre (maund / unit)</label><input type="number" step="any" class="form-control" id="yieldAcre" value="40"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Rate per Maund / Unit (Rs)</label><input type="number" step="any" class="form-control" id="rate" value="4000"></div>
                    <div class="col-md-6"><label class="form-label" for="seed">Seed Cost per Acre (Rs)</label><input type="number" step="any" class="form-control" id="seed" value="8000"></div>
                    <div class="col-md-6"><label class="form-label" for="fert">Fertilizer Cost per Acre (Rs)</label><input type="number" step="any" class="form-control" id="fert" value="25000"></div>
                    <div class="col-md-6"><label class="form-label" for="spray">Spray / Pesticide per Acre (Rs)</label><input type="number" step="any" class="form-control" id="spray" value="8000"></div>
                    <div class="col-md-6"><label class="form-label" for="labour">Labour and Harvest per Acre (Rs)</label><input type="number" step="any" class="form-control" id="labour" value="15000"></div>
                    <div class="col-md-6"><label class="form-label" for="water">Water, Fuel and Other per Acre (Rs)</label><input type="number" step="any" class="form-control" id="water" value="10000"></div>
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
                        <li>Income per acre is yield multiplied by your rate. All per acre cost heads are added, then profit scales by total acres.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> All rates and yields are your own figures — no live mandi or support prices are used.</p>
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
        var incomeAcre = n("yieldAcre") * n("rate"); var costAcre = n("seed") + n("fert") + n("spray") + n("labour") + n("water"); var profitAcre = incomeAcre - costAcre; setResult("Income per acre: " + fmt(incomeAcre) + "<br>Cost per acre: " + fmt(costAcre) + "<br>Profit per acre: " + fmt(profitAcre) + "<br><strong>Total profit on " + num(n("acres")) + " acres: " + fmt(profitAcre * n("acres")) + "</strong>"); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
