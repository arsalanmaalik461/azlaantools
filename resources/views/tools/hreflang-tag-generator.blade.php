@extends('layouts.app')

@section('title', 'Hreflang Tag Generator - Azlaan Tools')
@section('meta_description', 'Generate correct hreflang link tags for multilingual websites free online. Add language versions and copy ready SEO code.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Hreflang Tag Generator</h1>
            <p class="lead text-muted">Choose the URL and language for each version of your multi-language website — correct hreflang tags will be made. Tell Google which page is for which audience.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="rowsWrap"></div>
                    <button type="button" class="btn btn-outline-primary w-100 mb-3" id="addRowBtn">+ Add language version</button>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="includeDefault">
                        <label class="form-check-label" for="includeDefault">Also add the x-default tag (default / fallback page)</label>
                    </div>
                    <div class="mb-3 d-none" id="defaultUrlWrap">
                        <label for="defaultUrl" class="form-label fw-semibold">Default page URL</label>
                        <input type="url" class="form-control" id="defaultUrl" placeholder="https://example.com/">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Hreflang Tags</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="h5 mb-0">Your code</h2>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" id="copyBtn">Copy</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="dlBtn">Download .txt</button>
                            </div>
                        </div>
                        <pre class="border rounded p-3 bg-light" id="codeOut" style="white-space: pre-wrap; font-size: 0.85rem;"></pre>
                        <div class="alert alert-success d-none py-2" id="copiedMsg" role="status">Copied!</div>
                        <p class="small text-muted">Put this code in the <code>&lt;head&gt;</code> section of every page. You must put the <strong>full set</strong> (including itself) on every language version — putting it on only one page will not work.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>For each language version, choose the language/region and write its full URL.</li>
                <li>Use "Add language version" to include more versions.</li>
                <li>Press the button, copy the code and put it in the &lt;head&gt; of every page.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var rowsWrap = document.getElementById('rowsWrap');
    var addRowBtn = document.getElementById('addRowBtn');
    var includeDefault = document.getElementById('includeDefault');
    var defaultUrlWrap = document.getElementById('defaultUrlWrap');
    var defaultUrl = document.getElementById('defaultUrl');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var codeOut = document.getElementById('codeOut');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var copiedMsg = document.getElementById('copiedMsg');

    var locales = [
        ['en', 'English (generic)'], ['en-US', 'English — United States'], ['en-GB', 'English — United Kingdom'],
        ['ur', 'Urdu (generic)'], ['ur-PK', 'Urdu — Pakistan'],
        ['ar', 'Arabic (generic)'], ['ar-SA', 'Arabic — Saudi Arabia'], ['ar-AE', 'Arabic — UAE'],
        ['fr', 'French (generic)'], ['fr-FR', 'French — France'],
        ['de', 'German'], ['de-DE', 'German — Germany'],
        ['es', 'Spanish (generic)'], ['es-ES', 'Spanish — Spain'], ['es-MX', 'Spanish — Mexico'],
        ['it', 'Italian'], ['pt', 'Portuguese (generic)'], ['pt-BR', 'Portuguese — Brazil'],
        ['zh-CN', 'Chinese — Simplified'], ['zh-TW', 'Chinese — Traditional'],
        ['ja', 'Japanese'], ['ko', 'Korean'], ['ru', 'Russian'], ['tr', 'Turkish'],
        ['hi', 'Hindi'], ['bn', 'Bengali'], ['nl', 'Dutch'], ['sv', 'Swedish'],
        ['no', 'Norwegian'], ['da', 'Danish'], ['fi', 'Finnish'], ['pl', 'Polish'],
        ['id', 'Indonesian'], ['ms', 'Malay'], ['th', 'Thai'], ['vi', 'Vietnamese'],
        ['fa', 'Persian (Farsi)']
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function makeRow() {
        var row = document.createElement('div');
        row.className = 'row g-2 mb-2 lang-row';
        var col1 = document.createElement('div');
        col1.className = 'col-5';
        var sel = document.createElement('select');
        sel.className = 'form-select lang-sel';
        locales.forEach(function (l) {
            var o = document.createElement('option');
            o.value = l[0];
            o.textContent = l[1];
            sel.appendChild(o);
        });
        var col2 = document.createElement('div');
        col2.className = 'col-6';
        var inp = document.createElement('input');
        inp.type = 'url';
        inp.className = 'form-control lang-url';
        inp.placeholder = 'https://example.com/en/';
        var col3 = document.createElement('div');
        col3.className = 'col-1 d-flex align-items-center';
        var del = document.createElement('button');
        del.type = 'button';
        del.className = 'btn btn-sm btn-outline-danger';
        del.textContent = 'x';
        del.setAttribute('aria-label', 'Remove row');
        del.addEventListener('click', function () {
            if (rowsWrap.querySelectorAll('.lang-row').length > 1) { row.remove(); }
        });
        col1.appendChild(sel);
        col2.appendChild(inp);
        col3.appendChild(del);
        row.appendChild(col1);
        row.appendChild(col2);
        row.appendChild(col3);
        return row;
    }

    function addRow(presetLang) {
        var row = makeRow();
        if (presetLang) { row.querySelector('.lang-sel').value = presetLang; }
        rowsWrap.appendChild(row);
    }

    addRow('en');
    addRow('ur-PK');
    addRowBtn.addEventListener('click', function () { addRow(''); });

    includeDefault.addEventListener('change', function () {
        defaultUrlWrap.classList.toggle('d-none', !includeDefault.checked);
    });

    function validUrl(u) {
        return /^https?:\/\/[^\s/$.?#].[^\s]*$/i.test(u);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        copiedMsg.classList.add('d-none');
        var rows = rowsWrap.querySelectorAll('.lang-row');
        var entries = [];
        var seenLang = {};
        for (var i = 0; i < rows.length; i++) {
            var lang = rows[i].querySelector('.lang-sel').value;
            var url = rows[i].querySelector('.lang-url').value.trim();
            if (!url) continue;
            if (!validUrl(url)) { showError('Wrong URL (row ' + (i + 1) + '). Write the full URL that starts with http:// or https://.'); return; }
            if (seenLang[lang]) { showError('"' + lang + '" is used twice. Choose each language only once.'); return; }
            seenLang[lang] = true;
            entries.push({ lang: lang, url: url });
        }
        if (entries.length < 1) { showError('Write the URL of at least one language version.'); return; }

        var lines = [];
        entries.forEach(function (e) {
            lines.push('<link rel="alternate" hreflang="' + e.lang + '" href="' + e.url + '" />');
        });
        if (includeDefault.checked) {
            var du = defaultUrl.value.trim();
            if (!du) { showError('Write the default page URL for x-default.'); return; }
            if (!validUrl(du)) { showError('The default page URL is wrong.'); return; }
            lines.push('<link rel="alternate" hreflang="x-default" href="' + du + '" />');
        }

        codeOut.textContent = lines.join('\n');
        results.classList.remove('d-none');
    });

    function copyDone() {
        copiedMsg.classList.remove('d-none');
        setTimeout(function () { copiedMsg.classList.add('d-none'); }, 2000);
    }

    copyBtn.addEventListener('click', function () {
        var text = codeOut.textContent;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(copyDone, copyDone);
        } else { copyDone(); }
    });

    dlBtn.addEventListener('click', function () {
        var blob = new Blob([codeOut.textContent], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'hreflang-tags.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });
})();
</script>
@endsection
