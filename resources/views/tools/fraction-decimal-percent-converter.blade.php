@extends('layouts.app')
@section('title', 'Fraction Decimal Percent Converter — Free Online Tool')
@section('meta_description', 'Enter a fraction, decimal or percent and convert it into all three formats instantly.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Fraction Decimal Percent Converter</h1>
            <p class="lead small text-muted">Convert a fraction, decimal or percent into all three formats instantly.</p>
            <div class="row g-3 align-items-end">
                <div class="col-3"><label class="form-label" for="numIn">Numerator</label><input type="number" step="any" class="form-control" id="numIn" value="1"></div>
                <div class="col-3"><label class="form-label" for="denIn">Denominator</label><input type="number" step="any" class="form-control" id="denIn" value="2"></div>
                <div class="col-3"><label class="form-label" for="decIn">Decimal</label><input type="number" step="any" class="form-control" id="decIn" value="0.5"></div>
                <div class="col-3"><label class="form-label" for="pctIn">Percent (%)</label><input type="number" step="any" class="form-control" id="pctIn" value="50"></div>
            </div>
            <div class="mt-3" id="allOut"></div>
            <div class="alert alert-info mt-3 mb-0" id="resultBox">Result will appear here.</div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter a value in any box.</li>
                <li>The result updates live.</li>
                <li>See all formats below too.</li>
            </ol>
            <p class="small text-muted mb-0">Note: A decimal is shown as the nearest simple fraction (denominator up to 10000).</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var nEl=document.getElementById("numIn"); var dEl=document.getElementById("denIn"); var decEl=document.getElementById("decIn"); var pctEl=document.getElementById("pctIn"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(6)).toLocaleString("en-US",{maximumFractionDigits:6}); }
function gcd(a,b){ a=Math.abs(Math.round(a)); b=Math.abs(Math.round(b)); return b?gcd(b,a%b):a; }
function show(dec,src){ if(src!=="frac"){ if(isFinite(dec)){ var scaled=Math.round(dec*10000); var g=gcd(scaled,10000)||1; nEl.value=scaled/g; dEl.value=10000/g; } } if(src!=="dec") decEl.value=parseFloat(dec.toFixed(8)); if(src!=="pct") pctEl.value=parseFloat((dec*100).toFixed(6)); box.textContent="Decimal "+fmt(dec)+" = "+fmt(dec*100)+"%"; allEl.innerHTML="<div>Fraction: <strong>"+nEl.value+"/"+dEl.value+"</strong> | Decimal: <strong>"+fmt(dec)+"</strong> | Percent: <strong>"+fmt(dec*100)+"%</strong></div>"; }
function fromFrac(){ var n=parseFloat(nEl.value); var d=parseFloat(dEl.value); if(isNaN(n)||isNaN(d)||d===0){ box.textContent="The denominator cannot be zero."; return; } show(n/d,"frac"); }
function fromDec(){ var v=parseFloat(decEl.value); if(isNaN(v)){ box.textContent="Enter a valid decimal."; return; } show(v,"dec"); }
function fromPct(){ var v=parseFloat(pctEl.value); if(isNaN(v)){ box.textContent="Enter a valid percent."; return; } show(v/100,"pct"); }
nEl.addEventListener("input",fromFrac); dEl.addEventListener("input",fromFrac); decEl.addEventListener("input",fromDec); pctEl.addEventListener("input",fromPct); fromFrac();
})();
</script>
@endsection
