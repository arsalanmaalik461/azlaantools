@extends('layouts.app')

@section('title', 'JSON Formatter & Validator Online Free | Azlaan Tools')
@section('meta_description', 'Free JSON formatter and validator: pretty-print, minify and validate JSON instantly with clear error messages. No signup, everything runs in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">JSON Formatter &amp; Validator</h1>
    <p class="lead">Paste messy JSON to format it beautifully, minify it, or just check that it is valid — free, instant and private.</p>

    <div id="errorBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <label for="inputText" class="form-label fw-semibold mb-0">JSON Input</label>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="sampleBtn">Load Sample JSON</button>
            </div>
            <textarea class="form-control font-monospace mt-2" id="inputText" rows="10" placeholder="Paste your JSON here..."></textarea>

            <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                <button type="button" class="btn btn-primary" id="formatBtn">Format</button>
                <button type="button" class="btn btn-outline-primary" id="minifyBtn">Minify</button>
                <button type="button" class="btn btn-outline-success" id="validateBtn">Validate</button>
                <label for="indentSelect" class="form-label mb-0 ms-2">Indent:</label>
                <select class="form-select w-auto" id="indentSelect">
                    <option value="2" selected>2 spaces</option>
                    <option value="4">4 spaces</option>
                    <option value="tab">Tab</option>
                </select>
                <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
            </div>

            <div id="statsBar" class="alert alert-light border mt-3 mb-0 d-none small"></div>

            <label for="outputText" class="form-label fw-semibold mt-3">Output</label>
            <textarea class="form-control font-monospace" id="outputText" rows="12" readonly placeholder="Formatted JSON will appear here..."></textarea>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Output</button>
                <button type="button" class="btn btn-outline-success btn-sm" id="downloadBtn">Download .json</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your JSON is parsed only in your browser — it is never uploaded or stored anywhere.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Paste your JSON into the input box (or click <strong>Load Sample JSON</strong> to try it out).</li>
        <li>Click <strong>Format</strong> to pretty-print it, <strong>Minify</strong> to compress it, or <strong>Validate</strong> to only check it.</li>
        <li>If the JSON is valid you will see a green confirmation with key count and size stats; if not, the exact parser error is shown in red.</li>
        <li>Copy the output or download it as a .json file.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('inputText');
    var output = document.getElementById('outputText');
    var errorBox = document.getElementById('errorBox');
    var successBox = document.getElementById('successBox');
    var statsBar = document.getElementById('statsBar');
    var indentSelect = document.getElementById('indentSelect');

    var sampleJson = '{"name":"Azlaan Tools","country":"Pakistan","free":true,"tools":["slug-generator","json-formatter","base64"],"meta":{"version":1,"users":0}}';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showValid(statsText) {
        successBox.innerHTML = '';
        var strong = document.createElement('strong');
        strong.textContent = 'Valid JSON ✓ ';
        successBox.appendChild(strong);
        successBox.appendChild(document.createTextNode(statsText));
        successBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function hideAlerts() {
        errorBox.classList.add('d-none');
        successBox.classList.add('d-none');
        statsBar.classList.add('d-none');
    }

    function countKeys(value) {
        var count = 0;
        if (Array.isArray(value)) {
            for (var i = 0; i < value.length; i++) count += countKeys(value[i]);
        } else if (value !== null && typeof value === 'object') {
            var keys = Object.keys(value);
            count += keys.length;
            for (var j = 0; j < keys.length; j++) count += countKeys(value[keys[j]]);
        }
        return count;
    }
    function formatSize(str) {
        var bytes = new Blob([str]).size;
        if (bytes < 1024) return bytes + ' B';
        return (bytes / 1024).toFixed(2) + ' KB';
    }
    function statsFor(parsed, sourceStr) {
        var type = Array.isArray(parsed) ? 'Array' : (parsed === null ? 'Null' : typeof parsed);
        return 'Type: ' + type + ' | Total keys: ' + countKeys(parsed) + ' | Size: ' + formatSize(sourceStr) + ' | Characters: ' + sourceStr.length;
    }
    function showStats(parsed, sourceStr) {
        statsBar.textContent = statsFor(parsed, sourceStr);
        statsBar.classList.remove('d-none');
    }
    function getIndent() {
        return indentSelect.value === 'tab' ? '\t' : parseInt(indentSelect.value, 10);
    }
    function parseInput() {
        if (!input.value.trim()) { showError('Please paste some JSON first.'); return { ok: false, value: null }; }
        try {
            return { ok: true, value: JSON.parse(input.value) };
        } catch (e) {
            output.value = '';
            statsBar.classList.add('d-none');
            showError('Invalid JSON: ' + e.message);
            return { ok: false, value: null };
        }
    }

    document.getElementById('formatBtn').addEventListener('click', function () {
        hideAlerts();
        var result = parseInput();
        if (!result.ok) return;
        var parsed = result.value;
        var pretty = JSON.stringify(parsed, null, getIndent());
        output.value = pretty;
        showValid('Formatted successfully.');
        showStats(parsed, pretty);
    });

    document.getElementById('minifyBtn').addEventListener('click', function () {
        hideAlerts();
        var result = parseInput();
        if (!result.ok) return;
        var parsed = result.value;
        var min = JSON.stringify(parsed);
        output.value = min;
        showValid('Minified successfully.');
        showStats(parsed, min);
    });

    document.getElementById('validateBtn').addEventListener('click', function () {
        hideAlerts();
        var result = parseInput();
        if (!result.ok) return;
        var parsed = result.value;
        output.value = JSON.stringify(parsed, null, getIndent());
        showValid('Your JSON is valid.');
        showStats(parsed, input.value);
    });

    document.getElementById('sampleBtn').addEventListener('click', function () {
        input.value = sampleJson;
        hideAlerts();
        input.focus();
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        input.value = ''; output.value = ''; hideAlerts(); input.focus();
    });

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!output.value) { showError('Nothing to copy yet — format or validate some JSON first.'); return; }
        try { await navigator.clipboard.writeText(output.value); }
        catch (e) { output.select(); document.execCommand('copy'); }
        showValid('Output copied to clipboard.');
    });

    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!output.value) { showError('Nothing to download yet — format some JSON first.'); return; }
        var blob = new Blob([output.value], { type: 'application/json;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = 'formatted.json';
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
    });
})();
</script>
@endsection
