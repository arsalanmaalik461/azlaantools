@extends('layouts.app')

@section('title', 'Reverse Text Online Free - Reverse Characters, Words and Lines | Azlaan Tools')
@section('meta_description', 'Free Reverse Text tool: reverse characters, reverse word order, reverse each line or flip letters in every word. Instant results, no signup, works in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Reverse Text</h1>
    <p class="lead">Flip your text in four different ways - reverse all characters, reverse the word order, reverse each line, or reverse the letters inside every word. Free and instant.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label for="inputText" class="form-label fw-semibold">Your Text</label>
            <textarea id="inputText" class="form-control" rows="7" placeholder="Type or paste your text here..."></textarea>
            <div class="d-flex flex-wrap gap-3 mt-2 small text-muted">
                <span>Characters: <strong id="inputChars">0</strong></span>
                <span>Words: <strong id="inputWords">0</strong></span>
                <span>Lines: <strong id="inputLines">0</strong></span>
            </div>

            <label for="modeSelect" class="form-label fw-semibold mt-4">Reverse Mode</label>
            <select id="modeSelect" class="form-select">
                <option value="chars" selected>Reverse characters (whole text backwards)</option>
                <option value="wordOrder">Reverse words order</option>
                <option value="eachLine">Reverse each line</option>
                <option value="wordLetters">Reverse word letters (each word flipped, order kept)</option>
            </select>

            <div class="mt-3">
                <button type="button" id="reverseBtn" class="btn btn-primary btn-lg">Reverse Text</button>
            </div>

            <label for="outputText" class="form-label fw-semibold mt-4">Reversed Output</label>
            <textarea id="outputText" class="form-control" rows="7" placeholder="Reversed text will appear here as you type or when you click the button..." readonly></textarea>
            <div class="d-flex flex-wrap gap-3 mt-2 small text-muted">
                <span>Characters: <strong id="outputChars">0</strong></span>
                <span>Words: <strong id="outputWords">0</strong></span>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="copyBtn" class="btn btn-success">Copy Output</button>
                <button type="button" id="downloadBtn" class="btn btn-outline-success">Download .txt</button>
                <button type="button" id="swapBtn" class="btn btn-outline-primary">Use Output as Input</button>
                <button type="button" id="clearBtn" class="btn btn-outline-danger">Clear</button>
            </div>
        </div>
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Type or paste your text in the input box - the output updates live as you type.</li>
        <li>Choose a mode: reverse characters, reverse words order, reverse each line, or reverse word letters.</li>
        <li>Click <strong>Reverse Text</strong> if you want to re-apply the selected mode manually.</li>
        <li>Click <strong>Copy Output</strong> or <strong>Download .txt</strong> to save the result.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var inputText = document.getElementById('inputText');
    var outputText = document.getElementById('outputText');
    var modeSelect = document.getElementById('modeSelect');
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
    function countWords(t) {
        return t.trim() ? t.trim().split(/\s+/).length : 0;
    }
    function reverseString(s) {
        return Array.from(s).reverse().join('');
    }
    function doReverse(text, mode) {
        if (mode === 'chars') {
            return reverseString(text);
        }
        if (mode === 'wordOrder') {
            return text.split('\n').map(function (line) {
                var trimmed = line.trim();
                if (!trimmed) return line;
                return trimmed.split(/\s+/).reverse().join(' ');
            } ).join('\n');
        }
        if (mode === 'eachLine') {
            return text.split('\n').map(function (line) {
                return reverseString(line);
            } ).join('\n');
        }
        if (mode === 'wordLetters') {
            return text.replace(/\S+/g, function (word) {
                return reverseString(word);
            } );
        }
        return text;
    }
    function update() {
        var t = inputText.value;
        document.getElementById('inputChars').textContent = t.length;
        document.getElementById('inputWords').textContent = countWords(t);
        document.getElementById('inputLines').textContent = t ? t.split('\n').length : 0;
        var out = t ? doReverse(t, modeSelect.value) : '';
        outputText.value = out;
        document.getElementById('outputChars').textContent = out.length;
        document.getElementById('outputWords').textContent = countWords(out);
    }

    inputText.addEventListener('input', update);
    modeSelect.addEventListener('change', update);

    document.getElementById('reverseBtn').addEventListener('click', function () {
        hideAlerts();
        if (!inputText.value) {
            showError('Please type some text first.');
            return;
        }
        update();
        showSuccess('Text reversed using the selected mode.');
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
        a.download = 'reversed-text.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 2000);
    } );

    document.getElementById('swapBtn').addEventListener('click', function () {
        if (!outputText.value) return;
        inputText.value = outputText.value;
        update();
        inputText.focus();
    } );

    document.getElementById('clearBtn').addEventListener('click', function () {
        inputText.value = '';
        hideAlerts();
        update();
        inputText.focus();
    } );

    update();
} )();
</script>
@endsection
