@extends('layouts.app')
@section('title', 'GIF Compressor - Reduce Animated GIF Size Free | Azlaan Tools')
@section('meta_description', 'Compress animated GIFs free in your browser: shrink dimensions, tune quality and skip frames to cut file size. No upload, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">GIF Compressor</h1>
            <p class="lead text-muted">Make your GIF smaller without losing quality. The file is compressed right here in your browser — nothing is uploaded.</p>
            <div class="alert alert-info"><strong>How it works:</strong> this tool takes out all frames of the GIF, shrinks them and encodes them again. 3 ways to cut the size: smaller width, lighter quality, or skipping some frames.</div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drop your GIF file here or click to select it</p>
                        <p class="text-muted small mb-0">Only .gif files — max 20 MB</p>
                        <input type="file" id="fileInput" class="d-none" accept=".gif,image/gif">
                    </div>
                    <p id="fileInfo" class="small text-muted mt-2 mb-0 d-none"></p>

                    <div class="row g-3 mt-2">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="widthSelect">Max width</label>
                            <select id="widthSelect" class="form-select">
                                <option value="0" selected>Keep original</option>
                                <option value="800">800 px</option>
                                <option value="640">640 px</option>
                                <option value="480">480 px</option>
                                <option value="320">320 px</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="qualitySelect">Quality</label>
                            <select id="qualitySelect" class="form-select">
                                <option value="5" selected>High — large size</option>
                                <option value="10">Medium — balanced</option>
                                <option value="18">Low — smallest size</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" for="skipSelect">Frames</label>
                            <select id="skipSelect" class="form-select">
                                <option value="1" selected>Keep all frames</option>
                                <option value="2">Every second frame — much smaller</option>
                                <option value="3">Every third frame — smallest size</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" id="compressBtn" class="btn btn-primary btn-lg w-100 mt-3">Compress GIF</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="statusText" class="small text-muted mt-2"></div>

                    <div id="resultWrap" class="d-none mt-3">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Original</div><strong id="origSize">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Compressed</div><strong id="newSize" class="text-success">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Saved</div><strong id="savedPct" class="text-success">-</strong></div></div>
                        </div>
                        <p class="small text-muted text-center" id="frameInfo"></p>
                        <div class="text-center">
                            <img id="resultImg" class="img-fluid rounded border" alt="Compressed GIF preview" style="max-height: 320px;">
                        </div>
                        <a id="downloadBtn" href="#" download="compressed.gif" class="btn btn-success btn-lg w-100 mt-3">Download Compressed GIF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your GIF file.</li>
                <li>Choose width, quality and frames — smaller width and low quality = smaller size.</li>
                <li>Press <strong>Compress GIF</strong>, see the difference and download.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> your GIF is never uploaded to a server — everything happens in your browser.</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/gifshot@0.4.5/dist/gifshot.min.js"></script>
<script>
(function () {
    'use strict';

    /* ---- Pure-JS GIF89a frame decoder (no dependencies) ---- */
    var GifDec = (function () {
        function lzwDecode(minCodeSize, data, pixelCount) {
            var MAX = 4096;
            var clear = 1 << minCodeSize;
            var eoi = clear + 1;
            var codeSize = minCodeSize + 1;
            var codeMask = (1 << codeSize) - 1;
            var next = eoi + 1;
            var prefix = new Int32Array(MAX);
            var suffix = new Uint8Array(MAX);
            var stack = new Uint8Array(MAX + 1);
            var out = new Uint8Array(pixelCount);
            var op = 0, i;
            for (i = 0; i < clear; i++) { prefix[i] = 0; suffix[i] = i; }
            var datum = 0, bits = 0, dp = 0;
            var code, oldCode = -1, inCode, first = 0, sp = 0;
            while (op < pixelCount) {
                while (bits < codeSize) {
                    if (dp >= data.length) break;
                    datum |= data[dp++] << bits; bits += 8;
                }
                code = datum & codeMask; datum >>= codeSize; bits -= codeSize;
                if (code === clear) {
                    codeSize = minCodeSize + 1; codeMask = (1 << codeSize) - 1;
                    next = eoi + 1; oldCode = -1; continue;
                }
                if (code === eoi) break;
                if (oldCode === -1) {
                    first = suffix[code]; stack[sp++] = first; oldCode = code;
                } else {
                    inCode = code;
                    if (code >= next) { stack[sp++] = first; code = oldCode; }
                    while (code >= clear) { stack[sp++] = suffix[code]; code = prefix[code]; }
                    first = suffix[code];
                    stack[sp++] = first;
                    if (next < MAX) {
                        prefix[next] = oldCode; suffix[next] = first; next++;
                        if (next === (1 << codeSize) + 1 && codeSize < 12) {
                            codeSize++; codeMask = (1 << codeSize) - 1;
                        }
                    }
                    oldCode = inCode;
                }
                while (sp > 0) {
                    out[op++] = stack[--sp];
                    if (op >= pixelCount) break;
                }
            }
            return out;
        }

        function decodeGifFrames(bytes) {
            var pos = 0, len = bytes.length;
            function u16() { var v = bytes[pos] | (bytes[pos + 1] << 8); pos += 2; return v; }
            function str(n) { var s = ''; for (var i = 0; i < n; i++) s += String.fromCharCode(bytes[pos++]); return s; }
            function skipSub() { var n; do { n = bytes[pos++]; pos += n; } while (n !== 0); }
            function readSub() {
                var chunks = [], total = 0, n, i, o;
                while ((n = bytes[pos++]) !== 0) {
                    chunks.push(bytes.subarray(pos, pos + n)); total += n; pos += n;
                }
                var out = new Uint8Array(total); o = 0;
                for (i = 0; i < chunks.length; i++) { out.set(chunks[i], o); o += chunks[i].length; }
                return out;
            }
            if (str(3) !== 'GIF') throw new Error('not-gif');
            str(3);
            var w = u16(), h = u16();
            if (w <= 0 || h <= 0 || w * h > 4000000) throw new Error('bad-size');
            var packed = bytes[pos++]; pos += 2;
            var gct = null;
            if (packed & 0x80) {
                var gn = 3 * (2 << (packed & 7));
                gct = bytes.subarray(pos, pos + gn); pos += gn;
            }
            var frames = [];
            var cur = new Uint8ClampedArray(w * h * 4);
            var snapshot = null;
            var gce = { disposal: 0, delay: 10, trans: false, tIdx: 0 };
            var haveGce = false, frameCount = 0;
            var x, y, yy, xx, pi, di, ci, oi, src, pp;
            while (pos < len) {
                var sep = bytes[pos++];
                if (sep === 0x3B) break;
                if (sep === 0x21) {
                    var label = bytes[pos++];
                    if (label === 0xF9) {
                        pos++;
                        var gp = bytes[pos++];
                        gce.disposal = (gp >> 2) & 7;
                        gce.delay = u16();
                        gce.tIdx = bytes[pos++];
                        gce.trans = (gp & 1) !== 0;
                        pos++;
                        haveGce = true;
                    } else { skipSub(); }
                } else if (sep === 0x2C) {
                    var l = u16(), t = u16(), fw = u16(), fh = u16();
                    var ipack = bytes[pos++];
                    var inter = (ipack & 0x40) !== 0;
                    var ct = gct;
                    if (ipack & 0x80) {
                        var ln = 3 * (2 << (ipack & 7));
                        ct = bytes.subarray(pos, pos + ln); pos += ln;
                    }
                    if (!ct) throw new Error('no-color-table');
                    var minCs = bytes[pos++];
                    var data = readSub();
                    var idx = lzwDecode(minCs, data, fw * fh);
                    var rows = new Uint8Array(fw * fh);
                    if (inter) {
                        src = 0;
                        var passes = [[0, 8], [4, 8], [2, 4], [1, 2]];
                        for (pp = 0; pp < 4; pp++) {
                            for (y = passes[pp][0]; y < fh; y += passes[pp][1]) {
                                for (x = 0; x < fw; x++) rows[y * fw + x] = idx[src++];
                            }
                        }
                    } else { rows = idx; }
                    if (frameCount > 0) {
                        var prev = frames[frameCount - 1];
                        if (prev.disposal === 2) {
                            for (yy = prev.rect.t; yy < prev.rect.t + prev.rect.h; yy++) {
                                for (xx = prev.rect.l; xx < prev.rect.l + prev.rect.w; xx++) {
                                    oi = (yy * w + xx) * 4;
                                    cur[oi] = 0; cur[oi + 1] = 0; cur[oi + 2] = 0; cur[oi + 3] = 0;
                                }
                            }
                        } else if (prev.disposal === 3 && snapshot) {
                            cur.set(snapshot);
                        }
                    }
                    if (gce.disposal === 3) snapshot = new Uint8ClampedArray(cur);
                    for (yy = 0; yy < fh; yy++) {
                        for (xx = 0; xx < fw; xx++) {
                            pi = rows[yy * fw + xx];
                            if (gce.trans && pi === gce.tIdx) continue;
                            di = ((t + yy) * w + (l + xx)) * 4;
                            ci = pi * 3;
                            cur[di] = ct[ci]; cur[di + 1] = ct[ci + 1]; cur[di + 2] = ct[ci + 2]; cur[di + 3] = 255;
                        }
                    }
                    frames.push({
                        delay: (haveGce ? gce.delay : 10) * 10,
                        disposal: haveGce ? gce.disposal : 0,
                        rect: { l: l, t: t, w: fw, h: fh },
                        data: new Uint8ClampedArray(cur)
                    });
                    frameCount++;
                    gce = { disposal: 0, delay: 10, trans: false, tIdx: 0 };
                    haveGce = false;
                    if (frameCount > 400) throw new Error('too-many-frames');
                } else { throw new Error('bad-block'); }
            }
            if (!frames.length) throw new Error('no-frames');
            return { width: w, height: h, frames: frames };
        }
        return { decodeGifFrames: decodeGifFrames };
    })();

    /* ---- UI ---- */
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var fileInfo = document.getElementById('fileInfo');
    var widthSelect = document.getElementById('widthSelect');
    var qualitySelect = document.getElementById('qualitySelect');
    var skipSelect = document.getElementById('skipSelect');
    var compressBtn = document.getElementById('compressBtn');
    var errorBox = document.getElementById('errorBox');
    var statusText = document.getElementById('statusText');
    var resultWrap = document.getElementById('resultWrap');
    var origSize = document.getElementById('origSize');
    var newSize = document.getElementById('newSize');
    var savedPct = document.getElementById('savedPct');
    var frameInfo = document.getElementById('frameInfo');
    var resultImg = document.getElementById('resultImg');
    var downloadBtn = document.getElementById('downloadBtn');

    var fileBytes = null;
    var fileName = 'compressed.gif';
    var origByteCount = 0;

    function fmt(b) {
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(2) + ' MB';
    }
    function showError(m) {
        errorBox.textContent = m;
        errorBox.classList.remove('d-none');
        resultWrap.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function handleFile(f) {
        hideError();
        if (!f) return;
        var isGif = (f.type === 'image/gif') || /\.gif$/i.test(f.name || '');
        if (!isGif) { showError('Please choose a GIF file.'); return; }
        if (f.size > 20 * 1048576) { showError('File is larger than 20 MB. Try a smaller GIF.'); return; }
        fileName = (f.name || 'compressed.gif').replace(/\.gif$/i, '') + '-compressed.gif';
        var reader = new FileReader();
        reader.onload = function () {
            fileBytes = reader.result;
            origByteCount = fileBytes.byteLength;
            fileInfo.textContent = 'File: ' + (f.name || 'gif') + ' — ' + fmt(origByteCount);
            fileInfo.classList.remove('d-none');
            resultWrap.classList.add('d-none');
        };
        reader.onerror = function () { showError('Could not read the file. Please try again.'); };
        reader.readAsArrayBuffer(f);
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFile(fileInput.files[0]); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer) handleFile(e.dataTransfer.files[0]); });

    function frameToDataUrl(frame, w, h, tw, th) {
        var c1 = document.createElement('canvas');
        c1.width = w; c1.height = h;
        var ctx1 = c1.getContext('2d');
        var img = new ImageData(frame.data, w, h);
        ctx1.putImageData(img, 0, 0);
        var c2 = document.createElement('canvas');
        c2.width = tw; c2.height = th;
        c2.getContext('2d').drawImage(c1, 0, 0, tw, th);
        return c2.toDataURL('image/png');
    }

    compressBtn.addEventListener('click', function () {
        hideError();
        resultWrap.classList.add('d-none');
        if (!fileBytes) { showError('Please choose a GIF file first.'); return; }
        if (typeof gifshot === 'undefined') { showError('GIF library did not load. Check your internet and refresh.'); return; }
        compressBtn.disabled = true;
        statusText.textContent = 'Extracting frames…';
        setTimeout(function () {
            try {
                var dec = GifDec.decodeGifFrames(new Uint8Array(fileBytes));
                var frames = dec.frames;
                var skip = parseInt(skipSelect.value, 10);
                var kept = [];
                for (var i = 0; i < frames.length; i += skip) kept.push(frames[i]);
                var maxW = parseInt(widthSelect.value, 10);
                var tw = maxW > 0 ? Math.min(maxW, dec.width) : dec.width;
                var th = Math.max(1, Math.round(tw * dec.height / dec.width));
                var urls = [];
                for (var k = 0; k < kept.length; k++) urls.push(frameToDataUrl(kept[k], dec.width, dec.height, tw, th));
                var totalMs = 0;
                for (var d = 0; d < frames.length; d++) totalMs += frames[d].delay;
                var interval = Math.max(0.05, (totalMs / kept.length) / 1000);
                statusText.textContent = 'Re-encoding — ' + kept.length + ' frames…';
                setTimeout(function () {
                    gifshot.createGIF({
                        images: urls,
                        gifWidth: tw,
                        gifHeight: th,
                        interval: interval,
                        numWorkers: 2,
                        sampleInterval: parseInt(qualitySelect.value, 10)
                    }, function (obj) {
                        compressBtn.disabled = false;
                        statusText.textContent = '';
                        if (!obj || obj.error || !obj.image) {
                            showError('Encoding failed. Try a smaller width or fewer frames.');
                            return;
                        }
                        var b64 = obj.image;
                        var comma = b64.indexOf(',');
                        var estBytes = Math.round((b64.length - comma - 1) * 3 / 4);
                        origSize.textContent = fmt(origByteCount);
                        newSize.textContent = fmt(estBytes);
                        var saved = origByteCount > 0 ? Math.round((1 - estBytes / origByteCount) * 100) : 0;
                        savedPct.textContent = (saved >= 0 ? saved + '%' : '+' + Math.abs(saved) + '% bigger');
                        frameInfo.textContent = dec.frames.length + ' frames → ' + kept.length + ' frames, ' +
                            dec.width + 'x' + dec.height + ' → ' + tw + 'x' + th +
                            ', duration ' + (totalMs / 1000).toFixed(1) + 's kept.';
                        resultImg.src = b64;
                        downloadBtn.href = b64;
                        downloadBtn.setAttribute('download', fileName);
                        resultWrap.classList.remove('d-none');
                    });
                }, 30);
            } catch (err) {
                compressBtn.disabled = false;
                statusText.textContent = '';
                var msg = 'Could not decode this GIF. Try another GIF.';
                if (err && err.message === 'too-many-frames') msg = 'This GIF has too many frames (more than 400). Try a smaller GIF.';
                else if (err && err.message === 'bad-size') msg = 'This GIF is too large in dimensions. Try a smaller GIF.';
                showError(msg);
            }
        }, 30);
    });
})();
</script>
@endsection
