@extends('layouts.app')
@section('title', 'Base64 to Image — Decode Base64 to PNG / JPG Free | Azlaan Tools')
@section('meta_description', 'Convert Base64 text or a data URL back into an image. Live preview, format detection and PNG / JPG download. Free, no signup, everything stays in your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Base64 to Image</h1>
            <p class="lead text-muted">Paste Base64 text or a full data URL and turn it back into a real image file you can view and download.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="inputText">Paste Base64 or data URL</label>
                    <textarea id="inputText" class="form-control" rows="7" placeholder="Paste here — e.g. data:image/png;base64,iVBORw0KGgo... or just the Base64 text"></textarea>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="decodeBtn" class="btn btn-primary btn-lg">Decode &amp; Preview</button>
                        <button type="button" id="clearBtn" class="btn btn-outline-secondary btn-lg">Clear</button>
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div id="resultWrap" class="d-none mt-4 text-center">
                        <img id="preview" class="img-fluid rounded border" alt="Decoded image preview" style="max-height:420px; background-image: conic-gradient(#eee 25%, #fff 0 50%, #eee 0 75%, #fff 0); background-size: 20px 20px;">
                        <ul class="list-group list-group-flush text-start small mt-3">
                            <li class="list-group-item d-flex justify-content-between"><span>Detected format</span><strong id="fmtInfo">-</strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span>Dimensions</span><strong id="dimInfo">-</strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span>Approx. file size</span><strong id="sizeInfo">-</strong></li>
                        </ul>
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" id="downloadPngBtn" class="btn btn-success btn-lg">Download as PNG</button>
                            <button type="button" id="downloadJpgBtn" class="btn btn-primary">Download as JPG</button>
                            <button type="button" id="downloadOrigBtn" class="btn btn-outline-secondary">Download in detected format</button>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Paste your Base64 text or data URL into the box. Whitespace and line breaks are cleaned automatically.</li>
                <li>Press <strong>Decode &amp; Preview</strong> — the image, its format and dimensions appear instantly.</li>
                <li>Download it as PNG, JPG, or in its original detected format.</li>
            </ol>
            <p class="text-muted">Need the opposite direction? Use our Image to Base64 tool to turn a photo into Base64 text.</p>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your data never leaves your browser. Decoding happens on your own device.</div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function () {
    var inputText = document.getElementById('inputText');
    var errorBox = document.getElementById('errorBox');
    var resultWrap = document.getElementById('resultWrap');
    var preview = document.getElementById('preview');
    var fmtInfo = document.getElementById('fmtInfo');
    var dimInfo = document.getElementById('dimInfo');
    var sizeInfo = document.getElementById('sizeInfo');
    var decodedUrl = null, decodedType = 'image/png';
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); resultWrap.classList.add('d-none'); }
    function fmtBytes(b) { return b < 1024 ? b + ' B' : (b < 1048576 ? (b / 1024).toFixed(1) + ' KB' : (b / 1048576).toFixed(2) + ' MB'); }
    function parseInput(raw) {
        var s = raw.trim().replace(/\s+/g, '');
        var type = null;
        var prefix = 'data:';
        if (s.indexOf(prefix) === 0) {
            var comma = s.indexOf(',');
            if (comma === -1) return null;
            var header = s.substring(0, comma);
            var m = header.match(/data:([^;,]+)/);
            if (m) type = m[1];
            s = s.substring(comma + 1);
        }
        return { b64: s, type: type };
    }
    function detectFromBytes(bytes) {
        if (bytes.length >= 4 && bytes[0] === 0x89 && bytes[1] === 0x50) return 'image/png';
        if (bytes.length >= 3 && bytes[0] === 0xFF && bytes[1] === 0xD8) return 'image/jpeg';
        if (bytes.length >= 6 && bytes[0] === 0x47 && bytes[1] === 0x49 && bytes[2] === 0x46) return 'image/gif';
        if (bytes.length >= 12 && bytes[8] === 0x57 && bytes[9] === 0x45 && bytes[10] === 0x42 && bytes[11] === 0x50) return 'image/webp';
        if (bytes.length >= 2 && bytes[0] === 0x42 && bytes[1] === 0x4D) return 'image/bmp';
        return null;
    }
    function extFor(type) {
        if (type === 'image/jpeg') return 'jpg';
        if (type === 'image/gif') return 'gif';
        if (type === 'image/webp') return 'webp';
        if (type === 'image/bmp') return 'bmp';
        return 'png';
    }
    document.getElementById('decodeBtn').addEventListener('click', function () {
        errorBox.classList.add('d-none');
        var parsed = parseInput(inputText.value || '');
        if (!parsed || !parsed.b64) { showError('Please paste some Base64 text or a data URL first.'); return; }
        var bin;
        // Accept the base64url alphabet (- and _) and missing padding too — both
        // are common in pasted strings, and atob() throws on them.
        var b64 = parsed.b64.replace(/-/g, '+').replace(/_/g, '/');
        while (b64.length % 4) b64 += '=';
        try { bin = atob(b64); } catch (e) { showError('That does not look like valid Base64. Check for missing or extra characters and try again.'); return; }
        var bytes = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
        decodedType = parsed.type || detectFromBytes(bytes) || 'image/png';
        var blob = new Blob([bytes], { type: decodedType });
        if (decodedUrl) URL.revokeObjectURL(decodedUrl);
        decodedUrl = URL.createObjectURL(blob);
        var img = new Image();
        img.onload = function () {
            preview.src = decodedUrl;
            fmtInfo.textContent = decodedType;
            dimInfo.textContent = img.naturalWidth + ' x ' + img.naturalHeight + ' px';
            sizeInfo.textContent = fmtBytes(blob.size);
            resultWrap.classList.remove('d-none');
        };
        img.onerror = function () { showError('Decoded, but the result is not a viewable image. The Base64 may be incomplete or not an image.'); };
        img.src = decodedUrl;
    });
    inputText.addEventListener('input', function () {
        if ((inputText.value || '').trim().length > 30) {
            // Live preview after a short paste, without spamming decode on every keystroke for huge strings
            if (inputText.value.length < 200000) document.getElementById('decodeBtn').click();
        }
    });
    document.getElementById('clearBtn').addEventListener('click', function () {
        inputText.value = ''; resultWrap.classList.add('d-none'); errorBox.classList.add('d-none');
    });
    function downloadAs(type) {
        if (!decodedUrl) return;
        var img = new Image();
        img.onload = function () {
            var c = document.createElement('canvas'); c.width = img.naturalWidth; c.height = img.naturalHeight;
            var ctx = c.getContext('2d');
            if (type === 'image/jpeg') { ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, c.width, c.height); }
            ctx.drawImage(img, 0, 0);
            c.toBlob(function (b) {
                if (!b) { showError('Download failed in this browser — use "Download original" instead.'); return; }
                var u = URL.createObjectURL(b); var a = document.createElement('a'); a.href = u; a.download = 'decoded-image.' + extFor(type); document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(u); }, 3000);
            }, type, 0.95);
        };
        img.src = decodedUrl;
    }
    document.getElementById('downloadPngBtn').addEventListener('click', function () { downloadAs('image/png'); });
    document.getElementById('downloadJpgBtn').addEventListener('click', function () { downloadAs('image/jpeg'); });
    document.getElementById('downloadOrigBtn').addEventListener('click', function () {
        if (!decodedUrl) return;
        var a = document.createElement('a'); a.href = decodedUrl; a.download = 'decoded-image.' + extFor(decodedType); document.body.appendChild(a); a.click(); a.remove();
    });
})();
</script>
@endsection
