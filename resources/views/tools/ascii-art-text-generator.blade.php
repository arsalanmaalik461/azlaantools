@extends('layouts.app')

@section('title', 'ASCII Art Text Generator - Azlaan Tools')
@section('meta_description', 'Turn words into cool ASCII art text styles. Free online text banner maker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <h1 class="mb-3">ASCII Art Text Generator</h1>
            <p class="lead text-muted">Write your name or a word and turn it into stylish ASCII art — for banners, posts and WhatsApp status.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="artText" class="form-label fw-semibold">Enter text</label>
                        <input type="text" class="form-control" id="artText" placeholder="e.g. AZLAAN" maxlength="20" value="AZLAAN">
                    </div>
                    <div class="mb-3">
                        <label for="artStyle" class="form-label fw-semibold">Choose style</label>
                        <select class="form-select" id="artStyle">
                            <option value="block">Block Letters</option>
                            <option value="banner">Banner Box</option>
                            <option value="slant">Slanted / Italic</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="artChar" class="form-label fw-semibold">Fill character (for Block/Slant)</label>
                        <input type="text" class="form-control" id="artChar" placeholder="#" maxlength="1" value="#">
                        <div class="form-text">Leave empty to use the default #.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate ASCII Art</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label class="form-label fw-semibold">Result</label>
                        <pre id="artOut" class="border rounded p-3 bg-light" style="white-space:pre; overflow-x:auto; font-size:13px; line-height:1.15;"></pre>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success flex-grow-1" id="copyBtn">Copy Text</button>
                            <button type="button" class="btn btn-outline-primary flex-grow-1" id="dlBtn">Download .txt</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter text (maximum 20 characters).</li>
                <li>Choose a style, and change the fill character if you want.</li>
                <li>Press "Generate", then copy or download.</li>
            </ol>
            <p class="text-muted small">A-Z and 0-9 are supported; small letters will be changed to capital letters.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var artText = document.getElementById('artText');
    var artStyle = document.getElementById('artStyle');
    var artChar = document.getElementById('artChar');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var artOut = document.getElementById('artOut');
    var lastArt = '';

    // 5x5 block font: '.' empty, '#' filled
    var FONT = {
        'A': ['.###.', '#...#', '#####', '#...#', '#...#'],
        'B': ['####.', '#...#', '####.', '#...#', '####.'],
        'C': ['.####', '#....', '#....', '#....', '.####'],
        'D': ['####.', '#...#', '#...#', '#...#', '####.'],
        'E': ['#####', '#....', '####.', '#....', '#####'],
        'F': ['#####', '#....', '####.', '#....', '#....'],
        'G': ['.####', '#....', '#.###', '#...#', '.###.'],
        'H': ['#...#', '#...#', '#####', '#...#', '#...#'],
        'I': ['#####', '..#..', '..#..', '..#..', '#####'],
        'J': ['..###', '...#.', '...#.', '#..#.', '.##..'],
        'K': ['#...#', '#..#.', '###..', '#..#.', '#...#'],
        'L': ['#....', '#....', '#....', '#....', '#####'],
        'M': ['#...#', '##.##', '#.#.#', '#...#', '#...#'],
        'N': ['#...#', '##..#', '#.#.#', '#..##', '#...#'],
        'O': ['.###.', '#...#', '#...#', '#...#', '.###.'],
        'P': ['####.', '#...#', '####.', '#....', '#....'],
        'Q': ['.###.', '#...#', '#...#', '#.###', '.##.#'],
        'R': ['####.', '#...#', '####.', '#..#.', '#...#'],
        'S': ['.####', '#....', '.###.', '....#', '####.'],
        'T': ['#####', '..#..', '..#..', '..#..', '..#..'],
        'U': ['#...#', '#...#', '#...#', '#...#', '.###.'],
        'V': ['#...#', '#...#', '#...#', '.#.#.', '..#..'],
        'W': ['#...#', '#...#', '#.#.#', '##.##', '#...#'],
        'X': ['#...#', '.#.#.', '..#..', '.#.#.', '#...#'],
        'Y': ['#...#', '.#.#.', '..#..', '..#..', '..#..'],
        'Z': ['#####', '...#.', '..#..', '.#...', '#####'],
        '0': ['.###.', '#..##', '#.#.#', '##..#', '.###.'],
        '1': ['..#..', '.##..', '..#..', '..#..', '.###.'],
        '2': ['.###.', '....#', '..##.', '.#...', '#####'],
        '3': ['####.', '....#', '.###.', '....#', '####.'],
        '4': ['...#.', '..##.', '.#.#.', '#####', '...#.'],
        '5': ['#####', '#....', '####.', '....#', '####.'],
        '6': ['.###.', '#....', '####.', '#...#', '.###.'],
        '7': ['#####', '....#', '...#.', '..#..', '..#..'],
        '8': ['.###.', '#...#', '.###.', '#...#', '.###.'],
        '9': ['.###.', '#...#', '.####', '....#', '.###.'],
        ' ': ['.....', '.....', '.....', '.....', '.....'],
        '.': ['.....', '.....', '.....', '.....', '..#..'],
        '!': ['..#..', '..#..', '..#..', '.....', '..#..'],
        '?': ['.###.', '....#', '...#.', '.....', '..#..'],
        '-': ['.....', '.....', '#####', '.....', '.....'],
        '_': ['.....', '.....', '.....', '.....', '#####'],
        "'": ['..#..', '..#..', '.....', '.....', '.....'],
        ':': ['.....', '..#..', '.....', '..#..', '.....']
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

    function buildBlock(text, fill, slant) {
        var rows = ['', '', '', '', ''];
        for (var ci = 0; ci < text.length; ci++) {
            var ch = text[ci];
            var glyph = FONT[ch] || FONT[' '];
            for (var r = 0; r < 5; r++) {
                var line = glyph[r].split('#').join(fill).split('.').join(' ');
                if (slant) {
                    line = new Array(r + 1).join(' ') + line;
                }
                rows[r] += line + '  ';
            }
        }
        if (slant) {
            var maxLen = 0, k;
            for (k = 0; k < 5; k++) { if (rows[k].length > maxLen) maxLen = rows[k].length; }
            for (k = 0; k < 5; k++) {
                rows[k] = new Array(maxLen - rows[k].length - k + 1).join(' ') + rows[k];
            }
        }
        return rows.join('\n');
    }

    function buildBanner(text) {
        var inner = text.split('').join(' ');
        var width = inner.length + 4;
        var bar = '';
        for (var i = 0; i < width; i++) bar += '#';
        return bar + '\n# ' + inner + ' #\n' + bar;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var t = artText.value.trim().toUpperCase();
        if (!t) { showError('Please enter some text.'); return; }
        var fill = artChar.value ? artChar.value.charAt(0) : '#';
        var style = artStyle.value;
        var out;
        if (style === 'banner') {
            out = buildBanner(t);
        } else if (style === 'slant') {
            out = buildBlock(t, fill, true);
        } else {
            out = buildBlock(t, fill, false);
        }
        lastArt = out;
        artOut.textContent = out;
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        if (!lastArt) return;
        function done() { copyBtn.textContent = 'Copied!'; setTimeout(function () { copyBtn.textContent = 'Copy Text'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastArt).then(done, function () { showError('Could not copy.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = lastArt;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); } catch (e) { showError('Could not copy.'); }
            document.body.removeChild(ta);
        }
    });

    dlBtn.addEventListener('click', function () {
        if (!lastArt) return;
        var blob = new Blob([lastArt], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'ascii-art.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
