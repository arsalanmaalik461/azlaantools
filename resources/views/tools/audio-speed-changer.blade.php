@extends('layouts.app')
@section('title', 'Audio Speed and Pitch Changer — Free Online Tool')
@section('meta_description', 'Change audio speed and pitch and export the edited audio file')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Audio Speed and Pitch Changer</h1>
            <p class="lead small text-muted">Speed up or slow down audio and shift its pitch, preview the result, and export the edited file as WAV or MP3.</p>

                    <label class="form-label fw-semibold" for="asFile">Choose an audio file</label>
                    <input type="file" id="asFile" class="form-control" accept="audio/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><label class="form-label" for="asSpeed">Speed: <span id="asSpeedVal">1.00</span>x</label><input type="range" id="asSpeed" class="form-range" min="0.25" max="3" step="0.05" value="1"></div>
                        <div class="col-md-6"><label class="form-label" for="asPitch">Extra pitch shift: <span id="asPitchVal">0</span> semitones</label><input type="range" id="asPitch" class="form-range" min="-12" max="12" step="1" value="0"></div>
                    </div>
                    <p class="small text-muted mb-0" id="asInfo">Changing speed also changes pitch, like classic tape. The pitch slider adds an extra shift on top.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="asPlay" class="btn btn-primary" disabled>Preview</button>
                        <button type="button" id="asStop" class="btn btn-outline-secondary">Stop</button>
                        <button type="button" id="asWav" class="btn btn-success" disabled>Export WAV</button>
                        <button type="button" id="asMp3" class="btn btn-warning" disabled>Export MP3</button>
                    </div>

            <div id="asMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose an audio file.</li>
                    <li>Set the speed and any extra pitch shift, then preview.</li>
                    <li>Export the edited audio as WAV or MP3.</li>
            </ol>
            <p class="small text-muted mb-0">Speed is applied by resampling playback in an offline render, so the exported file length changes with speed: 2x is half as long, 0.5x is twice as long.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("asMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function dl(blob, name) {
        var a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    }
    function fmtBytes(b) {
        if (!b && b !== 0) return "-";
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        return (b / 1048576).toFixed(2) + " MB";
    }

    function bufferToWav(buf) {
        var numCh = Math.min(2, buf.numberOfChannels);
        var sr = buf.sampleRate, len = buf.length, block = numCh * 2, dataSize = len * block;
        var ab = new ArrayBuffer(44 + dataSize), view = new DataView(ab);
        function ws(off, s) { for (var i = 0; i < s.length; i++) view.setUint8(off + i, s.charCodeAt(i)); }
        ws(0, "RIFF"); view.setUint32(4, 36 + dataSize, true); ws(8, "WAVE"); ws(12, "fmt ");
        view.setUint32(16, 16, true); view.setUint16(20, 1, true); view.setUint16(22, numCh, true);
        view.setUint32(24, sr, true); view.setUint32(28, sr * block, true); view.setUint16(32, block, true);
        view.setUint16(34, 16, true); ws(36, "data"); view.setUint32(40, dataSize, true);
        var chans = []; for (var c = 0; c < numCh; c++) chans.push(buf.getChannelData(c));
        var off = 44;
        for (var i = 0; i < len; i++) { for (var ch = 0; ch < numCh; ch++) { var s = Math.max(-1, Math.min(1, chans[ch][i])); view.setInt16(off, s < 0 ? s * 32768 : s * 32767, true); off += 2; } }
        return new Blob([ab], { type: "audio/wav" });
    }

    function f16(v) { var s = Math.max(-1, Math.min(1, v)); return s < 0 ? s * 32768 : s * 32767; }
    function bufferToMp3(buf) {
        if (typeof lamejs === "undefined") return null;
        var left = buf.getChannelData(0), right = buf.numberOfChannels > 1 ? buf.getChannelData(1) : left;
        var enc = new lamejs.Mp3Encoder(buf.numberOfChannels > 1 ? 2 : 1, buf.sampleRate, 128), parts = [], block = 1152;
        for (var i = 0; i < left.length; i += block) {
            var l = new Int16Array(block), r = new Int16Array(block);
            for (var j = 0; j < block; j++) { var k = i + j; l[j] = k < left.length ? f16(left[k]) : 0; r[j] = k < right.length ? f16(right[k]) : 0; }
            var d = buf.numberOfChannels > 1 ? enc.encodeBuffer(l, r) : enc.encodeBuffer(l);
            if (d.length > 0) parts.push(new Int8Array(d));
        }
        var e = enc.flush(); if (e.length > 0) parts.push(new Int8Array(e));
        return new Blob(parts, { type: "audio/mpeg" });
    }

    var actx = null, abuf = null;
    function getCtx() { if (!actx) actx = new (window.AudioContext || window.webkitAudioContext)(); return actx; }
    function loadAudio(input, onOk) {
        var f = input.files && input.files[0]; if (!f) return;
        f.arrayBuffer().then(function (ab) { return getCtx().decodeAudioData(ab); }).then(function (buf) { abuf = buf; if (onOk) onOk(f, buf); }).catch(function () { showMsg("This audio file could not be decoded. Try MP3, WAV, M4A or OGG.", false); });
    }
    function playBuffer(buf) {
        var ctx = getCtx(); if (ctx.state === "suspended") ctx.resume();
        var src = ctx.createBufferSource(); src.buffer = buf; src.connect(ctx.destination); src.start(); return src;
    }

    function exportBuf(buf, mp3) {
        if (!buf) { showMsg("Load an audio file first.", false); return; }
        if (mp3) { var b = bufferToMp3(buf); if (!b) { showMsg("MP3 library unavailable. Use WAV export instead.", false); return; } dl(b, "edited-audio.mp3"); showMsg("MP3 exported (128 kbps)."); }
        else { dl(bufferToWav(buf), "edited-audio.wav"); showMsg("WAV exported."); }
    }

    var playSrc = null;
    function stopPlay() { if (playSrc) { try { playSrc.stop(); } catch (e) {} playSrc = null; } }
    function sync() {
        var sp = Number(document.getElementById("asSpeed").value), pi = Number(document.getElementById("asPitch").value);
        document.getElementById("asSpeedVal").textContent = sp.toFixed(2); document.getElementById("asPitchVal").textContent = pi;
        if (abuf) document.getElementById("asInfo").textContent = "New length will be about " + (abuf.duration / sp).toFixed(1) + "s at " + sp.toFixed(2) + "x speed.";
    }
    document.getElementById("asFile").addEventListener("change", function () {
        loadAudio(this, function (f, buf) { ["asPlay", "asWav", "asMp3"].forEach(function (id) { document.getElementById(id).disabled = false; }); sync(); showMsg("Loaded " + f.name + " (" + buf.duration.toFixed(1) + "s)."); });
    });
    ["asSpeed", "asPitch"].forEach(function (id) { document.getElementById(id).addEventListener("input", sync); });
    function renderSpeed(cb) {
        var sp = Number(document.getElementById("asSpeed").value), semi = Number(document.getElementById("asPitch").value);
        var outLen = Math.max(1, Math.ceil(abuf.length / sp));
        var off = new OfflineAudioContext(abuf.numberOfChannels, outLen, abuf.sampleRate);
        var src = off.createBufferSource(); src.buffer = abuf; src.playbackRate.value = sp; src.detune.value = semi * 100;
        src.connect(off.destination); src.start();
        off.startRendering().then(cb).catch(function () { showMsg("Rendering failed for this file.", false); });
    }
    document.getElementById("asPlay").addEventListener("click", function () { if (!abuf) return; stopPlay(); renderSpeed(function (buf) { playSrc = playBuffer(buf); showMsg("Playing edited preview."); }); });
    document.getElementById("asStop").addEventListener("click", function () { stopPlay(); showMsg("Playback stopped."); });
    document.getElementById("asWav").addEventListener("click", function () { renderSpeed(function (buf) { exportBuf(buf, false); }); });
    document.getElementById("asMp3").addEventListener("click", function () { renderSpeed(function (buf) { exportBuf(buf, true); }); });

})();
</script>
@endsection
