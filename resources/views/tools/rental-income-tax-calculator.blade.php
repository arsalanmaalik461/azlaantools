@extends('layouts.app')

@section('title', 'Rental Income Tax Calculator — Free Online Tool')
@section('meta_description', 'Enter your yearly rent and calculate property income tax using the FBR rental slabs.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Rental Income Tax Calculator</h1>
            <p class="lead small text-muted">Enter your yearly rent and calculate property income tax using the FBR rental slabs.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="rent">Gross Yearly Rent Received (Rs)</label><input type="number" step="any" class="form-control" id="rent" value="1200000"></div>
                    <div class="col-md-6"><label class="form-label" for="r2">Slab 2 Rate % on Rs 300,001 to 600,000 (editable)</label><input type="number" step="any" class="form-control" id="r2" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="r3">Slab 3 Rate % on Rs 600,001 to 2,000,000 (editable)</label><input type="number" step="any" class="form-control" id="r3" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="r4">Slab 4 Rate % on Rs 2,000,001 to 4,000,000 (editable)</label><input type="number" step="any" class="form-control" id="r4" value="25"></div>
                    <div class="col-md-6"><label class="form-label" for="r5">Slab 5 Rate % above Rs 4,000,000 (editable)</label><input type="number" step="any" class="form-control" id="r5" value="35"></div>
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
                        <li>Tax is charged slab by slab: each slice of rent is taxed at that slab rate only. All slab rates are editable fields above.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Uses the commonly published individual rental income slabs. Company and AOP rates differ. Rates change — verify with the official source before relying on this.</p>
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
        var inc = n("rent"); var tax = 0; tax += Math.max(0, Math.min(inc, 600000) - 300000) * n("r2") / 100; tax += Math.max(0, Math.min(inc, 2000000) - 600000) * n("r3") / 100; tax += Math.max(0, Math.min(inc, 4000000) - 2000000) * n("r4") / 100; tax += Math.max(0, inc - 4000000) * n("r5") / 100; setResult("Yearly rental income: " + fmt(inc) + "<br>Income tax on rent: " + fmt(tax) + "<br>Monthly average tax: " + fmt(tax / 12) + "<br>Effective tax rate: " + pct(inc > 0 ? tax / inc * 100 : 0) + "<br>First Rs 300,000 is taken as tax free in these slabs."); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
