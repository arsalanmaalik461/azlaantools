@extends('layouts.app')

@section('title', 'Exponent Calculator — Azlaan Tools')
@section('meta_description', 'Free exponent calculator. Enter a base and exponent (negative or fractional too) to get the result with step-by-step working, plus quick square, cube, square root and cube root.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Exponent Calculator</h1>
            <p class="lead text-muted">Enter the base and the power — you get the result with steps right away. Negative and fractional (like 0.5) exponents also work.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-5">
                            <label for="baseInput" class="form-label fw-semibold">Base (x)</label>
                            <input type="number" class="form-control form-control-lg" id="baseInput" placeholder="e.g. 2" step="any">
                        </div>
                        <div class="col-2 text-center fs-3 fw-bold pb-1">^</div>
                        <div class="col-5">
                            <label for="expInput" class="form-label fw-semibold">Exponent (n)</label>
                            <input type="number" class="form-control form-control-lg" id="expInput" placeholder="e.g. 10" step="any">
                        </div>
                    </div>
                    <div class="result-box mt-3">
                        <div class="text-muted small text-center">Result</div>
                        <div class="display-6 fw-bold text-center" id="expResult">—</div>
                        <p class="text-center mb-0 mt-2" id="expSteps">Enter the base and exponent — the steps will appear here.</p>
                    </div>

                    <h2 class="h6 fw-semibold mt-4">Quick powers of the base</h2>
                    <p class="small text-muted">One click on the base above:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary" id="quickSquare">x&sup2; Square</button>
                        <button type="button" class="btn btn-outline-primary" id="quickCube">x&sup3; Cube</button>
                        <button type="button" class="btn btn-outline-primary" id="quickSqrt">&radic;x Square Root</button>
                        <button type="button" class="btn btn-outline-primary" id="quickCbrt">&#8731;x Cube Root</button>
                    </div>
                    <p class="fw-semibold mt-2 mb-0" id="quickResult"></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>An exponent means multiplying the base by itself a certain number of times. A negative exponent means first finding the positive power, then taking its reciprocal (1 &divide; result). A fractional exponent of 0.5 means square root, and 1/3 means cube root.</p>
                    <p class="mb-1"><strong>Example 1:</strong> 2<sup>10</sup> = 2 &times; 2 &times; 2 &times; 2 &times; 2 &times; 2 &times; 2 &times; 2 &times; 2 &times; 2 = <strong>1024</strong>.</p>
                    <p class="mb-0"><strong>Example 2:</strong> 2<sup>-2</sup> = 1 &divide; 2&sup2; = 1 &divide; 4 = <strong>0.25</strong>. And 9<sup>0.5</sup> = &radic;9 = <strong>3</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function getBase() {
        var v = document.getElementById('baseInput').value;
        if (v === '' || v === null) return null;
        var p = parseFloat(v);
        return isFinite(p) ? p : null;
    }
    function getExp() {
        var v = document.getElementById('expInput').value;
        if (v === '' || v === null) return null;
        var p = parseFloat(v);
        return isFinite(p) ? p : null;
    }
    function fmt(n) {
        if (typeof n !== 'number' || !isFinite(n)) return '—';
        if (n !== 0 && (Math.abs(n) >= 1e15 || Math.abs(n) < 0.0001)) {
            return n.toExponential(6);
        }
        var r = Number(n.toFixed(8));
        return r.toLocaleString('en-PK', { maximumFractionDigits: 8 });
    }
    function calc() {
        var resEl = document.getElementById('expResult');
        var stepEl = document.getElementById('expSteps');
        var base = getBase(), exp = getExp();
        if (base === null || exp === null) {
            resEl.textContent = '—';
            stepEl.textContent = 'Enter the base and exponent — the steps will appear here.';
            return;
        }
        if (base === 0 && exp <= 0) {
            resEl.textContent = '—';
            stepEl.textContent = 'If 0 is raised to 0 or a negative power, the result is undefined.';
            return;
        }
        if (base < 0 && Math.floor(exp) !== exp) {
            resEl.textContent = '—';
            stepEl.textContent = 'A negative base with a fractional exponent does not give a real number (it is complex).';
            return;
        }
        var result = Math.pow(base, exp);
        if (!isFinite(result) || Math.abs(result) > 1e308) {
            resEl.textContent = 'Too large';
            stepEl.textContent = 'The result is too large (more than 1e308) — beyond the computer limit. Try smaller numbers.';
            return;
        }
        resEl.textContent = fmt(result);
        var steps = '';
        if (Math.floor(exp) === exp && exp >= 2 && exp <= 12 && base !== 0) {
            var parts = [];
            for (var i = 0; i < exp; i++) parts.push(String(base));
            steps = base + '^' + exp + ' = ' + parts.join(' × ') + ' = ' + fmt(result);
        } else if (Math.floor(exp) === exp && exp < 0 && exp >= -12) {
            var pos = Math.pow(base, Math.abs(exp));
            steps = 'Rule: negative exponent = reciprocal. ' + base + '^' + exp + ' = 1 ÷ ' + base + '^' + Math.abs(exp) + ' = 1 ÷ ' + fmt(pos) + ' = ' + fmt(result);
        } else if (exp === 0) {
            steps = 'Rule: any number raised to the power 0 is always 1 (except 0).';
        } else if (exp === 1) {
            steps = 'Rule: if the power is 1, the answer is the base itself.';
        } else if (exp === 0.5) {
            steps = 'Rule: power 0.5 means square root. √' + base + ' = ' + fmt(result);
        } else {
            steps = 'Rule: fractional exponent = root. ' + base + '^' + exp + ' was calculated with Math.pow — the answer is ' + fmt(result) + '.';
        }
        stepEl.textContent = steps;
    }
    function quick(kind) {
        var out = document.getElementById('quickResult');
        var base = getBase();
        if (base === null) { out.textContent = 'First enter the base above.'; return; }
        var label = '', val = null;
        if (kind === 'sq') { label = base + '² = '; val = base * base; }
        else if (kind === 'cu') { label = base + '³ = '; val = base * base * base; }
        else if (kind === 'sqrt') {
            if (base < 0) { out.textContent = 'The square root of a negative number is not a real number.'; return; }
            label = '√' + base + ' = '; val = Math.sqrt(base);
        } else if (kind === 'cbrt') { label = '∛' + base + ' = '; val = Math.cbrt(base); }
        if (val === null || !isFinite(val) || Math.abs(val) > 1e308) { out.textContent = label + 'too large'; return; }
        out.textContent = label + fmt(val);
    }
    document.getElementById('baseInput').addEventListener('input', calc);
    document.getElementById('expInput').addEventListener('input', calc);
    document.getElementById('quickSquare').addEventListener('click', function () { quick('sq'); });
    document.getElementById('quickCube').addEventListener('click', function () { quick('cu'); });
    document.getElementById('quickSqrt').addEventListener('click', function () { quick('sqrt'); });
    document.getElementById('quickCbrt').addEventListener('click', function () { quick('cbrt'); });
    calc();
})();
</script>
@endsection
