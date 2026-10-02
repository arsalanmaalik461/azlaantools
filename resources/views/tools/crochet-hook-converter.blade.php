@extends('layouts.app')
@section('title', 'Crochet Hook Size Converter — Free Online Tool')
@section('meta_description', 'Enter a crochet hook size and convert mm to US letter and UK sizes instantly.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Crochet Hook Size Converter</h1>
            <p class="lead small text-muted">Convert crochet hook sizes between mm, US letter and UK sizes.</p>
            <div class="mb-3"><label class="form-label" for="hookSel">Hook Size (mm)</label><select class="form-select" id="hookSel"><option value="2.25">2.25 mm</option><option value="2.75">2.75 mm</option><option value="3.25">3.25 mm</option><option value="3.5">3.5 mm</option><option value="3.75">3.75 mm</option><option value="4">4.0 mm</option><option value="4.5">4.5 mm</option><option value="5" selected>5.0 mm</option><option value="5.5">5.5 mm</option><option value="6">6.0 mm</option><option value="6.5">6.5 mm</option><option value="8">8.0 mm</option><option value="9">9.0 mm</option><option value="10">10.0 mm</option></select></div>
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
            <p class="small text-muted mb-0">Note: Some brands differ slightly in US lettering — the mm printed on the hook is always final.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var sel=document.getElementById("hookSel"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
var data={"2.25":["B-1","13"],"2.75":["C-2","11"],"3.25":["D-3","10"],"3.5":["E-4","9"],"3.75":["F-5","8"],"4":["G-6","7"],"4.5":["7","6"],"5":["H-8","5"],"5.5":["I-9","4"],"6":["J-10","3"],"6.5":["K-10.5","2"],"8":["L-11","0"],"9":["M/N-13","00"],"10":["N/P-15","000"]};
function calc(){ var mm=sel.value; var d=data[mm]; box.textContent=mm+" mm = US "+d[0]+" = UK "+d[1]; allEl.innerHTML="<div>Metric: <strong>"+mm+" mm</strong></div><div>US: <strong>"+d[0]+"</strong></div><div>UK (old): <strong>"+d[1]+"</strong></div>"; }
sel.addEventListener("change",calc); calc();
})();
</script>
@endsection
