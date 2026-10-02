@extends('layouts.app')

@section('title', 'Video Resizer - Azlaan Tools')
@section('meta_description', 'Resize video resolution in your browser: downscale 4K to 1080p or 720p, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Video Resizer</h1>
            <p class="lead text-muted">Lower the resolution of your video — 4K to 1080p or 720p — smaller file, quality kept. Everything happens in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">Video file</label>
                        <input type="file" class="form-control" id="fileInput" accept="video/*">
                        <div class="form-text">MP4, WebM and similar. The file is never uploaded.</div>
                    </div>
                    <div id="infoBox" class="alert alert-info d-none" role="status"></div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="targetSel" class="form-label fw-semibold">Target resolution</label>
                            <select class="form-select" id="targetSel">
                                <option value="480">480p</option>
                                <option value="720" selected>720p</option>
                                <option value="1080">1080p</option>
                                <option value="2160">Keep original</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="aspectChk" checked>
                                <label class="form-check-label" for="aspectChk">Keep aspect ratio</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Resize Video</button>
                    <div class="progress mt-3 d-none" id="progWrap">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progBar" style="width:0%"></div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="okBox" role="alert"></div>
                        <a href="#" class="btn btn-success w-100" id="dlBtn">Download Resized Video</a>
                        <div class="form-text mt-2 text-center">The output format depends on your browser (Chrome/Edge: WebM, Safari: MP4).</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your video file — its original resolution will appear below.</li>
                <li>Choose the target resolution (720p is the most common).</li>
                <li>Press Resize Video, wait for the processing to finish, then download.</li>
            </ol>
            <p class="text-muted small">Note: resizing happens in real time (the longer the video, the more time it takes). Audio is kept as it is.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('fileInput');
    var targetSel = document.getElementById('targetSel');
    var aspectChk = document.getElementById('aspectChk');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var okBox = document.getElementById('okBox');
    var dlBtn = document.getElementById('dlBtn');
    var progWrap = document.getElementById('progWrap');
    var progBar = document.getElementById('progBar');
    var infoBox = document.getElementById('infoBox');

    var srcUrl = null;
    var metaW = 0, metaH = 0, metaDur = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
        progWrap.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    fileInput.addEventListener('change', function () {
        hideError();
        var file = fileInput.files[0];
        if (!file) { infoBox.classList.add('d-none'); return; }
        if (srcUrl) URL.revokeObjectURL(srcUrl);
        srcUrl = URL.createObjectURL(file);
        var v = document.createElement('video');
        v.preload = 'metadata';
        v.src = srcUrl;
        v.onloadedmetadata = function () {
            metaW = v.videoWidth;
            metaH = v.videoHeight;
            metaDur = v.duration;
            infoBox.textContent = 'Original: ' + metaW + 'x' + metaH + ' px, ' + metaDur.toFixed(1) + ' seconds.';
            infoBox.classList.remove('d-none');
        };
        v.onerror = function () {
            infoBox.textContent = 'This video could not be opened in your browser.';
            infoBox.classList.remove('d-none');
        };
    });

    function pickMime() {
        var cands = [
            'video/mp4;codecs=avc1.42E01E,mp4a.40.2',
            'video/mp4',
            'video/webm;codecs=vp9,opus',
            'video/webm;codecs=vp8,opus',
            'video/webm'
        ];
        for (var i = 0; i < cands.length; i++) {
            if (window.MediaRecorder && MediaRecorder.isTypeSupported(cands[i])) return cands[i];
        }
        return '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var file = fileInput.files[0];
        if (!file) { showError('Please select a video file first.'); return; }
        if (!metaW || !metaH) { showError('The video info is still loading — wait a few seconds and press again.'); return; }
        if (typeof MediaRecorder === 'undefined') {
            showError('Your browser does not support video encoding. Use Chrome or Edge.');
            return;
        }

        var targetH = parseInt(targetSel.value, 10);
        var outH = targetH >= 2160 ? metaH : targetH;
        var outW;
        if (aspectChk.checked) {
            outW = Math.round(outH * metaW / metaH);
        } else {
            outW = Math.round(outH * 16 / 9);
        }
        outW = Math.max(2, outW - (outW % 2));
        outH = Math.max(2, outH - (outH % 2));

        goBtn.disabled = true;
        progWrap.classList.remove('d-none');
        progBar.style.width = '0%';
        progBar.textContent = 'Starting...';

        var video = document.createElement('video');
        video.muted = true;
        video.playsInline = true;
        video.preload = 'auto';
        video.src = srcUrl;

        var canvas = document.createElement('canvas');
        canvas.width = outW;
        canvas.height = outH;
        var ctx = canvas.getContext('2d');

        var mime = pickMime();
        var stream = canvas.captureStream(30);
        var audioCtx = null;
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var srcNode = audioCtx.createMediaElementSource(video);
            var dest = audioCtx.createMediaStreamDestination();
            srcNode.connect(dest);
            srcNode.connect(audioCtx.destination);
            var aTracks = dest.stream.getAudioTracks();
            for (var t = 0; t < aTracks.length; t++) stream.addTrack(aTracks[t]);
        } catch (e) { /* audio optional */ }

        var chunks = [];
        var rec;
        try {
            rec = new MediaRecorder(stream, mime ? { mimeType: mime, videoBitsPerSecond: 4000000 } : undefined);
        } catch (e) {
            showError('Could not start the recorder: ' + e.message);
            goBtn.disabled = false;
            return;
        }
        rec.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        rec.onerror = function () {
            showError('An error happened during recording. Please try again.');
            goBtn.disabled = false;
        };
        rec.onstop = function () {
            var type = mime ? mime.split(';')[0] : 'video/webm';
            var blob = new Blob(chunks, { type: type });
            var url = URL.createObjectURL(blob);
            var ext = type.indexOf('mp4') !== -1 ? 'mp4' : 'webm';
            var base = file.name.replace(/\.[a-z0-9]+$/i, '') || 'video';
            dlBtn.href = url;
            dlBtn.download = base + '-' + outH + 'p.' + ext;
            okBox.textContent = 'Done! Resized to ' + outW + 'x' + outH + ' px (' +
                (blob.size / 1048576).toFixed(1) + ' MB).';
            results.classList.remove('d-none');
            progWrap.classList.add('d-none');
            goBtn.disabled = false;
            if (audioCtx) { try { audioCtx.close(); } catch (e) {} }
        };

        var rafId = null;
        function drawLoop() {
            if (video.readyState >= 2) {
                ctx.drawImage(video, 0, 0, outW, outH);
            }
            rafId = requestAnimationFrame(drawLoop);
        }

        video.onended = function () {
            cancelAnimationFrame(rafId);
            setTimeout(function () {
                if (rec.state !== 'inactive') rec.stop();
            }, 400);
        };
        video.ontimeupdate = function () {
            if (metaDur > 0) {
                var pct = Math.min(99, Math.round(video.currentTime / metaDur * 100));
                progBar.style.width = pct + '%';
                progBar.textContent = pct + '%';
            }
        };
        video.onerror = function () {
            cancelAnimationFrame(rafId);
            showError('Could not process the video. / Please try another file.');
            goBtn.disabled = false;
        };

        video.play().then(function () {
            drawLoop();
            rec.start(1000);
        }).catch(function (e) {
            showError('Could not play the video: ' + e.message);
            goBtn.disabled = false;
        });
    });
})();
</script>
@endsection
