@extends('layouts.app')

@section('title', 'Economic Order Quantity Calculator — Free Online Tool')
@section('meta_description', 'Calculate the ideal reorder quantity that balances ordering cost and holding cost for any stock item')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Economic Order Quantity Calculator</h1>
            <p class="lead small text-muted">Calculate the ideal reorder quantity that balances ordering cost and holding cost for any stock item</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="demand">Yearly Demand (units)</label><input type="number" step="any" class="form-control" id="demand" value="12000"></div>
                    <div class="col-md-6"><label class="form-label" for="orderCost">Ordering Cost per Order (Rs)</label><input type="number" step="any" class="form-control" id="orderCost" value="2000"></div>
                    <div class="col-md-6"><label class="form-label" for="holdCost">Holding Cost per Unit per Year (Rs)</label><input type="number" step="any" class="form-control" id="holdCost" value="50"></div>
                    <div class="col-md-6"><label class="form-label" for="unitCost">Unit Cost (Rs, for order value)</label><input type="number" step="any" class="form-control" id="unitCost" value="500"></div>
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
                        <li>EOQ is the square root of (2 times yearly demand times ordering cost, divided by holding cost per unit).</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> EOQ assumes steady demand and constant costs — seasonal items need a safety stock on top.</p>
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
        if (n("demand") <= 0 || n("holdCost") <= 0) { showMsg("Enter demand and holding cost above zero."); return; } showMsg(""); var eoq = Math.sqrt(2 * n("demand") * n("orderCost") / n("holdCost")); var orders = n("demand") / eoq; var cycle = 365 / orders; setResult("Economic order quantity: " + num(Math.round(eoq)) + " units<br>Orders per year: " + num(orders) + "<br>Days between orders: " + num(Math.round(cycle)) + "<br>Value of one order: " + fmt(eoq * n("unitCost")));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
