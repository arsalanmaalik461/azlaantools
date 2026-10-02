@extends('layouts.app')

@section('title', 'Video Compressor - Azlaan Tools')
@section('meta_description', 'Compress video size right in your browser with no watermark - best for WhatsApp sharing, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Video Compressor</h1>
            <p class="lead text-muted">Make your video file smaller with no watermark. Re-encoding happens right in your browser — the file is never uploaded. Best for WhatsApp sharing.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="videoFile" class="form-label fw-semibold">Choose a video file</label>
                        <input type="file" class="form-control" id="videoFile" accept="video/*">
                        <div class="form-text">MP4, MOV, WebM and similar. Compressing large files may take some time.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="resSelect" class="form-label fw-semibold">Output resolution</label>
                            <select class="form-select" id="resSelect">
                                <option value="original" selected>Original (only lower the bitrate)</option>
                                <option value="1280">1280 x 720 (HD)</option>
                                <option value="854">854 x 480 (SD)</option>
                                <option value="640">640 x 360 (small file)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bitrateSelect" class="form-label fw-semibold">Quality / Bitrate</label>
                            <select class="form-select" id="bitrateSelect">
                                <option value="6000000">High (6 Mbps)</option>
                                <option value="3000000" selected>Medium (3 Mbps)</option>
                                <option value="1500000">Low (1.5 Mbps)</option>
                                <option value="700000">Very low (0.7 Mbps — like WhatsApp)</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Compress Video</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="progressWrap" class="d-none mt-3">
                        <div class="progress" style="height: 22px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <p class="text-muted small mt-2 mb-0" id="progressText">Compressing... please wait while the video plays.</p>
                    </div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="summaryBox"></div>
                        <div class="ratio ratio-16x9 mb-3 bg-light rounded">
                            <video id="previewVideo" controls playsinline class="rounded"></video>
                        </div>
                        <button type="button" class="btn btn-success w-100" id="dlBtn">Download Compressed Video</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your video file.</li>
                <li>Choose resolution and quality — smaller resolution + lower bitrate = smaller file.</li>
                <li>Press <strong>Compress Video</strong> and wait for the process to finish.</li>
                <li>Preview and <strong>Download</strong> it. No watermark is added.</li>
            </ol>
            <p class="text-muted small">Note: depending on your browser support, the output may be WebM or MP4. Audio is compressed along with it.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var videoFile = document.getElementById('videoFile');
    var resSelect = document.getElementById('resSelect');
    var bitrateSelect = document.getElementById('bitrateSelect');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var progressText = document.getElementById('progressText');
    var summaryBox = document.getElementById('summaryBox');
    var previewVideo = document.getElementById('previewVideo');
    var dlBtn = document.getElementById('dlBtn');
    var outUrl = null;
    var outExt = 'webm';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
        progressWrap.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(pct, txt) {
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
        if (txt) { progressText.textContent = txt; }
    }
    function fmtMB(bytes) {
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    function pickMime() {
        var cands = ['video/mp4', 'video/webm;codecs=h264', 'video/webm;codecs=vp9', 'video/webm'];
        for (var i = 0; i < cands.length; i++) {
            if (window.MediaRecorder && MediaRecorder.isTypeSupported(cands[i])) {
                return cands[i];
            }
        }
        return '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var file = videoFile.files && videoFile.files[0];
        if (!file) { showError('Please choose a video file first.'); return; }
        if (!window.MediaRecorder) { showError('Your browser does not support video recording. Try Chrome or Edge.'); return; }
        var mime = pickMime();
        if (!mime) { showError('No supported video format found in this browser.'); return; }
        outExt = mime.indexOf('mp4') >= 0 ? 'mp4' : 'webm';

        goBtn.disabled = true;
        goBtn.textContent = 'Working...';
        progressWrap.classList.remove('d-none');
        setProgress(2, 'Loading video...');

        var srcUrl = URL.createObjectURL(file);
        var video = document.createElement('video');
        video.muted = false;
        video.volume = 0;
        video.playsInline = true;
        video.preload = 'auto';
        video.src = srcUrl;

        video.onloadedmetadata = function () {
            var vw = video.videoWidth, vh = video.videoHeight, dur = video.duration;
            if (!vw || !vh || !dur || !isFinite(dur)) {
                cleanup();
                showError('Could not read the video data. Try a different file.');
                return;
            }
            var target = resSelect.value;
            var ow = vw, oh = vh;
            if (target !== 'original') {
                var tw = parseInt(target, 10);
                if (tw < vw) {
                    var scale = tw / vw;
                    ow = tw;
                    oh = Math.round(vh * scale / 2) * 2;
                }
            }
            ow = Math.max(2, Math.round(ow / 2) * 2);
            oh = Math.max(2, Math.round(oh / 2) * 2);
            var bitrate = parseInt(bitrateSelect.value, 10);

            var canvas = document.createElement('canvas');
            canvas.width = ow; canvas.height = oh;
            var ctx = canvas.getContext('2d');
            var stream = canvas.captureStream(30);
            try {
                var vs = video.captureStream();
                vs.getAudioTracks().forEach(function (t) { stream.addTrack(t); });
            } catch (e) { /* audio optional */ }

            var rec;
            try {
                rec = new MediaRecorder(stream, { mimeType: mime, videoBitsPerSecond: bitrate });
            } catch (e) {
                cleanup();
                showError('Could not start the recorder: ' + e.message);
                return;
            }
            var chunks = [];
            rec.ondataavailable = function (ev) { if (ev.data && ev.data.size) { chunks.push(ev.data); } };
            rec.onstop = function () {
                var blob = new Blob(chunks, { type: mime });
                if (outUrl) { URL.revokeObjectURL(outUrl); }
                outUrl = URL.createObjectURL(blob);
                previewVideo.src = outUrl;
                var saved = file.size > 0 ? Math.round((1 - blob.size / file.size) * 100) : 0;
                summaryBox.textContent = 'Original: ' + fmtMB(file.size) + '  ->  Compressed: ' + fmtMB(blob.size) +
                    ' (' + vw + 'x' + vh + ' to ' + ow + 'x' + oh + '). ' +
                    (saved > 0 ? saved + '% size reduced!' : 'Size stayed about the same — try a lower bitrate.');
                results.classList.remove('d-none');
                progressWrap.classList.add('d-none');
                goBtn.disabled = false;
                goBtn.textContent = 'Compress Video';
                URL.revokeObjectURL(srcUrl);
            };
            rec.onerror = function () {
                cleanup();
                showError('An error happened during compression. Please try again.');
            };

            video.onended = function () { finish(); };
            video.onerror = function () {
                cleanup();
                showError('The video could not be played. The format may not be supported.');
            };

            function drawFrame() {
                if (video.ended || video.paused) { return; }
                ctx.drawImage(video, 0, 0, ow, oh);
                var pct = dur > 0 ? Math.min(99, Math.round(video.currentTime / dur * 100)) : 50;
                setProgress(pct);
                requestAnimationFrame(drawFrame);
            }
            function finish() {
                try { if (rec.state !== 'inactive') { rec.stop(); } } catch (e) {}
                try { stream.getTracks().forEach(function (t) { t.stop(); }); } catch (e) {}
            }
            function cleanup() {
                try { if (rec && rec.state !== 'inactive') { rec.stop(); } } catch (e) {}
                try { video.pause(); } catch (e) {}
                URL.revokeObjectURL(srcUrl);
                goBtn.disabled = false;
                goBtn.textContent = 'Compress Video';
                progressWrap.classList.add('d-none');
            }

            video.play().then(function () {
                rec.start(500);
                setProgress(3, 'Compressing — wait for the video to finish...');
                drawFrame();
            }).catch(function () {
                cleanup();
                showError('Could not play the video (the browser blocked it). Click again.');
            });
        };
        video.onerror = function () {
            goBtn.disabled = false;
            goBtn.textContent = 'Compress Video';
            progressWrap.classList.add('d-none');
            showError('Could not load the video file.');
        };
    });

    dlBtn.addEventListener('click', function () {
        if (!outUrl) { return; }
        var a = document.createElement('a');
        a.href = outUrl;
        a.download = 'compressed-video.' + outExt;
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
})();
</script>
@endsection
