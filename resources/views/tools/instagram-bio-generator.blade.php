@extends('layouts.app')
@section('title', 'Instagram Bio Generator - Azlaan Tools')
@section('meta_description', 'Create a stylish Instagram bio with fancy fonts and emojis. Enter a few words, get copy-ready bios within the 150-character limit — free.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Instagram Bio Generator</h1>
            <p class="lead text-muted">Make a stylish Instagram bio — with fancy fonts and emojis. Enter a few words and copy ready bios (the 150-character limit is respected).</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="bName" class="form-label fw-semibold">Name / brand</label>
                            <input type="text" class="form-control" id="bName" placeholder="e.g. Arslan Malik">
                        </div>
                        <div class="col-md-6">
                            <label for="bNiche" class="form-label fw-semibold">Niche / work</label>
                            <input type="text" class="form-control" id="bNiche" placeholder="e.g. Solar installer">
                        </div>
                        <div class="col-md-6">
                            <label for="bLoc" class="form-label fw-semibold">Location</label>
                            <input type="text" class="form-control" id="bLoc" placeholder="e.g. Faisalabad">
                        </div>
                        <div class="col-md-6">
                            <label for="bCta" class="form-label fw-semibold">Call to action</label>
                            <input type="text" class="form-control" id="bCta" placeholder="e.g. DM for orders">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="bVibe" class="form-label fw-semibold">Vibe</label>
                        <select class="form-select" id="bVibe">
                            <option value="pro">Professional</option>
                            <option value="fun">Fun / playful</option>
                            <option value="aesthetic">Aesthetic</option>
                            <option value="bold">Bold / attitude</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Bios</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your name, work, location and call-to-action.</li>
                <li>Choose a vibe and press Generate — you will get 4 stylish bios.</li>
                <li>Copy your favorite bio with the "Copy" button and paste it into your Instagram profile.</li>
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

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    var OFF = {
        bold: { up: 0x1D400, lo: 0x1D41A, dig: 0x1D7CE },
        italic: { up: 0x1D434, lo: 0x1D44E, dig: -1 },
        mono: { up: 0x1D670, lo: 0x1D68A, dig: 0x1D7F6 },
        script: { up: 0x1D4D0, lo: 0x1D4EE, dig: -1 }
    };
    var SMALLCAPS = { a: 'ᴀ', b: 'ʙ', c: 'ᴄ', d: 'ᴅ', e: 'ᴇ', f: 'ꜰ', g: 'ɢ', h: 'ʜ', i: 'ɪ', j: 'ᴊ', k: 'ᴋ', l: 'ʟ', m: 'ᴍ', n: 'ɴ', o: 'ᴏ', p: 'ᴘ', q: 'ǫ', r: 'ʀ', s: 's', t: 'ᴛ', u: 'ᴜ', v: 'ᴠ', w: 'ᴡ', x: 'x', y: 'ʏ', z: 'ᴢ' };

    function stylize(text, style) {
        if (style === 'smallcaps') {
            return text.split('').map(function (c) {
                var l = c.toLowerCase();
                return SMALLCAPS[l] || c;
            }).join('');
        }
        var o = OFF[style];
        if (!o) return text;
        return text.split('').map(function (c) {
            var code = c.charCodeAt(0);
            if (code >= 65 && code <= 90) return String.fromCodePoint(o.up + (code - 65));
            if (code >= 97 && code <= 122) return String.fromCodePoint(o.lo + (code - 97));
            if (code >= 48 && code <= 57 && o.dig >= 0) return String.fromCodePoint(o.dig + (code - 48));
            return c;
        }).join('');
    }

    var VIBES = {
        pro: { emojis: ['💼', '📍', '📩'], head: 'Welcome to my profile' },
        fun: { emojis: ['🎉', '😎', '✨'], head: 'Life is a party' },
        aesthetic: { emojis: ['🌙', '🤍', '🕊️'], head: 'Soft souls only' },
        bold: { emojis: ['🔥', '⚡', '👑'], head: 'No competition' }
    };

    goBtn.addEventListener('click', function () {
        hideError();
        var name = document.getElementById('bName').value.trim();
        var niche = document.getElementById('bNiche').value.trim();
        var loc = document.getElementById('bLoc').value.trim();
        var cta = document.getElementById('bCta').value.trim();
        var vibeKey = document.getElementById('bVibe').value;
        if (!name && !niche) { showError('Please enter at least a name or niche.'); return; }
        var vibe = VIBES[vibeKey];
        var parts = [];
        if (name) parts.push(name);
        if (niche) parts.push(niche);
        var line2 = [];
        if (loc) line2.push('📍 ' + loc);
        if (cta) line2.push('👇 ' + cta);

        var combos = [
            { style: 'bold', sep: ' | ', e: [vibe.emojis[0], vibe.emojis[1]] },
            { style: 'script', sep: ' ✦ ', e: [vibe.emojis[2], vibe.emojis[1]] },
            { style: 'smallcaps', sep: ' • ', e: [vibe.emojis[1], vibe.emojis[0]] },
            { style: 'mono', sep: ' — ', e: [vibe.emojis[2], vibe.emojis[0]] }
        ];
        var html = '';
        combos.forEach(function (cb, i) {
            var l1 = cb.e[0] + ' ' + stylize(parts.join(cb.sep), cb.style);
            var bio = l1;
            if (line2.length) bio += '\n' + line2.join('  ');
            if (i % 2 === 1) bio += '\n' + stylize(vibe.head, cb.style === 'script' ? 'italic' : 'smallcaps') + ' ' + cb.e[1];
            var len = Array.from(bio).length;
            var ok = len <= 150;
            html += '<div class="border rounded p-3 mb-3 bg-white">' +
                '<div class="d-flex justify-content-between align-items-center mb-2">' +
                '<span class="fw-semibold small">Bio ' + (i + 1) + ' — ' + cb.style + '</span>' +
                '<span class="badge ' + (ok ? 'bg-success' : 'bg-danger') + '">' + len + '/150</span></div>' +
                '<p class="mb-2" style="white-space: pre-line;">' + bio.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</p>' +
                '<button type="button" class="btn btn-outline-primary btn-sm" data-bio="' + i + '">Copy Bio</button></div>';
        });
        results.innerHTML = html;
        results.classList.remove('d-none');
        results.querySelectorAll('[data-bio]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var p = btn.parentElement.querySelector('p');
                navigator.clipboard.writeText(p.innerText).then(function () {
                    var old = btn.textContent;
                    btn.textContent = 'Copied!';
                    setTimeout(function () { btn.textContent = old; }, 1200);
                });
            });
        });
    });
})();
</script>
@endsection
