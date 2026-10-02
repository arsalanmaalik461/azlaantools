@extends('layouts.app')
@section('title', 'Audio Equalizer — Free Online Tool')
@section('meta_description', 'Adjust bass mid and treble of audio and export the tuned file')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Audio Equalizer</h1>
            <p class="lead small text-muted">Boost or cut bass, mid and treble on any audio file, preview the tuned sound, and export it as WAV or MP3.</p>

                    <label class="form-label fw-semibold" for="eqFile">Choose an audio file</label>
                    <input type="file" id="eqFile" class="form-control" accept="audio/*">
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-4"><label class="form-label" for="eqBass">Bass: <span id="eqBassVal">0</span> dB</label><input type="range" id="eqBass" class="form-range" min="-15" max="15" value="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="eqMid">Mid: <span id="eqMidVal">0</span> dB</label><input type="range" id="eqMid" class="form-range" min="-15" max="15" value="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="eqTreble">Treble: <span id="eqTrebleVal">0</span> dB</label><input type="range" id="eqTreble" class="form-range" min="-15" max="15" value="0" step="1"></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="eqPlay" class="btn btn-primary" disabled>Preview With EQ</button>
                        <button type="button" id="eqStop" class="btn btn-outline-secondary">Stop</button>
                        <button type="button" id="eqReset" class="btn btn-outline-secondary">Reset EQ</button>
                        <button type="button" id="eqWav" class="btn btn-success" disabled>Export WAV</button>
                        <button type="button" id="eqMp3" class="btn btn-warning" disabled>Export MP3</button>
                    </div>

            <div id="eqMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose an audio file.</li>
                    <li>Move the Bass, Mid and Treble sliders and preview the result.</li>
                    <li>Export the tuned audio as WAV or MP3.</li>
            </ol>
            <p class="small text-muted mb-0">Bass is a low shelf at 200 Hz, Mid is a peak at 1000 Hz and Treble is a high shelf at 3200 Hz, applied with Web Audio biquad filters.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("eqMsg");
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

    var playSrc = null, liveNodes = [];
    function gains() { return [Number(document.getElementById("eqBass").value), Number(document.getElementById("eqMid").value), Number(document.getElementById("eqTreble").value)]; }
    function sync() { var g = gains(); document.getElementById("eqBassVal").textContent = g[0]; document.getElementById("eqMidVal").textContent = g[1]; document.getElementById("eqTrebleVal").textContent = g[2]; liveNodes.forEach(function (n, i) { n.gain.value = g[i]; }); }
    function makeFilters(ctx, dest) {
        var g = gains();
        var low = ctx.createBiquadFilter(); low.type = "lowshelf"; low.frequency.value = 200; low.gain.value = g[0];
        var mid = ctx.createBiquadFilter(); mid.type = "peaking"; mid.frequency.value = 1000; mid.Q.value = 1; mid.gain.value = g[1];
        var high = ctx.createBiquadFilter(); high.type = "highshelf"; high.frequency.value = 3200; high.gain.value = g[2];
        low.connect(mid); mid.connect(high); high.connect(dest);
        return [low, mid, high];
    }
    function stopPlay() { if (playSrc) { try { playSrc.stop(); } catch (e) {} playSrc = null; } liveNodes = []; }
    document.getElementById("eqFile").addEventListener("change", function () {
        loadAudio(this, function (f, buf) { ["eqPlay", "eqWav", "eqMp3"].forEach(function (id) { document.getElementById(id).disabled = false; }); showMsg("Loaded " + f.name + " (" + buf.duration.toFixed(1) + "s). Adjust the sliders and preview."); });
    });
    ["eqBass", "eqMid", "eqTreble"].forEach(function (id) { document.getElementById(id).addEventListener("input", sync); });
    document.getElementById("eqReset").addEventListener("click", function () { ["eqBass", "eqMid", "eqTreble"].forEach(function (id) { document.getElementById(id).value = 0; }); sync(); });
    document.getElementById("eqPlay").addEventListener("click", function () {
        if (!abuf) return; stopPlay();
        var ctx = getCtx(); if (ctx.state === "suspended") ctx.resume();
        playSrc = ctx.createBufferSource(); playSrc.buffer = abuf;
        liveNodes = makeFilters(ctx, ctx.destination);
        playSrc.connect(liveNodes[0]); playSrc.start(); showMsg("Playing with EQ applied...");
    });
    document.getElementById("eqStop").addEventListener("click", function () { stopPlay(); showMsg("Playback stopped."); });
    function renderEq(cb) {
        if (!abuf) return;
        var off = new OfflineAudioContext(abuf.numberOfChannels, abuf.length, abuf.sampleRate);
        var src = off.createBufferSource(); src.buffer = abuf;
        var filters = makeFilters(off, off.destination);
        src.connect(filters[0]); src.start();
        off.startRendering().then(cb).catch(function () { showMsg("Rendering failed for this file.", false); });
    }
    document.getElementById("eqWav").addEventListener("click", function () { renderEq(function (buf) { exportBuf(buf, false); }); });
    document.getElementById("eqMp3").addEventListener("click", function () { renderEq(function (buf) { exportBuf(buf, true); }); });

})();
</script>
@endsection
