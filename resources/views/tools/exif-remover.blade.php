@extends('layouts.app')
@section('title', 'EXIF Remover — Remove Photo Location & Metadata Free | Azlaan Tools')
@section('meta_description', 'See what hidden data is in your photo — camera, date and GPS location — then remove all EXIF metadata free in your browser. Photo never leaves your device.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">EXIF Remover</h1>
            <p class="lead text-muted">Photos can secretly contain your camera model, the date, and even the exact GPS location where you took them. Check what is inside your photo — then strip it all before you share it.</p>
            <div class="alert alert-warning"><strong>Why this matters:</strong> Sharing an original photo on social media or with strangers can reveal where you live or where your children go to school. Remove metadata first.</div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drag &amp; drop your photo here, or click to browse</p>
                        <p class="text-muted small mb-0">JPG works best for EXIF. PNG also supported for cleaning.</p>
                        <input type="file" id="fileInput" class="d-none" accept="image/*">
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div id="resultWrap" class="d-none mt-4">
                        <div class="text-center mb-3"><img id="preview" class="img-fluid rounded border" alt="Preview" style="max-height:300px;"></div>
                        <h3 class="h5">Metadata found in this photo</h3>
                        <div id="exifEmpty" class="alert alert-success d-none">No EXIF metadata found in this photo — it is already clean.</div>
                        <table class="table table-sm table-striped"><tbody id="exifBody"></tbody></table>
                        <button type="button" id="cleanBtn" class="btn btn-danger btn-lg w-100">Remove All Metadata &amp; Download Clean Photo</button>
                        <p class="small text-muted mt-2 mb-0">Cleaning re-draws the photo on a canvas and saves a fresh file — EXIF, GPS and camera data do not survive that process.</p>
                    </div>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Upload a photo — usually an original JPG straight from a phone or camera.</li>
                <li>Read the table: camera, lens, ISO, date and GPS location (if present) will be listed.</li>
                <li>Press <strong>Remove All Metadata</strong> to download a clean copy. Share the clean copy, not the original.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photo never leaves your browser — checking and cleaning both happen on your own device, so even this privacy tool cannot see your location.</div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/exifr@7.1.3/dist/full.umd.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var errorBox = document.getElementById('errorBox');
    var resultWrap = document.getElementById('resultWrap');
    var preview = document.getElementById('preview');
    var exifBody = document.getElementById('exifBody');
    var exifEmpty = document.getElementById('exifEmpty');
    var currentFile = null, srcUrl = null;
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function addRow(label, value) {
        if (value === undefined || value === null || value === '') return;
        var tr = document.createElement('tr');
        var th = document.createElement('th'); th.textContent = label; th.style.width = '35%';
        var td = document.createElement('td'); td.textContent = String(value);
        tr.appendChild(th); tr.appendChild(td); exifBody.appendChild(tr);
    }
    function handleFile(file) {
        errorBox.classList.add('d-none'); if (!file) return;
        if (!file.type || file.type.indexOf('image/') !== 0) { showError('Please choose an image file.'); return; }
        currentFile = file;
        if (srcUrl) URL.revokeObjectURL(srcUrl);
        srcUrl = URL.createObjectURL(file);
        preview.src = srcUrl;
        exifBody.innerHTML = ''; exifEmpty.classList.add('d-none');
        resultWrap.classList.remove('d-none');
        addRow('File name', file.name);
        addRow('File size', Math.round(file.size / 1024) + ' KB');
        if (typeof exifr === 'undefined') { addRow('Note', 'EXIF reader library failed to load — you can still remove all metadata below.'); return; }
        exifr.parse(file, { gps: true }).then(function (data) {
            if (!data || Object.keys(data).length === 0) { exifEmpty.classList.remove('d-none'); return; }
            addRow('Camera make', data.Make);
            addRow('Camera model', data.Model);
            addRow('Lens', data.LensModel);
            addRow('Date taken', data.DateTimeOriginal || data.CreateDate || data.ModifyDate);
            addRow('ISO', data.ISO);
            addRow('Aperture', data.FNumber ? 'f/' + data.FNumber : '');
            addRow('Shutter', data.ExposureTime ? data.ExposureTime + ' s' : '');
            addRow('Focal length', data.FocalLength ? data.FocalLength + ' mm' : '');
            addRow('Software', data.Software);
            if (data.latitude && data.longitude) {
                addRow('GPS location ⚠️', data.latitude.toFixed(6) + ', ' + data.longitude.toFixed(6) + ' — anyone with this photo can find this place');
            }
            var extra = Object.keys(data).length;
            addRow('Total EXIF fields', extra);
            if (exifBody.children.length <= 3) exifEmpty.classList.remove('d-none');
        }).catch(function () {
            exifEmpty.classList.remove('d-none');
        });
    }
    document.getElementById('cleanBtn').addEventListener('click', function () {
        if (!currentFile) return;
        var img = new Image();
        img.onload = function () {
            var c = document.createElement('canvas'); c.width = img.naturalWidth; c.height = img.naturalHeight;
            var ctx = c.getContext('2d');
            var isPng = currentFile.type === 'image/png';
            // Only flatten onto white for JPEG (which has no transparency). Filling
            // white for PNG output would destroy the image's transparent areas.
            if (!isPng) { ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, c.width, c.height); }
            ctx.drawImage(img, 0, 0);
            c.toBlob(function (b) {
                var u = URL.createObjectURL(b); var a = document.createElement('a'); a.href = u; a.download = 'clean-photo.' + (isPng ? 'png' : 'jpg'); document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(u); }, 3000);
            }, isPng ? 'image/png' : 'image/jpeg', 0.95);
        };
        img.src = srcUrl;
    });
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFile(fileInput.files && fileInput.files[0]); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer && e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]); });
})();
</script>
@endsection
