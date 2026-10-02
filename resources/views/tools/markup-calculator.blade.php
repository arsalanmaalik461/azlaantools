@extends('layouts.app')

@section('title', 'Markup Calculator — Free Online Tool')
@section('meta_description', 'Calculate selling price, profit and markup percent from cost, and convert any markup into the matching margin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Markup Calculator</h1>
            <p class="lead small text-muted">Calculate selling price, profit and markup percent from cost, and convert any markup into the matching margin</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="cost">Cost Price (Rs)</label><input type="number" step="any" class="form-control" id="cost" value="1000"></div>
                    <div class="col-md-6"><label class="form-label" for="markupPct">Markup % on Cost</label><input type="number" step="any" class="form-control" id="markupPct" value="25"></div>
                    <div class="col-md-6"><label class="form-label" for="sellKnown">Or Known Selling Price (Rs, optional)</label><input type="number" step="any" class="form-control" id="sellKnown" value="0"></div>
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
                        <li>Markup is profit as a percent of cost. The same profit is a smaller percent of the selling price, which is the margin.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> A 25 percent markup is only a 20 percent margin. Quote clearly which one a deal uses.</p>
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
        var sell = n("cost") * (1 + n("markupPct") / 100); var profit = sell - n("cost"); var margin = sell > 0 ? profit / sell * 100 : 0; var line = "Selling price at " + num(n("markupPct")) + "% markup: " + fmt(sell) + "<br>Profit: " + fmt(profit) + "<br>Equal margin percent: " + pct(margin); if (n("sellKnown") > 0 && n("cost") > 0) { var mk = (n("sellKnown") - n("cost")) / n("cost") * 100; var mg = (n("sellKnown") - n("cost")) / n("sellKnown") * 100; line += "<br>At selling price " + fmt(n("sellKnown")) + ": markup is " + pct(mk) + " and margin is " + pct(mg) + "."; } setResult(line); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
