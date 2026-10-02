@extends('layouts.app')

@section('title', 'URL Slug Generator - Create SEO Friendly Slugs | Azlaan Tools')
@section('meta_description', 'Free URL slug generator: turn any title or text into a clean, SEO-friendly URL slug instantly. No signup, everything runs in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">URL Slug Generator</h1>
    <p class="lead">Type any title or text and get a clean, lowercase, SEO-friendly URL slug instantly — free, no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label for="inputText" class="form-label fw-semibold">Your Title / Text</label>
            <input type="text" class="form-control form-control-lg" id="inputText" placeholder="e.g. My First Blog Post About Solar Panels!">

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label for="separator" class="form-label fw-semibold">Separator</label>
                    <select class="form-select" id="separator">
                        <option value="-" selected>Hyphen ( - )</option>
                        <option value="_">Underscore ( _ )</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="maxLength" class="form-label fw-semibold">Max Length (0 = no limit)</label>
                    <input type="number" class="form-control" id="maxLength" value="0" min="0" max="500">
                </div>
                <div class="col-md-4">
                    <label for="lowercaseOpt" class="form-label fw-semibold">Case</label>
                    <select class="form-select" id="lowercaseOpt">
                        <option value="lower" selected>Lowercase</option>
                        <option value="keep">Keep original case</option>
                    </select>
                </div>
            </div>

            <label for="outputText" class="form-label fw-semibold mt-3">Generated Slug</label>
            <div class="input-group">
                <input type="text" class="form-control form-control-lg" id="outputText" readonly placeholder="your-slug-will-appear-here">
                <button type="button" class="btn btn-success" id="copyBtn">Copy Slug</button>
            </div>
            <div class="small text-muted mt-2">Slug length: <strong id="slugLength">0</strong> characters</div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your text never leaves your browser — everything is generated locally on your device, no upload, no signup.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Type or paste your title or text in the box above.</li>
        <li>Choose a separator (hyphen or underscore) and an optional maximum length.</li>
        <li>Your slug is generated live as you type — special characters and non-Latin (Urdu/Arabic) characters are removed.</li>
        <li>Click <strong>Copy Slug</strong> and paste it into your URL, blog post or file name.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('inputText');
    var output = document.getElementById('outputText');
    var separator = document.getElementById('separator');
    var maxLength = document.getElementById('maxLength');
    var lowercaseOpt = document.getElementById('lowercaseOpt');
    var slugLength = document.getElementById('slugLength');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');

    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
        setTimeout(function () { successBox.classList.add('d-none'); }, 2500);
    }
    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }

    // Basic transliteration for a few common Urdu/Arabic words/letters, then strip the rest.
    var translit = {
        '\u0627': 'a', '\u0628': 'b', '\u067E': 'p', '\u062A': 't', '\u0679': 't',
        '\u062B': 's', '\u062C': 'j', '\u0686': 'ch', '\u062D': 'h', '\u062E': 'kh',
        '\u062F': 'd', '\u0688': 'd', '\u0630': 'z', '\u0631': 'r', '\u0691': 'r',
        '\u0632': 'z', '\u0698': 'zh', '\u0633': 's', '\u0634': 'sh', '\u0635': 's',
        '\u0636': 'z', '\u0637': 't', '\u0638': 'z', '\u0639': 'a', '\u063A': 'gh',
        '\u0641': 'f', '\u0642': 'q', '\u06A9': 'k', '\u06AF': 'g', '\u0644': 'l',
        '\u0645': 'm', '\u0646': 'n', '\u06BA': '', '\u0648': 'w', '\u06C1': 'h',
        '\u0647': 'h', '\u06CC': 'y', '\u064A': 'y', '\u0626': 'y', '\u0621': '',
        '\u0622': 'aa', '\u06BE': 'h', '\u06D2': 'ay'
    };

    function makeSlug(text) {
        var sep = separator.value;
        var s = text || '';
        // Transliterate mapped Arabic/Urdu characters, drop other non-latin chars later.
        var mapped = '';
        for (var i = 0; i < s.length; i++) {
            var ch = s.charAt(i);
            if (Object.prototype.hasOwnProperty.call(translit, ch)) mapped += translit[ch];
            else mapped += ch;
        }
        s = mapped;
        // Normalize accented latin characters (é -> e etc.)
        if (s.normalize) s = s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        if (lowercaseOpt.value === 'lower') s = s.toLowerCase();
        // Keep only letters, numbers, spaces and hyphens; strip everything else (incl. remaining non-latin)
        s = s.replace(/[^a-zA-Z0-9\s\-_]/g, '');
        s = s.trim();
        // Split on whitespace/underscore/hyphen runs, rejoin with chosen separator
        var parts = s.split(/[\s\-_]+/).filter(function (p) { return p.length > 0; });
        s = parts.join(sep);
        // Collapse repeated separators and trim from ends
        if (sep === '-') s = s.replace(/-+/g, '-').replace(/^-+|-+$/g, '');
        else s = s.replace(/_+/g, '_').replace(/^_+|_+$/g, '');
        // Max length: cut at separator boundary where possible
        var max = parseInt(maxLength.value, 10);
        if (max > 0 && s.length > max) {
            var cut = s.slice(0, max);
            var lastSep = cut.lastIndexOf(sep);
            if (lastSep > 0) cut = cut.slice(0, lastSep);
            s = cut.replace(/[-_]+$/, '');
        }
        return s;
    }

    function update() {
        var slug = makeSlug(input.value);
        output.value = slug;
        slugLength.textContent = slug.length;
    }

    input.addEventListener('input', update);
    separator.addEventListener('change', update);
    maxLength.addEventListener('input', update);
    lowercaseOpt.addEventListener('change', update);

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!output.value) { showError('Nothing to copy yet — type some text first.'); return; }
        try { await navigator.clipboard.writeText(output.value); }
        catch (e) { output.select(); document.execCommand('copy'); }
        showSuccess('Slug copied to clipboard!');
    });

    update();
})();
</script>
@endsection
