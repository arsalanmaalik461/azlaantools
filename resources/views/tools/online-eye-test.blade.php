@extends('layouts.app')

@section('title', 'Online Eye Test - Azlaan Tools')
@section('meta_description', 'Free online eye test: calibrate your screen and check your vision with a Snellen-style eye chart, per eye, in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Online Eye Test</h1>
            <p class="lead text-muted">Check your vision — calibrate your screen, then read each line on the Snellen-style chart.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Step 1: Calibrate your screen</h5>
                    <p class="small text-muted">Match the blue rectangle below to a real bank/credit card (move the slider). This is needed so the letters show at the correct size.</p>
                    <div class="text-center my-3">
                        <div id="calCard" class="d-inline-block border border-primary border-3 rounded" style="background:#e8f1ff;"></div>
                    </div>
                    <label for="calSlider" class="form-label fw-semibold">Adjust card size: <span id="calVal">100</span>%</label>
                    <input type="range" class="form-range" id="calSlider" min="50" max="160" value="100">
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <hr>
                    <h5>Step 2: Start the test</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="eyeSel" class="form-label fw-semibold">Which eye?</label>
                            <select class="form-select" id="eyeSel">
                                <option value="both">Both eyes</option>
                                <option value="right">Right eye only</option>
                                <option value="left">Left eye only</option>
                            </select>
                            <div class="form-text">Cover one eye with your hand while testing the other.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="distSel" class="form-label fw-semibold">Distance from screen</label>
                            <select class="form-select" id="distSel">
                                <option value="1000">1 meter</option>
                                <option value="2000" selected>2 meter</option>
                                <option value="3000">3 meter</option>
                            </select>
                            <div class="form-text">Sit as far away as you selected.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="startBtn">Start Test</button>

                    <div id="testArea" class="d-none mt-4 text-center">
                        <div class="small text-muted mb-1">Line <span id="lineLabel">20/200</span> — read the letters below</div>
                        <div id="chartLine" class="border rounded py-3 px-2 mb-3 bg-white" style="letter-spacing:0.35em;"></div>
                        <div class="row g-2 justify-content-center">
                            <div class="col-12 col-md-6">
                                <input type="text" class="form-control text-center text-uppercase" id="answerInput" placeholder="Type the letters you see" maxlength="8" autocomplete="off">
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="button" class="btn btn-success w-100" id="checkBtn">Check Line</button>
                            </div>
                        </div>
                        <div class="small text-muted mt-2" id="testMsg"></div>
                    </div>

                    <div id="results" class="d-none mt-4">
                        <h5>Result</h5>
                        <div class="alert alert-success text-center">
                            <div class="small text-muted" id="resEye">-</div>
                            <div class="fs-3 fw-bold" id="resScore">-</div>
                            <div class="small" id="resNote">-</div>
                        </div>
                        <button type="button" class="btn btn-outline-primary w-100" id="againBtn">Test Again</button>
                    </div>

                    <div class="alert alert-warning mt-4 small mb-0">
                        This is only an estimate, not medical treatment. If you notice a problem with your vision, get a checkup from an eye specialist (ophthalmologist).
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Use the slider to match the blue rectangle to a real card (calibration).</li>
                <li>Select the eye and distance, then press "Start Test".</li>
                <li>Read and type the letters on each line — the test stops and gives your score when you get a line wrong.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var calSlider = document.getElementById('calSlider');
    var calVal = document.getElementById('calVal');
    var calCard = document.getElementById('calCard');
    var eyeSel = document.getElementById('eyeSel');
    var distSel = document.getElementById('distSel');
    var startBtn = document.getElementById('startBtn');
    var testArea = document.getElementById('testArea');
    var lineLabel = document.getElementById('lineLabel');
    var chartLine = document.getElementById('chartLine');
    var answerInput = document.getElementById('answerInput');
    var checkBtn = document.getElementById('checkBtn');
    var testMsg = document.getElementById('testMsg');
    var results = document.getElementById('results');
    var resEye = document.getElementById('resEye');
    var resScore = document.getElementById('resScore');
    var resNote = document.getElementById('resNote');
    var againBtn = document.getElementById('againBtn');
    var errorBox = document.getElementById('errorBox');

    var BASE_PX_PER_MM = 96 / 25.4; // CSS reference
    var pxPerMm = BASE_PX_PER_MM;
    var LINES = [200, 100, 70, 50, 40, 30, 25, 20]; // Snellen denominators
    var LETTERS = 'CDEFLNOP TZ'.replace(/ /g, '');
    var lineIdx = 0, lastPassed = null, currentLetters = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function drawCalCard() {
        calCard.style.width = Math.round(85.6 * pxPerMm) + 'px';
        calCard.style.height = Math.round(53.98 * pxPerMm) + 'px';
    }
    calSlider.addEventListener('input', function () {
        calVal.textContent = calSlider.value;
        pxPerMm = BASE_PX_PER_MM * (+calSlider.value) / 100;
        drawCalCard();
    });
    drawCalCard();

    function randomLetters(n) {
        var s = '';
        for (var i = 0; i < n; i++) s += LETTERS.charAt(Math.floor(Math.random() * LETTERS.length));
        return s;
    }
    // Snellen: letter height subtends 5 arc-minutes at the stated acuity distance.
    function letterPx(denom, distMm) {
        var arcMin = 5 * (denom / 20); // scaled for this line
        var rad = arcMin / 60 * Math.PI / 180;
        var hMm = 2 * distMm * Math.tan(rad / 2);
        return Math.max(10, Math.round(hMm * pxPerMm));
    }
    function showLine() {
        var denom = LINES[lineIdx];
        lineLabel.textContent = '20/' + denom;
        currentLetters = randomLetters(5);
        var distMm = +distSel.value;
        chartLine.style.fontSize = letterPx(denom, distMm) + 'px';
        chartLine.style.fontWeight = '700';
        chartLine.style.fontFamily = 'monospace';
        chartLine.textContent = currentLetters.split('').join(' ');
        answerInput.value = '';
        testMsg.textContent = 'Line ' + (lineIdx + 1) + ' of ' + LINES.length;
        setTimeout(function () { answerInput.focus(); }, 50);
    }
    function eyeName(v) {
        return v === 'right' ? 'Right eye' : v === 'left' ? 'Left eye' : 'Both eyes';
    }
    function finish(passed) {
        testArea.classList.add('d-none');
        results.classList.remove('d-none');
        resEye.textContent = eyeName(eyeSel.value);
        if (passed === null) {
            resScore.textContent = 'Below 20/200';
            resNote.textContent = 'Could not read even the first line clearly — get a checkup from an eye specialist.';
        } else {
            resScore.textContent = 'About 20/' + passed;
            resNote.textContent = passed <= 25
                ? 'Great! Your vision looks normal at this distance.'
                : 'Your vision looks weak — talk to an eye specialist about glasses.';
        }
    }

    startBtn.addEventListener('click', function () {
        hideError();
        if (+calSlider.value === 100) {
            // still fine, but remind
            testMsg.textContent = '';
        }
        lineIdx = 0;
        lastPassed = null;
        results.classList.add('d-none');
        testArea.classList.remove('d-none');
        showLine();
    });
    checkBtn.addEventListener('click', function () {
        var ans = answerInput.value.toUpperCase().replace(/[^A-Z]/g, '');
        if (ans.length < 5) { testMsg.textContent = 'Type 5 letters (the ones you see).'; return; }
        var correct = 0;
        for (var i = 0; i < 5; i++) if (ans[i] === currentLetters[i]) correct++;
        if (correct >= 3) {
            lastPassed = LINES[lineIdx];
            testMsg.textContent = correct + '/5 correct — next (smaller) line.';
            lineIdx++;
            if (lineIdx >= LINES.length) finish(lastPassed);
            else showLine();
        } else {
            testMsg.textContent = 'Only ' + correct + '/5 correct — test complete.';
            finish(lastPassed);
        }
    });
    answerInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') checkBtn.click();
    });
    againBtn.addEventListener('click', function () {
        results.classList.add('d-none');
        testArea.classList.add('d-none');
    });
})();
</script>
@endsection
