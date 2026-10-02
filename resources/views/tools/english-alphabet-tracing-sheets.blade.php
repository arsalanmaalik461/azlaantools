@extends('layouts.app')

@section('title', 'English Alphabet Tracing Sheets - Azlaan Tools')
@section('meta_description', 'Free printable English alphabet tracing worksheets for kids. A to Z tracing sheets for handwriting practice.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">English Alphabet Tracing Sheets</h1>
            <p class="lead text-muted">A to Z tracing worksheets for kids. Print them and teach children how to write — completely free.</p>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="caseSel" class="form-label fw-semibold">Letter Case</label>
                            <select class="form-select" id="caseSel">
                                <option value="upper">UPPERCASE (A-Z)</option>
                                <option value="lower">lowercase (a-z)</option>
                                <option value="both">Both (A-Z + a-z)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gridSel" class="form-label fw-semibold">Letters Per Row</label>
                            <select class="form-select" id="gridSel">
                                <option value="4">4 per row</option>
                                <option value="6" selected>6 per row</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="guideLines" checked>
                        <label class="form-check-label" for="guideLines">Show red baseline and blue midline guides</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Tracing Sheets</button>
                    <button type="button" class="btn btn-outline-primary w-100 mt-2 d-none" id="printBtn">Print / Save as PDF</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="results" class="d-none">
                <div id="sheetArea"></div>
            </div>

            <div class="no-print">
                <h2>How to use</h2>
                <ol>
                    <li>Select the letter case and letters per row.</li>
                    <li>Press Generate — every letter will appear in dotted tracing style.</li>
                    <li>Press Print to print the worksheet or save it as PDF.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<style>
@media print {
    .no-print, header, footer, nav { display: none !important; }
    .container { max-width: 100% !important; }
}
.trace-cell { border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; text-align: center; background: #fff; page-break-inside: avoid; }
.trace-cell canvas { max-width: 100%; height: auto; }
.trace-word { font-size: 13px; color: #6c757d; margin-top: 4px; }
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var sheetArea = document.getElementById('sheetArea');

    var WORDS = {
        A: 'Apple', B: 'Ball', C: 'Cat', D: 'Dog', E: 'Egg', F: 'Fish', G: 'Grapes',
        H: 'Hat', I: 'Ice', J: 'Jug', K: 'Kite', L: 'Lion', M: 'Mango', N: 'Nest',
        O: 'Orange', P: 'Parrot', Q: 'Queen', R: 'Rabbit', S: 'Sun', T: 'Tiger',
        U: 'Umbrella', V: 'Van', W: 'Watch', X: 'Xylophone', Y: 'Yak', Z: 'Zebra'
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

    function drawLetter(canvas, letter, showGuides) {
        var W = 300, H = 340;
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, W, H);
        if (showGuides) {
            // top line, midline (blue), baseline (red)
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#3b82f6';
            ctx.beginPath(); ctx.moveTo(20, 60); ctx.lineTo(W - 20, 60); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(20, 165); ctx.lineTo(W - 20, 165); ctx.stroke();
            ctx.strokeStyle = '#ef4444';
            ctx.lineWidth = 3;
            ctx.beginPath(); ctx.moveTo(20, 270); ctx.lineTo(W - 20, 270); ctx.stroke();
            ctx.setLineDash([6, 5]);
            ctx.strokeStyle = '#93c5fd';
            ctx.lineWidth = 2;
            ctx.beginPath(); ctx.moveTo(20, 112); ctx.lineTo(W - 20, 112); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(20, 218); ctx.lineTo(W - 20, 218); ctx.stroke();
            ctx.setLineDash([]);
        }
        // dotted letter
        ctx.font = '220px "Times New Roman", Georgia, serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.setLineDash([10, 8]);
        ctx.lineWidth = 5;
        ctx.strokeStyle = '#1f2937';
        ctx.strokeText(letter, W / 2, H / 2 + 10);
        ctx.setLineDash([]);
        // direction arrow hint
        ctx.fillStyle = '#9ca3af';
        ctx.font = '20px sans-serif';
        ctx.fillText(letter + ' trace', W / 2, H - 18);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var mode = document.getElementById('caseSel').value;
        var perRow = parseInt(document.getElementById('gridSel').value, 10);
        var showGuides = document.getElementById('guideLines').checked;

        var letters = [];
        for (var i = 0; i < 26; i++) {
            var up = String.fromCharCode(65 + i);
            var lo = up.toLowerCase();
            if (mode === 'upper') letters.push(up);
            else if (mode === 'lower') letters.push(lo);
            else letters.push(up + '  ' + lo);
        }

        sheetArea.innerHTML = '';
        var colClass = perRow === 4 ? 'col-3' : 'col-4 col-md-2';
        var frag = document.createElement('div');
        frag.className = 'row g-3 mb-4';
        letters.forEach(function (lt) {
            var col = document.createElement('div');
            col.className = colClass;
            var cell = document.createElement('div');
            cell.className = 'trace-cell';
            var cv = document.createElement('canvas');
            var wordKey = lt.trim().charAt(0).toUpperCase();
            cell.appendChild(cv);
            var wd = document.createElement('div');
            wd.className = 'trace-word';
            wd.textContent = wordKey + ' for ' + (WORDS[wordKey] || '');
            cell.appendChild(wd);
            col.appendChild(cell);
            frag.appendChild(col);
            drawLetter(cv, lt, showGuides);
        });
        sheetArea.appendChild(frag);

        results.classList.remove('d-none');
        printBtn.classList.remove('d-none');
    });

    printBtn.addEventListener('click', function () {
        window.print();
    });
})();
</script>
@endsection
