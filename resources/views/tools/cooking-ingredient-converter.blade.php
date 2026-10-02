@extends('layouts.app')
@section('title', 'Cooking Ingredient Converter — Free Online Tool')
@section('meta_description', 'Select an ingredient such as flour, sugar or butter, then convert grams to cups with the correct weight.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Cooking Ingredient Converter</h1>
            <p class="lead small text-muted">Select an ingredient and convert grams to cups with the correct density.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="ingSel">Ingredient</label><select class="form-select" id="ingSel"><option value="120">All-purpose Flour (120g per cup)</option><option value="200">Granulated Sugar (200g per cup)</option><option value="227">Butter (227g per cup)</option><option value="100">Cocoa Powder (100g per cup)</option><option value="185">Cooked / Uncooked Rice (185g per cup)</option><option value="220">Brown Sugar packed (220g per cup)</option><option value="125">Powdered Sugar (125g per cup)</option><option value="245">Milk (245g per cup)</option><option value="240">Water (240g per cup)</option><option value="90">Rolled Oats (90g per cup)</option></select></div>
                <div class="col-md-4"><label class="form-label" for="ingVal">Amount</label><input type="number" step="any" class="form-control" id="ingVal" value="240"></div>
                <div class="col-md-4"><label class="form-label" for="ingUnit">Unit</label><select class="form-select" id="ingUnit"><option value="g">Grams (g)</option><option value="cup">Cups</option><option value="tbsp">Tablespoons</option><option value="tsp">Teaspoons</option><option value="oz">Ounces (oz)</option><option value="kg">Kilograms (kg)</option></select></div>
            </div>
            <div class="mt-3" id="allOut"></div>
            <div class="alert alert-info mt-3 mb-0" id="resultBox">Result will appear here.</div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter a value or select an option.</li>
                <li>Choose the From and To units — the result updates live.</li>
                <li>Also see the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Cup weights can change if sifted or packed — a kitchen scale (grams) is most accurate for baking.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var sEl=document.getElementById("ingSel"); var vEl=document.getElementById("ingVal"); var uEl=document.getElementById("ingUnit"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(3)).toLocaleString("en-US",{maximumFractionDigits:3}); }
function calc(){ var v=parseFloat(vEl.value); var perCup=parseFloat(sEl.value); if(isNaN(v)){ box.textContent="Please enter a valid amount."; return; } var grams; if(uEl.value==="g") grams=v; else if(uEl.value==="cup") grams=v*perCup; else if(uEl.value==="tbsp") grams=v*perCup/16; else if(uEl.value==="tsp") grams=v*perCup/48; else if(uEl.value==="oz") grams=v*28.3495; else grams=v*1000; var cups=grams/perCup; box.textContent=fmt(grams)+" g = "+fmt(cups)+" cups"; allEl.innerHTML="<div>Grams: <strong>"+fmt(grams)+"</strong> | kg: <strong>"+fmt(grams/1000)+"</strong> | oz: <strong>"+fmt(grams/28.3495)+"</strong></div><div>Cups: <strong>"+fmt(cups)+"</strong> | Tablespoons: <strong>"+fmt(cups*16)+"</strong> | Teaspoons: <strong>"+fmt(cups*48)+"</strong></div>"; }
[sEl,vEl,uEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
