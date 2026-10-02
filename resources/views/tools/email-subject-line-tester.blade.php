@extends('layouts.app')
@section('title', 'Email Subject Line Tester - Azlaan Tools')
@section('meta_description', 'Check your email subject score and write lines that boost open rate. Free online subject tester.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Email Subject Line Tester</h1>
            <p class="lead text-muted">Enter your email subject line — the tool gives its score, spam risk and improvement tips so your open rate goes up.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">Email subject line</label>
                        <input type="text" class="form-control" id="subject" placeholder="e.g. 5 mistakes that raise your electricity bill" maxlength="200">
                        <div class="form-text"><span id="charCount">0</span>/200 characters. Ideal length: 30-50 characters.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Test Subject Line</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center mb-3">
                            <div class="display-4 fw-bold" id="scoreNum">0</div>
                            <div class="fs-5" id="scoreGrade"></div>
                        </div>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar" id="scoreBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4"><div class="border rounded p-2 text-center"><div class="small text-muted">Length</div><strong id="statLen">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2 text-center"><div class="small text-muted">Words</div><strong id="statWords">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2 text-center"><div class="small text-muted">Spam risk</div><strong id="statSpam">-</strong></div></div>
                        </div>
                        <h2 class="h6">Feedback</h2>
                        <ul class="list-group mb-3" id="feedbackList"></ul>
                        <h2 class="h6">Preview (this is how it will look in the inbox)</h2>
                        <div class="border rounded p-3 bg-light">
                            <div class="fw-semibold small">From: your-company@example.com</div>
                            <div class="fs-6" id="previewSubj"></div>
                            <div class="small text-muted">The first line of this email will appear here in the preview...</div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your subject line.</li>
                <li>Press Test — see the score, spam risk and feedback.</li>
                <li>Edit as per the tips and test again.</li>
            </ol>
            <p class="small text-muted">This score is an estimate based on common email marketing best practices — your actual open rate depends on your audience.</p>
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
    var subjectEl = document.getElementById('subject');

    subjectEl.addEventListener('input', function () {
        document.getElementById('charCount').textContent = subjectEl.value.length;
    });

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

    var SPAM = ['free', 'winner', 'guarantee', 'guaranteed', 'click here', 'act now', 'buy now', 'limited time',
        'urgent', 'congratulations', 'cash', 'prize', 'risk free', 'risk-free', '100%', 'make money', 'double your',
        'no cost', 'no fees', 'no obligation', 'order now', 'special promotion', 'amazing deal', 'once in a lifetime',
        'while supplies last', 'you have won', "you've won", 'dear friend', '!!!'];
    var POWER = ['how to', 'secret', 'proven', 'easy', 'quick', 'new', 'exclusive', 'limited', 'free', 'now',
        'tips', 'guide', 'mistake', 'warning', 'discover', 'simple', 'best', 'top', 'save', 'win'];

    goBtn.addEventListener('click', function () {
        hideError();
        var s = subjectEl.value.trim();
        if (!s) { showError('Please enter a subject line first.'); return; }

        var score = 50, tips = [], good = [], bad = [];
        var len = s.length;
        var words = s.split(/\s+/).filter(Boolean);
        var lower = s.toLowerCase();

        // length
        if (len >= 30 && len <= 50) { score += 15; good.push('Length is great (' + len + ' characters) — it will show fully on mobile.'); }
        else if (len < 30) { score += 5; tips.push('Make it a bit longer (30-50 characters is ideal) — give full context.'); }
        else if (len <= 70) { score -= 5; tips.push('Make it a bit shorter — 50+ characters get cut off on mobile.'); }
        else { score -= 15; bad.push('Too long (' + len + ' chars) — it will get cut off in the mobile inbox.'); }

        // word count
        if (words.length >= 3 && words.length <= 7) { score += 5; good.push('Word count is fine (' + words.length + ' words).'); }
        else { score -= 3; tips.push('3-7 words are ideal — right now there are ' + words.length + '.'); }

        // spam words
        var found = SPAM.filter(function (w) { return lower.indexOf(w) !== -1; });
        if (found.length) {
            score -= found.length * 12;
            bad.push('Spam trigger words found: "' + found.slice(0, 3).join('", "') + '" — these can send it to the spam folder.');
        } else { score += 8; good.push('No common spam trigger word found.'); }

        // caps
        var letters = s.replace(/[^A-Za-z]/g, '');
        var caps = letters.replace(/[^A-Z]/g, '').length;
        var capsRatio = letters.length ? caps / letters.length : 0;
        if (capsRatio > 0.5 && letters.length > 3) { score -= 10; bad.push('Avoid ALL CAPS — it looks like shouting and attracts spam filters.'); }
        else if (capsRatio > 0) { score += 2; }

        // exclamation
        var excl = (s.match(/!/g) || []).length;
        if (excl === 0) { score += 2; }
        else if (excl === 1) { score += 3; good.push('One exclamation mark is fine — it adds excitement.'); }
        else { score -= 6; bad.push(excl + ' exclamation marks are too many — more than one looks spammy.'); }

        // emoji
        var emoji = (s.match(/[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{2B00}-\u{2BFF}]/gu) || []).length;
        if (emoji === 1) { score += 4; good.push('One emoji grabs attention.'); }
        else if (emoji > 1) { score -= 4; tips.push(emoji + ' emojis — one is enough.'); }

        // question
        if (s.indexOf('?') !== -1) { score += 5; good.push('A question builds curiosity — good technique.'); }

        // number
        if (/\d/.test(s)) { score += 5; good.push('A number (e.g. 5, 2026) adds specifics — clicks go up.'); }

        // personalization
        var hasToken = s.indexOf('{') !== -1 && s.indexOf('}') !== -1;
        if (/you|your|yours/i.test(s) || hasToken) { score += 6; good.push('A personal touch ("you") involves the reader.'); }
        else { tips.push('Add a word like "You" to make it personal.'); }

        // power words
        var pw = POWER.filter(function (w) { return lower.indexOf(w) !== -1; });
        if (pw.length) { score += 4; good.push('Power word found: "' + pw[0] + '" — this raises the open rate.'); }

        // fake RE/FW
        if (/^(re|fw|fwd)\s*:/i.test(s)) { score -= 15; bad.push('Starting with "RE:" or "FW:" is misleading — it breaks trust.'); }

        // vague
        if (/^(hi|hello|hey|update|news|newsletter)\b/i.test(s) && words.length <= 3) {
            score -= 8; tips.push('Too vague — give the reader a reason to open the email.');
        }

        score = Math.max(0, Math.min(100, score));
        var grade, cls;
        if (score >= 80) { grade = 'Excellent — ready to send!'; cls = 'bg-success'; }
        else if (score >= 60) { grade = 'Good — small fixes can make it the best.'; cls = 'bg-info'; }
        else if (score >= 40) { grade = 'Average — follow the tips below.'; cls = 'bg-warning'; }
        else { grade = 'Weak — it needs rewriting.'; cls = 'bg-danger'; }

        document.getElementById('scoreNum').textContent = score + '/100';
        document.getElementById('scoreGrade').textContent = grade;
        var bar = document.getElementById('scoreBar');
        bar.style.width = score + '%';
        bar.className = 'progress-bar ' + cls;

        document.getElementById('statLen').textContent = len + ' chars';
        document.getElementById('statWords').textContent = words.length;
        var spamRisk = found.length >= 2 ? 'High' : (found.length === 1 ? 'Medium' : 'Low');
        document.getElementById('statSpam').textContent = spamRisk;

        var list = document.getElementById('feedbackList');
        list.innerHTML = '';
        function add(items, badge, bcls) {
            items.forEach(function (t) {
                var li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = '<span class="badge ' + bcls + ' me-2">' + badge + '</span>' + esc(t);
                list.appendChild(li);
            });
        }
        add(good, 'Good', 'bg-success');
        add(tips, 'Tip', 'bg-info');
        add(bad, 'Fix', 'bg-danger');
        if (!good.length && !tips.length && !bad.length) {
            var li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = 'No special feedback — the line is fine.';
            list.appendChild(li);
        }

        document.getElementById('previewSubj').textContent = s;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
@endsection
