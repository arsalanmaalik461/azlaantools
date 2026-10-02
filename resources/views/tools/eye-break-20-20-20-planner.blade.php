@extends('layouts.app')

@section('title', 'Eye Break 20-20-20 Planner - Azlaan Tools')
@section('meta_description', 'Plan your screen breaks with the 20-20-20 rule and run a live eye-break timer. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Eye Break 20-20-20 Planner</h1>
            <p class="lead text-muted">A break schedule for your eyes while you work on a screen. The 20-20-20 rule: every 20 minutes, look at something 20 feet away for 20 seconds — this reduces eye strain.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Make your break schedule</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-4">
                            <label for="startTime" class="form-label fw-semibold">Work start (time)</label>
                            <input type="time" class="form-control" id="startTime" value="09:00">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="workHours" class="form-label fw-semibold">Work hours</label>
                            <input type="number" class="form-control" id="workHours" value="8" min="1" max="16" step="0.5">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="breakEvery" class="form-label fw-semibold">Break every (minutes)</label>
                            <input type="number" class="form-control" id="breakEvery" value="20" min="5" max="120">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Break Schedule</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-3">Your break schedule</h5>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-bordered table-striped table-sm align-middle">
                                <thead class="table-light sticky-top">
                                    <tr><th>#</th><th>Break time</th><th>What to do</th></tr>
                                </thead>
                                <tbody id="scheduleBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2">Tip: at every break, look at something at least 20 feet (6 meters) away for 20 seconds, and blink 2-3 times.</p>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Live 20-20-20 timer</h5>
                    <div class="text-center py-3 border rounded bg-light mb-3">
                        <div class="display-4 fw-bold" id="timerDisplay">20:00</div>
                        <div class="text-muted" id="timerPhase">Ready — press Start</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button type="button" class="btn btn-success" id="timerStart">Start Timer</button>
                        <button type="button" class="btn btn-outline-secondary" id="timerReset">Reset</button>
                    </div>
                    <p class="text-muted small mt-3 mb-0">This is an estimate, not a replacement for treatment. If your eyes hurt or your vision is blurry, see a doctor.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your work start time and hours, then press "Generate Break Schedule".</li>
                <li>At every break, look at something far away according to the schedule.</li>
                <li>Or press "Start Timer" to run the live timer — 20 minutes of work, then a 20-second break, repeating automatically.</li>
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
    var results = document.getElementById('results');
    var scheduleBody = document.getElementById('scheduleBody');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function fmtTime(d) {
        var h = d.getHours(), m = d.getMinutes();
        var ap = h >= 12 ? 'PM' : 'AM';
        h = h % 12; if (h === 0) h = 12;
        return h + ':' + pad(m) + ' ' + ap;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var st = document.getElementById('startTime').value;
        var hours = parseFloat(document.getElementById('workHours').value);
        var every = parseInt(document.getElementById('breakEvery').value, 10);
        if (!st) { showError('Please select a start time.'); return; }
        if (isNaN(hours) || hours < 1 || hours > 16) { showError('Please enter work hours between 1 and 16.'); return; }
        if (isNaN(every) || every < 5 || every > 120) { showError('Please enter a break interval between 5 and 120 minutes.'); return; }

        var parts = st.split(':');
        var start = new Date();
        start.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);
        var end = new Date(start.getTime() + hours * 3600000);

        scheduleBody.innerHTML = '';
        var t = new Date(start.getTime() + every * 60000);
        var n = 0;
        while (t <= end) {
            n++;
            var tr = document.createElement('tr');
            var tdN = document.createElement('td'); tdN.textContent = n;
            var tdT = document.createElement('td'); tdT.className = 'fw-semibold'; tdT.textContent = fmtTime(t);
            var tdW = document.createElement('td'); tdW.textContent = 'Look at something far for 20 seconds (20 feet / 6 meters)';
            tr.appendChild(tdN); tr.appendChild(tdT); tr.appendChild(tdW);
            scheduleBody.appendChild(tr);
            t = new Date(t.getTime() + every * 60000);
        }
        var endRow = document.createElement('tr');
        endRow.className = 'table-success';
        var e1 = document.createElement('td'); e1.textContent = '—';
        var e2 = document.createElement('td'); e2.className = 'fw-semibold'; e2.textContent = fmtTime(end);
        var e3 = document.createElement('td'); e3.textContent = 'Work finished — rest your eyes';
        endRow.appendChild(e1); endRow.appendChild(e2); endRow.appendChild(e3);
        scheduleBody.appendChild(endRow);

        results.classList.remove('d-none');
    });

    // Live timer
    var WORK_SEC = 20 * 60, BREAK_SEC = 20;
    var timerDisplay = document.getElementById('timerDisplay');
    var timerPhase = document.getElementById('timerPhase');
    var timerStart = document.getElementById('timerStart');
    var timerReset = document.getElementById('timerReset');
    var phase = 'work', remaining = WORK_SEC, tickId = null, audioCtx = null;

    function beep(times) {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var t0 = audioCtx.currentTime;
            for (var i = 0; i < times; i++) {
                var osc = audioCtx.createOscillator();
                var gain = audioCtx.createGain();
                osc.connect(gain); gain.connect(audioCtx.destination);
                osc.frequency.value = 880;
                osc.type = 'sine';
                gain.gain.setValueAtTime(0.25, t0 + i * 0.35);
                gain.gain.exponentialRampToValueAtTime(0.001, t0 + i * 0.35 + 0.3);
                osc.start(t0 + i * 0.35);
                osc.stop(t0 + i * 0.35 + 0.32);
            }
        } catch (e) { /* audio not available */ }
    }

    function renderTimer() {
        var m = Math.floor(remaining / 60), s = remaining % 60;
        timerDisplay.textContent = pad(m) + ':' + pad(s);
        if (phase === 'work') {
            timerPhase.textContent = 'Work — break in ' + Math.ceil(remaining / 60) + ' min';
            timerDisplay.className = 'display-4 fw-bold';
        } else {
            timerPhase.textContent = 'BREAK: look at something far for 20 seconds!';
            timerDisplay.className = 'display-4 fw-bold text-success';
        }
    }

    function tick() {
        remaining--;
        if (remaining < 0) {
            if (phase === 'work') {
                phase = 'break'; remaining = BREAK_SEC;
                beep(3);
            } else {
                phase = 'work'; remaining = WORK_SEC;
                beep(2);
            }
        }
        renderTimer();
    }

    timerStart.addEventListener('click', function () {
        if (tickId) {
            clearInterval(tickId); tickId = null;
            timerStart.textContent = 'Resume Timer';
            timerPhase.textContent = 'Paused — press Resume';
            return;
        }
        tickId = setInterval(tick, 1000);
        timerStart.textContent = 'Pause Timer';
        beep(1);
    });
    timerReset.addEventListener('click', function () {
        if (tickId) { clearInterval(tickId); tickId = null; }
        phase = 'work'; remaining = WORK_SEC;
        timerStart.textContent = 'Start Timer';
        renderTimer();
        timerPhase.textContent = 'Ready — press Start';
    });
    renderTimer();
})();
</script>
@endsection
