@extends('layouts.app')

@section('title', 'Word to PDF - Free Text to PDF Converter | Azlaan Tools')
@section('meta_description', 'Free text to PDF tool: type or paste your text, add a title, then save it as a clean PDF using your browser print dialog. No signup, no upload, fully private.')

@section('styles')
<style>
    #printArea { display: none; }
    #previewArea { white-space: pre-wrap; word-wrap: break-word; }
    @@media print {
        body * { visibility: hidden; }
        #printArea, #printArea * { visibility: visible; }
        #printArea {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20mm 15mm;
            color: #000;
            background: #fff;
        }
        #printTitle { margin-bottom: 16px; }
        #printBody { white-space: pre-wrap; word-wrap: break-word; line-height: 1.6; }
    }
</style>
@endsection

@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">Word to PDF (Text to PDF)</h1>
    <p class="lead">Type or paste your text, give it a title, and turn it into a clean PDF — free, no signup, and nothing is uploaded.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label for="docTitle" class="form-label fw-semibold">Document Title</label>
            <input type="text" class="form-control" id="docTitle" placeholder="e.g. My Document" value="My Document">

            <label for="docText" class="form-label fw-semibold mt-3">Your Text</label>
            <textarea class="form-control" id="docText" rows="12" placeholder="Type or paste the text you want in your PDF here... Line breaks are preserved."></textarea>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label for="fontSizeSelect" class="form-label fw-semibold">Font Size</label>
                    <select class="form-select" id="fontSizeSelect">
                        <option value="12">12 pt (small)</option>
                        <option value="14" selected>14 pt (normal)</option>
                        <option value="16">16 pt (large)</option>
                        <option value="18">18 pt (extra large)</option>
                        <option value="20">20 pt (huge)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="alignSelect" class="form-label fw-semibold">Title Alignment</label>
                    <select class="form-select" id="alignSelect">
                        <option value="left" selected>Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-lg w-100" id="printBtn">Create PDF (Print / Save as PDF)</button>
                </div>
            </div>

            <div class="alert alert-warning mt-3 mb-0">
                <strong>Important:</strong> When the print dialog opens, choose <strong>"Save as PDF"</strong> as the destination/printer, then click Save. That creates your PDF file.
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5">Preview</h2>
            <h3 id="previewTitle" class="h4">My Document</h3>
            <div id="previewArea" class="border rounded p-3 bg-white" style="font-size: 14pt; min-height: 120px;">Your preview will appear here...</div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your text never leaves your browser — the PDF is created by your own browser's print function, with no upload and no signup.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Enter a title for your document and type or paste your text in the box above.</li>
        <li>Choose a font size and title alignment, and check the live preview below the form.</li>
        <li>Click <strong>Create PDF</strong> — your browser's print dialog will open showing only the clean document.</li>
        <li>In the print dialog, set the destination to <strong>Save as PDF</strong> and click Save to download your PDF.</li>
    </ol>
</div>

<div id="printArea">
    <h1 id="printTitle">My Document</h1>
    <div id="printBody"></div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var titleInput = document.getElementById('docTitle');
    var textInput = document.getElementById('docText');
    var fontSizeSelect = document.getElementById('fontSizeSelect');
    var alignSelect = document.getElementById('alignSelect');
    var previewTitle = document.getElementById('previewTitle');
    var previewArea = document.getElementById('previewArea');
    var printTitle = document.getElementById('printTitle');
    var printBody = document.getElementById('printBody');
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
        setTimeout(function () { successBox.classList.add('d-none'); }, 3000);
    }
    function hideAlerts() { alertBox.classList.add('d-none'); successBox.classList.add('d-none'); }

    function sync() {
        var title = titleInput.value.trim() || 'My Document';
        var size = fontSizeSelect.value + 'pt';
        var align = alignSelect.value;
        // textContent keeps user text safe (no HTML runs) and pre-wrap preserves line breaks.
        previewTitle.textContent = title;
        previewTitle.style.textAlign = align;
        previewArea.textContent = textInput.value || 'Your preview will appear here...';
        previewArea.style.fontSize = size;
        printTitle.textContent = title;
        printTitle.style.textAlign = align;
        printBody.textContent = textInput.value;
        printBody.style.fontSize = size;
    }

    titleInput.addEventListener('input', sync);
    textInput.addEventListener('input', sync);
    fontSizeSelect.addEventListener('change', sync);
    alignSelect.addEventListener('change', sync);

    document.getElementById('printBtn').addEventListener('click', function () {
        hideAlerts();
        if (!textInput.value.trim()) {
            showError('Please type or paste some text first, then create your PDF.');
            textInput.focus();
            return;
        }
        sync();
        showSuccess('Opening the print dialog — choose "Save as PDF" as the destination to save your file.');
        setTimeout(function () { window.print(); }, 150);
    });

    sync();
})();
</script>
@endsection
