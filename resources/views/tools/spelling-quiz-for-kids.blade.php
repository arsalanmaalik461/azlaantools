@extends('layouts.app')

@section('title', 'Spelling Quiz for Kids - Azlaan Tools')
@section('meta_description', 'A fun spelling quiz game for kids with score tracking. Free English spelling practice for children.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Spelling Quiz for Kids</h1>
            <p class="lead text-muted">A fun spelling game for kids! Listen to the word, write its spelling, and raise your score.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="setupBox">
                        <div class="mb-3">
                            <label for="levelSel" class="form-label fw-semibold">Difficulty level</label>
                            <select class="form-select" id="levelSel">
                                <option value="easy">Easy (short words)</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="qCount" class="form-label fw-semibold">How many questions?</label>
                            <select class="form-select" id="qCount">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="15">15</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary w-100" id="startBtn">Start Quiz</button>
                        <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    </div>

                    <div id="quizBox" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary fs-6" id="qNum">Question 1/10</span>
                            <span class="badge bg-success fs-6" id="scoreBadge">Score: 0</span>
                        </div>
                        <div class="text-center mb-3 p-4 border rounded bg-light">
                            <p class="text-muted mb-2">Listen to the word and write the spelling:</p>
                            <button type="button" class="btn btn-lg btn-outline-primary mb-2" id="speakBtn">🔊 Listen to Word</button>
                            <p class="text-muted small mb-0" id="hintLine"></p>
                        </div>
                        <div class="mb-3">
                            <label for="answerIn" class="form-label fw-semibold">Your spelling</label>
                            <input type="text" class="form-control form-control-lg text-center" id="answerIn" placeholder="Write here..." autocomplete="off" autocapitalize="off" spellcheck="false">
                        </div>
                        <button type="button" class="btn btn-success w-100" id="checkBtn">Check Answer</button>
                        <div id="feedback" class="mt-3"></div>
                    </div>

                    <div id="doneBox" class="d-none text-center">
                        <h4 class="mb-2">🎉 Quiz Complete!</h4>
                        <p class="fs-5">Final score: <strong id="finalScore">0</strong> / <span id="finalTotal">0</span></p>
                        <p class="text-muted" id="finalMsg"></p>
                        <div id="wrongList" class="text-start"></div>
                        <button type="button" class="btn btn-primary mt-3" id="againBtn">Play Again</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the level and number of questions, then press "Start Quiz".</li>
                <li>Press "Listen to Word" — the word will be read aloud (keep your device volume on).</li>
                <li>Write the spelling and press "Check Answer". At the end you will see your score and a list of wrong words.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var levelSel = document.getElementById('levelSel');
    var qCount = document.getElementById('qCount');
    var startBtn = document.getElementById('startBtn');
    var errorBox = document.getElementById('errorBox');
    var setupBox = document.getElementById('setupBox');
    var quizBox = document.getElementById('quizBox');
    var doneBox = document.getElementById('doneBox');
    var qNum = document.getElementById('qNum');
    var scoreBadge = document.getElementById('scoreBadge');
    var speakBtn = document.getElementById('speakBtn');
    var hintLine = document.getElementById('hintLine');
    var answerIn = document.getElementById('answerIn');
    var checkBtn = document.getElementById('checkBtn');
    var feedback = document.getElementById('feedback');
    var finalScore = document.getElementById('finalScore');
    var finalTotal = document.getElementById('finalTotal');
    var finalMsg = document.getElementById('finalMsg');
    var wrongList = document.getElementById('wrongList');
    var againBtn = document.getElementById('againBtn');

    var WORDS = {
        easy: [
            ['cat', 'a small animal that says meow'], ['dog', 'a loyal animal that barks'],
            ['sun', 'gives light in the daytime'], ['book', 'a thing you read'],
            ['ball', 'a round thing you play with'], ['fish', 'an animal that lives in water'],
            ['tree', 'a big green plant'], ['milk', 'a white drink'],
            ['egg', 'a hen lays it'], ['pen', 'used for writing'],
            ['cup', 'a vessel for drinking tea'], ['star', 'shines at night'],
            ['apple', 'a red or green fruit'], ['bird', 'an animal that flies'],
            ['cake', 'a sweet food'], ['door', 'the entrance to a room']
        ],
        medium: [
            ['garden', 'a place with flowers and plants'], ['happy', 'the feeling of joy'],
            ['water', 'a thing you drink'], ['school', 'a place of study'],
            ['friend', 'a person you enjoy being with'], ['house', 'a building you live in'],
            ['light', 'brightness'], ['music', 'songs and tunes'],
            ['table', 'a thing to keep things on'], ['window', 'a glass place in the wall'],
            ['family', 'your household members'], ['summer', 'the hot season'],
            ['winter', 'the cold season'], ['river', 'flowing water'],
            ['mountain', 'a very high hill'], ['picture', 'a photo or drawing']
        ],
        hard: [
            ['beautiful', 'very pretty'], ['because', 'a word that shows reason'],
            ['children', 'kids'], ['important', 'very necessary'],
            ['people', 'persons'], ['together', 'with each other'],
            ['always', 'all the time'], ['never', 'not at any time'],
            ['surprise', 'something unexpected'], ['favorite', 'the most liked'],
            ['journey', 'a trip'], ['knowledge', 'information and learning'],
            ['morning', 'early time of day'], ['evening', 'the time after afternoon'],
            ['wonderful', 'very amazing'], ['celebrate', 'to enjoy a happy occasion']
        ]
    };

    var order = [], idx = 0, score = 0, total = 0, wrong = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

    function shuffle(arr) {
        for (var i = arr.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = arr[i]; arr[i] = arr[j]; arr[j] = t;
        }
        return arr;
    }

    function speak(word) {
        if (!('speechSynthesis' in window)) {
            feedback.innerHTML = '<div class="alert alert-warning">Your browser does not support speech — guess from the hint.</div>';
            return;
        }
        window.speechSynthesis.cancel();
        var u = new SpeechSynthesisUtterance(word);
        u.lang = 'en-US';
        u.rate = 0.85;
        window.speechSynthesis.speak(u);
    }

    function showQuestion() {
        var w = order[idx];
        qNum.textContent = 'Question ' + (idx + 1) + '/' + total;
        scoreBadge.textContent = 'Score: ' + score;
        hintLine.textContent = 'Hint: ' + w[1] + ' (' + w[0].length + ' letters)';
        answerIn.value = '';
        feedback.innerHTML = '';
        checkBtn.disabled = false;
        answerIn.focus();
        speak(w[0]);
    }

    startBtn.addEventListener('click', function () {
        hideError();
        var level = levelSel.value;
        var n = parseInt(qCount.value, 10);
        order = shuffle(WORDS[level].slice()).slice(0, n);
        if (!order.length) { showError('Words could not be loaded. Try again.'); return; }
        idx = 0; score = 0; total = order.length; wrong = [];
        setupBox.classList.add('d-none');
        doneBox.classList.add('d-none');
        quizBox.classList.remove('d-none');
        showQuestion();
    });

    speakBtn.addEventListener('click', function () { speak(order[idx][0]); });

    function check() {
        var w = order[idx][0];
        var ans = answerIn.value.trim().toLowerCase();
        if (!ans) { feedback.innerHTML = '<div class="alert alert-warning">Write the spelling first.</div>'; return; }
        checkBtn.disabled = true;
        if (ans === w) {
            score++;
            feedback.innerHTML = '<div class="alert alert-success">✅ Correct! Great job!</div>';
        } else {
            wrong.push([w, ans]);
            feedback.innerHTML = '<div class="alert alert-danger">❌ Wrong. The correct spelling is: <strong>' + esc(w) + '</strong></div>';
            speak(w);
        }
        scoreBadge.textContent = 'Score: ' + score;
        setTimeout(function () {
            idx++;
            if (idx < total) { showQuestion(); } else { finish(); }
        }, 1800);
    }
    checkBtn.addEventListener('click', check);
    answerIn.addEventListener('keydown', function (e) { if (e.key === 'Enter') check(); });

    function finish() {
        quizBox.classList.add('d-none');
        doneBox.classList.remove('d-none');
        finalScore.textContent = score;
        finalTotal.textContent = total;
        var pct = total ? (score / total) * 100 : 0;
        finalMsg.textContent = pct === 100 ? 'Perfect! You are a spelling champion!' : (pct >= 70 ? 'Very good! A little more practice!' : 'Keep trying — practice will make you better!');
        if (wrong.length) {
            var h = '<h6 class="mt-3">Wrong words — remember them again:</h6><ul class="list-group">';
            wrong.forEach(function (p) {
                h += '<li class="list-group-item"><strong>' + esc(p[0]) + '</strong> <span class="text-muted">(you wrote: ' + esc(p[1]) + ')</span></li>';
            });
            wrongList.innerHTML = h + '</ul>';
        } else { wrongList.innerHTML = ''; }
    }

    againBtn.addEventListener('click', function () {
        doneBox.classList.add('d-none');
        quizBox.classList.add('d-none');
        setupBox.classList.remove('d-none');
    });
})();
</script>
@endsection
