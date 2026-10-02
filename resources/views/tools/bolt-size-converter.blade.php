@extends('layouts.app')
@section('title', 'Bolt Size Converter — Free Online Tool')
@section('meta_description', 'Enter a bolt size and instantly convert metric mm to imperial inch and gauge sizes.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Bolt Size Converter</h1>
            <p class="lead small text-muted">Convert metric bolt size to imperial inch and common thread pitch.</p>
            <div class="mb-3"><label class="form-label" for="boltSel">Metric Bolt Size</label><select class="form-select" id="boltSel">
            <option value="3">M3</option><option value="4">M4</option><option value="5">M5</option><option value="6" selected>M6</option><option value="8">M8</option><option value="10">M10</option><option value="12">M12</option><option value="14">M14</option><option value="16">M16</option><option value="18">M18</option><option value="20">M20</option></select></div>
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
            <p class="small text-muted mb-0">Note: metric and imperial bolts are not interchangeable — even if the diameter is close, the thread pitch is different, so do not force them in.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var sel=document.getElementById("boltSel"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
var data={"3":[0.5,"1/8 inch approx","5.5 mm wrench"],"4":[0.7,"5/32 inch approx","7 mm wrench"],"5":[0.8,"3/16 inch approx","8 mm wrench"],"6":[1,"1/4 inch approx","10 mm wrench"],"8":[1.25,"5/16 inch approx","13 mm wrench"],"10":[1.5,"3/8 inch approx","17 mm wrench"],"12":[1.75,"1/2 inch approx","19 mm wrench"],"14":[2,"9/16 inch approx","22 mm wrench"],"16":[2,"5/8 inch approx","24 mm wrench"],"18":[2.5,"11/16 inch approx","27 mm wrench"],"20":[2.5,"3/4 inch approx","30 mm wrench"]};
function calc(){ var mm=sel.value; var d=data[mm]; var inch=(parseFloat(mm)/25.4).toFixed(4); box.textContent="M"+mm+" = "+inch+" inch diameter, coarse pitch "+d[0]+" mm"; allEl.innerHTML="<div>Diameter: <strong>M"+mm+" ("+mm+" mm = "+inch+" inch)</strong></div><div>Closest imperial: <strong>"+d[1]+"</strong> (not an exact match — always check the thread)</div><div>Coarse pitch: <strong>"+d[0]+" mm</strong> | Common spanner: <strong>"+d[2]+"</strong></div>"; }
sel.addEventListener("change",calc); calc();
})();
</script>
@endsection
