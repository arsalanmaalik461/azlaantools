@extends('layouts.app')
@section('title', 'Cholesterol Converter — Free Online Tool')
@section('meta_description', 'Enter your cholesterol value and convert mg per dL to mmol per L instantly.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Cholesterol Converter</h1>
            <p class="lead small text-muted">Convert cholesterol and triglycerides between mg per dL and mmol per L.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="cholVal">Value</label><input type="number" step="any" class="form-control" id="cholVal" value="200"></div>
                <div class="col-md-4"><label class="form-label" for="cholType">Type</label><select class="form-select" id="cholType"><option value="chol">Total / LDL / HDL Cholesterol</option><option value="trig">Triglycerides</option></select></div>
                <div class="col-md-4"><label class="form-label" for="cholUnit">Input Unit</label><select class="form-select" id="cholUnit"><option value="mgdl">mg per dL</option><option value="mmoll">mmol per L</option></select></div>
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
            <p class="small text-muted mb-0">Note: Estimate only — not medical advice. Cholesterol factor is 38.67 and triglycerides factor is 88.57.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("cholVal"); var tEl=document.getElementById("cholType"); var uEl=document.getElementById("cholUnit"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(3)).toLocaleString("en-US",{maximumFractionDigits:3}); }
function calc(){ var v=parseFloat(vEl.value); if(isNaN(v)){ box.textContent="Please enter a valid value."; return; } var f=tEl.value==="trig"?88.57:38.67; var mgdl=uEl.value==="mgdl"?v:v*f; var mmol=mgdl/f; box.textContent=fmt(mgdl)+" mg/dL = "+fmt(mmol)+" mmol/L"; allEl.innerHTML="<div>mg per dL: <strong>"+fmt(mgdl)+"</strong></div><div>mmol per L: <strong>"+fmt(mmol)+"</strong></div>"; }
[vEl,tEl,uEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
