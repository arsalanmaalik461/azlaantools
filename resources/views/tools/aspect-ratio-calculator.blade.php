@extends('layouts.app')

@section('title', 'Aspect Ratio Calculator - 16:9, 4:3 & More | Azlaan Tools')
@section('meta_description', 'Free aspect ratio calculator: simplify any width and height to a ratio, use presets like 16:9, and calculate resized dimensions instantly. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Aspect Ratio Calculator</h1>
            <p class="lead text-muted">Simplify any width and height into an aspect ratio and resize images or videos without stretching — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="wInput" class="form-label fw-semibold">Width</label>
                            <input type="number" class="form-control" id="wInput" value="1920" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="hInput" class="form-label fw-semibold">Height</label>
                            <input type="number" class="form-control" id="hInput" value="1080" min="1" step="any">
                        </div>
                    </div>
                    <div class="text-center my-3">
                        <div class="small text-muted">Simplified Ratio</div>
                        <div class="display-5 fw-bold" id="ratioOut">16 : 9</div>
                        <div class="text-muted" id="decimalOut">Decimal: 1.78 - e.g. Full HD, YouTube, most TVs</div>
                    </div>
                    <div class="d-flex justify-content-center mb-2">
                        <div id="previewBox" class="border border-primary bg-light d-flex align-items-center justify-content-center text-primary fw-semibold" style="width:320px;height:180px;max-width:100%;transition:all .2s;">Preview</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-w="16" data-h="9">16:9</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-w="4" data-h="3">4:3</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-w="1" data-h="1">1:1</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-w="9" data-h="16">9:16</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-w="3" data-h="2">3:2</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-w="21" data-h="9">21:9</button>
                    </div>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="errorBox"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Resize Helper (keeps the same ratio)</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="newW" class="form-label fw-semibold">New Width</label>
                            <input type="number" class="form-control" id="newW" placeholder="Type width..." min="1" step="any">
                            <div class="form-text">Height is calculated automatically.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="newH" class="form-label fw-semibold">New Height</label>
                            <input type="number" class="form-control" id="newH" placeholder="Type height..." min="1" step="any">
                            <div class="form-text">Width is calculated automatically.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter your width and height to see the simplified ratio and decimal value.</li>
                        <li>Or tap a preset like 16:9 for video or 9:16 for Reels and Shorts.</li>
                        <li>The blue preview rectangle always matches your ratio.</li>
                        <li>In the resize helper, type a new width to get the matching height (or the other way round).</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var wInput = document.getElementById('wInput');
    var hInput = document.getElementById('hInput');
    var newW = document.getElementById('newW');
    var newH = document.getElementById('newH');
    var errorBox = document.getElementById('errorBox');
    var names = { '16:9': 'Full HD, YouTube, most TVs', '4:3': 'Old TVs and some cameras', '1:1': 'Square posts, profile photos', '9:16': 'Reels, Shorts, TikTok, Status', '3:2': 'DSLR photos', '21:9': 'Ultrawide / cinematic', '16:10': 'Many laptop screens', '5:4': 'Classic monitors' };
    function gcd(a, b) {
        a = Math.round(a); b = Math.round(b);
        while (b) { var t = b; b = a % b; a = t; }
        return a || 1;
    }
    function update() {
        var w = parseFloat(wInput.value), h = parseFloat(hInput.value);
        if (isNaN(w) || isNaN(h) || w <= 0 || h <= 0) {
            errorBox.textContent = 'Width and height must both be numbers greater than zero.';
            errorBox.classList.remove('d-none');
            return;
        }
        errorBox.classList.add('d-none');
        var g = gcd(w, h);
        var rw = Math.round(w) / g, rh = Math.round(h) / g;
        var key = rw + ':' + rh;
        var dec = (w / h);
        document.getElementById('ratioOut').textContent = rw + ' : ' + rh;
        var note = names[key] ? ' - e.g. ' + names[key] : '';
        document.getElementById('decimalOut').textContent = 'Decimal: ' + dec.toFixed(2) + note;
        var box = document.getElementById('previewBox');
        var maxW = 320, maxH = 240;
        var scale = Math.min(maxW / w, maxH / h);
        var bw = Math.max(50, Math.round(w * scale));
        var bh = Math.max(40, Math.round(h * scale));
        box.style.width = bw + 'px';
        box.style.height = bh + 'px';
        box.textContent = Math.round(w) + ' x ' + Math.round(h);
        // refresh resize helper if a value is present
        if (document.activeElement === newW && newW.value) { syncFromW(); }
        if (document.activeElement === newH && newH.value) { syncFromH(); }
    }
    function syncFromW() {
        var w = parseFloat(wInput.value), h = parseFloat(hInput.value);
        var nw = parseFloat(newW.value);
        if (isNaN(nw) || isNaN(w) || isNaN(h) || w <= 0) { return; }
        newH.value = Math.round((nw * h / w) * 100) / 100;
    }
    function syncFromH() {
        var w = parseFloat(wInput.value), h = parseFloat(hInput.value);
        var nh = parseFloat(newH.value);
        if (isNaN(nh) || isNaN(w) || isNaN(h) || h <= 0) { return; }
        newW.value = Math.round((nh * w / h) * 100) / 100;
    }
    wInput.addEventListener('input', update);
    hInput.addEventListener('input', update);
    newW.addEventListener('input', syncFromW);
    newH.addEventListener('input', syncFromH);
    document.querySelectorAll('.preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var pw = parseInt(btn.getAttribute('data-w'), 10);
            var ph = parseInt(btn.getAttribute('data-h'), 10);
            wInput.value = pw * 120;
            hInput.value = ph * 120;
            update();
        });
    });
    update();
})();
</script>
@endsection
