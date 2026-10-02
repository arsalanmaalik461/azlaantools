@extends('layouts.app')

@section('title', 'Audio Cutter Online Free - Trim MP3, WAV, M4A | Azlaan Tools')
@section('meta_description', 'Free online audio cutter: upload MP3, WAV, M4A or OGG, trim with waveform and sliders, add fade in/out, export WAV or MP3. No signup, files never leave your device.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Audio Cutter / Trimmer</h1>
            <p class="lead text-muted">Cut any part of a song, voice note or recording. Pick the start and end, add fades, and download the trimmed clip.</p>

            <div id="acAlert" class="alert alert-danger d-none" role="alert"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="acFile">1. Choose an audio file (MP3, WAV, M4A, OGG)</label>
                    <input type="file" id="acFile" class="form-control form-control-lg" accept="audio/*,.mp3,.wav,.m4a,.ogg,.opus">

                    <div id="acEditor" class="d-none mt-4">
                        <canvas id="acCanvas" width="900" height="140" class="w-100 rounded bg-dark" style="cursor:pointer;"></canvas>
                        <p class="small text-muted">Tip: click on the waveform to move the start point; use the sliders for fine control.</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="acStart">Start: <span id="acStartVal">0.0</span>s</label>
                                <input type="range" id="acStart" class="form-range" min="0" max="100" step="0.1" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="acEnd">End: <span id="acEndVal">0.0</span>s</label>
                                <input type="range" id="acEnd" class="form-range" min="0" max="100" step="0.1" value="100">
                            </div>
                        </div>
                        <p class="mb-2">Selection length: <strong id="acLen">0.0</strong>s &nbsp;·&nbsp; File length: <strong id="acDur">0.0</strong>s</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="acFadeIn">Fade in (seconds)</label>
                                <input type="number" id="acFadeIn" class="form-control" min="0" max="10" step="0.5" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="acFadeOut">Fade out (seconds)</label>
                                <input type="number" id="acFadeOut" class="form-control" min="0" max="10" step="0.5" value="0">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" id="acPlay" class="btn btn-primary btn-lg">▶ Play Selection (loop)</button>
                            <button type="button" id="acStop" class="btn btn-outline-secondary btn-lg">Stop</button>
                            <button type="button" id="acWav" class="btn btn-success btn-lg">⬇ Export WAV</button>
                            <button type="button" id="acMp3" class="btn btn-warning btn-lg">⬇ Export MP3</button>
                        </div>
                        <p class="small text-muted mt-2 mb-0" id="acStatus">Ready.</p>
                    </div>
                </div>
            </div>

            <div class="alert alert-secondary"><strong>Privacy note:</strong> Your audio file stays on your device — nothing is uploaded. All cutting and exporting happens in your browser.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Upload an MP3, WAV, M4A or OGG file.</li>
                    <li>Drag the Start and End sliders (or click the waveform) to select the part you want to keep.</li>
                    <li>Optionally set fade in / fade out seconds, then press Play Selection to check it loops correctly.</li>
                    <li>Export as WAV (best quality, bigger file) or MP3 (smaller, works everywhere).</li>
                </ol>
                <p class="mb-0 small text-muted">Limitation: very long or very large files may use a lot of memory on phones — a computer works best for big files.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
(function () {
    var alertBox = document.getElementById('acAlert');
    var fileInput = document.getElementById('acFile');
    var editor = document.getElementById('acEditor');
    var canvas = document.getElementById('acCanvas');
    var ctx2d = canvas.getContext('2d');
    var startRange = document.getElementById('acStart');
    var endRange = document.getElementById('acEnd');
    var statusEl = document.getElementById('acStatus');
    var audioCtx = null;
    var buffer = null;
    var peaks = [];
    var playSrc = null;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }
    function fmt(n) { return Number(n).toFixed(1); }
    function getCtx() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        return audioCtx;
    }
    function computePeaks() {
        peaks = [];
        var ch = buffer.getChannelData(0);
        var buckets = canvas.width;
        var per = Math.max(1, Math.floor(ch.length / buckets));
        for (var i = 0; i < buckets; i++) {
            var max = 0;
            var start = i * per;
            for (var j = 0; j < per; j += 97) {
                var v = Math.abs(ch[start + j] || 0);
                if (v > max) max = v;
            }
            peaks.push(max);
        }
    }
    function draw() {
        if (!buffer) return;
        var w = canvas.width;
        var h = canvas.height;
        var sFrac = Number(startRange.value) / buffer.duration;
        var eFrac = Number(endRange.value) / buffer.duration;
        ctx2d.fillStyle = '#212529';
        ctx2d.fillRect(0, 0, w, h);
        for (var x = 0; x < w; x++) {
            var frac = x / w;
            var amp = (peaks[x] || 0) * (h / 2 - 4);
            ctx2d.fillStyle = (frac >= sFrac && frac <= eFrac) ? '#0dcaf0' : '#6c757d';
            ctx2d.fillRect(x, h / 2 - amp, 1, amp * 2 || 1);
        }
        ctx2d.fillStyle = '#ffc107';
        ctx2d.fillRect(sFrac * w - 1, 0, 3, h);
        ctx2d.fillRect(eFrac * w - 1, 0, 3, h);
    }
    function syncLabels() {
        document.getElementById('acStartVal').textContent = fmt(startRange.value);
        document.getElementById('acEndVal').textContent = fmt(endRange.value);
        document.getElementById('acLen').textContent = fmt(Math.max(0, Number(endRange.value) - Number(startRange.value)));
        draw();
    }
    function sliceBuffer() {
        var start = Number(startRange.value);
        var end = Number(endRange.value);
        var sr = buffer.sampleRate;
        var from = Math.floor(start * sr);
        var to = Math.min(buffer.length, Math.floor(end * sr));
        var len = Math.max(1, to - from);
        var out = getCtx().createBuffer(buffer.numberOfChannels, len, sr);
        var fadeIn = Math.min(len, Math.floor((Number(document.getElementById('acFadeIn').value) || 0) * sr));
        var fadeOut = Math.min(len, Math.floor((Number(document.getElementById('acFadeOut').value) || 0) * sr));
        for (var c = 0; c < buffer.numberOfChannels; c++) {
            var src = buffer.getChannelData(c);
            var dst = out.getChannelData(c);
            for (var i = 0; i < len; i++) {
                var gain = 1;
                if (fadeIn > 0 && i < fadeIn) gain = i / fadeIn;
                if (fadeOut > 0 && i >= len - fadeOut) gain = Math.min(gain, (len - i) / fadeOut);
                dst[i] = src[from + i] * gain;
            }
        }
        return out;
    }
    function wavBlob(buf) {
        var numCh = Math.min(2, buf.numberOfChannels);
        var sr = buf.sampleRate;
        var len = buf.length;
        var blockAlign = numCh * 2;
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
        var chans = [];
        for (var c = 0; c < numCh; c++) chans.push(buf.getChannelData(c));
        var off = 44;
        for (var i = 0; i < len; i++) {
            for (var ch = 0; ch < numCh; ch++) {
                var s = Math.max(-1, Math.min(1, chans[ch][i]));
                view.setInt16(off, s < 0 ? s * 32768 : s * 32767, true);
                off += 2;
            }
        }
        return new Blob([ab], { type: 'audio/wav' });
    }
    function floatTo16(v) {
        var s = Math.max(-1, Math.min(1, v));
        return s < 0 ? s * 32768 : s * 32767;
    }
    function mp3Blob(buf) {
        if (typeof lamejs === 'undefined') return null;
        var sr = buf.sampleRate;
        var left = buf.getChannelData(0);
        var right = buf.numberOfChannels > 1 ? buf.getChannelData(1) : left;
        var enc = new lamejs.Mp3Encoder(buf.numberOfChannels > 1 ? 2 : 1, sr, 128);
        var parts = [];
        var block = 1152;
        for (var i = 0; i < left.length; i += block) {
            var l = new Int16Array(block);
            var r = new Int16Array(block);
            for (var j = 0; j < block; j++) {
                var idx = i + j;
                l[j] = idx < left.length ? floatTo16(left[idx]) : 0;
                r[j] = idx < right.length ? floatTo16(right[idx]) : 0;
            }
            var data = buf.numberOfChannels > 1 ? enc.encodeBuffer(l, r) : enc.encodeBuffer(l);
            if (data.length > 0) parts.push(new Int8Array(data));
        }
        var end = enc.flush();
        if (end.length > 0) parts.push(new Int8Array(end));
        return new Blob(parts, { type: 'audio/mpeg' });
    }
    function download(blob, name) {
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
    }
    function stopPlayback() {
        if (playSrc) {
            try { playSrc.stop(); } catch (e) { }
            playSrc = null;
        }
    }

    fileInput.addEventListener('change', function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        alertBox.classList.add('d-none');
        statusEl.textContent = 'Loading file…';
        file.arrayBuffer().then(function (ab) {
            return getCtx().decodeAudioData(ab);
        } ).then(function (decoded) {
            buffer = decoded;
            editor.classList.remove('d-none');
            startRange.max = buffer.duration;
            endRange.max = buffer.duration;
            startRange.value = 0;
            endRange.value = buffer.duration;
            document.getElementById('acDur').textContent = fmt(buffer.duration);
            computePeaks();
            syncLabels();
            statusEl.textContent = 'Loaded: ' + file.name + '. Drag the sliders to trim.';
        } ).catch(function () {
            showError('This file could not be decoded. Please try an MP3, WAV, M4A or OGG file.');
            statusEl.textContent = 'Ready.';
        } );
    } );

    startRange.addEventListener('input', function () {
        if (Number(startRange.value) > Number(endRange.value) - 0.1) startRange.value = Math.max(0, Number(endRange.value) - 0.1);
        syncLabels();
    } );
    endRange.addEventListener('input', function () {
        if (Number(endRange.value) < Number(startRange.value) + 0.1) endRange.value = Math.min(buffer ? buffer.duration : 100, Number(startRange.value) + 0.1);
        syncLabels();
    } );
    canvas.addEventListener('click', function (ev) {
        if (!buffer) return;
        var rect = canvas.getBoundingClientRect();
        var frac = (ev.clientX - rect.left) / rect.width;
        startRange.value = Math.max(0, Math.min(Number(endRange.value) - 0.1, frac * buffer.duration));
        syncLabels();
    } );

    document.getElementById('acPlay').addEventListener('click', function () {
        if (!buffer) return;
        stopPlayback();
        var ctx = getCtx();
        if (ctx.state === 'suspended') ctx.resume();
        playSrc = ctx.createBufferSource();
        playSrc.buffer = sliceBuffer();
        playSrc.loop = true;
        playSrc.connect(ctx.destination);
        playSrc.start();
        statusEl.textContent = 'Playing selection on loop…';
    } );
    document.getElementById('acStop').addEventListener('click', function () {
        stopPlayback();
        statusEl.textContent = 'Playback stopped.';
    } );
    document.getElementById('acWav').addEventListener('click', function () {
        if (!buffer) return;
        stopPlayback();
        download(wavBlob(sliceBuffer()), 'trimmed-audio.wav');
        statusEl.textContent = 'WAV exported.';
    } );
    document.getElementById('acMp3').addEventListener('click', function () {
        if (!buffer) return;
        stopPlayback();
        statusEl.textContent = 'Encoding MP3…';
        setTimeout(function () {
            var blob = mp3Blob(sliceBuffer());
            if (blob) {
                download(blob, 'trimmed-audio.mp3');
                statusEl.textContent = 'MP3 exported (128 kbps).';
            } else {
                statusEl.textContent = 'MP3 library unavailable — please use WAV export.';
            }
        }, 50);
    } );
} )();
</script>
@endsection
