@extends('layouts.app')

@section('title', 'CPS Click Speed Test - Azlaan Tools')
@section('meta_description', 'Measure your clicks per second in 5 or 10 second tests. Free online click speed test.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">CPS Click Speed Test</h1>
            <p class="lead text-muted">Measure your click speed — how many clicks in 5 or 10 seconds? The timer starts with your first click.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="mb-3 d-flex justify-content-center gap-3 align-items-end flex-wrap">
                        <div>
                            <label for="durSel" class="form-label fw-semibold">Test duration</label>
                            <select class="form-select" id="durSel">
                                <option value="1">1 second</option>
                                <option value="5" selected>5 seconds</option>
                                <option value="10">10 seconds</option>
                                <option value="30">30 seconds</option>
                                <option value="60">60 seconds</option>
                            </select>
                        </div>
                        <div class="text-start">
                            <div class="text-muted small">Best CPS</div>
                            <div class="h4 mb-0" id="bestCps">-</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="border rounded p-2 bg-light">
                                <div class="text-muted small">Time</div>
                                <div class="h4 mb-0" id="timeLeft">5.0s</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 bg-light">
                                <div class="text-muted small">Clicks</div>
                                <div class="h4 mb-0" id="clickCount">0</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 bg-light">
                                <div class="text-muted small">CPS</div>
                                <div class="h4 mb-0 text-primary" id="liveCps">0.0</div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-lg w-100 py-5 fs-4" id="clickPad" style="user-select:none; -webkit-user-select:none; touch-action: manipulation;">CLICK HERE!</button>

                    <canvas id="spark" class="w-100 mt-3 border rounded" height="90" style="height:90px; background:#f8f9fa;"></canvas>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-3">
                        <div class="alert alert-success">
                            <div class="fs-5">Your score: <strong id="finalCps">0</strong> CPS (<span id="finalClicks">0</span> clicks)</div>
                            <div>Rank: <strong id="rankName">-</strong> <span id="rankEmoji"></span></div>
                            <div class="small text-muted" id="newBest"></div>
                        </div>
                        <button type="button" class="btn btn-outline-primary w-100" id="retryBtn">Try Again</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a test duration (5 seconds is the most popular).</li>
                <li>Click the blue button as fast as you can — the timer starts on your first click.</li>
                <li>When the time is up, you will see your CPS and rank. Your best score stays saved.</li>
            </ol>
            <h2>CPS ranks</h2>
            <p>Under 3: Turtle | 3-5: Average | 5-7: Fast | 7-9: Pro | 9+: Godlike. Minecraft and clicking-game players usually reach 6–9 CPS.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var durSel = document.getElementById('durSel');
    var clickPad = document.getElementById('clickPad');
    var timeLeftEl = document.getElementById('timeLeft');
    var clickCountEl = document.getElementById('clickCount');
    var liveCpsEl = document.getElementById('liveCps');
    var bestCpsEl = document.getElementById('bestCps');
    var spark = document.getElementById('spark');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var finalCps = document.getElementById('finalCps');
    var finalClicks = document.getElementById('finalClicks');
    var rankName = document.getElementById('rankName');
    var rankEmoji = document.getElementById('rankEmoji');
    var newBest = document.getElementById('newBest');
    var retryBtn = document.getElementById('retryBtn');

    var KEY = 'azlaan_cps_best_v1';
    var running = false, clicks = 0, startTs = 0, durMs = 5000, timerId = null, buckets = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function getBest() {
        try { return parseFloat(localStorage.getItem(KEY)) || 0; } catch (e) { return 0; }
    }
    function setBest(v) {
        try { localStorage.setItem(KEY, String(v)); } catch (e) {}
    }
    function refreshBest() {
        var b = getBest();
        bestCpsEl.textContent = b > 0 ? b.toFixed(2) : '-';
    }
    function rankFor(cps) {
        if (cps < 3) return ['Turtle', '🐢'];
        if (cps < 5) return ['Average', '🙂'];
        if (cps < 7) return ['Fast', '⚡'];
        if (cps < 9) return ['Pro', '🔥'];
        return ['Godlike', '👑'];
    }
    function drawSpark() {
        var ctx = spark.getContext('2d');
        var W = spark.width = spark.offsetWidth || 600;
        var H = spark.height = 90;
        ctx.clearRect(0, 0, W, H);
        if (!buckets.length) {
            ctx.fillStyle = '#adb5bd';
            ctx.font = '13px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('The graph will draw as you click', W / 2, H / 2);
            return;
        }
        var max = Math.max.apply(null, buckets.concat([1]));
        var bw = W / buckets.length;
        ctx.fillStyle = '#0d6efd';
        for (var i = 0; i < buckets.length; i++) {
            var h = (buckets[i] / max) * (H - 14);
            ctx.fillRect(i * bw + 1, H - h - 4, Math.max(1, bw - 2), h);
        }
        ctx.fillStyle = '#6c757d';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText('Clicks every 0.5 second', 6, 12);
    }

    function reset(dur) {
        running = false;
        clicks = 0;
        buckets = [];
        durMs = dur * 1000;
        if (timerId) { clearInterval(timerId); timerId = null; }
        timeLeftEl.textContent = dur.toFixed(1) + 's';
        clickCountEl.textContent = '0';
        liveCpsEl.textContent = '0.0';
        clickPad.textContent = 'CLICK HERE!';
        clickPad.disabled = false;
        results.classList.add('d-none');
        drawSpark();
    }

    function finish() {
        running = false;
        if (timerId) { clearInterval(timerId); timerId = null; }
        clickPad.disabled = true;
        clickPad.textContent = 'Time up!';
        var cps = clicks / (durMs / 1000);
        var r = rankFor(cps);
        finalCps.textContent = cps.toFixed(2);
        finalClicks.textContent = clicks;
        rankName.textContent = r[0];
        rankEmoji.textContent = r[1];
        var best = getBest();
        if (cps > best && clicks > 0) {
            setBest(cps);
            newBest.textContent = 'New record! The old best was ' + best.toFixed(2) + '.';
        } else {
            newBest.textContent = best > 0 ? 'Your best is still ' + best.toFixed(2) + ' CPS.' : '';
        }
        refreshBest();
        results.classList.remove('d-none');
    }

    clickPad.addEventListener('click', function () {
        hideError();
        if (!running) {
            running = true;
            startTs = Date.now();
            var bucketIdx = 0;
            timerId = setInterval(function () {
                var el = Date.now() - startTs;
                var left = Math.max(0, durMs - el);
                timeLeftEl.textContent = (left / 1000).toFixed(1) + 's';
                var bi = Math.floor(el / 500);
                if (bi !== bucketIdx) {
                    buckets.push(0);
                    bucketIdx = bi;
                    drawSpark();
                }
                var cps = clicks / Math.max(0.05, el / 1000);
                liveCpsEl.textContent = cps.toFixed(1);
                if (left <= 0) finish();
            }, 50);
            buckets.push(0);
        }
        if (!running) return;
        clicks++;
        clickCountEl.textContent = clicks;
        buckets[buckets.length - 1]++;
        var el2 = Date.now() - startTs;
        liveCpsEl.textContent = (clicks / Math.max(0.05, el2 / 1000)).toFixed(1);
        drawSpark();
    });

    durSel.addEventListener('change', function () {
        reset(parseFloat(durSel.value));
    });
    retryBtn.addEventListener('click', function () {
        reset(parseFloat(durSel.value));
    });

    refreshBest();
    reset(5);
    window.addEventListener('resize', drawSpark);
})();
</script>
@endsection
