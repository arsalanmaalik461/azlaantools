@extends('layouts.app')
@section('title', 'GIF Maker — Make Animated GIF from Photos Free | Azlaan Tools')
@section('meta_description', 'Make an animated GIF from multiple photos free: set delay, width and looping, preview and download. No signup, photos never leave your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">GIF Maker</h1>
            <p class="lead text-muted">Turn a set of photos into an animated GIF for WhatsApp, social media or fun. Add images, set the speed, and download.</p>
            <div class="alert alert-warning"><strong>Size tip:</strong> GIFs get big fast. Many large photos at full width can make a 10 MB+ file that is slow to share. Use a smaller width (480–640 px) for easy sharing.</div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drag &amp; drop images here, or click to browse</p>
                        <p class="text-muted small mb-0">Add 2 or more JPG / PNG images — order = animation order</p>
                        <input type="file" id="fileInput" class="d-none" accept="image/*" multiple>
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div id="thumbs" class="d-flex flex-wrap gap-2 mt-3"></div>
                    <div class="row g-3 mt-2">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="delayRange">Delay: <span id="delayVal">300</span> ms</label>
                            <input type="range" id="delayRange" class="form-range" min="50" max="2000" step="50" value="300">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="widthSelect">GIF width</label>
                            <select id="widthSelect" class="form-select">
                                <option value="320">320 px (small)</option>
                                <option value="480" selected>480 px (good for sharing)</option>
                                <option value="640">640 px</option>
                                <option value="800">800 px (large)</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="loopSelect">Loop</label>
                            <select id="loopSelect" class="form-select">
                                <option value="0" selected>Loop forever</option>
                                <option value="1">Play once</option>
                                <option value="3">Loop 3 times</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" id="makeBtn" class="btn btn-primary btn-lg w-100 mt-3">Make GIF</button>
                    <div id="statusText" class="small text-muted mt-2"></div>
                    <div id="resultWrap" class="d-none mt-3 text-center">
                        <img id="resultImg" class="img-fluid rounded border" alt="Your animated GIF">
                        <p class="mt-2 mb-2"><strong id="resultSize">-</strong></p>
                        <a id="downloadBtn" href="#" download="animated.gif" class="btn btn-success btn-lg w-100">Download GIF</a>
                    </div>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Add 2 or more images — they will play in the order you selected them.</li>
                <li>Set delay (lower = faster animation), width, and looping.</li>
                <li>Press <strong>Make GIF</strong>, preview it, then download.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photos never leave your browser. The GIF is built on your own device.</div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/gifshot@0.4.5/dist/gifshot.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var errorBox = document.getElementById('errorBox');
    var thumbs = document.getElementById('thumbs');
    var delayRange = document.getElementById('delayRange');
    var delayVal = document.getElementById('delayVal');
    var widthSelect = document.getElementById('widthSelect');
    var loopSelect = document.getElementById('loopSelect');
    var statusText = document.getElementById('statusText');
    var resultWrap = document.getElementById('resultWrap');
    var resultImg = document.getElementById('resultImg');
    var resultSize = document.getElementById('resultSize');
    var downloadBtn = document.getElementById('downloadBtn');
    var dataUrls = [];
    delayRange.addEventListener('input', function () { delayVal.textContent = delayRange.value; });
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function fmt(b) { return b < 1024 ? b + ' B' : (b < 1048576 ? (b / 1024).toFixed(1) + ' KB' : (b / 1048576).toFixed(2) + ' MB'); }
    function handleFiles(files) {
        errorBox.classList.add('d-none');
        var list = Array.prototype.slice.call(files || []).filter(function (f) { return f.type && f.type.indexOf('image/') === 0; });
        if (!list.length) return;
        // FileReaders finish in arbitrary order (big files last), so store results
        // in indexed slots and only publish them, in selection order, once all
        // reads settle — otherwise the animation order is scrambled.
        var slots = new Array(list.length);
        var settled = 0;
        list.forEach(function (file, idx) {
            var reader = new FileReader();
            reader.onload = function () { slots[idx] = reader.result; settled++; publish(); };
            reader.onerror = function () { settled++; publish(); };
            reader.readAsDataURL(file);
        });
        function publish() {
            if (settled !== list.length) return;
            slots.forEach(function (url) {
                if (!url) return;
                dataUrls.push(url);
                var img = document.createElement('img');
                img.src = url; img.alt = 'Frame'; img.className = 'rounded border';
                img.style.width = '72px'; img.style.height = '72px'; img.style.objectFit = 'cover';
                thumbs.appendChild(img);
            });
            if (dataUrls.length) statusText.textContent = dataUrls.length + ' image(s) ready.';
        }
    }
    function patchGifLoop(dataUrl, loopVal) {
        // gifshot always writes "loop forever" — its options have no loop/repeat
        // setting. Fix the finished GIF bytes directly: set the NETSCAPE2.0 loop
        // count, or remove the extension entirely for "play once".
        try {
            var comma = dataUrl.indexOf(',');
            var bin = atob(dataUrl.substring(comma + 1));
            var marker = 'NETSCAPE2.0';
            var idx = bin.indexOf(marker);
            if (idx < 3) return dataUrl;
            var bytes = [];
            for (var i = 0; i < bin.length; i++) bytes.push(bin.charCodeAt(i));
            if (loopVal === 1) {
                bytes.splice(idx - 3, 19);
            } else {
                bytes[idx + 13] = loopVal & 255;
                bytes[idx + 14] = (loopVal >> 8) & 255;
            }
            var out = '';
            for (var j = 0; j < bytes.length; j += 8192) {
                out += String.fromCharCode.apply(null, bytes.slice(j, j + 8192));
            }
            return dataUrl.substring(0, comma + 1) + btoa(out);
        } catch (e) { return dataUrl; }
    }
    document.getElementById('makeBtn').addEventListener('click', function () {
        errorBox.classList.add('d-none');
        if (typeof gifshot === 'undefined') { showError('GIF library failed to load. Check your internet and refresh.'); return; }
        if (dataUrls.length < 2) { showError('Please add at least 2 images first.'); return; }
        statusText.textContent = 'Building your GIF… large images can take a while.';
        resultWrap.classList.add('d-none');
        var width = parseInt(widthSelect.value, 10);
        var first = new Image();
        first.onload = function () {
            var height = Math.round(width * first.naturalHeight / first.naturalWidth) || width;
            var loopVal = parseInt(loopSelect.value, 10);
            gifshot.createGIF({
                images: dataUrls,
                interval: parseInt(delayRange.value, 10) / 1000,
                gifWidth: width,
                gifHeight: height,
                numWorkers: 2,
                sampleInterval: 10
            }, function (obj) {
                if (!obj || obj.error) { showError('GIF creation failed. Try fewer or smaller images.'); statusText.textContent = ''; return; }
                // gifshot ignores any loop option (it always writes "loop forever"),
                // so apply the chosen loop setting to the finished GIF bytes.
                var image = patchGifLoop(obj.image, loopVal);
                // Estimate size from base64 length
                var b64len = image.length - image.indexOf(',') - 1;
                var estBytes = Math.round(b64len * 3 / 4);
                resultImg.src = image;
                resultSize.textContent = 'GIF size: approx. ' + fmt(estBytes) + (estBytes > 8000000 ? ' — quite large, try a smaller width.' : '');
                downloadBtn.href = image;
                resultWrap.classList.remove('d-none');
                statusText.textContent = 'Done!';
            });
        };
        first.src = dataUrls[0];
    });
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFiles(fileInput.files); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer) handleFiles(e.dataTransfer.files); });
})();
</script>
@endsection
