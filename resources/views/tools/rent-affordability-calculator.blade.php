@extends('layouts.app')

@section('title', 'Rent Affordability Calculator — Free Online Tool')
@section('meta_description', 'Estimate affordable monthly rent from income using standard income share limits for singles and families')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Rent Affordability Calculator</h1>
            <p class="lead small text-muted">Estimate affordable monthly rent from income using standard income share limits for singles and families</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="income">Monthly Income (Rs)</label><input type="number" step="any" class="form-control" id="income" value="150000"></div>
                    <div class="col-md-6"><label class="form-label" for="share">Rent Share of Income % (standard 30)</label><input type="number" step="any" class="form-control" id="share" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other Fixed Monthly Costs (Rs)</label><input type="number" step="any" class="form-control" id="other" value="60000"></div>
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
                        <li>The standard guide keeps rent near 30 percent of monthly income. Edit the share percent for a single person or family case.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> A budgeting guide, not financial advice. Keep an emergency margin beyond rent.</p>
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
        var byShare = n("income") * n("share") / 100; var afterCosts = n("income") - n("other"); setResult("Affordable rent by income share rule: " + fmt(byShare) + "<br>Income left after other fixed costs: " + fmt(afterCosts) + "<br>Suggested maximum rent (lower of the two): " + fmt(Math.max(0, Math.min(byShare, afterCosts)))); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
