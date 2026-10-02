@extends('layouts.app')

@section('title', 'YouTube Tag Generator - Azlaan Tools')
@section('meta_description', 'Generate SEO tags and hashtags for your YouTube videos to rank higher, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">YouTube Tag Generator</h1>
            <p class="lead text-muted">Generate SEO tags and hashtags for your video — so your video ranks higher in search. Just enter the topic, tags are ready.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="topicInput" class="form-label fw-semibold">Video topic / title</label>
                        <input type="text" class="form-control" id="topicInput" placeholder="e.g. solar panel price in pakistan">
                        <div class="form-text">Write here what the video is about.</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="nicheSel" class="form-label fw-semibold">Niche</label>
                            <select class="form-select" id="nicheSel">
                                <option value="general">General</option>
                                <option value="tutorial">Tutorial / How-to</option>
                                <option value="vlog">Vlog</option>
                                <option value="tech">Tech / Review</option>
                                <option value="finance">Finance / Business</option>
                                <option value="islamic">Islamic</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="langSel" class="form-label fw-semibold">Language</label>
                            <select class="form-select" id="langSel">
                                <option value="en">English</option>
                                <option value="ur">Urdu mix</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Tags</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Tags (<span id="tagCount">0</span> tags, <span id="charCount">0</span>/500 chars)</h5>
                            <button type="button" class="btn btn-sm btn-outline-success" id="copyBtn">Copy All</button>
                        </div>
                        <textarea class="form-control mb-3" id="tagsOut" rows="6" readonly></textarea>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Hashtags</h5>
                            <button type="button" class="btn btn-sm btn-outline-success" id="copyHashBtn">Copy</button>
                        </div>
                        <input type="text" class="form-control" id="hashOut" readonly>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the video topic or title and choose a niche.</li>
                <li>Click "Generate Tags".</li>
                <li>Copy the tags and paste them in the YouTube Studio tag box (there is a 500-character limit).</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var topicInput = document.getElementById('topicInput');
    var nicheSel = document.getElementById('nicheSel');
    var langSel = document.getElementById('langSel');

    var SUFFIXES = ['2026', 'full video', 'in urdu', 'in hindi', 'how to', 'tutorial', 'explained', 'for beginners', 'step by step', 'new video', 'latest', 'tips and tricks', 'complete guide', 'review', 'price'];
    var NICHE_EXTRA = {
        general: [],
        tutorial: ['how to do', 'learn', 'course', 'full course', 'easy method'],
        vlog: ['daily vlog', 'day in my life', 'vlog 2026', 'pakistan vlog'],
        tech: ['unboxing', 'honest review', 'pros and cons', 'vs comparison', 'specs'],
        finance: ['passive income', 'money', 'business ideas', 'earning', 'investment'],
        islamic: ['islamic video', 'religion', 'islam', 'lecture', 'quran lesson']
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function copyText(el, btn) {
        el.select();
        try { document.execCommand('copy'); } catch (e) {}
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(el.value || el.textContent).then(function () {
                var old = btn.textContent; btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = old; }, 1500);
            });
        } else {
            var old = btn.textContent; btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = old; }, 1500);
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var topic = topicInput.value.trim();
        if (!topic) { showError('Please enter the video topic first.'); return; }
        var niche = nicheSel.value;
        var lang = langSel.value;
        topic = topic.replace(/\s+/g, ' ');
        var words = topic.split(' ');

        var set = {};
        var tags = [];
        function add(t) {
            t = t.trim().toLowerCase();
            if (t && t.length > 1 && t.length <= 60 && !set[t]) { set[t] = 1; tags.push(t); }
        }

        add(topic);
        if (words.length > 1) { add(words.join(' ')); }
        for (var i = 0; i < words.length; i++) { add(words[i]); }

        var pools = SUFFIXES.concat(NICHE_EXTRA[niche] || []);
        for (var s = 0; s < pools.length; s++) {
            add(topic + ' ' + pools[s]);
        }
        if (lang === 'ur') {
            var ur = ['how to guide', 'full information', 'explained simply', 'detailed explanation', 'real price', 'price in pakistan'];
            for (var u = 0; u < ur.length; u++) { add(topic + ' ' + ur[u]); }
        }
        // word-order variations for long-tail
        if (words.length > 1) {
            add(words.slice().reverse().join(' '));
            add('best ' + topic);
            add(topic + ' tips');
        }

        // Trim to YouTube 500-char limit
        var finalTags = [];
        var total = 0;
        for (var t2 = 0; t2 < tags.length; t2++) {
            var len = tags[t2].length + (finalTags.length ? 2 : 0);
            if (total + len > 500) { break; }
            finalTags.push(tags[t2]);
            total += len;
        }

        var tagsOut = document.getElementById('tagsOut');
        tagsOut.value = finalTags.join(', ');
        document.getElementById('tagCount').textContent = finalTags.length;
        document.getElementById('charCount').textContent = total;

        // hashtags from top words
        var hs = [];
        var seenH = {};
        function addH(w) {
            w = w.replace(/[^a-z0-9]/gi, '');
            if (w.length > 2 && !seenH[w.toLowerCase()] && hs.length < 8) {
                seenH[w.toLowerCase()] = 1; hs.push('#' + w);
            }
        }
        for (var w2 = 0; w2 < words.length; w2++) { addH(words[w2]); }
        addH(niche === 'general' ? 'video' : niche);
        document.getElementById('hashOut').value = hs.join(' ');

        results.classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        copyText(document.getElementById('tagsOut'), this);
    });
    document.getElementById('copyHashBtn').addEventListener('click', function () {
        copyText(document.getElementById('hashOut'), this);
    });
})();
</script>
@endsection
