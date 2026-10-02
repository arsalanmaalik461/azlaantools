@extends('layouts.app')

@section('title', 'Number Base Converter — Free Online Tool')
@section('meta_description', 'Enter a number and instantly convert it to binary, decimal, hexadecimal and octal.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Number Base Converter</h1>
            <p class="lead small text-muted mb-4">Enter a number and instantly convert it to binary, decimal, hexadecimal and octal.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-8 mb-3"><label for="baseVal" class="form-label fw-semibold">Number</label><input type="text" class="form-control" id="baseVal" value="255"></div>
                        <div class="col-md-4 mb-3"><label for="baseFrom" class="form-label fw-semibold">Input Base</label>
                            <select class="form-select" id="baseFrom"><option value="2">Binary (2)</option><option value="8">Octal (8)</option><option value="10" selected>Decimal (10)</option><option value="16">Hexadecimal (16)</option></select></div>
                    </div>
                    <div class="alert alert-danger d-none" id="baseErr"></div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between"><span>Binary (Base 2)</span><strong id="outBin">—</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Octal (Base 8)</span><strong id="outOct">—</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Decimal (Base 10)</span><strong id="outDec">—</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Hexadecimal (Base 16)</span><strong id="outHex">—</strong></li>
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select the option.</li>
                        <li>The result updates live at once — no button to press.</li>
                        <li>Change the value or the base and the new result will appear automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Only positive integers are supported. In hexadecimal, the letters A to F are used. Entering wrong digits (for example 2 in binary) will show an error.</p>
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
    var valEl = document.getElementById("baseVal"), fromEl = document.getElementById("baseFrom");
    var errEl = document.getElementById("baseErr");
    function calc() {
        var raw = valEl.value.trim(), base = parseInt(fromEl.value, 10);
        var outs = ["outBin", "outOct", "outDec", "outHex"];
        if (!raw) { outs.forEach(function (id) { document.getElementById(id).textContent = "—"; }); errEl.classList.add("d-none"); return; }
        var n = parseInt(raw, base);
        if (isNaN(n) || n.toString(base).toUpperCase() !== raw.replace(/^0+(?=.)/, "").toUpperCase()) {
            errEl.textContent = "This number is not valid for base " + base + ". Please check the digits.";
            errEl.classList.remove("d-none");
            outs.forEach(function (id) { document.getElementById(id).textContent = "—"; });
            return;
        }
        errEl.classList.add("d-none");
        document.getElementById("outBin").textContent = n.toString(2);
        document.getElementById("outOct").textContent = n.toString(8);
        document.getElementById("outDec").textContent = n.toString(10);
        document.getElementById("outHex").textContent = n.toString(16).toUpperCase();
    }
    valEl.addEventListener("input", calc);
    fromEl.addEventListener("change", calc);
    calc();
})();
</script>
@endsection
