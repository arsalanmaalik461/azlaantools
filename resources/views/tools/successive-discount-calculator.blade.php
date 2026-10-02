@extends('layouts.app')

@section('title', 'Successive Discount Calculator — Free Online Tool')
@section('meta_description', 'Calculate final price and total effective discount when two or three discounts apply one after another')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Successive Discount Calculator</h1>
            <p class="lead small text-muted">Calculate final price and total effective discount when two or three discounts apply one after another</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="price">Original Price (Rs)</label><input type="number" step="any" class="form-control" id="price" value="10000"></div>
                    <div class="col-md-6"><label class="form-label" for="d1">First Discount %</label><input type="number" step="any" class="form-control" id="d1" value="20"></div>
                    <div class="col-md-6"><label class="form-label" for="d2">Second Discount %</label><input type="number" step="any" class="form-control" id="d2" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="d3">Third Discount % (0 if none)</label><input type="number" step="any" class="form-control" id="d3" value="0"></div>
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
                        <li>Each discount applies on the already reduced price, one after another — never on the original price again.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> A 20 percent plus 10 percent offer is really 28 percent off, not 30 percent. That gap is exactly what this tool shows.</p>
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
        var after1 = n("price") * (1 - n("d1") / 100); var after2 = after1 * (1 - n("d2") / 100); var final = after2 * (1 - n("d3") / 100); var eff = n("price") > 0 ? (n("price") - final) / n("price") * 100 : 0; setResult("After first discount: " + fmt(after1) + "<br>After second discount: " + fmt(after2) + "<br><strong>Final price: " + fmt(final) + "</strong><br>Total saving: " + fmt(n("price") - final) + "<br>Effective total discount: " + pct(eff) + " (not " + num(n("d1") + n("d2") + n("d3")) + "%)"); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
