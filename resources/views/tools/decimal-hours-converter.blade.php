@extends('layouts.app')
@section('title', 'Decimal Hours Converter — Free Online Tool')
@section('meta_description', 'Enter time in decimal form like 1.5 hours and convert it instantly to hours, minutes and seconds.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Decimal Hours Converter</h1>
            <p class="lead small text-muted">Convert decimal hours to hours, minutes and seconds, and back again.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="decVal">Decimal Hours</label><input type="number" step="any" class="form-control" id="decVal" value="1.5"></div>
                <div class="col-md-6"><label class="form-label" for="hmsOut">Hours : Minutes : Seconds</label><input type="text" class="form-control" id="hmsOut" readonly></div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-4"><label class="form-label" for="hIn">Hours</label><input type="number" step="any" class="form-control" id="hIn" value="1"></div>
                <div class="col-4"><label class="form-label" for="mIn">Minutes</label><input type="number" step="any" class="form-control" id="mIn" value="30"></div>
                <div class="col-4"><label class="form-label" for="sIn">Seconds</label><input type="number" step="any" class="form-control" id="sIn" value="0"></div>
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
            <p class="small text-muted mb-0">Note: in payroll, 1.5 hours means 1 hour 30 minutes, not 1 hour 50 minutes.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var decEl=document.getElementById("decVal"); var outEl=document.getElementById("hmsOut"); var hEl=document.getElementById("hIn"); var mEl=document.getElementById("mIn"); var sEl=document.getElementById("sIn"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(4)).toLocaleString("en-US",{maximumFractionDigits:4}); }
function fromDec(){ var d=parseFloat(decEl.value); if(isNaN(d)){ outEl.value=""; box.textContent="Please enter valid decimal hours."; return; } var totalSec=Math.round(d*3600); var h=Math.floor(totalSec/3600); var m=Math.floor((totalSec%3600)/60); var s=totalSec%60; outEl.value=h+":"+String(m).padStart(2,"0")+":"+String(s).padStart(2,"0"); box.textContent=d+" hours = "+h+"h "+m+"m "+s+"s"; }
function fromHms(){ var h=parseFloat(hEl.value)||0; var m=parseFloat(mEl.value)||0; var s=parseFloat(sEl.value)||0; var dec=h+m/60+s/3600; allEl.innerHTML="<div>"+h+"h "+m+"m "+s+"s = <strong>"+fmt(dec)+" decimal hours</strong> | Total minutes: <strong>"+fmt(h*60+m+s/60)+"</strong> | Total seconds: <strong>"+fmt(h*3600+m*60+s)+"</strong></div>"; }
decEl.addEventListener("input",fromDec); [hEl,mEl,sEl].forEach(function(el){ el.addEventListener("input",fromHms); }); fromDec(); fromHms();
})();
</script>
@endsection
