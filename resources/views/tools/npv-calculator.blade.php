@extends('layouts.app')

@section('title', 'NPV Calculator — Free Online Tool')
@section('meta_description', 'Calculate net present value of a project from yearly cash flows and a discount rate entered by the user')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">NPV Calculator</h1>
            <p class="lead small text-muted">Calculate net present value of a project from yearly cash flows and a discount rate entered by the user</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="initial">Initial Investment (Rs)</label><input type="number" step="any" class="form-control" id="initial" value="1000000"></div>
                    <div class="col-md-6"><label class="form-label" for="rate">Discount Rate % per Year</label><input type="number" step="any" class="form-control" id="rate" value="12"></div>
                    <div class="col-md-6"><label class="form-label" for="cf1">Year 1 Cash Flow (Rs)</label><input type="number" step="any" class="form-control" id="cf1" value="300000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf2">Year 2 Cash Flow (Rs)</label><input type="number" step="any" class="form-control" id="cf2" value="350000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf3">Year 3 Cash Flow (Rs)</label><input type="number" step="any" class="form-control" id="cf3" value="400000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf4">Year 4 Cash Flow (Rs)</label><input type="number" step="any" class="form-control" id="cf4" value="450000"></div>
                    <div class="col-md-6"><label class="form-label" for="cf5">Year 5 Cash Flow (Rs)</label><input type="number" step="any" class="form-control" id="cf5" value="500000"></div>
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
                        <li>Each yearly cash flow is discounted back to today at your rate, added up, and the initial investment is subtracted.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> NPV is only as good as the cash flow estimates and discount rate you enter.</p>
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
        var r = n("rate") / 100; var cfs = [n("cf1"), n("cf2"), n("cf3"), n("cf4"), n("cf5")]; var pv = 0; for (var i = 0; i < 5; i++) { pv += cfs[i] / Math.pow(1 + r, i + 1); } var npv = pv - n("initial"); setResult("Present value of inflows: " + fmt(pv) + "<br><strong>NPV: " + fmt(npv) + "</strong><br>" + (npv >= 0 ? "Positive NPV: the project earns more than the discount rate." : "Negative NPV: the project earns less than the discount rate.")); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
