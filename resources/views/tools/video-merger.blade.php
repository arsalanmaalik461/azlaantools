@extends('layouts.app')

@section('title', 'Video Merger - Azlaan Tools')
@section('meta_description', 'Merge multiple videos into one file online for free, no watermark.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Video Merger</h1>
            <p class="lead text-muted">Join multiple video clips into one video — completely free, no watermark. Everything happens in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="vidFiles" class="form-label fw-semibold">Choose videos (in order)</label>
                        <input type="file" class="form-control" id="vidFiles" accept="video/*" multiple>
                        <div class="form-text">Clips join in the order you select them. / Clips join in selection order.</div>
                    </div>
                    <div class="mb-3">
                        <label for="resSel" class="form-label fw-semibold">Output size</label>
                        <select class="form-select" id="resSel">
                            <option value="720">720p (fast, small file)</option>
                            <option value="1080" selected>1080p (recommended)</option>
                            <option value="source">Same as first video (source size)</option>
                        </select>
                        <div class="form-text">Output format: WebM video (plays in every browser and on WhatsApp).</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Merge Videos</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div class="progress mt-3 d-none" id="progressWrap" style="height: 22px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width: 0%;">0%</div>
                    </div>
                    <p class="text-muted small mt-2 d-none" id="statusText"></p>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success"><strong>Done!</strong> <span id="clipInfo"></span></div>
                        <video id="preview" class="w-100 rounded border mb-3" controls playsinline></video>
                        <a href="#" class="btn btn-success w-100" id="dlLink" download="merged-video.webm">Download Merged Video</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your video clips (hold Ctrl to select multiple files).</li>
                <li>Choose the output size and press <strong>Merge Videos</strong>.</li>
                <li>Each clip plays one after another and is recorded — when it finishes, press <strong>Download</strong>.</li>
            </ol>
            <h2>Note</h2>
            <p>The video is recorded again (re-encoded), so long videos may take some time. Do not close the browser tab during the merge.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var filesInput = document.getElementById('vidFiles');
    var resSel = document.getElementById('resSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var statusText = document.getElementById('statusText');
    var dlLink = document.getElementById('dlLink');
    var preview = document.getElementById('preview');
    var clipInfo = document.getElementById('clipInfo');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
        progressWrap.classList.add('d-none');
        statusText.classList.add('d-none');
        goBtn.disabled = false;
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(pct, label) {
        pct = Math.max(0, Math.min(100, Math.round(pct)));
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
        statusText.textContent = label;
    }
    function pickMime() {
        var cands = ['video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm', ''];
        for (var i = 0; i < cands.length; i++) {
            if (!cands[i]) return '';
            try { if (window.MediaRecorder && MediaRecorder.isTypeSupported(cands[i])) return cands[i]; } catch (e) { /* ignore */ }
        }
        return '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var files = filesInput.files;
        if (!files || files.length < 2) { showError('Please select at least 2 videos.'); return; }
        if (typeof canvasCaptureCheck() === 'string') { showError(canvasCaptureCheck()); return; }
        goBtn.disabled = true;
        progressWrap.classList.remove('d-none');
        statusText.classList.remove('d-none');
        mergeAll(Array.prototype.slice.call(files));
    });

    function canvasCaptureCheck() {
        var c = document.createElement('canvas');
        if (!c.captureStream) return 'Your browser does not support video merging. Please use Chrome or Edge.';
        if (!window.MediaRecorder) return 'Your browser does not support recording. Please use Chrome or Edge.';
        return null;
    }

    function mergeAll(files) {
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        var stream = canvas.captureStream(30);
        var audioCtx = null, audioDest = null;
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            audioDest = audioCtx.createMediaStreamDestination();
        } catch (e) { audioCtx = null; }
        if (audioCtx && audioCtx.state === 'suspended') { audioCtx.resume(); }

        var combined = new MediaStream();
        stream.getVideoTracks().forEach(function (t) { combined.addTrack(t); });
        var mime = pickMime();
        var recorder;
        try {
            recorder = mime ? new MediaRecorder(combined, { mimeType: mime, videoBitsPerSecond: 5000000 }) : new MediaRecorder(combined);
        } catch (e) { showError('Could not start recording. Try a different browser.'); return; }
        var chunks = [];
        recorder.ondataavailable = function (ev) { if (ev.data && ev.data.size) chunks.push(ev.data); };
        recorder.onerror = function () { showError('A problem happened during recording.'); };

        var idx = 0, rafId = 0, firstSize = null;
        recorder.onstop = function () {
            var blob = new Blob(chunks, { type: 'video/webm' });
            var url = URL.createObjectURL(blob);
            preview.src = url;
            dlLink.href = url;
            clipInfo.textContent = files.length + ' clips have been joined into one video.';
            progressWrap.classList.add('d-none');
            statusText.classList.add('d-none');
            results.classList.remove('d-none');
            goBtn.disabled = false;
            if (audioCtx) { try { audioCtx.close(); } catch (e) {} }
        };

        function playNext() {
            if (idx >= files.length) { recorder.stop(); return; }
            setProgress((idx / files.length) * 90, 'Clip ' + (idx + 1) + ' of ' + files.length + ' is playing... (do not close the tab)');
            var url = URL.createObjectURL(files[idx]);
            var v = document.createElement('video');
            v.muted = false;
            v.preload = 'auto';
            v.src = url;
            var srcNode = null;
            if (audioCtx && audioDest) {
                try { srcNode = audioCtx.createMediaElementSource(v); srcNode.connect(audioDest); } catch (e) { srcNode = null; }
            }
            v.onloadedmetadata = function () {
                if (!firstSize) {
                    var mode = resSel.value;
                    if (mode === 'source') {
                        firstSize = { w: v.videoWidth || 1280, h: v.videoHeight || 720 };
                    } else if (mode === '1080') {
                        firstSize = { w: 1920, h: 1080 };
                    } else {
                        firstSize = { w: 1280, h: 720 };
                    }
                    canvas.width = firstSize.w;
                    canvas.height = firstSize.h;
                    // add audio tracks once canvas stream exists with size set
                    audioDest.stream.getAudioTracks().forEach(function (t) { combined.addTrack(t); });
                    try { recorder.start(500); } catch (e) { showError('Could not start recording.'); return; }
                }
                ctx.fillStyle = '#000';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                v.play().then(function () {
                    (function tick() {
                        if (!v.paused && !v.ended) {
                            ctx.drawImage(v, 0, 0, canvas.width, canvas.height);
                            rafId = requestAnimationFrame(tick);
                        }
                    })();
                }).catch(function () { showError('Could not play the video. The file may be corrupt.'); });
            };
            v.onended = function () {
                cancelAnimationFrame(rafId);
                URL.revokeObjectURL(url);
                if (srcNode) { try { srcNode.disconnect(); } catch (e) {} }
                idx++;
                setTimeout(playNext, 250);
            };
            v.onerror = function () { showError('Could not read clip ' + (idx + 1) + '. Please select only video files.'); };
        }
        setProgress(0, 'Getting ready...');
        playNext();
    }
})();
</script>
@endsection
