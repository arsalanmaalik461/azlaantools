@extends('layouts.app')

@section('title', 'Subscription Cost Calculator — Free Online Tool')
@section('meta_description', 'Add up all monthly subscriptions and see the true monthly and yearly cost of every recurring plan combined')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Subscription Cost Calculator</h1>
            <p class="lead small text-muted">Add up all monthly subscriptions and see the true monthly and yearly cost of every recurring plan combined</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="s1">Subscription 1 Monthly Cost (Rs)</label><input type="number" step="any" class="form-control" id="s1" value="800"></div>
                    <div class="col-md-6"><label class="form-label" for="s2">Subscription 2 Monthly Cost (Rs)</label><input type="number" step="any" class="form-control" id="s2" value="650"></div>
                    <div class="col-md-6"><label class="form-label" for="s3">Subscription 3 Monthly Cost (Rs)</label><input type="number" step="any" class="form-control" id="s3" value="1000"></div>
                    <div class="col-md-6"><label class="form-label" for="s4">Subscription 4 Monthly Cost (Rs)</label><input type="number" step="any" class="form-control" id="s4" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="s5">Subscription 5 Monthly Cost (Rs)</label><input type="number" step="any" class="form-control" id="s5" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="s6">Subscription 6 Monthly Cost (Rs)</label><input type="number" step="any" class="form-control" id="s6" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="once">One Time Yearly Plans Total (Rs per year)</label><input type="number" step="any" class="form-control" id="once" value="0"></div>
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
                        <li>Add every monthly plan in its own row — streaming, apps, internet, cloud and memberships — then see the true combined drain.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Prices are the ones you enter. Cancelling one unused plan saves its full yearly cost.</p>
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
        var monthly = n("s1") + n("s2") + n("s3") + n("s4") + n("s5") + n("s6"); var yearly = monthly * 12 + n("once"); setResult("Combined monthly cost: " + fmt(monthly) + "<br>Combined yearly cost: " + fmt(yearly) + "<br>Cost over 5 years at same rates: " + fmt(yearly * 5)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
