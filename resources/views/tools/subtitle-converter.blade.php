@extends('layouts.app')

@section('title', 'Subtitle Converter Online Free - SRT to VTT, VTT to SRT | Azlaan Tools')
@section('meta_description', 'Free subtitle converter: convert SRT to VTT and VTT to SRT, shift subtitle timing by seconds or milliseconds, export plain text dialogue. Paste or upload, instant, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Subtitle Converter (SRT ↔ VTT)</h1>
            <p class="lead text-muted">Convert subtitles between SRT and WebVTT, shift all timings earlier or later, or extract plain dialogue text — instantly, in your browser.</p>

            <div id="scAlert" class="alert alert-danger d-none" role="alert"></div>
            <div id="scSuccess" class="alert alert-success d-none" role="alert"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="scFile">Upload a subtitle file (.srt or .vtt) — or paste below</label>
                    <input type="file" id="scFile" class="form-control" accept=".srt,.vtt,text/plain">
                    <label class="form-label fw-semibold mt-3" for="scInput">Input subtitles</label>
                    <textarea id="scInput" class="form-control font-monospace" rows="10" placeholder="Paste SRT or VTT subtitles here..."></textarea>
                    <p class="small text-muted mt-2 mb-0">Detected format: <strong id="scDetected">—</strong> · Cues found: <strong id="scCues">0</strong></p>

                    <div class="row g-3 mt-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold" for="scShift">Shift all timings by (seconds, use minus for earlier)</label>
                            <input type="number" id="scShift" class="form-control" step="0.1" value="0" placeholder="e.g. 2.5 or -1.25">
                            <div class="form-text">Milliseconds work too: 0.5 = +500 ms.</div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" id="scApplyShift" class="btn btn-outline-primary">Apply Shift to Input</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex mt-3">
                        <button type="button" id="scToVtt" class="btn btn-success btn-lg">Convert to VTT</button>
                        <button type="button" id="scToSrt" class="btn btn-primary btn-lg">Convert to SRT</button>
                        <button type="button" id="scToTxt" class="btn btn-warning btn-lg">Dialogue Only (.txt)</button>
                    </div>

                    <label class="form-label fw-semibold mt-4" for="scOutput">Result</label>
                    <textarea id="scOutput" class="form-control font-monospace" rows="10" readonly placeholder="Converted subtitles will appear here..."></textarea>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="scDownload" class="btn btn-dark" disabled>⬇ Download Result</button>
                        <button type="button" id="scCopy" class="btn btn-outline-secondary" disabled>Copy Result</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-secondary"><strong>Privacy note:</strong> Your subtitles stay on your device — nothing is uploaded. Conversion happens entirely in your browser.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Upload an .srt or .vtt file, or paste the subtitle text directly.</li>
                    <li>If subtitles are out of sync, enter a shift in seconds (negative = earlier) and press Apply Shift.</li>
                    <li>Press Convert to VTT, Convert to SRT, or Dialogue Only for plain text.</li>
                    <li>Download or copy the result.</li>
                </ol>
                <p class="mb-0 small text-muted">Note: VTT styling cues and SRT position tags are simplified during conversion; timing and text are preserved. Timings never go below 00:00:00.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('scInput');
    var output = document.getElementById('scOutput');
    var alertBox = document.getElementById('scAlert');
    var successBox = document.getElementById('scSuccess');
    var downloadBtn = document.getElementById('scDownload');
    var copyBtn = document.getElementById('scCopy');
    var outName = 'subtitles.srt';
    var outType = 'text/plain';

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
    function pad(n, len) {
        var s = String(n);
        while (s.length < len) s = '0' + s;
        return s;
    }
    function parseTime(str) {
        var m = String(str).trim().match(/(?:(\d+):)?(\d{1,2}):(\d{2})[.,](\d{1,3})/);
        if (!m) return null;
        var h = parseInt(m[1] || '0', 10);
        var min = parseInt(m[2], 10);
        var sec = parseInt(m[3], 10);
        var ms = parseInt((m[4] + '000').slice(0, 3), 10);
        return ((h * 60 + min) * 60 + sec) * 1000 + ms;
    }
    function formatTime(ms, dot) {
        if (ms < 0) ms = 0;
        var h = Math.floor(ms / 3600000);
        var min = Math.floor(ms % 3600000 / 60000);
        var sec = Math.floor(ms % 60000 / 1000);
        var mmm = Math.floor(ms % 1000);
        return pad(h, 2) + ':' + pad(min, 2) + ':' + pad(sec, 2) + (dot ? '.' : ',') + pad(mmm, 3);
    }
    function parseCues(text) {
        var clean = String(text || '').replace(/\r\n/g, '\n').replace(/\r/g, '\n').replace(/^WEBVTT[^\n]*\n/, '');
        var blocks = clean.split(/\n\n+/);
        var cues = [];
        blocks.forEach(function (block) {
            var lines = block.split('\n').filter(function (l) { return l.trim() !== ''; } );
            if (!lines.length) return;
            var timeIdx = -1;
            for (var i = 0; i < lines.length; i++) {
                if (lines[i].indexOf('-->') !== -1) { timeIdx = i; break; }
            }
            if (timeIdx === -1) return;
            var parts = lines[timeIdx].split('-->');
            var start = parseTime(parts[0]);
            var endPart = (parts[1] || '').trim().split(/\s+/)[0];
            var end = parseTime(endPart);
            if (start === null || end === null) return;
            var bodyLines = lines.slice(timeIdx + 1);
            cues.push({ start: start, end: end, text: bodyLines.join('\n') });
        } );
        return cues;
    }
    function detect(text) {
        if (/^\s*WEBVTT/.test(text)) return 'WebVTT (.vtt)';
        if (/\d{2}:\d{2}:\d{2},\d{3}\s*-->/.test(text)) return 'SubRip (.srt)';
        if (/\d{2}:\d{2}:\d{2}\.\d{3}\s*-->/.test(text)) return 'WebVTT timing (no header)';
        return '—';
    }
    function refreshInfo() {
        var cues = parseCues(input.value);
        document.getElementById('scDetected').textContent = detect(input.value);
        document.getElementById('scCues').textContent = cues.length;
        return cues;
    }
    function shiftCues(cues, shiftMs) {
        return cues.map(function (c) {
            return { start: Math.max(0, c.start + shiftMs), end: Math.max(0, c.end + shiftMs), text: c.text };
        } );
    }
    function build(cues, format) {
        if (format === 'txt') {
            return cues.map(function (c) { return c.text; } ).join('\n');
        }
        var out = [];
        if (format === 'vtt') out.push('WEBVTT', '');
        cues.forEach(function (c, i) {
            if (format === 'srt') out.push(String(i + 1));
            out.push(formatTime(c.start, format === 'vtt') + ' --> ' + formatTime(c.end, format === 'vtt'));
            out.push(c.text);
            out.push('');
        } );
        return out.join('\n');
    }
    function setResult(text, name, msg) {
        output.value = text;
        outName = name;
        downloadBtn.disabled = !text;
        copyBtn.disabled = !text;
        if (text) showSuccess(msg);
    }
    function currentShiftMs() {
        return Math.round((Number(document.getElementById('scShift').value) || 0) * 1000);
    }
    function convert(format) {
        alertBox.classList.add('d-none');
        var cues = refreshInfo();
        if (!cues.length) { showError('No subtitle cues found. Please paste or upload valid SRT/VTT text first.'); return; }
        var shift = currentShiftMs();
        if (shift !== 0) cues = shiftCues(cues, shift);
        if (format === 'vtt') setResult(build(cues, 'vtt'), 'subtitles.vtt', 'Converted to WebVTT — ' + cues.length + ' cues.');
        else if (format === 'srt') setResult(build(cues, 'srt'), 'subtitles.srt', 'Converted to SRT — ' + cues.length + ' cues.');
        else setResult(build(cues, 'txt'), 'dialogue.txt', 'Dialogue text extracted — ' + cues.length + ' cues, timings removed.');
    }

    document.getElementById('scFile').addEventListener('change', function (ev) {
        var file = ev.target.files && ev.target.files[0];
        if (!file) return;
        file.text().then(function (text) {
            input.value = text;
            refreshInfo();
            showSuccess('File loaded: ' + file.name);
        } ).catch(function () { showError('Could not read that file.'); } );
    } );
    input.addEventListener('input', refreshInfo);
    document.getElementById('scApplyShift').addEventListener('click', function () {
        var cues = refreshInfo();
        if (!cues.length) { showError('No subtitle cues found to shift.'); return; }
        var shift = currentShiftMs();
        if (shift === 0) { showError('Enter a shift value first, e.g. 2.5 or -1.25 seconds.'); return; }
        var shifted = shiftCues(cues, shift);
        var isVtt = /^\s*WEBVTT/.test(input.value);
        input.value = build(shifted, isVtt ? 'vtt' : 'srt').replace(/^WEBVTT\n\n/, 'WEBVTT\n\n');
        if (isVtt && input.value.indexOf('WEBVTT') !== 0) input.value = 'WEBVTT\n\n' + input.value;
        refreshInfo();
        showSuccess('All timings shifted by ' + (shift / 1000) + 's inside the input box.');
    } );
    document.getElementById('scToVtt').addEventListener('click', function () { convert('vtt'); } );
    document.getElementById('scToSrt').addEventListener('click', function () { convert('srt'); } );
    document.getElementById('scToTxt').addEventListener('click', function () { convert('txt'); } );
    downloadBtn.addEventListener('click', function () {
        var blob = new Blob([output.value], { type: outType + ';charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = outName;
        document.body.appendChild(a);
        a.click();
        a.remove();
    } );
    copyBtn.addEventListener('click', function () {
        output.select();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(output.value).then(function () { showSuccess('Result copied to clipboard.'); } );
        } else {
            document.execCommand('copy');
            showSuccess('Result copied to clipboard.');
        }
    } );
    refreshInfo();
} )();
</script>
@endsection
