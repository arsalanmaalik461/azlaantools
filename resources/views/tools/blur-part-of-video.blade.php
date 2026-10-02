@extends('layouts.app')

@section('title', 'Blur Part Of Video - Azlaan Tools')
@section('meta_description', 'Blur or pixelate any region of a video in your browser. Censor faces, number plates and text free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Blur Part Of Video</h1>
            <p class="lead text-muted">Blur or pixelate any part of a video (face, number plate, text). The whole video is processed in your browser — nothing is uploaded, totally free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="videoFile" class="form-label fw-semibold">Choose a video file</label>
                        <input type="file" class="form-control" id="videoFile" accept="video/*">
                        <div class="form-text">MP4, WebM or any format your browser can play. The video stays on your device.</div>
                    </div>

                    <div id="editorArea" class="d-none">
                        <p class="fw-semibold mb-2">Select a region: drag with your mouse or finger on the image below to mark the part you want to blur.</p>
                        <canvas id="pickCanvas" class="img-fluid border rounded mb-3 w-100" style="cursor: crosshair; touch-action: none; max-height: 420px;"></canvas>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="blurAmount" class="form-label fw-semibold">Blur strength: <span id="blurVal" class="text-primary">14</span> px</label>
                                <input type="range" class="form-range" id="blurAmount" min="4" max="40" value="14">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="blurMode" class="form-label fw-semibold">Style</label>
                                <select class="form-select" id="blurMode">
                                    <option value="blur">Smooth blur</option>
                                    <option value="pixel">Pixelate (mosaic)</option>
                                </select>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100" id="goBtn">Apply Blur and Download</button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="clearRegionBtn">Clear region</button>

                        <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                        <div id="progressWrap" class="mt-3 d-none">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Processing video...</span>
                                <span id="progressPct">0%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <div id="results" class="d-none mt-4">
                            <div class="alert alert-success">
                                <strong>Done!</strong> Your blurred video is ready. Download it below.
                            </div>
                            <a href="#" class="btn btn-success w-100" id="dlLink" download="blurred-video.webm">Download Blurred Video (WebM)</a>
                            <p class="small text-muted mt-2">Note: the marked area stays blurred at the same spot through the whole video (static region). The blur will not follow moving objects. Output is in WebM format.</p>
                        </div>
                    </div>

                </div>
            </div>

            <video id="srcVideo" class="d-none" playsinline crossorigin="anonymous"></video>
            <canvas id="outCanvas" class="d-none"></canvas>

            <h2>How to use</h2>
            <ol>
                <li>Select your video file.</li>
                <li>On the first frame, drag to mark the part you want to hide (face, plate, text).</li>
                <li>Choose the blur strength and style (blur or pixelate).</li>
                <li>Press the button — the video will be processed and ready to download.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('videoFile');
    var editorArea = document.getElementById('editorArea');
    var pickCanvas = document.getElementById('pickCanvas');
    var pickCtx = pickCanvas.getContext('2d');
    var srcVideo = document.getElementById('srcVideo');
    var outCanvas = document.getElementById('outCanvas');
    var outCtx = outCanvas.getContext('2d');
    var frameCanvas = document.createElement('canvas');
    var frameCtx = frameCanvas.getContext('2d');
    var blurAmount = document.getElementById('blurAmount');
    var blurVal = document.getElementById('blurVal');
    var blurMode = document.getElementById('blurMode');
    var goBtn = document.getElementById('goBtn');
    var clearRegionBtn = document.getElementById('clearRegionBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var progressPct = document.getElementById('progressPct');
    var dlLink = document.getElementById('dlLink');

    var vidURL = null;
    var region = null;      // in video natural pixels
    var dragging = false;
    var dragStart = null;
    var frameReady = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    blurAmount.addEventListener('input', function () {
        blurVal.textContent = blurAmount.value;
    });

    fileInput.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        progressWrap.classList.add('d-none');
        region = null;
        frameReady = false;
        var f = fileInput.files[0];
        if (!f) { editorArea.classList.add('d-none'); return; }
        if (vidURL) { URL.revokeObjectURL(vidURL); }
        vidURL = URL.createObjectURL(f);
        srcVideo.src = vidURL;
        srcVideo.load();
    });

    srcVideo.addEventListener('loadedmetadata', function () {
        try { srcVideo.currentTime = Math.min(0.5, srcVideo.duration / 2); } catch (e) { drawFirstFrame(); }
    });
    srcVideo.addEventListener('seeked', drawFirstFrame);

    function drawFirstFrame() {
        var vw = srcVideo.videoWidth, vh = srcVideo.videoHeight;
        if (!vw || !vh) return;
        var maxW = 640;
        var scale = Math.min(1, maxW / vw);
        pickCanvas.width = Math.round(vw * scale);
        pickCanvas.height = Math.round(vh * scale);
        pickCtx.drawImage(srcVideo, 0, 0, pickCanvas.width, pickCanvas.height);
        frameReady = true;
        editorArea.classList.remove('d-none');
    }

    function redrawPick() {
        if (!frameReady) return;
        pickCtx.drawImage(srcVideo, 0, 0, pickCanvas.width, pickCanvas.height);
        if (region) {
            var sx = pickCanvas.width / srcVideo.videoWidth;
            var sy = pickCanvas.height / srcVideo.videoHeight;
            pickCtx.save();
            pickCtx.strokeStyle = '#0d6efd';
            pickCtx.lineWidth = 2;
            pickCtx.setLineDash([6, 4]);
            pickCtx.strokeRect(region.x * sx, region.y * sy, region.w * sx, region.h * sy);
            pickCtx.fillStyle = 'rgba(13,110,253,0.15)';
            pickCtx.fillRect(region.x * sx, region.y * sy, region.w * sx, region.h * sy);
            pickCtx.restore();
        }
    }

    function canvasPoint(evt) {
        var r = pickCanvas.getBoundingClientRect();
        var cx = (evt.touches && evt.touches[0]) ? evt.touches[0].clientX : evt.clientX;
        var cy = (evt.touches && evt.touches[0]) ? evt.touches[0].clientY : evt.clientY;
        var px = (cx - r.left) * (pickCanvas.width / r.width);
        var py = (cy - r.top) * (pickCanvas.height / r.height);
        return {
            x: px * (srcVideo.videoWidth / pickCanvas.width),
            y: py * (srcVideo.videoHeight / pickCanvas.height)
        };
    }

    function startDrag(e) {
        if (!frameReady) return;
        e.preventDefault();
        dragging = true;
        dragStart = canvasPoint(e);
        region = null;
    }
    function moveDrag(e) {
        if (!dragging || !dragStart) return;
        e.preventDefault();
        var p = canvasPoint(e);
        var x1 = Math.max(0, Math.min(dragStart.x, p.x));
        var y1 = Math.max(0, Math.min(dragStart.y, p.y));
        var x2 = Math.min(srcVideo.videoWidth, Math.max(dragStart.x, p.x));
        var y2 = Math.min(srcVideo.videoHeight, Math.max(dragStart.y, p.y));
        region = { x: x1, y: y1, w: x2 - x1, h: y2 - y1 };
        redrawPick();
    }
    function endDrag(e) {
        if (!dragging) return;
        if (e) e.preventDefault();
        dragging = false;
        dragStart = null;
        if (region && (region.w < 8 || region.h < 8)) { region = null; }
        redrawPick();
    }

    pickCanvas.addEventListener('mousedown', startDrag);
    pickCanvas.addEventListener('mousemove', moveDrag);
    pickCanvas.addEventListener('mouseup', endDrag);
    pickCanvas.addEventListener('mouseleave', function () { if (dragging) endDrag(null); });
    pickCanvas.addEventListener('touchstart', startDrag, { passive: false });
    pickCanvas.addEventListener('touchmove', moveDrag, { passive: false });
    pickCanvas.addEventListener('touchend', endDrag);

    clearRegionBtn.addEventListener('click', function () {
        region = null;
        redrawPick();
    });

    function applyEffect() {
        var vw = outCanvas.width, vh = outCanvas.height;
        frameCtx.drawImage(srcVideo, 0, 0, vw, vh);
        outCtx.drawImage(frameCanvas, 0, 0);
        if (!region) return;
        var rx = Math.round(region.x), ry = Math.round(region.y);
        var rw = Math.round(region.w), rh = Math.round(region.h);
        if (rw <= 0 || rh <= 0) return;
        if (blurMode.value === 'pixel') {
            var cell = Math.max(6, Math.floor(Math.min(rw, rh) / 10));
            var tw = Math.max(1, Math.floor(rw / cell));
            var th = Math.max(1, Math.floor(rh / cell));
            var tiny = document.createElement('canvas');
            tiny.width = tw; tiny.height = th;
            var tctx = tiny.getContext('2d');
            tctx.drawImage(frameCanvas, rx, ry, rw, rh, 0, 0, tw, th);
            outCtx.save();
            outCtx.imageSmoothingEnabled = false;
            outCtx.drawImage(tiny, 0, 0, tw, th, rx, ry, rw, rh);
            outCtx.restore();
        } else {
            outCtx.save();
            outCtx.beginPath();
            outCtx.rect(rx, ry, rw, rh);
            outCtx.clip();
            outCtx.filter = 'blur(' + blurAmount.value + 'px)';
            outCtx.drawImage(frameCanvas, rx, ry, rw, rh, rx, ry, rw, rh);
            outCtx.restore();
            outCtx.filter = 'none';
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        if (!frameReady) { showError('Please select a video file first.'); return; }
        if (!region) { showError('Please drag on the image to mark the blur region first.'); return; }
        if (typeof MediaRecorder === 'undefined' || !outCanvas.captureStream) {
            showError('Sorry, your browser does not support video recording. Try Chrome or Edge.');
            return;
        }

        var vw = srcVideo.videoWidth, vh = srcVideo.videoHeight;
        outCanvas.width = vw; outCanvas.height = vh;
        frameCanvas.width = vw; frameCanvas.height = vh;

        goBtn.disabled = true;
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';

        var stream = outCanvas.captureStream(30);
        var recorder;
        try {
            recorder = new MediaRecorder(stream, { mimeType: 'video/webm' });
        } catch (e) {
            try { recorder = new MediaRecorder(stream); }
            catch (e2) {
                showError('Recording could not start: ' + e2.message);
                goBtn.disabled = false;
                progressWrap.classList.add('d-none');
                return;
            }
        }

        var chunks = [];
        recorder.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        recorder.onstop = function () {
            goBtn.disabled = false;
            progressWrap.classList.add('d-none');
            if (!chunks.length) { showError('The video could not be processed. Please try again.'); return; }
            var blob = new Blob(chunks, { type: 'video/webm' });
            if (dlLink.href && dlLink.href.indexOf('blob:') === 0) { URL.revokeObjectURL(dlLink.href); }
            dlLink.href = URL.createObjectURL(blob);
            dlLink.download = 'blurred-video.webm';
            results.classList.remove('d-none');
        };

        var rafId = null;
        function loop() {
            if (srcVideo.paused || srcVideo.ended) {
                try { recorder.stop(); } catch (e) { /* noop */ }
                return;
            }
            applyEffect();
            rafId = requestAnimationFrame(loop);
        }

        srcVideo.onended = function () {
            try { recorder.stop(); } catch (e) { /* noop */ }
            if (rafId) cancelAnimationFrame(rafId);
            srcVideo.onended = null;
        };
        srcVideo.ontimeupdate = function () {
            if (srcVideo.duration) {
                var p = Math.min(100, Math.round((srcVideo.currentTime / srcVideo.duration) * 100));
                progressBar.style.width = p + '%';
                progressPct.textContent = p + '%';
            }
        };

        try { srcVideo.currentTime = 0; } catch (e) { /* noop */ }
        var playP = srcVideo.play();
        if (playP && playP.catch) {
            playP.catch(function (err) {
                showError('The video could not play: ' + err.message);
                goBtn.disabled = false;
                progressWrap.classList.add('d-none');
            });
        }
        recorder.start(250);
        loop();
    });
})();
</script>
@endsection
