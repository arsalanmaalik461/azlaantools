@extends('layouts.app')
@section('title', 'Bra Size Converter — Free Online Tool')
@section('meta_description', 'Enter band and cup size and instantly convert to UK, US, EU, and international sizes.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Bra Size Converter</h1>
            <p class="lead small text-muted">Convert band and cup size to UK, US, EU, and international sizes.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="bandSel">Band Size (UK/US)</label><select class="form-select" id="bandSel"><option>30</option><option>32</option><option selected>34</option><option>36</option><option>38</option><option>40</option><option>42</option><option>44</option></select></div>
                <div class="col-md-6"><label class="form-label" for="cupSel">Cup (UK)</label><select class="form-select" id="cupSel"><option>A</option><option>B</option><option selected>C</option><option>D</option><option>DD</option><option>E</option><option>F</option><option>FF</option><option>G</option></select></div>
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
                <li>Choose From and To units — the result updates live.</li>
                <li>Also see the converted values in all units below.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Fit varies across brands and styles — this chart is a general conversion, also try sister sizes.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var bEl=document.getElementById("bandSel"); var cEl=document.getElementById("cupSel"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
var euBand={"30":65,"32":70,"34":75,"36":80,"38":85,"40":90,"42":95,"44":100};
var cupMap={"A":["A","A"],"B":["B","B"],"C":["C","C"],"D":["D","D"],"DD":["E","DD"],"E":["F","E"],"F":["G","F"],"FF":["H","FF"],"G":["I","G"]};
function calc(){ var band=bEl.value; var cup=cEl.value; var eu=euBand[band]; var m=cupMap[cup]; box.textContent="UK "+band+cup+" = US "+band+m[1]+" = EU "+eu+m[0]; allEl.innerHTML="<div>UK: <strong>"+band+cup+"</strong></div><div>US: <strong>"+band+m[1]+"</strong></div><div>EU: <strong>"+eu+m[0]+"</strong></div><div>France/Spain band: <strong>"+(eu+15)+m[0]+"</strong> | Australia band: <strong>"+(parseInt(band,10)-22)+m[1]+"</strong></div>"; }
bEl.addEventListener("change",calc); cEl.addEventListener("change",calc); calc();
})();
</script>
@endsection
