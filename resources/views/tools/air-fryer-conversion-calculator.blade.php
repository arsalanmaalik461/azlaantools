@extends('layouts.app')
@section('title', 'Air Fryer Conversion Calculator — Free Online Tool')
@section('meta_description', 'Convert oven temperature and time to air fryer settings for any recipe.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Air Fryer Conversion Calculator</h1>
            <p class="lead small text-muted">Convert any oven recipe to air fryer settings — lower temperature and shorter time.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="ovenTemp">Oven Temperature</label><input type="number" step="any" class="form-control" id="ovenTemp" value="200"></div>
                <div class="col-md-4"><label class="form-label" for="tempUnit">Unit</label><select class="form-select" id="tempUnit"><option value="c">Celsius (C)</option><option value="f">Fahrenheit (F)</option></select></div>
                <div class="col-md-4"><label class="form-label" for="ovenTime">Oven Time (minutes)</label><input type="number" step="any" class="form-control" id="ovenTime" value="30"></div>
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
            <p class="small text-muted mb-0">Standard rule: lower the temperature by 20 C (25 F) and reduce the time by 20 percent. Every air fryer is different, so check the food halfway.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var tEl=document.getElementById("ovenTemp"); var uEl=document.getElementById("tempUnit"); var timeEl=document.getElementById("ovenTime"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(2)).toLocaleString("en-US",{maximumFractionDigits:2}); }
function calc(){ var t=parseFloat(tEl.value); var mins=parseFloat(timeEl.value); if(isNaN(t)||isNaN(mins)||mins<0){ box.textContent="Please enter valid temperature and time."; allEl.innerHTML=""; return; } var c = uEl.value==="c"? t : (t-32)*5/9; var afC=c-20; var afF=afC*9/5+32; var afTime=mins*0.8; box.textContent="Air fryer: "+fmt(afC)+" C ("+fmt(afF)+" F) for about "+fmt(afTime)+" minutes"; allEl.innerHTML="<div>Oven: "+fmt(c)+" C</div><div>Air fryer temperature: <strong>"+fmt(afC)+" C / "+fmt(afF)+" F</strong></div><div>Air fryer time: <strong>"+fmt(afTime)+" minutes</strong> (oven time x 0.8)</div><div class=\"small text-muted\">Check food halfway and shake or flip for even cooking.</div>"; }
[tEl,uEl,timeEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
