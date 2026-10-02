@extends('layouts.app')

@section('title', 'Ecommerce Profit Calculator — Free Online Tool')
@section('meta_description', 'Calculate true profit per order after product cost, marketplace commission, delivery, packaging and return losses')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Ecommerce Profit Calculator</h1>
            <p class="lead small text-muted">Calculate true profit per order after product cost, marketplace commission, delivery, packaging and return losses</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="price">Selling Price per Order (Rs)</label><input type="number" step="any" class="form-control" id="price" value="2000"></div>
                    <div class="col-md-6"><label class="form-label" for="cost">Product Cost per Order (Rs)</label><input type="number" step="any" class="form-control" id="cost" value="1100"></div>
                    <div class="col-md-6"><label class="form-label" for="commPct">Marketplace Commission %</label><input type="number" step="any" class="form-control" id="commPct" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="delivery">Delivery Cost per Order (Rs)</label><input type="number" step="any" class="form-control" id="delivery" value="250"></div>
                    <div class="col-md-6"><label class="form-label" for="pack">Packaging Cost per Order (Rs)</label><input type="number" step="any" class="form-control" id="pack" value="50"></div>
                    <div class="col-md-6"><label class="form-label" for="retPct">Return Rate % of orders</label><input type="number" step="any" class="form-control" id="retPct" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="retCost">Loss per Returned Order (Rs)</label><input type="number" step="any" class="form-control" id="retCost" value="300"></div>
                    <div class="col-md-6"><label class="form-label" for="orders">Orders per Month</label><input type="number" step="any" class="form-control" id="orders" value="300"></div>
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
                        <li>Every cost head is deducted per order. Return loss is spread across all orders using your return rate.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Payment gateway and withholding charges vary by platform — add them into packaging or product cost if your platform charges them.</p>
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
        var comm = n("price") * n("commPct") / 100; var retLoss = n("retPct") / 100 * n("retCost"); var per = n("price") - n("cost") - comm - n("delivery") - n("pack") - retLoss; setResult("Commission per order: " + fmt(comm) + "<br>Expected return loss per order: " + fmt(retLoss) + "<br><strong>Profit per order: " + fmt(per) + "</strong><br>Monthly profit at " + num(n("orders")) + " orders: " + fmt(per * n("orders")) + "<br>Profit margin on price: " + pct(n("price") > 0 ? per / n("price") * 100 : 0)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
