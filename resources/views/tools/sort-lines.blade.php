@extends('layouts.app')

@section('title', 'Sort Lines Online Free - Alphabetical, Length and Numeric | Azlaan Tools')
@section('meta_description', 'Free Sort Lines tool: sort text lines alphabetically A to Z or Z to A, by length or numerically, with options for duplicates and blank lines. No signup required.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Sort Lines</h1>
    <p class="lead">Sort any list or text lines instantly - alphabetically, in reverse, by length or by number. Free, private and with no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label for="inputText" class="form-label fw-semibold">Input Text</label>
            <textarea id="inputText" class="form-control" rows="9" placeholder="Paste your list here, one item per line..."></textarea>
            <div class="small text-muted mt-2">Input lines: <strong id="inputLines">0</strong></div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="sortMode" class="form-label fw-semibold">Sort Order</label>
                    <select id="sortMode" class="form-select">
                        <option value="az" selected>Alphabetical A to Z</option>
                        <option value="za">Alphabetical Z to A</option>
                        <option value="lengthAsc">By length (short to long)</option>
                        <option value="lengthDesc">By length (long to short)</option>
                        <option value="numericAsc">Numeric (small to large)</option>
                        <option value="numericDesc">Numeric (large to small)</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-end gap-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ignoreCaseCheck" checked>
                        <label class="form-check-label" for="ignoreCaseCheck">Case-insensitive sorting</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="removeBlankCheck" checked>
                        <label class="form-check-label" for="removeBlankCheck">Remove blank lines</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="removeDupCheck">
                        <label class="form-check-label" for="removeDupCheck">Remove duplicates</label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="button" id="sortBtn" class="btn btn-primary btn-lg">Sort Lines</button>
            </div>

            <label for="outputText" class="form-label fw-semibold mt-4">Sorted Output</label>
            <textarea id="outputText" class="form-control" rows="9" placeholder="Sorted lines will appear here..." readonly></textarea>
            <div class="d-flex flex-wrap gap-3 mt-2 small text-muted">
                <span>Lines before: <strong id="beforeCount">0</strong></span>
                <span>Lines after: <strong id="afterCount">0</strong></span>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="copyBtn" class="btn btn-success">Copy Output</button>
                <button type="button" id="downloadBtn" class="btn btn-outline-success">Download .txt</button>
                <button type="button" id="useOutputBtn" class="btn btn-outline-primary">Use Output as Input</button>
                <button type="button" id="clearBtn" class="btn btn-outline-danger">Clear</button>
            </div>
        </div>
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Paste your list into the input box, one item per line.</li>
        <li>Choose a sort order: A to Z, Z to A, by length, or numeric sorting.</li>
        <li>Tick the extra options you need - case-insensitive, remove blank lines, or remove duplicates.</li>
        <li>Click <strong>Sort Lines</strong>, then copy or download the sorted output.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var inputText = document.getElementById('inputText');
    var outputText = document.getElementById('outputText');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
    }
    function hideAlerts() {
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }
    function updateInputCount() {
        var t = inputText.value;
        document.getElementById('inputLines').textContent = t ? t.split('\n').length : 0;
    }
    function parseNumber(line) {
        var m = String(line).trim().match(/-?\d+(\.\d+)?/);
        return m ? parseFloat(m[0]) : NaN;
    }

    inputText.addEventListener('input', updateInputCount);

    document.getElementById('sortBtn').addEventListener('click', function () {
        hideAlerts();
        if (!inputText.value) {
            showError('Please paste some text first.');
            return;
        }
        var mode = document.getElementById('sortMode').value;
        var ignoreCase = document.getElementById('ignoreCaseCheck').checked;
        var removeBlank = document.getElementById('removeBlankCheck').checked;
        var removeDup = document.getElementById('removeDupCheck').checked;
        var rawLines = inputText.value.split('\n');
        var before = rawLines.length;
        var lines = rawLines.slice();

        if (removeBlank) {
            lines = lines.filter(function (l) {
                return l.trim() !== '';
            } );
        }
        if (removeDup) {
            var seen = new Set();
            lines = lines.filter(function (l) {
                var key = ignoreCase ? l.toLowerCase() : l;
                if (seen.has(key)) return false;
                seen.add(key);
                return true;
            } );
        }

        lines.sort(function (a, b) {
            if (mode === 'az' || mode === 'za') {
                var aa = ignoreCase ? a.toLowerCase() : a;
                var bb = ignoreCase ? b.toLowerCase() : b;
                var cmp = aa.localeCompare(bb);
                return mode === 'az' ? cmp : -cmp;
            }
            if (mode === 'lengthAsc') return a.length - b.length;
            if (mode === 'lengthDesc') return b.length - a.length;
            if (mode === 'numericAsc' || mode === 'numericDesc') {
                var na = parseNumber(a);
                var nb = parseNumber(b);
                var va = isNaN(na) ? 0 : na;
                var vb = isNaN(nb) ? 0 : nb;
                return mode === 'numericAsc' ? va - vb : vb - va;
            }
            return 0;
        } );

        outputText.value = lines.join('\n');
        document.getElementById('beforeCount').textContent = before;
        document.getElementById('afterCount').textContent = lines.length;
        showSuccess('Done! Sorted ' + lines.length + ' line(s).');
    } );

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!outputText.value) {
            showError('There is no output to copy yet.');
            return;
        }
        try {
            await navigator.clipboard.writeText(outputText.value);
            showSuccess('Copied to clipboard!');
        } catch (e) {
            outputText.removeAttribute('readonly');
            outputText.select();
            document.execCommand('copy');
            outputText.setAttribute('readonly', 'readonly');
            showSuccess('Copied to clipboard!');
        }
    } );

    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!outputText.value) {
            showError('There is no output to download yet.');
            return;
        }
        var blob = new Blob([outputText.value], {
            type: 'text/plain;charset=utf-8'
        } );
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'sorted-lines.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 2000);
    } );

    document.getElementById('useOutputBtn').addEventListener('click', function () {
        if (!outputText.value) return;
        inputText.value = outputText.value;
        updateInputCount();
        inputText.focus();
    } );

    document.getElementById('clearBtn').addEventListener('click', function () {
        inputText.value = '';
        outputText.value = '';
        document.getElementById('beforeCount').textContent = '0';
        document.getElementById('afterCount').textContent = '0';
        hideAlerts();
        updateInputCount();
    } );

    updateInputCount();
} )();
</script>
@endsection
