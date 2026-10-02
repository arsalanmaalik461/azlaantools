@extends('layouts.app')

@section('title', 'Voice Recorder Online Free - Record Audio, Download WAV | Azlaan Tools')
@section('meta_description', 'Free online voice recorder: record from your microphone with live waveform, pause and resume, play back and download as WAV or your browser format. No signup, nothing uploaded.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Voice Recorder</h1>
            <p class="lead text-muted">Record your voice from the microphone, watch the live waveform, play it back and download it — free, with no signup.</p>

            <div id="vrAlert" class="alert alert-danger d-none" role="alert"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <canvas id="vrCanvas" width="700" height="120" class="w-100 rounded bg-dark"></canvas>
                    <div class="text-center my-3">
                        <div class="display-4 fw-bold font-monospace" id="vrTimer">00:00</div>
                        <p class="text-muted mb-0" id="vrStatus">Ready. Allow microphone access when asked.</p>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button type="button" id="vrStart" class="btn btn-danger btn-lg px-4">● Record</button>
                        <button type="button" id="vrPause" class="btn btn-warning btn-lg px-4" disabled>Pause</button>
                        <button type="button" id="vrStop" class="btn btn-dark btn-lg px-4" disabled>■ Stop</button>
                    </div>
                    <div id="vrResult" class="d-none mt-4">
                        <label class="form-label fw-semibold">Your recording</label>
                        <audio id="vrAudio" class="w-100" controls></audio>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a id="vrDownloadNative" href="#" class="btn btn-success" download>⬇ Download (browser format)</a>
                            <button type="button" id="vrDownloadWav" class="btn btn-primary">⬇ Download WAV</button>
                        </div>
                        <p class="small text-muted mt-2 mb-0" id="vrMeta"></p>
                    </div>
                </div>
            </div>

            <div class="alert alert-secondary"><strong>Privacy note:</strong> Recordings stay on your device — nothing is uploaded. Microphone audio is processed only in your browser, which needs a secure (HTTPS) page.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Press <strong>Record</strong> and allow microphone access.</li>
                    <li>Speak — the waveform moves as you talk. Use <strong>Pause / Resume</strong> anytime.</li>
                    <li>Press <strong>Stop</strong>, listen to the playback, then download.</li>
                </ol>
                <p class="mb-0 small text-muted">Formats, labelled honestly: the first download is whatever your browser records natively (WebM in Chrome/Edge, MP4/M4A in Safari). The WAV button converts the same recording to universal WAV on your device.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var startBtn = document.getElementById('vrStart');
    var pauseBtn = document.getElementById('vrPause');
    var stopBtn = document.getElementById('vrStop');
    var statusEl = document.getElementById('vrStatus');
    var timerEl = document.getElementById('vrTimer');
    var alertBox = document.getElementById('vrAlert');
    var canvas = document.getElementById('vrCanvas');
    var ctx2d = canvas.getContext('2d');
    var resultBox = document.getElementById('vrResult');
    var audioEl = document.getElementById('vrAudio');
    var nativeLink = document.getElementById('vrDownloadNative');
    var wavBtn = document.getElementById('vrDownloadWav');
    var metaEl = document.getElementById('vrMeta');
    var stream = null;
    var recorder = null;
    var chunks = [];
    var audioCtx = null;
    var analyser = null;
    var rafId = null;
    var timerIv = null;
    var elapsedBefore = 0;
    var segmentStart = 0;
    var paused = false;
    var lastBlob = null;
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
    function totalSeconds() {
        var live = recorder && recorder.state === 'recording' ? (Date.now() - segmentStart) / 1000 : 0;
        return elapsedBefore + live;
    }
    function drawIdle() {
        ctx2d.fillStyle = '#212529';
        ctx2d.fillRect(0, 0, canvas.width, canvas.height);
        ctx2d.strokeStyle = '#0dcaf0';
        ctx2d.lineWidth = 2;
        ctx2d.beginPath();
        ctx2d.moveTo(0, canvas.height / 2);
        ctx2d.lineTo(canvas.width, canvas.height / 2);
        ctx2d.stroke();
    }
    function drawWave() {
        if (!analyser) return;
        var data = new Uint8Array(analyser.fftSize);
        analyser.getByteTimeDomainData(data);
        ctx2d.fillStyle = '#212529';
        ctx2d.fillRect(0, 0, canvas.width, canvas.height);
        ctx2d.strokeStyle = '#0dcaf0';
        ctx2d.lineWidth = 2;
        ctx2d.beginPath();
        var step = Math.ceil(data.length / canvas.width);
        for (var x = 0; x < canvas.width; x++) {
            // Clamp: x * step can run past the end of the array for the last
            // pixels, which produced NaN points and a broken waveform tail.
            var v = data[Math.min(data.length - 1, x * step)] / 128 - 1;
            var y = canvas.height / 2 + v * canvas.height / 2;
            if (x === 0) ctx2d.moveTo(x, y); else ctx2d.lineTo(x, y);
        }
        ctx2d.stroke();
        rafId = requestAnimationFrame(drawWave);
    }
    function stopVisuals() {
        if (rafId) cancelAnimationFrame(rafId);
        clearInterval(timerIv);
        drawIdle();
    }
    function bufferToWavBlob(buffer) {
        var numCh = Math.min(2, buffer.numberOfChannels);
        var sr = buffer.sampleRate;
        var len = buffer.length;
        var bytesPerSample = 2;
        var blockAlign = numCh * bytesPerSample;
        var dataSize = len * blockAlign;
        var ab = new ArrayBuffer(44 + dataSize);
        var view = new DataView(ab);
        function writeStr(off, str) {
            for (var i = 0; i < str.length; i++) view.setUint8(off + i, str.charCodeAt(i));
        }
        writeStr(0, 'RIFF');
        view.setUint32(4, 36 + dataSize, true);
        writeStr(8, 'WAVE');
        writeStr(12, 'fmt ');
        view.setUint32(16, 16, true);
        view.setUint16(20, 1, true);
        view.setUint16(22, numCh, true);
        view.setUint32(24, sr, true);
        view.setUint32(28, sr * blockAlign, true);
        view.setUint16(32, blockAlign, true);
        view.setUint16(34, 16, true);
        writeStr(36, 'data');
        view.setUint32(40, dataSize, true);
        var channels = [];
        for (var c = 0; c < numCh; c++) channels.push(buffer.getChannelData(c));
        var offset = 44;
        for (var i = 0; i < len; i++) {
            for (var ch = 0; ch < numCh; ch++) {
                var sample = Math.max(-1, Math.min(1, channels[ch][i]));
                view.setInt16(offset, sample < 0 ? sample * 32768 : sample * 32767, true);
                offset += 2;
            }
        }
        return new Blob([ab], { type: 'audio/wav' });
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || typeof MediaRecorder === 'undefined') {
        showError('Voice recording is not available in this browser (it needs HTTPS and microphone support, e.g. Chrome, Edge or Safari).');
        startBtn.disabled = true;
    }
    drawIdle();

    startBtn.addEventListener('click', function () {
        alertBox.classList.add('d-none');
        resultBox.classList.add('d-none');
        navigator.mediaDevices.getUserMedia({ audio: true }).then(function (s) {
            stream = s;
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var src = audioCtx.createMediaStreamSource(stream);
            analyser = audioCtx.createAnalyser();
            analyser.fftSize = 2048;
            src.connect(analyser);
            chunks = [];
            elapsedBefore = 0;
            paused = false;
            recorder = new MediaRecorder(stream);
            recorder.ondataavailable = function (ev) {
                if (ev.data && ev.data.size > 0) chunks.push(ev.data);
            };
            recorder.onstop = function () {
                var type = recorder.mimeType || 'audio/webm';
                lastBlob = new Blob(chunks, { type: type });
                if (lastUrl) URL.revokeObjectURL(lastUrl);
                lastUrl = URL.createObjectURL(lastBlob);
                audioEl.src = lastUrl;
                var ext = type.indexOf('mp4') !== -1 ? 'm4a' : (type.indexOf('ogg') !== -1 ? 'ogg' : 'webm');
                nativeLink.href = lastUrl;
                nativeLink.setAttribute('download', 'voice-recording.' + ext);
                metaEl.textContent = 'Browser format: ' + type + ' · Size: ' + (lastBlob.size / 1024).toFixed(1) + ' KB · Length: ' + fmt(totalSeconds());
                resultBox.classList.remove('d-none');
                statusEl.textContent = 'Done! Listen back or download.';
                stream.getTracks().forEach(function (t) { t.stop(); } );
                if (audioCtx) audioCtx.close();
                stopVisuals();
                startBtn.disabled = false;
                pauseBtn.disabled = true;
                stopBtn.disabled = true;
                pauseBtn.textContent = 'Pause';
            };
            recorder.start(250);
            segmentStart = Date.now();
            timerIv = setInterval(function () { timerEl.textContent = fmt(totalSeconds()); }, 250);
            drawWave();
            statusEl.textContent = 'Recording… speak now.';
            startBtn.disabled = true;
            pauseBtn.disabled = false;
            stopBtn.disabled = false;
        } ).catch(function (err) {
            showError('Microphone access was blocked or unavailable: ' + (err && err.message ? err.message : err));
        } );
    } );

    pauseBtn.addEventListener('click', function () {
        if (!recorder) return;
        try {
            if (!paused && recorder.state === 'recording') {
                recorder.pause();
                elapsedBefore += (Date.now() - segmentStart) / 1000;
                paused = true;
                pauseBtn.textContent = 'Resume';
                statusEl.textContent = 'Paused.';
            } else if (paused && recorder.state === 'paused') {
                recorder.resume();
                segmentStart = Date.now();
                paused = false;
                pauseBtn.textContent = 'Pause';
                statusEl.textContent = 'Recording… speak now.';
            }
        } catch (e) {
            // MediaRecorder.pause()/resume() are not supported in every browser.
            showError('Pause is not supported in this browser — please use Stop instead.');
        }
    } );

    stopBtn.addEventListener('click', function () {
        if (recorder && recorder.state !== 'inactive') {
            if (!paused) elapsedBefore += (Date.now() - segmentStart) / 1000;
            recorder.stop();
        }
    } );

    wavBtn.addEventListener('click', function () {
        if (!lastBlob) return;
        wavBtn.disabled = true;
        wavBtn.textContent = 'Converting…';
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        lastBlob.arrayBuffer().then(function (ab) { return ctx.decodeAudioData(ab); } ).then(function (buffer) {
            var wavBlob = bufferToWavBlob(buffer);
            var a = document.createElement('a');
            a.href = URL.createObjectURL(wavBlob);
            a.download = 'voice-recording.wav';
            document.body.appendChild(a);
            a.click();
            a.remove();
            wavBtn.disabled = false;
            wavBtn.textContent = '⬇ Download WAV';
        } ).catch(function () {
            showError('WAV conversion failed in this browser — please use the browser-format download instead.');
            wavBtn.disabled = false;
            wavBtn.textContent = '⬇ Download WAV';
        } );
    } );
} )();
</script>
@endsection
