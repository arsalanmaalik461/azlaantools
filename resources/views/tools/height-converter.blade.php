@extends('layouts.app')
@section('title', 'Height Converter — Free Online Tool')
@section('meta_description', 'Enter your height and instantly convert feet and inches to cm and metres, e.g. 5 feet 8 inches.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Height Converter</h1>
            <p class="lead small text-muted">Convert height from feet/inches to cm, metres and inches.</p>
            <div class="row g-3 align-items-end">
                <div class="col-4"><label class="form-label" for="ftIn">Feet</label><input type="number" step="any" class="form-control" id="ftIn" value="5"></div>
                <div class="col-4"><label class="form-label" for="inIn">Inches</label><input type="number" step="any" class="form-control" id="inIn" value="8"></div>
                <div class="col-4"><label class="form-label" for="cmIn">or just Centimetres (cm)</label><input type="number" step="any" class="form-control" id="cmIn" placeholder="e.g. 173"></div>
            </div>
            <div class="mt-3" id="allOut"></div>
            <div class="alert alert-info mt-3 mb-0" id="resultBox">Result will appear here.</div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter a value or choose an option.</li>
                <li>Choose the From and To units — the result updates live.</li>
                <li>See the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: 1 inch = 2.54 cm exactly, 1 foot = 12 inches.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var ftEl=document.getElementById("ftIn"); var inEl=document.getElementById("inIn"); var cmEl=document.getElementById("cmIn"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(2)).toLocaleString("en-US",{maximumFractionDigits:2}); }
function show(cm){ var totalIn=cm/2.54; var ft=Math.floor(totalIn/12); var inch=totalIn-ft*12; box.textContent=fmt(cm)+" cm = "+ft+" feet "+fmt(inch)+" inch"; allEl.innerHTML="<div>Centimetres: <strong>"+fmt(cm)+"</strong> | Metres: <strong>"+fmt(cm/100)+"</strong></div><div>Total inches: <strong>"+fmt(totalIn)+"</strong> | Feet + inches: <strong>"+ft+" ft "+fmt(inch)+" in</strong></div>"; }
function fromFt(){ var ft=parseFloat(ftEl.value)||0; var inch=parseFloat(inEl.value)||0; var cm=(ft*12+inch)*2.54; cmEl.value=""; show(cm); }
function fromCm(){ var cm=parseFloat(cmEl.value); if(isNaN(cm)){ return; } show(cm); }
ftEl.addEventListener("input",fromFt); inEl.addEventListener("input",fromFt); cmEl.addEventListener("input",fromCm); fromFt();
})();
</script>
@endsection
