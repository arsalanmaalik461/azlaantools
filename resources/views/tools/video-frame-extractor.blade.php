@extends('layouts.app')
@section('title', 'Video Frame Extractor — Free Online Tool')
@section('meta_description', 'Extract frames from a video as JPG or PNG images at set intervals')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Video Frame Extractor</h1>
            <p class="lead small text-muted">Extract still frames from a video every few seconds and download them as JPG or PNG images, singly or as a ZIP.</p>

                    <label class="form-label fw-semibold" for="vfFile">Choose a video</label>
                    <input type="file" id="vfFile" class="form-control" accept="video/*">
                    <video id="vfVideo" class="d-none" playsinline muted></video>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3"><label class="form-label" for="vfEvery">One frame every (seconds)</label><input type="number" id="vfEvery" class="form-control" value="2" min="0.5" step="0.5"></div>
                        <div class="col-md-3"><label class="form-label" for="vfFormat">Format</label><select id="vfFormat" class="form-select"><option value="image/jpeg">JPG</option><option value="image/png">PNG</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="vfMax">Max frames</label><input type="number" id="vfMax" class="form-control" value="30" min="1" max="120"></div>
                        <div class="col-md-3 d-flex align-items-end"><button type="button" id="vfGo" class="btn btn-primary w-100" disabled>Extract Frames</button></div>
                    </div>
                    <div class="d-flex gap-2 mt-3"><button type="button" id="vfZip" class="btn btn-success d-none">Download All as ZIP</button></div>
                    <div id="vfGrid" class="row g-3 mt-2"></div>

            <div id="vfMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a video.</li>
                    <li>Set how often to grab a frame and the image format.</li>
                    <li>Click extract, then download frames one by one or all together as a ZIP.</li>
            </ol>
            <p class="small text-muted mb-0">Frames are captured at the full resolution of the video. Long videos with small intervals are capped by the Max frames setting to protect your device memory.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("vfMsg");
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

    var video = document.getElementById("vfVideo"), frameBlobs = [];
    document.getElementById("vfFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        video.src = URL.createObjectURL(f);
        document.getElementById("vfGrid").innerHTML = ""; frameBlobs = [];
        document.getElementById("vfZip").classList.add("d-none");
        video.addEventListener("loadedmetadata", function once() { video.removeEventListener("loadedmetadata", once); document.getElementById("vfGo").disabled = false; showMsg("Loaded " + f.name + " (" + video.duration.toFixed(1) + "s, " + video.videoWidth + " x " + video.videoHeight + ")."); });
    });
    function seekTo(t) {
        return new Promise(function (resolve) {
            var done = false;
            function fin() { if (done) return; done = true; video.removeEventListener("seeked", fin); resolve(); }
            video.addEventListener("seeked", fin);
            video.currentTime = Math.min(t, Math.max(0, (video.duration || t) - 0.05));
            setTimeout(fin, 1500);
        });
    }
    document.getElementById("vfGo").addEventListener("click", function () {
        if (!video.videoWidth) { showMsg("Load a video first.", false); return; }
        var every = Math.max(0.5, Number(document.getElementById("vfEvery").value) || 2);
        var maxF = Math.min(120, Math.max(1, parseInt(document.getElementById("vfMax").value, 10) || 30));
        var fmt = document.getElementById("vfFormat").value, ext = fmt === "image/png" ? "png" : "jpg";
        var grid = document.getElementById("vfGrid"); grid.innerHTML = ""; frameBlobs = [];
        var cv = document.createElement("canvas"); cv.width = video.videoWidth; cv.height = video.videoHeight;
        var ctx = cv.getContext("2d"), btn = this; btn.disabled = true;
        var times = []; for (var t = 0; t < video.duration && times.length < maxF; t += every) times.push(t);
        var i = 0;
        function step() {
            if (i >= times.length) { btn.disabled = false; if (frameBlobs.length) document.getElementById("vfZip").classList.remove("d-none"); showMsg("Extracted " + frameBlobs.length + " frames."); return; }
            var tt = times[i];
            seekTo(tt).then(function () {
                ctx.drawImage(video, 0, 0);
                cv.toBlob(function (b) {
                    if (b) {
                        var idx = frameBlobs.length; frameBlobs.push({ blob: b, name: "frame-" + (idx + 1) + "-at-" + tt.toFixed(1) + "s." + ext });
                        var col = document.createElement("div"); col.className = "col-6 col-md-3";
                        var im = document.createElement("img"); im.className = "img-fluid rounded border"; im.src = URL.createObjectURL(b); im.alt = "Frame at " + tt.toFixed(1) + " seconds";
                        var a = document.createElement("a"); a.className = "btn btn-sm btn-outline-primary w-100 mt-1"; a.textContent = "Frame " + (idx + 1) + " · " + tt.toFixed(1) + "s"; a.href = im.src; a.setAttribute("download", frameBlobs[idx].name);
                        col.appendChild(im); col.appendChild(a); grid.appendChild(col);
                    }
                    i++; showMsg("Capturing frame " + i + " of " + times.length + "..."); step();
                }, fmt, 0.92);
            });
        }
        step();
    });
    document.getElementById("vfZip").addEventListener("click", function () {
        if (typeof JSZip === "undefined") { showMsg("ZIP library failed to load, but single frame downloads still work.", false); return; }
        var zip = new JSZip();
        frameBlobs.forEach(function (f) { zip.file(f.name, f.blob); });
        zip.generateAsync({ type: "blob" }).then(function (b) { dl(b, "video-frames.zip"); showMsg("ZIP downloaded with " + frameBlobs.length + " frames."); });
    });

})();
</script>
@endsection
