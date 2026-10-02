@extends('layouts.app')

@section('title', 'Blur Video Background - Azlaan Tools')
@section('meta_description', 'Blur your video background and keep the subject sharp — free online, files are processed only in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Blur Video Background</h1>
            <p class="lead text-muted">Upload a video, set the circle for the area (face/subject) you want to keep sharp — the rest of the background will be blurred. Best for privacy. Your file is never uploaded; everything happens in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="vidFile" class="form-label fw-semibold">Select a video file</label>
                        <input type="file" class="form-control" id="vidFile" accept="video/*">
                        <div class="form-text">MP4/WebM and more. Note: face is not auto-detected — you set the subject area yourself with the slider.</div>
                    </div>

                    <div id="editorPanel" class="d-none">
                        <canvas id="stage" class="w-100 rounded border mb-3" style="max-height:420px;background:#e9ecef;"></canvas>

                        <div class="row g-2">
                            <div class="col-6">
                                <label for="subX" class="form-label">Subject X (left-right) <span class="badge bg-light text-dark" id="subXVal">50%</span></label>
                                <input type="range" class="form-range" id="subX" min="5" max="95" value="50">
                            </div>
                            <div class="col-6">
                                <label for="subY" class="form-label">Subject Y (up-down) <span class="badge bg-light text-dark" id="subYVal">50%</span></label>
                                <input type="range" class="form-range" id="subY" min="5" max="95" value="50">
                            </div>
                            <div class="col-6">
                                <label for="subW" class="form-label">Subject width <span class="badge bg-light text-dark" id="subWVal">40%</span></label>
                                <input type="range" class="form-range" id="subW" min="10" max="90" value="40">
                            </div>
                            <div class="col-6">
                                <label for="subH" class="form-label">Subject height <span class="badge bg-light text-dark" id="subHVal">70%</span></label>
                                <input type="range" class="form-range" id="subH" min="10" max="95" value="70">
                            </div>
                            <div class="col-12">
                                <label for="blurAmt" class="form-label">Blur strength <span class="badge bg-light text-dark" id="blurAmtVal">14px</span></label>
                                <input type="range" class="form-range" id="blurAmt" min="2" max="30" value="14">
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <button type="button" class="btn btn-secondary flex-fill" id="previewBtn">▶ Preview</button>
                            <button type="button" class="btn btn-primary flex-fill" id="downloadBtn">⬇ Download Blurred Video</button>
                        </div>
                        <div class="progress mt-3 d-none" id="recProg"><div class="progress-bar progress-bar-striped progress-bar-animated" id="recBar" style="width:0%"></div></div>
                        <div class="form-text mt-2" id="statusText">Press Preview — adjust the circle with the sliders, then download (WebM format).</div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your video file.</li>
                <li>Press <strong>Preview</strong> — the video will play and the background will look blurred.</li>
                <li>Use the sliders to place the subject (face) circle exactly in the right place.</li>
                <li>Press <strong>Download Blurred Video</strong> — the whole video will be recorded and downloaded as a WebM file.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var vidFile = document.getElementById('vidFile');
    var editorPanel = document.getElementById('editorPanel');
    var stage = document.getElementById('stage');
    var ctx = stage.getContext('2d');
    var previewBtn = document.getElementById('previewBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var errorBox = document.getElementById('errorBox');
    var recProg = document.getElementById('recProg');
    var recBar = document.getElementById('recBar');
    var statusText = document.getElementById('statusText');

    var video = document.createElement('video');
    video.muted = true;
    video.playsInline = true;
    var rafId = null, playing = false, recording = false, recorder = null, chunks = [];

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function params() {
        return {
            cx: stage.width * (parseFloat(document.getElementById('subX').value) / 100),
            cy: stage.height * (parseFloat(document.getElementById('subY').value) / 100),
            rx: (stage.width * (parseFloat(document.getElementById('subW').value) / 100)) / 2,
            ry: (stage.height * (parseFloat(document.getElementById('subH').value) / 100)) / 2,
            blur: parseInt(document.getElementById('blurAmt').value, 10)
        };
    }

    function drawFrame() {
        if (video.readyState < 2) return;
        var p = params();
        ctx.filter = 'blur(' + p.blur + 'px)';
        ctx.drawImage(video, 0, 0, stage.width, stage.height);
        ctx.filter = 'none';
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(p.cx, p.cy, p.rx, p.ry, 0, 0, Math.PI * 2);
        ctx.clip();
        ctx.drawImage(video, 0, 0, stage.width, stage.height);
        ctx.restore();
    }

    function loop() {
        drawFrame();
        if (playing || recording) rafId = requestAnimationFrame(loop);
    }

    function bindSlider(id, outId, suffix) {
        var el = document.getElementById(id), out = document.getElementById(outId);
        el.addEventListener('input', function () { out.textContent = el.value + suffix; });
    }
    bindSlider('subX', 'subXVal', '%'); bindSlider('subY', 'subYVal', '%');
    bindSlider('subW', 'subWVal', '%'); bindSlider('subH', 'subHVal', '%');
    bindSlider('blurAmt', 'blurAmtVal', 'px');

    vidFile.addEventListener('change', function () {
        hideError();
        var f = vidFile.files[0];
        if (!f) return;
        if (!f.type || f.type.indexOf('video/') !== 0) { showError('Please select a video file.'); return; }
        stopAll();
        if (video.src) URL.revokeObjectURL(video.src);
        video.src = URL.createObjectURL(f);
        video.onloadedmetadata = function () {
            var scale = Math.min(1, 640 / video.videoWidth);
            stage.width = Math.round(video.videoWidth * scale);
            stage.height = Math.round(video.videoHeight * scale);
            editorPanel.classList.remove('d-none');
            drawFrame();
            statusText.textContent = 'Video loaded (' + Math.round(video.duration) + ' sec). Press Preview.';
        };
        video.onerror = function () { showError('The video could not be loaded. Try another format.'); };
    });

    function stopAll() {
        playing = false; recording = false;
        if (rafId) cancelAnimationFrame(rafId);
        video.pause();
        previewBtn.textContent = '▶ Preview';
    }

    previewBtn.addEventListener('click', function () {
        hideError();
        if (recording) { showError('You cannot stop the preview during recording.'); return; }
        if (playing) { stopAll(); return; }
        playing = true;
        video.currentTime = 0;
        video.play();
        previewBtn.textContent = '⏸ Stop Preview';
        loop();
        video.onended = function () { if (!recording) stopAll(); };
    });

    downloadBtn.addEventListener('click', function () {
        hideError();
        if (recording) return;
        if (typeof MediaRecorder === 'undefined' || !stage.captureStream) {
            showError('Your browser does not support video recording. Try Chrome or Edge.');
            return;
        }
        chunks = [];
        var stream = stage.captureStream(30);
        var mime = MediaRecorder.isTypeSupported('video/webm;codecs=vp9') ? 'video/webm;codecs=vp9' : 'video/webm';
        try { recorder = new MediaRecorder(stream, { mimeType: mime, videoBitsPerSecond: 5000000 }); }
        catch (e) { showError('Recording could not start.'); return; }
        recorder.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        recorder.onstop = function () {
            recording = false;
            recProg.classList.add('d-none');
            stopAll();
            var blob = new Blob(chunks, { type: 'video/webm' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'blurred-background-video.webm';
            document.body.appendChild(a); a.click(); a.remove();
            statusText.textContent = 'Download is ready! (' + (blob.size / 1048576).toFixed(1) + ' MB)';
        };
        recording = true;
        playing = false;
        video.currentTime = 0;
        video.onended = function () { if (recorder && recorder.state !== 'inactive') recorder.stop(); };
        video.play();
        recorder.start(250);
        recProg.classList.remove('d-none');
        statusText.textContent = 'Recording... you will get the download when the video ends...';
        loop();
        var tick = setInterval(function () {
            if (!recording || !video.duration) { clearInterval(tick); return; }
            recBar.style.width = Math.min(100, (video.currentTime / video.duration) * 100) + '%';
        }, 300);
    });
})();
</script>
@endsection
