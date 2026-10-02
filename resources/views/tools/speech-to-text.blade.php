@extends('layouts.app')

@section('title', 'Speech to Text Online Free - Voice Typing Urdu and English | Azlaan Tools')
@section('meta_description', 'Free Speech to Text voice typing tool: speak in English or Urdu and watch your words become text instantly. No signup, works in Chrome and Edge.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Speech to Text</h1>
    <p class="lead">Speak and watch your voice turn into written text - free voice typing for English and Urdu, right in your browser.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>
    <div id="unsupportedBox" class="alert alert-warning d-none" role="alert">
        <strong>Speech recognition is not supported in this browser.</strong> Voice typing needs the Web Speech API, which is available in Google Chrome and Microsoft Edge. Please open this page in Chrome or Edge (Firefox does not support it yet) and allow microphone access.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-6">
                    <label for="langSelect" class="form-label fw-semibold">Speaking Language</label>
                    <select id="langSelect" class="form-select">
                        <option value="en-US" selected>English (US) - en-US</option>
                        <option value="en-GB">English (UK) - en-GB</option>
                        <option value="ur-PK">Urdu (Pakistan) - ur-PK</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="continuousCheck" checked>
                        <label class="form-check-label" for="continuousCheck">Keep listening (continuous mode)</label>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <button type="button" id="startBtn" class="btn btn-success btn-lg">Start Mic</button>
                <button type="button" id="stopBtn" class="btn btn-danger btn-lg" disabled>Stop</button>
                <span id="listenStatus" class="badge bg-secondary fs-6">Not listening</span>
            </div>

            <label for="resultText" class="form-label fw-semibold">Transcript</label>
            <textarea id="resultText" class="form-control" rows="10" placeholder="Your spoken words will appear here. You can also type or edit manually..."></textarea>
            <p class="small text-muted mt-2 mb-0">Live preview: <span id="interimText" class="fst-italic"></span></p>
            <div class="small text-muted mt-2">Characters: <strong id="charCount">0</strong> &nbsp; Words: <strong id="wordCount">0</strong></div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="copyBtn" class="btn btn-primary">Copy Text</button>
                <button type="button" id="downloadBtn" class="btn btn-outline-success">Download .txt</button>
                <button type="button" id="clearBtn" class="btn btn-outline-danger">Clear</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Microphone access is requested only when you click Start, and you can stop listening at any time. In Chrome, audio for recognition may be processed by the browser provider - no audio is stored on our server.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Open this page in Chrome or Edge and choose your speaking language (English US, English UK or Urdu Pakistan).</li>
        <li>Click <strong>Start Mic</strong> and allow microphone permission when the browser asks.</li>
        <li>Speak clearly - final words are added to the transcript box and you can edit them by hand.</li>
        <li>Click <strong>Stop</strong> when finished, then <strong>Copy Text</strong> or <strong>Download .txt</strong> to save your transcript.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var langSelect = document.getElementById('langSelect');
    var continuousCheck = document.getElementById('continuousCheck');
    var startBtn = document.getElementById('startBtn');
    var stopBtn = document.getElementById('stopBtn');
    var listenStatus = document.getElementById('listenStatus');
    var resultText = document.getElementById('resultText');
    var interimText = document.getElementById('interimText');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var unsupportedBox = document.getElementById('unsupportedBox');
    var recognition = null;
    var listening = false;
    var manualStop = false;

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
    function updateCounts() {
        var t = resultText.value;
        document.getElementById('charCount').textContent = t.length;
        document.getElementById('wordCount').textContent = t.trim() ? t.trim().split(/\s+/).length : 0;
    }
    function setListening(on) {
        listening = on;
        startBtn.disabled = on;
        stopBtn.disabled = !on;
        listenStatus.textContent = on ? 'Listening...' : 'Not listening';
        listenStatus.className = on ? 'badge bg-danger fs-6' : 'badge bg-secondary fs-6';
        if (!on) interimText.textContent = '';
    }

    var SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRec) {
        unsupportedBox.classList.remove('d-none');
        startBtn.disabled = true;
        stopBtn.disabled = true;
    }

    resultText.addEventListener('input', updateCounts);

    startBtn.addEventListener('click', function () {
        hideAlerts();
        if (!SpeechRec) {
            unsupportedBox.classList.remove('d-none');
            showError('Speech recognition is not supported in this browser. Please use Chrome or Edge.');
            return;
        }
        if (listening) return;
        manualStop = false;
        recognition = new SpeechRec();
        recognition.lang = langSelect.value;
        recognition.interimResults = true;
        recognition.continuous = continuousCheck.checked;
        recognition.maxAlternatives = 1;

        recognition.onresult = function (event) {
            var interim = '';
            var finalChunk = '';
            for (var i = event.resultIndex; i < event.results.length; i++) {
                var res = event.results[i];
                var transcript = res[0] ? res[0].transcript : '';
                if (res.isFinal) {
                    finalChunk += transcript;
                } else {
                    interim += transcript;
                }
            }
            if (finalChunk) {
                var current = resultText.value;
                var needsSpace = current && !/\s$/.test(current) && finalChunk.charAt(0) !== ' ';
                resultText.value = current + (needsSpace ? ' ' : '') + finalChunk;
                updateCounts();
            }
            interimText.textContent = interim;
        };
        recognition.onerror = function (event) {
            var code = event ? event.error : '';
            if (code === 'not-allowed' || code === 'service-not-allowed') {
                showError('Microphone permission was denied. Please allow microphone access in your browser address bar and try again.');
            } else if (code === 'no-speech') {
                showError('No speech was detected. Please speak closer to the microphone and try again.');
            } else if (code === 'audio-capture') {
                showError('No microphone was found. Please connect a microphone and try again.');
            } else if (code === 'network') {
                showError('A network error occurred during recognition. Check your internet connection and try again.');
            } else if (code === 'aborted') {
                if (!manualStop) showError('Speech recognition was aborted. Please try again.');
            } else if (code) {
                showError('Speech recognition error: ' + code);
            }
        };
        recognition.onend = function () {
            if (listening && !manualStop && continuousCheck.checked) {
                try {
                    recognition.start();
                    return;
                } catch (e) {
                    setListening(false);
                }
            } else {
                setListening(false);
            }
        };
        try {
            recognition.start();
            setListening(true);
            showSuccess('Listening... speak now.');
        } catch (e) {
            console.error(e);
            setListening(false);
            showError('Could not start the microphone. Please try again in Chrome or Edge.');
        }
    } );

    stopBtn.addEventListener('click', function () {
        manualStop = true;
        if (recognition) {
            try {
                recognition.stop();
            } catch (e) {
                setListening(false);
            }
        }
        setListening(false);
    } );

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!resultText.value) {
            showError('There is no transcript to copy yet.');
            return;
        }
        try {
            await navigator.clipboard.writeText(resultText.value);
            showSuccess('Copied to clipboard!');
        } catch (e) {
            resultText.select();
            document.execCommand('copy');
            showSuccess('Copied to clipboard!');
        }
    } );

    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!resultText.value) {
            showError('There is no transcript to download yet.');
            return;
        }
        var blob = new Blob([resultText.value], {
            type: 'text/plain;charset=utf-8'
        } );
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'transcript.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 2000);
    } );

    document.getElementById('clearBtn').addEventListener('click', function () {
        resultText.value = '';
        interimText.textContent = '';
        hideAlerts();
        updateCounts();
    } );

    updateCounts();
} )();
</script>
@endsection
