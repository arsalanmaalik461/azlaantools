@extends('layouts.app')
@section('title', 'Kids Fraction Visualizer - Azlaan Tools')
@section('meta_description', 'Understand fractions with pictures! Interactive fraction visualizer for kids with pizza and bar charts — free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Kids Fraction Visualizer 🍕</h1>
            <p class="lead text-muted">See fractions in pictures and understand them easily! Change the numbers below — the pizza and chocolate bar will build themselves.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-6 mb-3">
                            <label for="numInput" class="form-label fw-semibold">Top number (shaded slices)</label>
                            <input type="number" class="form-control" id="numInput" value="3" min="1" max="20">
                            <input type="range" class="form-range mt-2" id="numRange" min="1" max="20" value="3">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="denInput" class="form-label fw-semibold">Bottom number (total slices)</label>
                            <input type="number" class="form-control" id="denInput" value="4" min="2" max="20">
                            <input type="range" class="form-range mt-2" id="denRange" min="2" max="20" value="4">
                        </div>
                    </div>

                    <div class="alert alert-danger mt-1 d-none" id="errorBox" role="alert"></div>

                    <div class="text-center my-3">
                        <div class="display-4 fw-bold" id="fracLabel">3/4</div>
                        <div class="text-muted" id="fracWords">three quarters</div>
                    </div>

                    <div class="row text-center">
                        <div class="col-12 col-md-6 mb-3">
                            <h2 class="h6 fw-bold">Pizza 🍕</h2>
                            <canvas id="pizzaCanvas" width="300" height="300" class="img-fluid" style="max-width: 300px;"></canvas>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <h2 class="h6 fw-bold">Chocolate Bar 🍫</h2>
                            <canvas id="barCanvas" width="300" height="300" class="img-fluid" style="max-width: 300px;"></canvas>
                        </div>
                    </div>

                    <div id="results" class="mt-2">
                        <div class="row text-center g-2">
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 h-100">
                                    <div class="text-muted small">Simplest form</div>
                                    <div class="fw-bold" id="rSimple">—</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 h-100">
                                    <div class="text-muted small">Decimal</div>
                                    <div class="fw-bold" id="rDec">—</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 h-100">
                                    <div class="text-muted small">Percent</div>
                                    <div class="fw-bold" id="rPct">—</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-2 h-100">
                                    <div class="text-muted small">Type</div>
                                    <div class="fw-bold" id="rType">—</div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mt-3 mb-0" id="rExplain"></p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Change the top and bottom numbers (with the slider or the box).</li>
                <li>See how much of the pizza and chocolate bar is colored.</li>
                <li>Also read the simplest form, decimal and percent below.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var numInput = document.getElementById('numInput');
    var denInput = document.getElementById('denInput');
    var numRange = document.getElementById('numRange');
    var denRange = document.getElementById('denRange');
    var errorBox = document.getElementById('errorBox');
    var fracLabel = document.getElementById('fracLabel');
    var fracWords = document.getElementById('fracWords');
    var pizza = document.getElementById('pizzaCanvas');
    var barC = document.getElementById('barCanvas');
    var pctx = pizza.getContext('2d');
    var bctx = barC.getContext('2d');

    var FILL = '#f59e0b', FILL2 = '#fbbf24', EMPTY = '#f1f5f9', LINE = '#ffffff';

    var ONES_EN = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
        'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty'];
    var DEN_EN = ['', '', 'halves', 'thirds', 'quarters', 'fifths', 'sixths', 'sevenths', 'eighths', 'ninths', 'tenths'];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function gcd(a, b) { return b ? gcd(b, a % b) : a; }
    function words(n, d) {
        var ne = ONES_EN[n] || n;
        var de = DEN_EN[d] || (ONES_EN[d] + 'ths');
        return ne + ' ' + de;
    }

    function drawPizza(n, d) {
        var W = pizza.width, cx = W / 2, cy = W / 2, R = W / 2 - 8, i, a0, a1;
        pctx.clearRect(0, 0, W, W);
        for (i = 0; i < d; i++) {
            a0 = -Math.PI / 2 + (i / d) * Math.PI * 2;
            a1 = -Math.PI / 2 + ((i + 1) / d) * Math.PI * 2;
            pctx.beginPath();
            pctx.moveTo(cx, cy);
            pctx.arc(cx, cy, R, a0, a1);
            pctx.closePath();
            pctx.fillStyle = i < n ? (i % 2 ? FILL2 : FILL) : EMPTY;
            pctx.fill();
            pctx.strokeStyle = LINE;
            pctx.lineWidth = 3;
            pctx.stroke();
        }
        pctx.beginPath();
        pctx.arc(cx, cy, R, 0, Math.PI * 2);
        pctx.strokeStyle = '#e2a63d';
        pctx.lineWidth = 8;
        pctx.stroke();
    }

    function drawBar(n, d) {
        var W = barC.width, H = barC.height, i;
        var cols = Math.ceil(d / 2), rows = 2;
        var bw = (W - 20) / cols, bh = (H - 20) / rows;
        bctx.clearRect(0, 0, W, H);
        for (i = 0; i < d; i++) {
            var c = i % cols, r = Math.floor(i / cols);
            var x = 10 + c * bw, y = 10 + r * bh;
            bctx.fillStyle = i < n ? (i % 2 ? FILL2 : FILL) : EMPTY;
            bctx.fillRect(x + 2, y + 2, bw - 4, bh - 4);
            bctx.strokeStyle = '#94a3b8';
            bctx.lineWidth = 2;
            bctx.strokeRect(x + 2, y + 2, bw - 4, bh - 4);
        }
    }

    function render() {
        hideError();
        var n = parseInt(numInput.value, 10);
        var d = parseInt(denInput.value, 10);
        if (!(n >= 1 && n <= 20)) { showError('The top number must be from 1 to 20.'); return; }
        if (!(d >= 2 && d <= 20)) { showError('The bottom number must be from 2 to 20.'); return; }
        numRange.value = n;
        denRange.value = d;
        fracLabel.textContent = n + '/' + d;
        fracWords.textContent = words(n, d);
        drawPizza(Math.min(n, d), d);
        drawBar(Math.min(n, d), d);

        var g = gcd(n, d);
        var sn = n / g, sd = d / g;
        document.getElementById('rSimple').textContent = (g === 1 ? n + '/' + d + ' (already simple)' : sn + '/' + sd);
        document.getElementById('rDec').textContent = (n / d).toFixed(3).replace(/0+$/, '').replace(/\.$/, '.0');
        document.getElementById('rPct').textContent = (n / d * 100).toFixed(1).replace(/\.0$/, '') + '%';
        var type, expl;
        if (n < d) {
            type = 'Proper fraction';
            expl = 'The top number is smaller than the bottom number — that means LESS than the whole thing, like ' + n + ' slices out of ' + d + '.';
        } else if (n === d) {
            type = 'Whole';
            expl = 'Both numbers are the same — that means you ate the WHOLE pizza! ' + n + '/' + d + ' = 1.';
        } else {
            var whole = Math.floor(n / d), rem = n % d;
            type = 'Improper fraction';
            expl = 'The top number is bigger — that means MORE than one! This makes ' + whole + ' whole ones and ' + rem + '/' + d + '.';
        }
        document.getElementById('rType').textContent = type;
        document.getElementById('rExplain').textContent = expl;
    }

    numInput.addEventListener('input', render);
    denInput.addEventListener('input', render);
    numRange.addEventListener('input', function () { numInput.value = numRange.value; render(); });
    denRange.addEventListener('input', function () { denInput.value = denRange.value; render(); });
    render();
})();
</script>
@endsection
