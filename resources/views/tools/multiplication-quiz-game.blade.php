@extends('layouts.app')

@section('title', 'Multiplication Quiz Game - Azlaan Tools')
@section('meta_description', 'Practice multiplication tables with a timed quiz game. Track speed, score and streak free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Multiplication Quiz Game</h1>
            <p class="lead text-muted">Learn multiplication tables with a timed quiz — track speed, score and streak. For children and adults.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="setupScreen">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="minTable" class="form-label fw-semibold">Table from</label>
                                <select class="form-select" id="minTable"></select>
                            </div>
                            <div class="col-6">
                                <label for="maxTable" class="form-label fw-semibold">Table to</label>
                                <select class="form-select" id="maxTable"></select>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="qCount" class="form-label fw-semibold">Questions</label>
                                <select class="form-select" id="qCount">
                                    <option value="10">10 questions</option>
                                    <option value="20" selected>20 questions</option>
                                    <option value="30">30 questions</option>
                                    <option value="50">50 questions</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="timeLimit" class="form-label fw-semibold">Time limit</label>
                                <select class="form-select" id="timeLimit">
                                    <option value="60">60 seconds</option>
                                    <option value="120" selected>120 seconds</option>
                                    <option value="180">180 seconds</option>
                                    <option value="0">No limit</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary w-100" id="startBtn">Start Quiz</button>
                        <p class="small text-muted mt-2 mb-0" id="bestLine">Best score: <strong id="bestScore">0</strong></p>
                    </div>

                    <div id="gameScreen" class="d-none text-center">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-primary fs-6" id="qNum">1/20</span>
                            <span class="badge bg-warning text-dark fs-6" id="timerBadge">120s</span>
                            <span class="badge bg-success fs-6" id="scoreBadge">Score: 0</span>
                        </div>
                        <div class="display-4 fw-bold my-4" id="question">7 x 8 = ?</div>
                        <div class="row justify-content-center">
                            <div class="col-8 col-md-5">
                                <input type="number" class="form-control form-control-lg text-center" id="answerInput" placeholder="Answer" autocomplete="off">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-lg mt-3 px-5" id="submitBtn">Submit Answer</button>
                        <div class="mt-2 fw-semibold" id="feedback" style="min-height: 1.5rem;"></div>
                        <p class="small text-muted mt-1 mb-0">Streak: <strong id="streak">0</strong> correct answers in a row</p>
                    </div>

                    <div id="endScreen" class="d-none text-center">
                        <h2 class="h4">Quiz Finished!</h2>
                        <div class="row g-3 my-3">
                            <div class="col-4"><div class="border rounded p-2"><div class="fw-bold fs-4" id="finalScore">0</div><div class="small text-muted">Score</div></div></div>
                            <div class="col-4"><div class="border rounded p-2"><div class="fw-bold fs-4" id="finalAcc">0%</div><div class="small text-muted">Accuracy</div></div></div>
                            <div class="col-4"><div class="border rounded p-2"><div class="fw-bold fs-4" id="finalStreak">0</div><div class="small text-muted">Best streak</div></div></div>
                        </div>
                        <p class="text-muted" id="endMsg"></p>
                        <button type="button" class="btn btn-primary w-100" id="againBtn">Play Again</button>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the range of tables and the number of questions.</li>
                <li>Start the quiz and answer each question quickly.</li>
                <li>A correct answer grows the streak — a wrong answer breaks it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var minTable = document.getElementById('minTable');
    var maxTable = document.getElementById('maxTable');
    var qCount = document.getElementById('qCount');
    var timeLimit = document.getElementById('timeLimit');
    var startBtn = document.getElementById('startBtn');
    var bestScore = document.getElementById('bestScore');
    var setupScreen = document.getElementById('setupScreen');
    var gameScreen = document.getElementById('gameScreen');
    var endScreen = document.getElementById('endScreen');
    var qNum = document.getElementById('qNum');
    var timerBadge = document.getElementById('timerBadge');
    var scoreBadge = document.getElementById('scoreBadge');
    var question = document.getElementById('question');
    var answerInput = document.getElementById('answerInput');
    var submitBtn = document.getElementById('submitBtn');
    var feedback = document.getElementById('feedback');
    var streakEl = document.getElementById('streak');
    var errorBox = document.getElementById('errorBox');
    var againBtn = document.getElementById('againBtn');

    for (var t = 2; t <= 12; t++) {
        var o1 = document.createElement('option');
        o1.value = t; o1.textContent = t + ' table';
        if (t === 2) o1.selected = true;
        minTable.appendChild(o1);
        var o2 = document.createElement('option');
        o2.value = t; o2.textContent = t + ' table';
        if (t === 9) o2.selected = true;
        maxTable.appendChild(o2);
    }

    var BEST_KEY = 'azlaan_mulquiz_best';
    var best = 0;
    try { best = parseInt(localStorage.getItem(BEST_KEY) || '0', 10) || 0; } catch (e) { best = 0; }
    bestScore.textContent = best;

    var state = null;
    var timerId = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function randInt(a, b) { return Math.floor(Math.random() * (b - a + 1)) + a; }

    function newQuestion() {
        var a = randInt(state.min, state.max);
        var b = randInt(2, 12);
        if (Math.random() < 0.5) { var tmp = a; a = b; b = tmp; }
        state.cur = { a: a, b: b, ans: a * b };
        question.textContent = a + ' x ' + b + ' = ?';
        answerInput.value = '';
        answerInput.focus();
        qNum.textContent = (state.done + 1) + '/' + state.total;
    }

    function tick() {
        state.left--;
        timerBadge.textContent = state.left + 's';
        if (state.left <= 10) { timerBadge.classList.remove('bg-warning', 'text-dark'); timerBadge.classList.add('bg-danger'); }
        if (state.left <= 0) { finish(); }
    }

    function startGame() {
        hideError();
        var mn = parseInt(minTable.value, 10);
        var mx = parseInt(maxTable.value, 10);
        if (mn > mx) { showError('The start table cannot be greater than the end table.'); return; }
        state = {
            min: mn, max: mx,
            total: parseInt(qCount.value, 10),
            done: 0, score: 0, correct: 0,
            streak: 0, bestStreak: 0,
            left: parseInt(timeLimit.value, 10)
        };
        setupScreen.classList.add('d-none');
        endScreen.classList.add('d-none');
        gameScreen.classList.remove('d-none');
        scoreBadge.textContent = 'Score: 0';
        streakEl.textContent = '0';
        feedback.textContent = '';
        if (state.left > 0) {
            timerBadge.textContent = state.left + 's';
            timerBadge.classList.remove('d-none');
            timerBadge.classList.add('bg-warning', 'text-dark');
            timerBadge.classList.remove('bg-danger');
            timerId = setInterval(tick, 1000);
        } else {
            timerBadge.classList.add('d-none');
        }
        newQuestion();
    }

    function submit() {
        if (!state) return;
        var raw = answerInput.value.trim();
        if (raw === '') { feedback.textContent = 'Enter your answer first.'; feedback.className = 'mt-2 fw-semibold text-muted'; return; }
        var val = parseInt(raw, 10);
        state.done++;
        if (val === state.cur.ans) {
            var pts = 10 + Math.min(state.streak, 10);
            state.score += pts;
            state.correct++;
            state.streak++;
            state.bestStreak = Math.max(state.bestStreak, state.streak);
            feedback.textContent = 'Correct! +' + pts + ' points';
            feedback.className = 'mt-2 fw-semibold text-success';
        } else {
            state.streak = 0;
            feedback.textContent = 'Wrong! The correct answer was ' + state.cur.ans;
            feedback.className = 'mt-2 fw-semibold text-danger';
        }
        scoreBadge.textContent = 'Score: ' + state.score;
        streakEl.textContent = state.streak;
        if (state.done >= state.total) {
            setTimeout(finish, 600);
        } else {
            setTimeout(newQuestion, 600);
        }
    }

    function finish() {
        if (timerId) { clearInterval(timerId); timerId = null; }
        gameScreen.classList.add('d-none');
        endScreen.classList.remove('d-none');
        document.getElementById('finalScore').textContent = state.score;
        var acc = state.done ? Math.round((state.correct / state.done) * 100) : 0;
        document.getElementById('finalAcc').textContent = acc + '%';
        document.getElementById('finalStreak').textContent = state.bestStreak;
        var msg;
        if (acc >= 90) msg = 'Excellent! You know the tables very well.';
        else if (acc >= 70) msg = 'Very good! Practice a little more.';
        else if (acc >= 40) msg = 'OK — 10 minutes of daily practice will make it better.';
        else msg = 'Do not give up — start with the small tables.';
        var isBest = state.score > best;
        if (isBest) {
            best = state.score;
            try { localStorage.setItem(BEST_KEY, String(best)); } catch (e) { /* noop */ }
            bestScore.textContent = best;
            msg += ' New best score!';
        }
        document.getElementById('endMsg').textContent = msg;
        state = null;
    }

    startBtn.addEventListener('click', startGame);
    submitBtn.addEventListener('click', submit);
    answerInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); submit(); }
    });
    againBtn.addEventListener('click', function () {
        endScreen.classList.add('d-none');
        setupScreen.classList.remove('d-none');
        hideError();
    });
})();
</script>
@endsection
