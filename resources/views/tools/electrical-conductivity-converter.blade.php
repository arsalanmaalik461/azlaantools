@extends('layouts.app')
@section('title', 'Electrical Conductivity Converter — Free Online Tool')
@section('meta_description', 'Enter conductivity or resistivity and convert between siemens per metre, mho and ohm metre.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Electrical Conductivity Converter</h1>
            <p class="lead small text-muted">Convert conductivity and resistivity between siemens, mho and ohm metre.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="ecVal">Value</label><input type="number" step="any" class="form-control" id="ecVal" value="1"></div>
                <div class="col-md-6"><label class="form-label" for="ecUnit">Unit</label><select class="form-select" id="ecUnit"><option value="sm">Siemens per metre (S/m) — conductivity</option><option value="msm">MilliSiemens per metre (mS/m)</option><option value="uscm">MicroSiemens per cm (uS/cm)</option><option value="ohm_m">Ohm metre (ohm m) — resistivity</option><option value="ohm_cm">Ohm centimetre (ohm cm)</option></select></div>
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
                <li>Choose the unit — the result updates live.</li>
                <li>See the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Resistivity = 1 / Conductivity. Mho is the old name for siemens (1 mho = 1 S).</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("ecVal"); var uEl=document.getElementById("ecUnit"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ if(!isFinite(n)) return "-"; if(n!==0&&(Math.abs(n)>=1e9||Math.abs(n)<1e-4)) return n.toExponential(4); return parseFloat(n.toFixed(6)).toLocaleString("en-US",{maximumFractionDigits:6}); }
function calc(){ var v=parseFloat(vEl.value); if(isNaN(v)||v<=0){ box.textContent="Please enter a positive value."; return; } var cond; if(uEl.value==="sm") cond=v; else if(uEl.value==="msm") cond=v/1000; else if(uEl.value==="uscm") cond=v*0.0001; else if(uEl.value==="ohm_m") cond=1/v; else cond=100/v; var res=1/cond; box.textContent="Conductivity "+fmt(cond)+" S/m = Resistivity "+fmt(res)+" ohm m"; allEl.innerHTML="<div>Conductivity: <strong>"+fmt(cond)+" S/m</strong> | "+fmt(cond*1000)+" mS/m | "+fmt(cond*10000)+" uS/cm</div><div>Resistivity: <strong>"+fmt(res)+" ohm m</strong> | "+fmt(res*100)+" ohm cm</div>"; }
vEl.addEventListener("input",calc); uEl.addEventListener("change",calc); calc();
})();
</script>
@endsection
