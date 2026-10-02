@extends('layouts.app')

@section('title', 'Reaction Time Test - Azlaan Tools')
@section('meta_description', 'Test your reflex speed in milliseconds. Free online reaction time test — how fast can you click?')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Reaction Time Test</h1>
            <p class="lead text-muted">Check your reflexes — click as soon as the screen turns green. Speed is measured in milliseconds.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="attempts" class="form-label fw-semibold">Attempts</label>
                        <select class="form-select" id="attempts">
                            <option value="3">3 attempts</option>
                            <option value="5" selected>5 attempts</option>
                            <option value="10">10 attempts</option>
                        </select>
                    </div>
                    <div id="gameBox" class="border rounded d-flex align-items-center justify-content-center text-center"
                         style="height: 220px; cursor: pointer; user-select: none; background: #f8f9fa;" role="button" tabindex="0">
                        <div id="gameText" class="fs-4 fw-semibold text-muted px-3">Click "Start" to begin the test</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Start</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Results</h2>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody id="resultTable"></tbody>
                            </table>
                        </div>
                        <h2 class="h6">Attempts (ms)</h2>
                        <div id="attemptList" class="d-flex flex-wrap gap-2"></div>
                        <button type="button" class="btn btn-outline-primary w-100 mt-3" id="resetBtn">Play Again</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the number of attempts and press Start.</li>
                <li>The box will be <strong>red</strong> — wait, then click as soon as it turns <strong>green</strong>.</li>
                <li>If you click before green, that attempt fails (false start).</li>
                <li>After all attempts, you will see your best and average reaction time.</li>
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
    var resetBtn = document.getElementById('resetBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var gameBox = document.getElementById('gameBox');
    var gameText = document.getElementById('gameText');
    var resultTable = document.getElementById('resultTable');
    var attemptList = document.getElementById('attemptList');

    var STATE = { IDLE: 0, WAITING: 1, READY: 2, DONE: 3 };
    var state = STATE.IDLE;
    var timerId = null;
    var startTime = 0;
    var scores = [];
    var totalAttempts = 5;
    var current = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setBox(bg, color, text) {
        gameBox.style.background = bg;
        gameText.textContent = text;
        gameText.className = 'fs-4 fw-semibold px-3 ' + (color || 'text-dark');
    }
    function row(label, value) {
        var tr = document.createElement('tr');
        var th = document.createElement('th');
        th.textContent = label;
        th.scope = 'row';
        var td = document.createElement('td');
        td.textContent = value;
        tr.appendChild(th);
        tr.appendChild(td);
        return tr;
    }
    function badge(ms) {
        var span = document.createElement('span');
        span.className = 'badge bg-secondary fs-6';
        span.textContent = ms + ' ms';
        return span;
    }
    function badgeFail() {
        var span = document.createElement('span');
        span.className = 'badge bg-danger fs-6';
        span.textContent = 'Too early!';
        return span;
    }

    function nextAttempt() {
        if (current >= totalAttempts) {
            finish();
            return;
        }
        current++;
        state = STATE.WAITING;
        setBox('#dc3545', 'text-white', 'Wait for green... (attempt ' + current + '/' + totalAttempts + ')');
        var delay = 1000 + Math.random() * 3000;
        timerId = setTimeout(function () {
            state = STATE.READY;
            setBox('#198754', 'text-white', 'CLICK NOW!');
            startTime = performance.now();
        }, delay);
    }

    function finish() {
        state = STATE.DONE;
        clearTimeout(timerId);
        setBox('#f8f9fa', 'text-muted', 'Test complete! Results below.');
        goBtn.classList.add('d-none');
        var valid = scores.filter(function (s) { return s !== null; });
        resultTable.innerHTML = '';
        attemptList.innerHTML = '';
        scores.forEach(function (s) {
            attemptList.appendChild(s === null ? badgeFail() : badge(s));
        });
        resultTable.appendChild(row('Total attempts', totalAttempts));
        resultTable.appendChild(row('Valid clicks', valid.length));
        resultTable.appendChild(row('False starts', scores.length - valid.length));
        if (valid.length) {
            var best = Math.min.apply(null, valid);
            var avg = valid.reduce(function (a, b) { return a + b; }, 0) / valid.length;
            resultTable.appendChild(row('Best reaction', best + ' ms'));
            resultTable.appendChild(row('Average reaction', Math.round(avg) + ' ms'));
            var verdict = best < 200 ? 'Excellent — gamer level reflexes!' :
                best < 300 ? 'Good — faster than average!' :
                best < 450 ? 'Average — okay reflexes.' :
                'Slow — try again, practice will improve it.';
            resultTable.appendChild(row('Verdict', verdict));
        } else {
            resultTable.appendChild(row('Verdict', 'No valid clicks — wait for green, do not click early.'));
        }
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function onBoxClick() {
        hideError();
        if (state === STATE.IDLE) {
            showError('Press Start first.');
            return;
        }
        if (state === STATE.WAITING) {
            clearTimeout(timerId);
            scores.push(null);
            setBox('#6c757d', 'text-white', 'Too early! Click "Next" to continue.');
            state = STATE.DONE; // pause until next
            nextBtnMode();
            return;
        }
        if (state === STATE.READY) {
            var ms = Math.round(performance.now() - startTime);
            scores.push(ms);
            setBox('#0d6efd', 'text-white', ms + ' ms! Click for next attempt.');
            state = STATE.DONE;
            nextBtnMode();
        }
        // DONE: click advances just like the Next button
        if (state === STATE.DONE && nextMode) {
            goBtn.click();
        }
    }

    var nextMode = false;
    function nextBtnMode() {
        nextMode = true;
        goBtn.textContent = current >= totalAttempts ? 'See Results' : 'Next Attempt';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (nextMode) {
            nextMode = false;
            goBtn.textContent = 'Start';
            if (current >= totalAttempts) {
                finish();
            } else {
                nextAttempt();
            }
            return;
        }
        // start
        totalAttempts = parseInt(document.getElementById('attempts').value, 10);
        scores = [];
        current = 0;
        results.classList.add('d-none');
        goBtn.textContent = 'Start';
        nextAttempt();
    });

    resetBtn.addEventListener('click', function () {
        clearTimeout(timerId);
        state = STATE.IDLE;
        nextMode = false;
        scores = [];
        current = 0;
        results.classList.add('d-none');
        goBtn.classList.remove('d-none');
        goBtn.textContent = 'Start';
        setBox('#f8f9fa', 'text-muted', 'Click "Start" to begin the test');
        hideError();
    });

    gameBox.addEventListener('click', onBoxClick);
    gameBox.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            onBoxClick();
        }
    });
})();
</script>
@endsection
