@extends('layouts.app')
@section('title', 'Beaufort Wind Scale Converter — Free Online Tool')
@section('meta_description', 'Enter the wind speed and instantly see the Beaufort number and its description.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Beaufort Wind Scale Converter</h1>
            <p class="lead small text-muted">Convert wind speed to a Beaufort number and its description.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="windVal">Wind Speed</label><input type="number" step="any" class="form-control" id="windVal" value="30"></div>
                <div class="col-md-6"><label class="form-label" for="windUnit">Unit</label><select class="form-select" id="windUnit"><option value="kmh">km per hour</option><option value="mph">Miles per hour</option><option value="knots">Knots</option><option value="ms">Metre per second</option></select></div>
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
                <li>See the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: the Beaufort scale is an observational scale of wind effects — the limits are approximate.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("windVal"); var uEl=document.getElementById("windUnit"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
var table=[[1,"Calm"],[6,"Light air"],[12,"Light breeze"],[20,"Gentle breeze"],[29,"Moderate breeze"],[39,"Fresh breeze"],[50,"Strong breeze"],[62,"Near gale"],[75,"Gale"],[89,"Strong gale"],[103,"Storm"],[118,"Violent storm"],[9999,"Hurricane force"]];
function fmt(n){ return parseFloat(n.toFixed(2)).toLocaleString("en-US",{maximumFractionDigits:2}); }
function calc(){ var v=parseFloat(vEl.value); if(isNaN(v)||v<0){ box.textContent="Enter a valid wind speed."; return; } var kmh = uEl.value==="kmh"?v: uEl.value==="mph"?v*1.609344: uEl.value==="knots"?v*1.852: v*3.6; var b=0; for(var i=0;i<table.length;i++){ if(kmh<table[i][0]){ b=i; break; } } if(kmh>=118) b=12; var desc=table[b][1]; box.textContent="Beaufort "+b+" — "+desc+" ("+fmt(kmh)+" km/h)"; allEl.innerHTML="<div>km/h: <strong>"+fmt(kmh)+"</strong> | mph: <strong>"+fmt(kmh/1.609344)+"</strong> | knots: <strong>"+fmt(kmh/1.852)+"</strong> | m/s: <strong>"+fmt(kmh/3.6)+"</strong></div><div>Beaufort: <strong>"+b+" — "+desc+"</strong></div>"; }
vEl.addEventListener("input",calc); uEl.addEventListener("change",calc); calc();
})();
</script>
@endsection
