@extends('layouts.app')

@section('title', 'Employee Cost Calculator — Free Online Tool')
@section('meta_description', 'Calculate total cost of an employee to the company including salary, EOBI, provident fund, bonuses and benefits')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Employee Cost Calculator</h1>
            <p class="lead small text-muted">Calculate total cost of an employee to the company including salary, EOBI, provident fund, bonuses and benefits</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="salary">Monthly Gross Salary (Rs)</label><input type="number" step="any" class="form-control" id="salary" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="eobi">Employer EOBI per Month (Rs, editable)</label><input type="number" step="any" class="form-control" id="eobi" value="2520"></div>
                    <div class="col-md-6"><label class="form-label" for="pfPct">Employer Provident Fund % of salary</label><input type="number" step="any" class="form-control" id="pfPct" value="0"></div>
                    <div class="col-md-6"><label class="form-label" for="bonus">Yearly Bonus (Rs)</label><input type="number" step="any" class="form-control" id="bonus" value="100000"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other Yearly Benefits: medical, fuel etc (Rs)</label><input type="number" step="any" class="form-control" id="other" value="60000"></div>
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
                        <li>Loaded cost adds EOBI, employer PF, bonuses and benefits on top of gross salary. EOBI is an editable field.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> EOBI and social security rates change and differ by province. Rates change — verify with the official source before relying on this.</p>
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
        var pf = n("salary") * n("pfPct") / 100; var monthly = n("salary") + n("eobi") + pf + (n("bonus") + n("other")) / 12; setResult("Employer PF per month: " + fmt(pf) + "<br><strong>Total monthly cost to company: " + fmt(monthly) + "</strong><br>Total yearly cost to company: " + fmt(monthly * 12) + "<br>Extra load above salary: " + pct(n("salary") > 0 ? (monthly - n("salary")) / n("salary") * 100 : 0)); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
