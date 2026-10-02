@extends('layouts.app')

@section('title', 'Curtain Size Calculator — Free Online Tool')
@section('meta_description', 'Calculate curtain width and drop needed for any window with fullness factor.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Curtain Size Calculator</h1>
                    <p class="lead small text-muted">Measure your window, choose the fullness (gather) you like, and get the total curtain width, finished drop, number of standard panels and fabric length to buy.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="cuW">Window / rod width (cm)</label><input type="number" class="form-control" id="cuW" value="180" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="cuH">Desired curtain drop (cm)</label><input type="number" class="form-control" id="cuH" value="220" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="cuFull">Fullness factor</label><select class="form-select" id="cuFull"><option value="1.5">1.5x — light gather</option><option value="2" selected>2x — standard full look</option><option value="2.5">2.5x — rich pleats</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="cuPanelW">Standard panel width (cm)</label><input type="number" class="form-control" id="cuPanelW" value="140" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="cuHem">Hem and heading allowance (cm)</label><input type="number" class="form-control" id="cuHem" value="25" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="cuOut">Enter window measurements to calculate curtain sizes.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Measure the rod or track width (extend 15 to 20 cm beyond the window on each side) and the drop you want.</li>
                        <li>Choose a fullness factor — 2x is the standard full look for most fabrics.</li>
                        <li>Buy the number of panels and fabric length shown, which already include hem allowance.</li>
                    </ol>
                    <p class="small text-muted mb-0">Finished curtain width equals rod width multiplied by the fullness factor, so the fabric still gathers nicely when closed. Fabric length to buy per panel equals the drop plus the hem allowance. Sheers usually use 2.5x to 3x fullness.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function calc() {
        var w = num("cuW"), h = num("cuH"), full = parseFloat(el("cuFull").value), panel = num("cuPanelW"), hem = num("cuHem");
        var out = el("cuOut");
        if (w <= 0 || h <= 0 || panel <= 0) { out.textContent = "Please enter window width, drop and panel width greater than zero."; return; }
        var needed = w * full;
        var panels = Math.ceil(needed / panel);
        var fabricLen = (h + hem) * panels;
        out.innerHTML = "<strong>Total finished curtain width needed:</strong> " + needed.toFixed(0) + " cm &nbsp; <strong>Panels:</strong> " + panels + " x " + panel.toFixed(0) + " cm wide<br><strong>Fabric length per panel:</strong> " + (h + hem).toFixed(0) + " cm (drop " + h.toFixed(0) + " + allowance " + hem.toFixed(0) + ") &nbsp; <strong>Total fabric length:</strong> " + (fabricLen / 100).toFixed(2) + " metres of " + panel.toFixed(0) + " cm wide fabric.";
    }
    ["cuW", "cuH", "cuFull", "cuPanelW", "cuHem"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
