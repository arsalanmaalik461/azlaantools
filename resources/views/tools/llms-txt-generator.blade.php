@extends('layouts.app')

@section('title', 'LLMs.txt Generator - Azlaan Tools')
@section('meta_description', 'Generate a proper llms.txt file for your website so AI assistants like ChatGPT and Claude can understand it. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">LLMs.txt Generator</h1>
            <p class="lead text-muted">Make an <code>llms.txt</code> file for your website so AI assistants like ChatGPT and Claude can understand your site better. After making the file, upload it to your site root (/) with the name <code>llms.txt</code>.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="siteName" class="form-label fw-semibold">Website name</label>
                        <input type="text" class="form-control" id="siteName" placeholder="e.g. Azlaan Tools">
                    </div>
                    <div class="mb-3">
                        <label for="baseUrl" class="form-label fw-semibold">Website base URL</label>
                        <input type="url" class="form-control" id="baseUrl" placeholder="https://example.com">
                    </div>
                    <div class="mb-3">
                        <label for="siteDesc" class="form-label fw-semibold">Short summary</label>
                        <textarea class="form-control" id="siteDesc" rows="2" placeholder="What is this website about, in one or two lines."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="contactInfo" class="form-label fw-semibold">Contact info (optional)</label>
                        <input type="text" class="form-control" id="contactInfo" placeholder="e.g. Contact: info at example.com">
                    </div>

                    <h6>Sections</h6>
                    <div id="sectionsWrap" class="mb-2"></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addSecBtn">+ Add Section</button>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make llms.txt</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Preview</h5>
                            <div>
                                <button type="button" class="btn btn-outline-secondary btn-sm me-1" id="copyBtn">Copy</button>
                                <button type="button" class="btn btn-success btn-sm" id="dlBtn">Download llms.txt</button>
                            </div>
                        </div>
                        <pre class="bg-light border rounded p-3 small" id="preview" style="white-space:pre-wrap;word-break:break-word;max-height:420px;overflow:auto;"></pre>
                        <p class="text-muted small mt-2">Tip: keep this file in your website root folder with the name <code>llms.txt</code>. Big sites also keep a <code>llms-full.txt</code>.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the website name, URL and summary.</li>
                <li>Use <strong>+ Add Section</strong> to make sections: each section has a title, summary and pages (one page per line: name | URL).</li>
                <li>Press <strong>Make llms.txt</strong>, check the preview and download it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var sectionsWrap = document.getElementById('sectionsWrap');
    var addSecBtn = document.getElementById('addSecBtn');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var preview = document.getElementById('preview');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var secCount = 0;

    function addSection(t, d, p) {
        secCount++;
        var box = document.createElement('div');
        box.className = 'border rounded p-2 mb-2 sec-box';
        var head = document.createElement('div');
        head.className = 'd-flex justify-content-between align-items-center mb-2';
        var lbl = document.createElement('strong');
        lbl.textContent = 'Section ' + secCount;
        var rm = document.createElement('button');
        rm.type = 'button'; rm.className = 'btn btn-sm btn-outline-danger';
        rm.textContent = 'Remove';
        rm.addEventListener('click', function () { box.remove(); });
        head.appendChild(lbl); head.appendChild(rm);
        var inT = document.createElement('input');
        inT.type = 'text'; inT.className = 'form-control form-control-sm mb-2 sec-title';
        inT.placeholder = 'Section title, e.g. Tools'; inT.value = t || '';
        var inD = document.createElement('input');
        inD.type = 'text'; inD.className = 'form-control form-control-sm mb-2 sec-desc';
        inD.placeholder = 'Section summary (optional)'; inD.value = d || '';
        var inP = document.createElement('textarea');
        inP.className = 'form-control form-control-sm sec-pages';
        inP.rows = 3;
        inP.placeholder = 'Pages, one per line: Page Name | /page-url\ne.g. BMI Calculator | /tools/bmi-calculator';
        inP.value = p || '';
        box.appendChild(head); box.appendChild(inT); box.appendChild(inD); box.appendChild(inP);
        sectionsWrap.appendChild(box);
    }
    addSecBtn.addEventListener('click', function () { addSection('', '', ''); });
    addSection('Tools', 'Free online tools', 'BMI Calculator | /tools/bmi-calculator');

    function validUrl(u) {
        try { var x = new URL(u); return x.protocol === 'http:' || x.protocol === 'https:'; }
        catch (e) { return false; }
    }

    function joinUrl(base, path) {
        path = path.trim();
        if (/^https?:\/\//i.test(path)) return path;
        if (path.charAt(0) !== '/') path = '/' + path;
        return base.replace(/\/+$/, '') + path;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var name = document.getElementById('siteName').value.trim();
        var base = document.getElementById('baseUrl').value.trim();
        var desc = document.getElementById('siteDesc').value.trim();
        var contact = document.getElementById('contactInfo').value.trim();
        if (!name) { showError('Enter the website name.'); return; }
        if (!validUrl(base)) { showError('Base URL is wrong, enter the full URL e.g. https://example.com'); return; }
        if (!desc) { showError('Enter a short summary of the website.'); return; }
        var boxes = sectionsWrap.querySelectorAll('.sec-box');
        if (!boxes.length) { showError('Add at least one section.'); return; }
        var out = '# ' + name + '\n\n> ' + desc + '\n';
        if (contact) out += '\n' + contact + '\n';
        var anyPage = false;
        boxes.forEach(function (b) {
            var t = b.querySelector('.sec-title').value.trim();
            var d = b.querySelector('.sec-desc').value.trim();
            var p = b.querySelector('.sec-pages').value.trim();
            if (!t) return;
            out += '\n## ' + t + '\n';
            if (d) out += '\n' + d + '\n';
            if (p) {
                out += '\n';
                p.split('\n').forEach(function (line) {
                    line = line.trim();
                    if (!line) return;
                    var parts = line.split('|');
                    var pname = parts[0].trim();
                    var purl = parts.length > 1 ? joinUrl(base, parts[1]) : base;
                    if (pname) { out += '- [' + pname + '](' + purl + ')\n'; anyPage = true; }
                });
            }
        });
        if (!anyPage) { showError('Add at least one page (Page Name | /url).'); return; }
        preview.textContent = out;
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        var t = preview.textContent;
        function done() {
            var old = copyBtn.textContent;
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = old; }, 1200);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(done, done);
        } else {
            var ta = document.createElement('textarea');
            ta.value = t; document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta); done();
        }
    });

    dlBtn.addEventListener('click', function () {
        var blob = new Blob([preview.textContent], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'llms.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
})();
</script>
@endsection
