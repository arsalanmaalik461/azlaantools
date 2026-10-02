@extends('layouts.app')
@section('title', 'Student ID Card Maker - Printable School ID Cards | Azlaan Tools')
@section('meta_description', 'Design printable student ID cards for free with photo, name, class and roll number. Add a whole class in batch and download all cards as a ZIP.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Student ID Card Maker</h1>
            <p class="lead text-muted">Make printable student ID cards for your school — with photo, name, class and roll number. Add a whole class at once and download all the cards as a ZIP.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="schoolName" class="form-label fw-semibold">School name</label>
                            <input type="text" class="form-control" id="schoolName" placeholder="e.g. Govt High School Faisalabad">
                        </div>
                        <div class="col-md-6">
                            <label for="stPhoto" class="form-label fw-semibold">Student photo</label>
                            <input type="file" class="form-control" id="stPhoto" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label for="stName" class="form-label fw-semibold">Student name</label>
                            <input type="text" class="form-control" id="stName" placeholder="e.g. Ahmed Raza">
                        </div>
                        <div class="col-md-3">
                            <label for="stClass" class="form-label fw-semibold">Class</label>
                            <input type="text" class="form-control" id="stClass" placeholder="e.g. 8th">
                        </div>
                        <div class="col-md-3">
                            <label for="stRoll" class="form-label fw-semibold">Roll number</label>
                            <input type="text" class="form-control" id="stRoll" placeholder="e.g. 24">
                        </div>
                        <div class="col-md-6">
                            <label for="stSection" class="form-label fw-semibold">Section (optional)</label>
                            <input type="text" class="form-control" id="stSection" placeholder="e.g. A">
                        </div>
                        <div class="col-md-6">
                            <label for="stSession" class="form-label fw-semibold">Session</label>
                            <input type="text" class="form-control" id="stSession" placeholder="e.g. 2026-27" value="2026-27">
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="goBtn">Generate Card</button>
                        <button type="button" class="btn btn-outline-primary" id="batchBtn">+ Add to Batch</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="successBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Card Preview</h2>
                        <canvas id="cardCanvas" width="856" height="540" class="img-fluid border rounded mb-3" style="max-width:100%;height:auto;"></canvas>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-success" id="dlOneBtn">⬇ Download This Card (PNG)</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="batchWrap" class="card shadow-sm mb-4 d-none">
                <div class="card-body">
                    <h2 class="h5">Batch (<span id="batchCount">0</span> cards)</h2>
                    <ul class="list-group mb-3" id="batchList"></ul>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" id="dlZipBtn">⬇ Download All as ZIP</button>
                        <button type="button" class="btn btn-outline-danger" id="clearBatchBtn">Clear Batch</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the school name, student name, class, roll number and select a photo.</li>
                <li>Press <strong>Generate Card</strong> — the preview will appear below.</li>
                <li>If the card looks right, get the PNG with <strong>Download This Card</strong>.</li>
                <li>For a whole class, add each student with <strong>Add to Batch</strong>, then press <strong>Download All as ZIP</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(function () {
    'use strict';
    var schoolName = document.getElementById('schoolName');
    var stPhoto = document.getElementById('stPhoto');
    var stName = document.getElementById('stName');
    var stClass = document.getElementById('stClass');
    var stRoll = document.getElementById('stRoll');
    var stSection = document.getElementById('stSection');
    var stSession = document.getElementById('stSession');
    var goBtn = document.getElementById('goBtn');
    var batchBtn = document.getElementById('batchBtn');
    var dlOneBtn = document.getElementById('dlOneBtn');
    var errorBox = document.getElementById('errorBox');
    var successBox = document.getElementById('successBox');
    var results = document.getElementById('results');
    var cardCanvas = document.getElementById('cardCanvas');
    var batchWrap = document.getElementById('batchWrap');
    var batchList = document.getElementById('batchList');
    var batchCount = document.getElementById('batchCount');
    var dlZipBtn = document.getElementById('dlZipBtn');
    var clearBatchBtn = document.getElementById('clearBatchBtn');

    var CW = 856, CH = 540;
    var batch = [];
    var lastDataUrl = null;
    var photoImg = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function hideAlerts() {
        errorBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }

    function readData() {
        return {
            school: schoolName.value.trim() || 'SCHOOL NAME',
            name: stName.value.trim(),
            cls: stClass.value.trim(),
            roll: stRoll.value.trim(),
            section: stSection.value.trim(),
            session: stSession.value.trim() || '2026-27'
        };
    }
    function validate(d) {
        if (!d.name) return 'Please enter the student name.';
        if (!d.cls) return 'Please enter the class.';
        if (!d.roll) return 'Please enter the roll number.';
        return null;
    }

    function loadPhoto(file, cb) {
        if (!file) { cb(null); return; }
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var s = Math.min(img.naturalWidth, img.naturalHeight);
            var sx = (img.naturalWidth - s) / 2, sy = (img.naturalHeight - s) / 2;
            var c = document.createElement('canvas');
            c.width = 240; c.height = 240;
            c.getContext('2d').drawImage(img, sx, sy, s, s, 0, 0, 240, 240);
            URL.revokeObjectURL(url);
            var sq = new Image();
            sq.onload = function () { cb(sq); };
            sq.src = c.toDataURL('image/jpeg', 0.9);
        };
        img.onerror = function () { URL.revokeObjectURL(url); cb(null); };
        img.src = url;
    }

    function roundRect(ctx, x, y, w, h, r) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    }

    function drawCard(ctx, d, photo) {
        // background
        var g = ctx.createLinearGradient(0, 0, CW, CH);
        g.addColorStop(0, '#f8fafc');
        g.addColorStop(1, '#e8eef7');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, CW, CH);
        // header band
        ctx.fillStyle = '#1d4ed8';
        ctx.fillRect(0, 0, CW, 110);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 44px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(d.school.toUpperCase().slice(0, 34), CW / 2, 48);
        ctx.font = '24px Arial, sans-serif';
        ctx.fillStyle = '#dbeafe';
        ctx.fillText('STUDENT IDENTITY CARD', CW / 2, 86);
        // photo
        var px = 60, py = 150, ps = 190;
        ctx.fillStyle = '#ffffff';
        roundRect(ctx, px - 10, py - 10, ps + 20, ps + 20, 16);
        ctx.fill();
        ctx.strokeStyle = '#1d4ed8';
        ctx.lineWidth = 4;
        ctx.stroke();
        if (photo) {
            ctx.save();
            roundRect(ctx, px, py, ps, ps, 10);
            ctx.clip();
            ctx.drawImage(photo, px, py, ps, ps);
            ctx.restore();
        } else {
            ctx.fillStyle = '#cbd5e1';
            ctx.fillRect(px, py, ps, ps);
            ctx.fillStyle = '#64748b';
            ctx.font = '28px Arial';
            ctx.fillText('PHOTO', px + ps / 2, py + ps / 2 + 10);
        }
        // fields
        var fx = 300, fy = 180;
        ctx.textAlign = 'left';
        function row(label, value, y, big) {
            ctx.fillStyle = '#64748b';
            ctx.font = '22px Arial, sans-serif';
            ctx.fillText(label, fx, y);
            ctx.fillStyle = '#0f172a';
            ctx.font = (big ? 'bold 34px' : 'bold 30px') + ' Arial, sans-serif';
            ctx.fillText(String(value).slice(0, 30), fx, y + (big ? 42 : 38));
        }
        row('NAME', d.name, fy, true);
        row('CLASS', d.cls + (d.section ? '  •  SECTION ' + d.section : ''), fy + 92, false);
        row('ROLL NO', d.roll, fy + 184, false);
        // footer strip
        ctx.fillStyle = '#1d4ed8';
        ctx.fillRect(0, CH - 70, CW, 70);
        ctx.fillStyle = '#ffffff';
        ctx.font = '24px Arial, sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText('Session: ' + d.session, 40, CH - 26);
        ctx.textAlign = 'right';
        ctx.fillText('Principal Signature: __________', CW - 40, CH - 26);
        // border
        ctx.strokeStyle = '#1d4ed8';
        ctx.lineWidth = 8;
        ctx.strokeRect(4, 4, CW - 8, CH - 8);
    }

    function generate(cb) {
        hideAlerts();
        var d = readData();
        var err = validate(d);
        if (err) { showError(err); return; }
        loadPhoto(stPhoto.files[0], function (photo) {
            photoImg = photo;
            var ctx = cardCanvas.getContext('2d');
            drawCard(ctx, d, photo);
            lastDataUrl = cardCanvas.toDataURL('image/png');
            results.classList.remove('d-none');
            showSuccess('Card generated.');
            if (cb) cb(d, lastDataUrl);
        });
    }

    goBtn.addEventListener('click', function () { generate(); });

    function renderBatch() {
        batchCount.textContent = batch.length;
        batchWrap.classList.toggle('d-none', batch.length === 0);
        batchList.innerHTML = '';
        batch.forEach(function (b, i) {
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = '<span><strong></strong></span>';
            li.querySelector('strong').textContent = b.name + ' — Class ' + b.cls + ', Roll ' + b.roll;
            var rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'btn btn-sm btn-outline-danger';
            rm.textContent = 'Remove';
            rm.addEventListener('click', function () { batch.splice(i, 1); renderBatch(); });
            li.appendChild(rm);
            batchList.appendChild(li);
        });
    }

    batchBtn.addEventListener('click', function () {
        generate(function (d, dataUrl) {
            batch.push({ name: d.name, cls: d.cls, roll: d.roll, dataUrl: dataUrl });
            renderBatch();
            showSuccess('Added to batch: ' + d.name + ' (' + batch.length + ' total)');
        });
    });

    dlOneBtn.addEventListener('click', function () {
        if (!lastDataUrl) return;
        var a = document.createElement('a');
        a.href = lastDataUrl;
        a.download = 'id-card-' + (stRoll.value.trim() || 'student') + '.png';
        document.body.appendChild(a);
        a.click();
        a.remove();
    });

    dlZipBtn.addEventListener('click', function () {
        hideAlerts();
        if (!batch.length) { showError('Batch is empty.'); return; }
        if (typeof JSZip === 'undefined') { showError('ZIP library could not load. Please check your internet.'); return; }
        dlZipBtn.disabled = true;
        dlZipBtn.textContent = 'Preparing ZIP...';
        var zip = new JSZip();
        var folder = zip.folder('id-cards');
        batch.forEach(function (b, i) {
            var base64 = b.dataUrl.split(',')[1];
            var safe = b.name.replace(/[^a-z0-9]+/gi, '-').toLowerCase() || 'card';
            folder.file((i + 1) + '-' + safe + '-roll-' + b.roll + '.png', base64, { base64: true });
        });
        zip.generateAsync({ type: 'blob' }).then(function (blob) {
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'student-id-cards.zip';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 3000);
            showSuccess(batch.length + ' cards downloaded as ZIP.');
        }).catch(function () {
            showError('Failed to create ZIP.');
        }).then(function () {
            dlZipBtn.disabled = false;
            dlZipBtn.textContent = '⬇ Download All as ZIP';
        });
    });

    clearBatchBtn.addEventListener('click', function () {
        batch = [];
        renderBatch();
        hideAlerts();
    });
})();
</script>
@endsection
