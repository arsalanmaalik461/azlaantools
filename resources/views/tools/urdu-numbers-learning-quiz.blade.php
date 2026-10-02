@extends('layouts.app')

@section('title', 'Urdu Numbers Learning Quiz - Azlaan Tools')
@section('meta_description', 'Learn Urdu counting 1 to 100 with a chart and interactive quiz, free online for kids and learners.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Urdu Numbers Learning Quiz</h1>
            <p class="lead text-muted">Learn Urdu counting from 1 to 100 — read the chart, then test yourself with the quiz.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-outline-primary" id="tabChart">Chart (1-100)</button>
                        <button type="button" class="btn btn-outline-primary" id="tabQuiz">Start Quiz</button>
                    </div>

                    <div id="chartPane">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="chartFrom" class="form-label fw-semibold">From</label>
                                <input type="number" class="form-control" id="chartFrom" value="1" min="1" max="100">
                            </div>
                            <div class="col-md-6">
                                <label for="chartTo" class="form-label fw-semibold">To</label>
                                <input type="number" class="form-control" id="chartTo" value="20" min="1" max="100">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary w-100" id="goBtn">View Chart</button>
                        <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                        <div id="chartResults" class="mt-4"></div>
                    </div>

                    <div id="quizPane" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Score: <span id="qScore">0</span> / <span id="qTotal">0</span></span>
                            <span class="small text-muted">Question <span id="qNum">1</span> / 10</span>
                        </div>
                        <div class="card bg-light mb-3">
                            <div class="card-body text-center py-4">
                                <div class="display-4 fw-bold" id="qQuestion">—</div>
                                <div class="small text-muted mt-1" id="qHint"></div>
                            </div>
                        </div>
                        <div class="row g-2" id="qOptions"></div>
                        <div class="alert mt-3 d-none" id="qFeedback" role="alert"></div>
                        <button type="button" class="btn btn-primary w-100 mt-3 d-none" id="qNext">Next Question</button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="qRestart">Restart Quiz</button>
                        <div id="results" class="d-none mt-3"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>In the <strong>Chart</strong> tab, select a range and read the Urdu numbers and their names.</li>
                <li>In the <strong>Quiz</strong> tab, take the 10-question quiz — you get instant feedback on every answer.</li>
                <li>Try to reach a score of 10/10!</li>
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

    var URDU_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    var ONES = ['', 'Ek', 'Do', 'Teen', 'Char', 'Panch', 'Chhe', 'Saat', 'Aath', 'Nau'];
    var TENS = ['', 'Das', 'Bees', 'Tees', 'Chalees', 'Pachas', 'Saath', 'Sattar', 'Assi', 'Nabbe'];
    var TEENS = ['Das', 'Gyarah', 'Barah', 'Terah', 'Chaudah', 'Pandrah', 'Solah', 'Satrah', 'Atharah', 'Unnis'];

    function urduDigits(n) {
        return String(n).split('').map(function (d) { return URDU_DIGITS[Number(d)]; }).join('');
    }
    function urduName(n) {
        if (n === 100) return 'So';
        if (n < 10) return ONES[n];
        if (n >= 10 && n < 20) return TEENS[n - 10];
        var t = Math.floor(n / 10), o = n % 10;
        return o === 0 ? TENS[t] : TENS[t] + ' ' + ONES[o];
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    // Tabs
    var tabChart = document.getElementById('tabChart');
    var tabQuiz = document.getElementById('tabQuiz');
    var chartPane = document.getElementById('chartPane');
    var quizPane = document.getElementById('quizPane');
    function setTab(which) {
        var chart = which === 'chart';
        chartPane.classList.toggle('d-none', !chart);
        quizPane.classList.toggle('d-none', chart);
        tabChart.classList.toggle('btn-primary', chart);
        tabChart.classList.toggle('btn-outline-primary', !chart);
        tabQuiz.classList.toggle('btn-primary', !chart);
        tabQuiz.classList.toggle('btn-outline-primary', chart);
    }
    tabChart.addEventListener('click', function () { setTab('chart'); });
    tabQuiz.addEventListener('click', function () { setTab('quiz'); startQuiz(); });

    // Chart
    goBtn.addEventListener('click', function () {
        hideError();
        var from = Number(document.getElementById('chartFrom').value);
        var to = Number(document.getElementById('chartTo').value);
        if (!from || !to || from < 1 || to > 100 || from > to) {
            showError('Enter a range within 1 to 100, and "from" must be smaller than "to".'); return;
        }
        if (to - from > 49) { showError('See max 50 numbers at a time (better for kids).'); return; }
        var html = '<div class="row g-2">';
        for (var n = from; n <= to; n++) {
            html += '<div class="col-4 col-md-3"><div class="card text-center h-100"><div class="card-body py-2">' +
                '<div class="h4 mb-0" dir="rtl">' + urduDigits(n) + '</div>' +
                '<div class="small text-muted">' + n + ' · ' + urduName(n) + '</div></div></div></div>';
        }
        html += '</div>';
        document.getElementById('chartResults').innerHTML = html;
    });

    // Quiz
    var qScore = 0, qTotal = 0, qRound = 0, qAnswered = false, current = null;
    var TOTAL_ROUNDS = 10;
    function rand(a, b) { return Math.floor(Math.random() * (b - a + 1)) + a; }
    function shuffle(arr) {
        for (var i = arr.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = arr[i]; arr[i] = arr[j]; arr[j] = t;
        }
        return arr;
    }
    function updateScore() {
        document.getElementById('qScore').textContent = qScore;
        document.getElementById('qTotal').textContent = qTotal;
        document.getElementById('qNum').textContent = Math.min(qRound, TOTAL_ROUNDS);
    }
    function startQuiz() {
        qScore = 0; qTotal = 0; qRound = 0;
        document.getElementById('results').classList.add('d-none');
        nextQuestion();
    }
    function nextQuestion() {
        if (qRound >= TOTAL_ROUNDS) { endQuiz(); return; }
        qRound++;
        qAnswered = false;
        var num = rand(1, 100);
        var mode = rand(0, 1); // 0: show urdu digit, pick name; 1: show number, pick urdu digit
        current = { num: num, mode: mode };
        document.getElementById('qQuestion').textContent = mode === 0 ? urduDigits(num) : String(num);
        document.getElementById('qQuestion').setAttribute('dir', mode === 0 ? 'rtl' : 'ltr');
        document.getElementById('qHint').textContent = mode === 0 ? 'Choose the name of this Urdu number' : 'Choose the Urdu number for this number';
        var opts = [num];
        while (opts.length < 4) {
            var c = num + rand(-10, 10);
            if (c >= 1 && c <= 100 && opts.indexOf(c) === -1) opts.push(c);
        }
        shuffle(opts);
        var wrap = document.getElementById('qOptions');
        wrap.innerHTML = '';
        opts.forEach(function (o) {
            var col = document.createElement('div');
            col.className = 'col-6';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-dark w-100 py-2';
            btn.textContent = mode === 0 ? urduName(o) : urduDigits(o);
            if (mode === 1) btn.setAttribute('dir', 'rtl');
            btn.addEventListener('click', function () { answer(o, btn); });
            col.appendChild(btn);
            wrap.appendChild(col);
        });
        var fb = document.getElementById('qFeedback');
        fb.classList.add('d-none');
        document.getElementById('qNext').classList.add('d-none');
        updateScore();
    }
    function answer(pick, btn) {
        if (qAnswered) return;
        qAnswered = true;
        qTotal++;
        var fb = document.getElementById('qFeedback');
        fb.classList.remove('d-none', 'alert-success', 'alert-danger');
        if (pick === current.num) {
            qScore++;
            fb.classList.add('alert-success');
            fb.textContent = 'Correct! ' + urduDigits(current.num) + ' = ' + urduName(current.num) + ' (' + current.num + ')';
            btn.classList.remove('btn-outline-dark');
            btn.classList.add('btn-success');
        } else {
            fb.classList.add('alert-danger');
            fb.textContent = 'Wrong. Correct answer: ' + urduDigits(current.num) + ' = ' + urduName(current.num) + ' (' + current.num + ')';
            btn.classList.remove('btn-outline-dark');
            btn.classList.add('btn-danger');
        }
        updateScore();
        document.getElementById('qNext').classList.remove('d-none');
    }
    function endQuiz() {
        document.getElementById('qQuestion').textContent = 'Done!';
        document.getElementById('qHint').textContent = '';
        document.getElementById('qOptions').innerHTML = '';
        var fb = document.getElementById('qFeedback');
        fb.classList.remove('d-none');
        fb.classList.add(qScore >= 7 ? 'alert-success' : 'alert-info');
        fb.textContent = 'Your score: ' + qScore + ' / ' + TOTAL_ROUNDS + (qScore >= 7 ? ' — Great!' : ' — Try again after reading the chart.');
        document.getElementById('qNext').classList.add('d-none');
        var res = document.getElementById('results');
        res.classList.remove('d-none');
    }
    document.getElementById('qNext').addEventListener('click', function () { nextQuestion(); });
    document.getElementById('qRestart').addEventListener('click', function () { startQuiz(); });
    setTab('chart');
})();
</script>
@endsection
