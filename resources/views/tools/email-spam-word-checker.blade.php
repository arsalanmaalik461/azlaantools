@extends('layouts.app')

@section('title', 'Spam Word Checker - Azlaan Tools')
@section('meta_description', 'Check your email text for spam trigger words to improve inbox deliverability, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Spam Word Checker</h1>
            <p class="lead text-muted">Find spam trigger words in your email text — so your email reaches the inbox instead of the spam folder. Paste the subject and body, and see the score.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="subjectInput" class="form-label fw-semibold">Email subject</label>
                        <input type="text" class="form-control" id="subjectInput" placeholder="e.g. Limited time offer - 100% FREE money!!!">
                    </div>
                    <div class="mb-3">
                        <label for="bodyInput" class="form-label fw-semibold">Email body text</label>
                        <textarea class="form-control" id="bodyInput" rows="6" placeholder="Paste your email text here..."></textarea>
                        <div class="form-text">Only text is checked — images or links are not analyzed.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Check Spam Words</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Spam Score</h5>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="display-5 fw-bold" id="scoreNum">100</div>
                            <div>
                                <span class="badge fs-6" id="scoreBadge">Safe</span>
                                <div class="text-muted small" id="scoreNote"></div>
                            </div>
                        </div>
                        <h5>Found spam trigger words (<span id="matchCount">0</span>)</h5>
                        <div id="matchList" class="mb-3"></div>
                        <h5>Suggestions</h5>
                        <ul id="tipList" class="mb-0"></ul>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste your email subject and body text.</li>
                <li>Press "Check Spam Words".</li>
                <li>Remove or change the trigger words to improve the score.</li>
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

    // Spam trigger words/phrases with severity weights (1 = mild, 3 = severe)
    var SPAM_WORDS = [
        ['free', 2], ['100% free', 3], ['guarantee', 2], ['guaranteed', 2],
        ['act now', 3], ['limited time', 2], ['urgent', 2], ['click here', 2],
        ['buy now', 2], ['order now', 2], ['make money', 3], ['earn money', 2],
        ['get rich', 3], ['double your', 3], ['risk free', 2], ['no risk', 2],
        ['cash bonus', 3], ['prize', 2], ['winner', 2], ['congratulations', 1],
        ['you have won', 3], ['lottery', 3], ['million dollars', 3],
        ['no credit check', 2], ['debt free', 2], ['work from home', 1],
        ['!!!', 1], ['$$$', 2], ['amazing', 1], ['incredible deal', 2],
        ['once in a lifetime', 2], ['exclusive deal', 1], ['special promotion', 1],
        ['call now', 2], ['apply now', 1], ['sign up free', 1],
        ['credit card', 1], ['bank account', 1], ['verify your account', 3],
        ['update your account', 2], ['suspended', 2], ['expire', 1],
        ['weight loss', 2], ['miracle', 2], ['cure', 2],
        ['opportunity', 1], ['investment', 1], ['income', 1],
        ['trial', 1], ['sample', 1], ['bargain', 1], ['cheap', 1],
        ['discount', 1], ['save up to', 1], ['lowest price', 1],
        ['no cost', 2], ['no fees', 1], ['pre-approved', 2],
        ['refinance', 1], ['mortgage', 1], ['loan', 1],
        ['pharmacy', 2], ['viagra', 3], ['pills', 2],
        ['nigeria', 3], ['inheritance', 3], ['beneficiary', 2]
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function findMatches(text) {
        var lower = text.toLowerCase();
        var hits = [];
        for (var i = 0; i < SPAM_WORDS.length; i++) {
            var phrase = SPAM_WORDS[i][0];
            var weight = SPAM_WORDS[i][1];
            var count = 0;
            var pos = lower.indexOf(phrase);
            while (pos !== -1) {
                count++;
                pos = lower.indexOf(phrase, pos + phrase.length);
            }
            if (count > 0) { hits.push({ phrase: phrase, weight: weight, count: count }); }
        }
        return hits;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var subject = document.getElementById('subjectInput').value;
        var body = document.getElementById('bodyInput').value;
        if (!subject.trim() && !body.trim()) {
            showError('Enter the subject or body text first.');
            return;
        }
        var full = (subject + ' ' + body).trim();

        var hits = findMatches(full);
        var penalty = 0;
        var map = {};
        for (var i = 0; i < hits.length; i++) {
            var h = hits[i];
            penalty += h.weight * (1 + Math.min(h.count - 1, 2));
            map[h.phrase] = (map[h.phrase] || 0) + h.count;
        }
        var score = Math.max(0, 100 - penalty * 6);

        document.getElementById('scoreNum').textContent = score;
        var badge = document.getElementById('scoreBadge');
        var note = document.getElementById('scoreNote');
        badge.className = 'badge fs-6';
        if (score >= 80) {
            badge.classList.add('bg-success'); badge.textContent = 'Safe';
            note.textContent = 'Your email has a good chance of reaching the inbox.';
        } else if (score >= 50) {
            badge.classList.add('bg-warning', 'text-dark'); badge.textContent = 'Risky';
            note.textContent = 'There are some trigger words — reduce them.';
        } else {
            badge.classList.add('bg-danger'); badge.textContent = 'Spam Risk';
            note.textContent = 'This email may go to the spam folder.';
        }

        var matchList = document.getElementById('matchList');
        var names = Object.keys(map).sort(function (a, b) { return map[b] - map[a]; });
        document.getElementById('matchCount').textContent = names.length;
        if (names.length) {
            var html = '';
            for (var k = 0; k < names.length; k++) {
                html += '<span class="badge bg-danger me-1 mb-1">' + esc(names[k]) + ' x' + map[names[k]] + '</span>';
            }
            matchList.innerHTML = html;
        } else {
            matchList.innerHTML = '<p class="text-success mb-0">No spam trigger words found. Great!</p>';
        }

        var tips = [];
        if (score < 100) { tips.push('Replace trigger words with neutral words (for example: use "complimentary" instead of "FREE").'); }
        if (/!!!/.test(full)) { tips.push('Use only one exclamation mark instead of "!!!".'); }
        if (subject && /free|guarantee|urgent|act now/i.test(subject)) { tips.push('Removing spam words from the subject line makes the biggest difference.'); }
        if (full.length > 2000) { tips.push('A very long promotional email can also trigger spam filters.'); }
        tips.push('Ask your contacts to mark you as "not spam" and reply — this improves sender reputation.');
        tips.push('Always send bulk emails through a verified mailing service (Mailchimp, Brevo and similar), not from your personal Gmail.');
        var tipList = document.getElementById('tipList');
        var th = '';
        for (var t = 0; t < tips.length; t++) { th += '<li>' + esc(tips[t]) + '</li>'; }
        tipList.innerHTML = th;

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
