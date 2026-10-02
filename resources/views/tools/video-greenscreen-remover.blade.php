@extends('layouts.app')

@section('title', 'Video Greenscreen Remover - Azlaan Tools')
@section('meta_description', 'Remove or replace green screen backgrounds from videos online for free. Everything runs in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Video Greenscreen Remover</h1>
            <p class="lead text-muted">Remove or replace a green screen background. Upload a video, set the key color and tolerance, preview the result, and record and download the new video — completely free, all inside your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="videoFile" class="form-label fw-semibold">Select a video file</label>
                        <input type="file" class="form-control" id="videoFile" accept="video/*">
                        <div class="form-text">MP4, WebM and similar. For large videos the processing runs on your device and may take a while.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label for="keyColor" class="form-label fw-semibold">Key color</label>
                            <input type="color" class="form-control form-control-color w-100" id="keyColor" value="#00ff00">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="tolerance" class="form-label fw-semibold">Tolerance: <span id="tolVal">90</span></label>
                            <input type="range" class="form-range" id="tolerance" min="10" max="220" value="90">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="bgMode" class="form-label fw-semibold">New background</label>
                            <select class="form-select" id="bgMode">
                                <option value="transparent">Transparent (checker)</option>
                                <option value="color">Solid color</option>
                                <option value="image">Your own image</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 d-none" id="bgColorWrap">
                            <label for="bgColor" class="form-label fw-semibold">Background color</label>
                            <input type="color" class="form-control form-control-color w-100" id="bgColor" value="#1e3a8a">
                        </div>
                        <div class="col-12 d-none" id="bgImageWrap">
                            <label for="bgImage" class="form-label fw-semibold">Background image</label>
                            <input type="file" class="form-control" id="bgImage" accept="image/*">
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-primary" id="previewBtn">Start Preview</button>
                        <button type="button" class="btn btn-success" id="recordBtn" disabled>Record &amp; Download</button>
                        <button type="button" class="btn btn-outline-secondary" id="stopBtn" disabled>Stop</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div class="ratio ratio-16x9 bg-light border rounded overflow-hidden">
                        <canvas id="outCanvas" width="640" height="360" class="w-100 h-100"></canvas>
                    </div>
                    <p class="text-muted small mt-2 mb-0" id="statusLine">First select a video, then press "Start Preview". The recording downloads in WebM format.</p>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the video with the green screen.</li>
                <li>Adjust the key color (usually green) and the tolerance slider until the background is cleanly removed.</li>
                <li>Use "Start Preview" to see the live result, then save the new video with "Record &amp; Download".</li>
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
    var keyColor = document.getElementById('keyColor');
    var tolerance = document.getElementById('tolerance');
    var tolVal = document.getElementById('tolVal');
    var bgMode = document.getElementById('bgMode');
    var bgColorWrap = document.getElementById('bgColorWrap');
    var bgColor = document.getElementById('bgColor');
    var bgImageWrap = document.getElementById('bgImageWrap');
    var bgImage = document.getElementById('bgImage');
    var previewBtn = document.getElementById('previewBtn');
    var recordBtn = document.getElementById('recordBtn');
    var stopBtn = document.getElementById('stopBtn');
    var errorBox = document.getElementById('errorBox');
    var canvas = document.getElementById('outCanvas');
    var statusLine = document.getElementById('statusLine');
    var results = document.getElementById('results');

    var video = document.createElement('video');
    video.muted = true;
    video.loop = true;
    video.playsInline = true;
    video.style.display = 'none';
    document.body.appendChild(video);

    var procCanvas = document.createElement('canvas');
    var procCtx = procCanvas.getContext('2d');
    var outCtx = canvas.getContext('2d');
    var bgImgEl = null;
    var running = false;
    var rafId = null;
    var recorder = null;
    var chunks = [];
    var videoURL = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    tolerance.addEventListener('input', function () { tolVal.textContent = tolerance.value; });
    bgMode.addEventListener('change', function () {
        bgColorWrap.classList.toggle('d-none', bgMode.value !== 'color');
        bgImageWrap.classList.toggle('d-none', bgMode.value !== 'image');
    });
    bgImage.addEventListener('change', function () {
        var f = bgImage.files[0];
        if (!f) { bgImgEl = null; return; }
        var img = new Image();
        img.onload = function () { bgImgEl = img; URL.revokeObjectURL(img.src); };
        img.src = URL.createObjectURL(f);
    });
    videoFile.addEventListener('change', function () {
        stopAll();
        var f = videoFile.files[0];
        if (!f) return;
        if (videoURL) URL.revokeObjectURL(videoURL);
        videoURL = URL.createObjectURL(f);
        video.src = videoURL;
        video.load();
        statusLine.textContent = 'Loading video... then press "Start Preview".';
    });

    function hexToRgb(hex) {
        var h = hex.replace('#', '');
        return [parseInt(h.substr(0, 2), 16), parseInt(h.substr(2, 2), 16), parseInt(h.substr(4, 2), 16)];
    }

    function drawChecker(w, h) {
        outCtx.fillStyle = '#ffffff';
        outCtx.fillRect(0, 0, w, h);
        outCtx.fillStyle = '#d7dce2';
        var s = Math.max(12, Math.floor(w / 32));
        for (var y = 0; y < h; y += s) {
            for (var x = 0; x < w; x += s) {
                if (((x / s) + (y / s)) % 2 === 0) outCtx.fillRect(x, y, s, s);
            }
        }
    }

    function paintBackground(w, h) {
        var mode = bgMode.value;
        if (mode === 'color') {
            outCtx.fillStyle = bgColor.value;
            outCtx.fillRect(0, 0, w, h);
        } else if (mode === 'image' && bgImgEl) {
            outCtx.drawImage(bgImgEl, 0, 0, w, h);
        } else {
            drawChecker(w, h);
        }
    }

    function processFrame() {
        if (!running) return;
        var w = canvas.width, h = canvas.height;
        var key = hexToRgb(keyColor.value);
        var tol = parseInt(tolerance.value, 10);
        paintBackground(w, h);
        if (video.readyState >= 2 && video.videoWidth) {
            procCtx.drawImage(video, 0, 0, w, h);
            var frame = procCtx.getImageData(0, 0, w, h);
            var d = frame.data;
            var kr = key[0], kg = key[1], kb = key[2];
            for (var i = 0; i < d.length; i += 4) {
                var dr = d[i] - kr, dg = d[i + 1] - kg, db = d[i + 2] - kb;
                var dist = Math.sqrt(dr * dr + dg * dg + db * db);
                if (dist < tol) {
                    d[i + 3] = 0;
                } else if (dist < tol * 1.6) {
                    d[i + 3] = Math.round(255 * (dist - tol) / (tol * 0.6));
                }
            }
            procCtx.putImageData(frame, 0, 0);
            outCtx.drawImage(procCanvas, 0, 0);
        }
        rafId = requestAnimationFrame(processFrame);
    }

    function startPreview() {
        hideError();
        if (!videoFile.files[0]) { showError('Please select a video file first.'); return; }
        if (video.videoWidth) {
            var aspect = video.videoWidth / video.videoHeight;
            var w = Math.min(960, Math.round(video.videoWidth));
            var hgt = Math.round(w / aspect);
            canvas.width = w; canvas.height = hgt;
            procCanvas.width = w; procCanvas.height = hgt;
        }
        running = true;
        video.play().catch(function () {});
        recordBtn.disabled = false;
        stopBtn.disabled = false;
        previewBtn.disabled = true;
        statusLine.textContent = 'Preview is running. Adjust tolerance with the slider, then record.';
        processFrame();
    }

    function stopAll() {
        running = false;
        if (rafId) cancelAnimationFrame(rafId);
        rafId = null;
        video.pause();
        if (recorder && recorder.state !== 'inactive') {
            try { recorder.stop(); } catch (e) {}
        }
        recorder = null;
        previewBtn.disabled = false;
        recordBtn.disabled = true;
        stopBtn.disabled = true;
    }

    previewBtn.addEventListener('click', startPreview);
    stopBtn.addEventListener('click', function () {
        stopAll();
        statusLine.textContent = 'Stopped. Press "Start Preview" to preview again.';
    });

    recordBtn.addEventListener('click', function () {
        hideError();
        if (!running) { showError('Start the preview first.'); return; }
        try {
            var stream = canvas.captureStream(30);
            chunks = [];
            var mime = MediaRecorder.isTypeSupported('video/webm;codecs=vp9') ? 'video/webm;codecs=vp9' : 'video/webm';
            recorder = new MediaRecorder(stream, { mimeType: mime, videoBitsPerSecond: 5000000 });
            recorder.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
            recorder.onstop = function () {
                var blob = new Blob(chunks, { type: 'video/webm' });
                var url = URL.createObjectURL(blob);
                results.classList.remove('d-none');
                results.innerHTML = '';
                var ok = document.createElement('div');
                ok.className = 'alert alert-success';
                ok.textContent = 'Recording is ready! (' + (blob.size / 1048576).toFixed(1) + ' MB)';
                var a = document.createElement('a');
                a.className = 'btn btn-primary';
                a.href = url;
                a.download = 'greenscreen-removed.webm';
                a.textContent = 'Download Video (WebM)';
                results.appendChild(ok);
                results.appendChild(a);
                statusLine.textContent = 'Recording finished. Use the download button above to save it.';
            };
            recorder.start();
            video.currentTime = 0;
            video.play().catch(function () {});
            recordBtn.disabled = true;
            statusLine.textContent = 'Recording... press "Stop" when the video ends.';
        } catch (err) {
            showError('Could not start recording: ' + err.message);
        }
    });
})();
</script>
@endsection
