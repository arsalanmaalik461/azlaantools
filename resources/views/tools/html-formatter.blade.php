@extends('layouts.app')

@section('title', 'HTML Formatter Online Free - Beautify and Minify HTML | Azlaan Tools')
@section('meta_description', 'Free HTML formatter: beautify messy HTML with clean indentation or minify it with a basic compressor. Choose indent size. Runs 100% in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">HTML Formatter</h1>
            <p class="lead text-muted">Beautify messy HTML or minify it. This tool runs 100% in your browser — nothing is uploaded or saved.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="htmlInput">HTML Input</label>
                    <textarea id="htmlInput" class="form-control font-monospace" rows="10" placeholder="Paste HTML here..."></textarea>
                    <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                        <button type="button" class="btn btn-primary" id="beautifyBtn">Beautify</button>
                        <button type="button" class="btn btn-outline-primary" id="minifyBtn">Minify (basic)</button>
                        <label class="form-label mb-0 ms-2" for="indentSelect">Indent:</label>
                        <select id="indentSelect" class="form-select w-auto"><option value="2">2 spaces</option><option value="4" selected>4 spaces</option></select>
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Load Sample</button>
                        <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
                    </div>
                    <div id="sizeInfo" class="alert alert-light border mt-3 small d-none"></div>
                    <label class="form-label fw-semibold mt-3" for="htmlOutput">Output</label>
                    <textarea id="htmlOutput" class="form-control font-monospace" rows="12" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Output</button><button type="button" class="btn btn-outline-success btn-sm" id="downloadBtn">Download .html</button></div>
                    <p class="small text-muted mt-2 mb-0">Minify mode is a basic regex-based compressor (removes comments and extra whitespace) — for production builds use a dedicated build tool.</p>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Paste your HTML into the input box.</li>
                <li>Click <strong>Beautify</strong> with your preferred indent size, or <strong>Minify (basic)</strong> to compress it.</li>
                <li>Check the size comparison, then copy the output or download it as an .html file.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/js-beautify@1.15.1/js/lib/beautify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-beautify@1.15.1/js/lib/beautify-css.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-beautify@1.15.1/js/lib/beautify-html.js"></script>
<script>
(function () {
    var input = document.getElementById('htmlInput'); var output = document.getElementById('htmlOutput'); var sizeInfo = document.getElementById('sizeInfo');
    function showSize(before, after) { sizeInfo.textContent = 'Before: ' + before.length + ' chars | After: ' + after.length + ' chars'; sizeInfo.classList.remove('d-none'); }
    function fallbackBeautify(html, indentSize) {
        var indent = ''; for (var i = 0; i < indentSize; i++) indent += ' ';
        var formatted = html.replace(/>\s+</g, '><').trim().replace(/</g, '\n<');
        var lines = formatted.split('\n'); var level = 0; var out = [];
        var voidTags = /^(area|base|br|col|embed|hr|img|input|link|meta|param|source|track|wbr)/i;
        lines.forEach(function (line) {
            var trimmed = line.trim(); if (!trimmed) return;
            if (/^<\//.test(trimmed)) level = Math.max(level - 1, 0);
            out.push(indent.repeat(level) + trimmed);
            var opens = /^<([a-zA-Z]+)/.test(trimmed) && !/<\/[a-zA-Z]+>\s*$/.test(trimmed.replace(/^<[^>]+>/, '')) && !/\/>$/.test(trimmed) && !voidTags.test(trimmed.replace(/^</, '')) && !/^<!/.test(trimmed);
            var single = (trimmed.match(/</g) || []).length <= 1;
            if (opens && single && !/^<\//.test(trimmed)) level++;
        });
        return out.join('\n');
    }
    document.getElementById('beautifyBtn').addEventListener('click', function () {
        var size = parseInt(document.getElementById('indentSelect').value, 10);
        var result;
        if (window.html_beautify) { result = window.html_beautify(input.value, { indent_size: size, wrap_line_length: 0 }); }
        else { result = fallbackBeautify(input.value, size); }
        output.value = result; showSize(input.value, result);
    });
    document.getElementById('minifyBtn').addEventListener('click', function () {
        var result = input.value.replace(/<!--[\s\S]*?-->/g, '').replace(/\n\s*/g, '').replace(/\s{2,}/g, ' ').replace(/>\s+</g, '><').trim();
        output.value = result; showSize(input.value, result);
    });
    document.getElementById('sampleBtn').addEventListener('click', function () { input.value = '<html><head><title>Hi</title></head><body><div><p>Hello <b>world</b></p><ul><li>One</li><li>Two</li></ul></div></body></html>'; });
    document.getElementById('clearBtn').addEventListener('click', function () { input.value = ''; output.value = ''; sizeInfo.classList.add('d-none'); });
    document.getElementById('copyBtn').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(output.value); });
    document.getElementById('downloadBtn').addEventListener('click', function () { var blob = new Blob([output.value], { type: 'text/html' }); var url = URL.createObjectURL(blob); var a = document.createElement('a'); a.href = url; a.download = 'formatted.html'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(url); }, 2000); });
})();
</script>
@endsection
