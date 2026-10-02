@extends('layouts.app')
@section('title', 'Excel to PDF Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert Excel (.xlsx, .xls) or CSV files to a printable PDF online for free. Preview the sheet, pick portrait or landscape, and download. No signup, no upload.')
@section('styles')
<style>
@@media print {
    body * { visibility: hidden !important; }
    #previewTable, #previewTable * { visibility: visible !important; }
    #previewTable { position: absolute !important; left: 0; top: 0; width: 100%; }
}
</style>
@endsection
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Excel to PDF</h1>
            <p class="lead text-muted">Convert an Excel or CSV sheet into a clean, printable PDF. First preview the sheet, then download the A4 PDF — portrait or landscape, your choice.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> This tool turns the sheet into a simple table-style PDF. Very wide sheets will fit in a small font — the landscape option is better for wide tables. Complex formatting, charts and merged-cell designs will not look exactly like they did in Excel.
            </div>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="drop-zone mb-3">
                <div class="fs-1 mb-2">📗</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop an Excel / CSV file here</p>
                <p class="text-muted mb-3">.xlsx, .xls or .csv — or click to browse</p>
                <button type="button" class="btn btn-primary">Select Excel File</button>
                <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong></p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="sheetSelect" class="form-label fw-semibold">Worksheet</label>
                                <select id="sheetSelect" class="form-select"></select>
                            </div>
                            <div class="col-md-6">
                                <label for="orientationSel" class="form-label fw-semibold">Page orientation</label>
                                <select id="orientationSel" class="form-select">
                                    <option value="portrait" selected>Portrait (A4)</option>
                                    <option value="landscape">Landscape (A4) — for wide tables</option>
                                </select>
                            </div>
                        </div>
                        <span class="form-label fw-semibold d-block mb-2">Preview</span>
                        <div id="previewTable" class="border rounded p-2 mb-3" style="overflow: auto; max-height: 520px; background: #fff; font-size: 13px;"></div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Download PDF</button>
                            <button type="button" id="printBtn" class="btn btn-outline-primary">Print / Save via Print</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything happens on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your .xlsx, .xls or .csv file into it.</li>
                <li>If the file has many worksheets, pick the sheet you want in the PDF from above.</li>
                <li>Choose portrait or landscape — landscape is better for a wide table.</li>
                <li>Click <strong>Download PDF</strong>. If the PDF does not download, use the <strong>Print</strong> button and choose "Save as PDF" in the browser print window.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var sheetSelect = document.getElementById('sheetSelect');
    var orientationSel = document.getElementById('orientationSel');
    var previewTable = document.getElementById('previewTable');
    var processBtn = document.getElementById('processBtn');
    var printBtn = document.getElementById('printBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var workbook = null;
    var storedName = 'sheet';

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
    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function renderSheet(sheetName) {
        if (!workbook || !sheetName) return;
        var ws = workbook.Sheets[sheetName];
        if (!ws) return;
        var aoa = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
        if (!aoa.length) {
            previewTable.innerHTML = '<p class="text-muted mb-0">This sheet is empty.</p>';
            return;
        }
        var maxCols = 0;
        aoa.forEach(function (row) { if (row.length > maxCols) maxCols = row.length; });
        var html = '<table class="table table-bordered table-sm mb-0"><tbody>';
        aoa.forEach(function (row, rIdx) {
            html += '<tr>';
            for (var c = 0; c < maxCols; c++) {
                var val = (row[c] === null || row[c] === undefined) ? '' : row[c];
                if (rIdx === 0) {
                    html += '<th style="background:#eef4ee;">' + escapeHtml(val) + '</th>';
                } else {
                    html += '<td>' + escapeHtml(val) + '</td>';
                }
            }
            html += '</tr>';
        });
        html += '</tbody></table>';
        previewTable.innerHTML = html;
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        var name = file.name.toLowerCase();
        if (!(name.endsWith('.xlsx') || name.endsWith('.xls') || name.endsWith('.csv'))) {
            showError('Please select a valid Excel file (.xlsx, .xls) or a CSV file.');
            return;
        }
        if (typeof XLSX === 'undefined') {
            showError('Excel library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            workbook = XLSX.read(new Uint8Array(buf), { type: 'array' });
            if (!workbook.SheetNames || workbook.SheetNames.length === 0) {
                throw new Error('No sheets found');
            }
            storedName = file.name.replace(/\.(xlsx|xls|csv)$/i, '') || 'sheet';
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + workbook.SheetNames.length + ' sheet(s)';
            sheetSelect.innerHTML = '';
            workbook.SheetNames.forEach(function (sn) {
                var opt = document.createElement('option');
                opt.value = sn;
                opt.textContent = sn;
                sheetSelect.appendChild(opt);
            });
            renderSheet(workbook.SheetNames[0]);
            toolWrap.classList.remove('d-none');
            showSuccess('File loaded — preview is below. Check the sheet and orientation, then download the PDF.');
        } catch (err) {
            console.error(err);
            workbook = null;
            toolWrap.classList.add('d-none');
            showError('Could not read this file. It may be corrupted or in an unsupported format. Please try a different file.');
        }
    }

    sheetSelect.addEventListener('change', function () { renderSheet(sheetSelect.value); });
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
        workbook = null;
        previewTable.innerHTML = '';
        sheetSelect.innerHTML = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });
    printBtn.addEventListener('click', function () {
        if (!workbook) { showError('Please select an Excel file first.'); return; }
        window.print();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!workbook) { showError('Please select an Excel file first.'); return; }
        if (typeof html2pdf === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection, or use the Print button and choose "Save as PDF".');
            return;
        }
        processBtn.disabled = true;
        processBtn.textContent = 'Creating PDF...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '40%';
        progressBar.textContent = '40%';
        var orientation = orientationSel.value === 'landscape' ? 'landscape' : 'portrait';
        var opt = {
            margin: 8,
            filename: storedName + '.pdf',
            image: { type: 'jpeg', quality: 0.96 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: orientation },
            pagebreak: { mode: ['css', 'legacy'] }
        };
        try {
            progressBar.style.width = '70%';
            progressBar.textContent = '70%';
            await html2pdf().set(opt).from(previewTable).save();
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            showSuccess('Done! Your PDF has been downloaded. If you do not like the layout, you can also try "Save as PDF" from the Print button.');
        } catch (err) {
            console.error(err);
            showError('Could not create the PDF directly. Please use the Print button above and choose "Save as PDF" in the print window.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
