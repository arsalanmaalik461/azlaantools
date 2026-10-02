@extends('layouts.app')

@section('title', 'Audio Joiner Online Free - Merge MP3 and Audio Files | Azlaan Tools')
@section('meta_description', 'Free online audio joiner: merge multiple MP3, WAV, M4A or OGG files into one, reorder them, add crossfade, export WAV or MP3. No signup, nothing uploaded.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Audio Joiner / Merger</h1>
            <p class="lead text-muted">Combine several audio files into one track — reorder them, add a smooth crossfade, and download the merged file.</p>

            <div id="ajAlert" class="alert alert-danger d-none" role="alert"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="ajFiles">1. Add audio files (MP3, WAV, M4A, OGG) — you can add more later</label>
                    <input type="file" id="ajFiles" class="form-control form-control-lg" accept="audio/*,.mp3,.wav,.m4a,.ogg,.opus" multiple>

                    <ul class="list-group mt-3" id="ajList"></ul>
                    <p class="small text-muted mt-2">Use ↑ ↓ to change the play order, ✕ to remove a file.</p>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="ajCrossfade">Crossfade between songs (seconds)</label>
                            <input type="number" id="ajCrossfade" class="form-control" min="0" max="10" step="0.5" value="0">
                            <div class="form-text">0 = simple join, one song after the other.</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <p class="mb-2">Total length: <strong id="ajTotal">0.0</strong>s · Files: <strong id="ajCount">0</strong></p>
                        </div>
                    </div>

                    <div id="ajResult" class="d-none mt-3">
                        <label class="form-label fw-semibold">Merged audio</label>
                        <audio id="ajAudio" class="w-100" controls></audio>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="ajWav" class="btn btn-success btn-lg">⬇ Merge &amp; Export WAV</button>
                        <button type="button" id="ajMp3" class="btn btn-warning btn-lg">⬇ Merge &amp; Export MP3</button>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="ajStatus">Add at least two files to merge (one file also works — it will be converted).</p>
                </div>
            </div>

            <div class="alert alert-secondary"><strong>Privacy note:</strong> Your audio files stay on your device — nothing is uploaded. Merging happens entirely in your browser.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Add two or more audio files.</li>
                    <li>Reorder them with the ↑ ↓ buttons so they play in the order you want.</li>
                    <li>Optionally set a crossfade in seconds for a smooth overlap between tracks.</li>
                    <li>Export as WAV (best quality) or MP3 (smaller file), then listen to the preview and download.</li>
                </ol>
                <p class="mb-0 small text-muted">Limitation: files are mixed at the highest sample rate among them and converted to stereo. Very long files may use a lot of memory on phones.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
(function () {
    var alertBox = document.getElementById('ajAlert');
    var fileInput = document.getElementById('ajFiles');
    var listEl = document.getElementById('ajList');
    var statusEl = document.getElementById('ajStatus');
    var resultBox = document.getElementById('ajResult');
    var audioEl = document.getElementById('ajAudio');
    var items = [];
    var audioCtx = null;
    var lastUrl = null;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }
    function getCtx() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        return audioCtx;
    }
    function renderList() {
        listEl.innerHTML = '';
        var total = 0;
        items.forEach(function (item, idx) {
            total += item.buffer.duration;
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2';
            var label = document.createElement('span');
            label.textContent = (idx + 1) + '. ' + item.name + ' (' + item.buffer.duration.toFixed(1) + 's)';
            var btns = document.createElement('span');
            var up = document.createElement('button');
            up.type = 'button';
            up.className = 'btn btn-sm btn-outline-secondary me-1';
            up.textContent = '↑';
            up.addEventListener('click', function () { move(idx, -1); } );
            var down = document.createElement('button');
            down.type = 'button';
            down.className = 'btn btn-sm btn-outline-secondary me-1';
            down.textContent = '↓';
            down.addEventListener('click', function () { move(idx, 1); } );
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '✕';
            del.addEventListener('click', function () {
                items.splice(idx, 1);
                renderList();
            } );
            btns.appendChild(up);
            btns.appendChild(down);
            btns.appendChild(del);
            li.appendChild(label);
            li.appendChild(btns);
            listEl.appendChild(li);
        } );
        document.getElementById('ajTotal').textContent = total.toFixed(1);
        document.getElementById('ajCount').textContent = items.length;
    }
    function move(idx, dir) {
        var j = idx + dir;
        if (j < 0 || j >= items.length) return;
        var tmp = items[idx];
        items[idx] = items[j];
        items[j] = tmp;
        renderList();
    }
    function renderMerged() {
        var crossfade = Math.max(0, Number(document.getElementById('ajCrossfade').value) || 0);
        var sr = 44100;
        items.forEach(function (item) {
            if (item.buffer.sampleRate > sr) sr = item.buffer.sampleRate;
        } );
        var totalLen = 0;
        var offsets = [];
        var cursor = 0;
        items.forEach(function (item, i) {
            offsets.push(cursor);
            var len = Math.floor(item.buffer.duration * sr);
            if (i === items.length - 1) {
                totalLen = cursor + len;
            } else {
                var cf = Math.min(Math.floor(crossfade * sr), len);
                cursor += len - cf;
            }
        } );
        var offline = new OfflineAudioContext(2, Math.max(1, totalLen), sr);
        items.forEach(function (item, i) {
            var src = offline.createBufferSource();
            src.buffer = item.buffer;
            var gain = offline.createGain();
            var startSec = offsets[i] / sr;
            var durSec = item.buffer.duration;
            if (crossfade > 0) {
                var cf = Math.min(crossfade, durSec / 2);
                if (i > 0) {
                    gain.gain.setValueAtTime(0, startSec);
                    gain.gain.linearRampToValueAtTime(1, startSec + cf);
                }
                if (i < items.length - 1) {
                    gain.gain.setValueAtTime(1, startSec + durSec - cf);
                    gain.gain.linearRampToValueAtTime(0, startSec + durSec);
                }
            }
            src.connect(gain);
            gain.connect(offline.destination);
            src.start(startSec);
        } );
        return offline.startRendering();
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
        var left = buf.getChannelData(0);
        var right = buf.numberOfChannels > 1 ? buf.getChannelData(1) : left;
        var enc = new lamejs.Mp3Encoder(2, buf.sampleRate, 128);
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
            var data = enc.encodeBuffer(l, r);
            if (data.length > 0) parts.push(new Int8Array(data));
        }
        var end = enc.flush();
        if (end.length > 0) parts.push(new Int8Array(end));
        return new Blob(parts, { type: 'audio/mpeg' });
    }
    function finishWith(blob, name) {
        if (lastUrl) URL.revokeObjectURL(lastUrl);
        lastUrl = URL.createObjectURL(blob);
        audioEl.src = lastUrl;
        resultBox.classList.remove('d-none');
        var a = document.createElement('a');
        a.href = lastUrl;
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        statusEl.textContent = 'Done! Your merged file downloaded — you can also play it above.';
    }

    fileInput.addEventListener('change', function () {
        var files = Array.prototype.slice.call(fileInput.files || []);
        if (!files.length) return;
        alertBox.classList.add('d-none');
        statusEl.textContent = 'Loading ' + files.length + ' file(s)…';
        // Decode results land in completion order (small files first), so collect
        // them in indexed slots and append in the user's selection order after
        // all settle — otherwise the track order (and the merge) is scrambled.
        var decoded = new Array(files.length);
        var pending = files.map(function (file, idx) {
            return file.arrayBuffer().then(function (ab) {
                return getCtx().decodeAudioData(ab);
            } ).then(function (buf) {
                decoded[idx] = { name: file.name, buffer: buf };
            } ).catch(function () {
                showError('Could not decode: ' + file.name + ' — it was skipped.');
            } );
        } );
        Promise.all(pending).then(function () {
            decoded.forEach(function (d) { if (d) items.push(d); });
            renderList();
            statusEl.textContent = items.length + ' file(s) ready. Set order and export.';
            fileInput.value = '';
        } );
    } );

    document.getElementById('ajWav').addEventListener('click', function () {
        if (!items.length) { showError('Please add at least one audio file first.'); return; }
        statusEl.textContent = 'Merging… please wait.';
        renderMerged().then(function (buf) { finishWith(wavBlob(buf), 'merged-audio.wav'); } ).catch(function () { showError('Merging failed — files may be too large for this device.'); } );
    } );
    document.getElementById('ajMp3').addEventListener('click', function () {
        if (!items.length) { showError('Please add at least one audio file first.'); return; }
        if (typeof lamejs === 'undefined') { showError('MP3 library unavailable — please use WAV export.'); return; }
        statusEl.textContent = 'Merging and encoding MP3… please wait.';
        renderMerged().then(function (buf) { finishWith(mp3Blob(buf), 'merged-audio.mp3'); } ).catch(function () { showError('Merging failed — files may be too large for this device.'); } );
    } );
} )();
</script>
@endsection
