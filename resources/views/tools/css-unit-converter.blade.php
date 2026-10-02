@extends('layouts.app')
@section('title', 'CSS Unit Converter — Free Online Tool')
@section('meta_description', 'Enter a size and convert it to px, rem, em, pt and percent with a base font size.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">CSS Unit Converter</h1>
            <p class="lead small text-muted">Convert web sizes to px, rem, em, pt and percent using a base font size.</p>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="cssVal">Value</label><input type="number" step="any" class="form-control" id="cssVal" value="16"></div>
                <div class="col-md-3"><label class="form-label" for="cssUnit">Unit</label><select class="form-select" id="cssUnit"><option value="px">px</option><option value="rem">rem</option><option value="em">em</option><option value="pt">pt</option><option value="percent">Percent (%)</option></select></div>
                <div class="col-md-3"><label class="form-label" for="baseSize">Base Font Size (px)</label><input type="number" step="any" class="form-control" id="baseSize" value="16"></div>
                <div class="col-md-3"><label class="form-label" for="viewportW">Viewport Width px (for vw)</label><input type="number" step="any" class="form-control" id="viewportW" value="1200"></div>
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
                <li>Also see the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: 1 pt = 1.333 px (at 96 dpi). em depends on the parent element's size, rem on the root.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("cssVal"); var uEl=document.getElementById("cssUnit"); var bEl=document.getElementById("baseSize"); var vwEl=document.getElementById("viewportW"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(4)).toLocaleString("en-US",{maximumFractionDigits:4}); }
function calc(){ var v=parseFloat(vEl.value); var base=parseFloat(bEl.value); var vw=parseFloat(vwEl.value); if(isNaN(v)||isNaN(base)||base<=0){ box.textContent="Enter a valid value and base size."; return; } var px; if(uEl.value==="px") px=v; else if(uEl.value==="rem"||uEl.value==="em") px=v*base; else if(uEl.value==="pt") px=v*96/72; else px=v/100*base; var vwVal=isNaN(vw)||vw<=0?0:px/vw*100; box.textContent=fmt(px)+" px = "+fmt(px/base)+" rem"; allEl.innerHTML="<div>px: <strong>"+fmt(px)+"</strong> | rem: <strong>"+fmt(px/base)+"</strong> | em: <strong>"+fmt(px/base)+"</strong></div><div>pt: <strong>"+fmt(px*72/96)+"</strong> | Percent of base: <strong>"+fmt(px/base*100)+"%</strong> | vw: <strong>"+fmt(vwVal)+"</strong></div><div class=\"small\">CSS: font-size: "+fmt(px/base)+"rem; /* "+fmt(px)+"px */</div>"; }
[vEl,uEl,bEl,vwEl].forEach(function(el){ el.addEventListener("input",calc); el.addEventListener("change",calc); }); calc();
})();
</script>
@endsection
