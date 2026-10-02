@extends('layouts.app')

@section('title', 'Business Cash Flow Calculator — Free Online Tool')
@section('meta_description', 'Calculate net monthly cash flow and closing balance from all business inflows and outflows by category')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Business Cash Flow Calculator</h1>
            <p class="lead small text-muted">Calculate net monthly cash flow and closing balance from all business inflows and outflows by category</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="opening">Opening Balance (Rs)</label><input type="number" step="any" class="form-control" id="opening" value="50000"></div>
                    <div class="col-md-6"><label class="form-label" for="sales">Cash Sales / Receipts (Rs)</label><input type="number" step="any" class="form-control" id="sales" value="400000"></div>
                    <div class="col-md-6"><label class="form-label" for="otherIn">Other Inflows: loans, investment (Rs)</label><input type="number" step="any" class="form-control" id="otherIn" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="stock">Stock / Inventory Purchases (Rs)</label><input type="number" step="any" class="form-control" id="stock" value="200000"></div>
                    <div class="col-md-6"><label class="form-label" for="rent">Rent (Rs)</label><input type="number" step="any" class="form-control" id="rent" value="50000"></div>
                    <div class="col-md-6"><label class="form-label" for="salaries">Salaries (Rs)</label><input type="number" step="any" class="form-control" id="salaries" value="80000"></div>
                    <div class="col-md-6"><label class="form-label" for="bills">Bills and Utilities (Rs)</label><input type="number" step="any" class="form-control" id="bills" value="20000"></div>
                    <div class="col-md-6"><label class="form-label" for="otherOut">Other Outflows (Rs)</label><input type="number" step="any" class="form-control" id="otherOut" value="30000"></div>
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
                        <li>Add all money in and all money out for the month. Closing balance is opening balance plus net cash flow.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Cash flow is not profit — credit sales and unpaid bills are excluded until cash actually moves.</p>
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
        var inflow = n("sales") + n("otherIn"); var outflow = n("stock") + n("rent") + n("salaries") + n("bills") + n("otherOut"); var net = inflow - outflow; setResult("Total inflows: " + fmt(inflow) + "<br>Total outflows: " + fmt(outflow) + "<br>Net cash flow: " + fmt(net) + "<br><strong>Closing balance: " + fmt(n("opening") + net) + "</strong>"); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
