@extends('layouts.app')

@section('title', 'English Vocabulary Quiz - Azlaan Tools')
@section('meta_description', 'Free online English vocabulary quiz with meanings. Practice words for CSS, MDCAT and IELTS.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">English Vocabulary Quiz</h1>
            <p class="lead text-muted">English vocabulary practice quiz — for CSS, MDCAT and IELTS. Choose the right meaning in every question.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="setupBox">
                        <div class="mb-3">
                            <label for="qCount" class="form-label fw-semibold">Number of Questions</label>
                            <select class="form-select" id="qCount">
                                <option value="10" selected>10 questions</option>
                                <option value="20">20 questions</option>
                                <option value="30">30 questions</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary w-100" id="goBtn">Start Quiz</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold" id="qProgress">Q 1/10</span>
                            <span class="badge bg-primary" id="scoreBadge">Score: 0</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" id="progBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <p class="fs-5" id="qWord"></p>
                        <p class="text-muted small" id="qHint"></p>
                        <div id="optList" class="d-grid gap-2"></div>
                        <div class="alert mt-3 d-none" id="feedbackBox" role="alert"></div>
                        <button type="button" class="btn btn-primary w-100 mt-2 d-none" id="nextBtn">Next</button>

                        <div id="finalBox" class="d-none text-center mt-2">
                            <h2 class="h4">Quiz Complete!</h2>
                            <p class="fs-3 fw-bold" id="finalScore"></p>
                            <p class="text-muted" id="finalVerdict"></p>
                            <div id="reviewList" class="text-start"></div>
                            <button type="button" class="btn btn-primary w-100 mt-3" id="retryBtn">Try Again</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the number of questions and press Start Quiz.</li>
                <li>Choose the right meaning for each word — you get instant feedback.</li>
                <li>At the end, see your score and a review of the wrong answers.</li>
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
    var setupBox = document.getElementById('setupBox');
    var qProgress = document.getElementById('qProgress');
    var scoreBadge = document.getElementById('scoreBadge');
    var progBar = document.getElementById('progBar');
    var qWord = document.getElementById('qWord');
    var qHint = document.getElementById('qHint');
    var optList = document.getElementById('optList');
    var feedbackBox = document.getElementById('feedbackBox');
    var nextBtn = document.getElementById('nextBtn');
    var finalBox = document.getElementById('finalBox');
    var finalScore = document.getElementById('finalScore');
    var finalVerdict = document.getElementById('finalVerdict');
    var reviewList = document.getElementById('reviewList');
    var retryBtn = document.getElementById('retryBtn');

    var BANK = [
        ['Abundant', 'plentiful', 'existing in large amounts'],
        ['Benevolent', 'kind-hearted', 'wants good for others'],
        ['Candid', 'honest and open', 'tells the truth without hiding it'],
        ['Diligent', 'hardworking', 'works with care and effort'],
        ['Eloquent', 'speaks well', 'expresses ideas well'],
        ['Frugal', 'careful with money', 'saves money and spends wisely'],
        ['Gregarious', 'sociable', 'enjoys meeting people'],
        ['Humble', 'not proud', 'free from pride'],
        ['Immense', 'huge', 'extremely large'],
        ['Jubilant', 'very happy', 'celebrating happiness'],
        ['Keen', 'eager', 'strongly wanting something'],
        ['Lucid', 'clear', 'easy to understand'],
        ['Meticulous', 'very careful', 'pays attention to every detail'],
        ['Novel', 'new and different', 'never seen before'],
        ['Obsolete', 'outdated', 'not used anymore'],
        ['Pragmatic', 'practical', 'decides based on reality'],
        ['Resilient', 'tough, recovers fast', 'recovers after trouble'],
        ['Scrupulous', 'very honest', 'strictly honest and moral'],
        ['Tenacious', 'determined', 'holds on and does not give up'],
        ['Ubiquitous', 'everywhere', 'present everywhere'],
        ['Vigilant', 'alert and watchful', 'stays alert to danger'],
        ['Wary', 'careful, suspicious', 'acts with care'],
        ['Zealous', 'passionate', 'works with great energy'],
        ['Ambiguous', 'unclear, double meaning', 'can be understood in two ways'],
        ['Coherent', 'logical and clear', 'clear and logically connected'],
        ['Discreet', 'keeps secrets', 'wisely keeps things private'],
        ['Ephemeral', 'short-lived', 'lasts a very short time'],
        ['Fortitude', 'inner strength', 'staying firm in difficulty'],
        ['Hypocrisy', 'two-faced', 'difference between words and actions'],
        ['Inevitable', 'unavoidable', 'sure to happen'],
        ['Lethargy', 'laziness', 'feeling lazy and tired'],
        ['Magnanimous', 'very generous', 'forgives with a big heart'],
        ['Nefarious', 'wicked', 'has bad intentions'],
        ['Opulent', 'luxurious', 'looks rich and grand'],
        ['Placate', 'calm down', 'make an angry person happy'],
        ['Quell', 'suppress', 'stop a revolt or noise'],
        ['Reluctant', 'unwilling', 'hesitates to act'],
        ['Serene', 'peaceful', 'full of peace'],
        ['Tedious', 'boring', 'long and makes you bored'],
        ['Versatile', 'multi-talented', 'good at different kinds of work']
    ];

    var quiz = [], idx = 0, score = 0, answered = false, wrong = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function shuffle(a) {
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = a[i]; a[i] = a[j]; a[j] = t;
        }
        return a;
    }

    function buildQuiz(n) {
        var pool = shuffle(BANK.slice());
        var qs = [];
        for (var i = 0; i < n && i < pool.length; i++) {
            var correct = pool[i];
            var distract = shuffle(BANK.filter(function (w) { return w[0] !== correct[0]; })).slice(0, 3);
            var opts = shuffle([correct[2]].concat(distract.map(function (d) { return d[2]; })));
            qs.push({ word: correct[0], meaning: correct[2], urdu: correct[1], opts: opts });
        }
        return qs;
    }

    function renderQ() {
        answered = false;
        var q = quiz[idx];
        qProgress.textContent = 'Q ' + (idx + 1) + '/' + quiz.length;
        scoreBadge.textContent = 'Score: ' + score;
        progBar.style.width = Math.round((idx / quiz.length) * 100) + '%';
        qWord.innerHTML = '';
        var b = document.createElement('strong');
        b.textContent = 'What does "' + q.word + '" mean?';
        qWord.appendChild(b);
        qHint.textContent = 'Hint: ' + q.urdu;
        optList.innerHTML = '';
        q.opts.forEach(function (opt) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-secondary text-start';
            btn.textContent = opt;
            btn.addEventListener('click', function () { answer(btn, opt); });
            optList.appendChild(btn);
        });
        feedbackBox.classList.add('d-none');
        nextBtn.classList.add('d-none');
    }

    function answer(btn, opt) {
        if (answered) return;
        answered = true;
        var q = quiz[idx];
        var ok = opt === q.meaning;
        var buttons = optList.querySelectorAll('button');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].disabled = true;
            if (buttons[i].textContent === q.meaning) {
                buttons[i].className = 'btn btn-success text-start';
            }
        }
        if (ok) {
            score++;
            scoreBadge.textContent = 'Score: ' + score;
            feedbackBox.className = 'alert alert-success mt-3';
            feedbackBox.textContent = 'Correct! Well done.';
        } else {
            btn.className = 'btn btn-danger text-start';
            feedbackBox.className = 'alert alert-danger mt-3';
            feedbackBox.textContent = 'Wrong. The correct answer: ' + q.meaning;
            wrong.push(q);
        }
        feedbackBox.classList.remove('d-none');
        nextBtn.classList.remove('d-none');
        nextBtn.textContent = idx + 1 >= quiz.length ? 'See Results' : 'Next';
    }

    function finishQuiz() {
        qProgress.textContent = 'Done';
        progBar.style.width = '100%';
        qWord.innerHTML = '';
        qHint.textContent = '';
        optList.innerHTML = '';
        feedbackBox.classList.add('d-none');
        nextBtn.classList.add('d-none');
        finalBox.classList.remove('d-none');
        finalScore.textContent = score + ' / ' + quiz.length;
        var pct = (score / quiz.length) * 100;
        finalVerdict.textContent = pct >= 80 ? 'Excellent! Your vocabulary is strong.' :
            pct >= 50 ? 'Good — practice a little more.' :
            'Keep practicing — learn 10 new words daily.';
        reviewList.innerHTML = '';
        if (wrong.length) {
            var h = document.createElement('h2');
            h.className = 'h6 mt-3';
            h.textContent = 'Review (wrong answers)';
            reviewList.appendChild(h);
            var ul = document.createElement('ul');
            ul.className = 'list-group';
            wrong.forEach(function (q) {
                var li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = q.word + ' = ' + q.meaning;
                ul.appendChild(li);
            });
            reviewList.appendChild(ul);
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var n = parseInt(document.getElementById('qCount').value, 10);
        quiz = buildQuiz(n);
        idx = 0; score = 0; wrong = [];
        setupBox.classList.add('d-none');
        finalBox.classList.add('d-none');
        results.classList.remove('d-none');
        renderQ();
    });

    nextBtn.addEventListener('click', function () {
        idx++;
        if (idx >= quiz.length) {
            finishQuiz();
        } else {
            renderQ();
        }
    });

    retryBtn.addEventListener('click', function () {
        setupBox.classList.remove('d-none');
        results.classList.add('d-none');
        finalBox.classList.add('d-none');
        hideError();
    });
})();
</script>
@endsection
