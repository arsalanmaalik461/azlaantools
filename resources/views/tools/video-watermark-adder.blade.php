@extends('layouts.app')

@section('title', 'Video Watermark Adder - Azlaan Tools')
@section('meta_description', 'Add a text or logo watermark to any video in your browser, free online, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Video Watermark Adder</h1>
            <p class="lead text-muted">Add a text or logo watermark on your video — for branding or protection. Everything happens in your browser, free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="videoFile" class="form-label fw-semibold">Video file</label>
                        <input type="file" class="form-control" id="videoFile" accept="video/*">
                    </div>
                    <div class="mb-3">
                        <label for="wmText" class="form-label fw-semibold">Watermark text</label>
                        <input type="text" class="form-control" id="wmText" placeholder="e.g. Azlaan Solar" value="Azlaan">
                    </div>
                    <div class="mb-3">
                        <label for="logoFile" class="form-label fw-semibold">Logo image (optional)</label>
                        <input type="file" class="form-control" id="logoFile" accept="image/*">
                        <div class="form-text">If you give a logo, the logo becomes the watermark instead of the text.</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="wmPos" class="form-label fw-semibold">Position</label>
                            <select class="form-select" id="wmPos">
                                <option value="br">Bottom right</option>
                                <option value="bl">Bottom left</option>
                                <option value="tr">Top right</option>
                                <option value="tl">Top left</option>
                                <option value="c">Center</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="wmOpacity" class="form-label fw-semibold">Opacity (<span id="opacityVal">70</span>%)</label>
                            <input type="range" class="form-range" id="wmOpacity" min="10" max="100" value="70">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="wmSize" class="form-label fw-semibold">Size (<span id="sizeVal">48</span>px)</label>
                        <input type="range" class="form-range" id="wmSize" min="16" max="120" value="48">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Add Watermark &amp; Export</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <p class="fw-semibold mb-2">Preview / recording:</p>
                        <canvas id="wmCanvas" class="w-100 rounded border"></canvas>
                        <div class="progress mt-2 mb-2 d-none" id="progWrap">
                            <div class="progress-bar" id="progBar" style="width:0%">0%</div>
                        </div>
                        <a class="btn btn-success w-100" id="downloadBtn" href="#" download="watermarked.webm">Download Watermarked Video (WebM)</a>
                        <div class="form-text mt-2">Output will be in WebM format (browser recording). The whole video is processed in real time — a long video can take some time.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the video file.</li>
                <li>Type the watermark text or give a logo image, and set the position and size.</li>
                <li>Press "Add Watermark &amp; Export" — when the recording finishes, download the video.</li>
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
    var canvas = document.getElementById('wmCanvas');
    var downloadBtn = document.getElementById('downloadBtn');
    var progWrap = document.getElementById('progWrap');
    var progBar = document.getElementById('progBar');
    var lastUrl = null;
    var running = false;

    document.getElementById('wmOpacity').addEventListener('input', function () {
        document.getElementById('opacityVal').textContent = this.value;
    });
    document.getElementById('wmSize').addEventListener('input', function () {
        document.getElementById('sizeVal').textContent = this.value;
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function drawWatermark(ctx, W, H, text, logoImg, pos, opacity, size) {
        ctx.save();
        ctx.globalAlpha = opacity / 100;
        var pad = Math.max(12, W * 0.02);
        if (logoImg) {
            var lw = size * 2.2;
            var lh = lw * (logoImg.naturalHeight / logoImg.naturalWidth);
            var lx = W - lw - pad, ly = H - lh - pad;
            if (pos === 'tl') { lx = pad; ly = pad; }
            else if (pos === 'tr') { lx = W - lw - pad; ly = pad; }
            else if (pos === 'bl') { lx = pad; ly = H - lh - pad; }
            else if (pos === 'c') { lx = (W - lw) / 2; ly = (H - lh) / 2; }
            ctx.drawImage(logoImg, lx, ly, lw, lh);
        } else {
            ctx.font = 'bold ' + size + 'px Arial, sans-serif';
            ctx.fillStyle = '#ffffff';
            ctx.strokeStyle = 'rgba(0,0,0,0.55)';
            ctx.lineWidth = Math.max(1, size / 12);
            ctx.textBaseline = 'bottom';
            var tw = ctx.measureText(text).width;
            var tx = W - tw - pad, ty = H - pad;
            if (pos === 'tl') { tx = pad; ty = pad + size; ctx.textBaseline = 'top'; }
            else if (pos === 'tr') { tx = W - tw - pad; ty = pad + size; ctx.textBaseline = 'top'; }
            else if (pos === 'bl') { tx = pad; ty = H - pad; }
            else if (pos === 'c') { tx = (W - tw) / 2; ty = (H + size) / 2; ctx.textBaseline = 'middle'; }
            ctx.strokeText(text, tx, ty);
            ctx.fillText(text, tx, ty);
        }
        ctx.restore();
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (running) { showError('Recording already in progress. Please wait, the video is being processed.'); return; }
        var videoFile = document.getElementById('videoFile').files[0];
        var text = document.getElementById('wmText').value.trim();
        var logoFile = document.getElementById('logoFile').files[0];
        var pos = document.getElementById('wmPos').value;
        var opacity = parseInt(document.getElementById('wmOpacity').value, 10);
        var size = parseInt(document.getElementById('wmSize').value, 10);

        if (!videoFile) { showError('Please select a video file.'); return; }
        if (!logoFile && !text) { showError('Type a watermark text or select a logo image.'); return; }
        if (typeof MediaRecorder === 'undefined' || !canvas.captureStream) {
            showError('Your browser does not support video recording. Please use Chrome or Edge.');
            return;
        }

        function loadLogo(cb) {
            if (!logoFile) { cb(null); return; }
            var img = new Image();
            img.onload = function () { cb(img); };
            img.onerror = function () { cb(null); };
            img.src = URL.createObjectURL(logoFile);
        }

        var videoUrl = URL.createObjectURL(videoFile);
        var video = document.createElement('video');
        video.muted = false;
        video.playsInline = true;
        video.preload = 'auto';
        video.src = videoUrl;

        video.onloadedmetadata = function () {
            var W = video.videoWidth, H = video.videoHeight;
            if (!W || !H) { showError('Could not read this video file.'); URL.revokeObjectURL(videoUrl); return; }
            canvas.width = W; canvas.height = H;
            loadLogo(function (logoImg) {
                if (logoFile && !logoImg) {
                    showError('The logo image did not load. Use a text watermark or try a different image.');
                    URL.revokeObjectURL(videoUrl);
                    return;
                }
                startRecording(video, videoUrl, W, H, text, logoImg, pos, opacity, size);
            });
        };
        video.onerror = function () {
            showError('The video file did not load. Try a different video file.');
            URL.revokeObjectURL(videoUrl);
        };
    });

    function startRecording(video, videoUrl, W, H, text, logoImg, pos, opacity, size) {
        running = true;
        results.classList.remove('d-none');
        progWrap.classList.remove('d-none');
        downloadBtn.classList.add('d-none');
        var ctx = canvas.getContext('2d');

        var canvasStream = canvas.captureStream(30);
        var tracks = canvasStream.getVideoTracks();
        try {
            var vStream = video.captureStream ? video.captureStream() : video.mozCaptureStream();
            if (vStream) {
                var aTracks = vStream.getAudioTracks();
                for (var i = 0; i < aTracks.length; i++) tracks.push(aTracks[i]);
            }
        } catch (e) { /* audio-less export still fine */ }
        var mixed = new MediaStream(tracks);

        var mime = 'video/webm;codecs=vp9';
        if (!window.MediaRecorder.isTypeSupported(mime)) mime = 'video/webm';
        var rec = new MediaRecorder(mixed, { mimeType: mime, videoBitsPerSecond: 5000000 });
        var chunks = [];
        rec.ondataavailable = function (e) { if (e.data && e.data.size > 0) chunks.push(e.data); };
        rec.onstop = function () {
            running = false;
            var blob = new Blob(chunks, { type: 'video/webm' });
            if (lastUrl) URL.revokeObjectURL(lastUrl);
            lastUrl = URL.createObjectURL(blob);
            downloadBtn.href = lastUrl;
            downloadBtn.classList.remove('d-none');
            progBar.style.width = '100%';
            progBar.textContent = 'Done';
            URL.revokeObjectURL(videoUrl);
        };

        video.currentTime = 0;
        video.onended = function () { rec.stop(); };
        video.onerror = function () { try { rec.stop(); } catch (e) {} running = false; };

        var dur = video.duration || 0;
        function frame() {
            if (!running) return;
            ctx.drawImage(video, 0, 0, W, H);
            drawWatermark(ctx, W, H, text, logoImg, pos, opacity, size);
            if (dur > 0) {
                var p = Math.min(100, Math.round((video.currentTime / dur) * 100));
                progBar.style.width = p + '%';
                progBar.textContent = p + '%';
            }
            if (!video.ended && !video.paused) requestAnimationFrame(frame);
        }

        var playPromise = video.play();
        if (playPromise && playPromise.catch) {
            playPromise.then(function () { rec.start(250); frame(); })
                .catch(function () { running = false; showError('The video could not play. Try a different file.'); });
        } else {
            rec.start(250); frame();
        }
    }
})();
</script>
@endsection
