@extends('layouts.app')

@section('title', 'Meta Tag Generator Online Free - SEO, Open Graph & Twitter Cards | Azlaan Tools')
@section('meta_description', 'Free meta tag generator: create SEO, Open Graph and Twitter Card meta tags for your website with a live link preview mock. No signup, runs in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Meta Tag Generator</h1>
            <p class="lead text-muted">Generate SEO, Open Graph and Twitter Card meta tags in seconds, with a rough link-preview mock. Runs 100% in your browser.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold" for="fTitle">Site / Page Title</label><input id="fTitle" class="form-control gen" value="Azlaan Tools - Free Online Tools"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="fAuthor">Author</label><input id="fAuthor" class="form-control gen" placeholder="Your name"></div>
                        <div class="col-12"><label class="form-label fw-semibold" for="fDesc">Description</label><textarea id="fDesc" class="form-control gen" rows="2">Free online tools for Pakistan: bill checkers, solar calculators, PDF and image tools. No signup.</textarea></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="fKeywords">Keywords (comma separated)</label><input id="fKeywords" class="form-control gen" placeholder="tools, pakistan, free"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="fUrl">Page URL</label><input id="fUrl" class="form-control gen" placeholder="https://example.com/page"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="fImage">OG Image URL</label><input id="fImage" class="form-control gen" placeholder="https://example.com/image.jpg"></div>
                        <div class="col-md-3"><label class="form-label fw-semibold" for="fType">OG Type</label><select id="fType" class="form-select gen"><option>website</option><option>article</option><option>product</option><option>profile</option></select></div>
                        <div class="col-md-3"><label class="form-label fw-semibold" for="fTheme">Theme Color</label><input type="color" id="fTheme" class="form-control form-control-color gen" value="#0d6efd"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="fTwitter">Twitter Handle</label><input id="fTwitter" class="form-control gen" placeholder="username (without symbol)"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="fCard">Twitter Card</label><select id="fCard" class="form-select gen"><option>summary_large_image</option><option>summary</option></select></div>
                    </div>
                    <label class="form-label fw-semibold mt-4" for="metaOut">Generated Meta Tags</label>
                    <textarea id="metaOut" class="form-control font-monospace" rows="14" readonly></textarea>
                    <button type="button" class="btn btn-success btn-sm mt-2" id="copyBtn">Copy Meta Tags</button>
                    <label class="form-label fw-semibold mt-4 d-block">Link Preview (rough mock)</label>
                    <div class="card" style="max-width:520px;">
                        <img id="prevImg" class="card-img-top d-none" alt="Preview image">
                        <div class="card-body"><div id="prevTitle" class="fw-bold"></div><div id="prevDesc" class="text-muted small"></div><div id="prevUrl" class="text-muted small"></div></div>
                    </div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Fill in your page title, description and the other fields — the code updates live.</li>
                <li>Check the preview mock to see roughly how your link may look when shared.</li>
                <li>Click <strong>Copy Meta Tags</strong> and paste the code inside the head section of your HTML page.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var at = String.fromCharCode(64);
    function val(id) { return document.getElementById(id).value.trim(); }
    function escAttr(s) { return s.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;'); }
    function generate() {
        var title = val('fTitle'); var desc = val('fDesc'); var lines = [];
        if (title) lines.push('<title>' + escAttr(title) + '</title>');
        if (desc) lines.push('<meta name="description" content="' + escAttr(desc) + '">');
        if (val('fKeywords')) lines.push('<meta name="keywords" content="' + escAttr(val('fKeywords')) + '">');
        if (val('fAuthor')) lines.push('<meta name="author" content="' + escAttr(val('fAuthor')) + '">');
        lines.push('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        if (val('fTheme')) lines.push('<meta name="theme-color" content="' + escAttr(val('fTheme')) + '">');
        if (title) lines.push('<meta property="og:title" content="' + escAttr(title) + '">');
        if (desc) lines.push('<meta property="og:description" content="' + escAttr(desc) + '">');
        lines.push('<meta property="og:type" content="' + escAttr(val('fType')) + '">');
        if (val('fUrl')) lines.push('<meta property="og:url" content="' + escAttr(val('fUrl')) + '">');
        if (val('fImage')) lines.push('<meta property="og:image" content="' + escAttr(val('fImage')) + '">');
        lines.push('<meta name="twitter:card" content="' + escAttr(val('fCard')) + '">');
        if (val('fTwitter')) lines.push('<meta name="twitter:site" content="' + at + escAttr(val('fTwitter').split(at).join('')) + '">');
        if (title) lines.push('<meta name="twitter:title" content="' + escAttr(title) + '">');
        if (desc) lines.push('<meta name="twitter:description" content="' + escAttr(desc) + '">');
        if (val('fImage')) lines.push('<meta name="twitter:image" content="' + escAttr(val('fImage')) + '">');
        document.getElementById('metaOut').value = lines.join('\n');
        document.getElementById('prevTitle').textContent = title || 'Your page title';
        document.getElementById('prevDesc').textContent = desc || 'Your description will appear here.';
        document.getElementById('prevUrl').textContent = val('fUrl') || 'example.com';
        var img = document.getElementById('prevImg');
        if (val('fImage')) { img.src = val('fImage'); img.classList.remove('d-none'); } else { img.classList.add('d-none'); img.removeAttribute('src'); }
    }
    document.querySelectorAll('.gen').forEach(function (el) { el.addEventListener('input', generate); el.addEventListener('change', generate); });
    document.getElementById('copyBtn').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(document.getElementById('metaOut').value); });
    generate();
})();
</script>
@endsection
