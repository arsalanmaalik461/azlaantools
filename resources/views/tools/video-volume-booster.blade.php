@extends('layouts.app')
@section('title', 'Video Volume Booster - Increase Video Audio Free | Azlaan Tools')
@section('meta_description', 'Boost low-volume video audio for free online. Raise the sound up to 3x and download a new louder video — processed in your browser, no upload.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Video Volume Booster</h1>
            <p class="lead text-muted">Make a quiet video louder. Raise the gain up to 3x, listen to the preview, then download the louder video — everything happens in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="videoInput" class="form-label fw-semibold">Select a video file</label>
                        <input type="file" class="form-control" id="videoInput" accept="video/*">
                        <div class="form-text">MP4, WebM or any browser-supported video.</div>
                    </div>

                    <div id="stageWrap" class="d-none">
                        <div class="mb-3">
                            <video id="videoEl" class="w-100 rounded border" controls playsinline></video>
                        </div>
                        <div class="mb-3">
                            <label for="gainRange" class="form-label fw-semibold">Volume boost: <span id="gainVal">2.0</span>x</label>
                            <input type="range" class="form-range" id="gainRange" min="0.5" max="3" step="0.1" value="2">
                            <div class="form-text">1x = normal, 2x = double, 3x = maximum. Sound can crackle at high gain — listen to the preview first.</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary" id="previewBtn">▶ Preview with Boost</button>
                            <button type="button" class="btn btn-primary" id="goBtn">Boost &amp; Download Video</button>
                            <button type="button" class="btn btn-outline-danger d-none" id="stopBtn">Stop Recording</button>
                        </div>
                        <div class="progress mt-3 d-none" id="progWrap">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progBar" role="progressbar" style="width:0%">0%</div>
                        </div>
                        <p class="text-muted mt-2 mb-0 small" id="statusLine"></p>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="successBox" role="alert"></div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>How it works:</strong> The video plays in your browser, digital gain makes the audio louder, and the screen plus sound is recorded into a new video (WebM). For a long video, reaching the download takes about as long as the video length. <strong>Privacy:</strong> nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your video file.</li>
                <li>Set the volume boost slider (from 1x to 3x).</li>
                <li>Press <strong>Preview with Boost</strong> to check that the sound is right.</li>
                <li>Press <strong>Boost &amp; Download Video</strong> — when the recording finishes, the louder video will download.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var videoInput = document.getElementById('videoInput');
    var stageWrap = document.getElementById('stageWrap');
    var videoEl = document.getElementById('videoEl');
    var gainRange = document.getElementById('gainRange');
    var gainVal = document.getElementById('gainVal');
    var previewBtn = document.getElementById('previewBtn');
    var goBtn = document.getElementById('goBtn');
    var stopBtn = document.getElementById('stopBtn');
    var progWrap = document.getElementById('progWrap');
    var progBar = document.getElementById('progBar');
    var statusLine = document.getElementById('statusLine');
    var errorBox = document.getElementById('errorBox');
    var successBox = document.getElementById('successBox');

    var audioCtx = null, gainNode = null, srcNode = null, streamDest = null;
    var recorder = null, chunks = [], recording = false, fileName = 'video';
    var canvas = null, rafId = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function hideAlerts() {
        errorBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }
    function setStatus(t) { statusLine.textContent = t; }

    gainRange.addEventListener('input', function () {
        gainVal.textContent = parseFloat(gainRange.value).toFixed(1);
        if (gainNode) gainNode.gain.value = parseFloat(gainRange.value);
    });

    function ensureAudio() {
        if (audioCtx) return true;
        var AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) { showError('Web Audio is not supported in this browser.'); return false; }
        audioCtx = new AC();
        gainNode = audioCtx.createGain();
        gainNode.gain.value = parseFloat(gainRange.value);
        try {
            srcNode = audioCtx.createMediaElementSource(videoEl);
        } catch (e) {
            showError('Audio graph could not be created for this video.');
            return false;
        }
        srcNode.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        streamDest = audioCtx.createMediaStreamDestination();
        gainNode.connect(streamDest);
        return true;
    }

    videoInput.addEventListener('change', function () {
        hideAlerts();
        stopRecording(true);
        var f = videoInput.files[0];
        if (!f) return;
        fileName = f.name.replace(/\.[^.]+$/, '') || 'video';
        if (videoEl.src) URL.revokeObjectURL(videoEl.src);
        videoEl.src = URL.createObjectURL(f);
        videoEl.load();
        stageWrap.classList.remove('d-none');
        setStatus('Video loaded. Listen to the preview first, then boost.');
    });

    previewBtn.addEventListener('click', function () {
        hideAlerts();
        if (!videoEl.src) { showError('Please select a video first.'); return; }
        if (!ensureAudio()) return;
        if (audioCtx.state === 'suspended') audioCtx.resume();
        videoEl.currentTime = 0;
        videoEl.play().catch(function () { showError('Video could not play in this browser.'); });
    });

    function pickMime() {
        var cands = ['video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm'];
        for (var i = 0; i < cands.length; i++) {
            if (window.MediaRecorder && MediaRecorder.isTypeSupported(cands[i])) return cands[i];
        }
        return '';
    }

    function stopRecording(silent) {
        if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
        if (recorder && recording) {
            try { recorder.stop(); } catch (e) {}
        }
        if (!silent) videoEl.pause();
        recording = false;
        stopBtn.classList.add('d-none');
    }

    goBtn.addEventListener('click', function () {
        hideAlerts();
        if (!videoEl.src) { showError('Please select a video first.'); return; }
        if (!ensureAudio()) return;
        if (typeof MediaRecorder === 'undefined') { showError('MediaRecorder is not supported in this browser.'); return; }
        var mime = pickMime();
        if (!mime) { showError('WebM recording is not supported in this browser.'); return; }
        if (videoEl.videoWidth === 0) { showError('The video is still loading — wait a few seconds and try again.'); return; }

        if (audioCtx.state === 'suspended') audioCtx.resume();

        canvas = document.createElement('canvas');
        canvas.width = videoEl.videoWidth;
        canvas.height = videoEl.videoHeight;
        var ctx = canvas.getContext('2d');

        function draw() {
            try { ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height); } catch (e) {}
            rafId = requestAnimationFrame(draw);
        }

        var vidStream = canvas.captureStream(30);
        var combined = new MediaStream();
        vidStream.getVideoTracks().forEach(function (t) { combined.addTrack(t); });
        streamDest.stream.getAudioTracks().forEach(function (t) { combined.addTrack(t); });

        chunks = [];
        recorder = new MediaRecorder(combined, { mimeType: mime, videoBitsPerSecond: 5000000 });
        recorder.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        recorder.onstop = function () {
            stopRecording(true);
            progWrap.classList.add('d-none');
            var blob = new Blob(chunks, { type: 'video/webm' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = fileName + '-louder.webm';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 3000);
            showSuccess('Done! Louder video downloaded.');
            setStatus('');
            goBtn.disabled = false;
        };

        videoEl.onended = function () {
            if (recording && recorder && recorder.state === 'recording') recorder.stop();
        };
        videoEl.ontimeupdate = function () {
            if (recording && videoEl.duration) {
                var p = Math.min(100, Math.round((videoEl.currentTime / videoEl.duration) * 100));
                progBar.style.width = p + '%';
                progBar.textContent = p + '%';
            }
        };

        try {
            draw();
            videoEl.currentTime = 0;
            videoEl.muted = false;
            recorder.start(1000);
            recording = true;
            videoEl.play().catch(function () {
                showError('Video could not play.');
                stopRecording();
            });
            progWrap.classList.remove('d-none');
            progBar.style.width = '0%';
            progBar.textContent = '0%';
            stopBtn.classList.remove('d-none');
            goBtn.disabled = true;
            setStatus('Recording with boosted audio... wait until the video ends.');
        } catch (e) {
            showError('Recording failed to start: ' + e.message);
            stopRecording();
        }
    });

    stopBtn.addEventListener('click', function () {
        if (recorder && recording) recorder.stop();
        setStatus('Recording stopped.');
        goBtn.disabled = false;
    });
})();
</script>
@endsection
