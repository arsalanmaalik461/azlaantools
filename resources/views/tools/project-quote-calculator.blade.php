@extends('layouts.app')

@section('title', 'Project Quote Calculator — Free Online Tool')
@section('meta_description', 'Build a project price quote from hours, rate, material cost, contingency percent and profit margin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Project Quote Calculator</h1>
            <p class="lead small text-muted">Build a project price quote from hours, rate, material cost, contingency percent and profit margin</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="hours">Labour Hours</label><input type="number" step="any" class="form-control" id="hours" value="40"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Labour Rate per Hour (Rs)</label><input type="number" step="any" class="form-control" id="rate" value="1500"></div>
                    <div class="col-md-6"><label class="form-label" for="material">Material Cost (Rs)</label><input type="number" step="any" class="form-control" id="material" value="80000"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other Costs: transport etc (Rs)</label><input type="number" step="any" class="form-control" id="other" value="10000"></div>
                    <div class="col-md-6"><label class="form-label" for="contPct">Contingency %</label><input type="number" step="any" class="form-control" id="contPct" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="marginPct">Profit Margin % on Total Cost</label><input type="number" step="any" class="form-control" id="marginPct" value="20"></div>
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
                        <li>Labour, material and other costs are added, contingency covers surprises, and margin is applied on the total cost.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Quote validity and payment terms should be written on the quotation you send — this tool only works out the price.</p>
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
        var labour = n("hours") * n("rate"); var sub = labour + n("material") + n("other"); var cont = sub * n("contPct") / 100; var costTotal = sub + cont; var price = costTotal * (1 + n("marginPct") / 100); setResult("Labour cost: " + fmt(labour) + "<br>Subtotal: " + fmt(sub) + "<br>Contingency: " + fmt(cont) + "<br>Total cost: " + fmt(costTotal) + "<br><strong>Quote price to client: " + fmt(price) + "</strong><br>Your profit in the quote: " + fmt(price - costTotal)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
