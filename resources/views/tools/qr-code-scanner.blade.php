@extends('layouts.app')

@section('title', 'QR Code Scanner - Azlaan Tools')
@section('meta_description', 'Scan any QR code with your camera or from a photo. Free online QR reader, no app needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">QR Code Scanner</h1>
            <p class="lead text-muted">Scan a QR code with your camera or by uploading a photo. No app needed — everything works right in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-primary" id="startCamBtn">Scan with Camera</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="stopCamBtn">Stop Camera</button>
                    </div>

                    <div class="text-center text-muted small mb-2">— or —</div>

                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">Upload a photo with a QR code</label>
                        <input type="file" class="form-control" id="fileInput" accept="image/*">
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="camWrap" class="d-none mt-3">
                        <video id="camVideo" class="w-100 border rounded" playsinline muted></video>
                        <p class="small text-muted mt-1">Hold the QR code in front of the camera — it will be read automatically.</p>
                    </div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Scan result</h2>
                        <div class="border rounded p-3 bg-light mb-2">
                            <p class="mb-0" id="scanText" style="word-break: break-all;"></p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="copyBtn">Copy Text</button>
                            <a href="#" class="btn btn-sm btn-outline-success d-none" id="openLinkBtn" target="_blank" rel="noopener">Open Link</a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="scanAgainBtn">Scan Again</button>
                        </div>
                        <div class="alert alert-success d-none py-2 mt-2" id="copiedMsg" role="status">Copied!</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Press "Scan with Camera" and allow camera access, then hold the QR code in front of it.</li>
                <li>Or upload a photo that has a QR code.</li>
                <li>Copy the result, or open it if it is a link.</li>
            </ol>
            <p class="small text-muted">Privacy: scanning happens on your device — your photo is never uploaded anywhere. Be careful before opening links from unknown QR codes.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var startCamBtn = document.getElementById('startCamBtn');
    var stopCamBtn = document.getElementById('stopCamBtn');
    var fileInput = document.getElementById('fileInput');
    var errorBox = document.getElementById('errorBox');
    var camWrap = document.getElementById('camWrap');
    var camVideo = document.getElementById('camVideo');
    var results = document.getElementById('results');
    var scanText = document.getElementById('scanText');
    var copyBtn = document.getElementById('copyBtn');
    var openLinkBtn = document.getElementById('openLinkBtn');
    var scanAgainBtn = document.getElementById('scanAgainBtn');
    var copiedMsg = document.getElementById('copiedMsg');

    var stream = null;
    var scanning = false;
    var lastScan = 0;
    var detector = null;
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function detectorReady() {
        if (detector) return true;
        if (typeof BarcodeDetector === 'undefined') return false;
        try {
            detector = new BarcodeDetector({ formats: ['qr_code'] });
            return true;
        } catch (e) {
            try {
                detector = new BarcodeDetector();
                return true;
            } catch (e2) { return false; }
        }
    }

    function looksLikeUrl(t) {
        return /^(https?:\/\/|www\.)/i.test(t.trim());
    }

    function showResult(text) {
        stopScanning();
        scanText.textContent = text;
        var url = text.trim();
        if (looksLikeUrl(url)) {
            openLinkBtn.href = /^https?:\/\//i.test(url) ? url : 'https://' + url;
            openLinkBtn.classList.remove('d-none');
        } else {
            openLinkBtn.classList.add('d-none');
        }
        results.classList.remove('d-none');
    }

    function scanFrame() {
        if (!scanning || !detector) return;
        var now = Date.now();
        if (now - lastScan > 350 && camVideo.videoWidth) {
            lastScan = now;
            canvas.width = camVideo.videoWidth;
            canvas.height = camVideo.videoHeight;
            ctx.drawImage(camVideo, 0, 0, canvas.width, canvas.height);
            detector.detect(canvas).then(function (codes) {
                if (codes && codes.length && codes[0].rawValue) {
                    showResult(codes[0].rawValue);
                } else if (scanning) {
                    requestAnimationFrame(scanFrame);
                }
            }).catch(function () {
                if (scanning) requestAnimationFrame(scanFrame);
            });
            return;
        }
        requestAnimationFrame(scanFrame);
    }

    function stopScanning() {
        scanning = false;
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
        camVideo.pause();
        camVideo.srcObject = null;
        camWrap.classList.add('d-none');
        startCamBtn.classList.remove('d-none');
        stopCamBtn.classList.add('d-none');
    }

    startCamBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        if (!detectorReady()) {
            showError('Sorry, your browser does not support QR scanning. Use Chrome or Edge (latest), or try the photo upload option.');
            return;
        }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Camera access is not available on this device or browser. Use the photo upload option.');
            return;
        }
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
            .then(function (s) {
                stream = s;
                camVideo.srcObject = stream;
                camWrap.classList.remove('d-none');
                startCamBtn.classList.add('d-none');
                stopCamBtn.classList.remove('d-none');
                return camVideo.play();
            })
            .then(function () {
                scanning = true;
                lastScan = 0;
                requestAnimationFrame(scanFrame);
            })
            .catch(function (err) {
                showError('Could not open the camera: ' + (err && err.message ? err.message : 'permission was not given') + '. Try the photo upload option.');
            });
    });

    stopCamBtn.addEventListener('click', stopScanning);

    fileInput.addEventListener('change', function () {
        hideError();
        var f = fileInput.files[0];
        if (!f) return;
        if (!detectorReady()) {
            showError('Sorry, your browser does not support QR scanning. Use Chrome or Edge (latest).');
            return;
        }
        var img = new Image();
        var url = URL.createObjectURL(f);
        img.onload = function () {
            URL.revokeObjectURL(url);
            var maxDim = 1200;
            var scale = Math.min(1, maxDim / Math.max(img.width, img.height));
            canvas.width = Math.round(img.width * scale);
            canvas.height = Math.round(img.height * scale);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            detector.detect(canvas).then(function (codes) {
                if (codes && codes.length && codes[0].rawValue) {
                    showResult(codes[0].rawValue);
                } else {
                    showError('No QR code found in this photo. Try a clear, straight photo.');
                }
            }).catch(function (err) {
                showError('Scan failed: ' + (err && err.message ? err.message : 'unknown error'));
            });
            fileInput.value = '';
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            showError('Photo could not be loaded.');
        };
        img.src = url;
    });

    copyBtn.addEventListener('click', function () {
        var text = scanText.textContent;
        function done() {
            copiedMsg.classList.remove('d-none');
            setTimeout(function () { copiedMsg.classList.add('d-none'); }, 2000);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, done);
        } else { done(); }
    });

    scanAgainBtn.addEventListener('click', function () {
        results.classList.add('d-none');
        scanText.textContent = '';
    });
})();
</script>
@endsection
