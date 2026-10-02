@extends('layouts.app')

@section('title', 'Webcam Photo Booth - Azlaan Tools')
@section('meta_description', 'Take photos with your webcam, apply fun filters and download them. Free online photo booth - no upload, everything stays in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Webcam Photo Booth</h1>
            <p class="lead text-muted">Take photos with your webcam, apply filters and download them. No upload — everything happens in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <video id="video" class="img-fluid rounded border" playsinline muted autoplay style="max-height:380px; background:#e9ecef; transform:scaleX(-1);"></video>
                        <div class="form-text mt-2" id="camStatus">The camera is not started yet.</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                        <button type="button" class="btn btn-primary" id="startBtn">Start Camera</button>
                        <button type="button" class="btn btn-outline-secondary" id="stopBtn" disabled>Stop Camera</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter</label>
                        <div class="d-flex flex-wrap gap-2" id="filterRow">
                            <button type="button" class="btn btn-sm btn-dark filter-btn" data-filter="none">Normal</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="grayscale(100%)">B&amp;W</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="sepia(100%)">Sepia</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="saturate(180%) contrast(112%)">Vivid</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="sepia(40%) saturate(160%) hue-rotate(-15deg)">Warm</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="saturate(130%) hue-rotate(18deg)">Cool</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="sepia(60%) contrast(90%) brightness(95%)">Vintage</button>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="countdownSel" class="form-label fw-semibold">Countdown</label>
                            <select class="form-select" id="countdownSel">
                                <option value="0">No countdown</option>
                                <option value="3">3 seconds</option>
                                <option value="5">5 seconds</option>
                            </select>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <button type="button" class="btn btn-success w-100" id="captureBtn" disabled>Capture Photo</button>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Captured photos (<span id="photoCount">0</span>)</h2>
                        <div class="row g-2" id="gallery"></div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info"><strong>Privacy:</strong> The camera image never leaves your browser — when you take a photo it downloads straight to your device.</div>

            <h2>How to use</h2>
            <ol>
                <li>Click <strong>Start Camera</strong> and allow camera access when the browser asks.</li>
                <li>Pick a filter to preview it live (B&amp;W, Sepia, Vivid, Warm, Cool, Vintage).</li>
                <li>Optionally set a countdown, then click <strong>Capture Photo</strong>.</li>
                <li>Download any captured photo from the gallery below.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var startBtn = document.getElementById('startBtn');
    var stopBtn = document.getElementById('stopBtn');
    var captureBtn = document.getElementById('captureBtn');
    var video = document.getElementById('video');
    var camStatus = document.getElementById('camStatus');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var gallery = document.getElementById('gallery');
    var photoCount = document.getElementById('photoCount');
    var countdownSel = document.getElementById('countdownSel');
    var filterRow = document.getElementById('filterRow');

    var stream = null;
    var currentFilter = 'none';
    var count = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    startBtn.addEventListener('click', function () {
        hideError();
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Your browser does not support the camera. Please use a new version of Chrome or Firefox.');
            return;
        }
        camStatus.textContent = 'Requesting camera permission...';
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false }).then(function (s) {
            stream = s;
            video.srcObject = s;
            camStatus.textContent = 'The camera is running. Smile!';
            startBtn.disabled = true;
            stopBtn.disabled = false;
            captureBtn.disabled = false;
        }).catch(function (err) {
            var msg = 'The camera could not start.';
            if (err && err.name === 'NotAllowedError') {
                msg = 'Camera permission was denied. Click the camera icon in the browser address bar and select Allow.';
            } else if (err && (err.name === 'NotFoundError' || err.name === 'OverconstrainedError')) {
                msg = 'No camera device was found.';
            } else if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                msg = 'The camera only works on a secure (HTTPS) connection.';
            }
            showError(msg);
            camStatus.textContent = 'The camera could not start.';
        });
    });

    stopBtn.addEventListener('click', function () {
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
        video.srcObject = null;
        camStatus.textContent = 'The camera is off.';
        startBtn.disabled = false;
        stopBtn.disabled = true;
        captureBtn.disabled = true;
    });

    filterRow.addEventListener('click', function (e) {
        var btn = e.target.closest('.filter-btn');
        if (!btn) return;
        currentFilter = btn.getAttribute('data-filter');
        video.style.filter = currentFilter === 'none' ? '' : currentFilter;
        var all = filterRow.querySelectorAll('.filter-btn');
        for (var i = 0; i < all.length; i++) {
            all[i].classList.remove('btn-dark');
            all[i].classList.add('btn-outline-secondary');
        }
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-dark');
    });

    function takePhoto() {
        if (!stream) { showError('Please start the camera first.'); return; }
        var w = video.videoWidth;
        var h = video.videoHeight;
        if (!w || !h) { showError('The video is not ready yet, please wait a second and try again.'); return; }
        var canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        var ctx = canvas.getContext('2d');
        ctx.translate(w, 0);
        ctx.scale(-1, 1);
        if (currentFilter !== 'none') ctx.filter = currentFilter;
        ctx.drawImage(video, 0, 0, w, h);
        var url = canvas.toDataURL('image/png');
        count++;
        var col = document.createElement('div');
        col.className = 'col-6 col-md-4';
        var link = document.createElement('a');
        link.href = url;
        link.download = 'photo-booth-' + count + '.png';
        var img = document.createElement('img');
        img.src = url;
        img.className = 'img-fluid rounded border';
        img.alt = 'Captured photo ' + count;
        link.appendChild(img);
        var cap = document.createElement('div');
        cap.className = 'small text-center mt-1';
        var dl = document.createElement('a');
        dl.href = url;
        dl.download = 'photo-booth-' + count + '.png';
        dl.textContent = 'Download photo ' + count;
        cap.appendChild(dl);
        col.appendChild(link);
        col.appendChild(cap);
        gallery.appendChild(col);
        photoCount.textContent = count;
        results.classList.remove('d-none');
    }

    captureBtn.addEventListener('click', function () {
        hideError();
        var secs = parseInt(countdownSel.value, 10);
        if (!secs) { takePhoto(); return; }
        captureBtn.disabled = true;
        var left = secs;
        captureBtn.textContent = left + '...';
        var timer = setInterval(function () {
            left--;
            if (left <= 0) {
                clearInterval(timer);
                captureBtn.disabled = false;
                captureBtn.textContent = 'Capture Photo';
                takePhoto();
            } else {
                captureBtn.textContent = left + '...';
            }
        }, 1000);
    });
})();
</script>
@endsection
