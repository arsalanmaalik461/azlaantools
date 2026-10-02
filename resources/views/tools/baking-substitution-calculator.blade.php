@extends('layouts.app')

@section('title', 'Baking Substitution Calculator — Free Online Tool')
@section('meta_description', 'Find substitute amounts when a baking ingredient is missing at home.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Baking Substitution Calculator</h1>
                    <p class="lead small text-muted">Pick the missing ingredient and the amount in your recipe to see the standard substitute and exactly how much of it to use.</p>
                    <div class="row g-3">
                        <div class="col-md-5"><label class="form-label" for="bsIng">Missing ingredient</label><select class="form-select" id="bsIng"></select></div>
                        <div class="col-md-3"><label class="form-label" for="bsAmt">Amount in recipe</label><input type="number" class="form-control" id="bsAmt" value="1" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bsUnit">Unit</label><select class="form-select" id="bsUnit"><option value="cup">Cup</option><option value="tbsp">Tablespoon</option><option value="tsp">Teaspoon</option><option value="g">Grams</option></select></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="bsOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose the ingredient that is missing from the list.</li>
                        <li>Enter the amount and unit used in your recipe.</li>
                        <li>Use the substitute amount shown — results follow standard published substitution ratios.</li>
                    </ol>
                    <p class="small text-muted mb-0">Substitution ratios are standard published kitchen references. Texture and flavour can change slightly, and leavening substitutes (baking powder and baking soda) should not be swapped in both directions without the acid adjustment noted.</p>
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
    var data = [
        { name: "Butter", sub: "Neutral oil (sunflower or canola)", ratio: 0.75, note: "Use 3/4 of the amount. For creamed cakes, melted butter substitute may give a denser crumb." },
        { name: "Butter (for greasing and saute style baking)", sub: "Ghee (clarified butter)", ratio: 1, note: "Swap 1 to 1 by volume." },
        { name: "Baking powder", sub: "Baking soda plus cream of tartar", ratio: 1, note: "For each 1 tsp baking powder use 1/4 tsp baking soda plus 1/2 tsp cream of tartar." },
        { name: "Baking soda", sub: "Baking powder", ratio: 3, note: "Use 3 times the amount of baking powder, and reduce other liquid slightly if the batter feels thin." },
        { name: "Buttermilk", sub: "Milk plus lemon juice or vinegar", ratio: 1, note: "For each cup, add 1 tbsp lemon juice or vinegar to milk, rest 5 minutes." },
        { name: "Caster sugar", sub: "Granulated sugar blended fine", ratio: 1, note: "Swap 1 to 1; pulse granulated sugar in a blender for a finer texture." },
        { name: "Brown sugar", sub: "White sugar plus molasses or jaggery syrup", ratio: 1, note: "For each cup use 1 cup white sugar plus 1 to 2 tbsp molasses." },
        { name: "Egg (1 whole)", sub: "Mashed banana or applesauce", ratio: 1, note: "Use 1/4 cup (about 60 g) per egg in muffins and quick breads. Not suitable for meringue." },
        { name: "Self raising flour", sub: "Plain flour plus baking powder", ratio: 1, note: "For each cup (120 g) add 1.5 tsp baking powder and 1/4 tsp salt." },
        { name: "Cornflour / cornstarch", sub: "Plain flour", ratio: 2, note: "Use twice the amount of plain flour for thickening sauces." },
        { name: "Cream (double / heavy)", sub: "Milk plus melted butter", ratio: 1, note: "For each cup use 3/4 cup milk plus 1/4 cup melted butter. Will not whip." },
        { name: "Honey", sub: "Sugar plus water", ratio: 1.25, note: "For each cup of honey use 1.25 cups sugar plus 1/4 cup extra liquid, and reduce other liquids." },
        { name: "Milk", sub: "Water plus milk powder", ratio: 1, note: "For each cup use 1 cup water plus 3 tbsp milk powder, or swap 1 to 1 with unsweetened plant milk." },
        { name: "Sour cream / yogurt", sub: "Plain yogurt or sour cream", ratio: 1, note: "Swap 1 to 1 either way in cakes and marinades." },
        { name: "Vegetable oil", sub: "Melted butter or ghee", ratio: 1, note: "Swap 1 to 1 by volume; expect a richer flavour." }
    ];
    var sel = el("bsIng");
    data.forEach(function (d, i) { var o = document.createElement("option"); o.value = i; o.textContent = d.name; sel.appendChild(o); });
    function calc() {
        var d = data[parseInt(sel.value, 10) || 0];
        var amt = parseFloat(el("bsAmt").value);
        var unit = el("bsUnit").value;
        var out = el("bsOut");
        if (isNaN(amt) || amt <= 0) { out.textContent = "Please enter an amount greater than zero."; return; }
        var subAmt = amt * d.ratio;
        out.innerHTML = "<strong>Substitute:</strong> " + d.sub + "<br><strong>Use:</strong> " + (Math.round(subAmt * 100) / 100) + " " + unit + " instead of " + amt + " " + unit + " " + d.name.toLowerCase() + ".<br><span>" + d.note + "</span>";
    }
    [sel, el("bsAmt"), el("bsUnit")].forEach(function (n) { n.addEventListener("input", calc); n.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
