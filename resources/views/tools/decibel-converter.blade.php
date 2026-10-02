@extends('layouts.app')
@section('title', 'Decibel Converter — Free Online Tool')
@section('meta_description', 'Enter a decibel value or ratio and convert it instantly to power ratio, voltage ratio and neper.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Decibel Converter</h1>
            <p class="lead small text-muted">Convert decibels to power ratio, voltage ratio and neper.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="dbVal">Decibel (dB)</label><input type="number" step="any" class="form-control" id="dbVal" value="20"></div>
                <div class="col-md-6"><label class="form-label" for="dbDir">Direction</label><select class="form-select" id="dbDir"><option value="fromdb">dB to Ratio</option><option value="toratio">Ratio to dB (enter the ratio below)</option></select></div>
            </div>
            <div class="mb-3 mt-3"><label class="form-label" for="ratioVal">Ratio (for the second option only)</label><input type="number" step="any" class="form-control" id="ratioVal" value="100"></div>
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
            <p class="small text-muted mb-0">Note: for power, dB = 10 log10(ratio); for voltage, dB = 20 log10(ratio). 1 Np = 8.686 dB.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var dbEl=document.getElementById("dbVal"); var dirEl=document.getElementById("dbDir"); var rEl=document.getElementById("ratioVal"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(4)).toLocaleString("en-US",{maximumFractionDigits:4}); }
function calc(){ var pr, vr, db; if(dirEl.value==="fromdb"){ db=parseFloat(dbEl.value); if(isNaN(db)){ box.textContent="Please enter a valid dB value."; return; } pr=Math.pow(10,db/10); vr=Math.pow(10,db/20); } else { var r=parseFloat(rEl.value); if(isNaN(r)||r<=0){ box.textContent="Ratio must be positive."; return; } pr=r; vr=r; db=10*Math.log10(r); } var np=db/8.685889638; box.textContent="Power ratio "+fmt(pr)+" = "+fmt(db)+" dB"; allEl.innerHTML="<div>Decibel: <strong>"+fmt(db)+" dB</strong> | Neper: <strong>"+fmt(np)+" Np</strong></div><div>Power ratio: <strong>"+fmt(Math.pow(10,db/10))+"</strong> | Voltage / current ratio: <strong>"+fmt(Math.pow(10,db/20))+"</strong></div>"; }
[dbEl,dirEl,rEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
