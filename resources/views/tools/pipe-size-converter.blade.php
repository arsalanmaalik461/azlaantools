@extends('layouts.app')

@section('title', 'Pipe Size Converter — Free Online Tool')
@section('meta_description', 'Enter a pipe size and instantly convert NPS inches to DN and actual mm diameter.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Pipe Size Converter</h1>
            <p class="lead small text-muted mb-4">Enter a pipe size and instantly convert NPS inches to DN and actual mm diameter.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label for="pipeSel" class="form-label fw-semibold">Select NPS Size</label><select class="form-select" id="pipeSel"></select></div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="pipeOut">—</div>
                        <div class="small" id="pipeDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the box above or select an option.</li>
                        <li>The result updates live right away — no button needed.</li>
                        <li>Change the value or unit and the new result shows automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">NPS (Nominal Pipe Size) is a name, not the actual diameter — for example, an NPS 1/2 pipe has an outside diameter of 21.3 mm, not 12.7 mm. DN (Diameter Nominal) is the metric name for the same size.</p>
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
    var pipes = [
        ["1/8", 6, 10.3], ["1/4", 8, 13.7], ["3/8", 10, 17.1], ["1/2", 15, 21.3], ["3/4", 20, 26.7],
        ["1", 25, 33.4], ["1 1/4", 32, 42.2], ["1 1/2", 40, 48.3], ["2", 50, 60.3], ["2 1/2", 65, 73.0],
        ["3", 80, 88.9], ["3 1/2", 90, 101.6], ["4", 100, 114.3], ["5", 125, 141.3], ["6", 150, 168.3],
        ["8", 200, 219.1], ["10", 250, 273.0], ["12", 300, 323.8], ["14", 350, 355.6], ["16", 400, 406.4],
        ["18", 450, 457.0], ["20", 500, 508.0], ["24", 600, 609.6]
    ];
    var sel = document.getElementById("pipeSel");
    pipes.forEach(function (p, i) {
        var o = document.createElement("option"); o.value = i; o.textContent = "NPS " + p[0]; sel.appendChild(o);
    });
    sel.value = "3";
    function calc() {
        var p = pipes[parseInt(sel.value, 10)];
        document.getElementById("pipeOut").textContent = "NPS " + p[0] + " = DN " + p[1];
        document.getElementById("pipeDetail").textContent = "Actual outside diameter: " + p[2] + " mm (" + (p[2] / 25.4).toFixed(3) + " inch). Inside diameter depends on the pipe schedule (thickness).";
    }
    sel.addEventListener("change", calc);
    calc();
})();
</script>
@endsection
