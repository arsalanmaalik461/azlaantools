@extends('layouts.app')

@section('title', 'Kids Coloring Pages Generator - Azlaan Tools')
@section('meta_description', 'Make free printable coloring pages for kids — animal, flower, rocket and mandala designs.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Kids Coloring Pages Generator</h1>
            <p class="lead text-muted">Make <strong>coloring pages</strong> for kids — select a design, print or download it, then color it. Completely free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="designSel" class="form-label fw-semibold">Select a design</label>
                        <select class="form-select" id="designSel">
                            <option value="cat">Cat</option>
                            <option value="fish">Fish</option>
                            <option value="flower">Flower</option>
                            <option value="rocket">Rocket</option>
                            <option value="butterfly">Butterfly</option>
                            <option value="mandala">Mandala Pattern</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary flex-fill" id="genBtn">Make New Design</button>
                        <button type="button" class="btn btn-outline-secondary" id="dlSvgBtn">SVG</button>
                        <button type="button" class="btn btn-outline-secondary" id="dlPngBtn">PNG</button>
                        <button type="button" class="btn btn-outline-success" id="printBtn">Print</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <div id="artArea" class="border rounded bg-white p-2 text-center"></div>
                        <div class="form-text mt-2">Tip: pressing the Print button prints only the coloring page. Every mandala design is unique.</div>
                    </div>
                    <div id="printArea" class="d-none"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a design (cat, fish, flower, rocket, butterfly or mandala).</li>
                <li>Press <strong>Make New Design</strong> — the mandala makes a new pattern every time.</li>
                <li>Print it with <strong>Print</strong> or download the <strong>PNG/SVG</strong> file.</li>
            </ol>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; display: block !important; }
    #printArea svg { width: 100%; height: auto; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var designSel = document.getElementById('designSel');
    var genBtn = document.getElementById('genBtn');
    var dlSvgBtn = document.getElementById('dlSvgBtn');
    var dlPngBtn = document.getElementById('dlPngBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var artArea = document.getElementById('artArea');
    var printArea = document.getElementById('printArea');

    var ST = 'stroke="#111111" stroke-width="5" fill="#ffffff" stroke-linecap="round" stroke-linejoin="round"';
    var STF = 'stroke="#111111" stroke-width="5" fill="none" stroke-linecap="round" stroke-linejoin="round"';
    var currentSvg = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function wrap(inner) {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">' + inner + '</svg>';
    }

    function catArt() {
        var p = '';
        p += '<polygon points="200,185 185,75 290,140" ' + ST + '/>';
        p += '<polygon points="400,185 415,75 310,140" ' + ST + '/>';
        p += '<circle cx="300" cy="265" r="125" ' + ST + '/>';
        p += '<circle cx="255" cy="245" r="17" ' + ST + '/>';
        p += '<circle cx="345" cy="245" r="17" ' + ST + '/>';
        p += '<circle cx="255" cy="245" r="6" fill="#111111"/>';
        p += '<circle cx="345" cy="245" r="6" fill="#111111"/>';
        p += '<polygon points="285,300 315,300 300,320" ' + ST + '/>';
        p += '<path d="M300,320 Q300,338 278,344 M300,320 Q300,338 322,344" ' + STF + '/>';
        p += '<line x1="180" y1="290" x2="245" y2="300" ' + STF + '/>';
        p += '<line x1="180" y1="320" x2="245" y2="312" ' + STF + '/>';
        p += '<line x1="420" y1="290" x2="355" y2="300" ' + STF + '/>';
        p += '<line x1="420" y1="320" x2="355" y2="312" ' + STF + '/>';
        p += '<ellipse cx="300" cy="490" rx="95" ry="70" ' + ST + '/>';
        p += '<path d="M390,505 Q470,490 460,400" ' + STF + '/>';
        return p;
    }

    function fishArt() {
        var p = '';
        p += '<ellipse cx="290" cy="300" rx="150" ry="100" ' + ST + '/>';
        p += '<polygon points="440,300 555,215 555,385" ' + ST + '/>';
        p += '<circle cx="205" cy="270" r="20" ' + ST + '/>';
        p += '<circle cx="205" cy="270" r="8" fill="#111111"/>';
        p += '<path d="M160,330 Q195,348 230,338" ' + STF + '/>';
        p += '<path d="M300,215 Q330,250 300,285 M340,230 Q365,265 340,300 M260,230 Q285,265 260,300" ' + STF + '/>';
        p += '<polygon points="290,400 330,450 250,450" ' + ST + '/>';
        p += '<circle cx="120" cy="180" r="12" ' + STF + '/>';
        p += '<circle cx="90" cy="130" r="9" ' + STF + '/>';
        p += '<circle cx="150" cy="110" r="7" ' + STF + '/>';
        return p;
    }

    function flowerArt() {
        var p = '';
        for (var i = 0; i < 8; i++) {
            p += '<ellipse cx="300" cy="165" rx="48" ry="85" transform="rotate(' + (i * 45) + ' 300 300)" ' + ST + '/>';
        }
        p += '<circle cx="300" cy="300" r="58" ' + ST + '/>';
        p += '<circle cx="280" cy="285" r="8" ' + STF + '/>';
        p += '<circle cx="320" cy="285" r="8" ' + STF + '/>';
        p += '<circle cx="300" cy="315" r="8" ' + STF + '/>';
        p += '<line x1="300" y1="358" x2="300" y2="565" ' + STF + '/>';
        p += '<path d="M300,450 Q220,440 185,375 Q270,385 300,450" ' + ST + '/>';
        p += '<path d="M300,490 Q380,480 415,415 Q330,425 300,490" ' + ST + '/>';
        return p;
    }

    function rocketArt() {
        var p = '';
        p += '<path d="M300,90 C365,155 375,260 352,365 L248,365 C225,260 235,155 300,90 Z" ' + ST + '/>';
        p += '<circle cx="300" cy="235" r="42" ' + ST + '/>';
        p += '<circle cx="300" cy="235" r="18" ' + STF + '/>';
        p += '<polygon points="248,365 248,275 185,385" ' + ST + '/>';
        p += '<polygon points="352,365 352,275 415,385" ' + ST + '/>';
        p += '<path d="M272,365 Q300,430 300,475 Q300,430 328,365" ' + STF + '/>';
        p += '<path d="M285,365 Q300,410 300,435 Q300,410 315,365" ' + STF + '/>';
        p += '<circle cx="130" cy="140" r="10" ' + STF + '/>';
        p += '<circle cx="480" cy="120" r="12" ' + STF + '/>';
        p += '<circle cx="500" cy="480" r="10" ' + STF + '/>';
        p += '<circle cx="110" cy="470" r="12" ' + STF + '/>';
        p += '<path d="M470,250 l0,24 M458,262 l24,0" ' + STF + '/>';
        return p;
    }

    function butterflyArt() {
        var p = '';
        p += '<ellipse cx="185" cy="235" rx="115" ry="130" ' + ST + '/>';
        p += '<ellipse cx="415" cy="235" rx="115" ry="130" ' + ST + '/>';
        p += '<ellipse cx="205" cy="430" rx="80" ry="90" ' + ST + '/>';
        p += '<ellipse cx="395" cy="430" rx="80" ry="90" ' + ST + '/>';
        p += '<circle cx="185" cy="235" r="35" ' + STF + '/>';
        p += '<circle cx="415" cy="235" r="35" ' + STF + '/>';
        p += '<circle cx="205" cy="430" r="22" ' + STF + '/>';
        p += '<circle cx="395" cy="430" r="22" ' + STF + '/>';
        p += '<ellipse cx="300" cy="330" rx="28" ry="115" ' + ST + '/>';
        p += '<circle cx="300" cy="200" r="30" ' + ST + '/>';
        p += '<circle cx="290" cy="195" r="6" fill="#111111"/>';
        p += '<circle cx="310" cy="195" r="6" fill="#111111"/>';
        p += '<path d="M285,175 Q260,130 230,120 M315,175 Q340,130 370,120" ' + STF + '/>';
        p += '<circle cx="228" cy="118" r="8" ' + STF + '/>';
        p += '<circle cx="372" cy="118" r="8" ' + STF + '/>';
        return p;
    }

    function mandalaArt(seed) {
        var s = seed;
        function rnd() { s = (s * 9301 + 49297) % 233280; return s / 233280; }
        var p = '';
        p += '<circle cx="300" cy="300" r="280" ' + ST + '/>';
        var rings = 3 + Math.floor(rnd() * 2);
        for (var r = 0; r < rings; r++) {
            var radius = 55 + r * 62 + Math.floor(rnd() * 25);
            p += '<circle cx="300" cy="300" r="' + radius + '" ' + STF + '/>';
            var petals = 8 + Math.floor(rnd() * 9);
            var pw = 16 + Math.floor(rnd() * 14);
            var ph = 26 + Math.floor(rnd() * 18);
            for (var q = 0; q < petals; q++) {
                var a = Math.round((360 / petals) * q);
                p += '<ellipse cx="300" cy="' + (300 - radius) + '" rx="' + pw + '" ry="' + ph + '" transform="rotate(' + a + ' 300 300)" ' + STF + '/>';
            }
            var dots = 6 + Math.floor(rnd() * 6);
            for (var d = 0; d < dots; d++) {
                var da = Math.round((360 / dots) * d);
                p += '<circle cx="300" cy="' + (300 - radius - 30) + '" r="6" transform="rotate(' + da + ' 300 300)" ' + STF + '/>';
            }
        }
        p += '<circle cx="300" cy="300" r="28" ' + ST + '/>';
        p += '<circle cx="300" cy="300" r="10" ' + STF + '/>';
        return p;
    }

    function generate() {
        hideError();
        var kind = designSel.value;
        var inner;
        if (kind === 'cat') { inner = catArt(); }
        else if (kind === 'fish') { inner = fishArt(); }
        else if (kind === 'flower') { inner = flowerArt(); }
        else if (kind === 'rocket') { inner = rocketArt(); }
        else if (kind === 'butterfly') { inner = butterflyArt(); }
        else { inner = mandalaArt(1 + Math.floor(Math.random() * 99999)); }
        currentSvg = wrap(inner);
        artArea.innerHTML = currentSvg;
        printArea.innerHTML = currentSvg;
    }

    genBtn.addEventListener('click', generate);
    designSel.addEventListener('change', generate);

    function download(url, name) {
        var a = document.createElement('a');
        a.href = url;
        a.download = name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    dlSvgBtn.addEventListener('click', function () {
        if (!currentSvg) { showError('Make a design first.'); return; }
        hideError();
        var blob = new Blob([currentSvg], { type: 'image/svg+xml' });
        download(URL.createObjectURL(blob), 'coloring-page.svg');
    });

    dlPngBtn.addEventListener('click', function () {
        if (!currentSvg) { showError('Make a design first.'); return; }
        hideError();
        var blob = new Blob([currentSvg], { type: 'image/svg+xml' });
        var url = URL.createObjectURL(blob);
        var img = new Image();
        img.onload = function () {
            var canvas = document.createElement('canvas');
            canvas.width = 1200;
            canvas.height = 1200;
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 1200, 1200);
            ctx.drawImage(img, 0, 0, 1200, 1200);
            URL.revokeObjectURL(url);
            canvas.toBlob(function (b) {
                download(URL.createObjectURL(b), 'coloring-page.png');
            }, 'image/png');
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            showError('Could not make the PNG. Try downloading the SVG.');
        };
        img.src = url;
    });

    printBtn.addEventListener('click', function () {
        if (!currentSvg) { showError('Make a design first.'); return; }
        hideError();
        window.print();
    });

    generate();
})();
</script>
@endsection
