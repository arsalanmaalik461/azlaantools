@extends('layouts.app')

@section('title', 'Vehicle Token Tax Estimator — Free Online Tool')
@section('meta_description', 'Enter your engine cc and vehicle type to estimate the yearly token tax.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Vehicle Token Tax Estimator</h1>
                    <p class="lead small text-muted">Enter your engine cc to get an estimate of the yearly token tax based on the cc slab. All slab rates are editable, and you can add late payment / arrears too.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="tkCc">Engine (cc)</label><input type="number" class="form-control" id="tkCc" value="1300" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="tkYears">Years to pay (including arrears)</label><input type="number" class="form-control" id="tkYears" value="1" min="1" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="tkLatePct">Late fee / penalty (% per year, editable)</label><input type="number" class="form-control" id="tkLatePct" value="0" min="0" step="any"></div>
                    </div>
                    <h2 class="h6 mt-4">CC slab table (Rs per year, every rate editable)</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="tkS0">Up to 1000 cc — annual token (Rs)</label><input type="number" class="form-control tk-slab" id="tkS0" value="800" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="tkS1">1001 to 1199 cc — annual token (Rs)</label><input type="number" class="form-control tk-slab" id="tkS1" value="1500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="tkS2">1200 to 1299 cc — annual token (Rs)</label><input type="number" class="form-control tk-slab" id="tkS2" value="1750" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="tkS3">1300 to 1499 cc — annual token (Rs)</label><input type="number" class="form-control tk-slab" id="tkS3" value="2500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="tkS4">1500 to 1599 cc — annual token (Rs)</label><input type="number" class="form-control tk-slab" id="tkS4" value="3750" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="tkS5">1600 to 1999 cc — annual token (Rs)</label><input type="number" class="form-control tk-slab" id="tkS5" value="4500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="tkS6">2000 cc and above — annual token (Rs)</label><input type="number" class="form-control tk-slab" id="tkS6" value="10000" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="tkOut">Enter your engine cc — the token tax estimate will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter your vehicle engine cc — the matching slab is selected automatically.</li>
                        <li>If you also need to pay token tax for previous years, increase the years and enter the late fee percentage (if any).</li>
                        <li>See the total payable token tax estimate below.</li>
                    </ol>
                    <p class="small text-muted mb-0">Token tax varies by province, and some provinces offer a lifetime token for vehicles up to 1000 cc — be sure to check your province schedule. Slab values are taken from commonly published rates and are editable. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup. Official source: provincial Excise and Taxation / e-Pay system.</p>
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
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) || v < 0 ? 0 : v; }
    function money(n) { return "Rs " + Math.round(n).toLocaleString("en-PK"); }
    var bounds = [1000, 1199, 1299, 1499, 1599, 1999, Infinity];
    var names = ["Up to 1000 cc", "1001 to 1199 cc", "1200 to 1299 cc", "1300 to 1499 cc", "1500 to 1599 cc", "1600 to 1999 cc", "2000 cc and above"];
    function calc() {
        var cc = num("tkCc");
        var out = el("tkOut");
        if (cc <= 0) { out.textContent = "Please enter a valid engine cc."; return; }
        var idx = 0;
        while (cc > bounds[idx]) { idx++; }
        var annual = num("tkS" + idx);
        var years = Math.max(1, parseInt(el("tkYears").value, 10) || 1);
        var base = annual * years;
        var late = base * num("tkLatePct") / 100;
        out.innerHTML = "<strong>Total token tax (estimated): " + money(base + late) + "</strong><br>Applied slab: " + names[idx] + " — yearly " + money(annual) + " x " + years + " years = " + money(base) + (late ? " + late fee " + money(late) : "") + ". Motorcycle token tax follows a different schedule.";
    }
    ["tkCc", "tkYears", "tkLatePct"].forEach(function (id) { el(id).addEventListener("input", calc); });
    var slabs = document.querySelectorAll(".tk-slab");
    for (var i = 0; i < slabs.length; i++) { slabs[i].addEventListener("input", calc); }
    calc();
})();
</script>
@endsection
