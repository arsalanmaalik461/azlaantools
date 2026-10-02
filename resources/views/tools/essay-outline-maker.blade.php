@extends('layouts.app')

@section('title', 'Essay Outline Maker - Azlaan Tools')
@section('meta_description', 'Build a proper essay outline online for free. Intro, body paragraphs and conclusion structure for any topic.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Essay Outline Maker</h1>
            <p class="lead text-muted">Make your essay outline — the full structure of intro, body and conclusion. Enter the topic, and your outline is ready.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="topicInput" class="form-label fw-semibold">Essay topic</label>
                        <input type="text" class="form-control" id="topicInput" placeholder="e.g. The Importance of Solar Energy in Pakistan">
                        <div class="form-text">Write the topic in English — the outline will also be in English</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="typeSel" class="form-label fw-semibold">Essay type</label>
                            <select class="form-select" id="typeSel">
                                <option value="argumentative">Argumentative</option>
                                <option value="descriptive">Descriptive</option>
                                <option value="narrative">Narrative</option>
                                <option value="comparative">Comparative</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="paraSel" class="form-label fw-semibold">Body paragraphs</label>
                            <select class="form-select" id="paraSel">
                                <option value="2">2 paragraphs</option>
                                <option value="3" selected>3 paragraphs</option>
                                <option value="4">4 paragraphs</option>
                                <option value="5">5 paragraphs</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="levelSel" class="form-label fw-semibold">Level</label>
                            <select class="form-select" id="levelSel">
                                <option value="school">School</option>
                                <option value="college" selected>College / University</option>
                                <option value="css">CSS / Competitive</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Outline</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="border rounded p-3 bg-light" id="outlineOut"></div>
                        <div class="d-grid gap-2 d-sm-flex mt-3">
                            <button type="button" class="btn btn-outline-primary flex-fill" id="copyBtn">Copy Outline</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="dlBtn">Download (.txt)</button>
                        </div>
                        <div class="alert alert-success mt-3 d-none" id="copyMsg" role="status">Outline copied!</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the essay topic and choose the type, paragraphs and level.</li>
                <li>Press <strong>Make Outline</strong> — you will get the full structure.</li>
                <li>Copy it and write it in your own words — the outline is only a guide.</li>
            </ol>
            <p class="text-muted small">Remember: the outline helps you plan, but writing the essay in your own words is the real work.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var topicInput = document.getElementById('topicInput');
    var typeSel = document.getElementById('typeSel');
    var paraSel = document.getElementById('paraSel');
    var levelSel = document.getElementById('levelSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var outlineOut = document.getElementById('outlineOut');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var copyMsg = document.getElementById('copyMsg');
    var lastText = '';

    var HOOKS = {
        argumentative: [
            'A surprising statistic or fact about the topic',
            'A thought-provoking question for the reader',
            'A brief real-world example showing why this matters'
        ],
        descriptive: [
            'A vivid sensory detail that sets the scene',
            'A striking image or metaphor related to the topic',
            'A personal observation that draws the reader in'
        ],
        narrative: [
            'The moment the story begins — start in the middle of action',
            'A short dialogue or quote from the event',
            'A reflection that hints at the lesson learned'
        ],
        comparative: [
            'A statement showing how the two sides differ',
            'A surprising similarity between the two subjects',
            'A question: which approach is truly better?'
        ]
    };
    var THESIS = {
        argumentative: 'A clear position statement: state what you believe about the topic and preview your 2-3 main reasons.',
        descriptive: 'A dominant impression: one sentence capturing the overall feeling or picture you will describe.',
        narrative: 'A theme statement: what this experience taught you, in one clear sentence.',
        comparative: 'A judgment: which side/subject is stronger, and the criteria you will use to compare.'
    };
    var BODY_POINTS = {
        argumentative: [
            'Strongest reason supporting your position',
            'Evidence: facts, examples, or expert opinion',
            'Address a counter-argument and refute it'
        ],
        descriptive: [
            'First vivid detail — what the reader sees',
            'Second detail — sounds, feelings, atmosphere',
            'Final detail that completes the picture'
        ],
        narrative: [
            'Background: who, where, and the situation',
            'Rising action: what happened, step by step',
            'Climax and resolution: the turning point'
        ],
        comparative: [
            'First point of comparison (similarity or difference)',
            'Second point of comparison with examples',
            'Third point showing the overall pattern'
        ]
    };
    var TRANSITIONS = ['Furthermore', 'In addition', 'On the other hand', 'For example', 'As a result', 'In contrast', 'Moreover', 'Finally'];
    var CLOSING = {
        argumentative: 'End with a call to action or a prediction of what happens if your view is ignored.',
        descriptive: 'Close with the lasting image or feeling you want the reader to keep.',
        narrative: 'Reflect: how did this change you? What would you tell others?',
        comparative: 'Give a final verdict and explain when each side might be the better choice.'
    };
    var LEVEL_TIP = {
        school: 'Simple, clear sentences. One idea per paragraph.',
        college: 'Use topic sentences and link ideas with transitions. Support claims with examples.',
        css: 'Analytical depth: link arguments to current affairs, add a critical perspective, and maintain formal academic tone.'
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function cap(s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }
    function roman(n) {
        var numerals = ['I', 'II', 'III', 'IV', 'V', 'VI'];
        return numerals[n] || ('Part ' + (n + 1));
    }

    goBtn.addEventListener('click', function () {
        hideError();
        copyMsg.classList.add('d-none');
        var topic = topicInput.value.trim();
        if (!topic) { showError('Please enter the essay topic.'); return; }
        if (topic.length < 4) { showError('Please enter the topic in a bit more detail.'); return; }
        var type = typeSel.value;
        var n = parseInt(paraSel.value, 10) || 3;
        var level = levelSel.value;
        var topicCap = cap(topic);

        var html = '';
        var text = '';
        function h(t, lvl) {
            html += '<' + lvl + ' class="mt-3">' + esc(t) + '</' + lvl + '>';
            text += '\n' + t + '\n' + new Array(t.length + 1).join('-') + '\n';
        }
        function p(t) {
            html += '<p class="mb-1">' + t + '</p>';
            text += t.replace(/<[^>]+>/g, '') + '\n';
        }
        function li(items) {
            html += '<ul>';
            items.forEach(function (it) {
                html += '<li>' + it + '</li>';
                text += '- ' + it.replace(/<[^>]+>/g, '') + '\n';
            });
            html += '</ul>';
        }

        h('Essay Outline: ' + topicCap, 'h4');
        p('<strong>Type:</strong> ' + cap(type) + ' &nbsp;|&nbsp; <strong>Level:</strong> ' + cap(level) + ' &nbsp;|&nbsp; <strong>Body paragraphs:</strong> ' + n);

        h('I. Introduction', 'h5');
        li([
            '<strong>Hook:</strong> ' + esc(HOOKS[type][0]) + '.',
            '<strong>Background:</strong> 2-3 sentences introducing "' + esc(topicCap) + '" and why it matters.',
            '<strong>Thesis statement:</strong> ' + esc(THESIS[type])
        ]);

        for (var i = 1; i <= n; i++) {
            h(roman(i) + '. Body Paragraph ' + i, 'h5');
            var pts = BODY_POINTS[type];
            var main = pts[(i - 1) % pts.length];
            li([
                '<strong>Topic sentence:</strong> ' + esc(main) + ' — connected to "' + esc(topicCap) + '".',
                '<strong>Support:</strong> a concrete example, fact, or explanation.',
                '<strong>Detail:</strong> one more supporting point or piece of evidence.',
                '<strong>Transition:</strong> start with "' + TRANSITIONS[(i - 1) % TRANSITIONS.length] + '" to link from the previous paragraph.'
            ]);
        }

        h('Conclusion', 'h5');
        li([
            '<strong>Restate thesis</strong> in new words — do not copy the introduction.',
            '<strong>Summarize</strong> the ' + n + ' main points in 2-3 sentences.',
            '<strong>Closing thought:</strong> ' + esc(CLOSING[type])
        ]);

        h('Writing tips', 'h5');
        li([
            '<strong>Level tip (' + cap(level) + '):</strong> ' + esc(LEVEL_TIP[level]),
            'Keep each paragraph focused on ONE main idea.',
            'Proofread for grammar and spelling before submitting.'
        ]);

        lastText = 'ESSAY OUTLINE\nTopic: ' + topicCap + '\nType: ' + cap(type) + ' | Level: ' + cap(level) + '\n' + text;
        outlineOut.innerHTML = html;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    copyBtn.addEventListener('click', function () {
        if (!lastText) return;
        function done() {
            copyMsg.classList.remove('d-none');
            setTimeout(function () { copyMsg.classList.add('d-none'); }, 2500);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastText).then(done).catch(function () { fallbackCopy(); done(); });
        } else {
            fallbackCopy();
            done();
        }
    });
    function fallbackCopy() {
        var ta = document.createElement('textarea');
        ta.value = lastText;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) { /* ignore */ }
        ta.remove();
    }

    dlBtn.addEventListener('click', function () {
        if (!lastText) return;
        var blob = new Blob([lastText], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'essay-outline.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () {
            URL.revokeObjectURL(a.href);
            a.remove();
        }, 500);
    });
})();
</script>
@endsection
