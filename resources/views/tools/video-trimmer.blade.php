@extends('layouts.app')
@section('title', 'Video Trimmer — Free Online Tool')
@section('meta_description', 'Trim and cut video clips by start and end time without uploading')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Video Trimmer</h1>
            <p class="lead small text-muted">Trim a video by choosing start and end times. The clip is re-recorded in your browser, with nothing uploaded.</p>

                    <label class="form-label fw-semibold" for="vmFile">Choose a video</label>
                    <input type="file" id="vmFile" class="form-control" accept="video/*">
                    <div class="text-center mt-3"><video id="vmVideo" class="w-100 rounded d-none" controls playsinline style="max-height:300px"></video></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="vmStart">Start (seconds)</label><input type="number" id="vmStart" class="form-control" value="0" min="0" step="0.1"></div>
                        <div class="col-md-4"><label class="form-label" for="vmEnd">End (seconds)</label><input type="number" id="vmEnd" class="form-control" value="5" min="0" step="0.1"></div>
                        <div class="col-md-4 d-flex align-items-end"><button type="button" id="vmGo" class="btn btn-primary w-100" disabled>Trim and Record Clip</button></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="vmInfo">No video loaded yet.</p>

                    <div id="vmResult" class="d-none mt-3 text-center"><video id="vmOut" class="w-100 rounded" controls style="max-height:320px"></video><div class="mt-2"><a id="vmDl" href="#" class="btn btn-success btn-lg">Download Video</a></div></div>
                    <p class="small text-muted mt-2 mb-0" id="vmStatus">Ready. Recording happens in real time while the video plays.</p>

            <div id="vmMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a video and note its total length.</li>
                    <li>Enter the start and end time of the part you want to keep.</li>
                    <li>Click trim. Only that part is recorded and offered as a download.</li>
            </ol>
            <p class="small text-muted mb-0">Trimming re-records the selected part in real time, so a 30 second clip takes about 30 seconds. For very long source videos, short clips work best. Output is MP4 where supported, otherwise WebM, and audio is kept when the browser allows it.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("vmMsg");
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

    var video = document.getElementById("vmVideo"), busy = false;
    document.getElementById("vmFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        video.src = URL.createObjectURL(f); video.classList.remove("d-none");
        video.addEventListener("loadedmetadata", function once() {
            video.removeEventListener("loadedmetadata", once);
            document.getElementById("vmEnd").value = video.duration.toFixed(1);
            document.getElementById("vmEnd").max = video.duration; document.getElementById("vmStart").max = video.duration;
            document.getElementById("vmInfo").textContent = "Length: " + video.duration.toFixed(1) + "s · " + video.videoWidth + " x " + video.videoHeight + ".";
            document.getElementById("vmGo").disabled = false;
            showMsg("Loaded " + f.name + ". Set start and end, then trim.");
        });
    });
    document.getElementById("vmGo").addEventListener("click", function () {
        if (busy || !video.videoWidth) return;
        var start = Math.max(0, Number(document.getElementById("vmStart").value) || 0);
        var end = Number(document.getElementById("vmEnd").value) || 0;
        if (end <= start) { showMsg("End time must be greater than start time.", false); return; }
        if (end > (video.duration || 0) + 0.1) { showMsg("End time is beyond the end of the video.", false); return; }
        var mime = pickMime(); if (mime === null) { showMsg("MediaRecorder is not available in this browser.", false); return; }
        var cv = document.createElement("canvas"); cv.width = video.videoWidth; cv.height = video.videoHeight;
        var ctx = cv.getContext("2d");
        busy = true; this.disabled = true;
        var status = document.getElementById("vmStatus"); status.textContent = "Recording trimmed clip (" + (end - start).toFixed(1) + "s)...";
        var rec = null;
        function draw() { ctx.drawImage(video, 0, 0); if (!video.ended && video.currentTime < end) requestAnimationFrame(draw); }
        function finish() { if (rec && rec.state !== "inactive") rec.stop(); video.pause(); }
        video.currentTime = start;
        video.addEventListener("seeked", function onSeek() {
            video.removeEventListener("seeked", onSeek);
            rec = recordStream(combinedStream(video, cv), mime, function (blob, ext) {
                busy = false; document.getElementById("vmGo").disabled = false;
                showResult(blob, ext, document.getElementById("vmOut"), document.getElementById("vmDl"), document.getElementById("vmResult"), "trimmed-video");
                status.textContent = "Done. Preview and download your clip.";
            });
            video.play().then(function () { requestAnimationFrame(draw); }).catch(function () { busy = false; document.getElementById("vmGo").disabled = false; showMsg("Playback was blocked. Press play first, then trim.", false); finish(); });
            var watcher = setInterval(function () {
                if (!busy) { clearInterval(watcher); return; }
                if (video.currentTime >= end || video.ended) { clearInterval(watcher); finish(); }
            }, 120);
        });
    });

})();
</script>
@endsection
