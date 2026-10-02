@extends('layouts.app')

@section('title', 'Keyword List Combiner - Azlaan Tools')
@section('meta_description', 'Merge two keyword lists to build long-tail keyword combinations — free online SEO helper.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Keyword List Combiner</h1>
            <p class="lead text-muted">Write two lists — the tool joins every word with every word to make combinations. Best for content ideas and long-tail keywords.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="listA" class="form-label fw-semibold">List A (one word per line)</label>
                            <textarea class="form-control" id="listA" rows="7" placeholder="solar&#10;electricity bill&#10;inverter"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="listB" class="form-label fw-semibold">List B (one word per line)</label>
                            <textarea class="form-control" id="listB" rows="7" placeholder="price in pakistan&#10;installation cost&#10;2026"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="mode" class="form-label fw-semibold">Combination mode</label>
                            <select class="form-select" id="mode">
                                <option value="ab">A + B</option>
                                <option value="ba">B + A</option>
                                <option value="both">Both (A+B and B+A)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="sep" class="form-label fw-semibold">Separator</label>
                            <select class="form-select" id="sep">
                                <option value=" ">Space</option>
                                <option value="-">Hyphen (-)</option>
                                <option value="_">Underscore (_)</option>
                                <option value="">None (join directly)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="casing" class="form-label fw-semibold">Case</label>
                            <select class="form-select" id="casing">
                                <option value="keep">As typed</option>
                                <option value="lower">lowercase</option>
                                <option value="title">Title Case</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="prefix" class="form-label fw-semibold">Prefix (optional)</label>
                            <input type="text" class="form-control" id="prefix" placeholder="e.g. best">
                        </div>
                        <div class="col-md-6">
                            <label for="suffix" class="form-label fw-semibold">Suffix (optional)</label>
                            <input type="text" class="form-control" id="suffix" placeholder="e.g. free">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dedupe" checked>
                                <label class="form-check-label" for="dedupe">Remove duplicate combinations</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Make Combinations</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Result (<span id="countOut">0</span> keywords)</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copyBtn">Copy</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="dlBtn">Download .txt</button>
                            </div>
                        </div>
                        <textarea class="form-control" id="output" rows="10" readonly></textarea>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your main keywords in List A (one per line).</li>
                <li>Write modifiers in List B (city, year, price etc.).</li>
                <li>Select the mode and separator, then press <strong>Make Combinations</strong>.</li>
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

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function lines(id) {
        return document.getElementById(id).value.split('\n')
            .map(function (s) { return s.trim(); })
            .filter(function (s) { return s.length > 0; });
    }
    function applyCase(s, c) {
        if (c === 'lower') return s.toLowerCase();
        if (c === 'title') return s.replace(/\w\S*/g, function (w) { return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); });
        return s;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var a = lines('listA'), b = lines('listB');
        if (!a.length) { showError('Write at least one keyword in List A.'); return; }
        if (!b.length) { showError('Write at least one keyword in List B.'); return; }
        var mode = document.getElementById('mode').value;
        var sep = document.getElementById('sep').value;
        var casing = document.getElementById('casing').value;
        var prefix = document.getElementById('prefix').value.trim();
        var suffix = document.getElementById('suffix').value.trim();
        var dedupe = document.getElementById('dedupe').checked;

        var out = [];
        function push(x, y) {
            var s = x + sep + y;
            if (prefix) s = prefix + ' ' + s;
            if (suffix) s = s + ' ' + suffix;
            out.push(applyCase(s, casing));
        }
        a.forEach(function (x) {
            b.forEach(function (y) {
                if (mode === 'ab' || mode === 'both') push(x, y);
                if (mode === 'ba' || mode === 'both') push(y, x);
            });
        });
        if (dedupe) {
            var seen = {}, uniq = [];
            out.forEach(function (s) { var k = s.toLowerCase(); if (!seen[k]) { seen[k] = 1; uniq.push(s); } });
            out = uniq;
        }
        if (out.length > 20000) { showError('Too many combinations (' + out.length + ') — make the lists shorter.'); return; }
        document.getElementById('output').value = out.join('\n');
        document.getElementById('countOut').textContent = out.length;
        results.classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var ta = document.getElementById('output');
        if (!ta.value) { showError('Make the combinations first.'); return; }
        navigator.clipboard.writeText(ta.value).then(function () {
            document.getElementById('copyBtn').textContent = 'Copied!';
            setTimeout(function () { document.getElementById('copyBtn').textContent = 'Copy'; }, 1500);
        }, function () { showError('Could not copy.'); });
    });

    document.getElementById('dlBtn').addEventListener('click', function () {
        var ta = document.getElementById('output');
        if (!ta.value) { showError('Make the combinations first.'); return; }
        var blob = new Blob([ta.value], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'keyword-combinations.txt';
        document.body.appendChild(a); a.click(); a.remove();
    });
})();
</script>
@endsection
