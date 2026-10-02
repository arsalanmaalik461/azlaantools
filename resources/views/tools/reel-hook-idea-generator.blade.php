@extends('layouts.app')

@section('title', 'Reel Hook Idea Generator - Azlaan Tools')
@section('meta_description', 'Generate catchy reel hooks and short video ideas for TikTok and Reels. Free content tool, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Reel Hook Idea Generator</h1>
            <p class="lead text-muted">Create catchy hooks and video ideas for Reels and TikTok - matched to your niche, template-based.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="nicheInput" class="form-label fw-semibold">Your niche / topic</label>
                            <input type="text" class="form-control" id="nicheInput" placeholder="e.g. solar panels, cooking, freelancing">
                        </div>
                        <div class="col-md-3">
                            <label for="toneSel" class="form-label fw-semibold">Hook style</label>
                            <select class="form-select" id="toneSel">
                                <option value="curiosity">Curiosity</option>
                                <option value="bold">Bold claim</option>
                                <option value="list">List / number</option>
                                <option value="question">Question</option>
                                <option value="howto">How-to</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="countSel" class="form-label fw-semibold">How many ideas</label>
                            <select class="form-select" id="countSel">
                                <option value="5">5 ideas</option>
                                <option value="10" selected>10 ideas</option>
                                <option value="15">15 ideas</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Generate Hooks</button>
                        <button type="button" class="btn btn-outline-secondary" id="copyAllBtn" disabled>Copy All</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="fw-semibold mb-2">Your reel hooks:</div>
                        <div id="hookList" class="d-flex flex-column gap-2"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your niche or topic (e.g. <em>solar panels</em>).</li>
                <li>Choose a hook style - curiosity, bold claim, list, question or how-to.</li>
                <li>Press <strong>Generate Hooks</strong> - unique ideas will appear below.</li>
                <li>Copy your favorite hook with its Copy button and use it in your reel.</li>
            </ol>
            <p class="small text-muted">Note: this tool is based on proven hook templates - the first 3 seconds of the hook matter most for a reel's success.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var TEMPLATES = {
        curiosity: [
            'Nobody is talking about this {niche} secret...',
            'I wasted 3 years on {niche} - if I had known this before...',
            '99% of people do not know this {niche} trick.',
            'Stop scrolling - this is the most underrated {niche} secret.',
            'If you are serious about {niche}, watch this reel till the end.',
            '{niche} experts keep this one hidden.',
            'This one {niche} mistake can cost you thousands.',
            'I tried this {niche} experiment myself - the result was shocking.'
        ],
        bold: [
            'Everything you know about {niche} is wrong.',
            'This is the fastest way to succeed in {niche} - period.',
            'Why do 90% of people fail at {niche}? This is why.',
            'Without {niche}, you are wasting money.',
            'This {niche} strategy changed everything.',
            'Everyone says {niche} is hard - this reel will change your mind.',
            'Double your {niche} results with just this one change.',
            'You will regret not trying this {niche} tip.'
        ],
        list: [
            '5 {niche} tips every beginner should know.',
            'Top 3 {niche} mistakes - number 2 is the most common.',
            'Learn {niche} in 7 days - a day by day plan.',
            '10 {niche} tools that make the work easy.',
            '5 hidden {niche} benefits you are missing.',
            '4 {niche} myths people still believe are true.',
            'My top 5 recommendations for {niche}.',
            '3 things every {niche} lover should know.'
        ],
        question: [
            'Are you also making this {niche} mistake?',
            'Does {niche} really help, or is it just hype?',
            'If you had to start {niche} again, what would you do differently?',
            'What is the best time for {niche}? The answer will surprise you.',
            'Is buying the cheap {niche} option smart?',
            'Ask yourself this 1 question before investing in {niche}.',
            'Why do people quit {niche}? This is the real reason.',
            'What is the biggest benefit of {niche} in your opinion? Comment below.'
        ],
        howto: [
            'An easy way to start {niche} from zero.',
            'Step by step: how to take your first step in {niche}.',
            'How to make a {niche} budget - simple formula.',
            'How to learn {niche} from home - free resources.',
            'Follow this routine for the best {niche} results.',
            'Do this {niche} task yourself in just 5 minutes.',
            'How to choose the right {niche} option - complete guide.',
            '{niche} for beginners: first week plan.'
        ]
    };

    var nicheInput = document.getElementById('nicheInput');
    var toneSel = document.getElementById('toneSel');
    var countSel = document.getElementById('countSel');
    var goBtn = document.getElementById('goBtn');
    var copyAllBtn = document.getElementById('copyAllBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var hookList = document.getElementById('hookList');
    var lastHooks = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function shuffle(arr) {
        var a = arr.slice();
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = a[i]; a[i] = a[j]; a[j] = t;
        }
        return a;
    }
    function capNiche(n) {
        return n.charAt(0).toUpperCase() + n.slice(1);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var niche = nicheInput.value.trim().replace(/\s+/g, ' ');
        if (!niche) { showError('Please enter your niche or topic.'); return; }
        if (niche.length > 60) { showError('Keep the topic under 60 characters.'); return; }
        var tone = toneSel.value;
        var count = parseInt(countSel.value, 10);
        var pool = shuffle(TEMPLATES[tone]);
        var hooks = [];
        var i = 0;
        while (hooks.length < count) {
            var tpl = pool[i % pool.length];
            var hook = tpl.replace(/\{niche\}/g, capNiche(niche));
            if (hooks.indexOf(hook) === -1) hooks.push(hook);
            i++;
            if (i > count * 10) break;
        }
        lastHooks = hooks;

        hookList.innerHTML = '';
        hooks.forEach(function (h, idx) {
            var row = document.createElement('div');
            row.className = 'd-flex gap-2 align-items-start border rounded p-2 bg-light';
            var num = document.createElement('span');
            num.className = 'badge bg-primary mt-1';
            num.textContent = (idx + 1);
            var txt = document.createElement('span');
            txt.className = 'flex-grow-1';
            txt.textContent = h;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary';
            btn.textContent = 'Copy';
            btn.addEventListener('click', function () {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(h).then(function () {
                        btn.textContent = 'Copied!';
                        setTimeout(function () { btn.textContent = 'Copy'; }, 1500);
                    });
                }
            });
            row.appendChild(num); row.appendChild(txt); row.appendChild(btn);
            hookList.appendChild(row);
        });

        copyAllBtn.disabled = false;
        results.classList.remove('d-none');
    });

    copyAllBtn.addEventListener('click', function () {
        if (!lastHooks.length) return;
        var all = lastHooks.map(function (h, i) { return (i + 1) + '. ' + h; }).join('\n');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(all).then(function () {
                copyAllBtn.textContent = 'All copied!';
                setTimeout(function () { copyAllBtn.textContent = 'Copy All'; }, 2000);
            });
        }
    });
})();
</script>
@endsection
