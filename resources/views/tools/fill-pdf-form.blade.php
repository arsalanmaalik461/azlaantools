@extends('layouts.app')
@section('title', 'Fill PDF Form Online Free — Azlaan Tools')
@section('meta_description', 'Fill PDF forms online for free. Detects fillable form fields automatically, lets you type your answers and downloads the completed PDF. No signup, no upload.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Fill PDF Form</h1>
            <p class="lead text-muted">Open a fillable PDF form, type your answers into the fields below, and download the completed form — no printer or scanner needed.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">📝</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF form here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong> — <span id="fieldInfo"></span></p>
                        <div id="noFieldsBox" class="alert alert-warning d-none">
                            This PDF has no fillable form fields. It may be a scanned or flat PDF. Tip: you can still add text on top of it with our <strong>Edit PDF</strong> tool.
                        </div>
                        <div id="fieldsWrap"></div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="flattenCheck">
                            <label class="form-check-label" for="flattenCheck">Flatten form (answers become permanent and can no longer be edited)</label>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Fill &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file and your answers never leave your browser — everything happens on your phone/computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF form into it.</li>
                <li>The form fields are detected automatically — type your answers, tick checkboxes and pick options.</li>
                <li>Optionally tick <strong>Flatten form</strong> if the answers should become permanent.</li>
                <li>Click <strong>Fill &amp; Download PDF</strong> and the completed form downloads automatically.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var fieldInfo = document.getElementById('fieldInfo');
    var noFieldsBox = document.getElementById('noFieldsBox');
    var fieldsWrap = document.getElementById('fieldsWrap');
    var flattenCheck = document.getElementById('flattenCheck');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var storedBuffer = null;
    var storedName = 'document';
    var fieldDefs = [];

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
    function prettyName(raw) {
        return raw.replace(/[._]+/g, ' ').replace(/\s+/g, ' ').trim() || raw;
    }

    function fieldKind(field) {
        // NOTE: field.constructor.name is useless with the minified pdf-lib
        // build (it returns "r"/"e"), so detect types with instanceof.
        if (typeof PDFLib !== 'undefined') {
            if (field instanceof PDFLib.PDFTextField) return 'PDFTextField';
            if (field instanceof PDFLib.PDFCheckBox) return 'PDFCheckBox';
            if (field instanceof PDFLib.PDFRadioGroup) return 'PDFRadioGroup';
            if (field instanceof PDFLib.PDFDropdown) return 'PDFDropdown';
            if (field instanceof PDFLib.PDFOptionList) return 'PDFOptionList';
            if (field instanceof PDFLib.PDFButton) return 'PDFButton';
            if (field instanceof PDFLib.PDFSignature) return 'PDFSignature';
        }
        return 'PDFField';
    }
    function buildFieldUI(form) {
        fieldsWrap.innerHTML = '';
        fieldDefs = [];
        var fields = form.getFields();
        fields.forEach(function (field, idx) {
            var typeName = fieldKind(field);
            var name = field.getName();
            var wrap = document.createElement('div');
            wrap.className = 'mb-3';
            var label = document.createElement('label');
            label.className = 'form-label fw-semibold';
            label.textContent = prettyName(name);
            wrap.appendChild(label);
            var inputId = 'field_' + idx;
            label.setAttribute('for', inputId);
            if (typeName === 'PDFTextField') {
                var isMulti = false;
                try { isMulti = field.isMultiline(); } catch (e) { isMulti = false; }
                var current = '';
                try { current = field.getText() || ''; } catch (e) { current = ''; }
                var el;
                if (isMulti) {
                    el = document.createElement('textarea');
                    el.rows = 3;
                } else {
                    el = document.createElement('input');
                    el.type = 'text';
                }
                el.className = 'form-control form-control-lg';
                el.id = inputId;
                el.value = current;
                wrap.appendChild(el);
                fieldDefs.push({ kind: 'text', name: name, el: el });
            } else if (typeName === 'PDFCheckBox') {
                wrap.classList.remove('mb-3');
                wrap.className = 'form-check mb-3';
                var cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input';
                cb.id = inputId;
                try { cb.checked = field.isChecked(); } catch (e) { cb.checked = false; }
                label.classList.remove('fw-semibold');
                label.classList.add('form-check-label');
                wrap.innerHTML = '';
                wrap.appendChild(cb);
                wrap.appendChild(label);
                fieldDefs.push({ kind: 'checkbox', name: name, el: cb });
            } else if (typeName === 'PDFRadioGroup') {
                var opts = [];
                try { opts = field.getOptions(); } catch (e) { opts = []; }
                var selected = '';
                try { selected = field.getSelected() || ''; } catch (e) { selected = ''; }
                var group = document.createElement('div');
                opts.forEach(function (optVal) {
                    var rc = document.createElement('div');
                    rc.className = 'form-check';
                    var r = document.createElement('input');
                    r.type = 'radio';
                    r.className = 'form-check-input';
                    r.name = inputId;
                    r.value = optVal;
                    if (optVal === selected) r.checked = true;
                    var rl = document.createElement('label');
                    rl.className = 'form-check-label';
                    rl.textContent = optVal;
                    rc.appendChild(r);
                    rc.appendChild(rl);
                    group.appendChild(rc);
                });
                wrap.appendChild(group);
                fieldDefs.push({ kind: 'radio', name: name, groupName: inputId });
            } else if (typeName === 'PDFDropdown' || typeName === 'PDFOptionList') {
                var dopts = [];
                try { dopts = field.getOptions(); } catch (e) { dopts = []; }
                var sel = document.createElement('select');
                sel.className = 'form-select form-select-lg';
                sel.id = inputId;
                var blank = document.createElement('option');
                blank.value = '';
                blank.textContent = '— Select —';
                sel.appendChild(blank);
                dopts.forEach(function (optVal) {
                    var o = document.createElement('option');
                    o.value = optVal;
                    o.textContent = optVal;
                    sel.appendChild(o);
                });
                try {
                    var cur = field.getSelected();
                    if (cur && cur.length) sel.value = cur[0];
                } catch (e) { /* ignore */ }
                wrap.appendChild(sel);
                fieldDefs.push({ kind: 'dropdown', name: name, el: sel });
            } else {
                var note = document.createElement('p');
                note.className = 'form-text text-muted mb-0';
                note.textContent = 'This field type (' + typeName.replace('PDF', '') + ') cannot be filled here and will be left unchanged.';
                wrap.appendChild(note);
                fieldDefs.push({ kind: 'skip', name: name });
            }
            fieldsWrap.appendChild(wrap);
        });
        return fields.length;
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            var doc = await PDFLib.PDFDocument.load(buf.slice(0));
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            var form = doc.getForm();
            var count = buildFieldUI(form);
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            fieldInfo.textContent = count + (count === 1 ? ' form field' : ' form fields') + ' found';
            noFieldsBox.classList.toggle('d-none', count !== 0);
            processBtn.disabled = count === 0;
            toolWrap.classList.remove('d-none');
            if (count > 0) {
                showSuccess('Form loaded — ' + count + ' field(s) detected. Fill them in below.');
            }
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            toolWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) loadFile(fileInput.files[0]);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.style.background = '#e9f2ff'; });
    dropZone.addEventListener('dragleave', function () { dropZone.style.background = '#f8f9fa'; });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    clearBtn.addEventListener('click', function () {
        storedBuffer = null;
        fieldDefs = [];
        fieldsWrap.innerHTML = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Filling...';
        try {
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var form = pdfDoc.getForm();
            var warnings = [];
            fieldDefs.forEach(function (def) {
                try {
                    if (def.kind === 'text') {
                        form.getTextField(def.name).setText(def.el.value);
                    } else if (def.kind === 'checkbox') {
                        var box = form.getCheckBox(def.name);
                        if (def.el.checked) box.check(); else box.uncheck();
                    } else if (def.kind === 'radio') {
                        var radios = document.querySelectorAll('input[name="' + def.groupName + '"]');
                        for (var i = 0; i < radios.length; i++) {
                            if (radios[i].checked) {
                                form.getRadioGroup(def.name).select(radios[i].value);
                                break;
                            }
                        }
                    } else if (def.kind === 'dropdown') {
                        if (def.el.value) {
                            var f = form.getField(def.name);
                            if (f instanceof PDFLib.PDFDropdown) f.select(def.el.value);
                            else if (f instanceof PDFLib.PDFOptionList) f.select([def.el.value]);
                        }
                    }
                } catch (e) {
                    console.error(e);
                    warnings.push(def.name);
                }
            });
            if (flattenCheck.checked) {
                form.flatten();
            }
            var bytes = await pdfDoc.save();
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-filled.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            if (warnings.length) {
                showSuccess('Done! Your filled PDF (' + formatSize(blob.size) + ') has been downloaded. Note: ' + warnings.length + ' field(s) could not be filled.');
            } else {
                showSuccess('Done! Your filled PDF (' + formatSize(blob.size) + ') has been downloaded.');
            }
        } catch (err) {
            console.error(err);
            showError('Could not fill this form. The PDF may be corrupted or password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Fill & Download PDF';
        }
    });
})();
</script>
@endsection
