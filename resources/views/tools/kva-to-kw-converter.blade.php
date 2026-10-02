@extends('layouts.app')
@section('title', 'kVA to kW Converter — Free Online Tool')
@section('meta_description', 'Enter kVA and power factor to get real kW and the reverse calculation instantly.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">kVA to kW Converter</h1>
            <p class="lead small text-muted">Calculate kVA, kW and power factor for your generator or transformer.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="kvaIn">Apparent Power (kVA)</label><input type="number" step="any" class="form-control" id="kvaIn" value="10"></div>
                <div class="col-md-4"><label class="form-label" for="pfIn">Power Factor (0 to 1)</label><input type="number" step="any" class="form-control" id="pfIn" value="0.8"></div>
                <div class="col-md-4"><label class="form-label" for="kwOut">Real Power (kW)</label><input type="text" class="form-control" id="kwOut" readonly></div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6"><label class="form-label" for="voltIn">Voltage (V, for amps estimate)</label><input type="number" step="any" class="form-control" id="voltIn" value="400"></div>
                <div class="col-md-6"><label class="form-label" for="phaseSel">Phase</label><select class="form-select" id="phaseSel"><option value="3">Three Phase</option><option value="1">Single Phase</option></select></div>
            </div>
            <div class="mt-3" id="allOut"></div>
            <div class="alert alert-info mt-3 mb-0" id="resultBox">Result will appear here.</div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter kVA, power factor and voltage — the result updates live.</li>
                <li>See the real kW, reactive kVAR and current estimate below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: kW = kVA x Power Factor. Most loads have a PF of 0.8 lagging — always check the nameplate.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var kvaEl=document.getElementById("kvaIn"); var pfEl=document.getElementById("pfIn"); var kwEl=document.getElementById("kwOut"); var vEl2=document.getElementById("voltIn"); var phEl=document.getElementById("phaseSel"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(3)).toLocaleString("en-US",{maximumFractionDigits:3}); }
function calc(){ var kva=parseFloat(kvaEl.value); var pf=parseFloat(pfEl.value); var volt=parseFloat(vEl2.value); if(isNaN(kva)||isNaN(pf)||pf<=0||pf>1){ kwEl.value=""; box.textContent="Enter kVA and power factor (between 0 and 1) correctly."; return; } var kw=kva*pf; var kvar=Math.sqrt(Math.max(kva*kva-kw*kw,0)); kwEl.value=fmt(kw); var amps=""; if(!isNaN(volt)&&volt>0){ var a=phEl.value==="3"? (kva*1000)/(1.7320508*volt) : (kva*1000)/volt; amps="<div>Current (approx): <strong>"+fmt(a)+" Amps</strong></div>"; } box.textContent=fmt(kva)+" kVA x PF "+pf+" = "+fmt(kw)+" kW"; allEl.innerHTML="<div>kW (real): <strong>"+fmt(kw)+"</strong> | kVA (apparent): <strong>"+fmt(kva)+"</strong> | kVAR (reactive): <strong>"+fmt(kvar)+"</strong></div><div>Reverse: to get "+fmt(kw)+" kW at PF "+pf+", use a <strong>"+fmt(kw/pf)+" kVA</strong> generator.</div>"+amps; }
[kvaEl,pfEl,vEl2,phEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
