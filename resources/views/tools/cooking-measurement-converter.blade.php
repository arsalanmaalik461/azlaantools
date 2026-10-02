@extends('layouts.app')
@section('title', 'Cooking Measurement Converter — Free Online Tool')
@section('meta_description', 'Enter cooking units and instantly convert to cups, tablespoons, teaspoons, ml and grams.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Cooking Measurement Converter</h1>
            <p class="lead small text-muted">Convert cooking volume units to cups, tablespoons, teaspoons, ml and litres.</p>
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label class="form-label" for="val">Value</label><input type="number" step="any" class="form-control" id="val" value="1"><label class="form-label mt-2" for="fromUnit">From</label><select class="form-select" id="fromUnit"></select></div>
                <div class="col-md-2 text-center"><button type="button" class="btn btn-outline-secondary w-100" id="swapBtn">&#8646; Swap</button></div>
                <div class="col-md-5"><label class="form-label" for="outVal">Result</label><input type="text" class="form-control" id="outVal" readonly><label class="form-label mt-2" for="toUnit">To</label><select class="form-select" id="toUnit"></select></div>
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
            <p class="small text-muted mb-0">Note: US cup = 236.588 ml, 1 tbsp = 3 tsp. Gram conversion depends on the density of the ingredient — for that use the Cooking Ingredient Converter.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';

var units = {ml:{label:"Millilitre (ml)",factor:1},l:{label:"Litre (L)",factor:1000},cup:{label:"US Cup",factor:236.588236},tbsp:{label:"Tablespoon (tbsp)",factor:14.7867648},tsp:{label:"Teaspoon (tsp)",factor:4.92892159},floz:{label:"Fluid ounce (fl oz)",factor:29.5735296},pint:{label:"US Pint",factor:473.176473},quart:{label:"US Quart",factor:946.352946},gallon:{label:"US Gallon",factor:3785.41178}};
var valEl=document.getElementById("val"); var fromEl=document.getElementById("fromUnit"); var toEl=document.getElementById("toUnit");
var outEl=document.getElementById("outVal"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
Object.keys(units).forEach(function(k){ var o1=document.createElement("option"); o1.value=k; o1.textContent=units[k].label; fromEl.appendChild(o1); var o2=document.createElement("option"); o2.value=k; o2.textContent=units[k].label; toEl.appendChild(o2); });
fromEl.value="cup"; toEl.value="ml";
function fmt(n){ if(!isFinite(n)) return "-"; if(n!==0&&(Math.abs(n)>=1e12||Math.abs(n)<1e-6)) return n.toExponential(6); return parseFloat(n.toFixed(8)).toLocaleString("en-US",{maximumFractionDigits:8}); }
function calc(){ var v=parseFloat(valEl.value); if(isNaN(v)){ outEl.value=""; box.textContent="Please enter a valid value."; if(allEl) allEl.innerHTML=""; return; } var baseVal=v*units[fromEl.value].factor; var r=baseVal/units[toEl.value].factor; outEl.value=fmt(r); box.textContent=fmt(v)+" "+units[fromEl.value].label+" = "+fmt(r)+" "+units[toEl.value].label; if(allEl){ var h=""; Object.keys(units).forEach(function(k){ h+="<div>"+units[k].label+": <strong>"+fmt(baseVal/units[k].factor)+"</strong></div>"; }); allEl.innerHTML=h; } }
valEl.addEventListener("input",calc); fromEl.addEventListener("change",calc); toEl.addEventListener("change",calc);
document.getElementById("swapBtn").addEventListener("click",function(){ var t=fromEl.value; fromEl.value=toEl.value; toEl.value=t; calc(); });
calc();

})();
</script>
@endsection
