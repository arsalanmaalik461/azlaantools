@extends('layouts.app')

@section('title', 'Plot Installment Plan Calculator — Free Online Tool')
@section('meta_description', 'Enter booking, allocation and monthly installments to find the total price of a housing society plot.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Plot Installment Plan Calculator</h1>
            <p class="lead small text-muted">Enter booking, allocation and monthly installments to find the total price of a housing society plot.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="booking">Booking Amount (Rs)</label><input type="number" step="any" class="form-control" id="booking" value="500000"></div>
                    <div class="col-md-6"><label class="form-label" for="allocation">Allocation Amount (Rs)</label><input type="number" step="any" class="form-control" id="allocation" value="500000"></div>
                    <div class="col-md-6"><label class="form-label" for="confirmation">Confirmation Amount (Rs)</label><input type="number" step="any" class="form-control" id="confirmation" value="250000"></div>
                    <div class="col-md-6"><label class="form-label" for="monthly">Monthly Installment (Rs)</label><input type="number" step="any" class="form-control" id="monthly" value="50000"></div>
                    <div class="col-md-6"><label class="form-label" for="months">Number of Monthly Installments</label><input type="number" step="any" class="form-control" id="months" value="48"></div>
                    <div class="col-md-6"><label class="form-label" for="extra">Extra Charges: development, corner, park facing etc (Rs)</label><input type="number" step="any" class="form-control" id="extra" value="200000"></div>
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
                        <li>Add booking, allocation and confirmation, then monthly installment multiplied by the number of months, plus any extra charges.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Society payment plans differ by project and file type. Enter the figures from your booking form. Rates change — verify with the official source before relying on this.</p>
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
        var total = n("booking") + n("allocation") + n("confirmation") + n("monthly") * n("months") + n("extra"); setResult("Total plot price over the plan: " + fmt(total) + "<br>Total installments portion: " + fmt(n("monthly") * n("months"))); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
