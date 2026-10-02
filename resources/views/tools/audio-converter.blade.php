@extends('layouts.app')
@section('title', 'Audio Converter — Free Online Tool')
@section('meta_description', 'Convert audio between WAV MP3 OGG and WebM formats for free')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Audio Converter</h1>
            <p class="lead small text-muted">Convert audio files between WAV, MP3, WebM and OGG in your browser, with no upload and no signup.</p>

                    <label class="form-label fw-semibold" for="avFile">Choose an audio file</label>
                    <input type="file" id="avFile" class="form-control" accept="audio/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><label class="form-label" for="avFormat">Convert to</label><select id="avFormat" class="form-select"><option value="wav">WAV (lossless)</option><option value="mp3">MP3 (128 kbps)</option><option value="webm">WebM</option><option value="ogg">OGG</option></select></div>
                        <div class="col-md-6 d-flex align-items-end"><button type="button" id="avGo" class="btn btn-primary w-100" disabled>Convert and Download</button></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="avInfo">No file loaded yet. WebM and OGG export plays the audio once in real time to record it, and depends on browser support.</p>

            <div id="avMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose an audio file in any common format.</li>
                    <li>Pick the target format.</li>
                    <li>Click convert. WAV and MP3 render instantly, WebM and OGG record in real time.</li>
            </ol>
            <p class="small text-muted mb-0">Decoding uses the Web Audio API, MP3 encoding uses lamejs, and WebM or OGG uses MediaRecorder. If your browser cannot record OGG, the tool will tell you instead of making a broken file.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("avMsg");
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

    var curName = "audio";
    document.getElementById("avFile").addEventListener("change", function () {
        var inp = this;
        loadAudio(inp, function (f, buf) { curName = f.name.replace(/\.[^.]+$/, "") || "audio"; document.getElementById("avGo").disabled = false; document.getElementById("avInfo").textContent = "Loaded: " + f.name + " · " + buf.duration.toFixed(1) + "s · " + buf.sampleRate + " Hz · " + buf.numberOfChannels + " channel(s)."; showMsg("File decoded and ready to convert."); });
    });
    document.getElementById("avGo").addEventListener("click", function () {
        if (!abuf) { showMsg("Load an audio file first.", false); return; }
        var fmt = document.getElementById("avFormat").value, btn = this;
        if (fmt === "wav") { dl(bufferToWav(abuf), curName + ".wav"); showMsg("Converted to WAV."); return; }
        if (fmt === "mp3") { var b = bufferToMp3(abuf); if (!b) { showMsg("MP3 library failed to load. WAV export still works.", false); return; } dl(b, curName + ".mp3"); showMsg("Converted to MP3 (128 kbps)."); return; }
        var mime = fmt === "webm" ? "audio/webm" : "audio/ogg";
        if (typeof MediaRecorder === "undefined" || (MediaRecorder.isTypeSupported && !MediaRecorder.isTypeSupported(mime))) { showMsg("Your browser cannot record " + fmt.toUpperCase() + ". Try WAV or MP3 instead.", false); return; }
        btn.disabled = true; showMsg("Recording " + fmt.toUpperCase() + " in real time (" + abuf.duration.toFixed(0) + "s). Keep this tab open...");
        var ctx = getCtx(), dest = ctx.createMediaStreamDestination();
        var rec = new MediaRecorder(dest.stream, { mimeType: mime }), chunks = [];
        rec.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        rec.onstop = function () { btn.disabled = false; dl(new Blob(chunks, { type: mime }), curName + "." + fmt); showMsg("Converted to " + fmt.toUpperCase() + "."); };
        var src = ctx.createBufferSource(); src.buffer = abuf; src.connect(dest); src.connect(ctx.destination);
        rec.start(); src.start(); src.onended = function () { setTimeout(function () { if (rec.state !== "inactive") rec.stop(); }, 250); };
    });

})();
</script>
@endsection
