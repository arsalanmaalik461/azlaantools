@extends('layouts.app')
@section('title', 'Rotate Video — Free Online Tool')
@section('meta_description', 'Rotate or flip a video by 90 180 or 270 degrees and save it')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Rotate Video</h1>
            <p class="lead small text-muted">Fix sideways phone videos: rotate by 90, 180 or 270 degrees or flip them, and save the corrected video.</p>

                    <label class="form-label fw-semibold" for="rvFile">Choose a video</label>
                    <input type="file" id="rvFile" class="form-control" accept="video/*">
                    <div class="text-center mt-3"><video id="rvVideo" class="w-100 rounded d-none" controls playsinline muted style="max-height:300px"></video></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="rvDeg">Rotation</label><select id="rvDeg" class="form-select"><option value="0">None (0 degrees)</option><option value="90">90 degrees clockwise</option><option value="180">180 degrees</option><option value="270">270 degrees clockwise</option></select></div>
                        <div class="col-md-4"><div class="form-check mt-4"><input type="checkbox" id="rvFlipH" class="form-check-input"><label class="form-check-label" for="rvFlipH">Flip horizontal</label></div></div>
                        <div class="col-md-4"><div class="form-check mt-4"><input type="checkbox" id="rvFlipV" class="form-check-input"><label class="form-check-label" for="rvFlipV">Flip vertical</label></div></div>
                    </div>
                    <button type="button" id="rvGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Rotate and Record Video</button>

                    <div id="rvResult" class="d-none mt-3 text-center"><video id="rvOut" class="w-100 rounded" controls style="max-height:320px"></video><div class="mt-2"><a id="rvDl" href="#" class="btn btn-success btn-lg">Download Video</a></div></div>
                    <p class="small text-muted mt-2 mb-0" id="rvStatus">Ready. Recording happens in real time while the video plays.</p>

            <div id="rvMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a video that was recorded sideways or upside down.</li>
                    <li>Pick the rotation and any flips.</li>
                    <li>Click the button. The corrected video is recorded while it plays through once, then you can download it.</li>
            </ol>
            <p class="small text-muted mb-0">Recording is in real time, so a 2 minute video takes about 2 minutes. Audio is kept when the browser allows capturing it. Output is MP4 where supported, otherwise WebM.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("rvMsg");
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

    var video = document.getElementById("rvVideo"), busy = false;
    document.getElementById("rvFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        video.src = URL.createObjectURL(f); video.classList.remove("d-none");
        document.getElementById("rvGo").disabled = false;
        document.getElementById("rvResult").classList.add("d-none");
        showMsg("Loaded " + f.name + ". Choose rotation and record.");
    });
    document.getElementById("rvGo").addEventListener("click", function () {
        if (busy || !video.videoWidth) return;
        var mime = pickMime(); if (mime === null) { showMsg("MediaRecorder is not available in this browser.", false); return; }
        var deg = Number(document.getElementById("rvDeg").value), swap = deg === 90 || deg === 270;
        var cv = document.createElement("canvas");
        cv.width = swap ? video.videoHeight : video.videoWidth; cv.height = swap ? video.videoWidth : video.videoHeight;
        var ctx = cv.getContext("2d");
        busy = true; this.disabled = true;
        var status = document.getElementById("rvStatus"); status.textContent = "Recording rotated video... keep this tab open.";
        var rec = recordStream(combinedStream(video, cv), mime, function (blob, ext) {
            busy = false; document.getElementById("rvGo").disabled = false;
            showResult(blob, ext, document.getElementById("rvOut"), document.getElementById("rvDl"), document.getElementById("rvResult"), "rotated-video");
            status.textContent = "Done. Preview the rotated video and download it.";
        });
        video.currentTime = 0; video.muted = false;
        function draw() {
            if (video.ended || video.paused && !busy) { }
            ctx.save();
            ctx.translate(cv.width / 2, cv.height / 2);
            ctx.rotate(deg * Math.PI / 180);
            ctx.scale(document.getElementById("rvFlipH").checked ? -1 : 1, document.getElementById("rvFlipV").checked ? -1 : 1);
            ctx.drawImage(video, -video.videoWidth / 2, -video.videoHeight / 2);
            ctx.restore();
            if (!video.ended) requestAnimationFrame(draw); else if (rec.state !== "inactive") rec.stop();
        }
        video.play().then(function () { requestAnimationFrame(draw); }).catch(function () { busy = false; document.getElementById("rvGo").disabled = false; showMsg("Playback was blocked. Press play on the video first, then record.", false); if (rec.state !== "inactive") rec.stop(); });
        video.onended = function () { setTimeout(function () { if (rec.state !== "inactive") rec.stop(); }, 300); };
    });

})();
</script>
@endsection
