@extends('layouts.app')

@section('title', 'Quadratic Equation Solver — Azlaan Tools')
@section('meta_description', 'Free online quadratic equation solver for ax²+bx+c=0. Find roots (real or complex), discriminant, vertex, axis of symmetry and y-intercept, with a graph of the parabola. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Quadratic Equation Solver</h1>
            <p class="lead text-muted">Enter the coefficients of the equation <strong>ax&sup2; + bx + c = 0</strong> — you will get the roots, discriminant, vertex and the parabola's graph instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-4">
                            <label for="qa" class="form-label">a (coefficient of x&sup2;)</label>
                            <input type="number" class="form-control form-control-lg" id="qa" placeholder="e.g. 1" step="any" value="1">
                        </div>
                        <div class="col-4">
                            <label for="qb" class="form-label">b (coefficient of x)</label>
                            <input type="number" class="form-control form-control-lg" id="qb" placeholder="e.g. -5" step="any" value="-5">
                        </div>
                        <div class="col-4">
                            <label for="qc" class="form-label">c (constant)</label>
                            <input type="number" class="form-control form-control-lg" id="qc" placeholder="e.g. 6" step="any" value="6">
                        </div>
                    </div>
                    <p class="text-center fs-5 mt-3 mb-0">Equation: <strong id="qEq">x&sup2; − 5x + 6 = 0</strong></p>

                    <div class="result-box mt-3 text-center">
                        <div class="text-muted small">Roots (values of x)</div>
                        <div class="fs-4 fw-bold" id="qRoots">—</div>
                        <div class="small" id="qRootType">—</div>
                    </div>

                    <div class="row g-3 text-center mt-1">
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="qDisc">—</div>
                                <div class="text-muted small">Discriminant (b&sup2; − 4ac)</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="qVertex">—</div>
                                <div class="text-muted small">Vertex (h, k)</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="qAxis">—</div>
                                <div class="text-muted small">Axis of Symmetry</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="qYint">—</div>
                                <div class="text-muted small">y-intercept</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="qOpens">—</div>
                                <div class="text-muted small">Parabola opens</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="qFactor">—</div>
                                <div class="text-muted small">Sum / Product of roots</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <canvas id="qPlot" width="640" height="400" class="border rounded w-100" style="max-width:640px;background:#fff;"></canvas>
                        <p class="small text-muted mb-0">Graph: the parabola with its vertex (green point) and real roots (red points) is shown.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>The quadratic formula is: <strong>x = (−b &plusmn; &radic;(b&sup2; − 4ac)) &divide; 2a</strong>. The discriminant (D = b&sup2; − 4ac) tells the type of the roots: if D &gt; 0 there are two different real roots, if D = 0 there is one repeated root, and if D &lt; 0 there are complex roots (p &plusmn; qi).</p>
                    <ul>
                        <li>Example 1: x&sup2; − 5x + 6 = 0 — D = 25 − 24 = 1, roots <strong>x = 3</strong> and <strong>x = 2</strong>, vertex (2.5, −0.25).</li>
                        <li>Example 2: x&sup2; + 2x + 5 = 0 — D = 4 − 20 = −16, complex roots <strong>−1 &plusmn; 2i</strong> (the parabola does not touch the x-axis).</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: if a = 0, the equation is no longer quadratic — it becomes linear (bx + c = 0), and the calculator will answer accordingly.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function val(id) {
        var v = document.getElementById(id).value;
        return v === '' ? null : parseFloat(v);
    }
    function fmt(n) {
        if (!isFinite(n)) { return '—'; }
        return parseFloat(n.toPrecision(10)).toLocaleString('en-US', { maximumFractionDigits: 6 });
    }
    function term(coef, sym, first) {
        if (coef === 0) { return ''; }
        var sign = coef < 0 ? ' − ' : (first ? '' : ' + ');
        if (coef < 0 && first) { sign = '−'; }
        var abs = Math.abs(coef);
        var num = (abs === 1 && sym !== '') ? '' : fmt(abs);
        return sign + num + sym;
    }
    function eqStr(a, b, c) {
        var s = term(a, 'x²', true) + term(b, 'x', a === 0) + term(c, '', a === 0 && b === 0);
        if (s === '') { s = '0'; }
        return s + ' = 0';
    }

    function drawPlot(a, b, c, roots) {
        var canvas = document.getElementById('qPlot');
        var ctx = canvas.getContext('2d');
        var W = canvas.width, H = canvas.height;
        ctx.clearRect(0, 0, W, H);

        var h = a !== 0 ? -b / (2 * a) : 0;
        var k = a * h * h + b * h + c;
        var xCenter = h;
        var xSpan = 5;
        roots.forEach(function (r) { xSpan = Math.max(xSpan, Math.abs(r - xCenter) + 2); });
        var xMin = xCenter - xSpan, xMax = xCenter + xSpan;

        function yAt(x) { return a * x * x + b * x + c; }
        var yMin = Math.min(0, k), yMax = Math.max(0, k);
        var i, x, y;
        for (i = 0; i <= 200; i++) {
            x = xMin + (xMax - xMin) * i / 200;
            y = yAt(x);
            if (y < yMin) { yMin = y; }
            if (y > yMax) { yMax = y; }
        }
        if (yMax - yMin < 1) { yMax += 1; yMin -= 1; }
        var yPad = (yMax - yMin) * 0.15;
        yMax += yPad; yMin -= yPad;

        function px(xv) { return (xv - xMin) / (xMax - xMin) * W; }
        function py(yv) { return H - (yv - yMin) / (yMax - yMin) * H; }

        // grid
        ctx.strokeStyle = '#e5e5e5'; ctx.lineWidth = 1;
        ctx.beginPath();
        for (i = Math.ceil(xMin); i <= xMax; i++) { ctx.moveTo(px(i), 0); ctx.lineTo(px(i), H); }
        for (i = Math.ceil(yMin); i <= yMax; i++) { ctx.moveTo(0, py(i)); ctx.lineTo(W, py(i)); }
        ctx.stroke();
        // axes
        ctx.strokeStyle = '#888'; ctx.lineWidth = 1.5;
        ctx.beginPath();
        if (xMin < 0 && xMax > 0) { ctx.moveTo(px(0), 0); ctx.lineTo(px(0), H); }
        if (yMin < 0 && yMax > 0) { ctx.moveTo(0, py(0)); ctx.lineTo(W, py(0)); }
        ctx.stroke();
        // axis labels
        ctx.fillStyle = '#666'; ctx.font = '12px sans-serif';
        ctx.fillText('x', W - 16, py(0) > 14 ? py(0) - 6 : 14);
        ctx.fillText('y', px(0) < W - 16 ? px(0) + 6 : W - 16, 14);

        // curve
        ctx.strokeStyle = '#1b7a43'; ctx.lineWidth = 2.5;
        ctx.beginPath();
        var started = false;
        for (i = 0; i <= 300; i++) {
            x = xMin + (xMax - xMin) * i / 300;
            y = yAt(x);
            var sx = px(x), sy = py(y);
            if (sy < -50 || sy > H + 50) { started = false; continue; }
            if (!started) { ctx.moveTo(sx, Math.max(-10, Math.min(H + 10, sy))); started = true; }
            else { ctx.lineTo(sx, sy); }
        }
        ctx.stroke();

        // roots
        ctx.fillStyle = '#d43b2f';
        roots.forEach(function (r) {
            ctx.beginPath(); ctx.arc(px(r), py(0), 5, 0, Math.PI * 2); ctx.fill();
        });
        // vertex
        if (a !== 0) {
            ctx.fillStyle = '#1b7a43';
            ctx.beginPath(); ctx.arc(px(h), py(k), 5, 0, Math.PI * 2); ctx.fill();
        }
    }

    function calc() {
        var a = val('qa'), b = val('qb'), c = val('qc');
        var ids = ['qRoots', 'qRootType', 'qDisc', 'qVertex', 'qAxis', 'qYint', 'qOpens', 'qFactor'];
        if (a === null || b === null || c === null) {
            ids.forEach(function (id) { document.getElementById(id).textContent = '—'; });
            document.getElementById('qEq').textContent = '—';
            return;
        }
        document.getElementById('qEq').textContent = eqStr(a, b, c);

        var rootsEl = document.getElementById('qRoots');
        var typeEl = document.getElementById('qRootType');
        var realRoots = [];

        if (a === 0) {
            document.getElementById('qDisc').textContent = '—';
            document.getElementById('qVertex').textContent = '—';
            document.getElementById('qAxis').textContent = '—';
            document.getElementById('qOpens').textContent = '— (line)';
            document.getElementById('qFactor').textContent = '—';
            document.getElementById('qYint').textContent = fmt(c);
            if (b === 0) {
                rootsEl.textContent = c === 0 ? 'Any x works (0 = 0)' : 'No solution';
                typeEl.textContent = 'This is not a quadratic or linear equation (a = 0 and b = 0).';
            } else {
                var lx = -c / b;
                rootsEl.textContent = 'x = ' + fmt(lx);
                typeEl.textContent = 'a = 0, so this is a linear equation: ' + fmt(b) + 'x + ' + fmt(c) + ' = 0.';
                realRoots = [lx];
            }
            drawPlot(0, b, c, realRoots);
            return;
        }

        var D = b * b - 4 * a * c;
        var h = -b / (2 * a);
        var k = a * h * h + b * h + c;
        document.getElementById('qDisc').textContent = fmt(D);
        document.getElementById('qVertex').textContent = '(' + fmt(h) + ', ' + fmt(k) + ')';
        document.getElementById('qAxis').textContent = 'x = ' + fmt(h);
        document.getElementById('qYint').textContent = fmt(c);
        document.getElementById('qOpens').textContent = a > 0 ? 'Upwards (U shape)' : 'Downwards (∩ shape)';
        document.getElementById('qFactor').textContent = 'Sum = ' + fmt(-b / a) + ' · Product = ' + fmt(c / a);

        if (D > 0) {
            var sq = Math.sqrt(D);
            var r1 = (-b + sq) / (2 * a);
            var r2 = (-b - sq) / (2 * a);
            rootsEl.textContent = 'x₁ = ' + fmt(r1) + ' ,  x₂ = ' + fmt(r2);
            typeEl.textContent = 'D > 0 — two different real roots.';
            realRoots = [r1, r2];
        } else if (D === 0) {
            var r = -b / (2 * a);
            rootsEl.textContent = 'x = ' + fmt(r) + ' (repeated)';
            typeEl.textContent = 'D = 0 — one repeated root; the parabola just touches the x-axis.';
            realRoots = [r];
        } else {
            var rp = -b / (2 * a);
            var ip = Math.sqrt(-D) / Math.abs(2 * a);
            rootsEl.textContent = 'x = ' + fmt(rp) + ' ± ' + fmt(ip) + 'i';
            typeEl.textContent = 'D < 0 — complex roots (' + fmt(rp) + ' + ' + fmt(ip) + 'i and ' + fmt(rp) + ' − ' + fmt(ip) + 'i); the parabola does not touch the x-axis.';
            realRoots = [];
        }
        drawPlot(a, b, c, realRoots);
    }

    ['qa', 'qb', 'qc'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc); });
    calc();
})();
</script>
@endsection
