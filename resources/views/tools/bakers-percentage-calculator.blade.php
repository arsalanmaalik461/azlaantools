@extends('layouts.app')

@section('title', 'Bakers Percentage Calculator — Free Online Tool')
@section('meta_description', 'Calculate flour, water, salt and yeast by bakers percentage and hydration.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Bakers Percentage Calculator</h1>
                    <p class="lead small text-muted">Work out every ingredient in grams from total dough weight and hydration. Flour is always 100 percent and everything else is a percentage of the flour weight.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="bpTotal">Total dough weight (g)</label><input type="number" class="form-control" id="bpTotal" value="1000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bpHydration">Hydration / water (%)</label><input type="number" class="form-control" id="bpHydration" value="65" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bpSalt">Salt (%)</label><input type="number" class="form-control" id="bpSalt" value="2" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bpYeast">Yeast (%)</label><input type="number" class="form-control" id="bpYeast" value="1" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bpOther">Other ingredients (%)</label><input type="number" class="form-control" id="bpOther" value="0" min="0" step="any"></div>
                    </div>
                    <div class="table-responsive mt-3"><table class="table table-striped mb-0"><thead><tr><th>Ingredient</th><th>Grams</th><th>Percent of flour</th></tr></thead><tbody id="bpBody"></tbody></table></div>                    <div class="alert alert-info mt-3 mb-0" id="bpMsg">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the total dough weight you want in grams.</li>
                        <li>Set hydration, salt, yeast and any other ingredient percentages.</li>
                        <li>Read the flour, water, salt and yeast amounts in grams from the table.</li>
                    </ol>
                    <p class="small text-muted mb-0">Bakers percentage expresses every ingredient as a percentage of the flour weight, so flour is always 100 percent. The flour weight is found by dividing the total dough weight by 1 plus the sum of all other percentages.</p>
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
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function calc() {
        var total = num("bpTotal"), hyd = num("bpHydration"), salt = num("bpSalt"), yeast = num("bpYeast"), other = num("bpOther");
        var msg = el("bpMsg"), body = el("bpBody");
        if (total <= 0) { msg.textContent = "Please enter a total dough weight greater than zero."; body.innerHTML = ""; return; }
        var factor = 1 + (hyd + salt + yeast + other) / 100;
        var flour = total / factor;
        var rows = [["Flour", flour, 100], ["Water", flour * hyd / 100, hyd], ["Salt", flour * salt / 100, salt], ["Yeast", flour * yeast / 100, yeast]];
        if (other > 0) { rows.push(["Other ingredients", flour * other / 100, other]); }
        var html = "";
        rows.forEach(function (r) { html += "<tr><td>" + r[0] + "</td><td>" + r[1].toFixed(1) + " g</td><td>" + r[2] + "%</td></tr>"; });
        body.innerHTML = html;
        msg.textContent = "Flour needed: " + flour.toFixed(1) + " g for " + total.toLocaleString("en-US") + " g of dough at " + hyd + "% hydration.";
    }
    ["bpTotal", "bpHydration", "bpSalt", "bpYeast", "bpOther"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
