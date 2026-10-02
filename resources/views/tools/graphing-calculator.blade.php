@extends('layouts.app')

@section('title', 'Graphing Calculator - Azlaan Tools')
@section('meta_description', 'Plot math functions on a graph free online — multiple functions, with zoom and pan.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Graphing Calculator</h1>
            <p class="lead text-muted">Write your math function and see its graph at once — completely free. Example: <code>x^2</code>, <code>sin(x)</code>, <code>2*x+3</code>.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="funcInput" class="form-label fw-semibold">Function f(x) =</label>
                        <input type="text" class="form-control" id="funcInput" placeholder="example: x^2 - 4" value="x^2 - 4">
                        <div class="form-text">Supported: + - * / ^ (power), sin cos tan, sqrt, abs, log (ln), exp, pi, e. Use a semicolon for 2 functions: <code>x^2; 2*x+1</code></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="xMin" class="form-label fw-semibold">X range (min)</label>
                            <input type="number" class="form-control" id="xMin" value="-10" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="xMax" class="form-label fw-semibold">X range (max)</label>
                            <input type="number" class="form-control" id="xMax" value="10" step="any">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Draw Graph</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <canvas id="graphCanvas" class="w-100 rounded border" style="cursor:grab;"></canvas>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" id="zoomInBtn">Zoom In (+)</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" id="zoomOutBtn">Zoom Out (-)</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" id="resetViewBtn">Reset View</button>
                        </div>
                        <p class="small text-muted mt-2 mb-0" id="zeroInfo"></p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write a function, for example <code>sin(x)</code> or <code>x^3 - 2*x</code>.</li>
                <li>Set the X range and press "Draw Graph".</li>
                <li>Drag with the mouse to pan, and use the buttons to zoom.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var funcInput = document.getElementById('funcInput');
    var xMinEl = document.getElementById('xMin');
    var xMaxEl = document.getElementById('xMax');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var canvas = document.getElementById('graphCanvas');
    var ctx = canvas.getContext('2d');
    var zoomInBtn = document.getElementById('zoomInBtn');
    var zoomOutBtn = document.getElementById('zoomOutBtn');
    var resetViewBtn = document.getElementById('resetViewBtn');
    var zeroInfo = document.getElementById('zeroInfo');

    var colors = ['#0d6efd', '#dc3545', '#198754', '#fd7e14', '#6f42c1'];
    var view = { xMin: -10, xMax: 10 };
    var funcs = [];
    var dragging = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    // Compile a user expression into a safe function of x
    function compileExpr(expr) {
        var t = expr.toLowerCase().replace(/\s+/g, '');
        if (!t) throw new Error('empty');
        t = t.replace(/\^/g, '**');
        // Only allow known tokens
        var allowed = /^(?:[0-9x+\-*/().,**]|sin|cos|tan|sqrt|abs|log|exp|pi|e)+$/;
        if (!allowed.test(t)) throw new Error('Invalid word — use only x, numbers and sin/cos/tan/sqrt/abs/log/exp/pi/e.');
        // Replace function names with Math.*
        t = t.replace(/\b(sin|cos|tan|sqrt|abs|exp)\b/g, 'Math.$1')
             .replace(/\blog\b/g, 'Math.log')
             .replace(/\bpi\b/g, 'Math.PI')
             .replace(/\be\b/g, 'Math.E');
        // Reject anything still containing letters other than Math
        var check = t.replace(/Math\./g, '');
        if (/[a-z]/.test(check)) throw new Error('Invalid word — use only the allowed functions.');
        /* jshint evil:true */
        return new Function('x', 'return (' + t + ');');
    }

    function draw() {
        var W = canvas.clientWidth || 600;
        var H = 380;
        var dpr = window.devicePixelRatio || 1;
        canvas.width = W * dpr;
        canvas.height = H * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, W, H);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);

        var xMin = view.xMin, xMax = view.xMax;
        var xRange = xMax - xMin;
        var yRange = xRange * H / W; // keep aspect
        var yMid = 0, yMin = yMid - yRange / 2, yMax = yMid + yRange / 2;

        function sx(x) { return (x - xMin) / xRange * W; }
        function sy(y) { return H - (y - yMin) / yRange * H; }

        // Grid
        ctx.strokeStyle = '#e9ecef';
        ctx.lineWidth = 1;
        var step = niceStep(xRange / 10);
        ctx.beginPath();
        for (var gx = Math.ceil(xMin / step) * step; gx <= xMax; gx += step) {
            ctx.moveTo(sx(gx), 0); ctx.lineTo(sx(gx), H);
        }
        for (var gy = Math.ceil(yMin / step) * step; gy <= yMax; gy += step) {
            ctx.moveTo(0, sy(gy)); ctx.lineTo(W, sy(gy));
        }
        ctx.stroke();

        // Axes
        ctx.strokeStyle = '#6c757d';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        if (yMin <= 0 && yMax >= 0) { ctx.moveTo(0, sy(0)); ctx.lineTo(W, sy(0)); }
        if (xMin <= 0 && xMax >= 0) { ctx.moveTo(sx(0), 0); ctx.lineTo(sx(0), H); }
        ctx.stroke();

        // Axis labels
        ctx.fillStyle = '#6c757d';
        ctx.font = '11px system-ui, sans-serif';
        for (var lx = Math.ceil(xMin / step) * step; lx <= xMax; lx += step) {
            if (Math.abs(lx) < step * 0.01) continue;
            ctx.fillText(round2(lx), sx(lx) + 3, H - 5);
        }
        for (var ly = Math.ceil(yMin / step) * step; ly <= yMax; ly += step) {
            if (Math.abs(ly) < step * 0.01) continue;
            ctx.fillText(round2(ly), 5, sy(ly) - 3);
        }

        // Plot functions
        var zeroList = [];
        funcs.forEach(function (fn, i) {
            ctx.strokeStyle = colors[i % colors.length];
            ctx.lineWidth = 2.5;
            ctx.beginPath();
            var prevX = null, prevY = null;
            for (var px = 0; px <= W; px += 1) {
                var x = xMin + px / W * xRange;
                var y;
                try { y = fn(x); } catch (e) { prevX = null; continue; }
                if (!isFinite(y)) { prevX = null; continue; }
                if (prevY !== null && Math.abs(y - prevY) > yRange * 2) { prevX = null; prevY = null; } // asymptote jump
                if (prevX === null) ctx.moveTo(px, sy(y)); else ctx.lineTo(px, sy(y));
                // zero crossing detect
                if (prevY !== null && prevY * y < 0 && i === 0) {
                    zeroList.push((x + (x - xRange / W)) / 2);
                }
                prevX = px; prevY = y;
            }
            ctx.stroke();
        });
        if (zeroList.length) {
            zeroInfo.textContent = 'X-axis cross (approx roots): ' + zeroList.slice(0, 6).map(round2).join(', ');
        } else {
            zeroInfo.textContent = 'No x-axis crossing found in this range.';
        }
    }

    function niceStep(raw) {
        var mag = Math.pow(10, Math.floor(Math.log10(raw)));
        var n = raw / mag;
        if (n < 1.5) return mag;
        if (n < 3.5) return 2 * mag;
        if (n < 7.5) return 5 * mag;
        return 10 * mag;
    }
    function round2(v) {
        return Math.abs(v) < 1e-9 ? '0' : String(Math.round(v * 100) / 100);
    }

    function plot() {
        hideError();
        var raw = funcInput.value.trim();
        var xmin = parseFloat(xMinEl.value), xmax = parseFloat(xMaxEl.value);
        if (!raw) { showError('Please enter a function first.'); return; }
        if (!isFinite(xmin) || !isFinite(xmax) || xmin >= xmax) { showError('Invalid X range — min must be smaller than max.'); return; }
        try {
            funcs = raw.split(';').map(function (s) { return compileExpr(s.trim()); });
        } catch (e) {
            showError(e.message === 'empty' ? 'The function is empty.' : e.message);
            return;
        }
        view.xMin = xmin; view.xMax = xmax;
        results.classList.remove('d-none');
        draw();
    }

    goBtn.addEventListener('click', plot);
    zoomInBtn.addEventListener('click', function () { zoom(0.5); });
    zoomOutBtn.addEventListener('click', function () { zoom(2); });
    resetViewBtn.addEventListener('click', function () {
        view.xMin = parseFloat(xMinEl.value) || -10;
        view.xMax = parseFloat(xMaxEl.value) || 10;
        draw();
    });
    function zoom(f) {
        var mid = (view.xMin + view.xMax) / 2;
        var half = (view.xMax - view.xMin) / 2 * f;
        view.xMin = mid - half; view.xMax = mid + half;
        draw();
    }

    canvas.addEventListener('pointerdown', function (e) {
        dragging = e.clientX;
        canvas.setPointerCapture(e.pointerId);
        canvas.style.cursor = 'grabbing';
    });
    canvas.addEventListener('pointermove', function (e) {
        if (dragging === null) return;
        var W = canvas.clientWidth;
        var dx = (e.clientX - dragging) / W * (view.xMax - view.xMin);
        view.xMin -= dx; view.xMax -= dx;
        dragging = e.clientX;
        draw();
    });
    canvas.addEventListener('pointerup', function () { dragging = null; canvas.style.cursor = 'grab'; });
    window.addEventListener('resize', function () { if (funcs.length) draw(); });

    funcInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') plot(); });
})();
</script>
@endsection
