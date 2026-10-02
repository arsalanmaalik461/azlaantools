@extends('layouts.app')

@section('title', 'Wedding Cost Planner Pakistan — Free Online Tool')
@section('meta_description', 'Enter guests and costs for each wedding function — mehndi, barat and walima — to build your total wedding budget.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Wedding Cost Planner Pakistan</h1>
            <p class="lead small text-muted">Enter guests and costs for each wedding function — mehndi, barat and walima — to build your total wedding budget.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="guests">Total Guests (all functions)</label><input type="number" step="any" class="form-control" id="guests" value="600"></div>
                    <div class="col-md-6"><label class="form-label" for="food">Food Cost per Guest (Rs)</label><input type="number" step="any" class="form-control" id="food" value="2500"></div>
                    <div class="col-md-6"><label class="form-label" for="venue">Venue / Hall Charges (Rs)</label><input type="number" step="any" class="form-control" id="venue" value="400000"></div>
                    <div class="col-md-6"><label class="form-label" for="decor">Decor and Stage (Rs)</label><input type="number" step="any" class="form-control" id="decor" value="150000"></div>
                    <div class="col-md-6"><label class="form-label" for="photo">Photo and Video (Rs)</label><input type="number" step="any" class="form-control" id="photo" value="120000"></div>
                    <div class="col-md-6"><label class="form-label" for="dress">Dresses, Salon and Jewellery Setting (Rs)</label><input type="number" step="any" class="form-control" id="dress" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other Costs: cards, transport, misc (Rs)</label><input type="number" step="any" class="form-control" id="other" value="100000"></div>
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
                        <li>Food is charged per guest while hall, decor and other heads are fixed. Change guest count to see how fast the budget moves.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> All rates are your own estimates — get written quotations from vendors before booking.</p>
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
        var foodTotal = n("guests") * n("food"); var total = foodTotal + n("venue") + n("decor") + n("photo") + n("dress") + n("other"); setResult("Total food cost: " + fmt(foodTotal) + "<br>Fixed costs total: " + fmt(total - foodTotal) + "<br><strong>Total wedding budget: " + fmt(total) + "</strong>" + (n("guests") > 0 ? "<br>Cost per guest (all in): " + fmt(total / n("guests")) : "")); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
