@extends('layouts.app')

@section('title', 'Remove Duplicate Lines Online Free | Azlaan Tools')
@section('meta_description', 'Free tool to remove duplicate lines from text instantly while keeping the original order. Options for case, spaces and blank lines. No signup, works in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Remove Duplicate Lines</h1>
    <p class="lead">Paste any list or text and remove repeated lines in one click - the first occurrence and original order are kept. Free, with no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label for="inputText" class="form-label fw-semibold">Input Text</label>
            <textarea id="inputText" class="form-control" rows="9" placeholder="Paste your text or list here, one item per line..."></textarea>
            <div class="small text-muted mt-2">Input lines: <strong id="inputLines">0</strong></div>

            <div class="d-flex flex-wrap gap-4 mt-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="caseCheck">
                    <label class="form-check-label" for="caseCheck">Case-sensitive (Apple and apple are different)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="trimCheck" checked>
                    <label class="form-check-label" for="trimCheck">Trim spaces before comparing</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="blankCheck">
                    <label class="form-check-label" for="blankCheck">Keep blank lines (keep only first blank line)</label>
                </div>
            </div>

            <div class="mt-3">
                <button type="button" id="processBtn" class="btn btn-primary btn-lg">Remove Duplicates</button>
            </div>

            <label for="outputText" class="form-label fw-semibold mt-4">Output (Unique Lines)</label>
            <textarea id="outputText" class="form-control" rows="9" placeholder="Unique lines will appear here..." readonly></textarea>
            <div class="d-flex flex-wrap gap-3 mt-2 small text-muted">
                <span>Lines before: <strong id="beforeCount">0</strong></span>
                <span>Lines after: <strong id="afterCount">0</strong></span>
                <span>Duplicates removed: <strong id="removedCount">0</strong></span>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="copyBtn" class="btn btn-success">Copy Output</button>
                <button type="button" id="downloadBtn" class="btn btn-outline-success">Download .txt</button>
                <button type="button" id="clearBtn" class="btn btn-outline-danger">Clear</button>
            </div>
        </div>
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Paste your text or list into the input box, one item per line.</li>
        <li>Choose options: case-sensitive comparison, trimming spaces, and whether blank lines are kept.</li>
        <li>Click <strong>Remove Duplicates</strong> - repeated lines are removed and counts are shown below the output.</li>
        <li>Click <strong>Copy Output</strong> or <strong>Download .txt</strong> to save the cleaned list.</li>
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

    inputText.addEventListener('input', updateInputCount);

    document.getElementById('processBtn').addEventListener('click', function () {
        hideAlerts();
        if (!inputText.value) {
            showError('Please paste some text first.');
            outputText.value = '';
            document.getElementById('beforeCount').textContent = '0';
            document.getElementById('afterCount').textContent = '0';
            document.getElementById('removedCount').textContent = '0';
            return;
        }
        var caseSensitive = document.getElementById('caseCheck').checked;
        var trimSpaces = document.getElementById('trimCheck').checked;
        var keepBlank = document.getElementById('blankCheck').checked;
        var lines = inputText.value.split('\n');
        var seen = new Set();
        var out = [];
        var blankSeen = false;
        lines.forEach(function (line) {
            var isBlank = line.trim() === '';
            if (isBlank && !keepBlank) {
                return;
            }
            if (isBlank && keepBlank) {
                if (blankSeen) return;
                blankSeen = true;
                out.push('');
                return;
            }
            var compareSource = trimSpaces ? line.trim() : line;
            var key = caseSensitive ? compareSource : compareSource.toLowerCase();
            if (seen.has(key)) return;
            seen.add(key);
            out.push(trimSpaces ? line.trim() : line);
        } );
        outputText.value = out.join('\n');
        var removed = lines.length - out.length;
        document.getElementById('beforeCount').textContent = lines.length;
        document.getElementById('afterCount').textContent = out.length;
        document.getElementById('removedCount').textContent = removed;
        showSuccess('Done! ' + removed + ' line(s) removed, ' + out.length + ' unique line(s) remain.');
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
        a.download = 'unique-lines.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 2000);
    } );

    document.getElementById('clearBtn').addEventListener('click', function () {
        inputText.value = '';
        outputText.value = '';
        document.getElementById('beforeCount').textContent = '0';
        document.getElementById('afterCount').textContent = '0';
        document.getElementById('removedCount').textContent = '0';
        hideAlerts();
        updateInputCount();
    } );

    updateInputCount();
} )();
</script>
@endsection
