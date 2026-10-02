@extends('layouts.app')
@section('title', 'Extract Audio from Video — Free Online Tool')
@section('meta_description', 'Extract audio from any video file and save it as MP3 or WAV')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Extract Audio from Video</h1>
            <p class="lead small text-muted">Pull the audio track out of a video file and save it as MP3 or WAV, entirely in your browser.</p>

                    <label class="form-label fw-semibold" for="eaFile">Choose a video file (MP4, WebM, MOV)</label>
                    <input type="file" id="eaFile" class="form-control" accept="video/*,.mp4,.webm,.mov,.m4v">
                    <div class="text-center mt-3"><video id="eaVideo" class="w-100 rounded d-none" controls playsinline style="max-height:320px"></video></div>
                    <p class="small text-muted mt-2 mb-0" id="eaInfo">No video loaded yet.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="eaWav" class="btn btn-success" disabled>Extract WAV (fast)</button>
                        <button type="button" id="eaMp3" class="btn btn-warning" disabled>Extract MP3 (fast)</button>
                        <button type="button" id="eaRec" class="btn btn-outline-primary" disabled>Record Audio in Real Time (fallback)</button>
                    </div>

            <div id="eaMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a video file.</li>
                    <li>Try the fast extract buttons, which decode the audio track directly.</li>
                    <li>If fast decode fails for your file type, use the real time fallback, which records while the video plays once.</li>
            </ol>
            <p class="small text-muted mb-0">Fast extraction depends on whether your browser can decode the audio inside that video container. The real time fallback works for any video the browser can play, but takes as long as the clip.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("eaMsg");
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

    var vbuf = null, recording = false;
    var video = document.getElementById("eaVideo");
    document.getElementById("eaFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        vbuf = null;
        video.src = URL.createObjectURL(f); video.classList.remove("d-none");
        document.getElementById("eaInfo").textContent = "Decoding the audio track of " + f.name + "...";
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        f.arrayBuffer().then(function (ab) { return ctx.decodeAudioData(ab); }).then(function (buf) {
            vbuf = buf;
            ["eaWav", "eaMp3", "eaRec"].forEach(function (id) { document.getElementById(id).disabled = false; });
            document.getElementById("eaInfo").textContent = "Audio track decoded: " + buf.duration.toFixed(1) + "s · " + buf.sampleRate + " Hz. Fast extract is ready.";
        }).catch(function () {
            document.getElementById("eaRec").disabled = false;
            document.getElementById("eaInfo").textContent = "Fast decode is not supported for this file in your browser. Use the real time fallback below.";
        });
    });
    document.getElementById("eaWav").addEventListener("click", function () { if (vbuf) { dl(bufferToWav(vbuf), "extracted-audio.wav"); showMsg("WAV extracted."); } });
    document.getElementById("eaMp3").addEventListener("click", function () { if (vbuf) { var b = bufferToMp3(vbuf); if (b) { dl(b, "extracted-audio.mp3"); showMsg("MP3 extracted (128 kbps)."); } else showMsg("MP3 library unavailable. Use WAV instead.", false); } });
    document.getElementById("eaRec").addEventListener("click", function () {
        if (recording || !video.src) return;
        var stream = null;
        try { stream = video.captureStream ? video.captureStream() : (video.mozCaptureStream ? video.mozCaptureStream() : null); } catch (e) { stream = null; }
        if (!stream || !stream.getAudioTracks().length) { showMsg("This browser cannot capture audio from the video element. Try Chrome on desktop.", false); return; }
        if (typeof MediaRecorder === "undefined") { showMsg("MediaRecorder is not available in this browser.", false); return; }
        var audioStream = new MediaStream(stream.getAudioTracks());
        var mime = MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported("audio/webm") ? "audio/webm" : "";
        var rec = new MediaRecorder(audioStream, mime ? { mimeType: mime } : undefined), chunks = [];
        rec.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        rec.onstop = function () { recording = false; dl(new Blob(chunks, { type: mime || "audio/webm" }), "extracted-audio.webm"); showMsg("Audio recorded and downloaded as WebM audio."); };
        recording = true; video.currentTime = 0; video.muted = false;
        video.play().then(function () { rec.start(); showMsg("Recording audio in real time. Keep this tab open until the video ends..."); }).catch(function () { recording = false; showMsg("Playback was blocked. Press play on the video, then try again.", false); });
        video.onended = function () { setTimeout(function () { if (rec.state !== "inactive") rec.stop(); }, 300); };
    });

})();
</script>
@endsection
