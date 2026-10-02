@extends('layouts.app')
@section('title', 'Urdu Alphabet Tracing Sheets - Azlaan Tools')
@section('meta_description', 'Make Urdu letter tracing sheets. From Alif to Ye, print and teach kids. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Urdu Alphabet Tracing Sheets</h1>
            <p class="lead text-muted">Make tracing sheets of Urdu letters (from Alif to Ye) — kids learn by tracing each letter with a finger or pencil. Print and use them.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fromLetter" class="form-label fw-semibold">First letter</label>
                            <select class="form-select" id="fromLetter"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="toLetter" class="form-label fw-semibold">Last letter</label>
                            <select class="form-select" id="toLetter"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="sheetStyle" class="form-label fw-semibold">Letter style</label>
                            <select class="form-select" id="sheetStyle">
                                <option value="dashed">Dotted (for tracing)</option>
                                <option value="outline">Outline (hollow letter)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sheetCols" class="form-label fw-semibold">Letters per row</label>
                            <select class="form-select" id="sheetCols">
                                <option value="3">3 (big letters)</option>
                                <option value="4" selected>4</option>
                                <option value="5">5 (small letters)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="practiceRows" checked>
                        <label class="form-check-label" for="practiceRows">Also show 2 small practice lines under each letter</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate Tracing Sheet</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="sheetInfo" role="alert"></div>
                        <canvas id="sheetCanvas" class="img-fluid border rounded w-100 bg-white"></canvas>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-success flex-fill" id="sheetDownload">Download PNG</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="sheetPrint">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the first and last letter (the full alphabet or some letters).</li>
                <li>Choose style and size, then press Generate.</li>
                <li>Download or print the sheet and get kids to trace it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    var LETTERS = [
        ['\u0627', 'alif'], ['\u0628', 'bay'], ['\u067E', 'pay'], ['\u062A', 'tay'],
        ['\u0679', 'tay-bhari'], ['\u062B', 'say'], ['\u062C', 'jeem'], ['\u0686', 'chay'],
        ['\u062D', 'hay'], ['\u062E', 'khay'], ['\u062F', 'daal'], ['\u0688', 'ddaal'],
        ['\u0630', 'zaal'], ['\u0631', 'ray'], ['\u0691', 'ray-bhari'], ['\u0632', 'zay'],
        ['\u0698', 'zhay'], ['\u0633', 'seen'], ['\u0634', 'sheen'], ['\u0635', 'suaad'],
        ['\u0636', 'zuaad'], ['\u0637', 'toy'], ['\u0638', 'zoy'], ['\u0639', 'ain'],
        ['\u063A', 'ghain'], ['\u0641', 'fay'], ['\u0642', 'qaaf'], ['\u06A9', 'kaaf'],
        ['\u06AF', 'gaaf'], ['\u0644', 'laam'], ['\u0645', 'meem'], ['\u0646', 'noon'],
        ['\u06BA', 'noon-ghunna'], ['\u0648', 'wao'], ['\u06C1', 'chhoti-hay'],
        ['\u06BE', 'do-chashmi-hay'], ['\u0621', 'hamza'], ['\u06CC', 'chhoti-yay'], ['\u06D2', 'bari-yay']
    ];

    var fromSel = document.getElementById('fromLetter');
    var toSel = document.getElementById('toLetter');
    LETTERS.forEach(function (L, i) {
        var o1 = document.createElement('option'); o1.value = i; o1.textContent = L[0] + ' (' + L[1] + ')'; fromSel.appendChild(o1);
        var o2 = document.createElement('option'); o2.value = i; o2.textContent = L[0] + ' (' + L[1] + ')'; toSel.appendChild(o2);
    });
    toSel.value = String(LETTERS.length - 1);

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function drawLetter(ctx, ch, x, y, size, dashed) {
        ctx.save();
        ctx.font = size + 'px "Noto Nastaliq Urdu", "Jameel Noori Nastaleeq", "Urdu Typesetting", serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        if (dashed) ctx.setLineDash([size / 14, size / 16]);
        ctx.lineWidth = Math.max(2, size / 60);
        ctx.strokeStyle = '#555';
        ctx.fillStyle = 'rgba(0,0,0,0)';
        ctx.strokeText(ch, x, y);
        ctx.setLineDash([]);
        ctx.restore();
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var from = parseInt(fromSel.value, 10), to = parseInt(toSel.value, 10);
        if (from > to) { showError('First letter must come before the last.'); return; }
        var cols = parseInt(document.getElementById('sheetCols').value, 10);
        var dashed = document.getElementById('sheetStyle').value === 'dashed';
        var practice = document.getElementById('practiceRows').checked;
        var set = LETTERS.slice(from, to + 1);

        var W = 1240, H = 1754; // A4 ratio
        var cv = document.getElementById('sheetCanvas');
        cv.width = W; cv.height = H;
        var ctx = cv.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);

        ctx.fillStyle = '#222';
        ctx.font = 'bold 44px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Urdu Letters Tracing Sheet', W / 2, 70);
        ctx.font = '26px Arial, sans-serif';
        ctx.fillStyle = '#666';
        ctx.fillText('Trace each letter with a pencil', W / 2, 110);

        var top = 150, bottomPad = 60;
        var cellW = W / cols;
        var rows = Math.ceil(set.length / cols);
        var cellH = (H - top - bottomPad) / rows;
        var big = Math.min(cellW * 0.42, cellH * 0.34);

        set.forEach(function (L, i) {
            var r = Math.floor(i / cols), c = i % cols;
            var x = c * cellW + cellW / 2;
            var y = top + r * cellH + cellH / 2 - (practice ? 14 : 0);
            ctx.strokeStyle = '#ddd';
            ctx.lineWidth = 2;
            ctx.strokeRect(c * cellW + 12, top + r * cellH + 8, cellW - 24, cellH - 16);
            drawLetter(ctx, L[0], x, y - cellH * 0.08, big, dashed);
            ctx.fillStyle = '#888';
            ctx.font = '24px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(L[1], x, y + big * 0.55);
            if (practice) {
                for (var p = 0; p < 2; p++) {
                    var py = y + big * 0.75 + p * (big * 0.42);
                    drawLetter(ctx, L[0], x, py, big * 0.42, true);
                    ctx.strokeStyle = '#e0e0e0';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(c * cellW + 40, py + big * 0.24);
                    ctx.lineTo(c * cellW + cellW - 40, py + big * 0.24);
                    ctx.stroke();
                }
            }
        });

        document.getElementById('sheetInfo').textContent = set.length + ' letters are ready. Print the sheet and get kids to trace them.';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('sheetDownload').addEventListener('click', function () {
        var cv = document.getElementById('sheetCanvas');
        cv.toBlob(function (blob) {
            if (!blob) return;
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'urdu-tracing-sheet.png';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
        }, 'image/png');
    });
    document.getElementById('sheetPrint').addEventListener('click', function () { window.print(); });
})();
</script>
@endsection
