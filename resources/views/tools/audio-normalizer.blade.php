@extends('layouts.app')
@section('title', 'Audio Normalizer — Free Online Tool')
@section('meta_description', 'Normalize audio volume so quiet and loud parts sound balanced')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Audio Normalizer</h1>
            <p class="lead small text-muted">Measure the peak and loudness of an audio file, even it out with gentle compression and gain, and export a balanced file.</p>

                    <label class="form-label fw-semibold" for="anFile">Choose an audio file</label>
                    <input type="file" id="anFile" class="form-control" accept="audio/*">
                    <p class="small text-muted mt-2 mb-0" id="anStats">No file loaded yet.</p>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6"><label class="form-label" for="anTarget">Target peak level</label><select id="anTarget" class="form-select"><option value="0.99">-0.1 dB (loudest safe)</option><option value="0.89">-1 dB</option><option value="0.71">-3 dB (gentle)</option></select></div>
                        <div class="col-md-6"><label class="form-label" for="anComp">Compression</label><select id="anComp" class="form-select"><option value="3">Light (3:1)</option><option value="6">Medium (6:1)</option><option value="12">Strong (12:1)</option><option value="1">None (peak gain only)</option></select></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="anPlay" class="btn btn-primary" disabled>Preview Normalized</button>
                        <button type="button" id="anStop" class="btn btn-outline-secondary">Stop</button>
                        <button type="button" id="anWav" class="btn btn-success" disabled>Export WAV</button>
                        <button type="button" id="anMp3" class="btn btn-warning" disabled>Export MP3</button>
                    </div>

            <div id="anMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose an audio file and read its measured peak and RMS levels.</li>
                    <li>Pick a target peak and compression strength.</li>
                    <li>Preview, then export the normalized audio as WAV or MP3.</li>
            </ol>
            <p class="small text-muted mb-0">Normalization here raises overall gain so the loudest peak hits the target, after a dynamics compressor tames spikes. It cannot restore clipped or distorted recordings.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("anMsg");
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

    var playSrc = null, rendered = null;
    function stopPlay() { if (playSrc) { try { playSrc.stop(); } catch (e) {} playSrc = null; } }
    function analyze(buf) {
        var peak = 0, sum = 0, n = 0;
        for (var c = 0; c < buf.numberOfChannels; c++) { var d = buf.getChannelData(c); for (var i = 0; i < d.length; i += 7) { var a = Math.abs(d[i]); if (a > peak) peak = a; sum += d[i] * d[i]; n++; } }
        return { peak: peak, rms: n ? Math.sqrt(sum / n) : 0 };
    }
    function db(v) { return v > 0 ? (20 * Math.log10(v)).toFixed(1) + " dB" : "-inf dB"; }
    document.getElementById("anFile").addEventListener("change", function () {
        loadAudio(this, function (f, buf) {
            var a = analyze(buf);
            document.getElementById("anStats").textContent = "Loaded: " + f.name + " · Peak " + db(a.peak) + " · RMS " + db(a.rms) + " · " + buf.duration.toFixed(1) + "s.";
            ["anPlay", "anWav", "anMp3"].forEach(function (id) { document.getElementById(id).disabled = false; });
            rendered = null; showMsg("Levels measured. Choose settings and preview or export.");
        });
    });
    function renderNorm(cb) {
        var target = Number(document.getElementById("anTarget").value), ratio = Number(document.getElementById("anComp").value);
        var off = new OfflineAudioContext(abuf.numberOfChannels, abuf.length, abuf.sampleRate);
        var src = off.createBufferSource(); src.buffer = abuf;
        var last = src;
        if (ratio > 1) { var comp = off.createDynamicsCompressor(); comp.threshold.value = -18; comp.ratio.value = ratio; comp.attack.value = 0.005; comp.release.value = 0.2; src.connect(comp); last = comp; }
        var gain = off.createGain(); gain.gain.value = 1; last.connect(gain); gain.connect(off.destination); src.start();
        off.startRendering().then(function (mid) {
            var a = analyze(mid), g = a.peak > 0 ? target / a.peak : 1;
            var off2 = new OfflineAudioContext(abuf.numberOfChannels, abuf.length, abuf.sampleRate);
            var s2 = off2.createBufferSource(); s2.buffer = mid;
            var g2 = off2.createGain(); g2.gain.value = g; s2.connect(g2); g2.connect(off2.destination); s2.start();
            return off2.startRendering();
        }).then(function (buf) { rendered = buf; cb(buf); }).catch(function () { showMsg("Rendering failed for this file.", false); });
    }
    document.getElementById("anPlay").addEventListener("click", function () { if (!abuf) return; stopPlay(); renderNorm(function (buf) { playSrc = playBuffer(buf); showMsg("Playing normalized preview."); }); });
    document.getElementById("anStop").addEventListener("click", function () { stopPlay(); showMsg("Playback stopped."); });
    document.getElementById("anWav").addEventListener("click", function () { renderNorm(function (buf) { exportBuf(buf, false); }); });
    document.getElementById("anMp3").addEventListener("click", function () { renderNorm(function (buf) { exportBuf(buf, true); }); });

})();
</script>
@endsection
