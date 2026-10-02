@extends('layouts.app')
@section('title', 'Video Speed Changer — Free Online Tool')
@section('meta_description', 'Speed up or slow down any video and export the new version')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Video Speed Changer</h1>
            <p class="lead small text-muted">Speed up or slow down a video, from quarter speed slow motion to four times fast, and export the new version.</p>

                    <label class="form-label fw-semibold" for="vsFile">Choose a video</label>
                    <input type="file" id="vsFile" class="form-control" accept="video/*">
                    <div class="text-center mt-3"><video id="vsVideo" class="w-100 rounded d-none" controls playsinline style="max-height:300px"></video></div>
                    <label class="form-label mt-3" for="vsRate">Playback speed: <span id="vsRateVal">1.00</span>x</label>
                    <input type="range" id="vsRate" class="form-range" min="0.25" max="4" step="0.25" value="1">
                    <div class="d-flex justify-content-between small text-muted"><span>0.25x slow</span><span>1x normal</span><span>2x</span><span>4x fast</span></div>
                    <button type="button" id="vsGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Record at New Speed</button>

                    <div id="vsResult" class="d-none mt-3 text-center"><video id="vsOut" class="w-100 rounded" controls style="max-height:320px"></video><div class="mt-2"><a id="vsDl" href="#" class="btn btn-success btn-lg">Download Video</a></div></div>
                    <p class="small text-muted mt-2 mb-0" id="vsStatus">Ready. Recording happens in real time while the video plays.</p>

            <div id="vsMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a video and drag the speed slider.</li>
                    <li>Click record. The video plays through once at the new speed while it is recorded.</li>
                    <li>Preview the result and download it.</li>
            </ol>
            <p class="small text-muted mb-0">The new file length is the original length divided by the speed, so 2x halves the time and 0.5x doubles it. Pitch correction is turned off, so voices change pitch like classic fast forward. Recording is real time relative to the new speed.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("vsMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function pickMime() {
        if (typeof MediaRecorder === "undefined") return null;
        var opts = ["video/mp4", "video/webm;codecs=vp9", "video/webm"];
        for (var i = 0; i < opts.length; i++) { if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(opts[i])) return opts[i]; }
        return "";
    }
    function combinedStream(video, canvas) {
        var tracks = canvas.captureStream(30).getVideoTracks().slice();
        try { var vs = video.captureStream ? video.captureStream() : (video.mozCaptureStream ? video.mozCaptureStream() : null); if (vs) vs.getAudioTracks().forEach(function (t) { tracks.push(t); }); } catch (e) {}
        return new MediaStream(tracks);
    }
    function recordStream(stream, mime, onDone) {
        var rec = new MediaRecorder(stream, mime ? { mimeType: mime, videoBitsPerSecond: 6000000 } : undefined), chunks = [];
        rec.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        rec.onstop = function () { onDone(new Blob(chunks, { type: mime || "video/webm" }), mime === "video/mp4" ? "mp4" : "webm"); };
        rec.start(200); return rec;
    }
    function showResult(blob, ext, videoEl, dlEl, wrapEl, label) {
        var url = URL.createObjectURL(blob);
        videoEl.src = url; dlEl.href = url; dlEl.setAttribute("download", label + "." + ext);
        wrapEl.classList.remove("d-none");
    }

    var video = document.getElementById("vsVideo"), busy = false;
    document.getElementById("vsRate").addEventListener("input", function () { document.getElementById("vsRateVal").textContent = Number(this.value).toFixed(2); if (video.src) video.playbackRate = Number(this.value); });
    document.getElementById("vsFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        video.src = URL.createObjectURL(f); video.classList.remove("d-none");
        video.playbackRate = Number(document.getElementById("vsRate").value);
        document.getElementById("vsGo").disabled = false; document.getElementById("vsResult").classList.add("d-none");
        showMsg("Loaded " + f.name + ". Preview plays at the chosen speed.");
    });
    document.getElementById("vsGo").addEventListener("click", function () {
        if (busy || !video.videoWidth) return;
        var mime = pickMime(); if (mime === null) { showMsg("MediaRecorder is not available in this browser.", false); return; }
        var rate = Number(document.getElementById("vsRate").value);
        var cv = document.createElement("canvas"); cv.width = video.videoWidth; cv.height = video.videoHeight;
        var ctx = cv.getContext("2d");
        busy = true; this.disabled = true;
        var status = document.getElementById("vsStatus"); status.textContent = "Recording at " + rate + "x... this takes about " + (video.duration / rate).toFixed(0) + "s.";
        var rec = recordStream(combinedStream(video, cv), mime, function (blob, ext) {
            busy = false; document.getElementById("vsGo").disabled = false;
            showResult(blob, ext, document.getElementById("vsOut"), document.getElementById("vsDl"), document.getElementById("vsResult"), "speed-video");
            status.textContent = "Done. Preview and download.";
        });
        video.currentTime = 0; video.playbackRate = rate;
        try { video.preservesPitch = false; video.mozPreservesPitch = false; video.webkitPreservesPitch = false; } catch (e) {}
        function draw() { ctx.drawImage(video, 0, 0); if (!video.ended) requestAnimationFrame(draw); else if (rec.state !== "inactive") rec.stop(); }
        video.play().then(function () { requestAnimationFrame(draw); }).catch(function () { busy = false; document.getElementById("vsGo").disabled = false; showMsg("Playback was blocked. Press play first, then record.", false); if (rec.state !== "inactive") rec.stop(); });
        video.onended = function () { setTimeout(function () { if (rec.state !== "inactive") rec.stop(); }, 300); };
    });

})();
</script>
@endsection
