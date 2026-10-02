@extends('layouts.app')

@section('title', 'YouTube Thumbnail Downloader - Free HD Thumbnail Download | Azlaan Tools')
@section('meta_description', 'Download YouTube video thumbnails in HD for free. Paste any YouTube, youtu.be or Shorts link and get maxres, HD, HQ and MQ thumbnails instantly. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">YouTube Thumbnail Downloader</h1>
            <p class="lead text-muted">Paste any YouTube video link and download its thumbnail in full HD quality — free, no signup, works for normal videos, short links and Shorts.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="ytUrl" class="form-label fw-semibold">YouTube Video URL</label>
                    <div class="input-group mb-2">
                        <input type="url" class="form-control" id="ytUrl" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ" autocomplete="off">
                        <button class="btn btn-primary" type="button" id="fetchBtn">Get Thumbnail</button>
                    </div>
                    <div class="form-text">Examples: youtube.com/watch?v=..., youtu.be/..., youtube.com/shorts/...</div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="resultBox" class="d-none mt-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Maximum Resolution (1280x720)</span>
                                <span>
                                    <a href="#" class="btn btn-sm btn-outline-secondary thumb-preview" data-quality="maxresdefault" target="_blank" rel="noopener">Preview</a>
                                    <button type="button" class="btn btn-sm btn-success thumb-download" data-quality="maxresdefault">Download</button>
                                </span>
                            </div>
                            <img class="img-fluid rounded border w-100 thumb-img" data-quality="maxresdefault" alt="Maximum resolution thumbnail">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="fw-semibold mb-1">SD (640x480)</div>
                                <img class="img-fluid rounded border w-100 thumb-img mb-2" data-quality="sddefault" alt="SD thumbnail">
                                <a href="#" class="btn btn-sm btn-outline-secondary thumb-preview" data-quality="sddefault" target="_blank" rel="noopener">Preview</a>
                                <button type="button" class="btn btn-sm btn-success thumb-download" data-quality="sddefault">Download</button>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-semibold mb-1">HQ (480x360)</div>
                                <img class="img-fluid rounded border w-100 thumb-img mb-2" data-quality="hqdefault" alt="HQ thumbnail">
                                <a href="#" class="btn btn-sm btn-outline-secondary thumb-preview" data-quality="hqdefault" target="_blank" rel="noopener">Preview</a>
                                <button type="button" class="btn btn-sm btn-success thumb-download" data-quality="hqdefault">Download</button>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-semibold mb-1">MQ (320x180)</div>
                                <img class="img-fluid rounded border w-100 thumb-img mb-2" data-quality="mqdefault" alt="MQ thumbnail">
                                <a href="#" class="btn btn-sm btn-outline-secondary thumb-preview" data-quality="mqdefault" target="_blank" rel="noopener">Preview</a>
                                <button type="button" class="btn btn-sm btn-success thumb-download" data-quality="mqdefault">Download</button>
                            </div>
                        </div>
                        <p class="small text-muted mt-3 mb-0">Note: Some browsers may open the image in a new tab instead of downloading it directly because the image is served by YouTube. If that happens, right-click / long-press the preview and choose "Save image".</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Open YouTube and copy the link of the video whose thumbnail you want.</li>
                        <li>Paste the link in the box above (watch, youtu.be and Shorts links all work).</li>
                        <li>Click <strong>Get Thumbnail</strong>.</li>
                        <li>Preview the size you want and click <strong>Download</strong> — Maximum Resolution gives the best quality when available.</li>
                    </ol>
                    <p class="small text-muted mb-0">Thumbnails belong to their respective video owners. Please respect copyright and only reuse thumbnails you have permission for.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('ytUrl');
    var fetchBtn = document.getElementById('fetchBtn');
    var errorBox = document.getElementById('errorBox');
    var resultBox = document.getElementById('resultBox');
    var currentId = '';

    function extractVideoId(raw) {
        var url = (raw || '').trim();
        if (!url) return null;
        // Plain 11-character ID pasted directly
        if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return url;
        var patterns = [
            /[?&]v=([a-zA-Z0-9_-]{11})/,
            /youtu\.be\/([a-zA-Z0-9_-]{11})/,
            /\/shorts\/([a-zA-Z0-9_-]{11})/,
            /\/embed\/([a-zA-Z0-9_-]{11})/,
            /\/live\/([a-zA-Z0-9_-]{11})/,
            /\/v\/([a-zA-Z0-9_-]{11})/
        ];
        for (var i = 0; i < patterns.length; i++) {
            var m = url.match(patterns[i]);
            if (m) return m[1];
        }
        return null;
    }

    function thumbUrl(quality) {
        return 'https://img.youtube.com/vi/' + currentId + '/' + quality + '.jpg';
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        resultBox.classList.add('d-none');
    }

    function fetchThumbs() {
        var id = extractVideoId(input.value);
        if (!id) {
            showError('Invalid YouTube URL. Please paste a valid video link, for example a watch, youtu.be or Shorts URL.');
            return;
        }
        currentId = id;
        errorBox.classList.add('d-none');
        document.querySelectorAll('.thumb-img').forEach(function (img) {
            img.src = thumbUrl(img.getAttribute('data-quality'));
        });
        document.querySelectorAll('.thumb-preview').forEach(function (a) {
            a.href = thumbUrl(a.getAttribute('data-quality'));
        });
        resultBox.classList.remove('d-none');
    }

    async function downloadThumb(quality, btn) {
        var url = thumbUrl(quality);
        var original = btn.textContent;
        btn.textContent = 'Working...';
        btn.disabled = true;
        try {
            var res = await fetch(url);
            if (!res.ok) throw new Error('fetch failed');
            var blob = await res.blob();
            var objUrl = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = objUrl;
            a.download = 'youtube-thumbnail-' + currentId + '-' + quality + '.jpg';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(objUrl); }, 2000);
        } catch (e) {
            // Cross-origin fallback: open in a new tab
            window.open(url, '_blank', 'noopener');
        }
        btn.textContent = original;
        btn.disabled = false;
    }

    fetchBtn.addEventListener('click', fetchThumbs);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') fetchThumbs(); });
    input.addEventListener('input', function () { errorBox.classList.add('d-none'); });
    document.querySelectorAll('.thumb-download').forEach(function (btn) {
        btn.addEventListener('click', function () { downloadThumb(btn.getAttribute('data-quality'), btn); });
    });
})();
</script>
@endsection
