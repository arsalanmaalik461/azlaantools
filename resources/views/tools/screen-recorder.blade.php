@extends('layouts.app')

@section('title', 'Screen Recorder Online Free - Record Screen with Audio | Azlaan Tools')
@section('meta_description', 'Free online screen recorder: record your screen, tab or window with microphone audio, preview and download as WebM or MP4. No signup, no upload, works in Chrome and Edge.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Screen Recorder</h1>
            <p class="lead text-muted">Record your screen, a browser tab or a window — with microphone audio if you want — then preview and download instantly. Free, no signup.</p>

            <div class="alert alert-info"><strong>Works best on a computer</strong> in Chrome or Edge. Screen recording needs a desktop browser and a secure (HTTPS) page — most phones cannot share their screen to a website.</div>
            <div id="srAlert" class="alert alert-danger d-none" role="alert"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="form-check form-switch fs-5 mb-2">
                        <input class="form-check-input" type="checkbox" id="srMic" checked>
                        <label class="form-check-label" for="srMic">Include microphone (my voice)</label>
                    </div>
                    <div class="form-check form-switch fs-5 mb-3">
                        <input class="form-check-input" type="checkbox" id="srSysAudio">
                        <label class="form-check-label" for="srSysAudio">Include system / tab audio (tick "Share audio" in the picker too)</label>
                    </div>

                    <div class="text-center my-3">
                        <div class="display-4 fw-bold font-monospace" id="srTimer">00:00</div>
                        <p class="text-muted mb-0" id="srStatus">Ready. Press the big red button to start.</p>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button type="button" id="srStart" class="btn btn-danger btn-lg px-5">● Start Recording</button>
                        <button type="button" id="srStop" class="btn btn-dark btn-lg px-4" disabled>■ Stop</button>
                    </div>

                    <video id="srPreview" class="w-100 mt-3 rounded bg-dark" style="min-height:200px;" autoplay muted playsinline></video>

                    <div id="srResult" class="d-none mt-3 text-center">
                        <p class="fw-semibold mb-2">Your recording is ready:</p>
                        <a id="srDownload" href="#" download="screen-recording.webm" class="btn btn-success btn-lg">⬇ Download Recording</a>
                        <p class="small text-muted mt-2 mb-0" id="srMeta"></p>
                    </div>
                </div>
            </div>

            <div class="alert alert-secondary"><strong>Privacy note:</strong> Recordings stay on your device — nothing is uploaded to any server. The file is created entirely in your browser.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Choose whether to include your microphone and system/tab audio.</li>
                    <li>Press <strong>Start Recording</strong> and pick a screen, window or tab in the browser picker. If you want tab sound, also tick "Share tab audio" in that picker.</li>
                    <li>Press <strong>Stop</strong> (or "Stop sharing" in the browser bar) when finished.</li>
                    <li>Watch the preview, then press <strong>Download</strong>. Format is WebM in Chrome/Edge and MP4 in Safari where supported — this is labelled honestly under the download button.</li>
                </ol>
                <p class="mb-0 small text-muted">Limitation: on mobile browsers screen sharing is usually unavailable — use a laptop or desktop computer.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var startBtn = document.getElementById('srStart');
    var stopBtn = document.getElementById('srStop');
    var statusEl = document.getElementById('srStatus');
    var timerEl = document.getElementById('srTimer');
    var alertBox = document.getElementById('srAlert');
    var preview = document.getElementById('srPreview');
    var resultBox = document.getElementById('srResult');
    var downloadLink = document.getElementById('srDownload');
    var metaEl = document.getElementById('srMeta');
    var recorder = null;
    var chunks = [];
    var allTracks = [];
    var audioCtx = null;
    var timerIv = null;
    var startedAt = 0;
    var lastUrl = null;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }
    function fmt(sec) {
        var m = Math.floor(sec / 60);
        var s = Math.floor(sec % 60);
        return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    }
    function pickMime() {
        var candidates = ['video/mp4', 'video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm'];
        if (typeof MediaRecorder === 'undefined') return '';
        for (var i = 0; i < candidates.length; i++) {
            if (MediaRecorder.isTypeSupported(candidates[i])) return candidates[i];
        }
        return '';
    }
    function cleanup() {
        allTracks.forEach(function (t) {
            try { t.stop(); } catch (e) { }
        } );
        allTracks = [];
        if (audioCtx) {
            try { audioCtx.close(); } catch (e) { }
            audioCtx = null;
        }
        clearInterval(timerIv);
        startBtn.disabled = false;
        stopBtn.disabled = true;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
        showError('Screen recording is not available in this browser or page (it needs HTTPS and a desktop browser like Chrome or Edge). Please open this page on a computer.');
        startBtn.disabled = true;
    }

    startBtn.addEventListener('click', function () {
        alertBox.classList.add('d-none');
        resultBox.classList.add('d-none');
        var wantMic = document.getElementById('srMic').checked;
        var wantSys = document.getElementById('srSysAudio').checked;
        navigator.mediaDevices.getDisplayMedia({ video: true, audio: wantSys }).then(function (displayStream) {
            allTracks = allTracks.concat(displayStream.getTracks());
            var micPromise = wantMic ? navigator.mediaDevices.getUserMedia({ audio: true }).catch(function () { return null; }) : Promise.resolve(null);
            micPromise.then(function (micStream) {
                var finalStream;
                var displayAudio = displayStream.getAudioTracks();
                if (micStream) {
                    allTracks = allTracks.concat(micStream.getTracks());
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    var dest = audioCtx.createMediaStreamDestination();
                    var micSrc = audioCtx.createMediaStreamSource(micStream);
                    micSrc.connect(dest);
                    if (displayAudio.length) {
                        var sysSrc = audioCtx.createMediaStreamSource(new MediaStream(displayAudio));
                        sysSrc.connect(dest);
                    }
                    var tracks = displayStream.getVideoTracks().concat(dest.stream.getAudioTracks());
                    finalStream = new MediaStream(tracks);
                } else {
                    finalStream = displayStream;
                }
                preview.srcObject = finalStream;
                preview.muted = true;
                var mime = pickMime();
                chunks = [];
                try {
                    recorder = mime ? new MediaRecorder(finalStream, { mimeType: mime }) : new MediaRecorder(finalStream);
                } catch (e) {
                    recorder = new MediaRecorder(finalStream);
                }
                recorder.ondataavailable = function (ev) {
                    if (ev.data && ev.data.size > 0) chunks.push(ev.data);
                };
                recorder.onstop = function () {
                    var type = recorder.mimeType || mime || 'video/webm';
                    var ext = type.indexOf('mp4') !== -1 ? 'mp4' : 'webm';
                    var blob = new Blob(chunks, { type: type });
                    if (lastUrl) URL.revokeObjectURL(lastUrl);
                    lastUrl = URL.createObjectURL(blob);
                    preview.srcObject = null;
                    preview.muted = false;
                    preview.controls = true;
                    preview.src = lastUrl;
                    downloadLink.href = lastUrl;
                    downloadLink.setAttribute('download', 'screen-recording.' + ext);
                    metaEl.textContent = 'Format: ' + ext.toUpperCase() + ' · Size: ' + (blob.size / 1048576).toFixed(2) + ' MB · Duration: ' + fmt((Date.now() - startedAt) / 1000);
                    resultBox.classList.remove('d-none');
                    statusEl.textContent = 'Done! Preview your recording, then download it.';
                    cleanup();
                };
                displayStream.getVideoTracks()[0].addEventListener('ended', function () {
                    if (recorder && recorder.state !== 'inactive') recorder.stop();
                } );
                recorder.start(1000);
                startedAt = Date.now();
                // If the mic was requested but denied/unavailable, say so —
                // otherwise the user only finds out after recording in silence.
                statusEl.textContent = (wantMic && !micStream)
                    ? 'Recording… (microphone was not allowed, so this recording has no mic audio.) Press Stop when finished.'
                    : 'Recording… speak now. Press Stop when finished.';
                startBtn.disabled = true;
                stopBtn.disabled = false;
                timerIv = setInterval(function () {
                    timerEl.textContent = fmt((Date.now() - startedAt) / 1000);
                }, 500);
            } );
        } ).catch(function (err) {
            showError('Could not start screen recording: ' + (err && err.message ? err.message : err) + '. Make sure you allow screen sharing in the picker.');
            statusEl.textContent = 'Ready. Press the big red button to start.';
        } );
    } );

    stopBtn.addEventListener('click', function () {
        if (recorder && recorder.state !== 'inactive') recorder.stop();
    } );
} )();
</script>
@endsection
