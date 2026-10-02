@extends('layouts.app')

@section('title', 'Tabata Timer — Free Online Tool')
@section('meta_description', 'Run 20 second work and 10 second rest Tabata rounds with sound alerts.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Tabata Timer</h1>
                    <p class="lead small text-muted">The classic Tabata protocol — 20 seconds all out, 10 seconds rest, 8 rounds — with a prepare countdown, big timer and beeps. Rounds and times stay adjustable for other HIIT formats.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="tbPrepare">Prepare (seconds)</label><input type="number" class="form-control" id="tbPrepare" value="10" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="tbWork">Work (seconds)</label><input type="number" class="form-control" id="tbWork" value="20" min="1" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="tbRest">Rest (seconds)</label><input type="number" class="form-control" id="tbRest" value="10" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="tbRounds">Rounds</label><input type="number" class="form-control" id="tbRounds" value="8" min="1" step="1"></div>
                    </div>
                    <div class="text-center my-4">
                        <div class="text-muted" id="tbPhase">Ready — classic Tabata is 4 minutes total</div>
                        <div class="display-3 fw-bold" id="tbClock">00:00</div>
                        <div class="text-muted" id="tbRound">Round 0 of 8</div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-danger" id="tbStart">Start Tabata</button>
                        <button type="button" class="btn btn-warning" id="tbPause">Pause</button>
                        <button type="button" class="btn btn-outline-secondary" id="tbReset">Reset</button>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="tbMsg">Press Start Tabata. A single beep starts each phase and three beeps mark the finish.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Keep the classic settings (10 prepare, 20 work, 10 rest, 8 rounds) or adjust them for your workout.</li>
                        <li>Press Start Tabata and work hard on every work phase — Tabata only works at near maximum effort.</li>
                        <li>The timer beeps at each change and finishes after the last work round.</li>
                    </ol>
                    <p class="small text-muted mb-0">The original Tabata protocol from the 1996 study used 20 seconds of very high intensity work and 10 seconds of rest for 8 rounds (4 minutes) on elite speed skaters. Scale the effort to your fitness level and stop if you feel dizzy or unwell.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    var audioCtx = null;
    function beep(times) { try { if (!audioCtx) { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } var k = 0; function once() { if (k >= times) { return; } k++; var osc = audioCtx.createOscillator(); var gain = audioCtx.createGain(); osc.frequency.value = 880; osc.connect(gain); gain.connect(audioCtx.destination); gain.gain.value = 0.15; osc.start(); osc.stop(audioCtx.currentTime + 0.18); setTimeout(once, 260); } once(); } catch (e) { /* sound not available */ } }
    function pad(n) { return (n < 10 ? "0" : "") + n; }
    function fmtSec(s) { s = Math.max(0, Math.ceil(s)); return pad(Math.floor(s / 60)) + ":" + pad(s % 60); }
    var timer = null, phases = [], idx = 0, endAt = 0, remaining = 0, running = false, totalRounds = 8;
    function iv(id, min) { var v = parseInt(el(id).value, 10); return isNaN(v) || v < min ? min : v; }
    function buildPhases() {
        phases = [];
        totalRounds = iv("tbRounds", 1);
        var prep = iv("tbPrepare", 0), work = iv("tbWork", 1), rest = iv("tbRest", 0);
        if (prep > 0) { phases.push({ name: "Prepare", secs: prep, round: 0 }); }
        for (var r = 1; r <= totalRounds; r++) { phases.push({ name: "WORK", secs: work, round: r }); if (rest > 0 && r < totalRounds) { phases.push({ name: "Rest", secs: rest, round: r }); } }
    }
    function showPhase() { var ph = phases[idx]; el("tbPhase").textContent = ph.name; el("tbRound").textContent = ph.round > 0 ? ("Round " + ph.round + " of " + totalRounds) : "Get ready"; }
    function tick() {
        var left = (endAt - Date.now()) / 1000;
        if (left <= 0) {
            idx++;
            if (idx >= phases.length) { stop(); el("tbPhase").textContent = "Done — Tabata complete"; el("tbClock").textContent = "00:00"; el("tbMsg").textContent = "Finished. Cool down and stretch."; beep(3); return; }
            beep(1);
            endAt = Date.now() + phases[idx].secs * 1000;
            showPhase();
            left = phases[idx].secs;
        }
        el("tbClock").textContent = fmtSec(left);
    }
    function stop() { if (timer) { clearInterval(timer); timer = null; } running = false; }
    el("tbStart").addEventListener("click", function () {
        if (running) { return; }
        if (!phases.length || idx >= phases.length) { buildPhases(); idx = 0; showPhase(); }
        endAt = Date.now() + (remaining > 0 ? remaining * 1000 : phases[idx].secs * 1000);
        remaining = 0;
        running = true;
        timer = setInterval(tick, 200);
        tick();
    });
    el("tbPause").addEventListener("click", function () { if (!running) { return; } remaining = Math.max(0, (endAt - Date.now()) / 1000); stop(); el("tbPhase").textContent = "Paused"; });
    el("tbReset").addEventListener("click", function () { stop(); phases = []; idx = 0; remaining = 0; el("tbPhase").textContent = "Ready"; el("tbClock").textContent = "00:00"; el("tbRound").textContent = "Round 0 of 0"; el("tbMsg").textContent = "Timer reset. Press Start Tabata when ready."; });
})();
</script>
@endsection
