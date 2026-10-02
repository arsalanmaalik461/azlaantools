@extends('layouts.app')
@section('title', 'Baby Clothing Size Converter — Free Online Tool')
@section('meta_description', 'Enter baby age, weight or height and see the size chart from newborn to 24 months right away.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Baby Clothing Size Converter</h1>
            <p class="lead small text-muted">See the baby clothing size chart from newborn to 24 months with age, weight and height.</p>
            <div class="mb-3"><label class="form-label" for="ageSel">Age Group</label><select class="form-select" id="ageSel">
            <option value="0">Premature</option><option value="1" selected>Newborn</option><option value="2">0-3 months</option><option value="3">3-6 months</option><option value="4">6-9 months</option><option value="5">9-12 months</option><option value="6">12-18 months</option><option value="7">18-24 months</option></select></div>
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
            <p class="small text-muted mb-0">Note: brands size a little differently — give weight and height more importance than age.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var sel=document.getElementById("ageSel"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
var rows=[["Premature","Preemie","44-50 cm","up to 2.5 kg"],["Newborn","NB / 0 months","50-55 cm","2.5-3.5 kg"],["0-3 months","0-3M","55-62 cm","3.5-5.7 kg"],["3-6 months","3-6M","62-68 cm","5.7-7.5 kg"],["6-9 months","6-9M","68-74 cm","7.5-9 kg"],["9-12 months","9-12M","74-80 cm","9-10.5 kg"],["12-18 months","12-18M","80-86 cm","10.5-12 kg"],["18-24 months","18-24M / 2T","86-92 cm","12-13.5 kg"]];
function calc(){ var r=rows[parseInt(sel.value,10)]; box.textContent=r[0]+": size "+r[1]+", height "+r[2]+", weight "+r[3]; var h="<table class=\"table table-sm\"><thead><tr><th>Age</th><th>Label</th><th>Height</th><th>Weight</th></tr></thead><tbody>"; rows.forEach(function(x,i){ h+="<tr"+(i===parseInt(sel.value,10)?" class=\"table-active fw-bold\"":"")+"><td>"+x[0]+"</td><td>"+x[1]+"</td><td>"+x[2]+"</td><td>"+x[3]+"</td></tr>"; }); h+="</tbody></table>"; allEl.innerHTML=h; }
sel.addEventListener("change",calc); calc();
})();
</script>
@endsection
