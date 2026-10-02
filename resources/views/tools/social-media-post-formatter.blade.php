@extends('layouts.app')

@section('title', 'Social Post Formatter - Azlaan Tools')
@section('meta_description', 'Format social media posts for every platform with character limits, counters and thread splitter, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Social Post Formatter</h1>
            <p class="lead text-muted">Write your post and check it against every platform's character limit — X, Instagram, LinkedIn, TikTok and more. If it is too long, split it into a thread. All in your browser, copy in one click.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="platformSelect" class="form-label fw-semibold">Choose platform</label>
                        <select class="form-select" id="platformSelect">
                            <option value="280" data-name="X (Twitter)">X (Twitter) — 280 characters</option>
                            <option value="500" data-name="Threads">Threads — 500 characters</option>
                            <option value="2200" data-name="Instagram caption" selected>Instagram caption — 2,200 characters</option>
                            <option value="3000" data-name="LinkedIn post">LinkedIn post — 3,000 characters</option>
                            <option value="4000" data-name="TikTok caption">TikTok caption — 4,000 characters</option>
                            <option value="63206" data-name="Facebook post">Facebook post — 63,206 characters</option>
                            <option value="5000" data-name="YouTube description">YouTube description — 5,000 characters</option>
                            <option value="65536" data-name="WhatsApp message">WhatsApp message — 65,536 characters</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="postText" class="form-label fw-semibold">Write your post</label>
                        <textarea class="form-control" id="postText" rows="7" placeholder="Write or paste your post here..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span id="countLabel">0 / 2,200 characters</span>
                            <span id="overLabel" class="text-danger fw-semibold d-none">Over the limit!</span>
                        </div>
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar" id="limitBar" role="progressbar" style="width:0%"></div>
                        </div>
                    </div>

                    <div class="row text-center mb-3">
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold" id="statWords">0</div><small class="text-muted">Words</small></div></div>
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold" id="statLines">0</div><small class="text-muted">Lines</small></div></div>
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold" id="statTags">0</div><small class="text-muted">Hashtags</small></div></div>
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold" id="statMentions">0</div><small class="text-muted">Mentions</small></div></div>
                    </div>

                    <div class="d-grid d-sm-flex gap-2">
                        <button type="button" class="btn btn-primary flex-sm-grow-1" id="copyBtn">Copy Post</button>
                        <button type="button" class="btn btn-outline-secondary" id="threadBtn">Split into Thread</button>
                        <button type="button" class="btn btn-outline-secondary" id="tagsBtn">Extract Hashtags</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="okBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 id="resultsTitle">Result</h5>
                        <div id="threadList"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li><strong>Choose a platform</strong> — each platform's real character limit is applied automatically.</li>
                <li><strong>Write or paste your post</strong> — see live counts of characters, words, hashtags and mentions. The progress bar changes color as you get near the limit.</li>
                <li><strong>Copy Post</strong> puts the text on your clipboard, or <strong>Extract Hashtags</strong> pulls out just the hashtags.</li>
                <li>If your post is over X's 280 limit, press <strong>Split into Thread</strong> — the post is split into numbered parts (1/4), ready to post one by one.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var platformSelect = document.getElementById('platformSelect');
    var postText = document.getElementById('postText');
    var countLabel = document.getElementById('countLabel');
    var overLabel = document.getElementById('overLabel');
    var limitBar = document.getElementById('limitBar');
    var statWords = document.getElementById('statWords');
    var statLines = document.getElementById('statLines');
    var statTags = document.getElementById('statTags');
    var statMentions = document.getElementById('statMentions');
    var copyBtn = document.getElementById('copyBtn');
    var threadBtn = document.getElementById('threadBtn');
    var tagsBtn = document.getElementById('tagsBtn');
    var errorBox = document.getElementById('errorBox');
    var okBox = document.getElementById('okBox');
    var results = document.getElementById('results');
    var resultsTitle = document.getElementById('resultsTitle');
    var threadList = document.getElementById('threadList');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        okBox.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function showOk(msg) {
        okBox.textContent = msg;
        okBox.classList.remove('d-none');
    }
    function currentLimit() {
        return parseInt(platformSelect.value, 10);
    }
    function currentName() {
        return platformSelect.options[platformSelect.selectedIndex].getAttribute('data-name');
    }

    function updateStats() {
        hideError();
        var text = postText.value;
        var limit = currentLimit();
        var len = text.length;
        var words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
        var lines = text === '' ? 0 : text.split('\n').length;
        var tags = (text.match(/#[\p{L}\p{N}_]+/gu) || []).length;
        var mentions = (text.match(/@[\p{L}\p{N}_.]+/gu) || []).length;

        countLabel.textContent = len.toLocaleString() + ' / ' + limit.toLocaleString() + ' characters';
        var pct = Math.min(100, Math.round(len / limit * 100));
        limitBar.style.width = pct + '%';
        limitBar.className = 'progress-bar';
        if (len > limit) {
            limitBar.classList.add('bg-danger');
            overLabel.classList.remove('d-none');
        } else if (pct >= 90) {
            limitBar.classList.add('bg-warning');
            overLabel.classList.add('d-none');
        } else {
            limitBar.classList.add('bg-success');
            overLabel.classList.add('d-none');
        }
        statWords.textContent = words;
        statLines.textContent = lines;
        statTags.textContent = tags;
        statMentions.textContent = mentions;
    }

    function copyText(t, okMsg) {
        hideError();
        if (!t) { showError('Please write something first, then copy.'); return; }
        function done() { showOk(okMsg); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(done, function () { fallbackCopy(t, done); });
        } else {
            fallbackCopy(t, done);
        }
    }
    function fallbackCopy(t, done) {
        var ta = document.createElement('textarea');
        ta.value = t;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); done(); }
        catch (e) { showError('Could not copy — please select the text yourself and copy.'); }
        document.body.removeChild(ta);
    }

    postText.addEventListener('input', updateStats);
    platformSelect.addEventListener('change', updateStats);

    copyBtn.addEventListener('click', function () {
        copyText(postText.value, 'Post copied — now paste it on ' + currentName() + '.');
    });

    tagsBtn.addEventListener('click', function () {
        hideError();
        var tags = postText.value.match(/#[\p{L}\p{N}_]+/gu) || [];
        if (tags.length === 0) { showError('No hashtag found in this post.'); return; }
        var seen = [];
        tags.forEach(function (t) {
            var low = t.toLowerCase();
            var dup = seen.some(function (x) { return x.toLowerCase() === low; });
            if (!dup) seen.push(t);
        });
        copyText(seen.join(' '), seen.length + ' hashtags copied (duplicates removed).');
    });

    threadBtn.addEventListener('click', function () {
        hideError();
        okBox.classList.add('d-none');
        var text = postText.value.trim();
        if (!text) { showError('Please write your post first.'); return; }
        var limit = 280;
        var reserve = 8;
        var maxLen = limit - reserve;
        var words = text.split(/\s+/);
        var chunks = [];
        var current = '';
        words.forEach(function (w) {
            if ((current + ' ' + w).trim().length > maxLen && current !== '') {
                chunks.push(current.trim());
                current = w;
            } else {
                current = (current + ' ' + w);
            }
        });
        if (current.trim() !== '') chunks.push(current.trim());
        var total = chunks.length;
        resultsTitle.textContent = 'Thread — ' + total + ' parts (for X)';
        threadList.innerHTML = '';
        chunks.forEach(function (c, i) {
            var label = (i + 1) + '/' + total + ' ' + c;
            var wrap = document.createElement('div');
            wrap.className = 'border rounded p-2 mb-2 bg-light';
            var p = document.createElement('p');
            p.className = 'mb-1 small';
            p.textContent = label;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary';
            btn.textContent = 'Copy part ' + (i + 1);
            (function (txt) {
                btn.addEventListener('click', function () { copyText(txt, 'Part copied.'); });
            })(label);
            wrap.appendChild(p);
            wrap.appendChild(btn);
            threadList.appendChild(wrap);
        });
        results.classList.remove('d-none');
    });

    updateStats();
})();
</script>
@endsection
