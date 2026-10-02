@extends('layouts.app')

@section('title', 'Music Note Frequency Converter — Free Online Tool')
@section('meta_description', 'Enter a note name or frequency and instantly see the link between Hz, note and wavelength.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Music Note Frequency Converter</h1>
            <p class="lead small text-muted mb-4">Enter a note name or frequency and instantly see the link between Hz, note and wavelength.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4 mb-3"><label for="noteName" class="form-label fw-semibold">Note</label>
                            <select class="form-select" id="noteName">
                                <option value="0">C</option><option value="1">C sharp / D flat</option><option value="2">D</option><option value="3">D sharp / E flat</option><option value="4">E</option><option value="5">F</option><option value="6">F sharp / G flat</option><option value="7">G</option><option value="8">G sharp / A flat</option><option value="9" selected>A</option><option value="10">A sharp / B flat</option><option value="11">B</option>
                            </select></div>
                        <div class="col-md-4 mb-3"><label for="octave" class="form-label fw-semibold">Octave</label><input type="number" class="form-control" id="octave" value="4" step="1" min="0" max="8"></div>
                        <div class="col-md-4 mb-3"><label for="a4ref" class="form-label fw-semibold">A4 Reference (Hz)</label><input type="number" class="form-control" id="a4ref" value="440" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="freqIn" class="form-label fw-semibold">Or enter frequency (Hz)</label><input type="number" class="form-control" id="freqIn" placeholder="e.g. 261.63" step="any"></div>
                    </div>
                    <div class="alert alert-info text-center mb-0">
                        <div class="fs-5 fw-bold" id="noteOut">—</div>
                        <div class="small" id="noteDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live — you do not need to press any button.</li>
                        <li>Change a value or unit and the new result appears on its own.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Equal temperament formula: f = A4 x 2^((n - 69) / 12), where n is the MIDI note number. Wavelength is calculated with the speed of sound in air at 343 m/s.</p>
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
    var names = ["C", "C#", "D", "D#", "E", "F", "F#", "G", "G#", "A", "A#", "B"];
    var noteEl = document.getElementById("noteName"), octEl = document.getElementById("octave");
    var refEl = document.getElementById("a4ref"), freqEl = document.getElementById("freqIn");
    var outEl = document.getElementById("noteOut"), detEl = document.getElementById("noteDetail");
    function fromNote() {
        var ref = parseFloat(refEl.value), oct = parseInt(octEl.value, 10);
        if (isNaN(ref) || ref <= 0 || isNaN(oct)) { outEl.textContent = "—"; detEl.textContent = ""; return; }
        var midi = (oct + 1) * 12 + parseInt(noteEl.value, 10);
        var f = ref * Math.pow(2, (midi - 69) / 12);
        outEl.textContent = names[parseInt(noteEl.value, 10)] + oct + " = " + fmt(f) + " Hz";
        detEl.textContent = "MIDI note: " + midi + " — Wavelength (air): " + fmt(343 / f) + " m";
        freqEl.value = "";
    }
    function fromFreq() {
        var f = parseFloat(freqEl.value), ref = parseFloat(refEl.value);
        if (isNaN(f) || f <= 0 || isNaN(ref) || ref <= 0) { fromNote(); return; }
        var midi = Math.round(69 + 12 * Math.log2(f / ref));
        var exact = ref * Math.pow(2, (midi - 69) / 12);
        var cents = Math.round(1200 * Math.log2(f / exact));
        var nm = names[((midi % 12) + 12) % 12], oc = Math.floor(midi / 12) - 1;
        outEl.textContent = fmt(f) + " Hz — closest note: " + nm + oc;
        detEl.textContent = "Actual note frequency: " + fmt(exact) + " Hz — difference: " + cents + " cents — Wavelength: " + fmt(343 / f) + " m";
    }
    noteEl.addEventListener("change", fromNote);
    octEl.addEventListener("input", fromNote);
    refEl.addEventListener("input", function () { if (freqEl.value) { fromFreq(); } else { fromNote(); } });
    freqEl.addEventListener("input", fromFreq);
    fromNote();
})();
</script>
@endsection
