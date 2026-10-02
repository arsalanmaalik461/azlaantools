@extends('layouts.app')

@section('title', 'Depreciation Calculator — Free Online Tool')
@section('meta_description', 'Calculate yearly depreciation and book value using straight line or reducing balance methods')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Depreciation Calculator</h1>
            <p class="lead small text-muted">Calculate yearly depreciation and book value using straight line or reducing balance methods</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="method">Method</label><select class="form-select" id="method"><option value="slm" selected>Straight line</option><option value="rbm">Reducing balance</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="cost">Asset Cost (Rs)</label><input type="number" step="any" class="form-control" id="cost" value="1000000"></div>
                    <div class="col-md-6"><label class="form-label" for="salvage">Salvage / Residual Value (Rs)</label><input type="number" step="any" class="form-control" id="salvage" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="life">Useful Life (years)</label><input type="number" step="any" class="form-control" id="life" value="5"></div>
                    <div class="col-md-6"><label class="form-label" for="rbRate">Reducing Balance Rate % per year</label><input type="number" step="any" class="form-control" id="rbRate" value="20"></div>
                    </div>
                    <div class="table-responsive mt-3"><table class="table table-sm table-bordered"><thead><tr><th>Year</th><th>Depreciation</th><th>Book Value</th></tr></thead><tbody id="depBody"></tbody></table></div>
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
                        <li>Straight line spreads cost minus salvage evenly over life. Reducing balance charges a fixed percent of the falling book value each year.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Tax depreciation rates under the Income Tax Ordinance differ from accounting rates. Rates change — verify with the official source before relying on this.</p>
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
        var rows = ""; var yrs = Math.round(n("life")); if (yrs <= 0 || n("cost") <= 0) { showMsg("Enter asset cost and life above zero."); return; } showMsg(""); var book = n("cost"); var firstDep = 0; if (t("method") === "slm") { var dep = (n("cost") - n("salvage")) / yrs; firstDep = dep; for (var i = 1; i <= yrs; i++) { book -= dep; rows += "<tr><td>" + i + "</td><td>" + fmt(dep) + "</td><td>" + fmt(Math.max(book, n("salvage"))) + "</td></tr>"; } } else { for (var j = 1; j <= yrs; j++) { var d2 = book * n("rbRate") / 100; if (j === 1) { firstDep = d2; } book -= d2; rows += "<tr><td>" + j + "</td><td>" + fmt(d2) + "</td><td>" + fmt(book) + "</td></tr>"; } } setResult("Yearly depreciation (first year): " + fmt(firstDep) + "<br>Book value at end of life: " + fmt(book)); document.getElementById("depBody").innerHTML = rows;
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
