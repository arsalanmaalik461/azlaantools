@extends('layouts.app')

@section('title', 'Stock Average Calculator — Free Online Tool')
@section('meta_description', 'Calculate average buy price and total cost after buying the same share in several lots at different prices')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Stock Average Calculator</h1>
            <p class="lead small text-muted">Calculate average buy price and total cost after buying the same share in several lots at different prices</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="q1">Lot 1 Quantity</label><input type="number" step="any" class="form-control" id="q1" value="100"></div>
                    <div class="col-md-6"><label class="form-label" for="p1">Lot 1 Price per Share (Rs)</label><input type="number" step="any" class="form-control" id="p1" value="150"></div>
                    <div class="col-md-6"><label class="form-label" for="q2">Lot 2 Quantity</label><input type="number" step="any" class="form-control" id="q2" value="100"></div>
                    <div class="col-md-6"><label class="form-label" for="p2">Lot 2 Price per Share (Rs)</label><input type="number" step="any" class="form-control" id="p2" value="130"></div>
                    <div class="col-md-6"><label class="form-label" for="q3">Lot 3 Quantity</label><input type="number" step="any" class="form-control" id="q3" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="p3">Lot 3 Price per Share (Rs)</label><input type="number" step="any" class="form-control" id="p3" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="q4">Lot 4 Quantity</label><input type="number" step="any" class="form-control" id="q4" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="p4">Lot 4 Price per Share (Rs)</label><input type="number" step="any" class="form-control" id="p4" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="q5">Lot 5 Quantity</label><input type="number" step="any" class="form-control" id="q5" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="p5">Lot 5 Price per Share (Rs)</label><input type="number" step="any" class="form-control" id="p5" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="cur">Current Market Price (Rs, optional)</label><input type="number" step="any" class="form-control" id="cur" value="145"></div>
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
                        <li>Average price is total cost of all lots divided by total shares. Set unused lots to zero.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Brokerage and CDC charges are not included unless you add them into the lot prices. Only prices you enter are used.</p>
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
        var qs = [n("q1"), n("q2"), n("q3"), n("q4"), n("q5")]; var ps = [n("p1"), n("p2"), n("p3"), n("p4"), n("p5")]; var tq = 0, tc = 0; for (var i = 0; i < 5; i++) { tq += qs[i]; tc += qs[i] * ps[i]; } if (tq <= 0) { showMsg("Enter at least one lot with quantity above zero."); return; } showMsg(""); var avg = tc / tq; var line = "Total shares: " + num(tq) + "<br>Total cost: " + fmt(tc) + "<br>Average buy price: " + fmt2(avg); if (n("cur") > 0) { line += "<br>Profit / loss at current price: " + fmt((n("cur") - avg) * tq) + " (" + pct((n("cur") - avg) / avg * 100) + ")"; } setResult(line);
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
