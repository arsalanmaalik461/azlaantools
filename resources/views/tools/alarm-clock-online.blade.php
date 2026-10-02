@extends('layouts.app')

@section('title', 'Online Alarm Clock - Azlaan Tools')
@section('meta_description', 'Set a loud free online alarm clock in your browser. Wake-up alarm with snooze and countdown timers.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Online Alarm Clock</h1>
            <p class="lead text-muted">Set an alarm in your browser — it will ring loud and on time. Easy to set, totally free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div id="clockNow" class="display-3 fw-bold mb-1" style="letter-spacing:2px;">--:--:--</div>
                    <div id="dateNow" class="text-muted mb-4">---</div>

                    <div id="ringBox" class="alert alert-warning d-none" role="alert">
                        <h4 class="alert-heading">ALARM IS RINGING!</h4>
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-3">
                            <button type="button" class="btn btn-danger btn-lg" id="stopBtn">Stop</button>
                            <button type="button" class="btn btn-outline-secondary btn-lg" id="snoozeBtn">Snooze (5 min)</button>
                        </div>
                    </div>

                    <div class="row g-3 text-start">
                        <div class="col-md-6">
                            <label for="alarmTime" class="form-label fw-semibold">Alarm time</label>
                            <input type="time" class="form-control form-control-lg" id="alarmTime">
                        </div>
                        <div class="col-md-6">
                            <label for="soundSel" class="form-label fw-semibold">Alarm sound</label>
                            <select class="form-select form-select-lg" id="soundSel">
                                <option value="beeps">Classic Beeps</option>
                                <option value="chime">Gentle Chime</option>
                                <option value="siren">Loud Siren</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button type="button" class="btn btn-primary btn-lg" id="setBtn">Set Alarm</button>
                        <div class="btn-group" role="group" aria-label="Quick timers">
                            <button type="button" class="btn btn-outline-primary" id="q10">10 min</button>
                            <button type="button" class="btn btn-outline-primary" id="q20">20 min</button>
                            <button type="button" class="btn btn-outline-primary" id="q45">45 min</button>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" id="testBtn">Test Sound</button>
                        <button type="button" class="btn btn-outline-danger d-none" id="cancelBtn">Cancel Alarm</button>
                    </div>
                    <div class="alert alert-info mt-3 d-none" id="statusBox" role="status"></div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Pick a time or press a quick timer (10/20/45 min), then <strong>Set Alarm</strong>.</li>
                <li>Use <strong>Test Sound</strong> first to check that the volume is right.</li>
                <li>The alarm will ring on time — press Stop or Snooze.</li>
            </ol>
            <p class="text-muted small">Important: keep this tab open and do not let your device sleep, or the alarm will not ring. Keep the volume high.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var clockNow = document.getElementById('clockNow');
    var dateNow = document.getElementById('dateNow');
    var alarmTime = document.getElementById('alarmTime');
    var soundSel = document.getElementById('soundSel');
    var setBtn = document.getElementById('setBtn');
    var testBtn = document.getElementById('testBtn');
    var cancelBtn = document.getElementById('cancelBtn');
    var stopBtn = document.getElementById('stopBtn');
    var snoozeBtn = document.getElementById('snoozeBtn');
    var ringBox = document.getElementById('ringBox');
    var statusBox = document.getElementById('statusBox');
    var errorBox = document.getElementById('errorBox');

    var armed = false;
    var ringing = false;
    var targetMin = -1;      // minutes since midnight for fixed-time alarm
    var snoozeAt = 0;        // timestamp for snooze
    var audioCtx = null;
    var ringInterval = null;

    function showStatus(msg) {
        statusBox.textContent = msg;
        statusBox.classList.remove('d-none');
    }
    function hideStatus() {
        statusBox.classList.add('d-none');
        statusBox.textContent = '';
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function getCtx() {
        if (!audioCtx) {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) return null;
            audioCtx = new AC();
        }
        if (audioCtx.state === 'suspended') audioCtx.resume();
        return audioCtx;
    }

    function tone(freq, start, dur) {
        var ctx = getCtx();
        if (!ctx) return;
        var o = ctx.createOscillator();
        var g = ctx.createGain();
        o.type = 'sine';
        o.frequency.value = freq;
        g.gain.setValueAtTime(0.0001, start);
        g.gain.exponentialRampToValueAtTime(0.9, start + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, start + dur);
        o.connect(g);
        g.connect(ctx.destination);
        o.start(start);
        o.stop(start + dur + 0.05);
    }

    function playPattern(kind) {
        var ctx = getCtx();
        if (!ctx) { showError('Your browser does not support sound.'); return; }
        var t = ctx.currentTime + 0.05;
        if (kind === 'beeps') {
            for (var i = 0; i < 4; i++) tone(880, t + i * 0.45, 0.32);
        } else if (kind === 'chime') {
            var notes = [523.25, 587.33, 659.25, 783.99, 880];
            for (var j = 0; j < notes.length; j++) tone(notes[j], t + j * 0.35, 0.6);
        } else {
            for (var k = 0; k < 3; k++) {
                tone(660, t + k * 0.8, 0.35);
                tone(990, t + k * 0.8 + 0.4, 0.35);
            }
        }
    }

    function startRinging() {
        ringing = true;
        ringBox.classList.remove('d-none');
        playPattern(soundSel.value);
        ringInterval = setInterval(function () { playPattern(soundSel.value); }, 3000);
    }

    function stopRinging() {
        ringing = false;
        ringBox.classList.add('d-none');
        if (ringInterval) { clearInterval(ringInterval); ringInterval = null; }
    }

    function disarm() {
        armed = false;
        targetMin = -1;
        snoozeAt = 0;
        stopRinging();
        hideStatus();
        cancelBtn.classList.add('d-none');
        setBtn.classList.remove('d-none');
    }

    function fmtTime12(h, m) {
        var ap = h >= 12 ? 'PM' : 'AM';
        var hh = h % 12;
        if (hh === 0) hh = 12;
        return hh + ':' + pad(m) + ' ' + ap;
    }

    function armForMinutes(mins) {
        hideError();
        getCtx();
        var now = new Date();
        var total = now.getHours() * 60 + now.getMinutes() + mins;
        var h = Math.floor(total / 60) % 24;
        var m = total % 60;
        targetMin = h * 60 + m;
        armed = true;
        snoozeAt = 0;
        cancelBtn.classList.remove('d-none');
        showStatus('Alarm set: ' + fmtTime12(h, m) + ' (rings in ' + mins + ' minutes). Keep the tab open.');
    }

    setBtn.addEventListener('click', function () {
        hideError();
        var v = alarmTime.value;
        if (!v) { showError('Please select the alarm time first.'); return; }
        getCtx();
        var parts = v.split(':');
        var h = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10);
        targetMin = h * 60 + m;
        armed = true;
        snoozeAt = 0;
        cancelBtn.classList.remove('d-none');
        showStatus('Alarm set: it will ring at ' + fmtTime12(h, m) + '. Keep the tab open.');
    });

    document.getElementById('q10').addEventListener('click', function () { armForMinutes(10); });
    document.getElementById('q20').addEventListener('click', function () { armForMinutes(20); });
    document.getElementById('q45').addEventListener('click', function () { armForMinutes(45); });

    testBtn.addEventListener('click', function () {
        hideError();
        playPattern(soundSel.value);
    });

    cancelBtn.addEventListener('click', function () {
        disarm();
        showStatus('Alarm cancelled.');
        setTimeout(hideStatus, 2500);
    });

    stopBtn.addEventListener('click', function () {
        disarm();
        showStatus('Alarm stopped.');
        setTimeout(hideStatus, 2500);
    });

    snoozeBtn.addEventListener('click', function () {
        stopRinging();
        snoozeAt = Date.now() + 5 * 60 * 1000;
        armed = true;
        showStatus('Snooze: it will ring again in 5 minutes.');
    });

    function tick() {
        var now = new Date();
        clockNow.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        dateNow.textContent = now.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        if (!armed || ringing) return;
        if (snoozeAt > 0) {
            if (Date.now() >= snoozeAt) {
                snoozeAt = 0;
                startRinging();
            }
            return;
        }
        var cur = now.getHours() * 60 + now.getMinutes();
        if (cur === targetMin && now.getSeconds() < 2) {
            startRinging();
        }
    }
    setInterval(tick, 500);
    tick();
})();
</script>
@endsection
