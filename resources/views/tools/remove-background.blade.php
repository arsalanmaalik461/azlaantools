@extends('layouts.app')
@section('title', 'Remove Background — AI Background Remover Free | Azlaan Tools')
@section('meta_description', 'Remove image background free in your browser with AI. Download transparent PNG or add a white or colour background. No signup, photo never leaves your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Remove Background</h1>
            <p class="lead text-muted">Remove the background from a photo with AI, right in your browser. Download a transparent PNG, or put your photo on a white or coloured background.</p>

            <div class="alert alert-warning" role="alert">
                <strong>Please read first:</strong> This tool uses a real AI model that runs in your browser. The model is <strong>large (tens of MB) and downloads on your first photo</strong>, then it is cached on your device. Best on Wi-Fi or a computer. On mobile data it may use a lot of data and take a while. Nothing downloads until you choose a photo.
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="startWrap" class="text-center py-3">
                        <p class="text-muted">When you are ready, open the tool and choose your photo. The AI model itself downloads when your first photo is processed.</p>
                        <button type="button" id="startBtn" class="btn btn-primary btn-lg">Start — Open Background Remover</button>
                        <div id="loadStatus" class="mt-3 d-none">
                            <div class="progress"><div id="loadBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 30%"></div></div>
                            <p class="small text-muted mt-2 mb-0">Loading the removal library…</p>
                        </div>
                    </div>

                    <div id="toolWrap" class="d-none">
                        <label class="form-label fw-semibold" for="fileInput">Choose a photo</label>
                        <input type="file" id="fileInput" class="form-control form-control-lg" accept="image/*">
                        <div id="dropZone" class="border rounded-3 p-4 text-center bg-light mt-3" style="border-style: dashed !important; cursor: pointer;">
                            <p class="mb-0 fw-semibold">Drag &amp; drop your photo here, or click to browse</p>
                        </div>
                        <div class="mt-3 d-none" id="workWrap">
                            <div class="progress mb-2"><div id="workBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 50%"></div></div>
                            <p class="small text-muted mb-0" id="workText">Removing background… please keep this tab open.</p>
                        </div>
                        <div id="resultWrap" class="d-none mt-4">
                            <p class="fw-semibold mb-2">Result (checkerboard = transparent)</p>
                            <div class="text-center rounded p-2" id="previewBg" style="background-image: conic-gradient(#ddd 25%, #fff 0 50%, #ddd 0 75%, #fff 0); background-size: 24px 24px;">
                                <img id="resultImg" class="img-fluid" alt="Background removed result" style="max-height: 420px;">
                            </div>
                            <div class="row g-3 mt-2 align-items-end">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold" for="bgSelect">Background for download</label>
                                    <select id="bgSelect" class="form-select">
                                        <option value="transparent" selected>Transparent (PNG)</option>
                                        <option value="white">White</option>
                                        <option value="color">Custom colour</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold" for="bgColor">Colour</label>
                                    <input type="color" id="bgColor" class="form-control form-control-color w-100" value="#2563eb">
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" id="downloadBtn" class="btn btn-success btn-lg w-100">Download</button>
                                </div>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Transparent download is PNG. White / colour downloads are also PNG so quality stays perfect.</p>
                        </div>
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                </div>
            </div>

            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Press <strong>Start</strong> to open the tool, then choose a photo — the AI model downloads at that point (only once — it is cached after that).</li>
                <li>Choose or drag in a photo — clear photos with one main person or object work best.</li>
                <li>Wait while the AI removes the background, then check the checkerboard preview.</li>
                <li>Keep it transparent, or choose white / a custom colour, then press <strong>Download</strong>.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photo never leaves your browser. The AI runs on your own device — nothing is uploaded to any server.</div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="module">
var startBtn = document.getElementById('startBtn');
var startWrap = document.getElementById('startWrap');
var loadStatus = document.getElementById('loadStatus');
var toolWrap = document.getElementById('toolWrap');
var fileInput = document.getElementById('fileInput');
var dropZone = document.getElementById('dropZone');
var workWrap = document.getElementById('workWrap');
var workBar = document.getElementById('workBar');
var workText = document.getElementById('workText');
var resultWrap = document.getElementById('resultWrap');
var resultImg = document.getElementById('resultImg');
var previewBg = document.getElementById('previewBg');
var errorBox = document.getElementById('errorBox');
var bgSelect = document.getElementById('bgSelect');
var bgColor = document.getElementById('bgColor');
var resultBlob = null;
var resultUrl = null;
var libPromise = null;
var busy = false;
function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
function hideError() { errorBox.classList.add('d-none'); }
function loadLib() {
    if (!libPromise) {
        // Dynamic import (not a static top-level import) so a CDN failure shows an
        // error message instead of leaving a dead Start button with no feedback.
        libPromise = import('https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.5/+esm').catch(function (err) {
            libPromise = null;
            throw err;
        });
    }
    return libPromise;
}
startBtn.addEventListener('click', function () {
    hideError();
    startBtn.disabled = true;
    loadStatus.classList.remove('d-none');
    loadLib().then(function () {
        startWrap.classList.add('d-none');
        toolWrap.classList.remove('d-none');
    }).catch(function () {
        loadStatus.classList.add('d-none');
        startBtn.disabled = false;
        showError('The background-removal library could not be loaded. Check your internet connection and press Start again.');
    });
});
function processFile(file) {
    if (!file || busy) return;
    if (!file.type || file.type.indexOf('image/') !== 0) { showError('Please choose an image file (JPG or PNG works best).'); return; }
    hideError();
    busy = true;
    fileInput.disabled = true;
    resultWrap.classList.add('d-none');
    workBar.style.width = '5%';
    workText.textContent = 'Downloading the AI model (first time only) and removing the background… please keep this tab open.';
    workWrap.classList.remove('d-none');
    loadLib().then(function (lib) {
        return lib.removeBackground(file, {
            output: { format: 'image/png', quality: 1 },
            progress: function (key, current, total) {
                if (total > 0) {
                    var pct = Math.max(5, Math.min(95, Math.round(current / total * 100)));
                    workBar.style.width = pct + '%';
                    workText.textContent = 'Working… ' + pct + '% — please keep this tab open.';
                }
            }
        });
    }).then(function (blob) {
        resultBlob = blob;
        if (resultUrl) URL.revokeObjectURL(resultUrl);
        resultUrl = URL.createObjectURL(blob);
        resultImg.src = resultUrl;
        workWrap.classList.add('d-none');
        resultWrap.classList.remove('d-none');
        updatePreviewBg();
    }).catch(function (err) {
        workWrap.classList.add('d-none');
        showError('Background removal failed. Try a smaller, clear JPG or PNG photo, on Wi-Fi or a computer. Detail: ' + (err && err.message ? err.message : 'unknown error'));
    }).then(function () {
        busy = false;
        fileInput.disabled = false;
        fileInput.value = '';
    });
}
fileInput.addEventListener('change', function () { processFile(fileInput.files && fileInput.files[0]); });
dropZone.addEventListener('click', function () { fileInput.click(); });
dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer && e.dataTransfer.files.length) processFile(e.dataTransfer.files[0]); });
function updatePreviewBg() {
    if (bgSelect.value === 'white') { previewBg.style.backgroundImage = 'none'; previewBg.style.backgroundColor = '#ffffff'; }
    else if (bgSelect.value === 'color') { previewBg.style.backgroundImage = 'none'; previewBg.style.backgroundColor = bgColor.value; }
    else { previewBg.style.backgroundImage = 'conic-gradient(#ddd 25%, #fff 0 50%, #ddd 0 75%, #fff 0)'; previewBg.style.backgroundColor = 'transparent'; }
}
bgSelect.addEventListener('change', updatePreviewBg);
bgColor.addEventListener('input', updatePreviewBg);
document.getElementById('downloadBtn').addEventListener('click', function () {
    if (!resultBlob) return;
    if (bgSelect.value === 'transparent') {
        var a = document.createElement('a'); a.href = resultUrl; a.download = 'no-background.png'; document.body.appendChild(a); a.click(); a.remove(); return;
    }
    var img = new Image();
    img.onload = function () {
        var c = document.createElement('canvas'); c.width = img.naturalWidth; c.height = img.naturalHeight;
        var ctx = c.getContext('2d');
        ctx.fillStyle = bgSelect.value === 'white' ? '#ffffff' : bgColor.value;
        ctx.fillRect(0, 0, c.width, c.height);
        ctx.drawImage(img, 0, 0);
        c.toBlob(function (b) {
            var u = URL.createObjectURL(b); var a2 = document.createElement('a'); a2.href = u; a2.download = 'photo-with-background.png'; document.body.appendChild(a2); a2.click(); a2.remove(); setTimeout(function () { URL.revokeObjectURL(u); }, 3000);
        }, 'image/png');
    };
    img.src = resultUrl;
});
</script>
@endsection
