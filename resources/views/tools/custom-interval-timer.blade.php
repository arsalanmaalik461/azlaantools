@extends('layouts.app')

@section('title', 'Custom Interval Timer — Free Online Tool')
@section('meta_description', 'Build custom work, rest and round intervals for workouts and drills.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Custom Interval Timer</h1>
                    <p class="lead small text-muted">Set prepare, work and rest seconds and the number of rounds, then run a full interval timer with big display, round counter and sound alerts for training, boxing or classroom drills.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="ciPrepare">Prepare (seconds)</label><input type="number" class="form-control" id="ciPrepare" value="10" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="ciWork">Work (seconds)</label><input type="number" class="form-control" id="ciWork" value="30" min="1" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="ciRest">Rest (seconds)</label><input type="number" class="form-control" id="ciRest" value="15" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="ciRounds">Rounds</label><input type="number" class="form-control" id="ciRounds" value="8" min="1" step="1"></div>
                    </div>
                    <div class="text-center my-4">
                        <div class="text-muted" id="ciPhase">Ready</div>
                        <div class="display-3 fw-bold" id="ciClock">00:00</div>
                        <div class="text-muted" id="ciRound">Round 0 of 0</div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-success" id="ciStart">Start</button>
                        <button type="button" class="btn btn-warning" id="ciPause">Pause</button>
                        <button type="button" class="btn btn-outline-secondary" id="ciReset">Reset</button>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="ciMsg">Set your intervals and press Start. Keep this tab open — sound alerts play at every phase change.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter prepare, work and rest seconds and the number of rounds.</li>
                        <li>Press Start and follow the big timer — it beeps at every phase change and three times at the finish.</li>
                        <li>Pause any time, and Reset to return to your settings.</li>
                    </ol>
                    <p class="small text-muted mb-0">Timing is based on the device clock, so it stays accurate even if the screen redraws late. Sound uses the browser Web Audio API and starts only after you press Start, as browsers require a user action for audio.</p>
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
    var timer = null, phases = [], idx = 0, endAt = 0, remaining = 0, running = false;
    function iv(id, min) { var v = parseInt(el(id).value, 10); return isNaN(v) || v < min ? min : v; }
    function buildPhases() {
        phases = [];
        var prep = iv("ciPrepare", 0), work = iv("ciWork", 1), rest = iv("ciRest", 0), rounds = iv("ciRounds", 1);
        if (prep > 0) { phases.push({ name: "Prepare", secs: prep, round: 0 }); }
        for (var r = 1; r <= rounds; r++) { phases.push({ name: "Work", secs: work, round: r }); if (rest > 0 && r < rounds) { phases.push({ name: "Rest", secs: rest, round: r }); } }
        return rounds;
    }
    function showPhase() { var ph = phases[idx]; el("ciPhase").textContent = ph.name; el("ciRound").textContent = ph.round > 0 ? ("Round " + ph.round + " of " + iv("ciRounds", 1)) : "Get ready"; }
    function tick() {
        var left = (endAt - Date.now()) / 1000;
        if (left <= 0) {
            idx++;
            if (idx >= phases.length) { stop(); el("ciPhase").textContent = "Done"; el("ciClock").textContent = "00:00"; el("ciMsg").textContent = "Workout complete. Well done."; beep(3); return; }
            beep(1);
            endAt = Date.now() + phases[idx].secs * 1000;
            showPhase();
            left = phases[idx].secs;
        }
        el("ciClock").textContent = fmtSec(left);
    }
    function stop() { if (timer) { clearInterval(timer); timer = null; } running = false; }
    el("ciStart").addEventListener("click", function () {
        if (running) { return; }
        if (!phases.length || idx >= phases.length) { buildPhases(); idx = 0; showPhase(); }
        endAt = Date.now() + (remaining > 0 ? remaining * 1000 : phases[idx].secs * 1000);
        remaining = 0;
        running = true;
        timer = setInterval(tick, 200);
        tick();
    });
    el("ciPause").addEventListener("click", function () { if (!running) { return; } remaining = Math.max(0, (endAt - Date.now()) / 1000); stop(); el("ciPhase").textContent = "Paused"; });
    el("ciReset").addEventListener("click", function () { stop(); phases = []; idx = 0; remaining = 0; el("ciPhase").textContent = "Ready"; el("ciClock").textContent = "00:00"; el("ciRound").textContent = "Round 0 of 0"; el("ciMsg").textContent = "Timer reset. Press Start when ready."; });
})();
</script>
@endsection
