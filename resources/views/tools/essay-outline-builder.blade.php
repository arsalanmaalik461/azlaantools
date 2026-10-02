@extends('layouts.app')

@section('title', 'Essay Outline Builder - Azlaan Tools')
@section('meta_description', 'Build a structured essay outline with thesis, points and examples. Free online, printable plan.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Essay Outline Builder</h1>
            <p class="lead text-muted">Build a structured essay outline: thesis, main points and examples — as one neat printable plan. Fill the form and your outline is ready.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="essayTopic" class="form-label fw-semibold">Essay topic</label>
                        <input type="text" class="form-control" id="essayTopic" placeholder="e.g. The importance of solar energy in Pakistan">
                    </div>
                    <div class="mb-3">
                        <label for="thesis" class="form-label fw-semibold">Thesis statement (your main argument in one sentence)</label>
                        <textarea class="form-control" id="thesis" rows="2" placeholder="e.g. Solar energy is the most practical solution to Pakistan's electricity crisis because it is cheap, clean and reliable."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="hook" class="form-label fw-semibold">Introduction hook (optional)</label>
                        <input type="text" class="form-control" id="hook" placeholder="e.g. A surprising fact, question or quote to open with">
                    </div>

                    <h5 class="mt-4 mb-3">Body paragraphs <span class="text-muted fw-normal small">(3 recommended)</span></h5>
                    <div id="bodyParas"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addParaBtn">+ Add Paragraph</button>

                    <div class="mb-3">
                        <label for="conclusion" class="form-label fw-semibold">Conclusion — restate the thesis and closing thought</label>
                        <textarea class="form-control" id="conclusion" rows="2" placeholder="e.g. Restate the thesis in new words and end with a call to action."></textarea>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Build Outline</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Your Essay Outline</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copyBtn">Copy Text</button>
                                <button type="button" class="btn btn-sm btn-success" id="printBtn">Print / PDF</button>
                            </div>
                        </div>
                        <div class="border rounded p-3 bg-light" id="outlineBox"></div>
                        <div class="form-text mt-2">Press Print and select "Save as PDF" to save the outline as PDF.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the topic, thesis statement and (optional) hook.</li>
                <li>For each body paragraph enter the main point, example/evidence and explanation.</li>
                <li>Press "Build Outline" — then copy, print or save the finished outline as PDF.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var bodyParas = document.getElementById('bodyParas');
    var addParaBtn = document.getElementById('addParaBtn');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var outlineBox = document.getElementById('outlineBox');
    var paraCount = 0;
    var lastPlain = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function addPara(point, example, explain) {
        paraCount++;
        var card = document.createElement('div');
        card.className = 'card mb-2 para-card';
        card.innerHTML =
            '<div class="card-body">' +
            '<div class="d-flex justify-content-between align-items-center mb-2">' +
            '<h6 class="mb-0">Body paragraph <span class="pnum"></span></h6>' +
            '<button type="button" class="btn btn-sm btn-outline-danger rm">Remove</button>' +
            '</div>' +
            '<div class="mb-2"><label class="form-label small fw-semibold">Main point</label>' +
            '<input type="text" class="form-control form-control-sm ppoint" placeholder="e.g. Solar panels cut electricity bills by up to 80%"></div>' +
            '<div class="mb-2"><label class="form-label small fw-semibold">Example / evidence</label>' +
            '<input type="text" class="form-control form-control-sm pexample" placeholder="e.g. A 5kW system in Lahore..."></div>' +
            '<div><label class="form-label small fw-semibold">Explanation (how this point supports the thesis)</label>' +
            '<input type="text" class="form-control form-control-sm pexplain" placeholder="e.g. This shows solar is affordable long-term..."></div>' +
            '</div>';
        card.querySelector('.pnum').textContent = '#' + paraCount;
        if (point) card.querySelector('.ppoint').value = point;
        if (example) card.querySelector('.pexample').value = example;
        if (explain) card.querySelector('.pexplain').value = explain;
        card.querySelector('.rm').addEventListener('click', function () {
            if (bodyParas.querySelectorAll('.para-card').length <= 1) {
                showError('At least 1 body paragraph is required.'); return;
            }
            card.remove();
            hideError();
        });
        bodyParas.appendChild(card);
    }

    addPara(); addPara(); addPara();
    addParaBtn.addEventListener('click', function () { hideError(); addPara(); });

    function esc(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var topic = document.getElementById('essayTopic').value.trim();
        var thesis = document.getElementById('thesis').value.trim();
        var hook = document.getElementById('hook').value.trim();
        var conclusion = document.getElementById('conclusion').value.trim();
        if (!topic) { showError('Please enter the essay topic.'); return; }
        if (!thesis) { showError('Please enter your thesis statement.'); return; }

        var paras = [];
        var cards = bodyParas.querySelectorAll('.para-card');
        for (var i = 0; i < cards.length; i++) {
            var pt = cards[i].querySelector('.ppoint').value.trim();
            var ex = cards[i].querySelector('.pexample').value.trim();
            var xp = cards[i].querySelector('.pexplain').value.trim();
            if (pt || ex || xp) paras.push({ point: pt, example: ex, explain: xp });
        }
        if (!paras.length) { showError('Enter the point of at least one body paragraph.'); return; }

        var html = '<h4 class="mb-3">' + esc(topic) + '</h4>';
        html += '<p><strong>I. Introduction</strong></p><ul>';
        if (hook) html += '<li><em>Hook:</em> ' + esc(hook) + '</li>';
        html += '<li><em>Thesis:</em> ' + esc(thesis) + '</li></ul>';

        var plain = 'ESSAY OUTLINE: ' + topic + '\n\nI. INTRODUCTION\n';
        if (hook) plain += '   Hook: ' + hook + '\n';
        plain += '   Thesis: ' + thesis + '\n';

        var numerals = ['II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
        paras.forEach(function (p, idx) {
            var num = numerals[idx] || ('P' + (idx + 1));
            html += '<p><strong>' + num + '. Body paragraph ' + (idx + 1) + '</strong></p><ul>';
            plain += '\n' + num + '. BODY PARAGRAPH ' + (idx + 1) + '\n';
            if (p.point) { html += '<li><em>Main point:</em> ' + esc(p.point) + '</li>'; plain += '   Main point: ' + p.point + '\n'; }
            if (p.example) { html += '<li><em>Example/evidence:</em> ' + esc(p.example) + '</li>'; plain += '   Example/evidence: ' + p.example + '\n'; }
            if (p.explain) { html += '<li><em>Explanation:</em> ' + esc(p.explain) + '</li>'; plain += '   Explanation: ' + p.explain + '\n'; }
            html += '</ul>';
        });

        html += '<p><strong>' + (numerals[paras.length] || 'Last') + '. Conclusion</strong></p><ul>';
        html += '<li><em>Restate thesis:</em> ' + (conclusion ? esc(conclusion) : esc(thesis)) + '</li></ul>';
        plain += '\n' + (numerals[paras.length] || 'Last') + '. CONCLUSION\n';
        plain += '   ' + (conclusion || thesis) + '\n';

        outlineBox.innerHTML = html;
        lastPlain = plain;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var btn = document.getElementById('copyBtn');
        if (navigator.clipboard && lastPlain) {
            navigator.clipboard.writeText(lastPlain).then(function () {
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = 'Copy Text'; }, 1500);
            });
        }
    });
    document.getElementById('printBtn').addEventListener('click', function () { window.print(); });
})();
</script>
@endsection
