@extends('layouts.app')

@section('title', 'Recipe Scaler — Free Online Tool')
@section('meta_description', 'Scale every ingredient in a recipe up or down for any number of servings.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Recipe Scaler</h1>
                    <p class="lead small text-muted">Change the servings and every ingredient quantity scales with it — perfect for halving a cake recipe or doubling a dawat dish.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="rsFrom">Original servings</label><input type="number" class="form-control" id="rsFrom" value="4" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="rsTo">New servings</label><input type="number" class="form-control" id="rsTo" value="8" min="0" step="any"></div>
                        <div class="col-md-4 d-flex align-items-end"><button type="button" class="btn btn-outline-primary w-100" id="rsAdd">Add ingredient row</button></div>
                    </div>
                    <div id="rsRows" class="mt-3"></div>
                    <div class="table-responsive mt-3"><table class="table table-striped mb-0"><thead><tr><th>Ingredient</th><th>Original</th><th>Scaled</th></tr></thead><tbody id="rsBody"></tbody></table></div>                    <div class="alert alert-info mt-3 mb-0" id="rsMsg">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the original servings in the recipe and the servings you want.</li>
                        <li>Type each ingredient name, quantity and unit — rows are prefilled with an example.</li>
                        <li>Read the scaled quantities in the table and use them for cooking.</li>
                    </ol>
                    <p class="small text-muted mb-0">Scaling is purely proportional. Baking recipes with leavening, and very large multiplications, may still need small adjustments to salt, spices and baking powder, which do not always scale perfectly linearly.</p>
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
    var defaults = [["Flour", 500, "g"], ["Sugar", 200, "g"], ["Milk", 250, "ml"], ["Butter", 100, "g"], ["Eggs", 3, "pcs"]];
    function addRow(name, qty, unit) {
        var wrap = document.createElement("div");
        wrap.className = "row g-2 mb-2 rs-row";
        wrap.innerHTML = "<div class=\"col-md-5\"><input type=\"text\" class=\"form-control rs-name\" placeholder=\"Ingredient name\"></div><div class=\"col-md-3\"><input type=\"number\" class=\"form-control rs-qty\" step=\"any\" placeholder=\"Qty\"></div><div class=\"col-md-4\"><input type=\"text\" class=\"form-control rs-unit\" placeholder=\"Unit (g, ml, cup)\"></div>";
        wrap.querySelector(".rs-name").value = name || "";
        wrap.querySelector(".rs-qty").value = (qty === undefined ? "" : qty);
        wrap.querySelector(".rs-unit").value = unit || "";
        var inputs = wrap.querySelectorAll("input");
        for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); }
        el("rsRows").appendChild(wrap);
    }
    function calc() {
        var from = parseFloat(el("rsFrom").value), to = parseFloat(el("rsTo").value);
        var body = el("rsBody"), msg = el("rsMsg");
        if (isNaN(from) || from <= 0 || isNaN(to) || to <= 0) { msg.textContent = "Please enter original and new servings greater than zero."; body.innerHTML = ""; return; }
        var factor = to / from;
        var rows = document.querySelectorAll(".rs-row");
        var html = "";
        for (var i = 0; i < rows.length; i++) {
            var nm = rows[i].querySelector(".rs-name").value || "Ingredient " + (i + 1);
            var q = parseFloat(rows[i].querySelector(".rs-qty").value);
            var un = rows[i].querySelector(".rs-unit").value || "";
            if (isNaN(q)) { continue; }
            html += "<tr><td>" + nm + "</td><td>" + q + " " + un + "</td><td><strong>" + (Math.round(q * factor * 100) / 100) + " " + un + "</strong></td></tr>";
        }
        body.innerHTML = html || "<tr><td colspan=\"3\">Add at least one ingredient with a quantity.</td></tr>";
        msg.textContent = "Scale factor: x" + (Math.round(factor * 1000) / 1000) + " (from " + from + " to " + to + " servings).";
    }
    defaults.forEach(function (d) { addRow(d[0], d[1], d[2]); });
    el("rsAdd").addEventListener("click", function () { addRow("", "", ""); calc(); });
    el("rsFrom").addEventListener("input", calc);
    el("rsTo").addEventListener("input", calc);
    calc();
})();
</script>
@endsection
