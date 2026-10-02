@extends('layouts.app')
@section('title', 'HTML to PDF Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert HTML code to a PDF online for free — paste HTML or upload an .html file, preview it, and download a PDF. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">HTML to PDF</h1>
            <p class="lead text-muted">Paste your HTML code or upload an .html file — see the live preview and download a PDF with one click.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> For safety, &lt;script&gt; tags are removed from pasted HTML, so JavaScript will not work in the preview. External images load only if their internet link is public. Complex CSS layouts (flex/grid) usually convert fine, but a 100% browser-like result is not always possible.
            </div>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="drop-zone mb-3">
                <div class="fs-1 mb-2">🌐</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop an .html file here (optional)</p>
                <p class="text-muted mb-3">or click to browse — or paste your HTML in the box below</p>
                <button type="button" class="btn btn-primary">Select HTML File</button>
                <input type="file" id="fileInput" accept=".html,.htm,text/html" class="d-none">
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="htmlInput" class="form-label fw-semibold">Your HTML code</label>
                    <textarea id="htmlInput" class="form-control mb-3" rows="10" spellcheck="false" placeholder="Paste your HTML here..."><h1 style="font-family: Arial, sans-serif; color: #0b3d2e;">My Document</h1>
<p style="font-family: Arial, sans-serif;">This is a sample. Replace this text with your own HTML, then press <strong>Update Preview</strong>.</p>
<table border="1" cellpadding="8" style="border-collapse: collapse; font-family: Arial, sans-serif;">
<tr><th>Item</th><th>Price</th></tr>
<tr><td>Sample item</td><td>Rs 1,000</td></tr>
</table></textarea>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="orientationSel" class="form-label fw-semibold">Page orientation</label>
                            <select id="orientationSel" class="form-select">
                                <option value="portrait" selected>Portrait (A4)</option>
                                <option value="landscape">Landscape (A4)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fileNameInput" class="form-label fw-semibold">PDF file name</label>
                            <input type="text" id="fileNameInput" class="form-control" value="document">
                        </div>
                    </div>
                    <span class="form-label fw-semibold d-block mb-2">Preview</span>
                    <div id="previewArea" class="border rounded p-3 mb-3" style="background: #fff; min-height: 160px; overflow: auto;"></div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="previewBtn" class="btn btn-primary">Update Preview</button>
                        <button type="button" id="processBtn" class="btn btn-success btn-lg">Download PDF</button>
                        <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                    </div>
                    <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything happens on your phone or computer; nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste HTML code in the box above, or drag &amp; drop / select an .html file — the code will load into the box by itself.</li>
                <li>Press <strong>Update Preview</strong> to see how the page will look (scripts are removed for safety).</li>
                <li>Choose the orientation and file name, then press <strong>Download PDF</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var htmlInput = document.getElementById('htmlInput');
    var orientationSel = document.getElementById('orientationSel');
    var fileNameInput = document.getElementById('fileNameInput');
    var previewArea = document.getElementById('previewArea');
    var previewBtn = document.getElementById('previewBtn');
    var processBtn = document.getElementById('processBtn');
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
    function cleanHtml(raw) {
        var holder = document.createElement('div');
        holder.innerHTML = raw;
        var bad = holder.querySelectorAll('script, iframe, object, embed, form, link, base, meta, title');
        for (var i = 0; i < bad.length; i++) {
            if (bad[i].parentNode) bad[i].parentNode.removeChild(bad[i]);
        }
        var all = holder.querySelectorAll('*');
        for (var j = 0; j < all.length; j++) {
            var attrs = all[j].attributes;
            if (!attrs) continue;
            for (var k = attrs.length - 1; k >= 0; k--) {
                var attrName = attrs[k].name.toLowerCase();
                var attrVal = (attrs[k].value || '').toLowerCase();
                if (attrName.indexOf('on') === 0) {
                    all[j].removeAttribute(attrs[k].name);
                } else if ((attrName === 'href' || attrName === 'src') && attrVal.indexOf('javascript:') === 0) {
                    all[j].removeAttribute(attrs[k].name);
                }
            }
        }
        return holder.innerHTML;
    }
    function updatePreview() {
        var raw = htmlInput.value.trim();
        if (!raw) {
            previewArea.innerHTML = '';
            showError('Please paste some HTML first, or upload an .html file.');
            return false;
        }
        previewArea.innerHTML = cleanHtml(raw);
        hideAlerts();
        return true;
    }
    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        var name = file.name.toLowerCase();
        if (!(name.endsWith('.html') || name.endsWith('.htm') || file.type === 'text/html')) {
            showError('Please select a valid HTML file (.html or .htm).');
            return;
        }
        try {
            var text = await file.text();
            htmlInput.value = text;
            var base = file.name.replace(/\.(html|htm)$/i, '') || 'document';
            fileNameInput.value = base;
            updatePreview();
            showSuccess('File loaded (' + formatSize(file.size) + ') — preview updated. You can download the PDF now.');
        } catch (err) {
            console.error(err);
            showError('Could not read this file. Please try a different HTML file, or paste the code directly.');
        }
    }

    previewBtn.addEventListener('click', function () {
        if (updatePreview()) showSuccess('Preview updated.');
    });
    htmlInput.addEventListener('input', function () { hideAlerts(); });
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) loadFile(fileInput.files[0]);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', function () { dropZone.classList.remove('dragover'); });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    clearBtn.addEventListener('click', function () {
        htmlInput.value = '';
        previewArea.innerHTML = '';
        fileNameInput.value = 'document';
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!updatePreview()) return;
        if (typeof html2pdf === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        var fname = (fileNameInput.value || 'document').trim().replace(/[\\/:*?"<>|]/g, '').replace(/\.pdf$/i, '') || 'document';
        processBtn.disabled = true;
        processBtn.textContent = 'Creating PDF...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '40%';
        progressBar.textContent = '40%';
        var orientation = orientationSel.value === 'landscape' ? 'landscape' : 'portrait';
        var opt = {
            margin: 10,
            filename: fname + '.pdf',
            image: { type: 'jpeg', quality: 0.96 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: orientation },
            pagebreak: { mode: ['css', 'legacy'] }
        };
        try {
            progressBar.style.width = '70%';
            progressBar.textContent = '70%';
            await html2pdf().set(opt).from(previewArea).save();
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            showSuccess('Done! Your PDF (' + fname + '.pdf) has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not create the PDF. Please make your HTML a bit simpler and try again — very complex layouts can fail sometimes.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });

    updatePreview();
})();
</script>
@endsection
