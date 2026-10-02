@extends('layouts.app')

@section('title', 'Teleprompter Online Free - Auto Scroll Script Reader | Azlaan Tools')
@section('meta_description', 'Free online teleprompter: paste your script, big text auto-scrolls at your speed, adjustable font size, mirror mode for prompter rigs and fullscreen. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Teleprompter</h1>
            <p class="lead text-muted">Paste your script, press Start, and read while the text scrolls smoothly at exactly your speed — perfect for videos, speeches and online classes.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="tpScript">Your script</label>
                    <textarea id="tpScript" class="form-control" rows="6" placeholder="Paste or type your script here...">Assalam-o-Alaikum and welcome to my video. Today I will show you something very useful. Speak slowly, smile, and look at the camera while the text scrolls. You can change the speed and text size anytime with the sliders below. Good luck with your recording!</textarea>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="tpSpeed">Scroll speed: <span id="tpSpeedVal">30</span></label>
                            <input type="range" id="tpSpeed" class="form-range" min="5" max="120" step="1" value="30">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="tpFont">Text size: <span id="tpFontVal">42</span>px</label>
                            <input type="range" id="tpFont" class="form-range" min="24" max="84" step="2" value="42">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="tpCountdown">Start countdown (seconds)</label>
                            <input type="number" id="tpCountdown" class="form-control" min="0" max="10" step="1" value="3">
                        </div>
                    </div>
                    <div class="form-check form-switch fs-5 mt-3">
                        <input class="form-check-input" type="checkbox" id="tpMirror">
                        <label class="form-check-label" for="tpMirror">Mirror mode (for beam-splitter prompter rigs)</label>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="tpToggle" class="btn btn-success btn-lg px-5">▶ Start Scrolling</button>
                        <button type="button" id="tpReset" class="btn btn-outline-secondary btn-lg">↺ Back to Top</button>
                        <button type="button" id="tpFull" class="btn btn-dark btn-lg">⛶ Fullscreen</button>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="tpStatus">Ready. Tip: you can also use Space to start/pause while the prompter is focused.</p>
                </div>
            </div>

            <div id="tpStage" class="rounded bg-black text-white p-4" style="height: 55vh; overflow: hidden; position: relative;" tabindex="0">
                <div id="tpMirrorWrap">
                    <div id="tpCount" class="display-1 fw-bold text-center d-none" style="padding-top: 15vh;"></div>
                    <div id="tpText" style="font-size: 42px; line-height: 1.5; white-space: pre-wrap; padding-top: 45vh; padding-bottom: 55vh;"></div>
                </div>
                <div style="position:absolute; top:0; bottom:0; left:0; width:6px; background:#ffc107;"></div>
            </div>
            <p class="small text-muted mt-2">The yellow line on the left marks your reading position — keep the current line near it.</p>

            <div class="alert alert-secondary mt-3"><strong>Privacy note:</strong> Your script stays on your device — nothing is uploaded or saved on any server.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Paste your script in the box above.</li>
                    <li>Set scroll speed and text size — start slow, you can adjust live while it scrolls.</li>
                    <li>Press <strong>Start Scrolling</strong> (optional countdown gives you time to get ready), and read aloud.</li>
                    <li>Use <strong>Mirror mode</strong> if you use a glass prompter rig, and <strong>Fullscreen</strong> for a distraction-free display.</li>
                </ol>
                <p class="mb-0 small text-muted">Tip: place the prompter screen as close to your camera lens as possible so your eyes look natural on video.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var scriptEl = document.getElementById('tpScript');
    var stage = document.getElementById('tpStage');
    var textEl = document.getElementById('tpText');
    var mirrorWrap = document.getElementById('tpMirrorWrap');
    var countEl = document.getElementById('tpCount');
    var toggleBtn = document.getElementById('tpToggle');
    var statusEl = document.getElementById('tpStatus');
    var speedEl = document.getElementById('tpSpeed');
    var fontEl = document.getElementById('tpFont');
    var offset = 0;
    var running = false;
    var rafId = null;
    var lastTs = 0;
    var countdownTimer = null;

    function renderText() {
        textEl.textContent = scriptEl.value || ' ';
    }
    function maxOffset() {
        return Math.max(0, textEl.offsetHeight - stage.clientHeight * 0.4);
    }
    function apply() {
        textEl.style.transform = 'translateY(' + (-offset) + 'px)';
    }
    function step(ts) {
        if (!running) return;
        if (!lastTs) lastTs = ts;
        var dt = (ts - lastTs) / 1000;
        lastTs = ts;
        offset += Number(speedEl.value) * dt;
        if (offset >= maxOffset()) {
            offset = maxOffset();
            apply();
            pause();
            statusEl.textContent = 'Finished — script complete. Press Back to Top to run it again.';
            return;
        }
        apply();
        rafId = requestAnimationFrame(step);
    }
    function start() {
        renderText();
        running = true;
        lastTs = 0;
        toggleBtn.textContent = '⏸ Pause';
        statusEl.textContent = 'Scrolling… adjust speed anytime.';
        rafId = requestAnimationFrame(step);
    }
    function pause() {
        running = false;
        if (rafId) cancelAnimationFrame(rafId);
        toggleBtn.textContent = '▶ Start Scrolling';
    }
    scriptEl.addEventListener('input', function () {
        offset = 0;
        renderText();
        apply();
    } );
    speedEl.addEventListener('input', function () {
        document.getElementById('tpSpeedVal').textContent = speedEl.value;
    } );
    fontEl.addEventListener('input', function () {
        document.getElementById('tpFontVal').textContent = fontEl.value;
        textEl.style.fontSize = fontEl.value + 'px';
    } );
    document.getElementById('tpMirror').addEventListener('change', function (ev) {
        mirrorWrap.style.transform = ev.target.checked ? 'scaleX(-1)' : '';
        // The countdown sits inside the mirrored wrap, so its digits would be
        // flipped too — counter-flip it so the numbers stay readable.
        countEl.style.transform = ev.target.checked ? 'scaleX(-1)' : '';
    } );
    toggleBtn.addEventListener('click', function () {
        if (running) {
            pause();
            statusEl.textContent = 'Paused.';
            return;
        }
        var cd = Math.max(0, parseInt(document.getElementById('tpCountdown').value, 10) || 0);
        if (cd > 0 && offset === 0) {
            var n = cd;
            countEl.classList.remove('d-none');
            countEl.textContent = n;
            statusEl.textContent = 'Get ready…';
            toggleBtn.disabled = true;
            countdownTimer = setInterval(function () {
                n--;
                if (n <= 0) {
                    clearInterval(countdownTimer);
                    countEl.classList.add('d-none');
                    toggleBtn.disabled = false;
                    start();
                } else {
                    countEl.textContent = n;
                }
            }, 1000);
        } else {
            start();
        }
    } );
    document.getElementById('tpReset').addEventListener('click', function () {
        pause();
        if (countdownTimer) clearInterval(countdownTimer);
        countEl.classList.add('d-none');
        toggleBtn.disabled = false;
        offset = 0;
        apply();
        statusEl.textContent = 'Back at the top. Ready.';
    } );
    document.getElementById('tpFull').addEventListener('click', function () {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else if (stage.requestFullscreen) {
            stage.requestFullscreen();
        }
    } );
    stage.addEventListener('keydown', function (ev) {
        if (ev.code === 'Space') {
            ev.preventDefault();
            toggleBtn.click();
        }
    } );
    renderText();
    apply();
} )();
</script>
@endsection
