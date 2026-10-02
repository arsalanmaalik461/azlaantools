@extends('layouts.app')
@section('title', 'Altitude Pressure Converter — Free Online Tool')
@section('meta_description', 'Enter an altitude and find the air pressure and water boiling point at that height.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Altitude Pressure Converter</h1>
            <p class="lead small text-muted">Find the air pressure and water boiling point at any altitude.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="altVal">Altitude / Height</label><input type="number" step="any" class="form-control" id="altVal" value="1000"></div>
                <div class="col-md-6"><label class="form-label" for="altUnit">Unit</label><select class="form-select" id="altUnit"><option value="m">Metres (m)</option><option value="ft">Feet (ft)</option></select></div>
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
            <p class="small text-muted mb-0">Note: The barometric formula (ISA) is used. The boiling point is a rough estimate — the real value can differ slightly with pressure and weather.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("altVal"); var uEl=document.getElementById("altUnit"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(3)).toLocaleString("en-US",{maximumFractionDigits:3}); }
function calc(){ var v=parseFloat(vEl.value); if(isNaN(v)){ box.textContent="Please enter altitude."; return; } var h=uEl.value==="ft"? v*0.3048 : v; var p=1013.25*Math.pow(1-2.25577e-5*h,5.25588); if(h>11000||p<=0){ box.textContent="This formula is for altitudes up to about 11000 metres."; } var boil=100 - h*0.003353; var boilF=boil*9/5+32; box.textContent="At "+fmt(h)+" m: pressure "+fmt(p)+" hPa, boiling point "+fmt(boil)+" C"; allEl.innerHTML="<div>Pressure: <strong>"+fmt(p)+" hPa</strong> | "+fmt(p*0.02953)+" inHg | "+fmt(p/1013.25)+" atm | "+fmt(p*0.750062)+" mmHg</div><div>Water boiling point (estimate): <strong>"+fmt(boil)+" C / "+fmt(boilF)+" F</strong></div>"; }
vEl.addEventListener("input",calc); uEl.addEventListener("change",calc); calc();
})();
</script>
@endsection
