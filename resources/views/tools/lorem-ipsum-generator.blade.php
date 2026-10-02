@extends('layouts.app')

@section('title', 'Lorem Ipsum Generator Online Free | Azlaan Tools')
@section('meta_description', 'Free Lorem Ipsum generator: create placeholder paragraphs, sentences or words in seconds for designs and mockups. No signup, runs in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Lorem Ipsum Generator</h1>
    <p class="lead">Generate classic Lorem Ipsum placeholder text — paragraphs, sentences or words — for your designs, websites and mockups. Free, no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="genType" class="form-label fw-semibold">Generate</label>
                    <select class="form-select" id="genType">
                        <option value="paragraphs" selected>Paragraphs</option>
                        <option value="sentences">Sentences</option>
                        <option value="words">Words</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="genCount" class="form-label fw-semibold">Count</label>
                    <input type="number" class="form-control" id="genCount" value="3" min="1" max="100">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="startClassic" checked>
                        <label class="form-check-label" for="startClassic">Start with "Lorem ipsum dolor sit amet..."</label>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-primary" id="generateBtn">Generate</button>
                <button type="button" class="btn btn-success" id="copyBtn">Copy Text</button>
                <button type="button" class="btn btn-outline-secondary" id="downloadBtn">Download .txt</button>
            </div>

            <label for="outputText" class="form-label fw-semibold mt-3">Generated Text</label>
            <textarea class="form-control" id="outputText" rows="10" readonly placeholder="Click Generate to create Lorem Ipsum text..."></textarea>
            <div class="small text-muted mt-2">Characters: <strong id="charCount">0</strong> &nbsp;|&nbsp; Words: <strong id="wordCount">0</strong></div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Text is generated locally in your browser — nothing is uploaded anywhere.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Choose whether you want paragraphs, sentences or words.</li>
        <li>Enter how many you need (1 to 100).</li>
        <li>Tick the checkbox if the text should start with the classic "Lorem ipsum dolor sit amet..." opening.</li>
        <li>Click <strong>Generate</strong>, then copy the text or download it as a .txt file.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var genType = document.getElementById('genType');
    var genCount = document.getElementById('genCount');
    var startClassic = document.getElementById('startClassic');
    var output = document.getElementById('outputText');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');

    var WORDS = ('lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua enim ad minim veniam quis nostrud exercitation ullamco laboris nisi aliquip ex ea commodo consequat duis aute irure in reprehenderit voluptate velit esse cillum fugiat nulla pariatur excepteur sint occaecat cupidatat non proident sunt culpa qui officia deserunt mollit anim id est laborum perspiciatis unde omnis iste natus error voluptatem accusantium doloremque laudantium totam rem aperiam eaque ipsa quae ab illo inventore veritatis quasi architecto beatae vitae dicta explicabo nemo enim ipsam voluptatem quia voluptas aspernatur odit aut fugit consequuntur magni dolores eos ratione sequi nesciunt neque porro quisquam dolorem numquam eius modi tempora incidunt magnam quaerat sapiente delectus reiciendis voluptatibus maiores alias consequatur perferendis doloribus asperiores repellat maecenas faucibus mollis interdum nullam dictum felis eu pede ultricies integer mauris phasellus ullamcorper ipsum rutrum nunc nunc tellus metus bibendum laoreet vivamus elementum semper nisi aenean vulputate eleifend aenean leo ligula porttitor eu consequat vitae eleifend ac enim aliquam lorem ante dapibus in viverra feugiat a tellus donec sodales sagittis magna auctor neque elit proin gravida hendrerit lectus vestibulum quam nisl venenatis tristique fusce fermentum posuere consectetur morbi leo risus porta ac vestibulum at eros praesent blandit laoreet nibh praesent commodo cursus magna vel scelerisque nisl consectetur etiam porta sem malesuada magna mollis euismod donec ullamcorper nulla non metus auctor fringilla maecenas sed diam eget risus varius blandit sit amet cursus mattis consectetur purus sit amet fermentum').split(' ');
    var CLASSIC = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
        setTimeout(function () { successBox.classList.add('d-none'); }, 2500);
    }
    function hideAlerts() { alertBox.classList.add('d-none'); successBox.classList.add('d-none'); }

    // Deterministic-ish pseudo random based on Math.random is fine here
    function randWord() { return WORDS[Math.floor(Math.random() * WORDS.length)]; }
    function makeSentence(minWords, maxWords) {
        var n = minWords + Math.floor(Math.random() * (maxWords - minWords + 1));
        var parts = [];
        for (var i = 0; i < n; i++) parts.push(randWord());
        var s = parts.join(' ');
        if (n > 7 && Math.random() > 0.5) {
            var pos = 3 + Math.floor(Math.random() * (n - 5));
            parts[pos] = parts[pos] + ',';
            s = parts.join(' ');
        }
        return s.charAt(0).toUpperCase() + s.slice(1) + '.';
    }
    function makeParagraph() {
        var sentences = 3 + Math.floor(Math.random() * 4); // 3-6
        var out = [];
        for (var i = 0; i < sentences; i++) out.push(makeSentence(6, 16));
        return out.join(' ');
    }

    function generate() {
        hideAlerts();
        var count = parseInt(genCount.value, 10);
        if (isNaN(count) || count < 1) { showError('Please enter a count of at least 1.'); return; }
        if (count > 100) { count = 100; genCount.value = 100; }
        var type = genType.value;
        var text = '';

        if (type === 'words') {
            var words = [];
            for (var i = 0; i < count; i++) words.push(randWord());
            text = words.join(' ');
            if (startClassic.checked) {
                var classicWords = 'lorem ipsum dolor sit amet consectetur adipiscing elit'.split(' ');
                for (var j = 0; j < Math.min(classicWords.length, count); j++) words[j] = classicWords[j];
                text = words.join(' ');
            }
            text = text.charAt(0).toUpperCase() + text.slice(1) + '.';
        } else if (type === 'sentences') {
            var sents = [];
            for (var k = 0; k < count; k++) sents.push(makeSentence(6, 16));
            if (startClassic.checked && sents.length) sents[0] = CLASSIC;
            text = sents.join(' ');
        } else {
            var paras = [];
            for (var p = 0; p < count; p++) paras.push(makeParagraph());
            if (startClassic.checked && paras.length) {
                paras[0] = CLASSIC + ' ' + paras[0];
            }
            text = paras.join('\n\n');
        }
        output.value = text;
        updateCounts();
        showSuccess('Generated ' + count + ' ' + type + '.');
    }

    function updateCounts() {
        var t = output.value;
        document.getElementById('charCount').textContent = t.length;
        document.getElementById('wordCount').textContent = t.trim() ? t.trim().split(/\s+/).length : 0;
    }

    document.getElementById('generateBtn').addEventListener('click', generate);
    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!output.value) { showError('Generate some text first.'); return; }
        try { await navigator.clipboard.writeText(output.value); }
        catch (e) { output.select(); document.execCommand('copy'); }
        showSuccess('Text copied to clipboard!');
    });
    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!output.value) { showError('Generate some text first.'); return; }
        var blob = new Blob([output.value], { type: 'text/plain;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = 'lorem-ipsum.txt';
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
    });

    generate();
})();
</script>
@endsection
