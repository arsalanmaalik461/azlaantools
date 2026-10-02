@extends('layouts.app')
@section('title', 'Video Cropper — Free Online Tool')
@section('meta_description', 'Crop a video to a new aspect ratio for Reels Shorts and TikTok')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Video Cropper</h1>
            <p class="lead small text-muted">Crop any video to a vertical, square or wide aspect ratio for Reels, Shorts and TikTok, and save the cropped video.</p>

                    <label class="form-label fw-semibold" for="vcFile">Choose a video</label>
                    <input type="file" id="vcFile" class="form-control" accept="video/*">
                    <div class="text-center mt-3"><video id="vcVideo" class="w-100 rounded d-none" controls playsinline style="max-height:300px"></video></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="vcAspect">Target aspect ratio</label><select id="vcAspect" class="form-select"><option value="9,16">9:16 Vertical (Reels / Shorts / TikTok)</option><option value="1,1">1:1 Square</option><option value="4,5">4:5 Portrait post</option><option value="16,9">16:9 Wide</option><option value="3,4">3:4 Portrait</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="vcPos">Crop position</label><select id="vcPos" class="form-select"><option value="center">Center</option><option value="top">Top</option><option value="bottom">Bottom</option><option value="left">Left</option><option value="right">Right</option></select></div>
                        <div class="col-md-4 d-flex align-items-end"><button type="button" id="vcGo" class="btn btn-primary w-100" disabled>Crop and Record Video</button></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="vcInfo">The crop takes the largest area of the chosen shape from the position you pick, and scales it to at most 720 px on the long side.</p>

                    <div id="vcResult" class="d-none mt-3 text-center"><video id="vcOut" class="w-100 rounded" controls style="max-height:320px"></video><div class="mt-2"><a id="vcDl" href="#" class="btn btn-success btn-lg">Download Video</a></div></div>
                    <p class="small text-muted mt-2 mb-0" id="vcStatus">Ready. Recording happens in real time while the video plays.</p>

            <div id="vcMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a video.</li>
                    <li>Pick the aspect ratio for your platform and a crop position.</li>
                    <li>Click crop. The cropped video records while it plays through once, then download it.</li>
            </ol>
            <p class="small text-muted mb-0">Recording is in real time. Audio is kept when the browser allows capturing it. Output is MP4 where supported, otherwise WebM.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("vcMsg");
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

    var video = document.getElementById("vcVideo"), busy = false;
    document.getElementById("vcFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        video.src = URL.createObjectURL(f); video.classList.remove("d-none");
        document.getElementById("vcGo").disabled = false; document.getElementById("vcResult").classList.add("d-none");
        showMsg("Loaded " + f.name + ".");
    });
    document.getElementById("vcGo").addEventListener("click", function () {
        if (busy || !video.videoWidth) return;
        var mime = pickMime(); if (mime === null) { showMsg("MediaRecorder is not available in this browser.", false); return; }
        var parts = document.getElementById("vcAspect").value.split(","), ar = Number(parts[0]) / Number(parts[1]);
        var sw = video.videoWidth, sh = video.videoHeight, cw, ch;
        if (sw / sh > ar) { ch = sh; cw = Math.round(sh * ar); } else { cw = sw; ch = Math.round(sw / ar); }
        var pos = document.getElementById("vcPos").value;
        var sx = pos === "left" ? 0 : (pos === "right" ? sw - cw : Math.round((sw - cw) / 2));
        var sy = pos === "top" ? 0 : (pos === "bottom" ? sh - ch : Math.round((sh - ch) / 2));
        var scale = Math.min(1, 720 / Math.max(cw, ch));
        var cv = document.createElement("canvas"); cv.width = Math.max(2, Math.round(cw * scale / 2) * 2); cv.height = Math.max(2, Math.round(ch * scale / 2) * 2);
        var ctx = cv.getContext("2d");
        busy = true; this.disabled = true;
        var status = document.getElementById("vcStatus"); status.textContent = "Recording cropped video at " + cv.width + " x " + cv.height + "...";
        var rec = recordStream(combinedStream(video, cv), mime, function (blob, ext) {
            busy = false; document.getElementById("vcGo").disabled = false;
            showResult(blob, ext, document.getElementById("vcOut"), document.getElementById("vcDl"), document.getElementById("vcResult"), "cropped-video");
            status.textContent = "Done. Preview and download your cropped video.";
        });
        video.currentTime = 0;
        function draw() { ctx.drawImage(video, sx, sy, cw, ch, 0, 0, cv.width, cv.height); if (!video.ended) requestAnimationFrame(draw); else if (rec.state !== "inactive") rec.stop(); }
        video.play().then(function () { requestAnimationFrame(draw); }).catch(function () { busy = false; document.getElementById("vcGo").disabled = false; showMsg("Playback was blocked. Press play first, then crop.", false); if (rec.state !== "inactive") rec.stop(); });
        video.onended = function () { setTimeout(function () { if (rec.state !== "inactive") rec.stop(); }, 300); };
    });

})();
</script>
@endsection
