@extends('layouts.app')
@section('title', 'Merge PDF Online Free — Azlaan Tools')
@section('meta_description', 'Merge multiple PDF files into one PDF online for free. No signup, no upload — files are processed in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">Merge PDF</h1>
    <p class="lead">Combine multiple PDF files into a single PDF — free, fast and private. Select your files, arrange them in order, and download the merged PDF.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">📄</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop PDF files here</p>
        <p class="text-muted mb-3">or click to browse from your device</p>
        <button type="button" class="btn btn-primary">Select PDF Files</button>
        <input type="file" id="fileInput" accept="application/pdf,.pdf" multiple class="d-none">
    </div>

    <div id="fileListWrap" class="d-none">
        <h2 class="h5">Selected Files (<span id="fileCount">0</span>)</h2>
        <ul id="fileList" class="list-group mb-3"></ul>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" id="mergeBtn" class="btn btn-success btn-lg">Merge PDFs</button>
            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear All</button>
        </div>
        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — everything happens on your phone or computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop two or more PDF files into it.</li>
        <li>Use the Up and Down buttons to arrange the files in the order you want.</li>
        <li>Remove any file you do not need using the Remove button.</li>
        <li>Click <strong>Merge PDFs</strong> and your merged file will download automatically as merged.pdf.</li>
    </ol>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    var files = [];
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var fileList = document.getElementById('fileList');
    var fileListWrap = document.getElementById('fileListWrap');
    var fileCount = document.getElementById('fileCount');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var mergeBtn = document.getElementById('mergeBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');

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
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function renderList() {
        fileList.innerHTML = '';
        fileCount.textContent = files.length;
        if (files.length === 0) {
            fileListWrap.classList.add('d-none');
            return;
        }
        fileListWrap.classList.remove('d-none');
        files.forEach(function (file, idx) {
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex flex-wrap align-items-center gap-2';

            var nameSpan = document.createElement('span');
            nameSpan.className = 'flex-grow-1 text-break';
            nameSpan.textContent = (idx + 1) + '. ' + file.name + ' (' + formatSize(file.size) + ')';
            li.appendChild(nameSpan);

            var upBtn = document.createElement('button');
            upBtn.type = 'button';
            upBtn.className = 'btn btn-sm btn-outline-primary';
            upBtn.textContent = '↑ Up';
            upBtn.disabled = idx === 0;
            upBtn.addEventListener('click', function () {
                var tmp = files[idx - 1]; files[idx - 1] = files[idx]; files[idx] = tmp;
                renderList();
            });
            li.appendChild(upBtn);

            var downBtn = document.createElement('button');
            downBtn.type = 'button';
            downBtn.className = 'btn btn-sm btn-outline-primary';
            downBtn.textContent = '↓ Down';
            downBtn.disabled = idx === files.length - 1;
            downBtn.addEventListener('click', function () {
                var tmp = files[idx + 1]; files[idx + 1] = files[idx]; files[idx] = tmp;
                renderList();
            });
            li.appendChild(downBtn);

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger';
            removeBtn.textContent = 'Remove';
            removeBtn.addEventListener('click', function () {
                files.splice(idx, 1);
                renderList();
            });
            li.appendChild(removeBtn);

            fileList.appendChild(li);
        });
    }

    function addFiles(fileListObj) {
        hideAlerts();
        var added = 0;
        for (var i = 0; i < fileListObj.length; i++) {
            var f = fileListObj[i];
            if (f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf')) {
                files.push(f);
                added++;
            }
        }
        if (added === 0) {
            showError('Please select valid PDF files only.');
        }
        renderList();
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        addFiles(fileInput.files);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.background = '#e9f2ff';
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.style.background = '#f8f9fa';
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files) addFiles(e.dataTransfer.files);
    });

    clearBtn.addEventListener('click', function () {
        files = [];
        hideAlerts();
        renderList();
    });

    mergeBtn.addEventListener('click', async function () {
        hideAlerts();
        if (files.length < 2) {
            showError('Please add at least 2 PDF files to merge.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        mergeBtn.disabled = true;
        mergeBtn.textContent = 'Merging...';
        progressWrap.classList.remove('d-none');
        try {
            var mergedPdf = await PDFLib.PDFDocument.create();
            for (var i = 0; i < files.length; i++) {
                var pct = Math.round(((i + 1) / files.length) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
                var arrayBuffer = await files[i].arrayBuffer();
                var pdf = await PDFLib.PDFDocument.load(arrayBuffer);
                var copiedPages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                copiedPages.forEach(function (page) { mergedPdf.addPage(page); });
            }
            var mergedBytes = await mergedPdf.save();
            var blob = new Blob([mergedBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'merged.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Your merged PDF (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not merge these PDFs. One of the files may be corrupted or password-protected. Please try again with different files.');
        } finally {
            mergeBtn.disabled = false;
            mergeBtn.textContent = 'Merge PDFs';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
