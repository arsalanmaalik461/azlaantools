@extends('layouts.app')

@section('title', 'Wedding Hashtag Generator - Azlaan Tools')
@section('meta_description', 'Make fun wedding hashtags from the couple names. Create a wedding hashtag with this free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Wedding Hashtag Generator</h1>
            <p class="lead text-muted">Type the names of the bride and groom and get fun wedding hashtags. Make a hashtag for the wedding and use it on Instagram!</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="name1" class="form-label fw-semibold">First name (e.g. Bride)</label>
                            <input type="text" class="form-control" id="name1" placeholder="e.g. Ayesha">
                        </div>
                        <div class="col-6">
                            <label for="name2" class="form-label fw-semibold">Second name (e.g. Groom)</label>
                            <input type="text" class="form-control" id="name2" placeholder="e.g. Bilal">
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label for="hCount" class="form-label fw-semibold">How many hashtags: <span id="hCountVal">12</span></label>
                        <input type="range" class="form-range" id="hCount" min="4" max="24" value="12">
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Hashtags</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex flex-wrap gap-2 mb-3" id="tagList"></div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="againBtn">Make New</button>
                            <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy All</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type both names (first and last names both work).</li>
                <li>Press "Make Hashtags" and you will get fun combinations.</li>
                <li>Click a favorite to copy it, or use "Copy All" to take the full list.</li>
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
    var tagList = document.getElementById('tagList');
    var lastTags = [];

    function clean(s) {
        return s.trim().toLowerCase().replace(/[^a-z]/g, '');
    }
    function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    document.getElementById('hCount').addEventListener('input', function () {
        document.getElementById('hCountVal').textContent = this.value;
    });

    function shuffle(a) {
        for (var i = a.length - 1; i > 0; i--) { var j = Math.floor(Math.random() * (i + 1)); var t = a[i]; a[i] = a[j]; a[j] = t; }
        return a;
    }

    function generate() {
        hideError();
        var n1raw = document.getElementById('name1').value;
        var n2raw = document.getElementById('name2').value;
        var w1 = clean(n1raw), w2 = clean(n2raw);
        if (w1.length < 2 || w2.length < 2) { showError('Please type both names with at least 2 English letters each.'); return; }

        var parts1 = n1raw.trim().split(/\s+/).map(clean).filter(function (x) { return x.length > 1; });
        var parts2 = n2raw.trim().split(/\s+/).map(clean).filter(function (x) { return x.length > 1; });
        var first1 = parts1[0] || w1, last1 = parts1.length > 1 ? parts1[parts1.length - 1] : '';
        var first2 = parts2[0] || w2, last2 = parts2.length > 1 ? parts2[parts2.length - 1] : '';

        var combos = [];
        function add(s) { s = s.replace(/[^a-zA-Z]/g, ''); if (s.length >= 4 && combos.indexOf(s) === -1) combos.push(s); }

        // Name mashups
        add(first1 + first2);
        add(first2 + first1);
        add(first1.slice(0, 3) + first2.slice(0, 3));
        add(first2.slice(0, 3) + first1.slice(0, 3));
        add(first1.slice(0, Math.ceil(first1.length / 2)) + first2.slice(Math.floor(first2.length / 2)));
        add(first2.slice(0, Math.ceil(first2.length / 2)) + first1.slice(Math.floor(first1.length / 2)));
        if (last1) add(first1 + last1);
        if (last2) add(first2 + last2);
        if (last1 && last2) add(last1 + last2);

        // Pun templates
        var puns = [
            first1 + 'SaysYes', first2 + 'SaysYes',
            'Hitched' + cap(first1) + cap(first2),
            cap(first1) + 'Got' + cap(first2),
            cap(first2) + 'Got' + cap(first1),
            'Finally' + cap(first1) + cap(first2),
            cap(first1) + 'Weds' + cap(first2),
            'Forever' + cap(first1) + cap(first2),
            'MrAndMrs' + cap(last2 || first2),
            cap(first1) + 'To' + cap(first2),
            'Love' + cap(first1) + cap(first2),
            'Just' + cap(first1) + 'Married',
            'Just' + cap(first2) + 'Married',
            cap(first1) + 'And' + cap(first2) + 'Forever',
            'The' + cap(last2 || first2) + 'Wedding',
            'Shaadi' + cap(first1) + cap(first2),
            cap(first1) + 'KiShaadi',
            'HappyEver' + cap(first1) + cap(first2),
            cap(first1) + cap(first2) + 'EverAfter',
            'SealedWith' + cap(first1) + cap(first2)
        ];
        puns.forEach(add);
        shuffle(combos);

        var n = parseInt(document.getElementById('hCount').value, 10);
        lastTags = combos.slice(0, n).map(function (c) { return '#' + c; });

        tagList.innerHTML = '';
        lastTags.forEach(function (t) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'btn btn-outline-primary btn-sm';
            b.textContent = t;
            b.title = 'Click to copy';
            b.addEventListener('click', function () {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(t);
                    b.textContent = '✓ ' + t;
                    setTimeout(function () { b.textContent = t; }, 1200);
                }
            });
            tagList.appendChild(b);
        });
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', generate);
    document.getElementById('againBtn').addEventListener('click', generate);

    document.getElementById('copyBtn').addEventListener('click', function () {
        if (!lastTags.length) return;
        var txt = lastTags.join(' ');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(txt).then(function () {
                var b = document.getElementById('copyBtn');
                b.textContent = 'Copied!';
                setTimeout(function () { b.textContent = 'Copy All'; }, 1500);
            });
        }
    });
})();
</script>
@endsection
