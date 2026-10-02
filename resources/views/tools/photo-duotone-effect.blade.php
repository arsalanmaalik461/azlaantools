@extends('layouts.app')

@section('title', 'Photo Duotone Effect Online Free - Azlaan Tools')
@section('meta_description', 'Apply a duotone color effect to any photo online free. Stylish two-tone poster look, download the result as PNG.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Photo Duotone Effect</h1>
            <p class="lead text-muted">Add a duotone (two-colour) effect to your photo — a stylish, poster-like look. Upload your photo, pick your colours and download the result.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imgInput" class="form-label fw-semibold">Upload a photo</label>
                        <input type="file" class="form-control" id="imgInput" accept="image/*">
                    </div>

                    <div class="mb-3 d-none" id="controlsWrap">
                        <label class="form-label fw-semibold">Preset styles</label>
                        <div class="d-flex flex-wrap gap-2 mb-3" id="presetRow"></div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="darkColor" class="form-label fw-semibold">Dark tone (shadows)</label>
                                <input type="color" class="form-control form-control-color w-100" id="darkColor" value="#1a1a2e">
                            </div>
                            <div class="col-6">
                                <label for="lightColor" class="form-label fw-semibold">Light tone (highlights)</label>
                                <input type="color" class="form-control form-control-color w-100" id="lightColor" value="#f4a259">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contrastRange" class="form-label fw-semibold">Contrast <span class="text-muted" id="contrastVal">100%</span></label>
                            <input type="range" class="form-range" id="contrastRange" min="50" max="200" value="100">
                        </div>

                        <button type="button" class="btn btn-primary w-100 mb-2" id="applyBtn">Apply Effect</button>
                        <button type="button" class="btn btn-success w-100" id="downloadBtn">Download PNG</button>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div class="mt-4 d-none" id="previewWrap">
                        <label class="form-label fw-semibold">Preview</label>
                        <canvas id="photoCanvas" class="img-fluid w-100 rounded border"></canvas>
                    </div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Upload your photo.</li>
                <li>Pick a preset style or set your own colours, then adjust the contrast.</li>
                <li>Press "Apply Effect" and save the result with "Download PNG".</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var imgInput = document.getElementById('imgInput');
    var controlsWrap = document.getElementById('controlsWrap');
    var presetRow = document.getElementById('presetRow');
    var darkColor = document.getElementById('darkColor');
    var lightColor = document.getElementById('lightColor');
    var contrastRange = document.getElementById('contrastRange');
    var contrastVal = document.getElementById('contrastVal');
    var applyBtn = document.getElementById('applyBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var errorBox = document.getElementById('errorBox');
    var previewWrap = document.getElementById('previewWrap');
    var canvas = document.getElementById('photoCanvas');
    var ctx = canvas.getContext('2d');
    var originalImg = null;

    var PRESETS = [
        { name: 'Sunset', dark: '#1a1a2e', light: '#f4a259' },
        { name: 'Ocean', dark: '#0b2545', light: '#5bc0eb' },
        { name: 'Forest', dark: '#1b3a2d', light: '#a8e6a1' },
        { name: 'Rose', dark: '#3d0c3d', light: '#ff8fab' },
        { name: 'Mono', dark: '#111111', light: '#f5f5f5' },
        { name: 'Vintage', dark: '#3a2e1f', light: '#e8c87e' },
        { name: 'Neon', dark: '#0d0221', light: '#00f5d4' },
        { name: 'Royal', dark: '#2b2d42', light: '#ffd166' }
    ];

    PRESETS.forEach(function (p) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'btn btn-sm btn-outline-secondary';
        b.textContent = p.name;
        b.style.background = 'linear-gradient(135deg,' + p.dark + ' 50%,' + p.light + ' 50%)';
        b.style.color = '#fff';
        b.title = p.name;
        b.addEventListener('click', function () {
            darkColor.value = p.dark;
            lightColor.value = p.light;
            applyEffect();
        });
        presetRow.appendChild(b);
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function hexToRgb(hex) {
        return {
            r: parseInt(hex.slice(1, 3), 16),
            g: parseInt(hex.slice(3, 5), 16),
            b: parseInt(hex.slice(5, 7), 16)
        };
    }

    imgInput.addEventListener('change', function () {
        hideError();
        var f = imgInput.files[0];
        if (!f) return;
        if (!/^image\//.test(f.type)) {
            showError('Please select an image file.');
            imgInput.value = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                originalImg = img;
                controlsWrap.classList.remove('d-none');
                previewWrap.classList.remove('d-none');
                applyEffect();
            };
            img.onerror = function () {
                showError('Could not load the image.');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(f);
    });

    contrastRange.addEventListener('input', function () {
        contrastVal.textContent = contrastRange.value + '%';
        applyEffect();
    });
    darkColor.addEventListener('input', applyEffect);
    lightColor.addEventListener('input', applyEffect);

    function applyEffect() {
        if (!originalImg) return;
        var maxW = 1600;
        var scale = Math.min(1, maxW / originalImg.width);
        canvas.width = Math.round(originalImg.width * scale);
        canvas.height = Math.round(originalImg.height * scale);
        ctx.drawImage(originalImg, 0, 0, canvas.width, canvas.height);

        var imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var data = imgData.data;
        var dark = hexToRgb(darkColor.value);
        var light = hexToRgb(lightColor.value);
        var contrast = parseInt(contrastRange.value, 10) / 100;

        for (var i = 0; i < data.length; i += 4) {
            var gray = (0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2]) / 255;
            gray = Math.min(1, Math.max(0, ((gray - 0.5) * contrast) + 0.5));
            data[i] = Math.round(dark.r + (light.r - dark.r) * gray);
            data[i + 1] = Math.round(dark.g + (light.g - dark.g) * gray);
            data[i + 2] = Math.round(dark.b + (light.b - dark.b) * gray);
        }
        ctx.putImageData(imgData, 0, 0);
    }

    applyBtn.addEventListener('click', function () {
        hideError();
        if (!originalImg) { showError('Please upload a photo first.'); return; }
        applyEffect();
    });

    downloadBtn.addEventListener('click', function () {
        hideError();
        if (!originalImg) { showError('Please upload a photo first.'); return; }
        var a = document.createElement('a');
        a.href = canvas.toDataURL('image/png');
        a.download = 'duotone-photo.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
