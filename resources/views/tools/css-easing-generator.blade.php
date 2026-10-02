@extends('layouts.app')
@section('title', 'CSS Easing Generator - Azlaan Tools')
@section('meta_description', 'Design cubic-bezier easing curves visually and copy the finished CSS. Free online easing generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">CSS Easing Generator</h1>
            <p class="lead text-muted">Design a cubic-bezier curve visually, see the live animation preview, and copy the finished CSS.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <svg id="curveSvg" viewBox="0 0 340 340" style="max-width: 340px; width: 100%; height: auto; border: 1px solid #dee2e6; border-radius: 8px; background: #fff; touch-action: none; cursor: crosshair;" role="img" aria-label="Easing curve editor">
                            <line id="refLine" x1="40" y1="300" x2="300" y2="40" stroke="#adb5bd" stroke-dasharray="5,5" stroke-width="1"></line>
                            <line id="h1" x1="40" y1="300" x2="40" y2="300" stroke="#0d6efd" stroke-width="1.5"></line>
                            <line id="h2" x1="300" y1="40" x2="300" y2="40" stroke="#0d6efd" stroke-width="1.5"></line>
                            <polyline id="curve" fill="none" stroke="#6d28d9" stroke-width="3" points=""></polyline>
                            <circle id="cp1" cx="40" cy="300" r="10" fill="#0d6efd" stroke="#fff" stroke-width="2" style="cursor: grab;"></circle>
                            <circle id="cp2" cx="300" cy="40" r="10" fill="#0d6efd" stroke="#fff" stroke-width="2" style="cursor: grab;"></circle>
                            <circle cx="40" cy="300" r="5" fill="#212529"></circle>
                            <circle cx="300" cy="40" r="5" fill="#212529"></circle>
                        </svg>
                        <p class="text-muted small mt-1 mb-0">Drag the blue dots to change the curve</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label for="nx1" class="form-label fw-semibold">x1</label>
                            <input type="number" class="form-control" id="nx1" step="0.01" min="0" max="1" value="0.25">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="ny1" class="form-label fw-semibold">y1</label>
                            <input type="number" class="form-control" id="ny1" step="0.01" min="-0.5" max="1.5" value="0.10">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="nx2" class="form-label fw-semibold">x2</label>
                            <input type="number" class="form-control" id="nx2" step="0.01" min="0" max="1" value="0.25">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="ny2" class="form-label fw-semibold">y2</label>
                            <input type="number" class="form-control" id="ny2" step="0.01" min="-0.5" max="1.5" value="1.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="form-label fw-semibold d-block mb-2">Presets</span>
                        <div class="d-flex flex-wrap gap-2" id="presetRow">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-p="0.25,0.1,0.25,1">ease</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-p="0.42,0,1,1">ease-in</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-p="0,0,0.58,1">ease-out</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-p="0.42,0,0.58,1">ease-in-out</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-p="0,0,1,1">linear</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-p="0.34,1.56,0.64,1">spring</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-p="0.85,0,0.15,1">snap</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="form-label fw-semibold d-block mb-2">Live preview (the box moves with this curve)</span>
                        <div class="position-relative border rounded" style="height: 90px; background: #f8f9fa; overflow: hidden;" id="track">
                            <div id="dot" style="position: absolute; top: 25px; left: 10px; width: 40px; height: 40px; border-radius: 10px; background: #6d28d9;"></div>
                        </div>
                        <div class="d-flex gap-2 mt-2 align-items-center">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="playBtn">Play Again</button>
                            <label for="durRange" class="form-label small mb-0 ms-2">Duration: <span id="durLabel">1.2</span>s</label>
                            <input type="range" class="form-range flex-grow-1" id="durRange" min="0.3" max="3" step="0.1" value="1.2" style="max-width: 200px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cssOut" class="form-label fw-semibold">CSS output</label>
                        <textarea class="form-control font-monospace" id="cssOut" rows="4" readonly style="font-size: 0.85rem;"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="copyBtn">Copy CSS</button>
                    <div class="alert alert-success mt-3 d-none" id="okBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Drag the blue dots or enter numbers — the curve updates right away.</li>
                <li>Watch the animation in the live preview below.</li>
                <li>Press "Copy CSS" and paste into your stylesheet.</li>
            </ol>
            <h2>Notes</h2>
            <ul>
                <li>If you set y1 / y2 outside 0-1 you get a <strong>spring / overshoot</strong> effect (like the "spring" preset).</li>
                <li>x values always stay between 0 and 1 — this is a CSS requirement.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var PAD = 40, SIZE = 260; // plot area inside 340x340 svg
    var svg = document.getElementById('curveSvg');
    var curveEl = document.getElementById('curve');
    var h1 = document.getElementById('h1');
    var h2 = document.getElementById('h2');
    var cp1 = document.getElementById('cp1');
    var cp2 = document.getElementById('cp2');
    var nx1 = document.getElementById('nx1');
    var ny1 = document.getElementById('ny1');
    var nx2 = document.getElementById('nx2');
    var ny2 = document.getElementById('ny2');
    var cssOut = document.getElementById('cssOut');
    var copyBtn = document.getElementById('copyBtn');
    var okBox = document.getElementById('okBox');
    var presetRow = document.getElementById('presetRow');
    var dot = document.getElementById('dot');
    var track = document.getElementById('track');
    var playBtn = document.getElementById('playBtn');
    var durRange = document.getElementById('durRange');
    var durLabel = document.getElementById('durLabel');

    var P = { x1: 0.25, y1: 0.10, x2: 0.25, y2: 1.00 };

    function clamp(v, lo, hi) { return Math.min(hi, Math.max(lo, v)); }
    function toX(x) { return PAD + x * SIZE; }
    function toY(y) { return PAD + (1 - y) * SIZE; }
    function fromX(px) { return clamp((px - PAD) / SIZE, 0, 1); }
    function fromY(py) { return clamp(1 - (py - PAD) / SIZE, -0.5, 1.5); }

    function bezY(t) {
        var u = 1 - t;
        return 3 * u * u * t * P.y1 + 3 * u * t * t * P.y2 + t * t * t;
    }

    function draw() {
        var x1 = toX(P.x1), y1 = toY(P.y1), x2 = toX(P.x2), y2 = toY(P.y2);
        cp1.setAttribute('cx', x1); cp1.setAttribute('cy', y1);
        cp2.setAttribute('cx', x2); cp2.setAttribute('cy', y2);
        h1.setAttribute('x2', x1); h1.setAttribute('y2', y1);
        h2.setAttribute('x2', x2); h2.setAttribute('y2', y2);
        var pts = [];
        for (var i = 0; i <= 60; i++) {
            var t = i / 60;
            var u = 1 - t;
            var bx = 3 * u * u * t * P.x1 + 3 * u * t * t * P.x2 + t * t * t;
            var by = 3 * u * u * t * P.y1 + 3 * u * t * t * P.y2 + t * t * t;
            pts.push(toX(bx).toFixed(1) + ',' + toY(by).toFixed(1));
        }
        curveEl.setAttribute('points', pts.join(' '));
        nx1.value = P.x1.toFixed(2); ny1.value = P.y1.toFixed(2);
        nx2.value = P.x2.toFixed(2); ny2.value = P.y2.toFixed(2);
        cssOut.value =
            'transition-timing-function: cubic-bezier(' + P.x1.toFixed(2) + ', ' + P.y1.toFixed(2) + ', ' + P.x2.toFixed(2) + ', ' + P.y2.toFixed(2) + ');\n' +
            'animation-timing-function: cubic-bezier(' + P.x1.toFixed(2) + ', ' + P.y1.toFixed(2) + ', ' + P.x2.toFixed(2) + ', ' + P.y2.toFixed(2) + ');';
    }

    function svgPoint(evt) {
        var rect = svg.getBoundingClientRect();
        var clientX = evt.touches && evt.touches.length ? evt.touches[0].clientX : evt.clientX;
        var clientY = evt.touches && evt.touches.length ? evt.touches[0].clientY : evt.clientY;
        return {
            x: (clientX - rect.left) * 340 / rect.width,
            y: (clientY - rect.top) * 340 / rect.height
        };
    }

    function makeDraggable(circle, which) {
        var dragging = false;
        function move(evt) {
            if (!dragging) return;
            if (evt.cancelable) evt.preventDefault();
            var pt = svgPoint(evt);
            var x = fromX(pt.x), y = fromY(pt.y);
            if (which === 1) { P.x1 = x; P.y1 = y; } else { P.x2 = x; P.y2 = y; }
            draw();
        }
        function start(evt) {
            dragging = true;
            circle.style.cursor = 'grabbing';
            if (evt.cancelable) evt.preventDefault();
        }
        function stop() { dragging = false; circle.style.cursor = 'grab'; }
        circle.addEventListener('mousedown', start);
        circle.addEventListener('touchstart', start, { passive: false });
        window.addEventListener('mousemove', move);
        window.addEventListener('touchmove', move, { passive: false });
        window.addEventListener('mouseup', stop);
        window.addEventListener('touchend', stop);
    }
    makeDraggable(cp1, 1);
    makeDraggable(cp2, 2);

    [[nx1, 'x1'], [ny1, 'y1'], [nx2, 'x2'], [ny2, 'y2']].forEach(function (pair) {
        pair[0].addEventListener('input', function () {
            var v = parseFloat(pair[0].value);
            if (isNaN(v)) return;
            var key = pair[1];
            P[key] = (key.charAt(0) === 'x') ? clamp(v, 0, 1) : clamp(v, -0.5, 1.5);
            draw();
        });
    });

    presetRow.addEventListener('click', function (e) {
        var btn = e.target.closest('button[data-p]');
        if (!btn) return;
        var v = btn.getAttribute('data-p').split(',').map(parseFloat);
        P.x1 = v[0]; P.y1 = v[1]; P.x2 = v[2]; P.y2 = v[3];
        draw();
        play();
    });

    var rafId = null;
    function play() {
        if (rafId) cancelAnimationFrame(rafId);
        var dur = parseFloat(durRange.value) * 1000;
        var maxX = track.clientWidth - 50;
        var start = null;
        function frame(ts) {
            if (!start) start = ts;
            var t = Math.min(1, (ts - start) / dur);
            var e = bezY(t);
            dot.style.left = (10 + e * maxX) + 'px';
            if (t < 1) { rafId = requestAnimationFrame(frame); }
            else {
                setTimeout(function () { play(); }, 400);
            }
        }
        rafId = requestAnimationFrame(frame);
    }
    playBtn.addEventListener('click', play);
    durRange.addEventListener('input', function () { durLabel.textContent = parseFloat(durRange.value).toFixed(1); });

    copyBtn.addEventListener('click', function () {
        okBox.classList.add('d-none');
        function ok() {
            okBox.textContent = 'CSS copied! Paste it into your stylesheet.';
            okBox.classList.remove('d-none');
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(cssOut.value).then(ok, ok);
        } else {
            cssOut.select();
            try { document.execCommand('copy'); } catch (e) {}
            ok();
        }
    });

    draw();
})();
</script>
@endsection
