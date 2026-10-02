@extends('layouts.app')
@section('title', 'Gas Units Converter — Free Online Tool')
@section('meta_description', 'Enter a gas quantity and instantly convert it to kg, litres, cubic feet, cubic metres and MMBTU.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Gas Units Converter</h1>
            <p class="lead small text-muted">Convert LPG and gas to kg, litres, cubic feet, cubic metres and MMBTU.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="gasVal">Amount</label><input type="number" step="any" class="form-control" id="gasVal" value="11.8"></div>
                <div class="col-md-4"><label class="form-label" for="gasUnit">Unit</label><select class="form-select" id="gasUnit"><option value="kg">LPG Kilogram (kg)</option><option value="litre">LPG Litre (liquid)</option><option value="m3">Cubic metre gas (m3)</option><option value="cft">Cubic feet (cft)</option><option value="mmbtu">MMBTU</option><option value="therm">Therm</option><option value="kwh">kWh energy</option></select></div>
                <div class="col-md-4"><label class="form-label" for="gasKwh">Energy per kg LPG (kWh per kg, editable)</label><input type="number" step="any" class="form-control" id="gasKwh" value="13.6"></div>
            </div>
            <p class="small text-muted mt-2">Assumptions: LPG liquid density 0.51 kg per litre, 1 m3 natural gas = about 10.5 kWh. Adjust the values for your supplier.</p>
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
            <p class="small text-muted mb-0">Note: This is an approximate energy equivalence — actual values can differ with LPG composition and gas pressure. For bills, use your gas company's official conversion.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("gasVal"); var uEl=document.getElementById("gasUnit"); var kEl=document.getElementById("gasKwh"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(4)).toLocaleString("en-US",{maximumFractionDigits:4}); }
function calc(){ var v=parseFloat(vEl.value); var perKg=parseFloat(kEl.value); if(isNaN(v)||isNaN(perKg)||perKg<=0){ box.textContent="Please enter valid values."; return; } var kwh; if(uEl.value==="kg") kwh=v*perKg; else if(uEl.value==="litre") kwh=v*0.51*perKg; else if(uEl.value==="m3") kwh=v*10.5; else if(uEl.value==="cft") kwh=v*0.0283168*10.5; else if(uEl.value==="mmbtu") kwh=v*293.071; else if(uEl.value==="therm") kwh=v*29.3071; else kwh=v; var kg=kwh/perKg; var litre=kg/0.51; var m3=kwh/10.5; box.textContent=fmt(kg)+" kg LPG = "+fmt(kwh)+" kWh energy (approx.)"; allEl.innerHTML="<div>LPG kg: <strong>"+fmt(kg)+"</strong> | LPG litre (liquid): <strong>"+fmt(litre)+"</strong></div><div>Gas volume: <strong>"+fmt(m3)+" m3</strong> | <strong>"+fmt(m3*35.3147)+" cubic feet</strong></div><div>Energy: <strong>"+fmt(kwh)+" kWh</strong> | <strong>"+fmt(kwh/293.071)+" MMBTU</strong> | <strong>"+fmt(kwh/29.3071)+" therm</strong></div>"; }
[vEl,uEl,kEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
