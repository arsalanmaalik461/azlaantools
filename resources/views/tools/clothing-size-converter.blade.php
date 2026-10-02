@extends('layouts.app')
@section('title', 'Clothing Size Converter — Free Online Tool')
@section('meta_description', 'Enter your clothing size and instantly convert to UK, US, EU sizes and inch/cm measurements.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Clothing Size Converter</h1>
            <p class="lead small text-muted">See clothing sizes in UK, US, EU and inch/cm measurements.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="clothType">Type</label><select class="form-select" id="clothType"><option value="women">Women Dress</option><option value="men">Men Suit / Jacket</option><option value="shoe_m">Men Shoes</option><option value="shoe_w">Women Shoes</option></select></div>
                <div class="col-md-6"><label class="form-label" for="clothSize">UK Size</label><select class="form-select" id="clothSize"></select></div>
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
                <li>Also see converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Size charts are general — a difference of 1 size between brands is normal.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var tEl=document.getElementById("clothType"); var sEl=document.getElementById("clothSize"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
var tables={women:{"6":[2,34],"8":[4,36],"10":[6,38],"12":[8,40],"14":[10,42],"16":[12,44],"18":[14,46],"20":[16,48]},men:{"36":[36,46],"38":[38,48],"40":[40,50],"42":[42,52],"44":[44,54],"46":[46,56],"48":[48,58]},shoe_m:{"6":[6.5,39],"7":[7.5,40],"8":[8.5,41],"9":[9.5,42],"10":[10.5,43],"11":[11.5,44],"12":[12.5,45]},shoe_w:{"3":[5,35.5],"4":[6,36.5],"5":[7,37.5],"6":[8,38.5],"7":[9,39.5],"8":[10,40.5]}};
function fill(){ sEl.innerHTML=""; Object.keys(tables[tEl.value]).forEach(function(k){ var o=document.createElement("option"); o.value=k; o.textContent="UK "+k; sEl.appendChild(o); }); calc(); }
function calc(){ var row=tables[tEl.value][sEl.value]; if(!row) return; box.textContent="UK "+sEl.value+" = US "+row[0]+" = EU "+row[1]; allEl.innerHTML="<div>UK: <strong>"+sEl.value+"</strong> | US: <strong>"+row[0]+"</strong> | EU: <strong>"+row[1]+"</strong></div><p class=\"small text-muted\">Size charts can differ from brand to brand — check the brand chart before ordering.</p>"; }
tEl.addEventListener("change",fill); sEl.addEventListener("change",calc); fill();
})();
</script>
@endsection
