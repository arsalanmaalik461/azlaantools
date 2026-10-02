@extends('layouts.app')

@section('title', 'Paper Size Converter — Free Online Tool')
@section('meta_description', 'Select a paper size like A4 or Legal and instantly see its dimensions in mm, cm and inches.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Paper Size Converter</h1>
            <p class="lead small text-muted mb-4">Select a paper size like A4 or Legal and see its mm, cm and inch dimensions instantly.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label for="paperSel" class="form-label fw-semibold">Select Paper Size</label><select class="form-select" id="paperSel"></select></div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="paperOut">—</div>
                        <div class="small" id="paperDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the box above or select an option.</li>
                        <li>The result updates live instantly — no button needed.</li>
                        <li>Change the value or unit and the new result shows automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">In the A series each size is half of the previous one — A5 is half of A4 and A3 is double. Letter and Legal are US sizes that differ from A4, so confirm the size before printing.</p>
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
    var sizes = {
        "A0": [841, 1189], "A1": [594, 841], "A2": [420, 594], "A3": [297, 420], "A4": [210, 297],
        "A5": [148, 210], "A6": [105, 148], "A7": [74, 105], "A8": [52, 74],
        "B4": [250, 353], "B5": [176, 250], "B6": [125, 176],
        "Letter": [216, 279], "Legal": [216, 356], "Tabloid": [279, 432], "Executive": [184, 267]
    };
    var sel = document.getElementById("paperSel");
    Object.keys(sizes).forEach(function (k) {
        var o = document.createElement("option"); o.value = k; o.textContent = k; sel.appendChild(o);
    });
    sel.value = "A4";
    function calc() {
        var s = sizes[sel.value];
        var w = s[0], h = s[1];
        document.getElementById("paperOut").textContent = sel.value + ": " + w + " x " + h + " mm";
        document.getElementById("paperDetail").textContent =
            "Centimetre: " + (w / 10).toFixed(1) + " x " + (h / 10).toFixed(1) + " cm — Inch: " +
            (w / 25.4).toFixed(2) + " x " + (h / 25.4).toFixed(2) + " in — In landscape, width and height are swapped.";
    }
    sel.addEventListener("change", calc);
    calc();
})();
</script>
@endsection
