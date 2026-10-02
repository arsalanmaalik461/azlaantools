@extends('layouts.app')

@section('title', 'YouTube Title Generator - Azlaan Tools')
@section('meta_description', 'Create clickable YouTube titles from your video topic, free online — for search and CTR both.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">YouTube Title Generator</h1>
            <p class="lead text-muted">Enter your video topic — get 20 clickable, SEO-friendly titles instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="topicInput" class="form-label fw-semibold">Video Topic</label>
                        <input type="text" class="form-control" id="topicInput" placeholder="e.g. solar panel prices in Pakistan">
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="toneSel" class="form-label fw-semibold">Title Style</label>
                            <select class="form-select" id="toneSel">
                                <option value="howto">How-To / Tutorial</option>
                                <option value="list">List / Top 5</option>
                                <option value="question">Question Hook</option>
                                <option value="shocking">Curiosity / Shocking</option>
                                <option value="review">Review / Comparison</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="langSel" class="form-label fw-semibold">Language</label>
                            <select class="form-select" id="langSel">
                                <option value="en">English</option>
                                <option value="ru">Urdu</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="kwInput" class="form-label fw-semibold">Keyword (optional)</label>
                            <input type="text" class="form-control" id="kwInput" placeholder="e.g. 2026">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate Titles</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="fw-semibold mb-0">20 Title Ideas</p>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyAll">Copy All</button>
                        </div>
                        <div class="list-group" id="titleList"></div>
                        <small class="text-muted d-block mt-2">Each title has a copy button. For best CTR, pick a title shorter than 60 characters.</small>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your video topic and choose style + language.</li>
                <li>Click Generate Titles — you will get 20 ideas.</li>
                <li>Copy your favorite title and paste it on YouTube.</li>
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
    var topicInput = document.getElementById('topicInput');
    var toneSel = document.getElementById('toneSel');
    var langSel = document.getElementById('langSel');
    var kwInput = document.getElementById('kwInput');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var titleList = document.getElementById('titleList');
    var copyAll = document.getElementById('copyAll');

    var banks = {
        en: {
            howto: ['How to {T} in 2026 (Step by Step)', 'How I {T} Without Any Experience', '{T}: Complete Beginner Guide', 'How to {T} Fast — Proven Method', '{T} Made Easy for Beginners'],
            list: ['Top 5 {T} Tips You Must Know', '7 {T} Mistakes to Avoid', '10 Best {T} Ideas for 2026', '5 {T} Secrets Nobody Tells You', 'Top 10 {T} in 2026 (Ranked)'],
            question: ['Is {T} Worth It in 2026?', 'Why Does Everyone Talk About {T}?', 'Can You Really {T}?', 'What If {T} Changed Everything?', 'Should You Try {T} in 2026?'],
            shocking: ['I Tried {T} for 30 Days — Shocking Result!', 'Nobody Tells You This About {T}', 'The Dark Truth About {T}', 'Stop Doing {T} Wrong!', '{T} Exposed: What They Hide'],
            review: ['{T}: Honest Review After 1 Month', '{T} vs {Alt}: Which Is Better?', 'I Tested {T} So You Don\'t Have To', '{T} Full Review — Pros and Cons', 'Best {T} in 2026: Honest Comparison']
        },
        ru: {
            howto: ['How to Do {T} — Step by Step Guide', 'Easy Way to Learn {T}', '{T} for Beginners — Full Tutorial', 'Learn {T} From Home', 'Complete {T} Method (2026)'],
            list: ['5 Best {T} Tips', '7 {T} Mistakes to Avoid', '10 Best {T} Ideas in 2026', '5 {T} Secrets You Did Not Know', 'Top 10 {T} — 2026 Ranking'],
            question: ['Is {T} Useful in 2026?', 'Why Is Everyone Talking About {T}?', 'Can You Really Do {T}?', 'Should You Try {T} or Not?', 'Should You Do {T} in 2026?'],
            shocking: ['I Did {T} for 30 Days — Surprising Result!', 'The Hidden Truth About {T}', 'Nobody Will Tell You This About {T}', 'Stop Doing {T} the Wrong Way!', 'The Reality of {T} — Must Watch'],
            review: ['Honest Review of {T} — After 1 Month', '{T} vs {Alt}: Which Is Better?', 'I Tested {T} So You Do Not Have To', 'Full {T} Review — Pros and Cons', 'Best {T} of 2026: Honest Comparison']
        }
    };
    var altSuggestions = { en: 'Alternatives', ru: 'Other options' };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function titleCase(s) {
        return s.replace(/\w\S*/g, function (w) { return w.charAt(0).toUpperCase() + w.substr(1).toLowerCase(); });
    }
    function copyText(text, btn) {
        function done() {
            if (btn) {
                var old = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = old; }, 1500);
            }
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done);
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) { /* noop */ }
            document.body.removeChild(ta);
            done();
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var topic = topicInput.value.trim();
        if (!topic) { showError('Please enter your video topic.'); return; }
        var lang = langSel.value;
        var tone = toneSel.value;
        var kw = kwInput.value.trim();
        var T = lang === 'en' ? titleCase(topic) : topic;
        if (kw) { T = T + ' ' + kw; }
        var bank = banks[lang][tone];
        var others = [];
        var langs = lang === 'en' ? 'en' : 'ru';
        var keys = Object.keys(banks[langs]);
        for (var k = 0; k < keys.length; k++) {
            if (keys[k] === tone) continue;
            for (var m = 0; m < banks[langs][keys[k]].length; m++) {
                others.push(banks[langs][keys[k]][m]);
            }
        }
        titleList.innerHTML = '';
        var made = [];
        function addFrom(tpl) {
            var t = tpl.replace(/\{T\}/g, T).replace(/\{Alt\}/g, altSuggestions[lang]);
            if (t.length > 100) { t = t.substring(0, 97) + '...'; }
            made.push(t);
            var item = document.createElement('div');
            item.className = 'list-group-item d-flex justify-content-between align-items-center gap-2';
            var span = document.createElement('span');
            span.textContent = t;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary flex-shrink-0';
            btn.textContent = 'Copy';
            btn.addEventListener('click', function () { copyText(t, btn); });
            item.appendChild(span);
            item.appendChild(btn);
            titleList.appendChild(item);
        }
        var extra = banks[lang][tone].concat(others);
        var count = 0, idx = 0, seen = {};
        while (count < 20 && idx < extra.length * 2) {
            var tpl = extra[idx % extra.length];
            idx++;
            var candidate = tpl.replace(/\{T\}/g, T).replace(/\{Alt\}/g, altSuggestions[lang]);
            if (seen[candidate]) continue;
            seen[candidate] = true;
            addFrom(tpl);
            count++;
        }
        results.classList.remove('d-none');
    });

    copyAll.addEventListener('click', function () {
        var items = titleList.querySelectorAll('.list-group-item span');
        var texts = [];
        for (var i = 0; i < items.length; i++) { texts.push((i + 1) + '. ' + items[i].textContent); }
        copyText(texts.join('\n'), copyAll);
    });
})();
</script>
@endsection
