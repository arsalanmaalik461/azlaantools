@extends('layouts.app')
@section('title', 'Reverse Word Order — Free Online Tool')
@section('meta_description', 'Reverse the order of words in a sentence without changing letters')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Reverse Word Order</h1>
            <p class="lead small text-muted">Reverse the order of the words in any sentence or paragraph while every word itself stays spelled exactly the same.</p>

                    <label class="form-label fw-semibold" for="woIn">Your text</label>
                    <textarea id="woIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="form-check mt-3"><input type="checkbox" id="woLines" class="form-check-input" checked><label class="form-check-label" for="woLines">Reverse each line separately</label></div>
                    <button type="button" id="woGo" class="btn btn-primary btn-lg w-100 mt-3">Reverse Word Order</button>
                    <label class="form-label mt-3" for="woOut">Result</label>
                    <textarea id="woOut" class="form-control" rows="7" readonly></textarea>
                    <button type="button" id="woCopy" class="btn btn-outline-secondary mt-2">Copy Result</button>

            <div id="woMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste a sentence or paragraph.</li>
                    <li>Choose whether each line reverses separately or the whole text flips as one block.</li>
                    <li>Click the button and copy the result.</li>
            </ol>
            <p class="small text-muted mb-0">Unlike a character reverser, the letters inside each word never change, so every word stays readable, only its position moves.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("woMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    document.getElementById("woGo").addEventListener("click", function () {
        var t = document.getElementById("woIn").value;
        var out;
        if (document.getElementById("woLines").checked) {
            out = t.split("\n").map(function (line) { return line.trim().split(/\s+/).filter(Boolean).reverse().join(" "); }).join("\n");
        } else {
            out = t.split(/\s+/).filter(Boolean).reverse().join(" ");
        }
        document.getElementById("woOut").value = out;
        showMsg("Word order reversed.");
    });
    document.getElementById("woCopy").addEventListener("click", function () { copyText(document.getElementById("woOut").value, "Result copied."); });

})();
</script>
@endsection
