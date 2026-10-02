@extends('layouts.app')

@section('title', 'Scientific Calculator — Azlaan Tools')
@section('meta_description', 'Free online scientific calculator with sin, cos, tan, log, ln, square root, powers, factorial and degrees/radians mode. Full keyboard support, calculation history, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Scientific Calculator</h1>
            <p class="lead text-muted">Full scientific calculator — with trigonometry, log, powers, roots and factorial. Press the buttons or use your keyboard, the answer shows instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="border rounded p-3 mb-1 bg-light">
                        <div class="text-muted small text-end text-break" id="sciExpr" style="min-height:1.4em;">&nbsp;</div>
                        <div class="fs-2 fw-bold text-end text-break" id="sciDisplay">0</div>
                    </div>
                    <div class="small mb-3 text-end">
                        <span class="badge bg-secondary" id="angleBadge">DEG</span>
                        <span class="text-danger d-none" id="sciHint">Wrong expression — check it and write it again.</span>
                    </div>

                    <div id="sciPad">
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary btn-lg w-100 sci-btn" data-act="deg" id="degBtn">DEG</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="(">(</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins=")">)</button></div>
                            <div class="col-3"><button type="button" class="btn btn-warning btn-lg w-100 sci-btn" data-act="back">&#9003;</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-danger btn-lg w-100 sci-btn" data-act="clear">C</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="%">%</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-act="neg">+/-</button></div>
                            <div class="col-3"><button type="button" class="btn btn-primary btn-lg w-100 sci-btn" data-ins="/">&divide;</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="sin(">sin</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="cos(">cos</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="tan(">tan</button></div>
                            <div class="col-3"><button type="button" class="btn btn-primary btn-lg w-100 sci-btn" data-ins="*">&times;</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="asin(">sin<sup>-1</sup></button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="acos(">cos<sup>-1</sup></button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="atan(">tan<sup>-1</sup></button></div>
                            <div class="col-3"><button type="button" class="btn btn-primary btn-lg w-100 sci-btn" data-ins="-">&minus;</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="ln(">ln</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="log(">log</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="&pi;">&pi;</button></div>
                            <div class="col-3"><button type="button" class="btn btn-primary btn-lg w-100 sci-btn" data-ins="+">+</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="&radic;(">&radic;</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="^2">x&sup2;</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="^">x&#696;</button></div>
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="e">e</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-ins="!">x!</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="7">7</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="8">8</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="9">9</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-light border btn-lg w-100 sci-btn" data-act="ans">ANS</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="4">4</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="5">5</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="6">6</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins=".">.</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="1">1</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="2">2</button></div>
                            <div class="col-3"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="3">3</button></div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><button type="button" class="btn btn-secondary btn-lg w-100 sci-btn" data-ins="0">0</button></div>
                            <div class="col-6"><button type="button" class="btn btn-success btn-lg w-100 sci-btn" data-act="equals">=</button></div>
                        </div>
                    </div>

                    <h2 class="h6 mt-4 mb-2">History (last 10) — click any result to use it again</h2>
                    <ul class="list-group" id="sciHistory">
                        <li class="list-group-item text-muted" id="sciHistoryEmpty">No calculations yet.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>Write an expression and press <strong>=</strong>. The calculator handles brackets and order of operations itself (powers first, then &times; &divide;, then + &minus;) — no extra steps needed.</p>
                    <ul>
                        <li>Example 1: <code>2 + 3 * 4</code> = <strong>14</strong> (first 3 &times; 4 = 12, then + 2).</li>
                        <li>Example 2: in DEG mode <code>sin(90)</code> = <strong>1</strong>. Keep DEG if your angle is in degrees; switch to RAD with the DEG button for radians.</li>
                        <li>Example 3: <code>2^10</code> = <strong>1,024</strong> and <code>5!</code> = <strong>120</strong>.</li>
                        <li>Keyboard: type numbers and + &minus; * / ^ directly, <strong>Enter</strong> = equals, <strong>Backspace</strong> = delete one character, <strong>Escape</strong> = clear.</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: log means log base 10, ln is the natural log. % divides a number by 100 — for example 50% = 0.5.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var expr = '';
    var ans = null;
    var angleMode = 'DEG';
    var justEvaluated = false;
    var history = [];

    var display = document.getElementById('sciDisplay');
    var exprLine = document.getElementById('sciExpr');
    var hint = document.getElementById('sciHint');
    var badge = document.getElementById('angleBadge');
    var degBtn = document.getElementById('degBtn');
    var historyList = document.getElementById('sciHistory');

    function pretty(s) {
        return s.replace(/\*/g, '\u00D7').replace(/\//g, '\u00F7');
    }

    function render() {
        display.textContent = expr === '' ? '0' : pretty(expr);
        display.classList.remove('text-danger');
        hint.classList.add('d-none');
        exprLine.innerHTML = '&nbsp;';
    }

    function showError() {
        display.textContent = 'Error';
        display.classList.add('text-danger');
        hint.classList.remove('d-none');
    }

    // ---------- Safe parser (tokenizer + recursive descent). No eval(). ----------
    function tokenize(s) {
        var tokens = [];
        var i = 0;
        while (i < s.length) {
            var c = s.charAt(i);
            if (c === ' ') { i++; continue; }
            if ((c >= '0' && c <= '9') || c === '.') {
                var num = '';
                var dots = 0;
                while (i < s.length && ((s.charAt(i) >= '0' && s.charAt(i) <= '9') || s.charAt(i) === '.')) {
                    if (s.charAt(i) === '.') { dots++; if (dots > 1) { throw new Error('bad number'); } }
                    num += s.charAt(i); i++;
                }
                var v = parseFloat(num);
                if (!isFinite(v)) { throw new Error('bad number'); }
                tokens.push({ t: 'num', v: v });
                continue;
            }
            if (c === '\u03C0') { tokens.push({ t: 'num', v: Math.PI }); i++; continue; }
            if (c === '\u221A') { tokens.push({ t: 'fn', v: 'sqrt' }); i++; continue; }
            if ((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z')) {
                var name = '';
                while (i < s.length && ((s.charAt(i) >= 'a' && s.charAt(i) <= 'z') || (s.charAt(i) >= 'A' && s.charAt(i) <= 'Z'))) { name += s.charAt(i); i++; }
                if (name === 'e') { tokens.push({ t: 'num', v: Math.E }); continue; }
                if (['sin', 'cos', 'tan', 'asin', 'acos', 'atan', 'ln', 'log', 'sqrt'].indexOf(name) === -1) { throw new Error('unknown: ' + name); }
                tokens.push({ t: 'fn', v: name });
                continue;
            }
            if ('+-*/^()!%'.indexOf(c) !== -1) { tokens.push({ t: 'op', v: c }); i++; continue; }
            throw new Error('bad char: ' + c);
        }
        return tokens;
    }

    function gamma(z) {
        // Lanczos approximation, used for non-integer factorials.
        var p = [0.99999999999980993, 676.5203681218851, -1259.1392167224028, 771.32342877765313, -176.61502916214059, 12.507343278686905, -0.13857109526572012, 9.9843695780195716e-6, 1.5056327351493116e-7];
        if (z < 0.5) { return Math.PI / (Math.sin(Math.PI * z) * gamma(1 - z)); }
        z -= 1;
        var x = p[0];
        for (var i = 1; i < p.length; i++) { x += p[i] / (z + i); }
        var t = z + p.length - 1.5;
        return Math.sqrt(2 * Math.PI) * Math.pow(t, z + 0.5) * Math.exp(-t) * x;
    }

    function factorial(n) {
        if (n < 0 && Math.floor(n) === n) { throw new Error('factorial of negative'); }
        if (Math.floor(n) === n) {
            if (n > 170) { throw new Error('too big'); }
            var r = 1;
            for (var i = 2; i <= n; i++) { r *= i; }
            return r;
        }
        return gamma(n + 1);
    }

    function applyFn(name, x) {
        var deg = Math.PI / 180;
        switch (name) {
            case 'sin': return Math.sin(angleMode === 'DEG' ? x * deg : x);
            case 'cos': return Math.cos(angleMode === 'DEG' ? x * deg : x);
            case 'tan':
                var a = angleMode === 'DEG' ? x * deg : x;
                if (Math.abs(Math.cos(a)) < 1e-12) { throw new Error('tan undefined'); }
                return Math.tan(a);
            case 'asin':
                if (x < -1 || x > 1) { throw new Error('asin domain'); }
                var r1 = Math.asin(x);
                return angleMode === 'DEG' ? r1 / deg : r1;
            case 'acos':
                if (x < -1 || x > 1) { throw new Error('acos domain'); }
                var r2 = Math.acos(x);
                return angleMode === 'DEG' ? r2 / deg : r2;
            case 'atan':
                var r3 = Math.atan(x);
                return angleMode === 'DEG' ? r3 / deg : r3;
            case 'ln':
                if (x <= 0) { throw new Error('ln domain'); }
                return Math.log(x);
            case 'log':
                if (x <= 0) { throw new Error('log domain'); }
                return Math.log10(x);
            case 'sqrt':
                if (x < 0) { throw new Error('sqrt domain'); }
                return Math.sqrt(x);
        }
        throw new Error('unknown fn');
    }

    function evaluate(s) {
        var tokens = tokenize(s);
        if (tokens.length === 0) { throw new Error('empty'); }
        var pos = 0;

        function peek() { return pos < tokens.length ? tokens[pos] : null; }
        function next() { return tokens[pos++]; }
        function isOp(tok, v) { return tok && tok.t === 'op' && tok.v === v; }

        function parseExpr() {
            var v = parseTerm();
            while (isOp(peek(), '+') || isOp(peek(), '-')) {
                var op = next().v;
                var rhs = parseTerm();
                v = op === '+' ? v + rhs : v - rhs;
            }
            return v;
        }
        function parseTerm() {
            var v = parseFactor();
            for (;;) {
                var tok = peek();
                if (isOp(tok, '*') || isOp(tok, '/')) {
                    var op = next().v;
                    var rhs = parseFactor();
                    if (op === '/') {
                        if (rhs === 0) { throw new Error('divide by zero'); }
                        v = v / rhs;
                    } else { v = v * rhs; }
                } else if (tok && (tok.t === 'num' || tok.t === 'fn' || isOp(tok, '('))) {
                    v = v * parseFactor(); // implicit multiplication, e.g. 2π or 3(4+1)
                } else { break; }
            }
            return v;
        }
        function parseFactor() {
            var tok = peek();
            if (isOp(tok, '-')) { next(); return -parseFactor(); }
            if (isOp(tok, '+')) { next(); return parseFactor(); }
            return parsePower();
        }
        function parsePower() {
            var base = parsePostfix();
            if (isOp(peek(), '^')) {
                next();
                var exp = parseFactor(); // right-associative, allows 2^-3
                return Math.pow(base, exp);
            }
            return base;
        }
        function parsePostfix() {
            var v = parsePrimary();
            for (;;) {
                var tok = peek();
                if (isOp(tok, '!')) { next(); v = factorial(v); }
                else if (isOp(tok, '%')) { next(); v = v / 100; }
                else { break; }
            }
            return v;
        }
        function parsePrimary() {
            var tok = next();
            if (!tok) { throw new Error('unexpected end'); }
            if (tok.t === 'num') { return tok.v; }
            if (tok.t === 'fn') {
                if (!isOp(peek(), '(')) { throw new Error('missing ('); }
                next();
                var arg = parseExpr();
                if (!isOp(peek(), ')')) { throw new Error('missing )'); }
                next();
                return applyFn(tok.v, arg);
            }
            if (isOp(tok, '(')) {
                var v = parseExpr();
                if (!isOp(peek(), ')')) { throw new Error('missing )'); }
                next();
                return v;
            }
            throw new Error('unexpected token');
        }

        var result = parseExpr();
        if (pos !== tokens.length) { throw new Error('trailing tokens'); }
        if (typeof result !== 'number' || !isFinite(result)) { throw new Error('not finite'); }
        return result;
    }

    function fmtNum(n) {
        var r = parseFloat(n.toPrecision(12));
        if (!isFinite(r)) { return 'Error'; }
        return r.toLocaleString('en-US', { maximumFractionDigits: 10 });
    }

    // ---------- Interaction ----------
    function insert(text) {
        if (justEvaluated) {
            var startsValue = /^[0-9.(\u03C0\u221A]/.test(text) || /^(sin|cos|tan|asin|acos|atan|ln|log|e)/.test(text);
            if (startsValue) { expr = ''; }
            justEvaluated = false;
        }
        expr += text;
        render();
    }

    function doEquals() {
        if (expr === '') { return; }
        try {
            var result = evaluate(expr);
            exprLine.textContent = pretty(expr) + ' =';
            ans = result;
            history.unshift({ expr: pretty(expr), result: result });
            if (history.length > 10) { history.pop(); }
            renderHistory();
            expr = String(parseFloat(result.toPrecision(12)));
            justEvaluated = true;
            display.textContent = fmtNum(result);
            hint.classList.add('d-none');
        } catch (err) {
            justEvaluated = false;
            showError();
        }
    }

    function renderHistory() {
        historyList.innerHTML = '';
        if (history.length === 0) {
            var li0 = document.createElement('li');
            li0.className = 'list-group-item text-muted';
            li0.textContent = 'No calculations yet.';
            historyList.appendChild(li0);
            return;
        }
        history.forEach(function (h) {
            var li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action d-flex justify-content-between';
            li.style.cursor = 'pointer';
            var left = document.createElement('span');
            left.textContent = h.expr;
            var right = document.createElement('strong');
            right.textContent = fmtNum(h.result);
            li.appendChild(left);
            li.appendChild(right);
            li.addEventListener('click', function () {
                expr = String(parseFloat(h.result.toPrecision(12)));
                justEvaluated = false;
                render();
            });
            historyList.appendChild(li);
        });
    }

    function toggleSign() {
        var m = expr.match(/(\d+\.?\d*|\.\d+)$/);
        if (!m) { return; }
        var start = expr.length - m[0].length;
        var before = start > 0 ? expr.charAt(start - 1) : '';
        var before2 = start > 1 ? expr.charAt(start - 2) : '';
        if (before === '-' && (start - 1 === 0 || '+-*/^('.indexOf(before2) !== -1)) {
            expr = expr.slice(0, start - 1) + expr.slice(start);
        } else {
            expr = expr.slice(0, start) + '-' + expr.slice(start);
        }
        justEvaluated = false;
        render();
    }

    document.getElementById('sciPad').addEventListener('click', function (e) {
        var btn = e.target.closest('.sci-btn');
        if (!btn) { return; }
        var act = btn.getAttribute('data-act');
        var ins = btn.getAttribute('data-ins');
        if (ins !== null) { insert(ins); return; }
        if (act === 'clear') { expr = ''; justEvaluated = false; render(); }
        else if (act === 'back') { expr = expr.slice(0, -1); justEvaluated = false; render(); }
        else if (act === 'equals') { doEquals(); }
        else if (act === 'neg') { toggleSign(); }
        else if (act === 'ans') { if (ans !== null) { insert(String(parseFloat(ans.toPrecision(12)))); } }
        else if (act === 'deg') {
            angleMode = angleMode === 'DEG' ? 'RAD' : 'DEG';
            degBtn.textContent = angleMode;
            badge.textContent = angleMode;
        }
    });

    document.addEventListener('keydown', function (e) {
        var tag = (e.target && e.target.tagName) || '';
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') { return; }
        var k = e.key;
        if (/^[0-9.]$/.test(k)) { insert(k); }
        else if (k === '+' || k === '-' || k === '*' || k === '/' || k === '^' || k === '(' || k === ')' || k === '%' || k === '!') { insert(k); }
        else if (k === 'Enter' || k === '=') { e.preventDefault(); doEquals(); }
        else if (k === 'Backspace') { e.preventDefault(); expr = expr.slice(0, -1); justEvaluated = false; render(); }
        else if (k === 'Escape') { expr = ''; justEvaluated = false; render(); }
        else if (k === 'p') { insert('\u03C0'); }
    });

    render();
})();
</script>
@endsection
