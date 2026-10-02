@extends('layouts.app')

@section('title', 'HEX / RGB Color Converter - HEX to RGB, HSL & Palette | Azlaan Tools')
@section('meta_description', 'Free HEX to RGB color converter with HSL values, click-to-copy formats and automatic shades and tints palette. No signup needed, works instantly in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">HEX / RGB Color Converter</h1>
            <p class="lead text-muted">Pick a color or type a HEX or RGB value and instantly get every format, plus a ready-to-use palette of shades and tints — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="colorPicker" class="form-label fw-semibold">Pick a Color</label>
                            <input type="color" class="form-control form-control-color w-100" id="colorPicker" value="#0d6efd" style="height:64px;">
                        </div>
                        <div class="col-md-9">
                            <div id="previewBox" class="rounded border" style="height:64px;background:#0d6efd;"></div>
                            <div class="small text-muted mt-1">Live preview of the selected color.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label for="hexInput" class="form-label fw-semibold">HEX</label>
                            <div class="input-group">
                                <span class="input-group-text">#</span>
                                <input type="text" class="form-control" id="hexInput" value="0D6EFD" maxlength="6">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">RGB (0 - 255)</label>
                            <div class="row g-2">
                                <div class="col-4"><input type="number" class="form-control" id="rInput" value="13" min="0" max="255" placeholder="R"></div>
                                <div class="col-4"><input type="number" class="form-control" id="gInput" value="110" min="0" max="255" placeholder="G"></div>
                                <div class="col-4"><input type="number" class="form-control" id="bInput" value="253" min="0" max="255" placeholder="B"></div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="errorBox"></div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="randomBtn">Random Color</button>
                    </div>
                    <hr>
                    <h2 class="h6 fw-bold">Click any format to copy it</h2>
                    <div class="row g-2" id="formatsWrap">
                        <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100 copy-format" data-kind="hex">HEX: <span id="fmtHex">#0D6EFD</span></button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100 copy-format" data-kind="rgb">RGB: <span id="fmtRgb">rgb(13, 110, 253)</span></button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100 copy-format" data-kind="hsl">HSL: <span id="fmtHsl">hsl(216, 98%, 52%)</span></button></div>
                    </div>
                    <div class="alert alert-success mt-3 mb-0 d-none" id="copyMsg">Copied to clipboard!</div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Shades &amp; Tints Palette</h2>
                    <p class="small text-muted">Darker shades on the left, your color in the middle, lighter tints on the right. Click any swatch to copy its HEX.</p>
                    <div class="d-flex flex-wrap gap-2" id="paletteWrap"></div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Use the color picker, or type a HEX value, or enter R, G and B numbers — all fields stay in sync.</li>
                        <li>Read the HEX, RGB and HSL values below and click any format button to copy it.</li>
                        <li>Browse the shades and tints palette and click a swatch to copy its HEX code.</li>
                        <li>Click <strong>Random Color</strong> for instant color inspiration.</li>
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
    var picker = document.getElementById('colorPicker');
    var hexInput = document.getElementById('hexInput');
    var rInput = document.getElementById('rInput');
    var gInput = document.getElementById('gInput');
    var bInput = document.getElementById('bInput');
    var errorBox = document.getElementById('errorBox');
    var copyMsg = document.getElementById('copyMsg');
    var state = { r: 13, g: 110, b: 253 };

    function clamp(n) { return Math.max(0, Math.min(255, Math.round(n))); }
    function toHex2(n) { var s = clamp(n).toString(16).toUpperCase(); return s.length === 1 ? '0' + s : s; }
    function hexString(r, g, b) { return '#' + toHex2(r) + toHex2(g) + toHex2(b); }
    function rgbToHsl(r, g, b) {
        r = r / 255; g = g / 255; b = b / 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h = 0, s = 0, l = (max + min) / 2;
        if (max !== min) {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            if (max === r) { h = (g - b) / d + (g < b ? 6 : 0); }
            else if (max === g) { h = (b - r) / d + 2; }
            else { h = (r - g) / d + 4; }
            h = h * 60;
        }
        return { h: Math.round(h), s: Math.round(s * 100), l: Math.round(l * 100) };
    }
    function showCopied(text) {
        copyMsg.textContent = 'Copied: ' + text;
        copyMsg.classList.remove('d-none');
        setTimeout(function () { copyMsg.classList.add('d-none'); }, 1800);
    }
    function copyText(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () { showCopied(text); }).catch(function () { showCopied(text); });
        } else { showCopied(text); }
    }
    function renderPalette() {
        var wrap = document.getElementById('paletteWrap');
        wrap.innerHTML = '';
        var factors = [-0.5, -0.4, -0.3, -0.2, -0.1, 0, 0.15, 0.3, 0.45, 0.6, 0.75];
        factors.forEach(function (f) {
            var r, g, b;
            if (f < 0) { r = clamp(state.r * (1 + f)); g = clamp(state.g * (1 + f)); b = clamp(state.b * (1 + f)); }
            else if (f > 0) { r = clamp(state.r + (255 - state.r) * f); g = clamp(state.g + (255 - state.g) * f); b = clamp(state.b + (255 - state.b) * f); }
            else { r = state.r; g = state.g; b = state.b; }
            var hex = hexString(r, g, b);
            var div = document.createElement('button');
            div.type = 'button';
            div.className = 'btn p-0 border';
            div.style.width = '86px';
            div.innerHTML = '<span style="display:block;height:44px;background:' + hex + ';"></span><span class="small d-block py-1">' + hex + '</span>';
            div.addEventListener('click', function () { copyText(hex); });
            wrap.appendChild(div);
        });
    }
    function render() {
        errorBox.classList.add('d-none');
        var hex = hexString(state.r, state.g, state.b);
        var hsl = rgbToHsl(state.r, state.g, state.b);
        picker.value = hex.toLowerCase();
        hexInput.value = hex.substring(1);
        rInput.value = state.r; gInput.value = state.g; bInput.value = state.b;
        document.getElementById('previewBox').style.background = hex;
        document.getElementById('fmtHex').textContent = hex;
        document.getElementById('fmtRgb').textContent = 'rgb(' + state.r + ', ' + state.g + ', ' + state.b + ')';
        document.getElementById('fmtHsl').textContent = 'hsl(' + hsl.h + ', ' + hsl.s + '%, ' + hsl.l + '%)';
        renderPalette();
    }
    picker.addEventListener('input', function () {
        var v = picker.value;
        state.r = parseInt(v.substring(1, 3), 16);
        state.g = parseInt(v.substring(3, 5), 16);
        state.b = parseInt(v.substring(5, 7), 16);
        render();
    });
    hexInput.addEventListener('input', function () {
        var v = hexInput.value.trim().replace('#', '');
        if (/^[0-9a-fA-F]{6}$/.test(v)) {
            state.r = parseInt(v.substring(0, 2), 16);
            state.g = parseInt(v.substring(2, 4), 16);
            state.b = parseInt(v.substring(4, 6), 16);
            render();
        } else if (v.length === 6) {
            errorBox.textContent = 'Invalid HEX value. Use 6 characters, 0-9 and A-F.';
            errorBox.classList.remove('d-none');
        }
    });
    function onRgb() {
        var r = parseInt(rInput.value, 10), g = parseInt(gInput.value, 10), b = parseInt(bInput.value, 10);
        if (isNaN(r) || isNaN(g) || isNaN(b)) { return; }
        state.r = clamp(r); state.g = clamp(g); state.b = clamp(b);
        render();
    }
    rInput.addEventListener('input', onRgb);
    gInput.addEventListener('input', onRgb);
    bInput.addEventListener('input', onRgb);
    document.querySelectorAll('.copy-format').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var kind = btn.getAttribute('data-kind');
            if (kind === 'hex') { copyText(document.getElementById('fmtHex').textContent); }
            if (kind === 'rgb') { copyText(document.getElementById('fmtRgb').textContent); }
            if (kind === 'hsl') { copyText(document.getElementById('fmtHsl').textContent); }
        });
    });
    document.getElementById('randomBtn').addEventListener('click', function () {
        state.r = Math.floor(Math.random() * 256);
        state.g = Math.floor(Math.random() * 256);
        state.b = Math.floor(Math.random() * 256);
        render();
    });
    render();
})();
</script>
@endsection
