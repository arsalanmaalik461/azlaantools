@extends('layouts.app')

@section('title', 'Monthly Ration Budget Calculator — Free Online Tool')
@section('meta_description', 'Enter quantity and rate of flour, rice, pulses and daily grocery items, and plan your monthly ration budget.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Monthly Ration Budget Calculator</h1>
            <p class="lead small text-muted">Enter quantity and rate of flour, rice, pulses and daily grocery items, and plan your monthly ration budget.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="q1">Atta / Flour Qty (kg per month)</label><input type="number" step="any" class="form-control" id="q1" value="20"></div>
                    <div class="col-md-6"><label class="form-label" for="r1">Atta Rate (Rs per kg)</label><input type="number" step="any" class="form-control" id="r1" value="150"></div>
                    <div class="col-md-6"><label class="form-label" for="q2">Rice Qty (kg per month)</label><input type="number" step="any" class="form-control" id="q2" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="r2">Rice Rate (Rs per kg)</label><input type="number" step="any" class="form-control" id="r2" value="320"></div>
                    <div class="col-md-6"><label class="form-label" for="q3">Daal / Pulses Qty (kg per month)</label><input type="number" step="any" class="form-control" id="q3" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="r3">Daal Rate (Rs per kg)</label><input type="number" step="any" class="form-control" id="r3" value="400"></div>
                    <div class="col-md-6"><label class="form-label" for="q4">Cooking Oil / Ghee Qty (kg or litre)</label><input type="number" step="any" class="form-control" id="q4" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="r4">Oil / Ghee Rate (Rs)</label><input type="number" step="any" class="form-control" id="r4" value="650"></div>
                    <div class="col-md-6"><label class="form-label" for="q5">Sugar Qty (kg per month)</label><input type="number" step="any" class="form-control" id="q5" value="4"></div>
                    <div class="col-md-6"><label class="form-label" for="r5">Sugar Rate (Rs per kg)</label><input type="number" step="any" class="form-control" id="r5" value="180"></div>
                    <div class="col-md-6"><label class="form-label" for="q6">Milk Qty (litre per month)</label><input type="number" step="any" class="form-control" id="q6" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="r6">Milk Rate (Rs per litre)</label><input type="number" step="any" class="form-control" id="r6" value="220"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other items: vegetables, meat, tea etc (Rs per month)</label><input type="number" step="any" class="form-control" id="other" value="15000"></div>
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
                        <li>Every row is quantity multiplied by the shop rate you enter. Add anything else in the other items field.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> No live grocery prices are used — all rates are the ones you type in.</p>
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
        var items = [["Atta", n("q1") * n("r1")], ["Rice", n("q2") * n("r2")], ["Daal", n("q3") * n("r3")], ["Oil / Ghee", n("q4") * n("r4")], ["Sugar", n("q5") * n("r5")], ["Milk", n("q6") * n("r6")]]; var total = n("other"); var lines = ""; for (var i = 0; i < items.length; i++) { total += items[i][1]; lines += items[i][0] + ": " + fmt(items[i][1]) + "<br>"; } setResult(lines + "Other items: " + fmt(n("other")) + "<br><strong>Monthly ration budget: " + fmt(total) + "</strong><br>Yearly budget: " + fmt(total * 12)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
