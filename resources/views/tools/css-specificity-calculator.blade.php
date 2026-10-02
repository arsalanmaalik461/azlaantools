@extends('layouts.app')

@section('title', 'CSS Specificity Calculator - Azlaan Tools')
@section('meta_description', 'Calculate the specificity score of any CSS selector instantly. Free online developer tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">CSS Specificity Calculator</h1>
            <p class="lead text-muted">Type any CSS selector — see its specificity score (a, b, c) instantly and find out which selector wins.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="selectorInput" class="form-label fw-semibold">CSS selectors (one per line, or separated by commas)</label>
                        <textarea class="form-control font-monospace" id="selectorInput" rows="4" placeholder="Example:&#10;#header .nav a:hover&#10;ul li.active::before&#10;div#main p.intro"></textarea>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn">Calculate</button>
                        <button type="button" class="btn btn-outline-secondary" id="exampleBtn">Fill examples</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr><th>Selector</th><th class="text-center">a (IDs)</th><th class="text-center">b (class/attr)</th><th class="text-center">c (elements)</th><th class="text-center">Score</th></tr>
                                </thead>
                                <tbody id="resultRows"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-success d-none" id="winnerBox"></div>
                        <h2 class="h6 mt-4">Understand specificity</h2>
                        <ul class="small text-muted">
                            <li><strong>a</strong> = ID selectors (<code>#header</code>) — the strongest.</li>
                            <li><strong>b</strong> = class (<code>.nav</code>), attributes (<code>[type=text]</code>), pseudo-classes (<code>:hover</code>).</li>
                            <li><strong>c</strong> = elements (<code>div</code>) and pseudo-elements (<code>::before</code>).</li>
                            <li><code>:where()</code> always has 0 specificity; <code>:not()</code>, <code>:is()</code> and <code>:has()</code> take the specificity of the strongest argument inside them.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type one or more CSS selectors (one per line).</li>
                <li>Press "Calculate" — each selector's (a, b, c) score will appear in the table.</li>
                <li>The selector with the highest score will be highlighted — that is the one that wins when two rules clash.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var selectorInput = document.getElementById('selectorInput');
    var goBtn = document.getElementById('goBtn');
    var exampleBtn = document.getElementById('exampleBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultRows = document.getElementById('resultRows');
    var winnerBox = document.getElementById('winnerBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function score(sp) { return sp.a * 10000 + sp.b * 100 + sp.c; }

    function splitTopLevel(str, delim) {
        var parts = [], depth = 0, cur = '';
        for (var i = 0; i < str.length; i++) {
            var ch = str[i];
            if (ch === '(' || ch === '[') { depth++; }
            if (ch === ')' || ch === ']') { depth--; }
            if (ch === delim && depth === 0) { parts.push(cur); cur = ''; }
            else { cur += ch; }
        }
        parts.push(cur);
        return parts;
    }

    function specificity(sel) {
        var a = 0, b = 0, c = 0;
        var s = sel;
        s = s.replace(/:where\(([^()]*)\)/gi, '');
        s = s.replace(/:(not|is|has|matches)\(([^()]*)\)/gi, function (m, fn, args) {
            var parts = splitTopLevel(args, ',');
            var best = { a: 0, b: 0, c: 0 };
            for (var i = 0; i < parts.length; i++) {
                var sp = specificity(parts[i].trim());
                if (score(sp) > score(best)) { best = sp; }
            }
            a += best.a; b += best.b; c += best.c;
            return '';
        });
        var m;
        m = s.match(/#[\w-]+/g); if (m) { a += m.length; }
        m = s.match(/\.[\w-]+/g); if (m) { b += m.length; }
        m = s.match(/\[[^\]]+\]/g); if (m) { b += m.length; }
        m = s.match(/::[\w-]+/g); if (m) { c += m.length; }
        s = s.replace(/::[\w-]+/g, ' ');
        m = s.match(/:(before|after|first-line|first-letter|selection|marker|placeholder|backdrop)\b/gi);
        if (m) { c += m.length; }
        s = s.replace(/:(before|after|first-line|first-letter|selection|marker|placeholder|backdrop)\b/gi, ' ');
        m = s.match(/:[\w-]+(\([^()]*\))?/g); if (m) { b += m.length; }
        var rest = s.replace(/#[\w-]+/g, ' ')
                     .replace(/\.[\w-]+/g, ' ')
                     .replace(/\[[^\]]+\]/g, ' ')
                     .replace(/:[\w-]+(\([^()]*\))?/g, ' ');
        var types = rest.match(/(^|[\s>+~])([a-zA-Z][\w-]*|\*)/g);
        if (types) {
            for (var j = 0; j < types.length; j++) {
                var name = types[j].replace(/^[\s>+~]+/, '').trim();
                if (name && name !== '*') { c++; }
            }
        }
        return { a: a, b: b, c: c };
    }

    function bar(sp) {
        var max = Math.max(sp.a, 3);
        function seg(n, color, label) {
            var w = Math.round((n / max) * 100);
            return '<div class="progress mb-1" style="height:8px" title="' + label + ': ' + n + '">' +
                   '<div class="progress-bar" style="width:' + w + '%;background-color:' + color + '"></div></div>';
        }
        return seg(sp.a, '#dc3545', 'IDs') + seg(sp.b, '#ffc107', 'classes') + seg(sp.c, '#0d6efd', 'elements');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var raw = selectorInput.value.trim();
        if (!raw) { showError('Please enter at least one selector.'); return; }
        var selectors = [];
        var lines = raw.split('\n');
        for (var i = 0; i < lines.length; i++) {
            var parts = splitTopLevel(lines[i], ',');
            for (var j = 0; j < parts.length; j++) {
                var p = parts[j].trim();
                if (p) { selectors.push(p); }
            }
        }
        if (!selectors.length) { showError('No valid selector found.'); return; }
        var rows = selectors.map(function (sel) {
            var sp = specificity(sel);
            return { sel: sel, sp: sp, sc: score(sp) };
        });
        rows.sort(function (x, y) { return y.sc - x.sc; });
        resultRows.innerHTML = '';
        rows.forEach(function (r, idx) {
            var tr = document.createElement('tr');
            if (idx === 0 && rows.length > 1) { tr.className = 'table-success'; }
            var tdSel = document.createElement('td');
            tdSel.innerHTML = '<code></code>' + (idx === 0 && rows.length > 1 ? ' <span class="badge bg-success">winner</span>' : '');
            tdSel.querySelector('code').textContent = r.sel;
            tr.appendChild(tdSel);
            [['a', r.sp.a], ['b', r.sp.b], ['c', r.sp.c]].forEach(function (pair) {
                var td = document.createElement('td');
                td.className = 'text-center';
                td.textContent = pair[1];
                tr.appendChild(td);
            });
            var tdScore = document.createElement('td');
            tdScore.className = 'text-center';
            tdScore.innerHTML = '<strong>' + r.sp.a + ',' + r.sp.b + ',' + r.sp.c + '</strong><div class="mt-1">' + bar(r.sp) + '</div>';
            tr.appendChild(tdScore);
            resultRows.appendChild(tr);
        });
        if (rows.length > 1) {
            winnerBox.textContent = 'Winner: ' + rows[0].sel + '  (' + rows[0].sp.a + ',' + rows[0].sp.b + ',' + rows[0].sp.c + ')';
            winnerBox.classList.remove('d-none');
        } else {
            winnerBox.classList.add('d-none');
        }
        results.classList.remove('d-none');
    });

    exampleBtn.addEventListener('click', function () {
        selectorInput.value = '#header .nav a:hover\nul li.active::before\ndiv#main p.intro\n*:not(.hidden)';
    });
})();
</script>
@endsection
