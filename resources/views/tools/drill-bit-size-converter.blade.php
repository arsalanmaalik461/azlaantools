@extends('layouts.app')
@section('title', 'Drill Bit Size Converter — Free Online Tool')
@section('meta_description', 'Enter a drill bit size and instantly convert it into mm, inch, gauge number and letter sizes.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Drill Bit Size Converter</h1>
            <p class="lead small text-muted">See drill bit sizes in mm, inch fraction, gauge and letter sizes.</p>
            <div class="mb-3"><label class="form-label" for="drillVal">Drill Size (mm)</label><input type="number" step="any" class="form-control" id="drillVal" value="6"></div>
            <div class="mt-3" id="allOut"></div>
            <div class="alert alert-info mt-3 mb-0" id="resultBox">Result will appear here.</div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter a value or pick an option.</li>
                <li>Choose From and To units — the result updates live.</li>
                <li>See the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: In number gauges, a bigger number = smaller drill. Letter sizes run from A to Z.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("drillVal"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
var letters=[[3.25,"A"],[3.66,"C"],[4.04,"E"],[4.57,"I"],[5.05,"K"],[5.41,"M"],[5.79,"O"],[6.25,"Q"],[6.53,"S"],[6.91,"U"],[7.14,"W"],[7.49,"Y"],[8.43,"Q2"],[9.09,"T2"],[9.35,"U2"],[9.80,"X2"]];
var gauges=[[1.6,"52"],[2,"48"],[2.5,"44"],[3,"40"],[3.25,"36"],[3.5,"33"],[4,"30"],[4.5,"25"],[5,"20"],[5.5,"16"],[6,"12"],[6.5,"9"],[7,"6"],[7.5,"3"],[8,"1"]];
function nearest(arr,v){ var best=arr[0]; var bd=1e9; arr.forEach(function(x){ var d=Math.abs(x[0]-v); if(d<bd){ bd=d; best=x; } }); return best; }
function frac(inch){ var den=64; var n=Math.round(inch*den); var a=n, b=den; function gcd(x,y){ return y?gcd(y,x%y):x; } var g=gcd(a,b); return (a/g)+"/"+(b/g)+" inch"; }
function calc(){ var mm=parseFloat(vEl.value); if(isNaN(mm)||mm<=0){ box.textContent="Please enter a valid mm size."; return; } var inch=mm/25.4; var g=nearest(gauges,mm); var l=nearest(letters,mm); box.textContent=mm+" mm = "+inch.toFixed(4)+" inch ("+frac(inch)+")"; allEl.innerHTML="<div>mm: <strong>"+mm+"</strong> | Inch decimal: <strong>"+inch.toFixed(4)+"</strong> | Nearest 64th: <strong>"+frac(inch)+"</strong></div><div>Nearest gauge number: <strong>#"+g[1]+" ("+g[0]+" mm)</strong> | Nearest letter: <strong>"+l[1]+" ("+l[0]+" mm)</strong></div><p class=\"small text-muted\">Gauge and letter are the nearest sizes, not always exact.</p>"; }
vEl.addEventListener("input",calc); calc();
})();
</script>
@endsection
