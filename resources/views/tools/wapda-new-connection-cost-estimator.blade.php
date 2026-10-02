@extends('layouts.app')

@section('title', 'WAPDA New Connection Cost Estimator — Free Online Tool')
@section('meta_description', 'Enter demand notice, meter, cable and service line costs to get a total estimate for your new connection.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">WAPDA New Connection Cost Estimator</h1>
                    <p class="lead small text-muted">Enter all costs of a new electricity connection in one worksheet — demand notice, security deposit, meter, cable and service line — and get the total estimate.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="wnLoad">Sanctioned load (kW)</label><input type="number" class="form-control" id="wnLoad" value="5" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnSecRate">Security deposit per kW (Rs, editable)</label><input type="number" class="form-control" id="wnSecRate" value="2000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnDemand">Demand notice / application fee (Rs, editable)</label><input type="number" class="form-control" id="wnDemand" value="1500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnMeter">Meter cost (Rs, editable — single/three phase)</label><input type="number" class="form-control" id="wnMeter" value="7500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnCableLen">Service cable length (feet)</label><input type="number" class="form-control" id="wnCableLen" value="40" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnCableRate">Cable rate per foot (Rs, editable)</label><input type="number" class="form-control" id="wnCableRate" value="180" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnService">Service line / pole share charges (Rs, editable)</label><input type="number" class="form-control" id="wnService" value="5000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnWiring">Wiring test report / inspection (Rs, editable)</label><input type="number" class="form-control" id="wnWiring" value="3000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="wnMisc">Miscellaneous (Rs, editable)</label><input type="number" class="form-control" id="wnMisc" value="2000" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="wnOut">Enter the load and costs — the total estimate will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter your sanctioned load in kW — the security deposit will be calculated automatically from it.</li>
                        <li>Adjust the demand notice, meter, cable (length x rate), service line and inspection charges according to your DISCO estimate.</li>
                        <li>See the total estimate below with a breakdown of each item.</li>
                    </ol>
                    <p class="small text-muted mb-0">This is an itemised worksheet, not a live official fee lookup: the real demand notice is issued by your DISCO (LESCO, IESCO, MEPCO etc.) after a survey and based on your load, and in remote areas the service line cost can be much higher. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup.</p>
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
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) || v < 0 ? 0 : v; }
    function money(n) { return "Rs " + Math.round(n).toLocaleString("en-PK"); }
    function calc() {
        var security = num("wnLoad") * num("wnSecRate");
        var cable = num("wnCableLen") * num("wnCableRate");
        var total = security + num("wnDemand") + num("wnMeter") + cable + num("wnService") + num("wnWiring") + num("wnMisc");
        el("wnOut").innerHTML = "<strong>New connection total estimate: " + money(total) + "</strong><br>Security deposit (" + num("wnLoad") + " kW): " + money(security) + " &nbsp; Demand notice: " + money(num("wnDemand")) + " &nbsp; Meter: " + money(num("wnMeter")) + " &nbsp; Cable: " + money(cable) + " &nbsp; Service line: " + money(num("wnService")) + " &nbsp; Wiring/inspection: " + money(num("wnWiring")) + " &nbsp; Misc: " + money(num("wnMisc"));
    }
    ["wnLoad", "wnSecRate", "wnDemand", "wnMeter", "wnCableLen", "wnCableRate", "wnService", "wnWiring", "wnMisc"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
