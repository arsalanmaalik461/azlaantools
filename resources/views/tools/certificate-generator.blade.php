@extends('layouts.app')

@section('title', 'Certificate Generator Online Free - Azlaan Tools')
@section('meta_description', 'Generate printable certificates online free. Create course or achievement certificates and download or print them.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Certificate Generator</h1>
            <p class="lead text-muted">Create a beautiful certificate for a course or achievement, then download or print it. Enter the name and details, choose a design, and it is ready!</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="recName" class="form-label fw-semibold">Recipient's Name</label>
                            <input type="text" class="form-control" id="recName" placeholder="e.g. Muhammad Ali">
                        </div>
                        <div class="col-md-6">
                            <label for="courseName" class="form-label fw-semibold">Course / Achievement</label>
                            <input type="text" class="form-control" id="courseName" placeholder="e.g. Web Development Course">
                        </div>
                        <div class="col-md-6">
                            <label for="issuerName" class="form-label fw-semibold">Issuing Organization / Person</label>
                            <input type="text" class="form-control" id="issuerName" placeholder="e.g. Azlaan Training Center">
                        </div>
                        <div class="col-md-6">
                            <label for="certDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="certDate">
                        </div>
                        <div class="col-md-6">
                            <label for="templateSelect" class="form-label fw-semibold">Design</label>
                            <select class="form-select" id="templateSelect">
                                <option value="gold">Classic Gold</option>
                                <option value="blue">Modern Blue</option>
                                <option value="green">Elegant Green</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="certTitle" class="form-label fw-semibold">Certificate Title</label>
                            <input type="text" class="form-control" id="certTitle" value="Certificate of Achievement">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Create Certificate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label class="form-label fw-semibold">Preview</label>
                        <canvas id="certCanvas" width="1200" height="850" class="img-fluid w-100 rounded border"></canvas>
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <button type="button" class="btn btn-success" id="downloadBtn">Download PNG</button>
                            <button type="button" class="btn btn-outline-primary" id="printBtn">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the name, course, issuing organization and date.</li>
                <li>Choose your favorite design and press "Create Certificate".</li>
                <li>See the preview, then download the PNG or print it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var recName = document.getElementById('recName');
    var courseName = document.getElementById('courseName');
    var issuerName = document.getElementById('issuerName');
    var certDate = document.getElementById('certDate');
    var templateSelect = document.getElementById('templateSelect');
    var certTitle = document.getElementById('certTitle');
    var goBtn = document.getElementById('goBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var canvas = document.getElementById('certCanvas');
    var ctx = canvas.getContext('2d');

    var THEMES = {
        gold: { main: '#8a6d1b', accent: '#c9a227', bg1: '#fffdf5', bg2: '#f7f0dc', text: '#3a2f10' },
        blue: { main: '#1d4ed8', accent: '#3b82f6', bg1: '#f5f9ff', bg2: '#e3edfb', text: '#16294d' },
        green: { main: '#15803d', accent: '#22c55e', bg1: '#f4fbf6', bg2: '#e0f2e7', text: '#123f24' }
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function fitText(text, maxWidth, baseSize, weight) {
        var size = baseSize;
        ctx.font = weight + ' ' + size + 'px Georgia, serif';
        while (ctx.measureText(text).width > maxWidth && size > 20) {
            size -= 4;
            ctx.font = weight + ' ' + size + 'px Georgia, serif';
        }
        return size;
    }

    function drawCertificate() {
        var t = THEMES[templateSelect.value] || THEMES.gold;
        var W = canvas.width, H = canvas.height;

        var grad = ctx.createLinearGradient(0, 0, 0, H);
        grad.addColorStop(0, t.bg1);
        grad.addColorStop(1, t.bg2);
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, W, H);

        // Borders
        ctx.strokeStyle = t.main;
        ctx.lineWidth = 10;
        ctx.strokeRect(24, 24, W - 48, H - 48);
        ctx.strokeStyle = t.accent;
        ctx.lineWidth = 3;
        ctx.strokeRect(48, 48, W - 96, H - 96);

        // Corner ornaments
        ctx.fillStyle = t.accent;
        [[48, 48], [W - 48, 48], [48, H - 48], [W - 48, H - 48]].forEach(function (p) {
            ctx.beginPath();
            ctx.arc(p[0], p[1], 12, 0, Math.PI * 2);
            ctx.fill();
        });

        ctx.textAlign = 'center';
        ctx.fillStyle = t.text;

        var size = fitText(certTitle.value, W - 260, 64, 'bold');
        ctx.font = 'bold ' + size + 'px Georgia, serif';
        ctx.fillStyle = t.main;
        ctx.fillText(certTitle.value, W / 2, 190);

        ctx.font = '28px Georgia, serif';
        ctx.fillStyle = t.text;
        ctx.fillText('This certificate is proudly presented to', W / 2, 280);

        size = fitText(recName.value, W - 220, 84, 'bold');
        ctx.font = 'bold ' + size + 'px Georgia, serif';
        ctx.fillStyle = t.main;
        ctx.fillText(recName.value, W / 2, 400);

        ctx.strokeStyle = t.accent;
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(W / 2 - 260, 430);
        ctx.lineTo(W / 2 + 260, 430);
        ctx.stroke();

        ctx.font = '30px Georgia, serif';
        ctx.fillStyle = t.text;
        ctx.fillText('for successfully completing', W / 2, 490);

        size = fitText(courseName.value, W - 260, 48, 'bold');
        ctx.font = 'bold ' + size + 'px Georgia, serif';
        ctx.fillStyle = t.main;
        ctx.fillText(courseName.value, W / 2, 570);

        var dateStr = certDate.value ? certDate.value.split('-').reverse().join('-') : '';
        ctx.font = '26px Georgia, serif';
        ctx.fillStyle = t.text;
        ctx.fillText(dateStr, W / 2 - 280, 720);
        ctx.fillText(issuerName.value, W / 2 + 280, 720);

        ctx.font = 'italic 22px Georgia, serif';
        ctx.fillStyle = '#6b7280';
        ctx.fillText('Date', W / 2 - 280, 752);
        ctx.fillText('Issued by', W / 2 + 280, 752);

        ctx.strokeStyle = '#9ca3af';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(W / 2 - 420, 700);
        ctx.lineTo(W / 2 - 140, 700);
        ctx.moveTo(W / 2 + 140, 700);
        ctx.lineTo(W / 2 + 420, 700);
        ctx.stroke();

        // Seal
        ctx.beginPath();
        ctx.arc(W / 2, 648, 44, 0, Math.PI * 2);
        ctx.fillStyle = t.accent;
        ctx.fill();
        ctx.beginPath();
        ctx.arc(W / 2, 648, 34, 0, Math.PI * 2);
        ctx.fillStyle = t.main;
        ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 22px Georgia, serif';
        ctx.fillText('★', W / 2, 657);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!recName.value.trim()) { showError('Please enter the recipient name.'); return; }
        if (!courseName.value.trim()) { showError('Please enter the course or achievement.'); return; }
        if (!issuerName.value.trim()) { showError('Please enter the issuer name.'); return; }
        drawCertificate();
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    templateSelect.addEventListener('change', function () {
        if (!results.classList.contains('d-none') && recName.value.trim()) {
            drawCertificate();
        }
    });

    downloadBtn.addEventListener('click', function () {
        var a = document.createElement('a');
        a.href = canvas.toDataURL('image/png');
        a.download = 'certificate.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    printBtn.addEventListener('click', function () {
        var win = window.open('', '_blank');
        var html = '<html><head><title>Certificate</title><style>body{margin:0;text-align:center;}img{max-width:100%;}</style></head>' +
            '<body><img src="' + canvas.toDataURL('image/png') + '"></body></html>';
        win.document.write(html);
        win.document.close();
        win.focus();
        setTimeout(function () { win.print(); }, 500);
    });
})();
</script>
@endsection
