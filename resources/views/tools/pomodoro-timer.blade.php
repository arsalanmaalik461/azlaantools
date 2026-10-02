@extends('layouts.app')
@section('title', 'Pomodoro Timer — Study Focus Timer | Azlaan Tools')
@section('meta_description', 'Free Pomodoro study timer with 25/5 and 50/10 presets, custom minutes, session counter and end-of-phase beep. Focus better, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Pomodoro Timer — Study Focus</h1>
            <p class="lead text-muted">Study in focused sprints with short breaks. Pick a preset, name your task, and let the timer keep you honest.</p>
            <div class="card shadow-sm mb-4" id="timerCard"><div class="card-body text-center">
                <input type="text" class="form-control form-control-lg text-center mb-3" id="taskLabel" placeholder="What are you studying? e.g. Maths — Chapter 5">
                <div class="btn-group mb-3" role="group">
                    <button type="button" class="btn btn-outline-primary presetBtn" data-focus="25" data-break="5">25 / 5 Classic</button>
                    <button type="button" class="btn btn-outline-primary presetBtn" data-focus="50" data-break="10">50 / 10 Deep</button>
                </div>
                <div class="row g-2 justify-content-center mb-3">
                    <div class="col-5 col-md-3"><label class="form-label" for="customFocus">Focus (min)</label><input type="number" class="form-control text-center" id="customFocus" value="25" min="1" max="180"></div>
                    <div class="col-5 col-md-3"><label class="form-label" for="customBreak">Break (min)</label><input type="number" class="form-control text-center" id="customBreak" value="5" min="1" max="60"></div>
                </div>
                <div class="badge fs-6 mb-2" id="phaseBadge">FOCUS</div>
                <div class="display-1 fw-bold" id="display">25:00</div>
                <div class="text-muted mb-3">Sessions completed: <strong id="sessionCount">0</strong></div>
                <div>
                    <button type="button" class="btn btn-success btn-lg me-2" id="startBtn">Start</button>
                    <button type="button" class="btn btn-warning btn-lg me-2" id="pauseBtn">Pause</button>
                    <button type="button" class="btn btn-outline-secondary btn-lg" id="resetBtn">Reset</button>
                </div>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Write your task, then choose 25/5, 50/10 or custom minutes.</li><li>Press Start and study until the beep — then take your break.</li><li>The timer switches between focus and break automatically and counts your sessions.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var phase = 'focus';
    var remaining = 25 * 60;
    var timerId = null;
    var sessions = 0;
    var audioCtx = null;
    function focusSecs() { return (parseInt(document.getElementById('customFocus').value, 10) || 25) * 60; }
    function breakSecs() { return (parseInt(document.getElementById('customBreak').value, 10) || 5) * 60; }
    function render() {
        var m = Math.floor(remaining / 60), s = remaining % 60;
        document.getElementById('display').textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        var badge = document.getElementById('phaseBadge');
        var card = document.getElementById('timerCard');
        if (phase === 'focus') { badge.textContent = 'FOCUS'; badge.className = 'badge fs-6 mb-2 bg-danger'; card.style.background = '#fff5f5'; }
        else { badge.textContent = 'BREAK'; badge.className = 'badge fs-6 mb-2 bg-success'; card.style.background = '#f0fff4'; }
        document.getElementById('sessionCount').textContent = sessions;
    }
    function beep() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var o = audioCtx.createOscillator(), g = audioCtx.createGain();
            o.connect(g); g.connect(audioCtx.destination);
            o.frequency.value = 880; g.gain.value = 0.2;
            o.start();
            setTimeout(function () { o.stop(); }, 600);
        } catch (e) { /* sound not available */ }
    }
    function tick() {
        remaining--;
        if (remaining <= 0) {
            beep();
            if (phase === 'focus') { sessions++; phase = 'break'; remaining = breakSecs(); }
            else { phase = 'focus'; remaining = focusSecs(); }
        }
        render();
    }
    document.getElementById('startBtn').addEventListener('click', function () { if (!timerId) timerId = setInterval(tick, 1000); });
    document.getElementById('pauseBtn').addEventListener('click', function () { clearInterval(timerId); timerId = null; });
    document.getElementById('resetBtn').addEventListener('click', function () { clearInterval(timerId); timerId = null; phase = 'focus'; remaining = focusSecs(); render(); });
    var presets = document.querySelectorAll('.presetBtn');
    presets.forEach(function (b) { b.addEventListener('click', function () {
        document.getElementById('customFocus').value = b.getAttribute('data-focus');
        document.getElementById('customBreak').value = b.getAttribute('data-break');
        clearInterval(timerId); timerId = null; phase = 'focus'; remaining = focusSecs(); render();
    }); });
    ['customFocus','customBreak'].forEach(function (id) { document.getElementById(id).addEventListener('change', function () { if (!timerId) { remaining = phase === 'focus' ? focusSecs() : breakSecs(); render(); } }); });
    render();
})();
</script>
@endsection
