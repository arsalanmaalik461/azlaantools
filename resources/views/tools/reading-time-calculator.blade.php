@extends('layouts.app')

@section('title', 'Reading Time Calculator — Free Online Tool')
@section('meta_description', 'Estimate how long any text or book will take to read at your reading speed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Reading Time Calculator</h1>
                    <p class="lead small text-muted">Paste text or enter a word count, set your reading speed, and get reading time, speaking time and the sessions needed at your daily reading habit.</p>
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label" for="rdText">Paste text (optional — or just enter a word count below)</label><textarea class="form-control" id="rdText" rows="4" placeholder="Paste the chapter, article or notes here"></textarea></div>
                        <div class="col-md-3"><label class="form-label" for="rdWords">Word count</label><input type="number" class="form-control" id="rdWords" value="10000" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="rdWpm">Reading speed (words per minute)</label><input type="number" class="form-control" id="rdWpm" value="200" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="rdDaily">Reading time per day (minutes)</label><input type="number" class="form-control" id="rdDaily" value="30" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="rdOut">Enter a word count to estimate reading time.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Paste text to count its words automatically, or type a word count directly (a typical novel page holds about 250 to 300 words).</li>
                        <li>Set your reading speed — average adults read 200 to 250 words per minute silently.</li>
                        <li>See the total reading time, speaking time, and how many days at your daily habit.</li>
                    </ol>
                    <p class="small text-muted mb-0">Speaking (reading aloud) averages about 130 to 150 words per minute. Dense study material is often read at 100 to 150 words per minute with notes, so lower the speed for textbooks.</p>
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
    function fmtMin(mins) { var h = Math.floor(mins / 60), m = Math.round(mins % 60); return (h > 0 ? h + " h " : "") + m + " min"; }
    function calc() {
        var text = el("rdText").value.trim();
        if (text) { var wc = text.split(/\s+/).filter(function (w) { return w.length > 0; }).length; el("rdWords").value = wc; }
        var words = parseFloat(el("rdWords").value), wpm = parseFloat(el("rdWpm").value), daily = parseFloat(el("rdDaily").value);
        var out = el("rdOut");
        if (isNaN(words) || words <= 0 || isNaN(wpm) || wpm <= 0) { out.textContent = "Please enter a word count and reading speed greater than zero."; return; }
        var readMin = words / wpm, speakMin = words / 140;
        var html = "<strong>Reading time:</strong> " + fmtMin(readMin) + " at " + wpm + " wpm &nbsp; <strong>Reading aloud:</strong> about " + fmtMin(speakMin) + "<br><strong>Length:</strong> " + words.toLocaleString("en-US") + " words, about " + (words / 275).toFixed(1) + " printed pages.";
        if (!isNaN(daily) && daily > 0) { html += " At " + daily + " minutes a day, finishing takes about <strong>" + Math.ceil(readMin / daily) + " days</strong>."; }
        out.innerHTML = html;
    }
    ["rdText", "rdWords", "rdWpm", "rdDaily"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
