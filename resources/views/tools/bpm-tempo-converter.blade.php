@extends('layouts.app')
@section('title', 'BPM Tempo Converter — Free Online Tool')
@section('meta_description', 'Enter BPM and instantly convert beat time to milliseconds, Hz, and note delay times.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">BPM Tempo Converter</h1>
            <p class="lead small text-muted">Convert BPM to beat time, Hz, and music delay times.</p>
            <div class="mb-3"><label class="form-label" for="bpmVal">Tempo (BPM)</label><input type="number" step="any" class="form-control" id="bpmVal" value="120"></div>
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
            <p class="small text-muted mb-0">Note: Delay times assume quarter note = 1 beat in 4/4 time.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("bpmVal"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(3)).toLocaleString("en-US",{maximumFractionDigits:3}); }
function calc(){ var bpm=parseFloat(vEl.value); if(isNaN(bpm)||bpm<=0){ box.textContent="Enter a valid BPM."; return; } var ms=60000/bpm; var hz=bpm/60; box.textContent=bpm+" BPM = "+fmt(ms)+" ms per beat, "+fmt(hz)+" Hz"; allEl.innerHTML="<div>Whole note: <strong>"+fmt(ms*4)+" ms</strong> | Half: <strong>"+fmt(ms*2)+" ms</strong> | Quarter (1 beat): <strong>"+fmt(ms)+" ms</strong></div><div>Eighth: <strong>"+fmt(ms/2)+" ms</strong> | Sixteenth: <strong>"+fmt(ms/4)+" ms</strong> | Dotted quarter: <strong>"+fmt(ms*1.5)+" ms</strong> | Quarter triplet: <strong>"+fmt(ms*2/3)+" ms</strong></div>"; }
vEl.addEventListener("input",calc); calc();
})();
</script>
@endsection
