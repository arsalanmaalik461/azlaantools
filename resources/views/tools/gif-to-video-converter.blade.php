@extends('layouts.app')
@section('title', 'GIF to Video Converter — Free Online Tool')
@section('meta_description', 'Convert animated GIF files to MP4 or WebM video online')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">GIF to Video Converter</h1>
            <p class="lead small text-muted">Convert an animated GIF into a real video file. The GIF plays on a canvas and is recorded to WebM, or MP4 where the browser supports it.</p>

                    <label class="form-label fw-semibold" for="gvFile">Choose an animated GIF</label>
                    <input type="file" id="gvFile" class="form-control" accept="image/gif,.gif">
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="gvDur">Record length (seconds)</label><input type="number" id="gvDur" class="form-control" value="4" min="1" max="30" step="0.5"></div>
                        <div class="col-md-4"><label class="form-label" for="gvLoops">Hint</label><input type="text" id="gvLoops" class="form-control" value="Set length to cover full loops" readonly></div>
                        <div class="col-md-4 d-flex align-items-end"><button type="button" id="gvGo" class="btn btn-primary w-100" disabled>Convert to Video</button></div>
                    </div>
                    <div class="text-center mt-3"><img id="gvImg" class="img-fluid rounded border d-none" alt="GIF preview"><canvas id="gvCanvas" class="d-none"></canvas></div>
                    <div id="gvResult" class="d-none mt-3 text-center"><video id="gvOut" class="w-100 rounded" controls style="max-height:320px"></video><div class="mt-2"><a id="gvDl" href="#" download="gif-video.webm" class="btn btn-success btn-lg">Download Video</a></div></div>
                    <p class="small text-muted mt-2 mb-0" id="gvStatus">Ready.</p>

            <div id="gvMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose an animated GIF and check the preview.</li>
                    <li>Set the record length so it covers at least one full loop of the animation.</li>
                    <li>Click convert, wait while it records in real time, then download the video.</li>
            </ol>
            <p class="small text-muted mb-0">Recording happens by playing the GIF on a canvas in real time, because browsers cannot decode GIF frames directly. Output is MP4 in browsers that support MP4 recording, otherwise WebM, which all major social platforms accept.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("gvMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    var gifImg = document.getElementById("gvImg");
    document.getElementById("gvFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        gifImg.src = URL.createObjectURL(f); gifImg.classList.remove("d-none");
        document.getElementById("gvResult").classList.add("d-none");
        document.getElementById("gvGo").disabled = false;
        document.getElementById("gvStatus").textContent = "Loaded " + f.name + ". Set the length and convert.";
    });
    document.getElementById("gvGo").addEventListener("click", function () {
        if (!gifImg.naturalWidth) { showMsg("Load a GIF first.", false); return; }
        if (typeof MediaRecorder === "undefined") { showMsg("MediaRecorder is not available in this browser.", false); return; }
        var dur = Math.max(1, Math.min(30, Number(document.getElementById("gvDur").value) || 4));
        var cv = document.getElementById("gvCanvas"); cv.width = gifImg.naturalWidth; cv.height = gifImg.naturalHeight;
        var ctx = cv.getContext("2d");
        var mime = "";
        if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported("video/mp4")) mime = "video/mp4";
        else if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported("video/webm")) mime = "video/webm";
        var rec = new MediaRecorder(cv.captureStream(30), mime ? { mimeType: mime, videoBitsPerSecond: 5000000 } : undefined), chunks = [];
        var ext = mime === "video/mp4" ? "mp4" : "webm", btn = this; btn.disabled = true;
        rec.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
        rec.onstop = function () {
            btn.disabled = false;
            var blob = new Blob(chunks, { type: mime || "video/webm" }), url = URL.createObjectURL(blob);
            document.getElementById("gvOut").src = url;
            var dlA = document.getElementById("gvDl"); dlA.href = url; dlA.setAttribute("download", "gif-video." + ext);
            document.getElementById("gvResult").classList.remove("d-none");
            document.getElementById("gvStatus").textContent = "Done. Video recorded as " + ext.toUpperCase() + " (" + Math.round(blob.size / 1024) + " KB).";
        };
        var start = performance.now();
        document.getElementById("gvStatus").textContent = "Recording " + dur + "s...";
        function frame(now) {
            ctx.drawImage(gifImg, 0, 0);
            if (now - start < dur * 1000) requestAnimationFrame(frame); else rec.stop();
        }
        rec.start(200); requestAnimationFrame(frame);
    });

})();
</script>
@endsection
