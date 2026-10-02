@extends('layouts.app')
@section('title', 'HbA1c Converter — Free Online Tool')
@section('meta_description', 'Enter an HbA1c value and convert percent to mmol per mol and average sugar estimate.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">HbA1c Converter</h1>
            <p class="lead small text-muted">Convert HbA1c to percent, mmol per mol and average sugar estimate.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="a1cVal">Value</label><input type="number" step="any" class="form-control" id="a1cVal" value="7"></div>
                <div class="col-md-6"><label class="form-label" for="a1cUnit">Unit</label><select class="form-select" id="a1cUnit"><option value="pct">Percent (%) NGSP</option><option value="mmol">mmol per mol IFCC</option></select></div>
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
                <li>Also see the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Estimate only — not medical advice. eAG formula: 28.7 x A1c minus 46.7 (ADAG study). Actual average sugar can differ for each person.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("a1cVal"); var uEl=document.getElementById("a1cUnit"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(2)).toLocaleString("en-US",{maximumFractionDigits:2}); }
function calc(){ var v=parseFloat(vEl.value); if(isNaN(v)||v<=0){ box.textContent="Enter a valid HbA1c."; return; } var pct=uEl.value==="pct"?v:(v/10.929)+2.15; var mmol=(pct-2.15)*10.929; var eagMg=28.7*pct-46.7; var eagMmol=eagMg/18.01559; box.textContent="HbA1c "+fmt(pct)+"% = "+fmt(mmol)+" mmol/mol, average sugar about "+fmt(eagMg)+" mg/dL"; allEl.innerHTML="<div>Percent (NGSP): <strong>"+fmt(pct)+"%</strong></div><div>mmol per mol (IFCC): <strong>"+fmt(mmol)+"</strong></div><div>Estimated average glucose: <strong>"+fmt(eagMg)+" mg/dL / "+fmt(eagMmol)+" mmol/L</strong></div>"; }
vEl.addEventListener("input",calc); uEl.addEventListener("change",calc); calc();
})();
</script>
@endsection
