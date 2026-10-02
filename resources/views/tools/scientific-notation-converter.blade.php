@extends('layouts.app')

@section('title', 'Scientific Notation Converter — Free Online Tool')
@section('meta_description', 'Enter a large or small number and convert it to scientific notation, e notation and the actual number.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Scientific Notation Converter</h1>
            <p class="lead small text-muted mb-4">Enter a large or small number and convert it to scientific notation, e notation and the actual number.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Number to Scientific Notation</h2>
                    <div class="mb-3"><label for="stdIn" class="form-label">Standard Number</label><input type="text" class="form-control" id="stdIn" value="123456000"></div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="sciOut">—</div>
                        <div class="small" id="sciDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Scientific Notation to Number</h2>
                    <div class="row g-2">
                        <div class="col-6"><label for="mant" class="form-label">Mantissa</label><input type="number" class="form-control" id="mant" value="1.23456" step="any"></div>
                        <div class="col-6"><label for="expo" class="form-label">Exponent (x 10^)</label><input type="number" class="form-control" id="expo" value="8" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">Standard Number: <strong id="stdOut">—</strong></div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live instantly — no button press needed.</li>
                        <li>Change the value or unit and the new result shows automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">In scientific notation a number is written with a mantissa between 1 and 10 and a power of 10. In engineering notation the exponent is always a multiple of 3.</p>
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
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    function toSci() {
        var raw = document.getElementById("stdIn").value.trim();
        var o = document.getElementById("sciOut"), det = document.getElementById("sciDetail");
        var n = Number(raw);
        if (!raw || isNaN(n)) { o.textContent = "—"; det.textContent = "Enter a valid number."; return; }
        if (n === 0) { o.textContent = "0"; det.textContent = "E notation: 0e+0"; return; }
        var exp = Math.floor(Math.log10(Math.abs(n)));
        var mant = n / Math.pow(10, exp);
        var eExp = Math.floor(exp / 3) * 3;
        var eMant = n / Math.pow(10, eExp);
        o.textContent = "Scientific: " + fmt(mant) + " x 10^" + exp;
        det.textContent = "E notation: " + mant.toExponential(5) + " — Engineering notation: " + fmt(eMant) + " x 10^" + eExp;
    }
    function toStd() {
        var m = parseFloat(document.getElementById("mant").value);
        var e = parseInt(document.getElementById("expo").value, 10);
        var o = document.getElementById("stdOut");
        if (isNaN(m) || isNaN(e)) { o.textContent = "—"; return; }
        var n = m * Math.pow(10, e);
        o.textContent = isFinite(n) ? n.toLocaleString("en-US", { maximumFractionDigits: 10 }) : "Number is too large";
    }
    document.getElementById("stdIn").addEventListener("input", toSci);
    document.getElementById("mant").addEventListener("input", toStd);
    document.getElementById("expo").addEventListener("input", toStd);
    toSci(); toStd();
})();
</script>
@endsection
