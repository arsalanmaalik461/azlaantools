@extends('layouts.app')

@section('title', 'Typing Speed Test - Check Your WPM Online Free | Azlaan Tools')
@section('meta_description', 'Free 60-second typing speed test: measure your WPM, accuracy and errors with live character highlighting. No signup needed, just start typing.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Typing Speed Test</h1>
            <p class="lead text-muted">Test how fast you type in 60 seconds. The timer starts on your first keystroke — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3 mb-3 text-center">
                        <div class="border rounded px-3 py-2"><div class="small text-muted">Time Left</div><div class="fw-bold fs-4" id="timerVal">60s</div></div>
                        <div class="border rounded px-3 py-2"><div class="small text-muted">WPM</div><div class="fw-bold fs-4" id="wpmVal">0</div></div>
                        <div class="border rounded px-3 py-2"><div class="small text-muted">Accuracy</div><div class="fw-bold fs-4" id="accVal">100%</div></div>
                        <div class="border rounded px-3 py-2"><div class="small text-muted">Errors</div><div class="fw-bold fs-4 text-danger" id="errVal">0</div></div>
                    </div>
                    <label class="form-label fw-semibold">Type this text:</label>
                    <div id="targetBox" class="border rounded p-3 mb-3 fs-5" style="line-height:1.9; font-family: monospace;"></div>
                    <label for="typeInput" class="form-label fw-semibold">Your typing</label>
                    <textarea class="form-control" id="typeInput" rows="4" placeholder="Start typing here to begin the test..." autocomplete="off" autocapitalize="off" spellcheck="false"></textarea>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="restartBtn">Restart with New Text</button>
                    </div>
                    <div id="resultCard" class="alert alert-success mt-3 d-none">
                        <strong>Test complete!</strong>
                        <div id="resultText" class="mt-1"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Read the target text shown in the box above.</li>
                        <li>Click the typing area and start typing — the 60-second timer starts on your first keystroke.</li>
                        <li>Correct letters turn green and mistakes turn red while WPM and accuracy update live.</li>
                        <li>When time ends, check your final WPM, accuracy and correct characters, then restart for a new text.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var texts = [
        'The quick brown fox jumps over the lazy dog near the river bank on a bright sunny morning.',
        'Practice makes perfect, so type a little every day and your fingers will learn the keyboard by heart.',
        'Pakistan is a beautiful country with mountains, deserts, rivers and hardworking people in every city.',
        'Good typing speed saves time at work, in exams and while chatting with friends and family online.',
        'A journey of a thousand miles begins with a single step, so start today and keep moving forward.',
        'Technology changes fast, but clear thinking and honest hard work never go out of style anywhere.',
        'The sun rises in the east and sets in the west, giving farmers light to work in their green fields.',
        'Reading books every night builds strong vocabulary and helps you write better letters and emails.'
    ];
    var target = '';
    var started = false, finished = false, timeLeft = 60, timerId = null;
    var input = document.getElementById('typeInput');
    var targetBox = document.getElementById('targetBox');

    function stats() {
        var typed = input.value;
        var correct = 0, errors = 0;
        for (var i = 0; i < typed.length; i++) {
            if (i < target.length && typed[i] === target[i]) { correct++; }
            else { errors++; }
        }
        var elapsed = 60 - timeLeft;
        if (elapsed <= 0) { elapsed = started ? 1 : 0; }
        var minutes = elapsed / 60;
        var wpm = minutes > 0 ? Math.round((correct / 5) / minutes) : 0;
        var acc = typed.length > 0 ? Math.round((correct / typed.length) * 100) : 100;
        return { correct: correct, errors: errors, wpm: wpm, acc: acc, typedLen: typed.length };
    }
    function renderTarget() {
        var typed = input.value;
        var html = '';
        for (var i = 0; i < target.length; i++) {
            var ch = target[i];
            var disp = ch === ' ' ? '&nbsp;' : ch;
            if (i < typed.length) {
                if (typed[i] === ch) { html += '<span class="text-success bg-light">' + disp + '</span>'; }
                else { html += '<span class="text-danger" style="background:#fde8e8;">' + disp + '</span>'; }
            } else if (i === typed.length) {
                html += '<span class="border-bottom border-primary border-3">' + disp + '</span>';
            } else { html += '<span class="text-muted">' + disp + '</span>'; }
        }
        targetBox.innerHTML = html;
        var s = stats();
        document.getElementById('wpmVal').textContent = s.wpm;
        document.getElementById('accVal').textContent = s.acc + '%';
        document.getElementById('errVal').textContent = s.errors;
    }
    function finish() {
        finished = true;
        if (timerId) { clearInterval(timerId); timerId = null; }
        input.disabled = true;
        var s = stats();
        document.getElementById('resultText').textContent = 'WPM: ' + s.wpm + ' | Accuracy: ' + s.acc + '% | Correct characters: ' + s.correct + ' | Errors: ' + s.errors;
        document.getElementById('resultCard').classList.remove('d-none');
    }
    function startTimer() {
        timerId = setInterval(function () {
            timeLeft--;
            document.getElementById('timerVal').textContent = timeLeft + 's';
            renderTarget();
            if (timeLeft <= 0) { finish(); }
        }, 1000);
    }
    input.addEventListener('input', function () {
        if (finished) { return; }
        if (!started && input.value.length > 0) { started = true; startTimer(); }
        if (input.value.length >= target.length) { renderTarget(); finish(); return; }
        renderTarget();
    });
    input.addEventListener('paste', function (e) { e.preventDefault(); });
    function restart() {
        if (timerId) { clearInterval(timerId); timerId = null; }
        target = texts[Math.floor(Math.random() * texts.length)];
        started = false; finished = false; timeLeft = 60;
        input.value = ''; input.disabled = false;
        document.getElementById('timerVal').textContent = '60s';
        document.getElementById('resultCard').classList.add('d-none');
        renderTarget();
        input.focus();
    }
    document.getElementById('restartBtn').addEventListener('click', restart);
    restart();
})();
</script>
@endsection
