@extends('layouts.app')
@section('title', 'Anagram Checker — Free Online Tool')
@section('meta_description', 'Check if two words or phrases use exactly the same letters')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Anagram Checker</h1>
            <p class="lead small text-muted">Enter two words or phrases to check whether they are anagrams, meaning they use exactly the same letters.</p>

                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="agA">First word or phrase</label><input type="text" id="agA" class="form-control" placeholder="e.g. listen"></div>
                        <div class="col-md-6"><label class="form-label" for="agB">Second word or phrase</label><input type="text" id="agB" class="form-control" placeholder="e.g. silent"></div>
                    </div>
                    <div class="form-check mt-3"><input type="checkbox" id="agStrict" class="form-check-input"><label class="form-check-label" for="agStrict">Count spaces and punctuation too</label></div>
                    <button type="button" id="agGo" class="btn btn-primary btn-lg w-100 mt-3">Check Anagram</button>
                    <div class="text-center mt-3"><div class="fs-4 fw-bold" id="agResult">Enter two phrases to begin</div><p class="small text-muted mb-0" id="agDetail"></p></div>

            <div id="agMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type the first word or phrase.</li>
                    <li>Type the second one and click Check Anagram.</li>
                    <li>Read the verdict along with the sorted letters of both sides.</li>
            </ol>
            <p class="small text-muted mb-0">By default spaces and punctuation are ignored and case does not matter, which is how most word games judge anagrams.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("agMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function norm(v) {
        var s = String(v).toLowerCase();
        if (!document.getElementById("agStrict").checked) s = s.replace(/[^a-z0-9\u0600-\u06FF]/g, "");
        return s;
    }
    function sorted(v) { return norm(v).split("").sort().join(""); }
    document.getElementById("agGo").addEventListener("click", function () {
        var a = document.getElementById("agA").value, b = document.getElementById("agB").value;
        var res = document.getElementById("agResult"), det = document.getElementById("agDetail");
        if (!norm(a) || !norm(b)) { res.textContent = "Please fill in both phrases."; det.textContent = ""; showMsg("Both fields are needed.", false); return; }
        var sa = sorted(a), sb = sorted(b), same = norm(a) === norm(b);
        if (sa === sb && !same) { res.textContent = "Yes, they are anagrams."; res.className = "fs-4 fw-bold text-success"; }
        else if (same) { res.textContent = "They are the same phrase, so trivially anagrams."; res.className = "fs-4 fw-bold text-success"; }
        else { res.textContent = "No, they are not anagrams."; res.className = "fs-4 fw-bold text-danger"; }
        det.textContent = "Sorted letters: " + sa + "  vs  " + sb + "  ·  Letter counts: " + sa.length + " vs " + sb.length + ".";
    });

})();
</script>
@endsection
