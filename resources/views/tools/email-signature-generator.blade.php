@extends('layouts.app')
@section('title', 'Email Signature Generator - Azlaan Tools')
@section('meta_description', 'Build a professional email signature in a minute. Enter your details, pick a style, preview live, and copy the HTML — free online.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Email Signature Generator</h1>
            <p class="lead text-muted">Make a professional email signature in one minute. Enter your details, pick a style, preview it live, and copy it.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="fName" class="form-label fw-semibold">Full name</label>
                            <input type="text" class="form-control" id="fName" placeholder="e.g. Arslan Malik">
                        </div>
                        <div class="col-md-6">
                            <label for="fTitle" class="form-label fw-semibold">Job title</label>
                            <input type="text" class="form-control" id="fTitle" placeholder="e.g. Sales Manager">
                        </div>
                        <div class="col-md-6">
                            <label for="fCompany" class="form-label fw-semibold">Company</label>
                            <input type="text" class="form-control" id="fCompany" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="fPhone" class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="fPhone" placeholder="e.g. 0300-8987448">
                        </div>
                        <div class="col-md-6">
                            <label for="fEmail" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="fEmail" placeholder="e.g. name@example.com">
                        </div>
                        <div class="col-md-6">
                            <label for="fWeb" class="form-label fw-semibold">Website</label>
                            <input type="text" class="form-control" id="fWeb" placeholder="e.g. arslanmalik.tech">
                        </div>
                        <div class="col-md-6">
                            <label for="fAddr" class="form-label fw-semibold">Address (optional)</label>
                            <input type="text" class="form-control" id="fAddr" placeholder="e.g. Faisalabad, Pakistan">
                        </div>
                        <div class="col-md-6">
                            <label for="fAccent" class="form-label fw-semibold">Accent color</label>
                            <input type="color" class="form-control form-control-color w-100" id="fAccent" value="#6d28d9" title="Accent color">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fStyle" class="form-label fw-semibold">Signature style</label>
                        <select class="form-select" id="fStyle">
                            <option value="classic">Classic — simple lines</option>
                            <option value="accent">Accent bar — colored side bar</option>
                            <option value="centered">Centered — middle aligned</option>
                        </select>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <span class="form-label fw-semibold d-block mb-2">Live preview</span>
                    <div id="previewBox" class="border rounded p-3 bg-white mb-3" style="min-height: 120px;">
                        <p class="text-muted small mb-0">Your signature will appear here...</p>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" id="copyHtmlBtn" class="btn btn-primary">Copy HTML</button>
                        <button type="button" id="copyRichBtn" class="btn btn-outline-primary">Copy as Rich Text</button>
                        <button type="button" id="dlBtn" class="btn btn-outline-secondary">Download .html</button>
                    </div>

                    <div id="results" class="d-none mt-3">
                        <span class="form-label fw-semibold d-block mb-2">HTML code (paste it in Gmail/Outlook signature settings)</span>
                        <textarea class="form-control font-monospace small" id="codeOut" rows="8" readonly></textarea>
                        <p class="form-text">Gmail: Settings → See all settings → Signature. Outlook: Settings → Mail → Compose and reply.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your details — name, title, company, phone, email and website.</li>
                <li>Pick a style and accent color; the preview updates by itself.</li>
                <li>Press "Copy HTML" or "Copy as Rich Text" and paste it in your email signature settings.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var ids = ['fName', 'fTitle', 'fCompany', 'fPhone', 'fEmail', 'fWeb', 'fAddr'];
    var fields = {};
    ids.forEach(function (id) { fields[id] = document.getElementById(id); });
    var fAccent = document.getElementById('fAccent');
    var fStyle = document.getElementById('fStyle');
    var previewBox = document.getElementById('previewBox');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var codeOut = document.getElementById('codeOut');
    var copyHtmlBtn = document.getElementById('copyHtmlBtn');
    var copyRichBtn = document.getElementById('copyRichBtn');
    var dlBtn = document.getElementById('dlBtn');

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function vals() {
        return {
            name: fields.fName.value.trim(),
            title: fields.fTitle.value.trim(),
            company: fields.fCompany.value.trim(),
            phone: fields.fPhone.value.trim(),
            email: fields.fEmail.value.trim(),
            web: fields.fWeb.value.trim(),
            addr: fields.fAddr.value.trim(),
            accent: fAccent.value,
            style: fStyle.value
        };
    }

    function normWeb(w) {
        if (!w) return '';
        return /^https?:\/\//i.test(w) ? w : 'https://' + w;
    }

    function buildHtml(v) {
        var lines = [];
        if (v.title && v.company) lines.push(esc(v.title) + ' | ' + esc(v.company));
        else if (v.title) lines.push(esc(v.title));
        else if (v.company) lines.push(esc(v.company));
        var contacts = [];
        if (v.phone) contacts.push('&#9742; ' + esc(v.phone));
        if (v.email) contacts.push('<a href="mailto:' + esc(v.email) + '" style="color:' + v.accent + ';text-decoration:none;">' + esc(v.email) + '</a>');
        if (v.web) contacts.push('<a href="' + esc(normWeb(v.web)) + '" style="color:' + v.accent + ';text-decoration:none;">' + esc(v.web) + '</a>');
        if (contacts.length) lines.push(contacts.join(' &nbsp;|&nbsp; '));
        if (v.addr) lines.push(esc(v.addr));
        var nameHtml = '<span style="font-size:18px;font-weight:bold;color:#111;">' + esc(v.name) + '</span>';
        var bodyHtml = lines.length ? '<div style="font-size:13px;color:#444;line-height:1.6;">' + lines.join('<br>') + '</div>' : '';
        if (v.style === 'accent') {
            return '<table cellpadding="0" cellspacing="0" border="0" style="font-family:Arial,sans-serif;">' +
                '<tr><td style="border-left:4px solid ' + v.accent + ';padding-left:12px;">' + nameHtml + '<div style="height:4px;"></div>' + bodyHtml + '</td></tr></table>';
        }
        if (v.style === 'centered') {
            return '<div style="font-family:Arial,sans-serif;text-align:center;">' + nameHtml + '<div style="height:6px;"></div>' + bodyHtml + '</div>';
        }
        return '<div style="font-family:Arial,sans-serif;">' + nameHtml + '<div style="height:4px;"></div>' + bodyHtml + '</div>';
    }

    function refresh() {
        hideError();
        var v = vals();
        if (!v.name && !v.company) {
            previewBox.innerHTML = '<p class="text-muted small mb-0">Your signature will appear here... (enter at least a name)</p>';
            results.classList.add('d-none');
            return;
        }
        var html = buildHtml(v);
        previewBox.innerHTML = html;
        codeOut.value = html;
        results.classList.remove('d-none');
    }

    Object.keys(fields).forEach(function (k) { fields[k].addEventListener('input', refresh); });
    fAccent.addEventListener('input', refresh);
    fStyle.addEventListener('change', refresh);

    function flash(btn, txt) {
        var old = btn.textContent;
        btn.textContent = txt;
        setTimeout(function () { btn.textContent = old; }, 1500);
    }

    copyHtmlBtn.addEventListener('click', function () {
        if (!codeOut.value) { showError('Enter your name first so a signature can be made.'); return; }
        hideError();
        navigator.clipboard.writeText(codeOut.value).then(function () { flash(copyHtmlBtn, 'Copied!'); })
            .catch(function () { showError('Could not copy — select the code and press Ctrl+C.'); });
    });

    copyRichBtn.addEventListener('click', function () {
        if (!previewBox.querySelector('table, div')) { showError('Enter your name first so a signature can be made.'); return; }
        hideError();
        var html = codeOut.value;
        try {
            var item = new ClipboardItem({ 'text/html': new Blob([html], { type: 'text/html' }), 'text/plain': new Blob([previewBox.innerText], { type: 'text/plain' }) });
            navigator.clipboard.write([item]).then(function () { flash(copyRichBtn, 'Copied!'); })
                .catch(function () { showError('Rich copy is not supported — use "Copy HTML".'); });
        } catch (e) { showError('Rich copy is not supported — use "Copy HTML".'); }
    });

    dlBtn.addEventListener('click', function () {
        if (!codeOut.value) { showError('Enter your name first so a signature can be made.'); return; }
        hideError();
        var blob = new Blob(['<!DOCTYPE html><html><body>' + codeOut.value + '</body></html>'], { type: 'text/html' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'email-signature.html';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    refresh();
})();
</script>
@endsection
