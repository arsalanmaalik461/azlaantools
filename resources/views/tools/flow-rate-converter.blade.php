@extends('layouts.app')
@section('title', 'Flow Rate Converter — Free Online Tool')
@section('meta_description', 'Enter a flow rate and convert it into litres per minute, gallons per minute and cubic metres per hour.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Flow Rate Converter</h1>
            <p class="lead small text-muted">Convert a flow rate into litres per minute, gallons per minute and cubic metres per hour.</p>
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
                <li>Choose From and To units — the result updates live.</li>
                <li>See the converted values for all units below too.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Pump ratings are often given in L/min or m3/h.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';

var units = {lmin:{label:"Litre per minute (L/min)",factor:1},ls:{label:"Litre per second (L/s)",factor:60},lh:{label:"Litre per hour (L/h)",factor:0.0166666667},m3h:{label:"Cubic metre per hour (m3/h)",factor:16.6666667},m3s:{label:"Cubic metre per second (m3/s)",factor:60000},gpm:{label:"US gallon per minute (GPM)",factor:3.785411784},gph:{label:"US gallon per hour (GPH)",factor:0.0630901967},ft3s:{label:"Cubic foot per second (ft3/s)",factor:1699.0108}};
var valEl=document.getElementById("val"); var fromEl=document.getElementById("fromUnit"); var toEl=document.getElementById("toUnit");
var outEl=document.getElementById("outVal"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
Object.keys(units).forEach(function(k){ var o1=document.createElement("option"); o1.value=k; o1.textContent=units[k].label; fromEl.appendChild(o1); var o2=document.createElement("option"); o2.value=k; o2.textContent=units[k].label; toEl.appendChild(o2); });
fromEl.value="lmin"; toEl.value="gpm";
function fmt(n){ if(!isFinite(n)) return "-"; if(n!==0&&(Math.abs(n)>=1e12||Math.abs(n)<1e-6)) return n.toExponential(6); return parseFloat(n.toFixed(8)).toLocaleString("en-US",{maximumFractionDigits:8}); }
function calc(){ var v=parseFloat(valEl.value); if(isNaN(v)){ outEl.value=""; box.textContent="Please enter a valid value."; if(allEl) allEl.innerHTML=""; return; } var baseVal=v*units[fromEl.value].factor; var r=baseVal/units[toEl.value].factor; outEl.value=fmt(r); box.textContent=fmt(v)+" "+units[fromEl.value].label+" = "+fmt(r)+" "+units[toEl.value].label; if(allEl){ var h=""; Object.keys(units).forEach(function(k){ h+="<div>"+units[k].label+": <strong>"+fmt(baseVal/units[k].factor)+"</strong></div>"; }); allEl.innerHTML=h; } }
valEl.addEventListener("input",calc); fromEl.addEventListener("change",calc); toEl.addEventListener("change",calc);
document.getElementById("swapBtn").addEventListener("click",function(){ var t=fromEl.value; fromEl.value=toEl.value; toEl.value=t; calc(); });
calc();

})();
</script>
@endsection
