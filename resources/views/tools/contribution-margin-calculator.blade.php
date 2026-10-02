@extends('layouts.app')

@section('title', 'Contribution Margin Calculator — Free Online Tool')
@section('meta_description', 'Calculate per unit and total contribution margin after variable costs and the contribution margin ratio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Contribution Margin Calculator</h1>
            <p class="lead small text-muted">Calculate per unit and total contribution margin after variable costs and the contribution margin ratio</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="price">Selling Price per Unit (Rs)</label><input type="number" step="any" class="form-control" id="price" value="500"></div>
                    <div class="col-md-6"><label class="form-label" for="varCost">Variable Cost per Unit (Rs)</label><input type="number" step="any" class="form-control" id="varCost" value="300"></div>
                    <div class="col-md-6"><label class="form-label" for="units">Units Sold</label><input type="number" step="any" class="form-control" id="units" value="1000"></div>
                    <div class="col-md-6"><label class="form-label" for="fixed">Fixed Costs (Rs)</label><input type="number" step="any" class="form-control" id="fixed" value="100000"></div>
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
                        <li>Contribution is selling price minus variable cost per unit. Total contribution must cover fixed costs before profit starts.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Only costs that change with each unit belong in variable cost — rent and salaries are usually fixed.</p>
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
        var cm = n("price") - n("varCost"); var ratio = n("price") > 0 ? cm / n("price") * 100 : 0; var total = cm * n("units"); setResult("Contribution per unit: " + fmt(cm) + "<br>Contribution margin ratio: " + pct(ratio) + "<br>Total contribution: " + fmt(total) + "<br>Profit after fixed costs: " + fmt(total - n("fixed"))); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
