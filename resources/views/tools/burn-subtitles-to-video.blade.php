@extends('layouts.app')

@section('title', 'Burn Subtitles To Video - Azlaan Tools')
@section('meta_description', 'Burn SRT subtitles permanently into your video online for free. Hardcode captions with adjustable size and position, export with audio.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Burn Subtitles To Video</h1>
            <p class="lead text-muted">Burn SRT subtitles into your video permanently — free hardcoded captions. Select your video and SRT file, see the preview, then export with audio.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="videoFile" class="form-label fw-semibold">1. Video file (MP4)</label>
                            <input type="file" class="form-control" id="videoFile" accept="video/mp4,video/webm,video/*">
                            <div class="form-text">A video that plays in the browser (MP4/H.264 is best).</div>
                        </div>
                        <div class="col-md-6">
                            <label for="srtFile" class="form-label fw-semibold">2. SRT subtitle file</label>
                            <input type="file" class="form-control" id="srtFile" accept=".srt,text/plain">
                            <div class="form-text">Or test with "Sample SRT" below.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="fontSize" class="form-label fw-semibold">Subtitle size: <span id="fontSizeVal">48</span>px</label>
                            <input type="range" class="form-range" id="fontSize" min="24" max="120" value="48">
                        </div>
                        <div class="col-md-4">
                            <label for="subPos" class="form-label fw-semibold">Position</label>
                            <select class="form-select" id="subPos">
                                <option value="bottom">Bottom</option>
                                <option value="top">Top</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="subStyle" class="form-label fw-semibold">Style</label>
                            <select class="form-select" id="subStyle">
                                <option value="outline">White text + black outline</option>
                                <option value="box">Yellow text + black box</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Load Sample SRT</button>
                        <button type="button" class="btn btn-primary flex-grow-1" id="burnBtn" disabled>Burn Subtitles &amp; Download</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="progressWrap" class="d-none mt-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold">Recording...</span><span id="progressPct">0%</span>
                        </div>
                        <div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width:0%"></div></div>
                        <div class="form-text mt-1">The video is playing again and subtitles are being recorded — do not close the tab.</div>
                    </div>

                    <div id="results" class="d-none mt-4">
                        <h5>Preview (subtitles burned)</h5>
                        <video id="srcVideo" class="d-none" playsinline></video>
                        <canvas id="previewCanvas" class="w-100 border rounded bg-black"></canvas>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="playBtn">Play / Pause</button>
                            <input type="range" class="form-range flex-grow-1" id="seekBar" min="0" max="1000" value="0">
                            <span class="small text-muted" id="timeLabel">0:00 / 0:00</span>
                        </div>
                        <div class="small text-muted mt-1" id="subInfo"></div>
                    </div>

                    <div class="alert alert-info mt-4 small mb-0">
                        <strong>Note:</strong> Export is in <strong>WebM</strong> format (a browser technical limit) — this file plays in Chrome, WhatsApp and all modern players. Subtitles become permanent inside the video, no separate SRT needed. Everything happens in your browser, the file is not uploaded anywhere.
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your MP4 video and its SRT subtitle file.</li>
                <li>Adjust the subtitle size, position and style — you will see it live in the preview.</li>
                <li>Click "Burn Subtitles &amp; Download" — the video will be recorded with audio and downloaded.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var videoFile = document.getElementById('videoFile');
    var srtFile = document.getElementById('srtFile');
    var fontSize = document.getElementById('fontSize');
    var fontSizeVal = document.getElementById('fontSizeVal');
    var subPos = document.getElementById('subPos');
    var subStyle = document.getElementById('subStyle');
    var burnBtn = document.getElementById('burnBtn');
    var sampleBtn = document.getElementById('sampleBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var video = document.getElementById('srcVideo');
    var canvas = document.getElementById('previewCanvas');
    var ctx = canvas.getContext('2d');
    var playBtn = document.getElementById('playBtn');
    var seekBar = document.getElementById('seekBar');
    var timeLabel = document.getElementById('timeLabel');
    var subInfo = document.getElementById('subInfo');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var progressPct = document.getElementById('progressPct');

    var subs = [];
    var videoReady = false;
    var recording = false;
    var audioLinked = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(t) {
        t = Math.max(0, Math.floor(t));
        var m = Math.floor(t / 60), s = t % 60;
        return m + ':' + (s < 10 ? '0' : '') + s;
    }
    function parseTime(s) {
        s = s.trim().replace(',', '.');
        var parts = s.split(':');
        if (parts.length < 3) return 0;
        var secParts = parts[2].split('.');
        var sec = +secParts[0] || 0;
        var frac = secParts[1] ? parseFloat('0.' + secParts[1].replace(/\D/g, '')) || 0 : 0;
        return (+parts[0] || 0) * 3600 + (+parts[1] || 0) * 60 + sec + frac;
    }
    function parseSRT(text) {
        var out = [];
        var blocks = text.replace(/\r/g, '').split(/\n\s*\n/);
        for (var i = 0; i < blocks.length; i++) {
            var lines = blocks[i].split('\n').filter(function (l) { return l.trim() !== ''; });
            if (lines.length < 2) continue;
            var ti = lines[0].indexOf('-->') >= 0 ? 0 : 1;
            if (ti >= lines.length) continue;
            var parts = lines[ti].split('-->');
            if (parts.length < 2) continue;
            var start = parseTime(parts[0].trim()), end = parseTime(parts[1].trim());
            var txt = lines.slice(ti + 1).join('\n').replace(/<[^>]*>/g, '').trim();
            if (txt && end > start) out.push({ start: start, end: end, text: txt });
        }
        out.sort(function (a, b) { return a.start - b.start; });
        return out;
    }
    function activeSub(t) {
        for (var i = 0; i < subs.length; i++) {
            if (t >= subs[i].start && t <= subs[i].end) return subs[i].text;
        }
        return '';
    }
    function wrapText(text, maxW) {
        var words = text.split(/\s+/), lines = [], cur = '';
        for (var i = 0; i < words.length; i++) {
            var test = cur ? cur + ' ' + words[i] : words[i];
            if (ctx.measureText(test).width > maxW && cur) { lines.push(cur); cur = words[i]; }
            else cur = test;
        }
        if (cur) lines.push(cur);
        return lines;
    }
    function drawFrame() {
        var W = canvas.width, H = canvas.height;
        if (!videoReady || !W) return;
        try { ctx.drawImage(video, 0, 0, W, H); }
        catch (e) { return; }
        var txt = activeSub(video.currentTime);
        if (!txt) return;
        var size = Math.round(W / 1280 * (+fontSize.value));
        if (size < 14) size = 14;
        ctx.font = 'bold ' + size + 'px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';
        var lines = [];
        txt.split('\n').forEach(function (ln) {
            wrapText(ln, W * 0.92).forEach(function (w) { lines.push(w); });
        });
        var lh = size * 1.25;
        var y = subPos.value === 'top' ? size * 0.6 + 12 : H - 24;
        var drawn = [];
        if (subPos.value === 'top') {
            for (var i = 0; i < lines.length; i++) drawn.push({ t: lines[i], y: y + i * lh });
        } else {
            for (var j = lines.length - 1; j >= 0; j--) drawn.push({ t: lines[j], y: y - (lines.length - 1 - j) * lh });
        }
        drawn.forEach(function (d) {
            var x = W / 2;
            if (subStyle.value === 'box') {
                var tw = ctx.measureText(d.t).width;
                ctx.fillStyle = 'rgba(0,0,0,0.75)';
                ctx.fillRect(x - tw / 2 - 12, d.y - size - 8, tw + 24, size + 20);
                ctx.fillStyle = '#ffe600';
            } else {
                ctx.lineWidth = Math.max(2, size / 10);
                ctx.strokeStyle = 'rgba(0,0,0,0.9)';
                ctx.strokeText(d.t, x, d.y);
                ctx.fillStyle = '#ffffff';
            }
            ctx.fillText(d.t, x, d.y);
        });
    }
    function loop() {
        drawFrame();
        if (videoReady && video.duration) {
            if (!recording) seekBar.value = Math.round(video.currentTime / video.duration * 1000);
            timeLabel.textContent = fmt(video.currentTime) + ' / ' + fmt(video.duration);
        }
        requestAnimationFrame(loop);
    }
    function refreshState() {
        hideError();
        if (videoReady && subs.length) {
            burnBtn.disabled = false;
            results.classList.remove('d-none');
            subInfo.textContent = subs.length + ' subtitles loaded.';
        } else {
            burnBtn.disabled = true;
            if (videoReady) results.classList.remove('d-none');
        }
    }

    videoFile.addEventListener('change', function () {
        hideError();
        var f = videoFile.files[0];
        if (!f) return;
        videoReady = false;
        video.src = URL.createObjectURL(f);
        video.load();
    });
    video.addEventListener('loadedmetadata', function () {
        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 720;
        videoReady = true;
        refreshState();
        loop();
    });
    video.addEventListener('error', function () {
        showError('Video could not load. Please try an MP4 (H.264) file.');
    });
    srtFile.addEventListener('change', function () {
        var f = srtFile.files[0];
        if (!f) return;
        var r = new FileReader();
        r.onload = function () {
            subs = parseSRT(String(r.result || ''));
            if (!subs.length) showError('No subtitles found in the SRT file. Please check the format.');
            else refreshState();
        };
        r.readAsText(f);
    });
    sampleBtn.addEventListener('click', function () {
        var s = '1\n00:00:01,000 --> 00:00:04,000\nHello! This is a sample subtitle.\n\n' +
                '2\n00:00:05,000 --> 00:00:09,000\nSubtitles will be permanently burned into the video.\n\n' +
                '3\n00:00:10,000 --> 00:00:14,000\nYou can adjust the size and position from above.';
        subs = parseSRT(s);
        refreshState();
    });
    fontSize.addEventListener('input', function () { fontSizeVal.textContent = fontSize.value; });
    playBtn.addEventListener('click', function () {
        if (video.paused) video.play(); else video.pause();
    });
    seekBar.addEventListener('input', function () {
        if (video.duration) video.currentTime = seekBar.value / 1000 * video.duration;
    });

    function pickMime() {
        var c = ['video/webm;codecs=vp9', 'video/webm;codecs=vp8', 'video/webm'];
        for (var i = 0; i < c.length; i++) {
            if (window.MediaRecorder && MediaRecorder.isTypeSupported(c[i])) return c[i];
        }
        return '';
    }

    burnBtn.addEventListener('click', function () {
        hideError();
        if (!videoReady || !subs.length) { showError('Please load both the video and SRT first.'); return; }
        var mime = pickMime();
        if (!mime) { showError('Your browser does not support video recording. Please try Chrome.'); return; }
        if (recording) return;
        recording = true;
        burnBtn.disabled = true;
        progressWrap.classList.remove('d-none');
        video.pause();
        video.currentTime = 0;

        function startRec() {
            var stream = canvas.captureStream(30);
            try {
                if (!audioLinked) {
                    var AC = window.AudioContext || window.webkitAudioContext;
                    if (AC) {
                        var actx = new AC();
                        var src = actx.createMediaElementSource(video);
                        src.connect(actx.destination);
                        var dest = actx.createMediaStreamDestination();
                        src.connect(dest);
                        var tracks = stream.getVideoTracks().concat(dest.stream.getAudioTracks());
                        stream = new MediaStream(tracks);
                        audioLinked = true;
                    }
                }
            } catch (e) { /* video-only fallback */ }
            var rec;
            try { rec = new MediaRecorder(stream, { mimeType: mime, videoBitsPerSecond: 8000000 }); }
            catch (e) { showError('Could not start recording: ' + e.message); recording = false; burnBtn.disabled = false; progressWrap.classList.add('d-none'); return; }
            var chunks = [];
            rec.ondataavailable = function (ev) { if (ev.data && ev.data.size) chunks.push(ev.data); };
            rec.onstop = function () {
                recording = false;
                burnBtn.disabled = false;
                progressWrap.classList.add('d-none');
                var blob = new Blob(chunks, { type: 'video/webm' });
                var a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'subtitled-video.webm';
                document.body.appendChild(a);
                a.click();
                setTimeout(function () { document.body.removeChild(a); }, 500);
                showError('');
                hideError();
                subInfo.textContent = 'Done! ' + (blob.size / 1048576).toFixed(1) + ' MB WebM downloaded — subtitles are now permanent.';
            };
            video.onended = function () {
                if (rec.state !== 'inactive') rec.stop();
                video.onended = null;
            };
            video.ontimeupdate = function () {
                if (video.duration) {
                    var p = Math.round(video.currentTime / video.duration * 100);
                    progressBar.style.width = p + '%';
                    progressPct.textContent = p + '%';
                }
            };
            rec.start(500);
            video.play().catch(function () {
                rec.stop();
                showError('Video could not play.');
            });
            setTimeout(function () {
                if (recording && video.paused && video.currentTime === 0) {
                    try { rec.stop(); } catch (e) {}
                    recording = false; burnBtn.disabled = false; progressWrap.classList.add('d-none');
                    showError('Recording timed out — please try again.');
                }
            }, 8000);
        }
        if (video.readyState >= 2) startRec();
        else { video.onseeked = function () { video.onseeked = null; startRec(); }; }
    });
})();
</script>
@endsection
