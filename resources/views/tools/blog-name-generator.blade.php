@extends('layouts.app')
@section('title', 'Blog Name Generator — Azlaan Tools')
@section('meta_description', 'Generate catchy blog names and domain ideas from your niche. Free online blog name generator — no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Blog Name Generator</h1>
            <p class="lead text-muted">Create catchy blog names and domain ideas from your niche. Type a keyword, pick a style — free for new bloggers.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="keyword" class="form-label fw-semibold">Your niche / keyword</label>
                            <input type="text" class="form-control" id="keyword" placeholder="e.g. cooking, tech, travel, fitness">
                            <div class="form-text">Write one or two words — the names will be built from them.</div>
                        </div>
                        <div class="col-6">
                            <label for="styleSel" class="form-label fw-semibold">Style</label>
                            <select class="form-select" id="styleSel">
                                <option value="catchy" selected>Catchy</option>
                                <option value="pro">Professional</option>
                                <option value="fun">Fun / playful</option>
                                <option value="short">Short &amp; brandable</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="countSel" class="form-label fw-semibold">How many names</label>
                            <select class="form-select" id="countSel">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="twoWords">
                                <label class="form-check-label" for="twoWords">Also use a two-word keyword (for example "desi cooking")</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate Names</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="h5 mb-0">Generated names</h2>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="againBtn">Generate Again</button>
                        </div>
                        <p class="text-muted small">The domain is only an <em>idea</em> — check real availability at a domain registrar (like Namecheap, GoDaddy).</p>
                        <div class="list-group" id="nameList"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your niche keyword (for example <em>fitness</em>).</li>
                <li>Pick a style and count, then press <strong>Generate Names</strong>.</li>
                <li>Use the copy button next to your favorite name to copy the domain idea.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var BANKS = {
        catchy: {
            pre: ['The', 'Daily', 'Super', 'Mega', 'True', 'Real', 'Epic', 'Fresh', 'Bold', 'Prime'],
            suf: ['Hub', 'Buzz', 'Junction', 'Nest', 'Wave', 'Spot', 'Vibes', 'Zone', 'Tribe', 'Dose'],
            mid: ['Talks', 'Diaries', 'Chronicles', 'Unplugged', 'Decoded', 'Simplified', 'Insider', 'Addict']
        },
        pro: {
            pre: ['Pro', 'Elite', 'Prime', 'Apex', 'Smart', 'Sharp', 'Clear', 'Solid', 'Expert', 'Master'],
            suf: ['Insights', 'Advisory', 'Journal', 'Review', 'Digest', 'Brief', 'Council', 'Lab', 'Works', 'Edge'],
            mid: ['Strategies', 'Mastery', 'Blueprint', 'Playbook', 'Standards', 'Perspective', 'Analysis', 'Guide']
        },
        fun: {
            pre: ['Happy', 'Sunny', 'Wacky', 'Cozy', 'Jolly', 'Peppy', 'Zippy', 'Cheeky', 'Bubbly', 'Snazzy'],
            suf: ['Club', 'Party', 'Corner', 'Land', 'Fiesta', 'Nook', 'Gang', 'Den', 'Hut', 'Circus'],
            mid: ['Doodles', 'Giggles', 'Wanderings', 'Munchies', 'Capers', 'Tales', 'Antics', 'Whims']
        },
        short: {
            pre: ['Go', 'My', 'Get', 'Try', 'Up', 'Neo', 'Uni', 'Ez', 'Yo', 'Re'],
            suf: ['ly', 'io', 'hub', 'box', 'bay', 'ora', 'nest', 'deck', 'wise', 'loop'],
            mid: ['bit', 'pod', 'stack', 'gram', 'feed', 'post', 'note', 'byte']
        }
    };
    var FILLER = ['Daily', 'Desi', 'Urban', 'Modern', 'Simple', 'Honest', 'Local', 'Global', 'Smart', 'Easy'];

    var goBtn = document.getElementById('goBtn');
    var againBtn = document.getElementById('againBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var nameList = document.getElementById('nameList');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }
    function shuffle(arr) {
        var a = arr.slice();
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = a[i]; a[i] = a[j]; a[j] = t;
        }
        return a;
    }
    function domainify(name) {
        return name.toLowerCase().replace(/[^a-z0-9]+/g, '') + '.com';
    }

    function buildNames() {
        var kwRaw = document.getElementById('keyword').value.trim().toLowerCase();
        if (!kwRaw) { return null; }
        var words = kwRaw.split(/\s+/).filter(Boolean);
        var kw = words.map(cap).join(' ');
        var kwFlat = words.map(cap).join('');
        var kw2 = document.getElementById('twoWords').checked && words.length > 1 ? words.map(cap).join(' ') : kw;
        var style = document.getElementById('styleSel').value;
        var bank = BANKS[style] || BANKS.catchy;
        var names = [];
        var seen = {};

        function add(n) {
            n = n.replace(/\s+/g, ' ').trim();
            if (n && !seen[n.toLowerCase()] && n.length <= 40) { seen[n.toLowerCase()] = 1; names.push(n); }
        }

        var pre = shuffle(bank.pre), suf = shuffle(bank.suf), mid = shuffle(bank.mid), fil = shuffle(FILLER);
        var i;
        for (i = 0; i < pre.length; i++) { add(pre[i] + ' ' + kw); }
        for (i = 0; i < suf.length; i++) { add(kw + ' ' + suf[i]); }
        for (i = 0; i < mid.length; i++) { add(kw + ' ' + mid[i]); }
        for (i = 0; i < fil.length; i++) { add(fil[i] + ' ' + kw2); }
        // brandable fused forms
        for (i = 0; i < suf.length; i++) { add(kwFlat + suf[i]); }
        if (style === 'short') {
            for (i = 0; i < pre.length; i++) { add(pre[i] + kwFlat); }
            for (i = 0; i < bank.suf.length; i++) { add(words[0] + bank.suf[i]); }
        }
        // keyword + action nouns
        var acts = ['Guide', 'Tips', 'Hacks', '101', 'Basics', 'Today'];
        for (i = 0; i < acts.length; i++) { add(kw + ' ' + acts[i]); }
        return shuffle(names);
    }

    function render() {
        hideError();
        var names = buildNames();
        if (!names) { showError('Please enter your niche / keyword first.'); return; }
        var count = parseInt(document.getElementById('countSel').value, 10);
        names = names.slice(0, count);
        nameList.innerHTML = '';
        for (var i = 0; i < names.length; i++) {
            (function (nm) {
                var item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center gap-2 flex-wrap';
                var left = document.createElement('div');
                var strong = document.createElement('div');
                strong.className = 'fw-semibold';
                strong.textContent = nm;
                var dom = document.createElement('div');
                dom.className = 'text-muted small';
                dom.textContent = domainify(nm);
                left.appendChild(strong);
                left.appendChild(dom);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-primary btn-sm';
                btn.textContent = 'Copy domain';
                btn.addEventListener('click', function () {
                    var d = domainify(nm);
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(d).then(function () {
                            btn.textContent = 'Copied!';
                            setTimeout(function () { btn.textContent = 'Copy domain'; }, 1500);
                        });
                    } else {
                        var ta = document.createElement('textarea');
                        ta.value = d;
                        document.body.appendChild(ta);
                        ta.select();
                        try { document.execCommand('copy'); } catch (e) { /* ignore */ }
                        document.body.removeChild(ta);
                        btn.textContent = 'Copied!';
                        setTimeout(function () { btn.textContent = 'Copy domain'; }, 1500);
                    }
                });
                item.appendChild(left);
                item.appendChild(btn);
                nameList.appendChild(item);
            })(names[i]);
        }
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', render);
    againBtn.addEventListener('click', render);
})();
</script>
@endsection
