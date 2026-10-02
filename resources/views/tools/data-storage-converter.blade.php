@extends('layouts.app')
@section('title', 'Data Storage Converter — Free Online Tool')
@section('meta_description', 'Enter a file size and instantly convert it to bytes, KB, MB, GB, TB and bits.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Data Storage Converter</h1>
            <p class="lead small text-muted">Convert file size to bits, bytes, KB, MB, GB, TB and binary KiB, MiB, GiB.</p>
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
            <p class="small text-muted mb-0">Note: Storage companies use KB = 1000 bytes, while operating systems often show KiB = 1024 bytes.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';

var units = {bit:{label:"Bit",factor:0.125},byte:{label:"Byte (B)",factor:1},kb:{label:"Kilobyte KB (1000 B)",factor:1000},kib:{label:"Kibibyte KiB (1024 B)",factor:1024},mb:{label:"Megabyte MB",factor:1000000},mib:{label:"Mebibyte MiB",factor:1048576},gb:{label:"Gigabyte GB",factor:1000000000},gib:{label:"Gibibyte GiB",factor:1073741824},tb:{label:"Terabyte TB",factor:1000000000000},tib:{label:"Tebibyte TiB",factor:1099511627776},pb:{label:"Petabyte PB",factor:1000000000000000}};
var valEl=document.getElementById("val"); var fromEl=document.getElementById("fromUnit"); var toEl=document.getElementById("toUnit");
var outEl=document.getElementById("outVal"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
Object.keys(units).forEach(function(k){ var o1=document.createElement("option"); o1.value=k; o1.textContent=units[k].label; fromEl.appendChild(o1); var o2=document.createElement("option"); o2.value=k; o2.textContent=units[k].label; toEl.appendChild(o2); });
fromEl.value="mb"; toEl.value="gb";
function fmt(n){ if(!isFinite(n)) return "-"; if(n!==0&&(Math.abs(n)>=1e12||Math.abs(n)<1e-6)) return n.toExponential(6); return parseFloat(n.toFixed(8)).toLocaleString("en-US",{maximumFractionDigits:8}); }
function calc(){ var v=parseFloat(valEl.value); if(isNaN(v)){ outEl.value=""; box.textContent="Please enter a valid value."; if(allEl) allEl.innerHTML=""; return; } var baseVal=v*units[fromEl.value].factor; var r=baseVal/units[toEl.value].factor; outEl.value=fmt(r); box.textContent=fmt(v)+" "+units[fromEl.value].label+" = "+fmt(r)+" "+units[toEl.value].label; if(allEl){ var h=""; Object.keys(units).forEach(function(k){ h+="<div>"+units[k].label+": <strong>"+fmt(baseVal/units[k].factor)+"</strong></div>"; }); allEl.innerHTML=h; } }
valEl.addEventListener("input",calc); fromEl.addEventListener("change",calc); toEl.addEventListener("change",calc);
document.getElementById("swapBtn").addEventListener("click",function(){ var t=fromEl.value; fromEl.value=toEl.value; toEl.value=t; calc(); });
calc();

})();
</script>
@endsection
