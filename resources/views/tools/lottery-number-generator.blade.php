@extends('layouts.app')
@section('title', 'Lottery Number Generator - Lucky Random Picks | Azlaan Tools')
@section('meta_description', 'Generate lucky lottery numbers for free with cryptographically secure random picks. Customize ball count, range and bonus balls — instant results, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Lottery Number Generator</h1>
            <p class="lead text-muted">Create your lucky lottery numbers — smart random picks, bonus ball option and sorted results. Good luck!</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="presetSel" class="form-label fw-semibold">Lottery preset</label>
                            <select class="form-select" id="presetSel">
                                <option value="custom">Custom</option>
                                <option value="powerball">Powerball (5 of 1–69 + 1 of 1–26)</option>
                                <option value="megamillions">Mega Millions (5 of 1–70 + 1 of 1–25)</option>
                                <option value="649">6/49 (6 of 1–49)</option>
                                <option value="prizebond">Prize Bond style (6 digits 0–9)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="setsInput" class="form-label fw-semibold">Number of sets (how many tickets)</label>
                            <input type="number" class="form-control" id="setsInput" value="5" min="1" max="20">
                        </div>
                        <div class="col-md-4">
                            <label for="ballsInput" class="form-label fw-semibold">Main balls</label>
                            <input type="number" class="form-control" id="ballsInput" value="6" min="1" max="20">
                        </div>
                        <div class="col-md-4">
                            <label for="minInput" class="form-label fw-semibold">Range from</label>
                            <input type="number" class="form-control" id="minInput" value="1" min="0" max="999">
                        </div>
                        <div class="col-md-4">
                            <label for="maxInput" class="form-label fw-semibold">Range to</label>
                            <input type="number" class="form-control" id="maxInput" value="49" min="1" max="999">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="bonusCheck">
                                <label class="form-check-label fw-semibold" for="bonusCheck">Bonus ball (from a separate range)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2" id="bonusRangeWrap" style="display:none;">
                                <div class="col-6">
                                    <label for="bMinInput" class="form-label small">Bonus from</label>
                                    <input type="number" class="form-control" id="bMinInput" value="1" min="0" max="999">
                                </div>
                                <div class="col-6">
                                    <label for="bMaxInput" class="form-label small">Bonus to</label>
                                    <input type="number" class="form-control" id="bMaxInput" value="26" min="1" max="999">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="sortCheck" checked>
                                <label class="form-check-label fw-semibold" for="sortCheck">Sort numbers (small to big)</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">🎲 Generate Lucky Numbers</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Your Lucky Numbers</h2>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyBtn">📋 Copy All</button>
                        </div>
                        <div id="setsWrap" class="d-flex flex-column gap-3"></div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Note:</strong> This is only a random number generator for fun — no guarantee of winning. Lottery is gambling; play responsibly.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a lottery preset or set your own range.</li>
                <li>Enter how many sets you want — turn on the switch if you want a bonus ball.</li>
                <li>Press <strong>Generate Lucky Numbers</strong>.</li>
                <li>Copy the numbers and use them on your ticket.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var presetSel = document.getElementById('presetSel');
    var setsInput = document.getElementById('setsInput');
    var ballsInput = document.getElementById('ballsInput');
    var minInput = document.getElementById('minInput');
    var maxInput = document.getElementById('maxInput');
    var bonusCheck = document.getElementById('bonusCheck');
    var bonusRangeWrap = document.getElementById('bonusRangeWrap');
    var bMinInput = document.getElementById('bMinInput');
    var bMaxInput = document.getElementById('bMaxInput');
    var sortCheck = document.getElementById('sortCheck');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var setsWrap = document.getElementById('setsWrap');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    var presets = {
        powerball: { balls: 5, min: 1, max: 69, bonus: true, bmin: 1, bmax: 26 },
        megamillions: { balls: 5, min: 1, max: 70, bonus: true, bmin: 1, bmax: 25 },
        '649': { balls: 6, min: 1, max: 49, bonus: false },
        prizebond: { balls: 6, min: 0, max: 9, bonus: false }
    };

    presetSel.addEventListener('change', function () {
        var p = presets[presetSel.value];
        if (!p) return;
        ballsInput.value = p.balls;
        minInput.value = p.min;
        maxInput.value = p.max;
        bonusCheck.checked = !!p.bonus;
        if (p.bonus) { bMinInput.value = p.bmin; bMaxInput.value = p.bmax; }
        bonusRangeWrap.style.display = p.bonus ? 'block' : 'none';
    });

    bonusCheck.addEventListener('change', function () {
        bonusRangeWrap.style.display = bonusCheck.checked ? 'block' : 'none';
    });

    // Cryptographically secure random int in [min, max]
    function randInt(min, max) {
        var range = max - min + 1;
        var bytes = new Uint32Array(1);
        var limit = Math.floor(4294967296 / range) * range;
        var x;
        do {
            window.crypto.getRandomValues(bytes);
            x = bytes[0];
        } while (x >= limit);
        return min + (x % range);
    }

    function drawUnique(count, min, max, allowDup) {
        var out = [];
        if (allowDup) {
            for (var i = 0; i < count; i++) out.push(randInt(min, max));
            return out;
        }
        var pool = [];
        for (var n = min; n <= max; n++) pool.push(n);
        for (var j = 0; j < count; j++) {
            var idx = randInt(0, pool.length - 1);
            out.push(pool.splice(idx, 1)[0]);
        }
        return out;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var sets = parseInt(setsInput.value, 10);
        var balls = parseInt(ballsInput.value, 10);
        var min = parseInt(minInput.value, 10);
        var max = parseInt(maxInput.value, 10);
        var useBonus = bonusCheck.checked;
        var bmin = parseInt(bMinInput.value, 10);
        var bmax = parseInt(bMaxInput.value, 10);

        if (isNaN(sets) || sets < 1 || sets > 20) { showError('Sets must be between 1 and 20.'); return; }
        if (isNaN(balls) || balls < 1 || balls > 20) { showError('Balls must be between 1 and 20.'); return; }
        if (isNaN(min) || isNaN(max) || min > max) { showError('Invalid range — "from" must be less than "to".'); return; }
        if (max - min < 0) { showError('Invalid range.'); return; }
        var unique = !(presetSel.value === 'prizebond');
        if (unique && (max - min + 1) < balls) { showError('Range is too small to make ' + balls + ' unique numbers.'); return; }
        if (useBonus && (isNaN(bmin) || isNaN(bmax) || bmin > bmax)) { showError('Invalid bonus range.'); return; }

        setsWrap.innerHTML = '';
        for (var s = 0; s < sets; s++) {
            var nums = drawUnique(balls, min, max, !unique);
            if (sortCheck.checked && unique) nums.sort(function (a, b) { return a - b; });
            var bonus = useBonus ? randInt(bmin, bmax) : null;

            var card = document.createElement('div');
            card.className = 'card';
            var body = document.createElement('div');
            body.className = 'card-body py-3';
            var label = document.createElement('div');
            label.className = 'small text-muted mb-2 fw-semibold';
            label.textContent = 'Set ' + (s + 1);
            var row = document.createElement('div');
            row.className = 'd-flex flex-wrap gap-2 align-items-center';
            nums.forEach(function (n) {
                var b = document.createElement('span');
                b.className = 'badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center';
                b.style.cssText = 'width:46px;height:46px;font-size:1.05rem;';
                b.textContent = n;
                row.appendChild(b);
            });
            if (bonus !== null) {
                var plus = document.createElement('span');
                plus.className = 'fw-bold text-muted';
                plus.textContent = '+';
                row.appendChild(plus);
                var bb = document.createElement('span');
                bb.className = 'badge bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center';
                bb.style.cssText = 'width:46px;height:46px;font-size:1.05rem;';
                bb.textContent = bonus;
                row.appendChild(bb);
            }
            body.appendChild(label);
            body.appendChild(row);
            card.appendChild(body);
            setsWrap.appendChild(card);
        }
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        var lines = [];
        var cards = setsWrap.querySelectorAll('.card');
        cards.forEach(function (c, i) {
            var nums = [];
            c.querySelectorAll('.badge').forEach(function (b) { nums.push(b.textContent); });
            lines.push('Set ' + (i + 1) + ': ' + nums.join(', '));
        });
        var text = lines.join('\n');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                copyBtn.textContent = '✓ Copied!';
                setTimeout(function () { copyBtn.textContent = '📋 Copy All'; }, 1500);
            });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); copyBtn.textContent = '✓ Copied!'; } catch (e) {}
            ta.remove();
            setTimeout(function () { copyBtn.textContent = '📋 Copy All'; }, 1500);
        }
    });
})();
</script>
@endsection
