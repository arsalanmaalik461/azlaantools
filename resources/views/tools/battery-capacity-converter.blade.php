@extends('layouts.app')
@section('title', 'Battery Capacity Converter — Free Online Tool')
@section('meta_description', 'Enter battery capacity and convert it between Ah, mAh, Wh and kWh at any voltage.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Battery Capacity Converter</h1>
            <p class="lead small text-muted">Convert battery capacity between Ah, mAh, Wh and kWh at any voltage — for solar and UPS systems.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="capVal">Capacity</label><input type="number" step="any" class="form-control" id="capVal" value="100"></div>
                <div class="col-md-4"><label class="form-label" for="capUnit">Unit</label><select class="form-select" id="capUnit"><option value="ah">Amp hour (Ah)</option><option value="mah">Milliamp hour (mAh)</option><option value="wh">Watt hour (Wh)</option><option value="kwh">Kilowatt hour (kWh)</option></select></div>
                <div class="col-md-4"><label class="form-label" for="volt">Voltage (V)</label><input type="number" step="any" class="form-control" id="volt" value="12"></div>
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
                <li>Choose the units — the result updates live.</li>
                <li>See the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Wh = Ah x Voltage. Actual usable capacity depends on battery type and depth of discharge.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("capVal"); var uEl=document.getElementById("capUnit"); var voltEl=document.getElementById("volt"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(4)).toLocaleString("en-US",{maximumFractionDigits:4}); }
function calc(){ var v=parseFloat(vEl.value); var volt=parseFloat(voltEl.value); if(isNaN(v)||isNaN(volt)||volt<=0){ box.textContent="Enter a valid capacity and voltage."; return; } var wh; if(uEl.value==="ah") wh=v*volt; else if(uEl.value==="mah") wh=v/1000*volt; else if(uEl.value==="wh") wh=v; else wh=v*1000; var ah=wh/volt; box.textContent=fmt(ah)+" Ah = "+fmt(wh)+" Wh at "+fmt(volt)+" V"; allEl.innerHTML="<div>Ah: <strong>"+fmt(ah)+"</strong></div><div>mAh: <strong>"+fmt(ah*1000)+"</strong></div><div>Wh: <strong>"+fmt(wh)+"</strong></div><div>kWh: <strong>"+fmt(wh/1000)+"</strong></div>"; }
[vEl,uEl,voltEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
