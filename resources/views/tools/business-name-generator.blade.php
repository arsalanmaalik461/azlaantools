@extends('layouts.app')

@section('title', 'Business Name Generator - Azlaan Tools')
@section('meta_description', 'Generate catchy brandable business name ideas online for free. Try different names for your business.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Business Name Generator</h1>
            <p class="lead text-muted">Try catchy, memorable names for your business. Write a keyword, choose a style — new ideas appear instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="kwInput" class="form-label fw-semibold">Keyword (your work / product)</label>
                        <input type="text" class="form-control" id="kwInput" placeholder="example: solar, biryani, boutique, mobile...">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="styleSel" class="form-label fw-semibold">Style</label>
                            <select class="form-select" id="styleSel">
                                <option value="mix" selected>Mix (all styles)</option>
                                <option value="modern">Modern / Startup</option>
                                <option value="classic">Classic / Trusted</option>
                                <option value="playful">Playful / Fun</option>
                                <option value="desi">Local touch</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="countSel" class="form-label fw-semibold">How many names?</label>
                            <select class="form-select" id="countSel">
                                <option value="12">12 ideas</option>
                                <option value="24" selected>24 ideas</option>
                                <option value="40">40 ideas</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Names</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <p class="fw-semibold mb-2">Name ideas <span class="text-muted small">(click a name to copy it)</span></p>
                        <div class="list-group" id="nameList"></div>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-3" id="againBtn">Generate Again</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your business keyword (for example <em>solar</em>, <em>tea</em>, <em>tailor</em>).</li>
                <li>Choose the style and the number, then click <strong>Generate Names</strong>.</li>
                <li>Click a name you like to copy it. Click again to get new ideas.</li>
            </ol>
            <h2>When choosing a name</h2>
            <p>A short, easy to say and easy to remember name works best. Before you decide, check that no one else is using the same name and that the domain and social media handles are free.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var kwInput = document.getElementById('kwInput');
    var styleSel = document.getElementById('styleSel');
    var countSel = document.getElementById('countSel');
    var goBtn = document.getElementById('goBtn');
    var againBtn = document.getElementById('againBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var nameList = document.getElementById('nameList');

    var PREFIX = ['Nova', 'Prime', 'Bright', 'Swift', 'True', 'Ever', 'Pure', 'Apex', 'Smart', 'Rapid', 'Fresh', 'Bold', 'Clear', 'Grand', 'Nexa', 'Viva', 'Ultra', 'Pro', 'Max', 'Star'];
    var SUFFIX = ['ly', 'Hub', 'Labs', 'Works', 'Nest', 'Point', 'Wise', 'Stack', 'Base', 'Line', 'Craft', 'Edge', 'Flow', 'Grid', 'Mint', 'Peak', 'Shift', 'Spark', 'Verse', 'Zone', 'ify', 'ora'];
    var TAIL = ['Co.', 'Studio', 'House', 'Works', 'Point', 'Ghar', 'Bazaar', 'Store', 'Center', 'Services', 'Traders', 'Enterprise'];
    var ADJ = ['Golden', 'Royal', 'Super', 'Happy', 'Shandar', 'Noor', 'Roshan', 'Asli', 'Khaas', 'Naya', 'Sona', 'Chamak'];
    var DESI_TAIL = ['Wala', 'Ghar', 'Bazaar', 'Khana', 'Dukan', 'Adda'];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function cap(s) {
        s = (s || '').trim().replace(/\s+/g, ' ');
        return s.charAt(0).toUpperCase() + s.slice(1);
    }
    function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
    function shuffle(a) {
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = a[i]; a[i] = a[j]; a[j] = t;
        }
        return a;
    }

    function buildNames(kw, style, count) {
        var K = cap(kw);
        var out = [];
        function add(n) {
            n = n.replace(/\s+/g, ' ').trim();
            if (n && out.indexOf(n) === -1) out.push(n);
        }
        var i, p, s;
        if (style === 'mix' || style === 'modern') {
            for (i = 0; i < 14; i++) add(K + pick(SUFFIX));
            for (i = 0; i < 10; i++) add(pick(PREFIX) + K);
            for (i = 0; i < 6; i++) add('Get' + K);
            for (i = 0; i < 6; i++) add('Try' + K);
            for (i = 0; i < 6; i++) add(K + 'Pro');
            for (i = 0; i < 4; i++) { p = pick(PREFIX); add(p + K.charAt(0).toLowerCase() + K.slice(1)); }
        }
        if (style === 'mix' || style === 'classic') {
            for (i = 0; i < 10; i++) add(K + ' ' + pick(TAIL));
            for (i = 0; i < 6; i++) add(pick(ADJ) + ' ' + K);
            for (i = 0; i < 6; i++) add('Al-' + K);
            for (i = 0; i < 4; i++) add('New ' + K + ' ' + pick(TAIL));
        }
        if (style === 'mix' || style === 'playful') {
            for (i = 0; i < 8; i++) add(K + pick(['Pop', 'Buzz', 'Wink', 'Joy', 'Zap', 'Boom', 'Choo', 'Masti']));
            for (i = 0; i < 6; i++) add(pick(['Chai', 'Masti', 'Dhoom', 'Rang']) + K);
            for (i = 0; i < 4; i++) add(K + ' & Co.');
        }
        if (style === 'mix' || style === 'desi') {
            for (i = 0; i < 10; i++) add(K + ' ' + pick(DESI_TAIL));
            for (i = 0; i < 6; i++) add(pick(ADJ) + ' ' + K + ' Wala');
            for (i = 0; i < 4; i++) add('Apna ' + K + ' Ghar');
        }
        // mashups: first half of keyword + suffix
        var half = K.slice(0, Math.max(2, Math.floor(K.length / 2)));
        for (i = 0; i < 8; i++) { s = pick(SUFFIX); add(cap(half + s.toLowerCase())); }
        return shuffle(out).slice(0, count);
    }

    function render(names) {
        nameList.innerHTML = '';
        names.forEach(function (n) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            var span = document.createElement('span');
            span.className = 'fw-semibold';
            span.textContent = n;
            var badge = document.createElement('span');
            badge.className = 'badge bg-primary rounded-pill';
            badge.textContent = 'Copy';
            b.appendChild(span);
            b.appendChild(badge);
            b.addEventListener('click', function () {
                copyText(n);
                badge.textContent = 'Copied!';
                setTimeout(function () { badge.textContent = 'Copy'; }, 1200);
            });
            nameList.appendChild(b);
        });
        results.classList.remove('d-none');
    }

    function copyText(t) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).catch(function () { fallbackCopy(t); });
        } else { fallbackCopy(t); }
    }
    function fallbackCopy(t) {
        var ta = document.createElement('textarea');
        ta.value = t;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
    }

    function generate() {
        hideError();
        var kw = kwInput.value.trim();
        if (!kw) { showError('Please enter a keyword first (for example: solar).'); return; }
        if (kw.length > 30) { showError('Keep the keyword under 30 letters.'); return; }
        var names = buildNames(kw, styleSel.value, parseInt(countSel.value, 10));
        render(names);
    }

    goBtn.addEventListener('click', generate);
    againBtn.addEventListener('click', generate);
})();
</script>
@endsection
