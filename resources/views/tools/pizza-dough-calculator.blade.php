@extends('layouts.app')

@section('title', 'Pizza Dough Calculator — Free Online Tool')
@section('meta_description', 'Calculate dough ingredients by number of pizzas, ball weight and hydration.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Pizza Dough Calculator</h1>
                    <p class="lead small text-muted">Enter how many pizzas you are making, the dough ball weight and hydration, and get flour, water, salt and yeast in grams for the whole batch.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="pzCount">Number of pizzas</label><input type="number" class="form-control" id="pzCount" value="4" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="pzBall">Dough ball weight (g each)</label><input type="number" class="form-control" id="pzBall" value="250" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pzHyd">Hydration (%)</label><input type="number" class="form-control" id="pzHyd" value="62" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pzSalt">Salt (%)</label><input type="number" class="form-control" id="pzSalt" value="2.5" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pzYeast">Yeast (%)</label><input type="number" class="form-control" id="pzYeast" value="0.5" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pzOil">Oil (%)</label><input type="number" class="form-control" id="pzOil" value="0" min="0" step="any"></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="pzOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the number of pizzas and the dough ball weight (250 g suits a 12 inch pizza).</li>
                        <li>Set hydration, salt, yeast and oil percentages.</li>
                        <li>Weigh out the flour, water, salt, yeast and oil amounts shown for the full batch.</li>
                    </ol>
                    <p class="small text-muted mb-0">Percentages are bakers percentages based on flour weight. A 250 g ball at 62 percent hydration is a good starting point for home ovens; Neapolitan style often uses 55 to 65 percent hydration and a long cold ferment.</p>
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
        var total = num("pzCount") * num("pzBall");
        var hyd = num("pzHyd"), salt = num("pzSalt"), yeast = num("pzYeast"), oil = num("pzOil");
        var out = el("pzOut");
        if (total <= 0) { out.textContent = "Please enter the number of pizzas and a ball weight greater than zero."; return; }
        var flour = total / (1 + (hyd + salt + yeast + oil) / 100);
        out.innerHTML = "<strong>Total dough:</strong> " + total.toFixed(0) + " g<br><strong>Flour:</strong> " + flour.toFixed(1) + " g &nbsp; <strong>Water:</strong> " + (flour * hyd / 100).toFixed(1) + " g &nbsp; <strong>Salt:</strong> " + (flour * salt / 100).toFixed(1) + " g &nbsp; <strong>Yeast:</strong> " + (flour * yeast / 100).toFixed(2) + " g &nbsp; <strong>Oil:</strong> " + (flour * oil / 100).toFixed(1) + " g";
    }
    ["pzCount", "pzBall", "pzHyd", "pzSalt", "pzYeast", "pzOil"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
