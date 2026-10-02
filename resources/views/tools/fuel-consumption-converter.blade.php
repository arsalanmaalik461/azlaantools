@extends('layouts.app')
@section('title', 'Fuel Consumption Converter — Free Online Tool')
@section('meta_description', 'Enter your fuel average and instantly convert it to mpg, litres per 100 km and km per litre.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Fuel Consumption Converter</h1>
            <p class="lead small text-muted">Convert fuel average to MPG, litres per 100 km and km per litre.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="fuelVal">Value</label><input type="number" step="any" class="form-control" id="fuelVal" value="12"></div>
                <div class="col-md-6"><label class="form-label" for="fuelUnit">Unit</label><select class="form-select" id="fuelUnit"><option value="kmpl">km per litre (km/L)</option><option value="l100">Litres per 100 km (L/100km)</option><option value="mpgus">MPG (US)</option><option value="mpguk">MPG (UK / Imperial)</option></select></div>
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
                <li>See converted values for all units below too.</li>
            </ol>
            <p class="small text-muted mb-0">Note: US gallon = 3.785 litres, UK imperial gallon = 4.546 litres — so both MPG values are different.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("fuelVal"); var uEl=document.getElementById("fuelUnit"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(3)).toLocaleString("en-US",{maximumFractionDigits:3}); }
function calc(){ var v=parseFloat(vEl.value); if(isNaN(v)||v<=0){ box.textContent="Please enter a positive value."; return; } var l100; if(uEl.value==="kmpl") l100=100/v; else if(uEl.value==="l100") l100=v; else if(uEl.value==="mpgus") l100=235.215/v; else l100=282.481/v; var kmpl=100/l100; var mpgus=235.215/l100; var mpguk=282.481/l100; box.textContent=fmt(kmpl)+" km/L = "+fmt(l100)+" L/100km = "+fmt(mpgus)+" MPG (US)"; allEl.innerHTML="<div>km per litre: <strong>"+fmt(kmpl)+"</strong></div><div>L per 100 km: <strong>"+fmt(l100)+"</strong></div><div>MPG US: <strong>"+fmt(mpgus)+"</strong> | MPG UK: <strong>"+fmt(mpguk)+"</strong></div>"; }
vEl.addEventListener("input",calc); uEl.addEventListener("change",calc); calc();
})();
</script>
@endsection
