@extends('layouts.app')

@section('title', 'Favicon Generator - Create Favicons Online Free | Azlaan Tools')
@section('meta_description', 'Free favicon generator. Turn any image, letter or emoji-style text into 16x16, 32x32, 48x48 and 180x180 favicons and download them individually or as a ZIP. No signup needed.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Favicon Generator</h1>
    <p class="lead">Create favicons for your website in seconds - from an uploaded image, or from a letter with your brand colours. Download each size or grab them all in one ZIP. Free, with no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="btn-group mb-3" role="group" aria-label="Favicon mode">
                <button type="button" id="modeImageBtn" class="btn btn-primary">From Image</button>
                <button type="button" id="modeTextBtn" class="btn btn-outline-primary">From Text / Letter</button>
            </div>

            <div id="imageMode">
                <label class="form-label fw-semibold" for="fileInput">Upload Image (square works best)</label>
                <input type="file" id="fileInput" class="form-control" accept="image/*">
                <div class="form-text">Your image is centre-cropped to a square automatically.</div>
            </div>

            <div id="textMode" class="d-none">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="letterInput">Letter / Text (1-2 characters)</label>
                        <input type="text" id="letterInput" class="form-control" value="A" maxlength="2">
                        <div class="form-text">You can also paste an emoji here, e.g. a star or bolt symbol.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="bgColor">Background Colour</label>
                        <input type="color" id="bgColor" class="form-control form-control-color w-100" value="#0d6efd">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="fgColor">Text Colour</label>
                        <input type="color" id="fgColor" class="form-control form-control-color w-100" value="#ffffff">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="shapeSelect">Shape</label>
                        <select id="shapeSelect" class="form-select">
                            <option value="rounded" selected>Rounded Square</option>
                            <option value="square">Square</option>
                            <option value="circle">Circle</option>
                        </select>
                    </div>
                </div>
                <button type="button" id="generateTextBtn" class="btn btn-primary mt-3">Generate from Text</button>
            </div>
        </div>
    </div>

    <div id="resultsWrap" class="card shadow-sm mb-4 d-none">
        <div class="card-body">
            <h2 class="h5">Your Favicons</h2>
            <p class="text-muted small">Preview at actual size and at a larger view. Click a size to download it, or download everything as a ZIP.</p>
            <div class="row g-3" id="previewGrid"></div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="zipBtn" class="btn btn-success btn-lg">Download All (.zip)</button>
            </div>
            <div class="mt-3">
                <label class="form-label fw-semibold" for="htmlCode">Add this to your website &lt;head&gt;</label>
                <textarea id="htmlCode" class="form-control" rows="4" readonly></textarea>
                <button type="button" id="copyCodeBtn" class="btn btn-outline-secondary btn-sm mt-2">Copy Code</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your image never leaves your browser - favicons are generated entirely on your own device, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Choose <strong>From Image</strong> and upload a logo or photo, or choose <strong>From Text / Letter</strong> and type a letter, pick colours and click Generate.</li>
        <li>Preview the favicons in 16x16, 32x32, 48x48 and 180x180 (Apple touch icon) sizes.</li>
        <li>Download a single size by clicking its button, or click <strong>Download All (.zip)</strong> for the full set.</li>
        <li>Unzip the files into your website root folder and paste the shown HTML code into your page head.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
(function () {
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var fileInput = document.getElementById('fileInput');
    var resultsWrap = document.getElementById('resultsWrap');
    var previewGrid = document.getElementById('previewGrid');
    var htmlCode = document.getElementById('htmlCode');
    var zipBtn = document.getElementById('zipBtn');
    var sizes = [16, 32, 48, 180];
    var generated = [];
    var sourceCanvas = null;
    var sourceImg = null;
    var sourceUrl = null;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
    }
    function hideAlerts() {
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }
    function nameFor(size) {
        if (size === 180) return 'apple-touch-icon.png';
        return 'favicon-' + size + 'x' + size + '.png';
    }
    function buildCode() {
        var lines = [];
        lines.push('<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">');
        lines.push('<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">');
        lines.push('<link rel="icon" type="image/png" sizes="48x48" href="/favicon-48x48.png">');
        lines.push('<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">');
        return lines.join('\n');
    }
    function makeFaviconCanvas(src, size) {
        var c = document.createElement('canvas');
        c.width = size;
        c.height = size;
        var cx = c.getContext('2d');
        cx.imageSmoothingEnabled = true;
        cx.imageSmoothingQuality = 'high';
        if (src) {
            var sw = src.width || src.naturalWidth;
            var sh = src.height || src.naturalHeight;
            var side = Math.min(sw, sh);
            var sx = (sw - side) / 2;
            var sy = (sh - side) / 2;
            cx.drawImage(src, sx, sy, side, side, 0, 0, size, size);
        }
        return c;
    }
    function renderResults() {
        previewGrid.innerHTML = '';
        generated = [];
        var src = sourceCanvas || sourceImg;
        if (!src) return;
        sizes.forEach(function (size) {
            var c = makeFaviconCanvas(src, size);
            generated.push({
                size: size,
                canvas: c,
                name: nameFor(size)
            });
            var col = document.createElement('div');
            col.className = 'col-6 col-md-3 text-center';
            var box = document.createElement('div');
            box.className = 'border rounded p-3 bg-light h-100';
            var big = document.createElement('canvas');
            big.width = size;
            big.height = size;
            big.style.width = '64px';
            big.style.height = '64px';
            big.style.imageRendering = size <= 32 ? 'pixelated' : 'auto';
            big.getContext('2d').drawImage(c, 0, 0);
            box.appendChild(big);
            var label = document.createElement('p');
            label.className = 'mb-1 mt-2 fw-semibold small';
            label.textContent = size + ' x ' + size + (size === 180 ? ' (Apple)' : '');
            box.appendChild(label);
            var actual = document.createElement('p');
            actual.className = 'mb-2';
            var actualImg = document.createElement('img');
            actualImg.src = c.toDataURL('image/png');
            actualImg.width = Math.min(size, 48);
            actualImg.height = Math.min(size, 48);
            actualImg.alt = 'Favicon ' + size;
            actual.appendChild(actualImg);
            box.appendChild(actual);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-success';
            btn.textContent = 'Download ' + size + 'x' + size;
            btn.addEventListener('click', function () {
                c.toBlob(function (blob) {
                    if (!blob) return;
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = nameFor(size);
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    setTimeout(function () {
                        URL.revokeObjectURL(url);
                    }, 3000);
                }, 'image/png');
            });
            box.appendChild(btn);
            col.appendChild(box);
            previewGrid.appendChild(col);
        });
        htmlCode.value = buildCode();
        resultsWrap.classList.remove('d-none');
    }

    function setMode(textMode) {
        document.getElementById('imageMode').classList.toggle('d-none', textMode);
        document.getElementById('textMode').classList.toggle('d-none', !textMode);
        var ib = document.getElementById('modeImageBtn');
        var tb = document.getElementById('modeTextBtn');
        ib.className = textMode ? 'btn btn-outline-primary' : 'btn btn-primary';
        tb.className = textMode ? 'btn btn-primary' : 'btn btn-outline-primary';
    }
    document.getElementById('modeImageBtn').addEventListener('click', function () {
        setMode(false);
    });
    document.getElementById('modeTextBtn').addEventListener('click', function () {
        setMode(true);
    });

    fileInput.addEventListener('change', function () {
        hideAlerts();
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        if (!file.type || file.type.indexOf('image/') !== 0) {
            showError('Please choose an image file (JPG, PNG or WebP).');
            fileInput.value = '';
            return;
        }
        if (sourceUrl) URL.revokeObjectURL(sourceUrl);
        sourceUrl = URL.createObjectURL(file);
        var im = new Image();
        im.onload = function () {
            sourceImg = im;
            sourceCanvas = null;
            renderResults();
            showSuccess('Favicons generated from your image. Download a size below or get the full ZIP.');
        };
        im.onerror = function () {
            showError('That image could not be loaded. Please try a different file.');
        };
        im.src = sourceUrl;
    });

    document.getElementById('generateTextBtn').addEventListener('click', function () {
        hideAlerts();
        var letter = document.getElementById('letterInput').value.trim();
        if (!letter) {
            showError('Please type a letter or character for your favicon.');
            return;
        }
        var bg = document.getElementById('bgColor').value;
        var fg = document.getElementById('fgColor').value;
        var shape = document.getElementById('shapeSelect').value;
        var base = 512;
        var c = document.createElement('canvas');
        c.width = base;
        c.height = base;
        var cx = c.getContext('2d');
        cx.fillStyle = bg;
        if (shape === 'circle') {
            cx.beginPath();
            cx.arc(base / 2, base / 2, base / 2, 0, Math.PI * 2);
            cx.fill();
        } else if (shape === 'rounded') {
            var r = Math.round(base * 0.18);
            cx.beginPath();
            if (typeof cx.roundRect === 'function') {
                cx.roundRect(0, 0, base, base, r);
            } else {
                cx.rect(0, 0, base, base);
            }
            cx.fill();
        } else {
            cx.fillRect(0, 0, base, base);
        }
        var isSingle = letter.length === 1;
        var fontSize = isSingle ? Math.round(base * 0.62) : Math.round(base * 0.42);
        cx.fillStyle = fg;
        cx.font = 'bold ' + fontSize + 'px Arial, sans-serif';
        cx.textAlign = 'center';
        cx.textBaseline = 'middle';
        cx.fillText(letter, base / 2, base / 2 + fontSize * 0.04);
        sourceCanvas = c;
        sourceImg = null;
        renderResults();
        showSuccess('Favicons generated from your text. Download a size below or get the full ZIP.');
    });

    document.getElementById('letterInput').addEventListener('input', function () {
        void 0;
    });
    var liveInputs = ['bgColor', 'fgColor', 'shapeSelect'];
    liveInputs.forEach(function (id) {
        document.getElementById(id).addEventListener('change', function () {
            if (sourceCanvas) document.getElementById('generateTextBtn').click();
        });
    });

    zipBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!generated.length) {
            showError('Generate favicons first, then download the ZIP.');
            return;
        }
        if (typeof JSZip === 'undefined') {
            showError('ZIP library failed to load. Please check your internet connection and try again, or download the sizes individually.');
            return;
        }
        zipBtn.disabled = true;
        zipBtn.textContent = 'Creating ZIP...';
        try {
            var zip = new JSZip();
            for (var i = 0; i < generated.length; i++) {
                var item = generated[i];
                var blob = await new Promise(function (resolve) {
                    item.canvas.toBlob(resolve, 'image/png');
                });
                if (blob) zip.file(item.name, blob);
            }
            zip.file('how-to-use.txt', 'Azlaan Tools - Favicon Pack\n\nFiles:\n- favicon-16x16.png\n- favicon-32x32.png\n- favicon-48x48.png\n- apple-touch-icon.png (180x180)\n\nUpload these files to your website root folder and add this code to your page head:\n\n' + buildCode() + '\n');
            var zipBlob = await zip.generateAsync({ type: 'blob' });
            var url = URL.createObjectURL(zipBlob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'favicons.zip';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () {
                URL.revokeObjectURL(url);
            }, 3000);
            showSuccess('Done! favicons.zip has been downloaded with all 4 sizes.');
        } catch (e) {
            showError('Could not create the ZIP. Please download the sizes individually instead.');
        } finally {
            zipBtn.disabled = false;
            zipBtn.textContent = 'Download All (.zip)';
        }
    });

    document.getElementById('copyCodeBtn').addEventListener('click', async function () {
        if (!htmlCode.value) return;
        try {
            await navigator.clipboard.writeText(htmlCode.value);
            showSuccess('HTML code copied to clipboard.');
        } catch (e) {
            htmlCode.select();
            document.execCommand('copy');
            showSuccess('HTML code copied to clipboard.');
        }
    });
})();
</script>
@endsection
