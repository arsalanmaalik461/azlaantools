@extends('layouts.app')

@section('title', 'JSON to TypeScript Converter - Azlaan Tools')
@section('meta_description', 'Free JSON to TypeScript converter: paste JSON and generate clean TypeScript interfaces with nested types, arrays and optional fields.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">JSON to TypeScript Converter</h1>
            <p class="lead text-muted">Paste JSON — TypeScript interfaces are created automatically. Nested objects, arrays, and optional fields are all handled.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="jsonInput" class="form-label fw-semibold">JSON input</label>
                        <textarea class="form-control font-monospace" id="jsonInput" rows="10" placeholder='{"name": "Ali", "age": 25, "tags": ["a", "b"]}' dir="ltr" style="font-size:13px;"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="rootName" class="form-label fw-semibold">Root interface name</label>
                            <input type="text" class="form-control" id="rootName" value="RootObject" dir="ltr">
                        </div>
                        <div class="col-md-3">
                            <label for="indentSel" class="form-label fw-semibold">Indent</label>
                            <select class="form-select" id="indentSel">
                                <option value="2">2 spaces</option>
                                <option value="4">4 spaces</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-3 flex-wrap pb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="optExport" checked>
                                <label class="form-check-label" for="optExport">export keyword</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="optReadonly">
                                <label class="form-check-label" for="optReadonly">readonly</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="optSemi" checked>
                                <label class="form-check-label" for="optSemi">semicolons</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="optOptional" checked>
                                <label class="form-check-label" for="optOptional">detect optional (?) fields</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Sample JSON</button>
                        <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Convert to TypeScript</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Generated TypeScript <span class="badge bg-success" id="ifaceCount"></span></h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="copyBtn">Copy</button>
                                <button type="button" class="btn btn-sm btn-outline-success" id="dlBtn">Download .ts</button>
                            </div>
                        </div>
                        <pre class="border rounded p-3 bg-light" style="font-size:13px;max-height:480px;overflow:auto;" dir="ltr"><code id="tsOutput"></code></pre>
                        <div class="small text-muted" id="copyMsg"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste your JSON in the box above.</li>
                <li>Set the options (interface name, export, optional fields).</li>
                <li>Click "Convert to TypeScript" — then copy the code or download the .ts file.</li>
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
    var rootName = document.getElementById('rootName');
    var indentSel = document.getElementById('indentSel');
    var optExport = document.getElementById('optExport');
    var optReadonly = document.getElementById('optReadonly');
    var optSemi = document.getElementById('optSemi');
    var optOptional = document.getElementById('optOptional');
    var sampleBtn = document.getElementById('sampleBtn');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var tsOutput = document.getElementById('tsOutput');
    var ifaceCount = document.getElementById('ifaceCount');
    var copyMsg = document.getElementById('copyMsg');

    var interfaces = [];
    var usedNames = {};

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function cap(s) {
        s = String(s).replace(/[^a-zA-Z0-9_]/g, '_');
        return s.charAt(0).toUpperCase() + s.slice(1);
    }
    function uniqueName(base) {
        base = cap(base) || 'Item';
        var n = base, i = 2;
        while (usedNames[n]) { n = base + i; i++; }
        usedNames[n] = true;
        return n;
    }
    function safeKey(k) {
        return /^[A-Za-z_$][A-Za-z0-9_$]*$/.test(k) ? k : JSON.stringify(k);
    }
    function primType(v) {
        if (v === null) return 'any';
        var t = typeof v;
        if (t === 'string') return 'string';
        if (t === 'number') return 'number';
        if (t === 'boolean') return 'boolean';
        return 'any';
    }
    // Returns TS type string for value v; registers interfaces as needed.
    function typeOf(v, keyHint, samples) {
        if (v === null) return 'any';
        if (Array.isArray(v)) {
            if (!v.length) return 'any[]';
            var elemTypes = {};
            var objSamples = [];
            v.forEach(function (el) {
                if (el !== null && typeof el === 'object' && !Array.isArray(el)) objSamples.push(el);
                else elemTypes[typeOf(el, keyHint + 'Item', null)] = true;
            });
            var parts = Object.keys(elemTypes);
            if (objSamples.length) {
                var merged = mergeObjects(objSamples);
                var iname = uniqueName(keyHint || 'Item');
                emitInterface(iname, merged.fields, merged.optional);
                parts.push(iname + (v.some(function (el) { return Array.isArray(el); }) ? '' : ''));
            }
            if (!parts.length) return 'any[]';
            var u = parts.join(' | ');
            return parts.length > 1 ? '(' + u + ')[]' : parts[0] + '[]';
        }
        if (typeof v === 'object') {
            var iname2 = uniqueName(keyHint || 'Object');
            var m2 = mergeObjects([v]);
            emitInterface(iname2, m2.fields, m2.optional);
            return iname2;
        }
        return primType(v);
    }
    // Merge array of objects -> {fields: {key: [values]}, optional: {key: true}}
    function mergeObjects(objs) {
        var fields = {}, counts = {};
        objs.forEach(function (o) {
            Object.keys(o).forEach(function (k) {
                if (!fields[k]) { fields[k] = []; counts[k] = 0; }
                fields[k].push(o[k]);
                counts[k]++;
            });
        });
        var optional = {};
        Object.keys(fields).forEach(function (k) {
            if (counts[k] < objs.length) optional[k] = true;
        });
        return { fields: fields, optional: optional };
    }
    function emitInterface(name, fields, optional) {
        var lines = [];
        Object.keys(fields).forEach(function (k) {
            var vals = fields[k];
            var tset = {};
            vals.forEach(function (v) {
                tset[typeOf(v, k, null)] = true;
            });
            var t = Object.keys(tset).join(' | ');
            var isOpt = optOptional.checked && (optional[k] || vals.some(function (v) { return v === null || v === undefined; }));
            lines.push({ key: k, type: t || 'any', opt: !!isOpt });
        });
        interfaces.push({ name: name, lines: lines });
    }

    sampleBtn.addEventListener('click', function () {
        jsonInput.value = JSON.stringify({
            id: 101, name: 'Ali Raza', active: true, balance: 2500.5,
            tags: ['solar', 'tools'],
            address: { city: 'Faisalabad', zip: '38000' },
            orders: [
                { orderId: 'A1', total: 500, note: null },
                { orderId: 'A2', total: 750 }
            ],
            meta: null
        }, null, 2);
    });

    goBtn.addEventListener('click', function () {
        hideError();
        copyMsg.textContent = '';
        var raw = jsonInput.value.trim();
        if (!raw) { showError('Please paste some JSON first.'); return; }
        var data;
        try { data = JSON.parse(raw); }
        catch (e) { showError('JSON error: ' + e.message); return; }
        interfaces = [];
        usedNames = {};
        var rn = (rootName.value.trim() || 'RootObject').replace(/[^a-zA-Z0-9_]/g, '');
        if (!/^[A-Za-z_]/.test(rn)) rn = 'RootObject';
        var rootType;
        if (Array.isArray(data)) {
            rootType = typeOf(data, rn.replace(/s$/, '') || 'Item', null);
            if (rootType.slice(-2) !== '[]' && interfaces.length === 0) rootType = rootType + '[]';
        } else if (data !== null && typeof data === 'object') {
            var m = mergeObjects([data]);
            var iname = uniqueName(rn);
            emitInterface(iname, m.fields, {});
            rootType = iname;
        } else {
            showError('The top-level JSON must be an object or array.');
            return;
        }
        var ind = indentSel.value === '4' ? '    ' : '  ';
        var exp = optExport.checked ? 'export ' : '';
        var ro = optReadonly.checked ? 'readonly ' : '';
        var semi = optSemi.checked ? ';' : '';
        var out = [];
        interfaces.forEach(function (iface) {
            out.push(exp + 'interface ' + iface.name + ' {');
            iface.lines.forEach(function (ln) {
                out.push(ind + ro + safeKey(ln.key) + (ln.opt ? '?' : '') + ': ' + ln.type + semi);
            });
            out.push('}');
            out.push('');
        });
        if (!Array.isArray(data)) out.push(exp + 'type ' + rn + ' = ' + rootType + semi);
        tsOutput.textContent = out.join('\n').trim() + '\n';
        ifaceCount.textContent = interfaces.length + ' interfaces';
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        var t = tsOutput.textContent;
        function done() { copyMsg.textContent = 'Copied!'; }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(done, function () { fallback(); });
        } else fallback();
        function fallback() {
            var ta = document.createElement('textarea');
            ta.value = t;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { copyMsg.textContent = 'Could not copy — please select it manually.'; }
            document.body.removeChild(ta);
        }
    });
    dlBtn.addEventListener('click', function () {
        var blob = new Blob([tsOutput.textContent], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = (rootName.value.trim() || 'types') + '.ts';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); }, 500);
    });
})();
</script>
@endsection
