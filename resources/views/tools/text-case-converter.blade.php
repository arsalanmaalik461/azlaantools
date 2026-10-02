@extends('layouts.app')

@section('title', 'Text Case Converter - UPPERCASE, lowercase, Title Case | Azlaan Tools')
@section('meta_description', 'Free text case converter: change text to UPPERCASE, lowercase, Title Case, Sentence case, alternating case, remove extra spaces or reverse text. Copy or download instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Text Case Converter</h1>
            <p class="lead text-muted">Type or paste your text and convert it to any case with one click — no signup, everything happens in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="textInput" class="form-label fw-semibold">Your Text</label>
                    <textarea class="form-control" id="textInput" rows="7" placeholder="Type or paste your text here..."></textarea>
                    <div class="d-flex flex-wrap gap-2 mt-2 small text-muted">
                        <span>Characters: <strong id="charCount">0</strong></span>
                        <span>Characters (no spaces): <strong id="charNoSpace">0</strong></span>
                        <span>Words: <strong id="wordCount">0</strong></span>
                        <span>Lines: <strong id="lineCount">0</strong></span>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-primary btn-sm case-btn" data-action="upper">UPPERCASE</button>
                        <button type="button" class="btn btn-primary btn-sm case-btn" data-action="lower">lowercase</button>
                        <button type="button" class="btn btn-primary btn-sm case-btn" data-action="title">Title Case</button>
                        <button type="button" class="btn btn-primary btn-sm case-btn" data-action="sentence">Sentence case</button>
                        <button type="button" class="btn btn-primary btn-sm case-btn" data-action="alternating">aLtErNaTiNg</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm case-btn" data-action="trimSpaces">Remove Extra Spaces</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm case-btn" data-action="reverse">Reverse Text</button>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Text</button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="downloadBtn">Download .txt</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearBtn">Clear</button>
                    </div>
                    <div class="alert alert-success mt-3 mb-0 d-none" id="copyMsg">Copied to clipboard!</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Type or paste your text in the box above.</li>
                        <li>Click any case button — UPPERCASE, Title Case, aLtErNaTiNg and more — to convert instantly.</li>
                        <li>Check the live character and word count below the box.</li>
                        <li>Click <strong>Copy Text</strong> to copy the result, or <strong>Download .txt</strong> to save it as a file.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var textarea = document.getElementById('textInput');
    var copyMsg = document.getElementById('copyMsg');

    function updateCounts() {
        var t = textarea.value;
        document.getElementById('charCount').textContent = t.length;
        document.getElementById('charNoSpace').textContent = t.replace(/\s/g, '').length;
        var words = t.trim() ? t.trim().split(/\s+/).length : 0;
        document.getElementById('wordCount').textContent = words;
        document.getElementById('lineCount').textContent = t ? t.split(/\n/).length : 0;
    }

    function toTitleCase(t) {
        return t.toLowerCase().replace(/(^|\s|[("'])(\S)/g, function (m, p1, p2) { return p1 + p2.toUpperCase(); });
    }
    function toSentenceCase(t) {
        var lower = t.toLowerCase();
        return lower.replace(/(^\s*[a-z])|([.!?]\s*[a-z])/g, function (m) { return m.toUpperCase(); });
    }
    function toAlternating(t) {
        var out = '', upper = false;
        for (var i = 0; i < t.length; i++) {
            var ch = t[i];
            if (/[a-zA-Z]/.test(ch)) { out += upper ? ch.toUpperCase() : ch.toLowerCase(); upper = !upper; }
            else { out += ch; }
        }
        return out;
    }

    var actions = {
        upper: function (t) { return t.toUpperCase(); },
        lower: function (t) { return t.toLowerCase(); },
        title: toTitleCase,
        sentence: toSentenceCase,
        alternating: toAlternating,
        trimSpaces: function (t) { return t.replace(/[ \t]+/g, ' ').replace(/\n{3,}/g, '\n\n').trim(); },
        reverse: function (t) { return Array.from(t).reverse().join(''); }
    };

    document.querySelectorAll('.case-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var fn = actions[btn.getAttribute('data-action')];
            if (fn) { textarea.value = fn(textarea.value); updateCounts(); textarea.focus(); }
        });
    });

    textarea.addEventListener('input', updateCounts);

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!textarea.value) return;
        try {
            await navigator.clipboard.writeText(textarea.value);
        } catch (e) {
            textarea.select();
            document.execCommand('copy');
        }
        copyMsg.classList.remove('d-none');
        setTimeout(function () { copyMsg.classList.add('d-none'); }, 2000);
    });

    document.getElementById('downloadBtn').addEventListener('click', function () {
        var blob = new Blob([textarea.value], { type: 'text/plain;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = 'converted-text.txt';
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        textarea.value = ''; updateCounts(); textarea.focus();
    });

    updateCounts();
})();
</script>
@endsection
