@extends('layouts.app')

@section('title', 'Image To ASCII Art - Azlaan Tools')
@section('meta_description', 'Convert any photo into cool ASCII text art right in your browser. Free, private and instant.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <h1 class="mb-3">Image To ASCII Art</h1>
            <p class="lead text-muted">Upload your photo and turn it into fun ASCII text art. Everything happens in your browser — the photo is never uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imgFile" class="form-label fw-semibold">Select a photo (JPG / PNG)</label>
                        <input type="file" class="form-control" id="imgFile" accept="image/*">
                        <div class="form-text">The photo is processed only in your browser.</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="asciiWidth" class="form-label fw-semibold">Art width (characters)</label>
                            <input type="number" class="form-control" id="asciiWidth" value="100" min="40" max="220">
                        </div>
                        <div class="col-md-4">
                            <label for="charsetSel" class="form-label fw-semibold">Character set</label>
                            <select class="form-select" id="charsetSel">
                                <option value="0">Standard ( .:-=+*#%@ )</option>
                                <option value="1">Blocks ( ░▒▓█ )</option>
                                <option value="2">Simple ( .+*%# )</option>
                                <option value="3">Text style ( letters )</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="invertChk" class="form-label fw-semibold">Invert</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="invertChk">
                                <label class="form-check-label" for="invertChk">Light background style</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate ASCII Art</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-2">Your ASCII Art</h5>
                        <div class="border rounded p-2 mb-3" style="overflow:auto; max-height:480px; background:#ffffff;">
                            <pre id="asciiOut" class="mb-0" style="font-family:'Courier New',monospace; font-size:8px; line-height:8px; letter-spacing:0; color:#111;"></pre>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy Text</button>
                            <button type="button" class="btn btn-outline-success" id="dlBtn">Download .txt</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your photo (JPG or PNG).</li>
                <li>Choose the width and character set — smaller width = smaller art, bigger width = more detail.</li>
                <li>Press "Generate ASCII Art", then copy or download it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var imgFile = document.getElementById('imgFile');
    var asciiWidth = document.getElementById('asciiWidth');
    var charsetSel = document.getElementById('charsetSel');
    var invertChk = document.getElementById('invertChk');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var asciiOut = document.getElementById('asciiOut');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');

    var SETS = [
        ' .:-=+*#%@',
        ' ░▒▓█',
        ' .+*%#@',
        ' .abcdefghijklmnopqrstuvwxyz#%@'
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var file = imgFile.files[0];
        if (!file) { showError('Please choose an image first.'); return; }
        if (!file.type || file.type.indexOf('image/') !== 0) { showError('Please select only an image file (JPG/PNG).'); return; }

        var width = parseInt(asciiWidth.value, 10);
        if (isNaN(width) || width < 40 || width > 220) { showError('Keep the width between 40 and 220 characters.'); return; }

        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                try {
                    var aspect = img.height / img.width;
                    var h = Math.max(10, Math.round(width * aspect * 0.55));
                    var canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = h;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, h);
                    var data = ctx.getImageData(0, 0, width, h).data;
                    var chars = SETS[parseInt(charsetSel.value, 10)] || SETS[0];
                    var n = chars.length;
                    var invert = invertChk.checked;
                    var lines = [];
                    for (var y = 0; y < h; y++) {
                        var line = '';
                        for (var x = 0; x < width; x++) {
                            var i = (y * width + x) * 4;
                            var gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                            var idx = Math.floor((gray / 255) * (n - 1));
                            if (invert) idx = (n - 1) - idx;
                            line += chars.charAt(idx);
                        }
                        lines.push(line);
                    }
                    asciiOut.textContent = lines.join('\n');
                    results.classList.remove('d-none');
                    results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } catch (err) {
                    showError('The photo could not be processed. Please try another image.');
                }
            };
            img.onerror = function () { showError('The image could not be loaded. The file may be damaged.'); };
            img.src = e.target.result;
        };
        reader.onerror = function () { showError('The file could not be read.'); };
        reader.readAsDataURL(file);
    });

    copyBtn.addEventListener('click', function () {
        var txt = asciiOut.textContent;
        if (!txt) return;
        function done() { copyBtn.textContent = 'Copied!'; setTimeout(function () { copyBtn.textContent = 'Copy Text'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(done, function () { fallbackCopy(txt, done); });
        } else { fallbackCopy(txt, done); }
    });
    function fallbackCopy(txt, done) {
        var ta = document.createElement('textarea');
        ta.value = txt;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        document.body.removeChild(ta);
    }

    dlBtn.addEventListener('click', function () {
        var txt = asciiOut.textContent;
        if (!txt) return;
        var blob = new Blob([txt], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'ascii-art.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });
})();
</script>
@endsection
