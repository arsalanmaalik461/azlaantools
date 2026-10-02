@extends('layouts.app')

@section('title', 'Mute Video - Azlaan Tools')
@section('meta_description', 'Remove audio from any video in your browser and download a fully silent copy, free and private.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Mute Video</h1>
            <p class="lead text-muted">Remove all sound from your video. The video is processed in your browser — nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="mvFile" class="form-label fw-semibold">Select a video</label>
                        <input type="file" class="form-control" id="mvFile" accept="video/*">
                    </div>
                    <div class="text-center mt-3">
                        <video id="mvPreview" class="w-100 rounded d-none" controls playsinline style="max-height:300px"></video>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="mvInfo">No video loaded yet.</p>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn" disabled>Mute Video</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="progress mt-3 d-none" id="mvProgressWrap">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="mvProgress" role="progressbar" style="width:0%">0%</div>
                    </div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Muted video is ready</h2>
                        <div class="text-center">
                            <video id="mvOut" class="w-100 rounded" controls playsinline style="max-height:320px"></video>
                        </div>
                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-success btn-lg" id="mvDownload">Download Muted Video</a>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your video (mp4, webm, or any browser-supported format).</li>
                <li>Press the <strong>Mute Video</strong> button — the video will be re-recorded in real time without sound.</li>
                <li>Check the preview and save the silent copy with <strong>Download Muted Video</strong>.</li>
            </ol>
            <p class="small text-muted">Muting re-records the video in real time, so a longer video takes more time. The output is MP4 (where supported) or WebM, and it has no audio track.</p>
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
    var fileInput = document.getElementById('mvFile');
    var preview = document.getElementById('mvPreview');
    var info = document.getElementById('mvInfo');
    var progWrap = document.getElementById('mvProgressWrap');
    var progBar = document.getElementById('mvProgress');
    var busy = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pickMime() {
        if (typeof MediaRecorder === 'undefined') return null;
        var opts = ['video/mp4', 'video/webm;codecs=vp9', 'video/webm'];
        for (var i = 0; i < opts.length; i++) {
            if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(opts[i])) return opts[i];
        }
        return '';
    }
    function setProgress(p) {
        progWrap.classList.remove('d-none');
        var pct = Math.min(100, Math.max(0, Math.round(p * 100)));
        progBar.style.width = pct + '%';
        progBar.textContent = pct + '%';
    }

    fileInput.addEventListener('change', function () {
        hideError();
        var f = fileInput.files && fileInput.files[0];
        if (!f) return;
        preview.src = URL.createObjectURL(f);
        preview.classList.remove('d-none');
        preview.addEventListener('loadedmetadata', function once() {
            preview.removeEventListener('loadedmetadata', once);
            var dur = isFinite(preview.duration) ? preview.duration : 0;
            info.textContent = 'Loaded: ' + f.name + ' · ' + dur.toFixed(1) + 's · ' + preview.videoWidth + 'x' + preview.videoHeight;
            goBtn.disabled = false;
        });
    });

    goBtn.addEventListener('click', function () {
        hideError();
        if (busy || !preview.videoWidth) return;
        var f = fileInput.files && fileInput.files[0];
        if (!f) { showError('Please select a video first.'); return; }
        var mime = pickMime();
        if (mime === null) { showError('MediaRecorder is not supported in this browser.'); return; }
        var dur = preview.duration;
        if (!isFinite(dur) || dur <= 0) { showError('Video duration could not be read. Try another file.'); return; }

        var cv = document.createElement('canvas');
        cv.width = preview.videoWidth;
        cv.height = preview.videoHeight;
        var ctx = cv.getContext('2d');
        // VIDEO ONLY stream — no audio tracks added, this is what silences the video.
        var stream = cv.captureStream(30);
        var chunks = [];
        var rec = new MediaRecorder(stream, mime ? { mimeType: mime, videoBitsPerSecond: 6000000 } : undefined);
        rec.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        rec.onstop = function () {
            var type = mime || 'video/webm';
            var blob = new Blob(chunks, { type: type });
            var url = URL.createObjectURL(blob);
            var out = document.getElementById('mvOut');
            out.src = url;
            var ext = (mime === 'video/mp4') ? 'mp4' : 'webm';
            var dl = document.getElementById('mvDownload');
            dl.href = url;
            dl.setAttribute('download', 'muted-video.' + ext);
            results.classList.remove('d-none');
            progWrap.classList.add('d-none');
            info.textContent = 'Done! The video is now fully silent — no audio track.';
            busy = false;
            goBtn.disabled = false;
        };
        rec.start(200);

        busy = true;
        goBtn.disabled = true;
        setProgress(0);
        var startT = performance.now();

        preview.currentTime = 0;
        preview.muted = true;
        function draw() {
            if (preview.currentTime < dur) ctx.drawImage(preview, 0, 0);
            setProgress(preview.currentTime / dur);
            if (busy && !preview.ended && preview.currentTime < dur - 0.05) {
                requestAnimationFrame(draw);
            } else {
                if (rec.state !== 'inactive') rec.stop();
                preview.pause();
            }
        }
        var playP = preview.play();
        if (playP && playP.then) {
            playP.then(function () { requestAnimationFrame(draw); })
                .catch(function () {
                    busy = false; goBtn.disabled = false;
                    if (rec.state !== 'inactive') rec.stop();
                    showError('Playback blocked. Press play on the preview first, then mute again.');
                });
        } else {
            requestAnimationFrame(draw);
        }
        // Safety: stop after duration + 5s
        setTimeout(function () {
            if (busy) {
                busy = false;
                if (rec.state !== 'inactive') rec.stop();
                preview.pause();
                goBtn.disabled = false;
            }
        }, (dur + 5) * 1000);
    });
})();
</script>
@endsection
