@extends('layouts.app')

@section('title', 'Video Looper - Azlaan Tools')
@section('meta_description', 'Loop any video N times or turn it into a boomerang (forward and reverse), free online in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Video Looper</h1>
            <p class="lead text-muted">Play a video in a loop or make a boomerang (forward and back). Preview it and download the new video — everything runs in your browser, free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="vidFile" class="form-label fw-semibold">Select a video file</label>
                        <input type="file" class="form-control" id="vidFile" accept="video/*">
                    </div>
                    <div class="mb-3">
                        <label for="modeSel" class="form-label fw-semibold">Mode</label>
                        <select class="form-select" id="modeSel">
                            <option value="loop">Loop — repeat the video N times</option>
                            <option value="boomerang">Boomerang — forward then back (reverse)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="loopCountWrap">
                        <label for="loopCount" class="form-label fw-semibold">How many times to loop? (2 - 10)</label>
                        <input type="number" class="form-control" id="loopCount" min="2" max="10" value="3">
                    </div>

                    <div class="d-none mb-3" id="previewWrap">
                        <video id="preview" class="w-100 rounded border" controls playsinline muted></video>
                        <div class="form-text" id="infoLine"></div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary flex-fill" id="previewBtn" disabled>Preview Effect</button>
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn" disabled>Create &amp; Download</button>
                    </div>
                    <div class="progress mt-3 d-none" id="progWrap">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progBar" role="progressbar" style="width:0%">0%</div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="doneBox"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a video file (MP4, WebM and similar).</li>
                <li>In <strong>Loop</strong> mode, choose how many times to repeat, or pick <strong>Boomerang</strong> mode.</li>
                <li>Preview with <strong>Preview Effect</strong>, then save the new video with <strong>Create &amp; Download</strong>.</li>
            </ol>
            <p class="text-muted small">Note: Boomerang export has no sound (reverse audio is not possible). For boomerang, the first 12 seconds of the video are used. Output comes in WebM format.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var vidFile = document.getElementById('vidFile');
    var modeSel = document.getElementById('modeSel');
    var loopCount = document.getElementById('loopCount');
    var loopCountWrap = document.getElementById('loopCountWrap');
    var previewWrap = document.getElementById('previewWrap');
    var preview = document.getElementById('preview');
    var infoLine = document.getElementById('infoLine');
    var previewBtn = document.getElementById('previewBtn');
    var goBtn = document.getElementById('goBtn');
    var progWrap = document.getElementById('progWrap');
    var progBar = document.getElementById('progBar');
    var errorBox = document.getElementById('errorBox');
    var doneBox = document.getElementById('doneBox');
    var results = document.getElementById('results');
    var objUrl = null, exporting = false, boomTimer = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        doneBox.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function hideDone() { doneBox.classList.add('d-none'); doneBox.textContent = ''; }
    function setProg(pct) {
        progWrap.classList.remove('d-none');
        pct = Math.max(0, Math.min(100, Math.round(pct)));
        progBar.style.width = pct + '%';
        progBar.textContent = pct + '%';
    }
    function fmt(s) {
        s = Math.max(0, Math.floor(s));
        return Math.floor(s / 60) + ':' + ('0' + (s % 60)).slice(-2);
    }

    modeSel.addEventListener('change', function () {
        loopCountWrap.style.display = modeSel.value === 'loop' ? '' : 'none';
        stopBoomPreview();
    });

    vidFile.addEventListener('change', function () {
        hideError(); hideDone();
        stopBoomPreview();
        if (objUrl) { URL.revokeObjectURL(objUrl); objUrl = null; }
        var f = vidFile.files && vidFile.files[0];
        if (!f) { previewBtn.disabled = true; goBtn.disabled = true; previewWrap.classList.add('d-none'); return; }
        objUrl = URL.createObjectURL(f);
        preview.src = objUrl;
        previewWrap.classList.remove('d-none');
        previewBtn.disabled = false;
        goBtn.disabled = false;
        preview.onloadedmetadata = function () {
            infoLine.textContent = 'Duration: ' + fmt(preview.duration) + ' • ' + preview.videoWidth + 'x' + preview.videoHeight;
        };
    });

    function stopBoomPreview() {
        if (boomTimer) { clearInterval(boomTimer); boomTimer = null; }
    }
    preview.addEventListener('pause', stopBoomPreview);

    previewBtn.addEventListener('click', function () {
        hideError(); hideDone();
        if (!preview.src) { showError('Please select a video first.'); return; }
        stopBoomPreview();
        preview.muted = true;
        if (modeSel.value === 'loop') {
            var n = Math.max(2, Math.min(10, parseInt(loopCount.value, 10) || 3));
            var left = n;
            preview.onended = function () {
                left--;
                if (left > 0) { preview.currentTime = 0; preview.play(); }
                else { preview.onended = null; }
            };
            preview.currentTime = 0;
            preview.play();
        } else {
            var fps = 15, dir = 1, step = dir / fps;
            preview.currentTime = 0;
            preview.play().catch(function () {});
            preview.pause();
            boomTimer = setInterval(function () {
                var t = preview.currentTime + step;
                if (t >= preview.duration) { t = preview.duration; step = -1 / fps; }
                else if (t <= 0) { t = 0; step = 1 / fps; }
                preview.currentTime = t;
            }, 1000 / fps);
        }
    });

    function downloadBlob(blob, name) {
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 2000);
    }

    goBtn.addEventListener('click', function () {
        hideError(); hideDone();
        if (!preview.src || exporting) { return; }
        if (modeSel.value === 'loop') { exportLoop(); } else { exportBoomerang(); }
    });

    function exportLoop() {
        var n = Math.max(2, Math.min(10, parseInt(loopCount.value, 10) || 3));
        if (!preview.captureStream && !preview.mozCaptureStream) {
            showError('Your browser does not support video capture. Use Chrome or Edge.');
            return;
        }
        exporting = true;
        goBtn.disabled = true;
        previewBtn.disabled = true;
        setProg(0);
        var stream = preview.captureStream ? preview.captureStream() : preview.mozCaptureStream();
        var mime = 'video/webm';
        if (window.MediaRecorder && MediaRecorder.isTypeSupported('video/webm;codecs=vp9')) {
            mime = 'video/webm;codecs=vp9';
        }
        var rec = new MediaRecorder(stream, { mimeType: mime, videoBitsPerSecond: 5000000 });
        var chunks = [];
        rec.ondataavailable = function (e) { if (e.data && e.data.size) { chunks.push(e.data); } };
        rec.onstop = function () {
            exporting = false;
            goBtn.disabled = false;
            previewBtn.disabled = false;
            progWrap.classList.add('d-none');
            var blob = new Blob(chunks, { type: 'video/webm' });
            downloadBlob(blob, 'looped-' + n + 'x.webm');
            doneBox.textContent = 'Done! The ' + n + 'x looped video is downloading (' + (blob.size / 1048576).toFixed(1) + ' MB).';
            doneBox.classList.remove('d-none');
        };
        var left = n;
        preview.onended = function () {
            left--;
            setProg(((n - left) / n) * 100);
            if (left > 0) { preview.currentTime = 0; preview.play(); }
            else { preview.onended = null; rec.stop(); }
        };
        preview.muted = false;
        preview.currentTime = 0;
        rec.start(250);
        preview.play();
    }

    function seekTo(t) {
        return new Promise(function (resolve) {
            var done = function () {
                preview.removeEventListener('seeked', done);
                resolve();
            };
            preview.addEventListener('seeked', done);
            preview.currentTime = t;
            setTimeout(done, 1500);
        });
    }
    function wait(ms) { return new Promise(function (r) { setTimeout(r, ms); }); }

    function exportBoomerang() {
        var fps = 12;
        var D = Math.min(preview.duration || 0, 12);
        if (!D || D < 0.5) { showError('Could not read the video duration.'); return; }
        if (!window.MediaRecorder) { showError('Your browser does not support MediaRecorder. Use Chrome or Edge.'); return; }
        exporting = true;
        goBtn.disabled = true;
        previewBtn.disabled = true;
        stopBoomPreview();
        setProg(0);

        var vw = preview.videoWidth, vh = preview.videoHeight;
        var scale = Math.min(1, 1280 / vw);
        var cw = Math.round(vw * scale), ch = Math.round(vh * scale);
        var canvas = document.createElement('canvas');
        canvas.width = cw; canvas.height = ch;
        var ctx = canvas.getContext('2d');
        var stream = canvas.captureStream(0);
        var track = stream.getVideoTracks()[0];
        var rec = new MediaRecorder(stream, { mimeType: 'video/webm', videoBitsPerSecond: 5000000 });
        var chunks = [];
        rec.ondataavailable = function (e) { if (e.data && e.data.size) { chunks.push(e.data); } };
        rec.onstop = function () {
            exporting = false;
            goBtn.disabled = false;
            previewBtn.disabled = false;
            progWrap.classList.add('d-none');
            var blob = new Blob(chunks, { type: 'video/webm' });
            downloadBlob(blob, 'boomerang.webm');
            doneBox.textContent = 'Done! The boomerang video is downloading (' + (blob.size / 1048576).toFixed(1) + ' MB). Note: boomerang has no sound.';
            doneBox.classList.remove('d-none');
        };

        var count = Math.max(2, Math.floor(D * fps));
        var times = [];
        for (var i = 0; i < count; i++) { times.push(i / fps); }
        for (var j = count - 2; j >= 0; j--) { times.push(j / fps); }

        preview.muted = true;
        preview.pause();
        rec.start(250);
        var startAt = Date.now();
        var chain = Promise.resolve();
        times.forEach(function (t, idx) {
            chain = chain.then(function () {
                return seekTo(Math.min(t, D - 0.05)).then(function () {
                    ctx.drawImage(preview, 0, 0, cw, ch);
                    var target = startAt + (idx * 1000) / fps;
                    var delay = target - Date.now();
                    return wait(Math.max(0, delay)).then(function () {
                        track.requestFrame();
                        setProg((idx / (times.length - 1)) * 100);
                    });
                });
            });
        });
        chain.then(function () {
            return wait(400);
        }).then(function () {
            rec.stop();
        }).catch(function (e) {
            exporting = false;
            goBtn.disabled = false;
            previewBtn.disabled = false;
            showError('Export failed: ' + e);
        });
    }
})();
</script>
@endsection
