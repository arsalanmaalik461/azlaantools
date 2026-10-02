@extends('layouts.app')

@section('title', 'Online Notepad - Free, Autosaved in Your Browser | Azlaan Tools')
@section('meta_description', 'Free online notepad with autosave: write notes that stay saved in your own browser, with word count and one-click download. No signup, no server, fully private.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Online Notepad</h1>
    <p class="lead">A simple, free notepad that autosaves as you type — your notes stay in your own browser, ready when you come back. No signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <label for="noteArea" class="form-label fw-semibold mb-0">Your Notes</label>
                <span id="saveStatus" class="badge bg-secondary">Not saved yet</span>
            </div>
            <textarea class="form-control" id="noteArea" rows="14" placeholder="Start typing... your notes are saved automatically in this browser." style="font-size: 16px;"></textarea>

            <div class="d-flex flex-wrap gap-2 mt-2 small text-muted">
                <span>Words: <strong id="wordCount">0</strong></span>
                <span>Characters: <strong id="charCount">0</strong></span>
                <span>Lines: <strong id="lineCount">0</strong></span>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                <button type="button" class="btn btn-success btn-sm" id="downloadBtn">Download .txt</button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="copyBtn">Copy All</button>
                <button type="button" class="btn btn-outline-danger btn-sm" id="clearBtn">Clear</button>
                <span class="ms-2 small fw-semibold">Font size:</span>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="fontMinus" title="Smaller text">A-</button>
                <span id="fontSizeLabel" class="small">16px</span>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="fontPlus" title="Bigger text">A+</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your notes never leave your browser — they are saved only in this browser's local storage on your own device. Clearing your browser data will delete them, so download important notes as a backup.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Just start typing — everything is saved automatically as you write.</li>
        <li>Watch the <strong>Saved</strong> badge at the top to confirm the time of the last save.</li>
        <li>Close the page and come back later in the same browser — your notes will still be here.</li>
        <li>Click <strong>Download .txt</strong> to save a copy to your device, or <strong>Clear</strong> to erase the note (you will be asked to confirm).</li>
        <li>Use the A- / A+ buttons to make the text smaller or bigger.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var KEY = 'azlaan_notepad';
    var FONT_KEY = 'azlaan_notepad_font';
    var area = document.getElementById('noteArea');
    var saveStatus = document.getElementById('saveStatus');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var fontSize = 16;
    var saveTimer = null;

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
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function timeNow() {
        var d = new Date();
        return pad(d.getHours()) + ':' + pad(d.getMinutes());
    }
    function updateCounts() {
        var t = area.value;
        document.getElementById('wordCount').textContent = t.trim() ? t.trim().split(/\s+/).length : 0;
        document.getElementById('charCount').textContent = t.length;
        document.getElementById('lineCount').textContent = t ? t.split(/\n/).length : 0;
    }
    function markSaved() {
        saveStatus.textContent = 'Saved \u2713 ' + timeNow();
        saveStatus.className = 'badge bg-success';
    }
    function saveNow() {
        try {
            localStorage.setItem(KEY, area.value);
            markSaved();
        } catch (e) {
            saveStatus.textContent = 'Save failed (storage full or blocked)';
            saveStatus.className = 'badge bg-danger';
        }
    }
    function scheduleSave() {
        saveStatus.textContent = 'Saving...';
        saveStatus.className = 'badge bg-warning text-dark';
        if (saveTimer) clearTimeout(saveTimer);
        saveTimer = setTimeout(saveNow, 300);
    }
    function applyFont() {
        area.style.fontSize = fontSize + 'px';
        document.getElementById('fontSizeLabel').textContent = fontSize + 'px';
        try { localStorage.setItem(FONT_KEY, String(fontSize)); } catch (e) {}
    }

    // Load saved note + font size
    try {
        var saved = localStorage.getItem(KEY);
        if (saved !== null && saved !== '') {
            area.value = saved;
            markSaved();
        }
        var savedFont = parseInt(localStorage.getItem(FONT_KEY) || '', 10);
        if (!isNaN(savedFont) && savedFont >= 12 && savedFont <= 28) fontSize = savedFont;
    } catch (e) {
        showError('Your browser blocked local storage, so autosave may not work in this session.');
    }
    applyFont();
    updateCounts();

    area.addEventListener('input', function () { updateCounts(); scheduleSave(); });

    document.getElementById('fontPlus').addEventListener('click', function () {
        if (fontSize < 28) { fontSize += 2; applyFont(); }
    });
    document.getElementById('fontMinus').addEventListener('click', function () {
        if (fontSize > 12) { fontSize -= 2; applyFont(); }
    });

    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!area.value) { showError('The notepad is empty — nothing to download.'); return; }
        var blob = new Blob([area.value], { type: 'text/plain;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = 'my-notes.txt';
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
        showSuccess('Notes downloaded as my-notes.txt');
    });

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!area.value) { showError('The notepad is empty — nothing to copy.'); return; }
        try { await navigator.clipboard.writeText(area.value); }
        catch (e) { area.select(); document.execCommand('copy'); }
        showSuccess('Notes copied to clipboard!');
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        if (!area.value) return;
        if (!window.confirm('Clear all notes? This cannot be undone.')) return;
        area.value = '';
        updateCounts();
        try { localStorage.removeItem(KEY); } catch (e) {}
        saveStatus.textContent = 'Cleared';
        saveStatus.className = 'badge bg-secondary';
        area.focus();
    });
})();
</script>
@endsection
