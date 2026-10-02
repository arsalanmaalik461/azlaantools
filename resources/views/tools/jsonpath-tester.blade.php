@extends('layouts.app')
@section('title', 'JSONPath Tester - Evaluate JSONPath Expressions Free | Azlaan Tools')
@section('meta_description', 'Test JSONPath expressions against your JSON free in the browser: filters, wildcards, slices and recursive descent with live results. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-2">JSONPath Tester</h1>
            <p class="lead text-muted">Test a JSONPath expression against your JSON — see the path and value of every match instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="jsonInput" class="form-label fw-semibold">JSON data</label>
                        <textarea class="form-control font-monospace" id="jsonInput" rows="9" placeholder='{"store": {"book": [...]}}'></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="exprInput" class="form-label fw-semibold">JSONPath expression</label>
                        <input type="text" class="form-control font-monospace" id="exprInput" placeholder="$.store.book[?(@.price < 10)].title" value="$.store.book[*].title">
                    </div>
                    <div class="mb-3">
                        <span class="small text-muted me-2">Examples:</span>
                        <span id="exBtns">
                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-expr="$.store.book[*].title">$.store.book[*].title</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-expr="$..author">$..author</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-expr="$.store.book[?(@.price &lt; 10)].title">$.store.book[?(@.price &lt; 10)].title</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-expr="$.store.book[-1].title">$.store.book[-1].title</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-expr="$.store.book[0:2].title">$.store.book[0:2].title</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-expr="$..book[?(@.category == 'fiction')].price">$..book[?(@.category == 'fiction')].price</button>
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" id="sampleJsonBtn" class="btn btn-outline-secondary">Sample JSON</button>
                        <button type="button" id="testBtn" class="btn btn-primary btn-lg flex-grow-1">Test Expression</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Matches: <span id="resultCount" class="badge bg-primary">0</span></h2>
                        <div id="resultList" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <h2>Supported syntax</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light"><tr><th>Expression</th><th>Meaning</th></tr></thead>
                    <tbody>
                        <tr><td class="font-monospace">$.a.b</td><td>Child property</td></tr>
                        <tr><td class="font-monospace">$['a']</td><td>Bracket child (for special names)</td></tr>
                        <tr><td class="font-monospace">$[0], $[-1]</td><td>Array index, from the end</td></tr>
                        <tr><td class="font-monospace">$[1:4], $[::2]</td><td>Slice (start:end:step)</td></tr>
                        <tr><td class="font-monospace">$.* , $[*]</td><td>All children (wildcard)</td></tr>
                        <tr><td class="font-monospace">$..name</td><td>Recursive descent — the name at every depth</td></tr>
                        <tr><td class="font-monospace">[?(@.price &lt; 10)]</td><td>Filter: <code>== != &lt; &lt;= &gt; &gt;=</code>, <code>&amp;&amp;</code> <code>||</code> <code>!</code></td></tr>
                        <tr><td class="font-monospace">$[0,2,'a']</td><td>Union — multiple indexes or keys</td></tr>
                    </tbody>
                </table>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste your JSON in the box above (or click Sample JSON).</li>
                <li>Write a JSONPath expression or click an example button.</li>
                <li>Click <strong>Test Expression</strong> — the path and value of every match will appear below.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var jsonInput = document.getElementById('jsonInput');
    var exprInput = document.getElementById('exprInput');
    var testBtn = document.getElementById('testBtn');
    var sampleJsonBtn = document.getElementById('sampleJsonBtn');
    var exBtns = document.getElementById('exBtns');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultCount = document.getElementById('resultCount');
    var resultList = document.getElementById('resultList');

    /* ---------- JSONPath parser + evaluator (pure JS) ---------- */
    function JParser(src) { this.src = src; this.pos = 0; }
    JParser.prototype.err = function (m) { throw new Error(m + ' (position ' + this.pos + ')'); };
    JParser.prototype.peek = function () { return this.src.charAt(this.pos); };
    JParser.prototype.skip = function () { while (this.pos < this.src.length && /\s/.test(this.src.charAt(this.pos))) this.pos++; };
    JParser.prototype.expect = function (ch) {
        this.skip();
        if (this.src.charAt(this.pos) !== ch) this.err('expected "' + ch + '"');
        this.pos++;
    };
    JParser.prototype.parseString = function () {
        this.skip();
        var q = this.src.charAt(this.pos);
        if (q !== "'" && q !== '"') this.err('expected string');
        this.pos++;
        var out = '';
        while (this.pos < this.src.length) {
            var c = this.src.charAt(this.pos);
            if (c === '\\' && this.pos + 1 < this.src.length) {
                var n = this.src.charAt(this.pos + 1);
                out += (n === 'n' ? '\n' : n === 't' ? '\t' : n);
                this.pos += 2;
            } else if (c === q) { this.pos++; return out; }
            else { out += c; this.pos++; }
        }
        this.err('unterminated string');
        return '';
    };
    JParser.prototype.parseName = function () {
        this.skip();
        var m = /^[A-Za-z0-9_$\-]+/.exec(this.src.slice(this.pos));
        if (!m) this.err('expected property name');
        this.pos += m[0].length;
        return m[0];
    };
    JParser.prototype.parseIntTok = function () {
        this.skip();
        var m = /^(-?\d+)/.exec(this.src.slice(this.pos));
        if (!m) return null;
        this.pos += m[0].length;
        return parseInt(m[1], 10);
    };

    function parseShorthand(P) {
        var c = P.peek();
        if (c === '*') { P.pos++; return { t: 'wild' }; }
        return { t: 'child', key: P.parseName() };
    }

    function parseFilterExpr(P) { return parseOr(P); }
    function parseOr(P) {
        var l = parseAnd(P); P.skip();
        while (P.src.substr(P.pos, 2) === '||') { P.pos += 2; l = { t: 'or', l: l, r: parseAnd(P) }; P.skip(); }
        return l;
    }
    function parseAnd(P) {
        var l = parseUnary(P); P.skip();
        while (P.src.substr(P.pos, 2) === '&&') { P.pos += 2; l = { t: 'and', l: l, r: parseUnary(P) }; P.skip(); }
        return l;
    }
    function parseUnary(P) {
        P.skip();
        if (P.peek() === '!') { P.pos++; return { t: 'not', x: parseUnary(P) }; }
        if (P.peek() === '(') { P.pos++; var e = parseOr(P); P.skip(); P.expect(')'); return e; }
        return parseComparison(P);
    }
    function parseComparison(P) {
        var left = parseOperand(P); P.skip();
        var ops = ['==', '!=', '<=', '>=', '<', '>'];
        for (var i = 0; i < ops.length; i++) {
            if (P.src.substr(P.pos, ops[i].length) === ops[i]) {
                P.pos += ops[i].length;
                return { t: 'cmp', op: ops[i], l: left, r: parseOperand(P) };
            }
        }
        return { t: 'exists', x: left };
    }
    function parseOperand(P) {
        P.skip();
        var c = P.peek();
        if (c === '@') { P.pos++; return { t: 'path', steps: parseAtSteps(P) }; }
        if (c === "'" || c === '"') return { t: 'lit', v: P.parseString() };
        var m = /^(-?\d+(\.\d+)?)/.exec(P.src.slice(P.pos));
        if (m) { P.pos += m[0].length; return { t: 'lit', v: parseFloat(m[0]) }; }
        if (P.src.substr(P.pos, 4) === 'true') { P.pos += 4; return { t: 'lit', v: true }; }
        if (P.src.substr(P.pos, 5) === 'false') { P.pos += 5; return { t: 'lit', v: false }; }
        if (P.src.substr(P.pos, 4) === 'null') { P.pos += 4; return { t: 'lit', v: null }; }
        P.err('bad filter operand');
        return null;
    }
    function parseAtSteps(P) {
        var steps = [];
        for (;;) {
            P.skip();
            var c = P.peek();
            if (c === '.') {
                P.pos++;
                if (P.peek() === '*') { P.pos++; steps.push({ t: 'wild' }); }
                else steps.push({ t: 'child', key: P.parseName() });
            } else if (c === '[') {
                P.pos++; P.skip();
                var cc = P.peek();
                if (cc === "'" || cc === '"') { steps.push({ t: 'child', key: P.parseString() }); }
                else {
                    var n = P.parseIntTok();
                    if (n === null) P.err('bad index');
                    steps.push({ t: 'index', i: n });
                }
                P.skip(); P.expect(']');
            } else break;
        }
        return steps;
    }

    function parseBracket(P) {
        P.expect('['); P.skip();
        var c = P.peek();
        if (c === '?') {
            P.pos++; P.expect('(');
            var f = parseFilterExpr(P);
            P.skip(); P.expect(')'); P.skip(); P.expect(']');
            return { t: 'filter', f: f };
        }
        if (c === '*') { P.pos++; P.skip(); P.expect(']'); return { t: 'wild' }; }
        if (c === "'" || c === '"') {
            var items = [];
            for (;;) {
                items.push({ t: 'child', key: P.parseString() });
                P.skip();
                if (P.peek() === ',') { P.pos++; continue; }
                break;
            }
            P.expect(']');
            return items.length === 1 ? items[0] : { t: 'union', items: items };
        }
        var n1 = P.parseIntTok(); P.skip();
        if (P.peek() === ':') {
            P.pos++;
            var n2 = P.parseIntTok(); P.skip();
            var n3 = null;
            if (P.peek() === ':') { P.pos++; n3 = P.parseIntTok(); P.skip(); }
            P.expect(']');
            return { t: 'slice', s: n1, e: n2, st: n3 };
        }
        if (n1 === null) P.err('bad bracket expression');
        var idxItems = [{ t: 'index', i: n1 }];
        while (P.peek() === ',') {
            P.pos++;
            var nn = P.parseIntTok();
            if (nn === null) P.err('union can only contain indexes or strings');
            idxItems.push({ t: 'index', i: nn });
            P.skip();
        }
        P.expect(']');
        return idxItems.length === 1 ? idxItems[0] : { t: 'union', items: idxItems };
    }

    function parsePath(src) {
        var P = new JParser(src);
        P.skip();
        if (P.peek() !== '$') P.err('Expression must start with $');
        P.pos++;
        var steps = [];
        for (;;) {
            P.skip();
            var c = P.peek();
            if (c === '') break;
            if (c === '.') {
                if (P.src.charAt(P.pos + 1) === '.') { P.pos += 2; steps.push({ t: 'rec', s: parseShorthand(P) }); }
                else { P.pos++; steps.push(parseShorthand(P)); }
            } else if (c === '[') {
                steps.push(parseBracket(P));
            } else { P.err('unexpected character "' + c + '"'); }
        }
        return steps;
    }

    /* ---------- evaluation ---------- */
    function childPath(path, key, isArr) {
        if (isArr) return path + '[' + key + ']';
        return /^[A-Za-z_$][\w$]*$/.test(key) ? path + '.' + key : path + "['" + key + "']";
    }
    function eachChild(node, fn) {
        if (Array.isArray(node)) { for (var i = 0; i < node.length; i++) fn(i, node[i], true); }
        else if (node !== null && typeof node === 'object') {
            for (var k in node) { if (Object.prototype.hasOwnProperty.call(node, k)) fn(k, node[k], false); }
        }
    }
    function collectDesc(item, out) {
        eachChild(item.node, function (k, v, isArr) {
            var d = { node: v, path: childPath(item.path, k, isArr) };
            out.push(d);
            collectDesc(d, out);
        });
    }
    function sliceIdx(len, s) {
        var step = (s.st === null || s.st === undefined) ? 1 : s.st;
        if (step === 0) throw new Error('slice step cannot be 0');
        function norm(v, def) {
            if (v === null || v === undefined) return def;
            if (v < 0) v = len + v;
            if (step > 0) return Math.min(len, Math.max(0, v));
            return Math.min(len - 1, Math.max(-1, v));
        }
        var start = norm(s.s, step > 0 ? 0 : len - 1);
        var end = norm(s.e, step > 0 ? len : -1);
        var idx = [];
        if (step > 0) { for (var i = start; i < end; i += step) idx.push(i); }
        else { for (var j = start; j > end; j += step) idx.push(j); }
        return idx;
    }
    function getPathVal(node, steps) {
        var cur = node;
        for (var i = 0; i < steps.length; i++) {
            var s = steps[i];
            if (cur === null || cur === undefined) return undefined;
            if (s.t === 'child') {
                if (typeof cur !== 'object' || Array.isArray(cur)) return undefined;
                cur = cur[s.key];
            } else if (s.t === 'index') {
                if (!Array.isArray(cur)) return undefined;
                var ix = s.i < 0 ? cur.length + s.i : s.i;
                cur = cur[ix];
            } else return undefined;
        }
        return cur;
    }
    function cmpVal(a, op, b) {
        if (op === '==') return a == b;
        if (op === '!=') return a != b;
        if (op === '<') return a < b;
        if (op === '<=') return a <= b;
        if (op === '>') return a > b;
        return a >= b;
    }
    function evalFilter(f, node) {
        if (f.t === 'or') return evalFilter(f.l, node) || evalFilter(f.r, node);
        if (f.t === 'and') return evalFilter(f.l, node) && evalFilter(f.r, node);
        if (f.t === 'not') return !evalFilter(f.x, node);
        if (f.t === 'exists') {
            var v = f.x.t === 'path' ? getPathVal(node, f.x.steps) : f.x.v;
            return f.x.t === 'path' ? v !== undefined : !!v;
        }
        if (f.t === 'cmp') {
            var a = f.l.t === 'path' ? getPathVal(node, f.l.steps) : f.l.v;
            var b = f.r.t === 'path' ? getPathVal(node, f.r.steps) : f.r.v;
            return cmpVal(a, f.op, b);
        }
        return false;
    }
    function applyStep(step, items) {
        var out = [];
        function push(n, p) { out.push({ node: n, path: p }); }
        for (var m = 0; m < items.length; m++) {
            var it = items[m], node = it.node, path = it.path;
            if (step.t === 'child') {
                if (node !== null && typeof node === 'object' && !Array.isArray(node) &&
                    Object.prototype.hasOwnProperty.call(node, step.key)) {
                    push(node[step.key], childPath(path, step.key, false));
                }
            } else if (step.t === 'index') {
                if (Array.isArray(node)) {
                    var ix = step.i < 0 ? node.length + step.i : step.i;
                    if (ix >= 0 && ix < node.length) push(node[ix], path + '[' + ix + ']');
                }
            } else if (step.t === 'wild') {
                eachChild(node, function (k, v, isArr) { push(v, childPath(path, k, isArr)); });
            } else if (step.t === 'slice') {
                if (Array.isArray(node)) {
                    var idx = sliceIdx(node.length, step);
                    for (var q = 0; q < idx.length; q++) push(node[idx[q]], path + '[' + idx[q] + ']');
                }
            } else if (step.t === 'union') {
                for (var u = 0; u < step.items.length; u++) {
                    var r = applyStep(step.items[u], [it]);
                    for (var w = 0; w < r.length; w++) out.push(r[w]);
                }
            } else if (step.t === 'filter') {
                eachChild(node, function (k, v, isArr) {
                    if (evalFilter(step.f, v)) push(v, childPath(path, k, isArr));
                });
            } else if (step.t === 'rec') {
                var all = [it];
                collectDesc(it, all);
                for (var d = 0; d < all.length; d++) {
                    var rr = applyStep(step.s, [all[d]]);
                    for (var z = 0; z < rr.length; z++) out.push(rr[z]);
                }
            }
        }
        return out;
    }
    function jsonPath(expr, data) {
        var steps = parsePath(expr);
        var items = [{ node: data, path: '$' }];
        for (var i = 0; i < steps.length; i++) items = applyStep(steps[i], items);
        var seen = {}, res = [];
        for (var j = 0; j < items.length; j++) {
            if (!seen[items[j].path]) { seen[items[j].path] = true; res.push(items[j]); }
        }
        return res;
    }

    /* ---------- UI ---------- */
    var SAMPLE = {
        store: {
            book: [
                { category: 'reference', author: 'Nigel Rees', title: 'Sayings of the Century', price: 8.95 },
                { category: 'fiction', author: 'Evelyn Waugh', title: 'Sword of Honour', price: 12.99 },
                { category: 'fiction', author: 'Herman Melville', title: 'Moby Dick', isbn: '0-553-21311-3', price: 8.99 },
                { category: 'fiction', author: 'J. R. R. Tolkien', title: 'The Lord of the Rings', isbn: '0-395-19395-8', price: 22.99 }
            ],
            bicycle: { color: 'red', price: 19.95 }
        },
        expensive: 10
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

    sampleJsonBtn.addEventListener('click', function () {
        hideError();
        jsonInput.value = JSON.stringify(SAMPLE, null, 2);
    });

    exBtns.addEventListener('click', function (e) {
        var b = e.target;
        if (b && b.getAttribute && b.getAttribute('data-expr')) {
            exprInput.value = b.getAttribute('data-expr');
            testBtn.click();
        }
    });

    testBtn.addEventListener('click', function () {
        hideError();
        var raw = jsonInput.value.trim();
        if (!raw) { showError('Please paste JSON data first.'); return; }
        var data;
        try { data = JSON.parse(raw); }
        catch (e) { showError('JSON error: ' + e.message); return; }
        var expr = exprInput.value.trim();
        if (!expr) { showError('Please write a JSONPath expression.'); return; }
        var matches;
        try { matches = jsonPath(expr, data); }
        catch (e) { showError('Expression error: ' + e.message); return; }
        resultCount.textContent = matches.length;
        if (!matches.length) {
            resultList.innerHTML = '<p class="text-muted">No matches found — check the expression or the data.</p>';
        } else {
            var h = '';
            for (var i = 0; i < matches.length; i++) {
                var val = JSON.stringify(matches[i].node, null, 2);
                if (val === undefined) val = 'undefined';
                if (val.length > 800) val = val.slice(0, 800) + '\n… (truncated)';
                h += '<div class="card mb-2"><div class="card-body py-2">' +
                    '<div class="font-monospace small text-primary mb-1">' + esc(matches[i].path) + '</div>' +
                    '<pre class="mb-0 small bg-light p-2 rounded" style="white-space: pre-wrap;">' + esc(val) + '</pre>' +
                    '</div></div>';
            }
            resultList.innerHTML = h;
        }
        results.classList.remove('d-none');
    });

    if (!jsonInput.value.trim()) jsonInput.value = JSON.stringify(SAMPLE, null, 2);
})();
</script>
@endsection
