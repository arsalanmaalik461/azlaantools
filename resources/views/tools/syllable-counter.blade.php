@extends('layouts.app')
@section('title', 'Syllable Counter — Free Online Tool')
@section('meta_description', 'Count syllables in words sentences and full paragraphs instantly')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Syllable Counter</h1>
            <p class="lead small text-muted">Count the syllables in a single word, a sentence or a whole paragraph, with a per word breakdown for poems and haiku.</p>

                    <label class="form-label fw-semibold" for="syIn">Your text</label>
                    <textarea id="syIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <button type="button" id="syGo" class="btn btn-primary btn-lg w-100 mt-3">Count Syllables</button>
                    <div class="row g-3 mt-1 text-center">
                        <div class="col-4"><div class="border rounded p-3"><div class="fs-3 fw-bold" id="syTotal">0</div><div class="small text-muted">Total syllables</div></div></div>
                        <div class="col-4"><div class="border rounded p-3"><div class="fs-3 fw-bold" id="syWords">0</div><div class="small text-muted">Words</div></div></div>
                        <div class="col-4"><div class="border rounded p-3"><div class="fs-3 fw-bold" id="syAvg">0</div><div class="small text-muted">Avg per word</div></div></div>
                    </div>
                    <h3 class="h6 mt-3">Per word breakdown</h3>
                    <div id="syList"><p class="text-muted small mb-0">No analysis yet. A haiku line should total 5, 7 or 5 syllables.</p></div>

            <div id="syMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type or paste a word, line or paragraph.</li>
                    <li>Click Count Syllables.</li>
                    <li>Check the total and the per word breakdown, perfect for haiku and poem meters.</li>
            </ol>
            <p class="small text-muted mb-0">Counting uses the standard vowel group heuristic: groups of vowels count once, a silent final e is removed, and words ending in consonant plus le gain one back. It is very accurate for common English but unusual names and loanwords can differ from a dictionary.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("syMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function countWord(w) {
        var word = w.toLowerCase().replace(/[^a-z]/g, "");
        if (!word) return 0;
        if (word.length <= 3) return 1;
        var clean = word.replace(/(?:[^laeiouy]e|ed|es)$/, "");
        var groups = clean.match(/[aeiouy]{1,2}/g);
        var n = groups ? groups.length : 0;
        if (/[^aeiouy]le$/.test(word)) n += 0;
        return Math.max(1, n);
    }
    document.getElementById("syGo").addEventListener("click", function () {
        var words = document.getElementById("syIn").value.trim().split(/\s+/).filter(Boolean);
        var total = 0, rows = [];
        words.forEach(function (w) { var c = countWord(w); total += c; rows.push(w + " (" + c + ")"); });
        document.getElementById("syTotal").textContent = total;
        document.getElementById("syWords").textContent = words.length;
        document.getElementById("syAvg").textContent = words.length ? (total / words.length).toFixed(2) : 0;
        var box = document.getElementById("syList");
        box.innerHTML = "";
        if (!rows.length) { box.innerHTML = "<p class=\"text-muted small mb-0\">Type some text first.</p>"; showMsg("No words to count.", false); return; }
        var p = document.createElement("p"); p.className = "small mb-0"; p.textContent = rows.join("  ·  ");
        box.appendChild(p);
        showMsg("Counted " + total + " syllables in " + words.length + " words.");
    });

})();
</script>
@endsection
