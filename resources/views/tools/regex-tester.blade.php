@extends('layouts.app')

@section('title', 'Regex Tester Online Free - Test Regular Expressions | Azlaan Tools')
@section('meta_description', 'Free online regex tester: test regular expressions live with highlighted matches, capture groups, match count and replace preview. Runs 100% in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Regex Tester</h1>
            <p class="lead text-muted">Test regular expressions live — highlighted matches, capture groups and a replace preview. This tool runs 100% in your browser — nothing is uploaded or saved.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" for="patternInput">Pattern</label>
                            <div class="input-group"><span class="input-group-text font-monospace">/</span><input id="patternInput" class="form-control font-monospace" value="\b\w+&#64;\w+\.\w+\b" placeholder="e.g. \d+"><span class="input-group-text font-monospace">/</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Flags</label>
                            <div class="d-flex gap-3 pt-2">
                                <div class="form-check"><input class="form-check-input flag" type="checkbox" id="flagG" checked><label class="form-check-label" for="flagG">g</label></div>
                                <div class="form-check"><input class="form-check-input flag" type="checkbox" id="flagI"><label class="form-check-label" for="flagI">i</label></div>
                                <div class="form-check"><input class="form-check-input flag" type="checkbox" id="flagM"><label class="form-check-label" for="flagM">m</label></div>
                                <div class="form-check"><input class="form-check-input flag" type="checkbox" id="flagS"><label class="form-check-label" for="flagS">s</label></div>
                            </div>
                        </div>
                    </div>
                    <label class="form-label fw-semibold" for="testText">Test Text</label>
                    <textarea id="testText" class="form-control font-monospace" rows="6">Contact us at hello@example.com or support@azlaan.tools for help. Call 0300-1234567 today.</textarea>
                    <div id="regexError" class="alert alert-danger mt-3 d-none"></div>
                    <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                        <span class="badge bg-primary fs-6" id="matchCount">0 matches</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="sampleBtn">Load Sample</button>
                    </div>
                    <label class="form-label fw-semibold mt-3">Highlighted Result</label>
                    <div id="highlighted" class="border rounded p-3 font-monospace text-break" style="white-space:pre-wrap;min-height:80px;"></div>
                    <label class="form-label fw-semibold mt-3">Matches &amp; Capture Groups</label>
                    <div id="groupsList" class="list-group small font-monospace"></div>
                    <div class="row g-2 mt-3">
                        <div class="col-md-4"><label class="form-label fw-semibold" for="replaceInput">Replace With</label><input id="replaceInput" class="form-control font-monospace" placeholder="Use $1, $2 for groups"></div>
                        <div class="col-md-8"><label class="form-label fw-semibold" for="replaceOutput">Replace Preview</label><textarea id="replaceOutput" class="form-control font-monospace" rows="3" readonly></textarea></div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm mt-2" id="copyReplaceBtn">Copy Replace Result</button>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Type your regular expression pattern and tick the flags you need (g, i, m, s).</li>
                <li>Paste or type test text — matches highlight instantly and the count updates live.</li>
                <li>Check the list below for each match and its capture groups.</li>
                <li>Optionally type a replacement (use $1, $2 for groups) to preview the replaced text, then copy it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var patternEl = document.getElementById('patternInput');
    var textEl = document.getElementById('testText');
    var errEl = document.getElementById('regexError');
    var hlEl = document.getElementById('highlighted');
    var groupsEl = document.getElementById('groupsList');
    var countEl = document.getElementById('matchCount');
    var replaceEl = document.getElementById('replaceInput');
    var replaceOut = document.getElementById('replaceOutput');
    function esc(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    function flags() { var f = ''; if (document.getElementById('flagG').checked) f += 'g'; if (document.getElementById('flagI').checked) f += 'i'; if (document.getElementById('flagM').checked) f += 'm'; if (document.getElementById('flagS').checked) f += 's'; return f; }
    function run() {
        errEl.classList.add('d-none'); groupsEl.innerHTML = '';
        var pat = patternEl.value; var text = textEl.value; var fl = flags();
        if (!pat) { hlEl.textContent = text; countEl.textContent = '0 matches'; replaceOut.value = text; return; }
        var re;
        try { re = new RegExp(pat, fl); } catch (e) { errEl.textContent = 'Invalid regular expression: ' + e.message; errEl.classList.remove('d-none'); countEl.textContent = '0 matches'; return; }
        var matches = []; var m; var guard = 0;
        var globalRe = new RegExp(pat, fl.indexOf('g') >= 0 ? fl : fl + 'g');
        while ((m = globalRe.exec(text)) !== null && guard < 500) { matches.push(m); guard++; if (m[0] === '') { globalRe.lastIndex++; } }
        countEl.textContent = matches.length + (matches.length === 1 ? ' match' : ' matches');
        var html = ''; var last = 0;
        matches.forEach(function (mm, idx) {
            html += esc(text.slice(last, mm.index)) + '<mark>' + esc(mm[0]) + '</mark>'; last = mm.index + mm[0].length;
            var item = document.createElement('div'); item.className = 'list-group-item';
            var groups = []; for (var g = 1; g < mm.length; g++) groups.push('Group ' + g + ': ' + (mm[g] === undefined ? '(none)' : mm[g]));
            item.textContent = '#' + (idx + 1) + ' at index ' + mm.index + ' — "' + mm[0] + '"' + (groups.length ? ' | ' + groups.join(' | ') : '');
            groupsEl.appendChild(item);
        });
        html += esc(text.slice(last)); hlEl.innerHTML = html || esc(text);
        try { replaceOut.value = text.replace(new RegExp(pat, fl.indexOf('g') >= 0 ? fl : fl), replaceEl.value); } catch (e2) { replaceOut.value = ''; }
    }
    [patternEl, textEl, replaceEl].forEach(function (el) { el.addEventListener('input', run); });
    document.querySelectorAll('.flag').forEach(function (el) { el.addEventListener('change', run); });
    document.getElementById('sampleBtn').addEventListener('click', function () { patternEl.value = '\\d{4}-\\d{7}'; textEl.value = 'Call 0300-1234567 or 0314-7654321 today.'; run(); });
    document.getElementById('copyReplaceBtn').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(replaceOut.value); });
    run();
})();
</script>
@endsection
