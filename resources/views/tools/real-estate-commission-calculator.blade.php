@extends('layouts.app')

@section('title', 'Real Estate Commission Calculator — Free Online Tool')
@section('meta_description', 'Calculate property dealer commission on a deal and split it between buyer side, seller side and agents')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Real Estate Commission Calculator</h1>
            <p class="lead small text-muted">Calculate property dealer commission on a deal and split it between buyer side, seller side and agents</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="price">Deal Price (Rs)</label><input type="number" step="any" class="form-control" id="price" value="15000000"></div>
                    <div class="col-md-6"><label class="form-label" for="commPct">Total Commission % (both sides combined)</label><input type="number" step="any" class="form-control" id="commPct" value="2"></div>
                    <div class="col-md-6"><label class="form-label" for="agents">Number of Agents Sharing Equally</label><input type="number" step="any" class="form-control" id="agents" value="2"></div>
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
                        <li>Commission percent is applied on the deal price, then split half to buyer side and half to seller side by default.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Commission rates and splits are negotiated per deal and market — confirm in writing before the token money stage.</p>
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
        var total = n("price") * n("commPct") / 100; var perSide = total / 2; setResult("Total commission: " + fmt(total) + "<br>Buyer side share: " + fmt(perSide) + "<br>Seller side share: " + fmt(perSide) + (n("agents") > 0 ? "<br>Per agent (equal split): " + fmt(total / n("agents")) : "")); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
