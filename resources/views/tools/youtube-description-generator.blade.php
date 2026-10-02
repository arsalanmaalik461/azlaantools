@extends('layouts.app')

@section('title', 'YouTube Description Generator - Azlaan Tools')
@section('meta_description', 'Generate SEO-friendly YouTube video descriptions with keywords, hashtags and CTAs in seconds, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">YouTube Description Generator</h1>
            <p class="lead text-muted">Enter keywords, get a ready SEO description. Save upload time.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="ydTitle" class="form-label fw-semibold">Video title</label>
                        <input type="text" class="form-control" id="ydTitle" placeholder="e.g. How to Save Electricity Bill in Pakistan">
                    </div>
                    <div class="mb-3">
                        <label for="ydTopic" class="form-label fw-semibold">Main topic (1 line)</label>
                        <input type="text" class="form-control" id="ydTopic" placeholder="e.g. 7 ways to reduce your electricity bill">
                    </div>
                    <div class="mb-3">
                        <label for="ydKeywords" class="form-label fw-semibold">Keywords (separated by comma)</label>
                        <input type="text" class="form-control" id="ydKeywords" placeholder="e.g. electricity bill, solar panel, save electricity, pakistan">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="ydChannel" class="form-label fw-semibold">Channel name</label>
                            <input type="text" class="form-control" id="ydChannel" placeholder="e.g. Azlaan Solar">
                        </div>
                        <div class="col-md-6">
                            <label for="ydStyle" class="form-label fw-semibold">Description style</label>
                            <select class="form-select" id="ydStyle">
                                <option value="standard">Standard (hook + summary + CTA)</option>
                                <option value="short">Short (fast summary)</option>
                                <option value="detailed">Detailed (chapters + resources)</option>
                                <option value="tutorial">Tutorial (steps focus)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ydTimestamps" class="form-label fw-semibold">Timestamps (optional, "0:00 Topic" on each line)</label>
                        <textarea class="form-control" id="ydTimestamps" rows="3" placeholder="0:00 Intro&#10;0:45 First method&#10;2:30 Second method"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Description</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Generated description <span class="small text-muted" id="ydCount"></span></h2>
                        <pre class="bg-light border rounded p-3 small" id="ydOutput" style="white-space:pre-wrap; word-wrap:break-word; max-height:420px; overflow:auto;"></pre>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success" id="ydCopy">Copy</button>
                            <button type="button" class="btn btn-outline-secondary" id="ydDownload">TXT Download</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the video title, topic and keywords.</li>
                <li>Choose a style and add timestamps (if you have them).</li>
                <li>Click <strong>Generate Description</strong>, then copy and paste into the YouTube description box.</li>
            </ol>
            <p class="small text-muted">The generated text is a starting point — read it and adjust it to match your video.</p>
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
    var output = document.getElementById('ydOutput');
    var lastText = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function cleanList(s) {
        return s.split(',').map(function (x) { return x.trim(); }).filter(function (x) { return x.length > 0; });
    }
    function titleCase(s) {
        return s.replace(/\w\S*/g, function (t) { return t.charAt(0).toUpperCase() + t.substr(1).toLowerCase(); });
    }

    function buildDescription() {
        var title = document.getElementById('ydTitle').value.trim();
        var topic = document.getElementById('ydTopic').value.trim();
        var keywords = cleanList(document.getElementById('ydKeywords').value);
        var channel = document.getElementById('ydChannel').value.trim() || 'this channel';
        var style = document.getElementById('ydStyle').value;
        var tsRaw = document.getElementById('ydTimestamps').value.trim();

        if (!title) { showError('Please enter the video title.'); return null; }
        if (!keywords.length) { showError('Enter at least one keyword (separated by comma).'); return null; }

        var kwStr = keywords.join(', ');
        var hashtags = keywords.slice(0, 5).map(function (k) { return '#' + k.replace(/[^a-zA-Z0-9_]/g, ''); }).join(' ');
        var lines = [];

        // Hook
        lines.push(title);
        lines.push('');
        lines.push('In this video: ' + (topic || title) + '. Watch till the end for the full breakdown.');
        lines.push('');

        if (style === 'short') {
            lines.push('Summary: ' + (topic || title) + '. Key points covered: ' + kwStr + '.');
            lines.push('');
        } else {
            lines.push('What you will learn:');
            keywords.forEach(function (k) {
                lines.push('  - ' + titleCase(k));
            });
            lines.push('');
        }

        if (style === 'tutorial') {
            lines.push('Steps covered in this tutorial:');
            keywords.slice(0, 5).forEach(function (k, i) {
                lines.push('  ' + (i + 1) + '. ' + titleCase(k));
            });
            lines.push('');
        }

        if (tsRaw) {
            lines.push('TIMESTAMPS');
            tsRaw.split('\n').forEach(function (l) {
                l = l.trim();
                if (l) lines.push(l);
            });
            lines.push('');
        } else if (style === 'detailed') {
            lines.push('CHAPTERS');
            lines.push('0:00 - Introduction');
            lines.push('1:00 - ' + titleCase(keywords[0] || 'Main topic'));
            if (keywords[1]) lines.push('3:00 - ' + titleCase(keywords[1]));
            if (keywords[2]) lines.push('5:00 - ' + titleCase(keywords[2]));
            lines.push('8:00 - Final tips and conclusion');
            lines.push('');
        }

        lines.push('Subscribe to ' + channel + ' for more videos on ' + kwStr + '.');
        lines.push('Turn on the bell icon so you never miss an upload.');
        lines.push('');
        lines.push('Keywords: ' + kwStr);
        if (hashtags) { lines.push(''); lines.push(hashtags); }
        if (style === 'detailed') {
            lines.push('');
            lines.push('DISCLAIMER: All information shared is for educational purposes. Results may vary.');
        }
        return lines.join('\n');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var text = buildDescription();
        if (!text) return;
        lastText = text;
        output.textContent = text;
        var words = text.split(/\s+/).length;
        document.getElementById('ydCount').textContent = '(' + words + ' words, ' + text.length + ' chars)';
        results.classList.remove('d-none');
    });

    document.getElementById('ydCopy').addEventListener('click', function () {
        hideError();
        if (!lastText) { showError('Generate the description first.'); return; }
        function done() {
            document.getElementById('ydCopy').textContent = 'Copied!';
            setTimeout(function () { document.getElementById('ydCopy').textContent = 'Copy'; }, 1500);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastText).then(done, function () { showError('Copy failed. Select the text and copy it manually.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = lastText;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { showError('Copy failed. Select the text and copy it manually.'); }
            document.body.removeChild(ta);
        }
    });

    document.getElementById('ydDownload').addEventListener('click', function () {
        hideError();
        if (!lastText) { showError('Generate the description first.'); return; }
        var blob = new Blob([lastText], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'youtube-description.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
