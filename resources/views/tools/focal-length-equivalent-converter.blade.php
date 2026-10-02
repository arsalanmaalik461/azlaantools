@extends('layouts.app')
@section('title', 'Focal Length Equivalent Converter — Free Online Tool')
@section('meta_description', 'Enter a lens focal length and sensor size to get the 35mm full frame equivalent focal length.')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Focal Length Equivalent Converter</h1>
            <p class="lead small text-muted">Convert a lens focal length to its 35mm full frame equivalent using the crop factor.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="focalVal">Actual Focal Length (mm)</label><input type="number" step="any" class="form-control" id="focalVal" value="35"></div>
                <div class="col-md-6"><label class="form-label" for="sensorSel">Sensor / Crop Factor</label><select class="form-select" id="sensorSel"><option value="1">Full Frame (1.0x)</option><option value="1.5" selected>APS-C Nikon/Sony (1.5x)</option><option value="1.6">APS-C Canon (1.6x)</option><option value="2">Micro Four Thirds (2.0x)</option><option value="2.7">1 inch sensor (2.7x)</option><option value="1.3">APS-H (1.3x)</option><option value="0.79">Medium Format Fuji GFX (0.79x)</option></select></div>
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
                <li>Choose the sensor — the result updates live.</li>
                <li>See the extra details below too.</li>
            </ol>
            <p class="small text-muted mb-0">Note: The equivalent is for field of view only — for depth of field and noise, the aperture is also affected by the crop factor.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){ 'use strict';
var vEl=document.getElementById("focalVal"); var sEl=document.getElementById("sensorSel"); var box=document.getElementById("resultBox"); var allEl=document.getElementById("allOut");
function fmt(n){ return parseFloat(n.toFixed(2)).toLocaleString("en-US",{maximumFractionDigits:2}); }
function calc(){ var f=parseFloat(vEl.value); var cf=parseFloat(sEl.value); if(isNaN(f)||f<=0){ box.textContent="Enter a valid focal length."; return; } var eq=f*cf; var fov=2*Math.atan(21.63/eq)*180/Math.PI; box.textContent=f+"mm on this sensor = "+fmt(eq)+"mm full frame equivalent"; allEl.innerHTML="<div>Full frame equivalent: <strong>"+fmt(eq)+" mm</strong></div><div>Diagonal field of view (approx): <strong>"+fmt(fov)+" degrees</strong></div><div>To get the same view on full frame you need a lens of: <strong>"+fmt(eq)+" mm</strong> | For a 50mm full frame style view on this sensor: <strong>"+fmt(50/cf)+" mm</strong></div>"; }
vEl.addEventListener("input",calc); sEl.addEventListener("change",calc); calc();
})();
</script>
@endsection
