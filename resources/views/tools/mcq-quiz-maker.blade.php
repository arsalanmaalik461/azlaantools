@extends('layouts.app')

@section('title', 'MCQ Quiz Maker - Azlaan Tools')
@section('meta_description', 'Make your own MCQ quiz and take a self-test. Free online MCQ quiz maker for entry test practice.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">MCQ Quiz Maker</h1>
            <p class="lead text-muted">Add your MCQs and take a self-test — great for entry test (MDCAT, ECAT, NTS) practice.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="quizTabs">
                        <li class="nav-item"><button type="button" class="nav-link active" id="tabBuild">1. Make Questions</button></li>
                        <li class="nav-item"><button type="button" class="nav-link" id="tabTake">2. Take Test</button></li>
                    </ul>

                    <div id="paneBuild">
                        <div class="mb-3">
                            <label for="qText" class="form-label fw-semibold">Question</label>
                            <input type="text" class="form-control" id="qText" placeholder="e.g. What is the capital of Pakistan?">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2"><div class="input-group"><span class="input-group-text">A</span><input type="text" class="form-control optIn" id="opt0" placeholder="Option A"></div></div>
                            <div class="col-md-6 mb-2"><div class="input-group"><span class="input-group-text">B</span><input type="text" class="form-control optIn" id="opt1" placeholder="Option B"></div></div>
                            <div class="col-md-6 mb-2"><div class="input-group"><span class="input-group-text">C</span><input type="text" class="form-control optIn" id="opt2" placeholder="Option C"></div></div>
                            <div class="col-md-6 mb-2"><div class="input-group"><span class="input-group-text">D</span><input type="text" class="form-control optIn" id="opt3" placeholder="Option D"></div></div>
                        </div>
                        <div class="mb-3">
                            <label for="correctSel" class="form-label fw-semibold">Correct answer</label>
                            <select class="form-select" id="correctSel">
                                <option value="0">A</option><option value="1">B</option><option value="2">C</option><option value="3">D</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary w-100 mb-3" id="addBtn">Add Question</button>

                        <div class="mb-3">
                            <label for="importBox" class="form-label fw-semibold">Or import many questions at once</label>
                            <textarea class="form-control" id="importBox" rows="6" placeholder="Q: What is the capital of Pakistan?&#10;A) Karachi&#10;B) Lahore&#10;C) Islamabad&#10;D) Peshawar&#10;Ans: C"></textarea>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="importBtn">Import</button>
                        </div>

                        <h6>Saved questions (<span id="qCount">0</span>)</h6>
                        <div id="qList" class="mb-2"></div>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearBtn">Clear All</button>
                    </div>

                    <div id="paneTake" class="d-none">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label for="timerSel" class="form-label fw-semibold">Timer</label>
                                <select class="form-select" id="timerSel">
                                    <option value="0">No timer</option>
                                    <option value="60">1 minute / question</option>
                                    <option value="120">2 minutes / question</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="shuffleChk" class="form-label fw-semibold">Options</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="shuffleChk" checked>
                                    <label class="form-check-label" for="shuffleChk">Shuffle questions</label>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success w-100 mb-3" id="startBtn">Start Test</button>
                        <div id="quizArea" class="d-none">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold" id="qProg"></span>
                                <span class="badge bg-warning text-dark d-none" id="qTimer"></span>
                            </div>
                            <div class="progress mb-3" style="height:8px"><div class="progress-bar" id="qBar" style="width:0%"></div></div>
                            <h5 id="qqText"></h5>
                            <div id="qqOpts" class="list-group mb-3"></div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="prevBtn">Previous</button>
                                <button type="button" class="btn btn-outline-secondary" id="nextBtn">Next</button>
                                <button type="button" class="btn btn-danger ms-auto" id="submitBtn">Submit Test</button>
                            </div>
                        </div>
                        <div id="resultArea" class="d-none mt-3"></div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>In the <strong>Make Questions</strong> tab, add MCQs or bring many at once with the import format.</li>
                <li>In the <strong>Take Test</strong> tab, set the timer and start the test.</li>
                <li>After submit, see the score, percentage and the correct answer of each question.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var LS_KEY = 'mcqQuizMaker';
    var questions = [];
    try {
        var saved = localStorage.getItem(LS_KEY);
        if (saved) { questions = JSON.parse(saved) || []; }
    } catch (e) { questions = []; }

    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var optInputs = [
        document.getElementById('opt0'), document.getElementById('opt1'),
        document.getElementById('opt2'), document.getElementById('opt3')
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function persist() {
        try { localStorage.setItem(LS_KEY, JSON.stringify(questions)); } catch (e) {}
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    var RP = String.fromCharCode(41);

    var tabBuild = document.getElementById('tabBuild');
    var tabTake = document.getElementById('tabTake');
    var paneBuild = document.getElementById('paneBuild');
    var paneTake = document.getElementById('paneTake');
    tabBuild.addEventListener('click', function () {
        tabBuild.classList.add('active'); tabTake.classList.remove('active');
        paneBuild.classList.remove('d-none'); paneTake.classList.add('d-none');
    });
    tabTake.addEventListener('click', function () {
        tabTake.classList.add('active'); tabBuild.classList.remove('active');
        paneTake.classList.remove('d-none'); paneBuild.classList.add('d-none');
    });

    function renderList() {
        document.getElementById('qCount').textContent = questions.length;
        var html = '';
        questions.forEach(function (q, i) {
            html += '<div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-start">' +
                '<div><strong>' + (i + 1) + '. ' + esc(q.q) + '</strong><br><span class="small text-muted">' +
                q.opts.map(function (o, j) { return 'ABCD'[j] + RP + ' ' + esc(o); }).join(' • ') +
                ' — <span class="text-success fw-semibold">Correct: ' + 'ABCD'[q.correct] + '</span></span></div>' +
                '<button type="button" class="btn btn-sm btn-outline-danger ms-2" data-del="' + i + '">X</button></div>';
        });
        document.getElementById('qList').innerHTML = html || '<p class="text-muted small">No questions yet. Add from above.</p>';
        var dels = document.querySelectorAll('[data-del]');
        for (var k = 0; k < dels.length; k++) {
            dels[k].addEventListener('click', function () {
                questions.splice(parseInt(this.getAttribute('data-del'), 10), 1);
                persist(); renderList();
            });
        }
    }

    document.getElementById('addBtn').addEventListener('click', function () {
        hideError();
        var q = document.getElementById('qText').value.trim();
        var opts = [optInputs[0].value.trim(), optInputs[1].value.trim(), optInputs[2].value.trim(), optInputs[3].value.trim()];
        var correct = parseInt(document.getElementById('correctSel').value, 10);
        if (!q) { showError('Enter the question.'); return; }
        if (opts.some(function (o) { return !o; })) { showError('Enter all four options.'); return; }
        questions.push({ q: q, opts: opts, correct: correct });
        persist(); renderList();
        document.getElementById('qText').value = '';
        for (var i = 0; i < 4; i++) { optInputs[i].value = ''; }
    });

    document.getElementById('importBtn').addEventListener('click', function () {
        hideError();
        var text = document.getElementById('importBox').value.trim();
        if (!text) { showError('First enter questions in the import format.'); return; }
        var blocks = text.split(/\n\s*\n/), added = 0;
        blocks.forEach(function (b) {
            var lines = b.split('\n').map(function (l) { return l.trim(); }).filter(function (l) { return l; });
            var q = '', opts = [], correct = -1;
            lines.forEach(function (l) {
                var qm = l.match(/^Q\s*[:.\-]?\s*(.+)/i);
                var om = l.match(/^([A-D])\s*[:.\-]\s*(.+)/i);
                var am = l.match(/^(Ans|Answer)\s*[:.\-]?\s*([A-D])/i);
                if (qm) { q = qm[1]; }
                else if (om) { opts['ABCD'.indexOf(om[1].toUpperCase())] = om[2]; }
                else if (am) { correct = 'ABCD'.indexOf(am[2].toUpperCase()); }
            });
            if (q && opts.length === 4 && opts.every(function (o) { return o; }) && correct >= 0) {
                questions.push({ q: q, opts: opts, correct: correct });
                added++;
            }
        });
        persist(); renderList();
        if (!added) { showError('No question could be imported. Check the format — Q: line, A B C D options, and the Ans: line.'); return; }
        document.getElementById('importBox').value = '';
        showError('');
        errorBox.classList.add('d-none');
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        if (confirm('Delete all questions?')) { questions = []; persist(); renderList(); }
    });

    // ---- Quiz ----
    var quiz = [], answers = [], qi = 0, timerId = null, timeLeft = 0;
    var quizArea = document.getElementById('quizArea');
    var resultArea = document.getElementById('resultArea');

    function stopTimer() { if (timerId) { clearInterval(timerId); timerId = null; } }

    document.getElementById('startBtn').addEventListener('click', function () {
        hideError();
        resultArea.classList.add('d-none');
        if (questions.length < 1) { showError('First make at least 1 question.'); return; }
        quiz = questions.slice();
        if (document.getElementById('shuffleChk').checked) {
            for (var i = quiz.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var t = quiz[i]; quiz[i] = quiz[j]; quiz[j] = t;
            }
        }
        answers = quiz.map(function () { return -1; });
        qi = 0;
        stopTimer();
        var perQ = parseInt(document.getElementById('timerSel').value, 10);
        var qTimer = document.getElementById('qTimer');
        if (perQ > 0) {
            timeLeft = perQ * quiz.length;
            qTimer.classList.remove('d-none');
            timerId = setInterval(function () {
                timeLeft--;
                var mm = Math.floor(timeLeft / 60), ss = timeLeft % 60;
                qTimer.textContent = '⏱ ' + mm + ':' + (ss < 10 ? '0' : '') + ss;
                if (timeLeft <= 0) { stopTimer(); submitQuiz(); }
            }, 1000);
        } else { qTimer.classList.add('d-none'); }
        quizArea.classList.remove('d-none');
        renderQ();
    });

    function renderQ() {
        var q = quiz[qi];
        document.getElementById('qProg').textContent = 'Question ' + (qi + 1) + ' / ' + quiz.length;
        document.getElementById('qBar').style.width = ((qi + 1) / quiz.length * 100) + '%';
        document.getElementById('qqText').textContent = q.q;
        var wrap = document.getElementById('qqOpts');
        wrap.innerHTML = '';
        q.opts.forEach(function (o, i) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'list-group-item list-group-item-action' + (answers[qi] === i ? ' active' : '');
            b.textContent = 'ABCD'[i] + RP + ' ' + o;
            b.addEventListener('click', function () {
                answers[qi] = i;
                renderQ();
            });
            wrap.appendChild(b);
        });
        document.getElementById('prevBtn').disabled = qi === 0;
        document.getElementById('nextBtn').disabled = qi === quiz.length - 1;
    }

    document.getElementById('prevBtn').addEventListener('click', function () { if (qi > 0) { qi--; renderQ(); } });
    document.getElementById('nextBtn').addEventListener('click', function () { if (qi < quiz.length - 1) { qi++; renderQ(); } });
    document.getElementById('submitBtn').addEventListener('click', function () {
        if (confirm('Submit the test?')) { submitQuiz(); }
    });

    function submitQuiz() {
        stopTimer();
        quizArea.classList.add('d-none');
        var score = 0, html = '';
        quiz.forEach(function (q, i) {
            var ok = answers[i] === q.correct;
            if (ok) { score++; }
            html += '<div class="border rounded p-2 mb-2 ' + (ok ? 'border-success' : 'border-danger') + '">' +
                '<strong>' + (i + 1) + '. ' + esc(q.q) + '</strong><br>' +
                '<span class="small">Your answer: ' + (answers[i] >= 0 ? 'ABCD'[answers[i]] + RP + ' ' + esc(q.opts[answers[i]]) : '<em>not answered</em>') + '</span><br>' +
                (ok ? '<span class="badge bg-success">Correct ✓</span>'
                    : '<span class="badge bg-danger">Wrong ✗</span> <span class="small">Correct answer: <strong>' + 'ABCD'[q.correct] + RP + ' ' + esc(q.opts[q.correct]) + '</strong></span>') +
                '</div>';
        });
        var pct = Math.round(score / quiz.length * 100);
        var grade = pct >= 80 ? 'Excellent!' : pct >= 60 ? 'Good!' : pct >= 40 ? 'You can do better.' : 'Prepare again and retry.';
        resultArea.innerHTML = '<div class="card"><div class="card-body text-center">' +
            '<h4>Score: ' + score + ' / ' + quiz.length + '</h4>' +
            '<div class="display-6 fw-bold text-primary">' + pct + '%</div>' +
            '<p class="text-muted">' + grade + '</p>' +
            '<button type="button" class="btn btn-outline-primary btn-sm" id="retakeBtn">Retake Test</button>' +
            '</div></div><h6 class="mt-3">Answer review</h6>' + html;
        resultArea.classList.remove('d-none');
        document.getElementById('retakeBtn').addEventListener('click', function () {
            resultArea.classList.add('d-none');
            document.getElementById('startBtn').click();
        });
        resultArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    renderList();
})();
</script>
@endsection
