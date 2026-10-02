@extends('layouts.app')

@section('title', 'School Annual Fee Calculator — Free Online Tool')
@section('meta_description', 'Enter the admission fee, monthly fee and annual charges to work out a child yearly school cost.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">School Annual Fee Calculator</h1>
            <p class="lead small text-muted">Enter the admission fee, monthly fee and annual charges to work out a child yearly school cost.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="admission">Admission Fee One Time (Rs)</label><input type="number" step="any" class="form-control" id="admission" value="30000"></div>
                    <div class="col-md-6"><label class="form-label" for="monthly">Monthly Fee per Child (Rs)</label><input type="number" step="any" class="form-control" id="monthly" value="12000"></div>
                    <div class="col-md-6"><label class="form-label" for="months">Fee Months per Year</label><input type="number" step="any" class="form-control" id="months" value="12"></div>
                    <div class="col-md-6"><label class="form-label" for="annual">Annual Charges per Child (Rs)</label><input type="number" step="any" class="form-control" id="annual" value="15000"></div>
                    <div class="col-md-6"><label class="form-label" for="children">Number of Children</label><input type="number" step="any" class="form-control" id="children" value="2"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other Yearly Costs: books, uniform, van (Rs)</label><input type="number" step="any" class="form-control" id="other" value="40000"></div>
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
                        <li>Monthly fee is multiplied by fee months, annual charges are added per child, and admission is counted in the first year only.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Fee structures differ by school and class. Enter the figures from the school fee voucher.</p>
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
        var perChild = n("monthly") * n("months") + n("annual"); var firstYear = perChild * n("children") + n("admission") * n("children") + n("other"); var nextYear = perChild * n("children") + n("other"); setResult("Yearly fee per child: " + fmt(perChild) + "<br>First year total (with admission): " + fmt(firstYear) + "<br>Following years total: " + fmt(nextYear)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
