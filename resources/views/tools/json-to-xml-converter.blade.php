@extends('layouts.app')

@section('title', 'JSON to XML Converter - Azlaan Tools')
@section('meta_description', 'Convert JSON data to clean XML — with options, validation, and download, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">JSON to XML Converter</h1>
            <p class="lead text-muted">Paste JSON and get XML — with options for the root element, attributes, and indentation. Everything runs in the browser, no upload.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="jsonInput" class="form-label fw-semibold">JSON input</label>
                        <textarea class="form-control font-monospace" id="jsonInput" rows="8" placeholder='{"name": "Azlaan", "city": "Lahore", "items": [1, 2]}'></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="rootName" class="form-label fw-semibold">Root element</label>
                            <input type="text" class="form-control" id="rootName" value="root" placeholder="root">
                        </div>
                        <div class="col-md-4">
                            <label for="arrayName" class="form-label fw-semibold">Array item tag</label>
                            <input type="text" class="form-control" id="arrayName" value="item" placeholder="item">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="declCheck" checked>
                                <label class="form-check-label" for="declCheck">XML declaration</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Convert to XML</button>
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Sample</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label for="xmlOutput" class="form-label fw-semibold">XML output</label>
                        <textarea class="form-control font-monospace" id="xmlOutput" rows="10" readonly></textarea>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy XML</button>
                            <button type="button" class="btn btn-success" id="dlBtn">Download .xml</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste your JSON in the box above (or click Sample).</li>
                <li>Set the root element and array item tag names — keys starting with "_" become attributes (like "_id" -&gt; id="...").</li>
                <li>Click <strong>Convert to XML</strong>, then copy or download it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var jsonInput = document.getElementById('jsonInput');
    var xmlOutput = document.getElementById('xmlOutput');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function validTag(s) {
        return /^[A-Za-z_][A-Za-z0-9_.-]*$/.test(s) ? s : 'node';
    }
    function esc(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&apos;');
    }
    function pad(level) { return '  '.repeat(level); }

    function toXml(value, tag, arrayTag, level, lines) {
        var t = validTag(tag);
        if (value === null || value === undefined) {
            lines.push(pad(level) + '<' + t + '/>');
        } else if (Array.isArray(value)) {
            if (value.length === 0) { lines.push(pad(level) + '<' + t + '/>'); return; }
            lines.push(pad(level) + '<' + t + '>');
            value.forEach(function (v) { toXml(v, arrayTag, arrayTag, level + 1, lines); });
            lines.push(pad(level) + '</' + t + '>');
        } else if (typeof value === 'object') {
            var keys = Object.keys(value);
            var attrs = '', children = [];
            keys.forEach(function (k) {
                if (k.charAt(0) === '_') {
                    attrs += ' ' + validTag(k.slice(1)) + '="' + esc(value[k]) + '"';
                } else {
                    children.push(k);
                }
            });
            if (children.length === 0) {
                lines.push(pad(level) + '<' + t + attrs + '/>');
            } else {
                lines.push(pad(level) + '<' + t + attrs + '>');
                children.forEach(function (k) { toXml(value[k], k, arrayTag, level + 1, lines); });
                lines.push(pad(level) + '</' + t + '>');
            }
        } else {
            lines.push(pad(level) + '<' + t + '>' + esc(value) + '</' + t + '>');
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var raw = jsonInput.value.trim();
        if (!raw) { showError('Please enter a value — paste some JSON first.'); return; }
        var data;
        try {
            data = JSON.parse(raw);
        } catch (e) {
            showError('Invalid JSON: ' + e.message);
            return;
        }
        var root = document.getElementById('rootName').value.trim() || 'root';
        var arrayTag = document.getElementById('arrayName').value.trim() || 'item';
        var lines = [];
        if (document.getElementById('declCheck').checked) {
            lines.push('<?xml version="1.0" encoding="UTF-8"?>');
        }
        toXml(data, root, arrayTag, 0, lines);
        xmlOutput.value = lines.join('\n');
        results.classList.remove('d-none');
    });

    document.getElementById('sampleBtn').addEventListener('click', function () {
        hideError();
        jsonInput.value = '{\n  "_id": "101",\n  "name": "Azlaan",\n  "city": "Lahore",\n  "services": ["Solar", "AC", "Electrical"],\n  "contact": { "phone": "03008987448", "active": true }\n}';
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var b = this;
        xmlOutput.select();
        try { document.execCommand('copy'); b.textContent = 'Copied!'; }
        catch (e) { b.textContent = 'Copy failed'; }
        setTimeout(function () { b.textContent = 'Copy XML'; }, 1500);
    });

    document.getElementById('dlBtn').addEventListener('click', function () {
        var blob = new Blob([xmlOutput.value], { type: 'application/xml' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'converted.xml';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); document.body.removeChild(a); }, 100);
    });
})();
</script>
@endsection
