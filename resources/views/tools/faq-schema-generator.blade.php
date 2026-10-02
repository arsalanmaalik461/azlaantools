@extends('layouts.app')
@section('title', 'FAQ Schema Generator - Azlaan Tools')
@section('meta_description', 'Generate valid FAQPage JSON-LD schema from your questions and answers. Copy or download the markup for Google rich results — free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">FAQ Schema Generator</h1>
            <p class="lead text-muted">Write your questions and answers and get ready-made FAQ schema (JSON-LD) for Google — so your page shows up in search with expandable FAQs.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="faqRows"></div>
                    <button type="button" class="btn btn-outline-primary mb-3" id="addRowBtn">+ Add Question</button>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" id="genBtn">Generate Schema</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="copyBtn">Copy</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="dlBtn">Download .html</button>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label class="form-label fw-semibold" for="outCode">Generated FAQPage JSON-LD</label>
                        <pre class="border rounded bg-light p-3" style="max-height: 340px; overflow: auto; white-space: pre-wrap;" id="outCode"></pre>
                        <div class="alert alert-info mt-3 mb-0 small">
                            Paste this code into your page's <code>&lt;head&gt;</code>, then check it on the <a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">Google Rich Results Test</a>. Rich results are not guaranteed — that is Google's decision.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Use <strong>+ Add Question</strong> to add as many questions and answers as you like (at least 1).</li>
                <li>Press <strong>Generate Schema</strong> — valid FAQPage JSON-LD will be created.</li>
                <li>Take the code with <strong>Copy</strong> or <strong>Download</strong> and add it to your website's <code>&lt;head&gt;</code> section.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var faqRows = document.getElementById('faqRows');
    var addRowBtn = document.getElementById('addRowBtn');
    var genBtn = document.getElementById('genBtn');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var outCode = document.getElementById('outCode');
    var rowCount = 0;
    var lastOutput = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function stripTags(s) {
        return s.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    }

    function addRow(q, a) {
        rowCount++;
        var n = rowCount;
        var div = document.createElement('div');
        div.className = 'border rounded p-3 mb-3 faq-row';
        div.innerHTML =
            '<div class="d-flex justify-content-between align-items-center mb-2">' +
            '<strong>Question ' + n + '</strong>' +
            '<button type="button" class="btn btn-sm btn-outline-danger fq-rm">Remove</button>' +
            '</div>' +
            '<div class="mb-2"><label class="form-label" for="fq-q-' + n + '">Question</label>' +
            '<input type="text" class="form-control fq-q" id="fq-q-' + n + '" placeholder="e.g. How long does delivery take?"></div>' +
            '<div><label class="form-label" for="fq-a-' + n + '">Answer</label>' +
            '<textarea class="form-control fq-a" id="fq-a-' + n + '" rows="2" placeholder="Write the answer..."></textarea></div>';
        if (q) div.querySelector('.fq-q').value = q;
        if (a) div.querySelector('.fq-a').value = a;
        div.querySelector('.fq-rm').addEventListener('click', function () {
            if (faqRows.querySelectorAll('.faq-row').length <= 1) {
                showError('At least one question is required.');
                return;
            }
            hideError();
            div.remove();
            renumber();
        });
        faqRows.appendChild(div);
    }
    function renumber() {
        var rows = faqRows.querySelectorAll('.faq-row'), i;
        for (i = 0; i < rows.length; i++) {
            rows[i].querySelector('strong').textContent = 'Question ' + (i + 1);
        }
    }

    addRowBtn.addEventListener('click', function () { hideError(); addRow('', ''); });

    genBtn.addEventListener('click', function () {
        hideError();
        var rows = faqRows.querySelectorAll('.faq-row'), i, items = [];
        for (i = 0; i < rows.length; i++) {
            var q = stripTags(rows[i].querySelector('.fq-q').value);
            var a = stripTags(rows[i].querySelector('.fq-a').value);
            if (!q && !a) continue;
            if (!q) { showError('Question ' + (i + 1) + ' is empty.'); return; }
            if (!a) { showError('Answer ' + (i + 1) + ' is empty.'); return; }
            items.push({
                '@type': 'Question',
                name: q,
                acceptedAnswer: { '@type': 'Answer', text: a }
            });
        }
        if (!items.length) { showError('Please write at least one complete question and answer.'); return; }
        var schema = {
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            mainEntity: items
        };
        var json = JSON.stringify(schema, null, 2);
        lastOutput = '<script type="application/ld+json">\n' + json + '\n<\/script>';
        outCode.textContent = lastOutput;
        results.classList.remove('d-none');
        copyBtn.classList.remove('d-none');
        dlBtn.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        function done() { copyBtn.textContent = 'Copied!'; setTimeout(function () { copyBtn.textContent = 'Copy'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastOutput).then(done, function () { fallbackCopy(); done(); });
        } else { fallbackCopy(); done(); }
        function fallbackCopy() {
            var ta = document.createElement('textarea');
            ta.value = lastOutput;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) { /* noop */ }
            document.body.removeChild(ta);
        }
    });

    dlBtn.addEventListener('click', function () {
        var blob = new Blob([lastOutput], { type: 'text/html' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'faq-schema.html';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); URL.revokeObjectURL(url); }, 500);
    });

    addRow('How long does delivery take?', 'Delivery usually happens in 2 to 4 working days.');
    addRow('', '');
})();
</script>
@endsection
