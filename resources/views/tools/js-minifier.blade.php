@extends('layouts.app')

@section('title', 'JavaScript Minifier & Beautifier Online Free | Azlaan Tools')
@section('meta_description', 'Free JavaScript minifier and beautifier: compress JS with Terser or pretty-print it, with before and after size comparison. Runs 100% in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">JavaScript Minifier &amp; Beautifier</h1>
            <p class="lead text-muted">Minify JavaScript to shrink file size, or beautify compressed code to read it. This tool runs 100% in your browser — nothing is uploaded or saved.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="jsInput">JavaScript Input</label>
                    <textarea id="jsInput" class="form-control font-monospace" rows="10" placeholder="Paste JavaScript here..."></textarea>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="minifyBtn">Minify</button>
                        <button type="button" class="btn btn-outline-primary" id="beautifyBtn">Beautify</button>
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Load Sample</button>
                        <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
                    </div>
                    <div id="jsError" class="alert alert-danger mt-3 d-none"></div>
                    <div id="sizeInfo" class="alert alert-light border mt-3 small d-none"></div>
                    <label class="form-label fw-semibold mt-3" for="jsOutput">Output</label>
                    <textarea id="jsOutput" class="form-control font-monospace" rows="12" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Output</button><button type="button" class="btn btn-outline-success btn-sm" id="downloadBtn">Download .js</button></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Paste your JavaScript into the input box.</li>
                <li>Click <strong>Minify</strong> to compress it with Terser, or <strong>Beautify</strong> to format it readably.</li>
                <li>Compare before/after sizes and the percentage saved.</li>
                <li>Copy the output or download it as a .js file.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/js-beautify@1.15.1/js/lib/beautify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/terser@5.31.6/dist/bundle.min.js"></script>
<script>
(function () {
    var input = document.getElementById('jsInput'); var output = document.getElementById('jsOutput');
    var errEl = document.getElementById('jsError'); var sizeInfo = document.getElementById('sizeInfo');
    function showSize(before, after) {
        var saved = before.length ? Math.round((1 - after.length / before.length) * 100) : 0;
        sizeInfo.textContent = 'Before: ' + before.length + ' chars | After: ' + after.length + ' chars | Saved: ' + saved + '%';
        sizeInfo.classList.remove('d-none');
    }
    function showErr(msg) { errEl.textContent = msg; errEl.classList.remove('d-none'); }
    document.getElementById('minifyBtn').addEventListener('click', async function () {
        errEl.classList.add('d-none');
        if (!input.value.trim()) { showErr('Please paste some JavaScript first.'); return; }
        if (!window.Terser || !window.Terser.minify) { showErr('Terser library could not be loaded. Check your connection and try Beautify instead.'); return; }
        try {
            var result = await window.Terser.minify(input.value);
            if (result.error) { showErr('Minify error: ' + result.error.message); return; }
            output.value = result.code || ''; showSize(input.value, output.value);
        } catch (e) { showErr('Minify error: ' + e.message); }
    });
    document.getElementById('beautifyBtn').addEventListener('click', function () {
        errEl.classList.add('d-none');
        if (window.js_beautify) { output.value = window.js_beautify(input.value, { indent_size: 2 }); }
        else { output.value = input.value; showErr('Beautifier library could not be loaded — showing input unchanged.'); }
        showSize(input.value, output.value);
    });
    document.getElementById('sampleBtn').addEventListener('click', function () { input.value = 'function greet(name) {\n    var message = "Hello, " + name + "!";\n    console.log(message);\n    return message;\n}\ngreet("World");'; });
    document.getElementById('clearBtn').addEventListener('click', function () { input.value = ''; output.value = ''; errEl.classList.add('d-none'); sizeInfo.classList.add('d-none'); });
    document.getElementById('copyBtn').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(output.value); });
    document.getElementById('downloadBtn').addEventListener('click', function () { var blob = new Blob([output.value], { type: 'text/javascript' }); var url = URL.createObjectURL(blob); var a = document.createElement('a'); a.href = url; a.download = 'script.js'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(url); }, 2000); });
})();
</script>
@endsection
