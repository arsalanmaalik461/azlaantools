@extends('layouts.app')

@section('title', 'Present Value Calculator — Free Online Tool')
@section('meta_description', 'Discount a future amount or a series of future payments back to the value of that money today')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Present Value Calculator</h1>
            <p class="lead small text-muted">Discount a future amount or a series of future payments back to the value of that money today</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="mode">Calculation Type</label><select class="form-select" id="mode"><option value="single" selected>Single future amount</option><option value="series">Series of equal payments</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="fv">Future Amount / Payment per period (Rs)</label><input type="number" step="any" class="form-control" id="fv" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Discount Rate % per period</label><input type="number" step="any" class="form-control" id="rate" value="10"></div>
                    <div class="col-md-6"><label class="form-label" for="periods">Number of Periods</label><input type="number" step="any" class="form-control" id="periods" value="5"></div>
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
                        <li>For a single amount, present value is the future amount divided by (1 + rate) raised to the periods. For a series, the annuity formula is used.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Uses standard time value of money formulas. The discount rate is your own assumption.</p>
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
        var r = n("rate") / 100, k = n("periods"); if (k <= 0) { showMsg("Number of periods must be above zero."); return; } showMsg(""); var pv; if (t("mode") === "series") { pv = r > 0 ? n("fv") * (1 - Math.pow(1 + r, -k)) / r : n("fv") * k; } else { pv = n("fv") / Math.pow(1 + r, k); } setResult("Present value today: " + fmt(pv) + "<br>Discount (future minus today): " + fmt((t("mode") === "series" ? n("fv") * k : n("fv")) - pv));
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
