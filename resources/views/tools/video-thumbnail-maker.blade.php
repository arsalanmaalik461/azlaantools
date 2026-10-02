@extends('layouts.app')
@section('title', 'Video Thumbnail Generator — Free Online Tool')
@section('meta_description', 'Capture a perfect thumbnail frame from any video at any second')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Video Thumbnail Generator</h1>
            <p class="lead small text-muted">Jump to any second of a video, capture the exact frame, and download it as a JPG or PNG thumbnail.</p>

                    <label class="form-label fw-semibold" for="vtFile">Choose a video</label>
                    <input type="file" id="vtFile" class="form-control" accept="video/*">
                    <div class="text-center mt-3"><video id="vtVideo" class="w-100 rounded d-none" controls playsinline muted style="max-height:320px"></video></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="vtTime">Frame time (seconds)</label><input type="number" id="vtTime" class="form-control" value="1" min="0" step="0.1"></div>
                        <div class="col-md-4"><label class="form-label" for="vtFormat">Format</label><select id="vtFormat" class="form-select"><option value="image/jpeg">JPG</option><option value="image/png">PNG</option></select></div>
                        <div class="col-md-4 d-flex align-items-end gap-2"><button type="button" id="vtGrab" class="btn btn-primary flex-grow-1" disabled>Grab Frame</button><button type="button" id="vtCurrent" class="btn btn-outline-secondary" disabled>Use Current Frame</button></div>
                    </div>
                    <div class="text-center mt-3"><img id="vtImg" class="img-fluid rounded border d-none" alt="Captured frame"></div>
                    <button type="button" id="vtGo" class="btn btn-success btn-lg w-100 mt-3 d-none">Download Thumbnail</button>
                    <p class="small text-muted mt-2 mb-0" id="vtInfo">No video loaded yet.</p>

            <div id="vtMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a video.</li>
                    <li>Scrub the player or type an exact time, then press Grab Frame.</li>
                    <li>Download the captured frame at full video resolution.</li>
            </ol>
            <p class="small text-muted mb-0">The capture is taken at the native resolution of the video file, not the smaller preview size, so it works well as a YouTube or blog thumbnail base.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("vtMsg");
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

    var video = document.getElementById("vtVideo"), lastBlob = null;
    document.getElementById("vtFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        video.src = URL.createObjectURL(f); video.classList.remove("d-none");
        video.addEventListener("loadedmetadata", function once() {
            video.removeEventListener("loadedmetadata", once);
            document.getElementById("vtTime").max = video.duration;
            document.getElementById("vtInfo").textContent = "Length " + video.duration.toFixed(1) + "s · " + video.videoWidth + " x " + video.videoHeight + ".";
            ["vtGrab", "vtCurrent"].forEach(function (id) { document.getElementById(id).disabled = false; });
            showMsg("Loaded " + f.name + ".");
        });
    });
    function capture() {
        if (!video.videoWidth) { showMsg("Load a video first.", false); return; }
        var cv = document.createElement("canvas"); cv.width = video.videoWidth; cv.height = video.videoHeight;
        cv.getContext("2d").drawImage(video, 0, 0);
        var fmt = document.getElementById("vtFormat").value;
        cv.toBlob(function (b) {
            if (!b) { showMsg("Capture failed. Try a different time.", false); return; }
            lastBlob = b;
            var im = document.getElementById("vtImg"); im.src = URL.createObjectURL(b); im.classList.remove("d-none");
            var go = document.getElementById("vtGo"); go.classList.remove("d-none");
            go.textContent = "Download Thumbnail (" + cv.width + " x " + cv.height + ", " + fmtBytes(b.size) + ")";
            showMsg("Frame captured at " + video.currentTime.toFixed(1) + "s.");
        }, fmt, 0.92);
    }
    function seekThenCapture(t) {
        var done = false;
        function fin() { if (done) return; done = true; video.removeEventListener("seeked", fin); capture(); }
        video.addEventListener("seeked", fin);
        video.currentTime = Math.min(Math.max(0, t), Math.max(0, (video.duration || t) - 0.05));
        setTimeout(fin, 1500);
    }
    document.getElementById("vtGrab").addEventListener("click", function () { seekThenCapture(Number(document.getElementById("vtTime").value) || 0); });
    document.getElementById("vtCurrent").addEventListener("click", capture);
    document.getElementById("vtGo").addEventListener("click", function () {
        if (!lastBlob) return;
        dl(lastBlob, "video-thumbnail." + (document.getElementById("vtFormat").value === "image/png" ? "png" : "jpg"));
    });

})();
</script>
@endsection
