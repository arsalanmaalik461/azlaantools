@extends('layouts.app')
@section('title', 'Stopwatch with Lap Times - Azlaan Tools')
@section('meta_description', 'Precise online stopwatch with lap splits and best lap highlight — free, no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Stopwatch with Lap Times</h1>
            <p class="lead text-muted">Precise stopwatch — start, pause, lap splits. The best lap is highlighted in green.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div id="display" class="fw-bold mb-1" style="font-size: 64px; font-variant-numeric: tabular-nums; letter-spacing: 2px;">00:00<span class="text-muted" style="font-size: 32px;">.00</span></div>
                    <div class="text-muted mb-4 small">minutes : seconds . centiseconds</div>

                    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                        <button type="button" class="btn btn-success btn-lg px-4" id="startBtn">Start</button>
                        <button type="button" class="btn btn-primary btn-lg px-4 d-none" id="lapBtn">Lap</button>
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="resetBtn">Reset</button>
                    </div>
                    <div class="text-muted small mb-3">Keyboard: <kbd>Space</kbd> start/pause &nbsp; <kbd>L</kbd> lap &nbsp; <kbd>R</kbd> reset</div>

                    <div class="alert alert-danger d-none text-start" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Lap Times <span class="badge bg-secondary" id="lapCount">0</span></h5>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="exportBtn">Export CSV</button>
                        </div>
                        <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
                            <table class="table table-striped table-bordered mb-0">
                                <thead class="table-light sticky-top">
                                    <tr><th>Lap</th><th>Lap time</th><th>Total time</th><th>Note</th></tr>
                                </thead>
                                <tbody id="lapsBody"></tbody>
                            </table>
                        </div>
                        <div class="mt-2 small text-muted" id="lapStats"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Press <strong>Start</strong> (or Space) — the stopwatch starts.</li>
                <li>Press <strong>Lap</strong> (or L) on each round — the split time is saved in the table.</li>
                <li>The fastest lap is highlighted in green, the slowest in red. Download the times with <strong>Export CSV</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var display = document.getElementById('display');
    var startBtn = document.getElementById('startBtn');
    var lapBtn = document.getElementById('lapBtn');
    var resetBtn = document.getElementById('resetBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var lapsBody = document.getElementById('lapsBody');

    var running = false;
    var startStamp = 0;
    var elapsedBefore = 0;
    var rafId = null;
    var laps = [];
    var lastLapTotal = 0;

    function now() {
        return (typeof performance !== 'undefined' && performance.now) ? performance.now() : Date.now();
    }
    function currentElapsed() {
        return running ? elapsedBefore + (now() - startStamp) : elapsedBefore;
    }
    function pad(n, len) {
        var s = String(Math.floor(n));
        while (s.length < len) { s = '0' + s; }
        return s;
    }
    function fmt(ms) {
        var totalCs = Math.floor(ms / 10);
        var cs = totalCs % 100;
        var totalS = Math.floor(totalCs / 100);
        var s = totalS % 60;
        var totalM = Math.floor(totalS / 60);
        var m = totalM % 60;
        var h = Math.floor(totalM / 60);
        var str = (h > 0 ? pad(h, 2) + ':' : '') + pad(m, 2) + ':' + pad(s, 2);
        return { main: str, cs: pad(cs, 2), full: str + '.' + pad(cs, 2) };
    }
    function paint() {
        var f = fmt(currentElapsed());
        display.innerHTML = f.main + '<span class="text-muted" style="font-size: 32px;">.' + f.cs + '</span>';
    }
    function tick() {
        paint();
        if (running) { rafId = requestAnimationFrame(tick); }
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function start() {
        hideError();
        if (running) { return; }
        running = true;
        startStamp = now();
        startBtn.textContent = 'Pause';
        startBtn.classList.remove('btn-success');
        startBtn.classList.add('btn-warning');
        lapBtn.classList.remove('d-none');
        rafId = requestAnimationFrame(tick);
    }
    function pause() {
        if (!running) { return; }
        elapsedBefore = currentElapsed();
        running = false;
        cancelAnimationFrame(rafId);
        startBtn.textContent = 'Resume';
        startBtn.classList.remove('btn-warning');
        startBtn.classList.add('btn-success');
        paint();
    }
    function toggle() {
        if (running) { pause(); } else { start(); }
    }
    function reset() {
        hideError();
        running = false;
        cancelAnimationFrame(rafId);
        elapsedBefore = 0;
        startStamp = 0;
        laps = [];
        lastLapTotal = 0;
        lapsBody.innerHTML = '';
        results.classList.add('d-none');
        startBtn.textContent = 'Start';
        startBtn.classList.remove('btn-warning');
        startBtn.classList.add('btn-success');
        lapBtn.classList.add('d-none');
        paint();
    }

    function recordLap() {
        if (!running) { showError('Start the stopwatch first, then press Lap.'); return; }
        hideError();
        var total = currentElapsed();
        var lapTime = total - lastLapTotal;
        lastLapTotal = total;
        laps.push({ lap: lapTime, total: total });
        renderLaps();
    }

    function renderLaps() {
        lapsBody.innerHTML = '';
        if (!laps.length) { results.classList.add('d-none'); return; }
        var best = 0, worst = 0, sum = 0, i;
        for (i = 0; i < laps.length; i++) {
            sum += laps[i].lap;
            if (laps[i].lap < laps[best].lap) { best = i; }
            if (laps[i].lap > laps[worst].lap) { worst = i; }
        }
        for (i = 0; i < laps.length; i++) {
            var tr = document.createElement('tr');
            var note = '';
            if (laps.length > 1) {
                if (i === best) { tr.className = 'table-success'; note = 'Best lap'; }
                else if (i === worst) { tr.className = 'table-danger'; note = 'Slowest'; }
            }
            var td1 = document.createElement('td'); td1.textContent = i + 1;
            var td2 = document.createElement('td'); td2.textContent = fmt(laps[i].lap).full;
            var td3 = document.createElement('td'); td3.textContent = fmt(laps[i].total).full;
            var td4 = document.createElement('td'); td4.textContent = note;
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4);
            lapsBody.appendChild(tr);
        }
        document.getElementById('lapCount').textContent = laps.length;
        document.getElementById('lapStats').textContent =
            'Average lap: ' + fmt(sum / laps.length).full + '  |  Total: ' + fmt(laps[laps.length - 1].total).full;
        results.classList.remove('d-none');
    }

    startBtn.addEventListener('click', toggle);
    lapBtn.addEventListener('click', recordLap);
    resetBtn.addEventListener('click', reset);
    document.getElementById('exportBtn').addEventListener('click', function () {
        if (!laps.length) { showError('Record at least one lap before exporting.'); return; }
        hideError();
        var csv = 'Lap,Lap Time,Total Time\n';
        for (var i = 0; i < laps.length; i++) {
            csv += (i + 1) + ',' + fmt(laps[i].lap).full + ',' + fmt(laps[i].total).full + '\n';
        }
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'stopwatch-laps.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    document.addEventListener('keydown', function (e) {
        if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT')) { return; }
        if (e.code === 'Space') { e.preventDefault(); toggle(); }
        else if (e.key === 'l' || e.key === 'L') { recordLap(); }
        else if (e.key === 'r' || e.key === 'R') { reset(); }
    });

    paint();
})();
</script>
@endsection
