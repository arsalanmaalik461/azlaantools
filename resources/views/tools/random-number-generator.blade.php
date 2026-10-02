@extends('layouts.app')

@section('title', 'Random Number Generator - Dice Roller & Coin Flip | Azlaan Tools')
@section('meta_description', 'Generate secure random numbers between any min and max, with unique and sorted options. Plus a free dice roller and coin flip tool. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Random Number Generator</h1>
            <p class="lead text-muted">Generate truly random numbers for draws, games, passwords seeds and decisions — plus a dice roller and coin flip for fun.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Random Numbers</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="minVal" class="form-label fw-semibold">Minimum</label>
                            <input type="number" class="form-control" id="minVal" value="1">
                        </div>
                        <div class="col-md-4">
                            <label for="maxVal" class="form-label fw-semibold">Maximum</label>
                            <input type="number" class="form-control" id="maxVal" value="100">
                        </div>
                        <div class="col-md-4">
                            <label for="countVal" class="form-label fw-semibold">How Many Numbers</label>
                            <input type="number" class="form-control" id="countVal" value="5" min="1" max="1000">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="uniqueToggle">
                        <label class="form-check-label" for="uniqueToggle">Unique numbers only (no repeats)</label>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="sortedToggle">
                        <label class="form-check-label" for="sortedToggle">Sort results (small to large)</label>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="generateBtn">Generate Numbers</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox"></div>
                    <div id="resultWrap" class="d-none mt-3">
                        <label for="resultBox" class="form-label fw-semibold">Results</label>
                        <textarea class="form-control" id="resultBox" rows="4" readonly></textarea>
                        <button type="button" class="btn btn-success btn-sm mt-2" id="copyBtn">Copy Results</button>
                        <span class="text-success small d-none" id="copyMsg">Copied!</span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <h2 class="h5">Dice Roller</h2>
                            <div class="display-3 my-2" id="diceDisplay">&#9860; &#9861;</div>
                            <div class="fw-semibold mb-3" id="diceTotal">Total: -</div>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" id="roll1Btn">Roll 1 Dice</button>
                                <button type="button" class="btn btn-outline-primary" id="roll2Btn">Roll 2 Dice</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <h2 class="h5">Coin Flip</h2>
                            <div class="display-3 my-2" id="coinDisplay">&#129689;</div>
                            <div class="fw-semibold mb-3" id="coinResult">Heads or Tails?</div>
                            <button type="button" class="btn btn-outline-primary" id="flipBtn">Flip Coin</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the <strong>Minimum</strong> and <strong>Maximum</strong> values and how many numbers you want.</li>
                        <li>Turn on <strong>Unique</strong> if repeats are not allowed (great for lucky draws), and <strong>Sort</strong> if you want results in order.</li>
                        <li>Click <strong>Generate Numbers</strong> and copy the results with one click.</li>
                        <li>Bonus: use the Dice Roller or Coin Flip below for quick game decisions.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    // Unbiased random integer in [min, max] using crypto.getRandomValues with rejection sampling.
    function secureRandomInt(min, max) {
        var range = max - min + 1;
        if (range <= 0) return min;
        if (window.crypto && window.crypto.getRandomValues) {
            var maxUint = 4294967295;
            var limit = maxUint - (maxUint % range) - 1;
            // Simpler correct rejection bound:
            limit = Math.floor((maxUint + 1) / range) * range - 1;
            var arr = new Uint32Array(1);
            do { window.crypto.getRandomValues(arr); } while (arr[0] > limit);
            return min + (arr[0] % range);
        }
        // Fallback (still rejection-based to reduce bias)
        var r, maxVal = 1;
        while (maxVal < range) maxVal *= 256;
        var limitFb = Math.floor(maxVal / range) * range;
        do { r = Math.floor(Math.random() * maxVal); } while (r >= limitFb);
        return min + (r % range);
    }

    var errorBox = document.getElementById('errorBox');
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        document.getElementById('resultWrap').classList.add('d-none');
    }

    document.getElementById('generateBtn').addEventListener('click', function () {
        errorBox.classList.add('d-none');
        var min = parseInt(document.getElementById('minVal').value, 10);
        var max = parseInt(document.getElementById('maxVal').value, 10);
        var count = parseInt(document.getElementById('countVal').value, 10);
        var unique = document.getElementById('uniqueToggle').checked;
        var sorted = document.getElementById('sortedToggle').checked;

        if (isNaN(min) || isNaN(max) || isNaN(count)) { showError('Please enter valid numbers in all fields.'); return; }
        if (min > max) { showError('Minimum must be less than or equal to Maximum.'); return; }
        if (count < 1 || count > 1000) { showError('Count must be between 1 and 1000.'); return; }
        var rangeSize = max - min + 1;
        if (unique && count > rangeSize) { showError('Cannot generate ' + count + ' unique numbers from a range of only ' + rangeSize + ' values.'); return; }

        var results = [];
        if (unique) {
            var seen = {};
            var guard = 0;
            while (results.length < count && guard < count * 100 + 1000) {
                guard++;
                var n = secureRandomInt(min, max);
                if (!seen[n]) { seen[n] = true; results.push(n); }
            }
        } else {
            for (var i = 0; i < count; i++) results.push(secureRandomInt(min, max));
        }
        if (sorted) results.sort(function (a, b) { return a - b; });

        document.getElementById('resultBox').value = results.join(', ');
        document.getElementById('resultWrap').classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', async function () {
        var box = document.getElementById('resultBox');
        try { await navigator.clipboard.writeText(box.value); }
        catch (e) { box.select(); document.execCommand('copy'); }
        var msg = document.getElementById('copyMsg');
        msg.classList.remove('d-none');
        setTimeout(function () { msg.classList.add('d-none'); }, 2000);
    });

    // Dice roller — unicode faces: 1 ⚀ 2 ⚁ 3 ⚂ 4 ⚃ 5 ⚄ 6 ⚅ (code points 9856-9861)
    var diceFaces = ['&#9856;', '&#9857;', '&#9858;', '&#9859;', '&#9860;', '&#9861;'];
    function rollDice(count) {
        var html = '', total = 0;
        for (var i = 0; i < count; i++) {
            var v = secureRandomInt(1, 6);
            total += v;
            html += diceFaces[v - 1] + ' ';
        }
        document.getElementById('diceDisplay').innerHTML = html.trim();
        document.getElementById('diceTotal').textContent = 'Total: ' + total;
    }
    document.getElementById('roll1Btn').addEventListener('click', function () { rollDice(1); });
    document.getElementById('roll2Btn').addEventListener('click', function () { rollDice(2); });

    document.getElementById('flipBtn').addEventListener('click', function () {
        var isHeads = secureRandomInt(0, 1) === 0;
        document.getElementById('coinDisplay').innerHTML = isHeads ? '&#129689;' : '&#11036;';
        document.getElementById('coinResult').textContent = isHeads ? 'Heads!' : 'Tails!';
    });
})();
</script>
@endsection
