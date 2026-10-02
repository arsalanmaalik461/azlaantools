@extends('layouts.app')
@section('title', 'Palindrome Checker — Free Online Tool')
@section('meta_description', 'Check if a word or sentence reads the same forward and backward')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Palindrome Checker</h1>
            <p class="lead small text-muted">Type any word or sentence to check whether it is a palindrome, reading the same forward and backward.</p>

                    <label class="form-label fw-semibold" for="paIn">Word or sentence</label>
                    <input type="text" id="paIn" class="form-control form-control-lg" placeholder="e.g. A man a plan a canal Panama">
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="form-check"><input type="checkbox" id="paIgnore" class="form-check-input" checked><label class="form-check-label" for="paIgnore">Ignore spaces and punctuation</label></div>
                        <div class="form-check"><input type="checkbox" id="paCase" class="form-check-input" checked><label class="form-check-label" for="paCase">Ignore upper and lower case</label></div>
                    </div>
                    <button type="button" id="paGo" class="btn btn-primary btn-lg w-100 mt-3">Check Palindrome</button>
                    <div class="text-center mt-3"><div class="fs-4 fw-bold" id="paResult">Type something to begin</div><p class="small text-muted mb-0" id="paDetail"></p></div>

            <div id="paMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type a word or a full sentence.</li>
                    <li>Keep the ignore options on for classic sentence palindromes.</li>
                    <li>Click Check to see the verdict and the reversed text.</li>
            </ol>
            <p class="small text-muted mb-0">Famous examples: Madam, Racecar, and the sentence Never odd or even. Numbers work too, like 12321.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("paMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    document.getElementById("paGo").addEventListener("click", function () {
        var raw = document.getElementById("paIn").value;
        var s = raw;
        if (document.getElementById("paCase").checked) s = s.toLowerCase();
        if (document.getElementById("paIgnore").checked) s = s.replace(/[^a-z0-9\u0600-\u06FF]/gi, document.getElementById("paCase").checked ? "" : "");
        if (document.getElementById("paIgnore").checked && !document.getElementById("paCase").checked) s = raw.replace(/[^a-zA-Z0-9\u0600-\u06FF]/g, "");
        var rev = s.split("").reverse().join("");
        var res = document.getElementById("paResult");
        if (!s) { res.textContent = "Please type something first."; res.className = "fs-4 fw-bold"; document.getElementById("paDetail").textContent = ""; return; }
        if (s === rev) { res.textContent = "Yes, it is a palindrome."; res.className = "fs-4 fw-bold text-success"; }
        else { res.textContent = "No, it is not a palindrome."; res.className = "fs-4 fw-bold text-danger"; }
        document.getElementById("paDetail").textContent = "Checked text: " + s + "  ·  Reversed: " + rev + "  ·  Length: " + s.length + " characters.";
    });

})();
</script>
@endsection
