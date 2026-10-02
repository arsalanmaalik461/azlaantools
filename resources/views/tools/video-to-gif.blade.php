@extends('layouts.app')

@section('title', 'Video to GIF Converter Online Free | Azlaan Tools')
@section('meta_description', 'Free video to GIF converter: turn a short MP4/WebM clip into an animated GIF, choose start, end, FPS and width. No signup, video never leaves your device.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Video to GIF Converter</h1>
            <p class="lead text-muted">Turn a short video clip into an animated GIF you can share anywhere — WhatsApp, Twitter, anywhere.</p>

            <div class="alert alert-warning"><strong>Keep it short:</strong> use a clip under about <strong>15 seconds</strong> and a modest width (320–480px). GIFs are made frame-by-frame in your browser, so long or huge videos will be slow and produce very large files.</div>
            <div id="vgAlert" class="alert alert-danger d-none" role="alert"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="vgFile">1. Choose a short video (MP4, WebM, MOV)</label>
                    <input type="file" id="vgFile" class="form-control form-control-lg" accept="video/*,.mp4,.webm,.mov,.m4v">

                    <div id="vgEditor" class="d-none mt-4">
                        <video id="vgVideo" class="w-100 rounded bg-dark" controls playsinline></video>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="vgStart">Start (seconds)</label>
                                <input type="number" id="vgStart" class="form-control" min="0" step="0.1" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="vgEnd">End (seconds)</label>
                                <input type="number" id="vgEnd" class="form-control" min="0" step="0.1" value="5">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="vgFps">Frames per second</label>
                                <select id="vgFps" class="form-select">
                                    <option value="8">8 FPS (small file)</option>
                                    <option value="10" selected>10 FPS (balanced)</option>
                                    <option value="15">15 FPS (smoother, bigger)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="vgWidth">GIF width (px)</label>
                                <select id="vgWidth" class="form-select">
                                    <option value="320">320 px</option>
                                    <option value="480" selected>480 px</option>
                                    <option value="640">640 px</option>
                                </select>
                            </div>
                        </div>
                        <p class="small text-muted mt-2" id="vgInfo">Video length: –</p>
                        <div class="d-grid d-md-block mt-2">
                            <button type="button" id="vgGo" class="btn btn-success btn-lg px-5">🎬 Create GIF</button>
                        </div>
                        <div class="progress mt-3 d-none" id="vgProgressWrap" style="height: 22px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="vgProgress" style="width: 0%;">0%</div>
                        </div>
                        <p class="small text-muted mt-2 mb-0" id="vgStatus">Ready.</p>
                    </div>

                    <div id="vgResult" class="d-none mt-4 text-center">
                        <p class="fw-semibold">Your GIF is ready:</p>
                        <img id="vgImg" alt="Generated GIF" class="img-fluid rounded border">
                        <div class="mt-3">
                            <a id="vgDownload" href="#" download="video.gif" class="btn btn-primary btn-lg">⬇ Download GIF</a>
                            <p class="small text-muted mt-2 mb-0" id="vgMeta"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-secondary"><strong>Privacy note:</strong> Your video stays on your device — nothing is uploaded. Frames are captured and the GIF is built entirely in your browser.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Upload a short video (under ~15 seconds works best).</li>
                    <li>Set the start and end time, FPS and GIF width — smaller settings mean a smaller file.</li>
                    <li>Press <strong>Create GIF</strong> and watch the progress bar.</li>
                    <li>Preview the GIF, then press <strong>Download GIF</strong>.</li>
                </ol>
                <p class="mb-0 small text-muted">Limitations: GIF has no sound and only 256 colours, so quality is lower than the original video. Very long clips may run out of memory — trim first if needed.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/gifshot@0.4.5/dist/gifshot.min.js"></script>
<script>
(function () {
    var alertBox = document.getElementById('vgAlert');
    var fileInput = document.getElementById('vgFile');
    var editor = document.getElementById('vgEditor');
    var video = document.getElementById('vgVideo');
    var statusEl = document.getElementById('vgStatus');
    var progressWrap = document.getElementById('vgProgressWrap');
    var progressBar = document.getElementById('vgProgress');
    var resultBox = document.getElementById('vgResult');
    var imgEl = document.getElementById('vgImg');
    var fileUrl = null;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }
    function setProgress(pct, label) {
        progressWrap.classList.remove('d-none');
        progressBar.style.width = pct + '%';
        progressBar.textContent = label || (Math.round(pct) + '%');
    }

    fileInput.addEventListener('change', function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        alertBox.classList.add('d-none');
        resultBox.classList.add('d-none');
        if (fileUrl) URL.revokeObjectURL(fileUrl);
        fileUrl = URL.createObjectURL(file);
        video.src = fileUrl;
        editor.classList.remove('d-none');
        statusEl.textContent = 'Video loaded: ' + file.name;
    } );

    video.addEventListener('loadedmetadata', function () {
        var dur = video.duration || 0;
        document.getElementById('vgInfo').textContent = 'Video length: ' + dur.toFixed(1) + 's · Size: ' + video.videoWidth + '×' + video.videoHeight;
        document.getElementById('vgEnd').value = Math.min(dur, 5).toFixed(1);
        document.getElementById('vgEnd').max = dur;
        document.getElementById('vgStart').max = dur;
        if (dur > 20) {
            statusEl.textContent = 'This video is ' + dur.toFixed(0) + 's long — please pick a short section (under 15s) using Start and End.';
        }
    } );

    document.getElementById('vgGo').addEventListener('click', function () {
        alertBox.classList.add('d-none');
        if (typeof gifshot === 'undefined') {
            showError('The GIF library could not load — check your internet connection and reload the page.');
            return;
        }
        var start = Math.max(0, Number(document.getElementById('vgStart').value) || 0);
        var end = Number(document.getElementById('vgEnd').value) || 0;
        var fps = Number(document.getElementById('vgFps').value) || 10;
        var width = Number(document.getElementById('vgWidth').value) || 480;
        if (end <= start) { showError('End time must be greater than start time.'); return; }
        if (end - start > 15) { showError('Please keep the selected part under 15 seconds — GIFs get very large.'); return; }
        if (!video.videoWidth) { showError('Video is not loaded yet — please wait a moment and try again.'); return; }
        var height = Math.round(width * video.videoHeight / video.videoWidth);
        var frameCount = Math.max(2, Math.round((end - start) * fps));
        var canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        var cctx = canvas.getContext('2d');
        var frames = [];
        var btn = document.getElementById('vgGo');
        btn.disabled = true;
        video.pause();
        video.muted = true;
        setProgress(2, 'Capturing frames…');
        statusEl.textContent = 'Capturing ' + frameCount + ' frames…';

        var idx = 0;
        function captureNext() {
            if (idx >= frameCount) {
                encode();
                return;
            }
            // Keep the target slightly before the very end — seeking to exactly
            // duration can leave the video in an ended state with no frame.
            var maxT = Math.max(start, (video.duration || end) - 0.05);
            var t = Math.min(start + idx / fps, maxT);
            var done = false;
            var timer = null;
            var onSeeked = function () {
                if (done) return;
                done = true;
                if (timer) clearTimeout(timer);
                video.removeEventListener('seeked', onSeeked);
                cctx.drawImage(video, 0, 0, width, height);
                frames.push(canvas.toDataURL('image/jpeg', 0.85));
                idx++;
                setProgress(Math.round(idx / frameCount * 60), 'Capturing frames ' + idx + '/' + frameCount);
                captureNext();
            };
            video.addEventListener('seeked', onSeeked);
            video.currentTime = t;
            // 'seeked' does not always fire (e.g. seeking to the time the video is
            // already at) — without this fallback the capture hangs forever with
            // the button disabled. Proceed with the current frame instead.
            timer = setTimeout(onSeeked, 1200);
        }
        function encode() {
            video.muted = false;
            setProgress(65, 'Building GIF…');
            statusEl.textContent = 'Building your GIF — this can take a few seconds…';
            gifshot.createGIF({
                images: frames,
                interval: 1 / fps,
                gifWidth: width,
                gifHeight: height,
                sampleInterval: 10,
                numWorkers: 2,
                progressCallback: function (p) {
                    setProgress(65 + Math.round(p * 35), 'Building GIF ' + Math.round(p * 100) + '%');
                }
            }, function (obj) {
                btn.disabled = false;
                if (!obj || obj.error) {
                    showError('GIF creation failed. Try a shorter clip, lower FPS or smaller width.');
                    statusEl.textContent = 'Failed.';
                    return;
                }
                setProgress(100, 'Done!');
                imgEl.src = obj.image;
                var dl = document.getElementById('vgDownload');
                dl.href = obj.image;
                var approxKb = Math.round(obj.image.length * 0.75 / 1024);
                document.getElementById('vgMeta').textContent = 'Size: about ' + approxKb + ' KB · ' + width + '×' + height + ' · ' + frameCount + ' frames at ' + fps + ' FPS · GIF has no sound.';
                resultBox.classList.remove('d-none');
                statusEl.textContent = 'Done! Preview above, then download.';
            } );
        }
        captureNext();
    } );
} )();
</script>
@endsection
