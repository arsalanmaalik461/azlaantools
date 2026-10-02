@extends('layouts.app')

@section('title', 'Commission Calculator — Free Online Tool')
@section('meta_description', 'Calculate sales commission from sale value and flat, tiered or sliding commission rates with total pay')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Commission Calculator</h1>
            <p class="lead small text-muted">Calculate sales commission from sale value and flat, tiered or sliding commission rates with total pay</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="mode">Commission Structure</label><select class="form-select" id="mode"><option value="flat" selected>Flat percent</option><option value="tiered">Tiered: low rate up to slab, high rate above</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="sale">Sale Value (Rs)</label><input type="number" step="any" class="form-control" id="sale" value="500000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Flat Rate / Tier 1 Rate %</label><input type="number" step="any" class="form-control" id="rate" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="slab">Tier 1 Limit (Rs)</label><input type="number" step="any" class="form-control" id="slab" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate2">Tier 2 Rate % (above limit)</label><input type="number" step="any" class="form-control" id="rate2" value="8"></div>
                    <div class="col-md-6"><label class="form-label" for="base">Base Salary (Rs, optional)</label><input type="number" step="any" class="form-control" id="base" value="0"></div>
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
                        <li>Flat mode applies one percent to the full sale. Tiered mode applies the first rate up to the slab limit and the second rate on the rest.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Confirm whether commission is paid on sale value, collected amount or profit — this tool uses the sale value you enter.</p>
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
        var comm; if (t("mode") === "tiered") { comm = Math.min(n("sale"), n("slab")) * n("rate") / 100 + Math.max(0, n("sale") - n("slab")) * n("rate2") / 100; } else { comm = n("sale") * n("rate") / 100; } setResult("Commission earned: " + fmt(comm) + "<br>Base salary: " + fmt(n("base")) + "<br><strong>Total pay: " + fmt(comm + n("base")) + "</strong>"); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
