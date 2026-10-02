@extends('layouts.app')

@section('title', 'Word Counter - Azlaan Tools')
@section('meta_description', 'Free online word counter. Count words, characters, sentences and paragraphs live, check reading and speaking time, and see your top keywords.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Word Counter</h1>
            <p class="lead text-muted">Type or paste your text below and get live word, character and sentence counts, reading time, and your most-used keywords.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <textarea class="form-control" id="textInput" rows="10" placeholder="Start typing or paste your text here..."></textarea>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary" id="pasteBtn">Paste</button>
                        <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
                    </div>

                    <div class="row g-3 text-center mt-2">
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statWords">0</div><div class="text-muted small">Words</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statChars">0</div><div class="text-muted small">Characters</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statCharsNoSpace">0</div><div class="text-muted small">Characters (no spaces)</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statSentences">0</div><div class="text-muted small">Sentences</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statParagraphs">0</div><div class="text-muted small">Paragraphs</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statReading">0 min</div><div class="text-muted small">Reading Time</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statSpeaking">0 min</div><div class="text-muted small">Speaking Time</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="statLongest">-</div><div class="text-muted small">Longest Word</div></div></div>
                    </div>

                    <h3 class="h5 mt-4">Top Keywords</h3>
                    <div id="keywordList">
                        <p class="text-muted small mb-0">Start typing to see your top 10 most-used words.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type directly in the box, or click <strong>Paste</strong> to insert text from your clipboard.</li>
                <li>Counts update live as you type: words, characters, sentences and paragraphs.</li>
                <li>Check the estimated reading time (about 200 words per minute) and speaking time.</li>
                <li>Scroll to Top Keywords to see your 10 most frequent words, and click <strong>Clear</strong> to start over.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('textInput');

    function formatTime(minutes) {
        if (minutes <= 0) return '0 min';
        if (minutes < 1) return Math.ceil(minutes * 60) + ' sec';
        return minutes.toFixed(1).replace(/\.0$/, '') + ' min';
    }

    function update() {
        var text = input.value;
        var trimmed = text.trim();
        var words = trimmed ? trimmed.split(/\s+/).filter(Boolean) : [];
        var wordCount = words.length;
        var chars = text.length;
        var charsNoSpace = text.replace(/\s/g, '').length;
        var sentences = 0;
        if (trimmed) {
            var matches = trimmed.match(/[^.!?]+[.!?]+/g);
            sentences = matches ? matches.length : 1;
        }
        var paragraphs = trimmed ? trimmed.split(/\n\s*\n/).filter(function (p) { return p.trim().length > 0; }).length : 0;
        if (trimmed && paragraphs === 0) paragraphs = 1;

        var longest = '-';
        var freq = {};
        words.forEach(function (w) {
            if (w.length > longest.length || longest === '-') {
                if (longest === '-' || w.length > longest.length) longest = w;
            }
            var clean = w.toLowerCase().replace(/[^a-z0-9\u00C0-\u024F\u0600-\u06FF'-]/g, '');
            if (clean.length > 3) { freq[clean] = (freq[clean] || 0) + 1; }
        });
        if (!wordCount) longest = '-';

        document.getElementById('statWords').textContent = wordCount.toLocaleString();
        document.getElementById('statChars').textContent = chars.toLocaleString();
        document.getElementById('statCharsNoSpace').textContent = charsNoSpace.toLocaleString();
        document.getElementById('statSentences').textContent = sentences.toLocaleString();
        document.getElementById('statParagraphs').textContent = paragraphs.toLocaleString();
        document.getElementById('statReading').textContent = formatTime(wordCount / 200);
        document.getElementById('statSpeaking').textContent = formatTime(wordCount / 130);
        document.getElementById('statLongest').textContent = longest;

        var box = document.getElementById('keywordList');
        var entries = Object.keys(freq).map(function (k) { return { word: k, count: freq[k] }; });
        entries.sort(function (a, b) { return b.count - a.count || a.word.localeCompare(b.word); });
        entries = entries.slice(0, 10);
        box.innerHTML = '';
        if (!entries.length) {
            box.innerHTML = '<p class="text-muted small mb-0">No keywords yet. Words longer than 3 characters are counted here.</p>';
            return;
        }
        var list = document.createElement('ol');
        list.className = 'mb-0';
        entries.forEach(function (e) {
            var li = document.createElement('li');
            li.textContent = e.word + ' — ' + e.count + ' time' + (e.count === 1 ? '' : 's') + (wordCount ? ' (' + ((e.count / wordCount) * 100).toFixed(1) + '%)' : '');
            list.appendChild(li);
        });
        box.appendChild(list);
    }

    input.addEventListener('input', update);
    document.getElementById('clearBtn').addEventListener('click', function () { input.value = ''; update(); input.focus(); });
    document.getElementById('pasteBtn').addEventListener('click', function () {
        if (navigator.clipboard && navigator.clipboard.readText) {
            navigator.clipboard.readText().then(function (t) { input.value += t; update(); }).catch(function () { input.focus(); });
        } else { input.focus(); }
    });
    update();
})();
</script>
@endsection
