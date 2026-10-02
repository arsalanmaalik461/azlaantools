@extends('layouts.app')
@section('title', 'Area Converter — Free Online Tool')
@section('meta_description', 'Enter an area and convert it instantly into square feet, square metre, acre, hectare and square yard.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Area Converter</h1>
            <p class="lead small text-muted">Convert area between all units, including square feet, metre, acre, hectare, Marla and Kanal.</p>
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
                <li>See the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: In Pakistan 1 Marla = 272.25 sq ft and 1 Kanal = 20 Marla — in some areas the Marla size may differ, so check your deed.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';

var units = {sqm:{label:"Square metre (sq m)",factor:1},sqft:{label:"Square feet (sq ft)",factor:0.09290304},sqkm:{label:"Square kilometre (sq km)",factor:1000000},sqmi:{label:"Square mile",factor:2589988.11},acre:{label:"Acre",factor:4046.8564224},hectare:{label:"Hectare",factor:10000},sqyd:{label:"Square yard",factor:0.83612736},sqin:{label:"Square inch",factor:0.00064516},sqcm:{label:"Square centimetre",factor:0.0001},marla:{label:"Marla (272.25 sq ft)",factor:25.29285264},kanal:{label:"Kanal (20 Marla)",factor:505.8570528}};
var valEl=document.getElementById("val"); var fromEl=document.getElementById("fromUnit"); var toEl=document.getElementById("toUnit");
var outEl=document.getElementById("outVal"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
Object.keys(units).forEach(function(k){ var o1=document.createElement("option"); o1.value=k; o1.textContent=units[k].label; fromEl.appendChild(o1); var o2=document.createElement("option"); o2.value=k; o2.textContent=units[k].label; toEl.appendChild(o2); });
fromEl.value="sqft"; toEl.value="sqm";
function fmt(n){ if(!isFinite(n)) return "-"; if(n!==0&&(Math.abs(n)>=1e12||Math.abs(n)<1e-6)) return n.toExponential(6); return parseFloat(n.toFixed(8)).toLocaleString("en-US",{maximumFractionDigits:8}); }
function calc(){ var v=parseFloat(valEl.value); if(isNaN(v)){ outEl.value=""; box.textContent="Please enter a valid value."; if(allEl) allEl.innerHTML=""; return; } var baseVal=v*units[fromEl.value].factor; var r=baseVal/units[toEl.value].factor; outEl.value=fmt(r); box.textContent=fmt(v)+" "+units[fromEl.value].label+" = "+fmt(r)+" "+units[toEl.value].label; if(allEl){ var h=""; Object.keys(units).forEach(function(k){ h+="<div>"+units[k].label+": <strong>"+fmt(baseVal/units[k].factor)+"</strong></div>"; }); allEl.innerHTML=h; } }
valEl.addEventListener("input",calc); fromEl.addEventListener("change",calc); toEl.addEventListener("change",calc);
document.getElementById("swapBtn").addEventListener("click",function(){ var t=fromEl.value; fromEl.value=toEl.value; toEl.value=t; calc(); });
calc();

})();
</script>
@endsection
