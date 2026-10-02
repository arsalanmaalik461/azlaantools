@extends('layouts.app')
@section('title', 'Username Generator - Azlaan Tools')
@section('meta_description', 'Create cool unique usernames for games, Instagram and social media. Free online username generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Username Generator</h1>
            <p class="lead text-muted">Create cool and unique usernames for games, Instagram and social media. Enter your own word or get random ideas.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="baseWord" class="form-label fw-semibold">Your word (optional)</label>
                        <input type="text" class="form-control" id="baseWord" placeholder="e.g. tiger, arslan, gamer" maxlength="20">
                        <div class="form-text">Leave it empty to get fully random names.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="styleSel" class="form-label fw-semibold">Style</label>
                            <select class="form-select" id="styleSel">
                                <option value="gamer">Gamer tags</option>
                                <option value="aesthetic">Aesthetic / soft</option>
                                <option value="professional">Professional</option>
                                <option value="funny">Funny</option>
                                <option value="random" selected>Mix (random)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="countSel" class="form-label fw-semibold">How many names</label>
                            <select class="form-select" id="countSel">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="addNumbers" checked>
                        <label class="form-check-label" for="addNumbers">Add numbers (like 786, 007)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="addSymbols">
                        <label class="form-check-label" for="addSymbols">Add symbols ( _ . xX )</label>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Usernames</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h6 mb-3">Tap a name to copy it:</h2>
                        <div id="nameList" class="list-group"></div>
                        <p class="text-muted small mt-3 mb-0">Note: We do not check if a name is available — check yourself on Instagram or in the game whether the name is free.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>If you want, type your own word (like your name).</li>
                <li>Select a style and a count.</li>
                <li>Press "Generate Usernames" — tap your favourite name to copy it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var nameList = document.getElementById('nameList');
    var baseWord = document.getElementById('baseWord');
    var styleSel = document.getElementById('styleSel');
    var countSel = document.getElementById('countSel');
    var addNumbers = document.getElementById('addNumbers');
    var addSymbols = document.getElementById('addSymbols');

    var WORDS = {
        gamer: ['shadow', 'blitz', 'venom', 'rage', 'sniper', 'fury', 'ghost', 'havoc', 'storm', 'wolf', 'reaper', 'vortex', 'titan', 'nova', 'strike', 'phantom', 'zephyr', 'onyx'],
        aesthetic: ['moon', 'cloud', 'petal', 'willow', 'dusk', 'luna', 'mist', 'ivy', 'dream', 'sakura', 'velvet', 'aurora', 'rain', 'moss', 'ember', 'star'],
        professional: ['creative', 'digital', 'studio', 'labs', 'works', 'hub', 'pro', 'tech', 'media', 'design', 'pixel', 'craft'],
        funny: ['noob', 'potato', 'waffle', 'socks', 'pickle', 'noodle', 'muffin', 'taco', 'banana', 'chicken', 'donut', 'panda', 'llama', 'tofu'],
        random: ['tiger', 'falcon', 'river', 'comet', 'pixel', 'echo', 'blaze', 'drift', 'onyx', 'sage', 'jolt', 'frost', 'ember', 'kite']
    };
    var ADJECTIVES = ['dark', 'wild', 'swift', 'silent', 'golden', 'electric', 'cosmic', 'brave', 'lucky', 'midnight', 'crimson', 'neon', 'frosty', 'rapid'];
    var TITLES = ['king', 'queen', 'master', 'legend', 'boss', 'chief', 'hero', 'star'];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
    function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }
    function cleanBase(s) {
        return s.toLowerCase().replace(/[^a-z0-9]/g, '').slice(0, 20);
    }
    function numSuffix() {
        var n = Math.random();
        if (n < 0.25) return String(Math.floor(Math.random() * 100));
        if (n < 0.5) return String(100 + Math.floor(Math.random() * 900));
        if (n < 0.7) return '786';
        if (n < 0.85) return '007';
        return String(10 + Math.floor(Math.random() * 90));
    }

    function buildName(style, base) {
        var pool = WORDS[style] || WORDS.random;
        var word = base || pick(pool);
        var r = Math.random();
        var name;
        if (style === 'professional' || r < 0.2) {
            name = word + pick(['', '_', '.']) + pick(WORDS.professional.concat(TITLES));
        } else if (style === 'funny' || r < 0.4) {
            name = pick(ADJECTIVES) + cap(word);
        } else if (r < 0.6) {
            name = pick(TITLES) + cap(word);
        } else if (r < 0.8) {
            name = word + 'x' + pick(pool).charAt(0).toUpperCase() + pick(pool).slice(1);
        } else {
            name = pick(ADJECTIVES) + '_' + word;
        }
        if (addSymbols.checked && Math.random() < 0.5) {
            var sym = pick(['_', '.', 'xX', '._']);
            name = (Math.random() < 0.5 ? sym : '') + name + (Math.random() < 0.5 ? sym.replace('xX', 'Xx') : '');
        }
        if (addNumbers.checked && Math.random() < 0.7) {
            name += (Math.random() < 0.3 ? '_' : '') + numSuffix();
        }
        return name.replace(/[^a-zA-Z0-9._]/g, '');
    }

    function copyText(text, el) {
        function done() {
            el.classList.add('active');
            setTimeout(function () { el.classList.remove('active'); }, 600);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, done);
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
            done();
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var base = cleanBase(baseWord.value.trim());
        var style = styleSel.value;
        var count = parseInt(countSel.value, 10);
        var seen = {};
        var out = [];
        var guard = 0;
        while (out.length < count && guard < count * 40) {
            guard++;
            var useStyle = style === 'random' ? pick(['gamer', 'aesthetic', 'funny']) : style;
            var nm = buildName(useStyle, base);
            if (!nm || seen[nm]) continue;
            seen[nm] = true;
            out.push(nm);
        }
        if (!out.length) { showError('Could not generate a name. Please try again.'); return; }
        nameList.innerHTML = '';
        out.forEach(function (nm) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            var span = document.createElement('span');
            span.textContent = nm;
            span.className = 'fw-semibold text-break';
            var badge = document.createElement('span');
            badge.className = 'badge bg-primary rounded-pill';
            badge.textContent = 'Copy';
            b.appendChild(span);
            b.appendChild(badge);
            b.addEventListener('click', function () { copyText(nm, b); });
            nameList.appendChild(b);
        });
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
