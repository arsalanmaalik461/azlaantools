@extends('layouts.app')
@section('title', 'Create Fillable PDF Form Online Free — Azlaan Tools')
@section('meta_description', 'Turn any PDF into a fillable form with text fields and checkboxes online for free. Add fields, position them, download the fillable PDF. No signup, no upload.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Create Fillable PDF Form</h1>
            <p class="lead text-muted">Upload your PDF, then add text fields and checkboxes that users can fill in. Make a fillable form and download it — free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">PDF file</label>
                        <input type="file" class="form-control" id="fileInput" accept="application/pdf,.pdf">
                        <div class="form-text">Your PDF stays in your browser — nothing is uploaded.</div>
                    </div>
                    <p class="mb-1">Loaded file: <strong id="fileName">no file</strong> (<span id="pageCount">0</span> pages)</p>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <hr>

                    <h3 class="h5">Add a field</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="fType" class="form-label fw-semibold">Field type</label>
                            <select class="form-select" id="fType">
                                <option value="text">Text field</option>
                                <option value="checkbox">Checkbox</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fLabel" class="form-label fw-semibold">Field label</label>
                            <input type="text" class="form-control" id="fLabel" placeholder="e.g. Full name">
                        </div>
                        <div class="col-md-4">
                            <label for="fPage" class="form-label fw-semibold">Page number</label>
                            <input type="number" class="form-control" id="fPage" value="1" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="fPos" class="form-label fw-semibold">Position on page</label>
                            <select class="form-select" id="fPos">
                                <option value="tl">Top — left</option>
                                <option value="tc">Top — center</option>
                                <option value="tr">Top — right</option>
                                <option value="ml">Middle — left</option>
                                <option value="mc">Middle — center</option>
                                <option value="mr">Middle — right</option>
                                <option value="bl">Bottom — left</option>
                                <option value="bc">Bottom — center</option>
                                <option value="br">Bottom — right</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary w-100" id="addBtn">+ Add field</button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="h5">Fields added (<span id="fieldCount">0</span>)</h3>
                        <ul class="list-group" id="fieldList">
                            <li class="list-group-item text-muted">No fields yet — add one above.</li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Create Fillable PDF &amp; Download</button>
                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Privacy note:</strong> Your PDF never leaves your browser — everything happens on your device.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file — page count will appear above.</li>
                <li>Choose a field type, label, page number and position, then click <strong>+ Add field</strong>. Repeat for every field.</li>
                <li>Click <strong>Create Fillable PDF &amp; Download</strong> — your PDF downloads with real form fields anyone can fill in Adobe Reader or any PDF app.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('fileInput');
    var fileName = document.getElementById('fileName');
    var pageCount = document.getElementById('pageCount');
    var addBtn = document.getElementById('addBtn');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var fieldList = document.getElementById('fieldList');
    var fieldCount = document.getElementById('fieldCount');

    var pdfBytes = null;
    var totalPages = 0;
    var fields = [];
    var fieldSeq = 0;
    var origName = 'form';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function renderFields() {
        fieldCount.textContent = fields.length;
        fieldList.innerHTML = '';
        if (fields.length === 0) {
            var li = document.createElement('li');
            li.className = 'list-group-item text-muted';
            li.textContent = 'No fields yet — add one above.';
            fieldList.appendChild(li);
            return;
        }
        fields.forEach(function (f, idx) {
            var item = document.createElement('li');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            var posNames = { tl: 'top-left', tc: 'top-center', tr: 'top-right', ml: 'middle-left', mc: 'middle-center', mr: 'middle-right', bl: 'bottom-left', bc: 'bottom-center', br: 'bottom-right' };
            var span = document.createElement('span');
            span.textContent = (f.type === 'text' ? 'Text' : 'Checkbox') + ': ' + f.label + ' (page ' + f.page + ', ' + posNames[f.pos] + ')';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Remove';
            btn.setAttribute('data-idx', idx);
            btn.addEventListener('click', function () {
                fields.splice(parseInt(this.getAttribute('data-idx'), 10), 1);
                renderFields();
            });
            item.appendChild(span);
            item.appendChild(btn);
            fieldList.appendChild(item);
        });
    }

    fileInput.addEventListener('change', function () {
        hideError();
        var file = fileInput.files[0];
        if (!file) { return; }
        if (file.type !== 'application/pdf' && file.name.slice(-4).toLowerCase() !== '.pdf') {
            showError('Please select a PDF file.');
            return;
        }
        origName = file.name.replace(/\.pdf$/i, '');
        var reader = new FileReader();
        reader.onload = async function () {
            pdfBytes = reader.result;
            try {
                var doc = await PDFLib.PDFDocument.load(pdfBytes);
                totalPages = doc.getPageCount();
                fileName.textContent = file.name;
                pageCount.textContent = totalPages;
                document.getElementById('fPage').max = totalPages;
            } catch (e) {
                showError('Could not read this PDF. It may be corrupted or password-protected.');
                pdfBytes = null;
                totalPages = 0;
            }
        };
        reader.readAsArrayBuffer(file);
    });

    addBtn.addEventListener('click', function () {
        hideError();
        if (!pdfBytes) { showError('Please select a PDF file first.'); return; }
        var label = document.getElementById('fLabel').value.trim();
        var page = parseInt(document.getElementById('fPage').value, 10);
        if (!label) { showError('Please enter a field label.'); return; }
        if (isNaN(page) || page < 1 || page > totalPages) {
            showError('Page number must be between 1 and ' + totalPages + '.');
            return;
        }
        fieldSeq += 1;
        fields.push({
            id: 'field_' + fieldSeq,
            type: document.getElementById('fType').value,
            label: label,
            page: page,
            pos: document.getElementById('fPos').value
        });
        document.getElementById('fLabel').value = '';
        renderFields();
    });

    function fieldRect(pos, pageW, pageH, fw, fh) {
        var margin = 50;
        var x, y;
        var v = pos.charAt(0), h = pos.charAt(1);
        if (v === 't') { y = pageH - 60 - fh; }
        else if (v === 'm') { y = (pageH - fh) / 2; }
        else { y = 80; }
        if (h === 'l') { x = margin; }
        else if (h === 'c') { x = (pageW - fw) / 2; }
        else { x = pageW - margin - fw; }
        return { x: x, y: y };
    }

    goBtn.addEventListener('click', async function () {
        hideError();
        results.classList.add('d-none');
        if (!pdfBytes) { showError('Please select a PDF file first.'); return; }
        if (fields.length === 0) { showError('No field added — please add a field first.'); return; }
        try {
            var doc = await PDFLib.PDFDocument.load(pdfBytes);
            var font = await doc.embedFont(PDFLib.StandardFonts.Helvetica);
            var form = doc.getForm();
            var ok = 0;
            fields.forEach(function (f) {
                var page = doc.getPage(f.page - 1);
                var dims = page.getSize();
                if (f.type === 'checkbox') {
                    var cb = form.createCheckBox(f.id);
                    var r = fieldRect(f.pos, dims.width, dims.height, 18, 18);
                    cb.addToPage(page, { x: r.x, y: r.y, width: 18, height: 18, borderWidth: 1, borderColor: PDFLib.rgb(0.2, 0.2, 0.2) });
                    page.drawText(f.label, { x: r.x, y: r.y + 24, size: 11, font: font, color: PDFLib.rgb(0.15, 0.15, 0.15) });
                } else {
                    var tf = form.createTextField(f.id);
                    tf.setText('');
                    var r2 = fieldRect(f.pos, dims.width, dims.height, 220, 24);
                    tf.addToPage(page, { x: r2.x, y: r2.y, width: 220, height: 24, textColor: PDFLib.rgb(0, 0, 0), backgroundColor: PDFLib.rgb(1, 1, 1), borderColor: PDFLib.rgb(0.4, 0.4, 0.4), borderWidth: 1, font: font, fontSize: 12 });
                    page.drawText(f.label, { x: r2.x, y: r2.y + 30, size: 11, font: font, color: PDFLib.rgb(0.15, 0.15, 0.15) });
                }
                ok += 1;
            });
            form.updateFieldAppearances(font);
            var outBytes = await doc.save();
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = origName + '-fillable.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
            results.classList.remove('d-none');
            results.innerHTML = '<div class="alert alert-success mb-0">Fillable PDF ready — ' + ok + ' fields added. If the download did not start, please try again.</div>';
        } catch (e) {
            showError('Could not create the fillable PDF. The file may be corrupted or password-protected.');
        }
    });

    renderFields();
})();
</script>
@endsection
