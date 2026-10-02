@extends('layouts.app')
@section('title', 'Audio Visualizer Video - Azlaan Tools')
@section('meta_description', 'Turn any audio file into an animated music visualizer video, free online. Bars, circular and waveform styles — records right in your browser, no upload.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Audio Visualizer Video</h1>
            <p class="lead text-muted">Select your music/audio file, choose a style — this tool will create an animated visualizer video and download it as a WebM file. Perfect for YouTube Shorts or music videos.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="audioFile" class="form-label fw-semibold">Audio file (MP3, WAV, OGG...)</label>
                        <input type="file" class="form-control" id="audioFile" accept="audio/*">
                        <div class="form-text">The file stays in your browser — nothing is uploaded.</div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="vizStyle" class="form-label fw-semibold">Visualizer style</label>
                            <select class="form-select" id="vizStyle">
                                <option value="bars">Bars (classic)</option>
                                <option value="circular">Circular</option>
                                <option value="wave">Waveform</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="vizTheme" class="form-label fw-semibold">Color theme</label>
                            <select class="form-select" id="vizTheme">
                                <option value="violet">Violet Sunset</option>
                                <option value="ocean">Ocean Blue</option>
                                <option value="fire">Fire</option>
                                <option value="mint">Mint Green</option>
                                <option value="gold">Golden</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="vizBg" class="form-label fw-semibold">Video background</label>
                            <select class="form-select" id="vizBg">
                                <option value="#0b1020">Deep Navy</option>
                                <option value="#000000">Black</option>
                                <option value="#1a0b2e">Dark Purple</option>
                                <option value="#041a12">Dark Green</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="vizRes" class="form-label fw-semibold">Resolution</label>
                            <select class="form-select" id="vizRes">
                                <option value="720">720p (1280 × 720)</option>
                                <option value="1080">1080p (1920 × 1080)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" id="genBtn">Generate Video</button>
                        <button type="button" class="btn btn-outline-danger d-none" id="stopBtn">Stop &amp; Download</button>
                        <a href="#" class="btn btn-success d-none" id="dlLink" download>Download Video</a>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <p class="mt-3 mb-0 fw-semibold" id="statusText"></p>
                    <p class="text-muted small mb-0" id="progText"></p>

                    <div id="results" class="d-none mt-3">
                        <canvas id="vizCanvas" class="w-100 rounded border" style="aspect-ratio: 16 / 9;"></canvas>
                        <div class="alert alert-info mt-3 mb-0">
                            The video downloads in <strong>WebM</strong> format — YouTube, Facebook and WhatsApp all accept it.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your audio file (MP3, WAV, M4A, OGG).</li>
                <li>Choose the visualizer style, colors and resolution.</li>
                <li>Click <strong>Generate Video</strong> — the audio will start playing and the animation will be recorded.</li>
                <li>When the audio ends (or when you click <strong>Stop &amp; Download</strong>) the video will download.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var AC = window.AudioContext || window.webkitAudioContext;
    var genBtn = document.getElementById('genBtn');
    var stopBtn = document.getElementById('stopBtn');
    var dlLink = document.getElementById('dlLink');
    var audioFile = document.getElementById('audioFile');
    var vizStyle = document.getElementById('vizStyle');
    var vizTheme = document.getElementById('vizTheme');
    var vizBg = document.getElementById('vizBg');
    var vizRes = document.getElementById('vizRes');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var statusText = document.getElementById('statusText');
    var progText = document.getElementById('progText');
    var canvas = document.getElementById('vizCanvas');
    var ctx = canvas.getContext('2d');

    var THEMES = {
        violet: ['#8b5cf6', '#ec4899'],
        ocean: ['#22d3ee', '#3b82f6'],
        fire: ['#f97316', '#ef4444'],
        mint: ['#34d399', '#a3e635'],
        gold: ['#fbbf24', '#f59e0b']
    };

    var audioCtx = null, analyser = null, sourceNode = null;
    var recorder = null, chunks = [], rafId = null, startTime = 0, totalDur = 0;
    var timerId = null, recording = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(s) {
        s = Math.max(0, Math.floor(s));
        var m = Math.floor(s / 60), r = s % 60;
        return (m < 10 ? '0' + m : m) + ':' + (r < 10 ? '0' + r : r);
    }
    function pickMime() {
        var cands = ['video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm'];
        for (var i = 0; i < cands.length; i++) {
            if (window.MediaRecorder && MediaRecorder.isTypeSupported(cands[i])) return cands[i];
        }
        return '';
    }

    function drawBars(data, W, H, c1, c2) {
        var n = 64, bw = W / n, i, v, h, x, g;
        for (i = 0; i < n; i++) {
            v = data[Math.floor(i * data.length * 0.72 / n)] / 255;
            h = Math.max(4, v * H * 0.78);
            x = i * bw + bw * 0.18;
            g = ctx.createLinearGradient(0, H - h, 0, H);
            g.addColorStop(0, c1); g.addColorStop(1, c2);
            ctx.fillStyle = g;
            ctx.fillRect(x, H - h, bw * 0.64, h);
        }
    }
    function drawCircular(data, W, H, c1, c2) {
        var cx = W / 2, cy = H / 2, R = Math.min(W, H) * 0.17, n = 96, i, v, len, a, x1, y1, x2, y2;
        ctx.strokeStyle = c2; ctx.lineWidth = 6;
        ctx.beginPath(); ctx.arc(cx, cy, R, 0, Math.PI * 2); ctx.stroke();
        ctx.lineWidth = Math.max(3, W / 220);
        for (i = 0; i < n; i++) {
            v = data[Math.floor(i * data.length * 0.72 / n)] / 255;
            len = 4 + v * Math.min(W, H) * 0.3;
            a = (i / n) * Math.PI * 2 - Math.PI / 2;
            x1 = cx + Math.cos(a) * (R + 8); y1 = cy + Math.sin(a) * (R + 8);
            x2 = cx + Math.cos(a) * (R + 8 + len); y2 = cy + Math.sin(a) * (R + 8 + len);
            ctx.strokeStyle = i % 2 ? c1 : c2;
            ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y2); ctx.stroke();
        }
    }
    function drawWave(analyserNode, W, H, c1, c2) {
        var td = new Uint8Array(analyserNode.fftSize), i, x, y;
        analyserNode.getByteTimeDomainData(td);
        var g = ctx.createLinearGradient(0, 0, W, 0);
        g.addColorStop(0, c1); g.addColorStop(1, c2);
        ctx.strokeStyle = g; ctx.lineWidth = Math.max(2, H / 180);
        ctx.beginPath();
        for (i = 0; i < td.length; i++) {
            x = (i / (td.length - 1)) * W;
            y = ((td[i] - 128) / 128) * (H * 0.38) + H / 2;
            if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
        }
        ctx.stroke();
    }

    function loop() {
        if (!recording) return;
        rafId = requestAnimationFrame(loop);
        var W = canvas.width, H = canvas.height;
        var theme = THEMES[vizTheme.value] || THEMES.violet;
        ctx.fillStyle = vizBg.value;
        ctx.fillRect(0, 0, W, H);
        var style = vizStyle.value;
        if (style === 'wave') {
            drawWave(analyser, W, H, theme[0], theme[1]);
        } else {
            var data = new Uint8Array(analyser.frequencyBinCount);
            analyser.getByteFrequencyData(data);
            if (style === 'circular') drawCircular(data, W, H, theme[0], theme[1]);
            else drawBars(data, W, H, theme[0], theme[1]);
        }
    }

    function stopRecording() {
        if (!recording) return;
        recording = false;
        cancelAnimationFrame(rafId);
        clearInterval(timerId);
        try { if (sourceNode) sourceNode.stop(); } catch (e) { /* already stopped */ }
        try { if (recorder && recorder.state !== 'inactive') recorder.stop(); } catch (e) { /* noop */ }
        stopBtn.classList.add('d-none');
        genBtn.disabled = false;
        statusText.textContent = 'Preparing your video...';
    }

    genBtn.addEventListener('click', function () {
        hideError();
        var f = audioFile.files[0];
        if (!f) { showError('Please select an audio file first.'); return; }
        if (!AC) { showError('Your browser does not support Web Audio. Use Chrome or Edge.'); return; }
        var mime = pickMime();
        if (!mime) { showError('Your browser does not support video recording. Use Chrome or Edge.'); return; }

        genBtn.disabled = true;
        dlLink.classList.add('d-none');
        results.classList.remove('d-none');
        statusText.textContent = 'Loading audio...';
        progText.textContent = '';

        if (vizRes.value === '1080') { canvas.width = 1920; canvas.height = 1080; }
        else { canvas.width = 1280; canvas.height = 720; }

        var reader = new FileReader();
        reader.onload = function () {
            try {
                if (audioCtx) { try { audioCtx.close(); } catch (e) { /* noop */ } }
                audioCtx = new AC();
                if (audioCtx.state === 'suspended') audioCtx.resume();
                audioCtx.decodeAudioData(reader.result.slice(0), function (buf) {
                    startRender(buf, f.name, mime);
                }, function () {
                    genBtn.disabled = false;
                    showError('Could not understand this audio file. Try another MP3/WAV file.');
                });
            } catch (e) {
                genBtn.disabled = false;
                showError('Audio could not start: ' + e.message);
            }
        };
        reader.readAsArrayBuffer(f);
    });

    function startRender(buf, name, mime) {
        totalDur = buf.duration;
        sourceNode = audioCtx.createBufferSource();
        sourceNode.buffer = buf;
        analyser = audioCtx.createAnalyser();
        analyser.fftSize = 512;
        analyser.smoothingTimeConstant = 0.82;
        sourceNode.connect(analyser);
        analyser.connect(audioCtx.destination);
        var dest = audioCtx.createMediaStreamDestination();
        sourceNode.connect(dest);

        var vStream = canvas.captureStream(30);
        var combined = new MediaStream(vStream.getVideoTracks().concat(dest.stream.getAudioTracks()));
        chunks = [];
        recorder = new MediaRecorder(combined, { mimeType: mime, videoBitsPerSecond: 5000000 });
        recorder.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        recorder.onstop = function () {
            var blob = new Blob(chunks, { type: mime.split(';')[0] });
            var url = URL.createObjectURL(blob);
            dlLink.href = url;
            var base = name.replace(/\.[^.]+$/, '') || 'audio';
            dlLink.download = base + '-visualizer.webm';
            dlLink.classList.remove('d-none');
            statusText.textContent = 'Done! Download your video.';
            progText.textContent = 'Duration: ' + fmt(totalDur) + ' • ' + (blob.size / 1048576).toFixed(1) + ' MB';
        };

        recording = true;
        startTime = Date.now();
        sourceNode.onended = stopRecording;
        sourceNode.start(0);
        recorder.start(200);
        loop();
        stopBtn.classList.remove('d-none');
        statusText.textContent = 'Recording — audio is playing...';
        timerId = setInterval(function () {
            var el = (Date.now() - startTime) / 1000;
            progText.textContent = fmt(el) + ' / ' + fmt(totalDur) + ' recorded';
        }, 500);
    }

    stopBtn.addEventListener('click', stopRecording);
})();
</script>
@endsection
