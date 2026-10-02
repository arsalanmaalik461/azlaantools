@extends('layouts.app')

@section('title', 'Dairy Farm Profit Calculator — Free Online Tool')
@section('meta_description', 'Enter daily milk, rate and feed cost, and find your dairy farm monthly profit.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Dairy Farm Profit Calculator</h1>
            <p class="lead small text-muted">Enter daily milk, rate and feed cost, and find your dairy farm monthly profit.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="animals">Number of Milking Animals</label><input type="number" step="any" class="form-control" id="animals" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="milkDay">Milk per Animal per Day (litre)</label><input type="number" step="any" class="form-control" id="milkDay" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Milk Sale Rate (Rs per litre)</label><input type="number" step="any" class="form-control" id="rate" value="200"></div>
                    <div class="col-md-6"><label class="form-label" for="feed">Feed Cost per Day Total (Rs)</label><input type="number" step="any" class="form-control" id="feed" value="5000"></div>
                    <div class="col-md-6"><label class="form-label" for="labour">Labour Cost per Month (Rs)</label><input type="number" step="any" class="form-control" id="labour" value="30000"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other Monthly Costs: medicine, electricity (Rs)</label><input type="number" step="any" class="form-control" id="other" value="15000"></div>
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
                        <li>Monthly milk is animals times daily litres times 30 days. Daily feed cost is also scaled to 30 days.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Calf sales, dung value and animal purchase cost are not included — add them in other costs or income as needed.</p>
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
        var milkMonth = n("animals") * n("milkDay") * 30; var income = milkMonth * n("rate"); var costs = n("feed") * 30 + n("labour") + n("other"); setResult("Milk per month: " + num(milkMonth) + " litre<br>Milk income: " + fmt(income) + "<br>Total monthly costs: " + fmt(costs) + "<br><strong>Monthly profit: " + fmt(income - costs) + "</strong><br>Profit per animal per month: " + fmt(n("animals") > 0 ? (income - costs) / n("animals") : 0)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
