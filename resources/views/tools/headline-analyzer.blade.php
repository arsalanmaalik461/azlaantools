@extends('layouts.app')

@section('title', 'Headline Analyzer - Azlaan Tools')
@section('meta_description', 'Score your blog headline 0-100: check word balance and emotional pull to increase CTR — free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Headline Analyzer</h1>
            <p class="lead text-muted">Write your blog or article headline here — the tool gives it a 0-100 score, and shows word balance and emotional pull so you get more clicks.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="headlineInput" class="form-label fw-semibold">Write your headline</label>
                        <input type="text" class="form-control" id="headlineInput" placeholder="e.g. 10 Easy Ways to Reduce Your Electricity Bill" maxlength="140">
                        <div class="form-text"><span id="charCount">0</span>/140 characters</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Analyze</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your headline in the box.</li>
                <li>Press "Analyze".</li>
                <li>Read the score, word balance and suggestions, and improve your headline.</li>
            </ol>
            <p class="text-muted small">This score is based on general copywriting rules — not real CTR data from Google or social platforms. Best practice: do an A/B test.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var headlineInput = document.getElementById('headlineInput');
    var charCount = document.getElementById('charCount');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    var powerWords = ['free', 'best', 'secret', 'proven', 'ultimate', 'amazing', 'powerful', 'easy', 'quick', 'simple', 'guide', 'tips', 'tricks', 'hack', 'new', 'top', 'guaranteed', 'exclusive', 'instant', 'save', 'earn', 'win', 'asana', 'muft', 'behtareen', 'tareeqa', 'tareeqay', 'raaz', 'kam', 'zyada', 'free'];
    var emotionalWords = ['shocking', 'incredible', 'unbelievable', 'fear', 'love', 'hate', 'amazing', 'terrible', 'wonderful', 'surprising', 'secret', 'warning', 'danger', 'miracle', 'dream', 'pain', 'joy', 'hope', 'amazing', 'hairan', 'khauf', 'mohabbat', 'dard', 'khushi', 'umeed'];
    var commonWords = ['a', 'the', 'and', 'or', 'of', 'in', 'on', 'to', 'for', 'with', 'ka', 'ki', 'ke', 'ko', 'me', 'mein', 'par', 'aur', 'se', 'ne', 'ya', 'hai', 'hain', 'kaise', 'kya', 'kyun'];

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
    function hasNum(headline) { return /\d/.test(headline); }
    function startsWithQuestion(headline) { return /^(kya|kyun|kaise|how|why|what|when|where|is|are|do|does)\b/i.test(headline.trim()); }

    headlineInput.addEventListener('input', function () {
        charCount.textContent = headlineInput.value.length;
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var headline = headlineInput.value.trim();
        if (!headline) { showError('Please write your headline first.'); return; }

        var words = headline.split(/\s+/);
        var wordCount = words.length;
        var charLen = headline.length;
        var lower = headline.toLowerCase();

        // --- scoring factors (max 100) ---
        var score = 0;
        var tips = [];

        // Length (ideal 50-70 chars): 20 pts
        if (charLen >= 40 && charLen <= 70) { score += 20; }
        else if (charLen >= 30 && charLen <= 90) { score += 14; tips.push('Your headline length is ' + charLen + ' characters — ideal is 50-70.'); }
        else { score += 6; tips.push('Your headline is ' + (charLen < 40 ? 'too short' : 'too long') + ' — 50-70 characters is ideal.'); }

        // Word count (ideal 6-12): 15 pts
        if (wordCount >= 6 && wordCount <= 12) { score += 15; }
        else if (wordCount >= 4 && wordCount <= 16) { score += 10; tips.push('Word count is ' + wordCount + ' — 6-12 words give better CTR.'); }
        else { score += 5; tips.push('Word count is ' + wordCount + ' — aim for 6-12 words.'); }

        // Power words: 20 pts
        var powerFound = words.filter(function (w) { return powerWords.indexOf(w.toLowerCase().replace(/[^a-z]/g, '')) !== -1; });
        if (powerFound.length >= 2) { score += 20; }
        else if (powerFound.length === 1) { score += 12; tips.push('Found one power word (' + esc(powerFound[0]) + ') — add another: free, best, secret, proven, ultimate.'); }
        else { score += 4; tips.push('No power word — add a word like "free", "best", "proven", "secret".'); }

        // Emotional words: 15 pts
        var emoFound = words.filter(function (w) { return emotionalWords.indexOf(w.toLowerCase().replace(/[^a-z]/g, '')) !== -1; });
        if (emoFound.length >= 1) { score += 15; } else { score += 5; tips.push('Add an emotional word (amazing, shocking, warning, secret) — it increases curiosity.'); }

        // Numbers: 10 pts
        if (hasNum(headline)) { score += 10; } else { score += 2; tips.push('Add a number — headlines like "10 Ways" get more clicks.'); }

        // Question or how-to format: 10 pts
        if (startsWithQuestion(headline) || /how to|kaise/i.test(headline)) { score += 10; }
        else { score += 4; tips.push('Try a question or "How to" format — curiosity increases clicks.'); }

        // Capitalization / readability: 10 pts
        var firstUpper = /^[A-Z]/.test(headline);
        var allCapsWords = words.filter(function (w) { return /^[A-Z]{3,}$/.test(w); }).length;
        if (firstUpper && allCapsWords <= 1) { score += 10; }
        else { score += 4; if (allCapsWords > 1) tips.push('Avoid too many ALL CAPS words — it looks spammy.'); }

        score = Math.max(0, Math.min(100, Math.round(score)));

        // Word balance
        var commonCount = words.filter(function (w) { return commonWords.indexOf(w.toLowerCase().replace(/[^a-z]/g, '')) !== -1; }).length;
        var uncommonCount = wordCount - commonCount - powerFound.length - emoFound.length;
        var pct = function (n) { return wordCount ? Math.round(n / wordCount * 100) : 0; };

        var grade, gradeClass;
        if (score >= 80) { grade = 'Excellent — publish it!'; gradeClass = 'success'; }
        else if (score >= 60) { grade = 'Good — can be slightly better.'; gradeClass = 'primary'; }
        else if (score >= 40) { grade = 'Average — read the suggestions below.'; gradeClass = 'warning'; }
        else { grade = 'Weak — rewrite the headline.'; gradeClass = 'danger'; }

        var html = '<div class="text-center mb-3">';
        html += '<div class="display-4 fw-bold text-' + gradeClass + '">' + score + '<small class="fs-5 text-muted">/100</small></div>';
        html += '<div class="fw-semibold">' + esc(grade) + '</div></div>';

        html += '<h5>Word Balance</h5>';
        html += '<div class="mb-2"><div class="d-flex justify-content-between small"><span>Common words</span><span>' + pct(commonCount) + '%</span></div>';
        html += '<div class="progress mb-2" style="height:8px;"><div class="progress-bar bg-secondary" style="width:' + pct(commonCount) + '%"></div></div></div>';
        html += '<div class="mb-2"><div class="d-flex justify-content-between small"><span>Power words</span><span>' + pct(powerFound.length) + '%</span></div>';
        html += '<div class="progress mb-2" style="height:8px;"><div class="progress-bar bg-primary" style="width:' + pct(powerFound.length) + '%"></div></div></div>';
        html += '<div class="mb-3"><div class="d-flex justify-content-between small"><span>Emotional words</span><span>' + pct(emoFound.length) + '%</span></div>';
        html += '<div class="progress" style="height:8px;"><div class="progress-bar bg-danger" style="width:' + pct(emoFound.length) + '%"></div></div></div>';

        html += '<div class="row text-center mb-3">';
        html += '<div class="col-4"><div class="border rounded p-2"><div class="fw-bold">' + wordCount + '</div><div class="small text-muted">Words</div></div></div>';
        html += '<div class="col-4"><div class="border rounded p-2"><div class="fw-bold">' + charLen + '</div><div class="small text-muted">Characters</div></div></div>';
        html += '<div class="col-4"><div class="border rounded p-2"><div class="fw-bold">' + (hasNum(headline) ? 'Yes' : 'No') + '</div><div class="small text-muted">Number</div></div></div>';
        html += '</div>';

        if (tips.length) {
            html += '<h5>Suggestions</h5><ul class="list-group">';
            tips.slice(0, 6).forEach(function (t) { html += '<li class="list-group-item small">' + t + '</li>'; });
            html += '</ul>';
        } else {
            html += '<div class="alert alert-success">Great headline — all factors are strong!</div>';
        }

        results.innerHTML = html;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
