@extends('layouts.app')

@section('title', 'Multiplication Table Chart - Azlaan Tools')
@section('meta_description', 'Printable multiplication tables 2 to 20 with blank practice grids. Free chart to learn multiplication tables.')

@section('content')
<style>
@media print {
    .no-print { display: none !important; }
    .print-card { break-inside: avoid; box-shadow: none !important; border: 1px solid #ccc !important; }
    .container { max-width: 100% !important; }
}
.practice-cell {
    display: inline-block; min-width: 64px; border-bottom: 2px solid #333; height: 1.4em;
}
.tbl-row { line-height: 2; }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="no-print">
                <h1 class="mb-3">Multiplication Table Chart</h1>
                <p class="lead text-muted">Tables from 2 to 20 — view the chart, or make a blank practice sheet and print it. Great for children.</p>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <label for="fromTbl" class="form-label fw-semibold">Table from</label>
                                <input type="number" class="form-control" id="fromTbl" min="2" max="20" value="2">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="toTbl" class="form-label fw-semibold">Table to</label>
                                <input type="number" class="form-control" id="toTbl" min="2" max="20" value="10">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="uptoMult" class="form-label fw-semibold">Multiply up to</label>
                                <select class="form-select" id="uptoMult">
                                    <option value="10" selected>Up to 10</option>
                                    <option value="12">Up to 12</option>
                                    <option value="20">Up to 20</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="modeSel" class="form-label fw-semibold">Mode</label>
                                <select class="form-select" id="modeSel">
                                    <option value="answers" selected>Chart (with answers)</option>
                                    <option value="blank">Practice (blank spaces)</option>
                                </select>
                                <div class="form-text">In practice mode the answers stay blank — write them yourself to learn.</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-primary flex-fill" id="goBtn">Generate Chart</button>
                            <button type="button" class="btn btn-outline-secondary" id="printBtn">Print</button>
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    </div>
                </div>
            </div>

            <div id="results" class="d-none mt-2">
                <div class="row g-3" id="chartGrid"></div>
            </div>

            <div class="no-print">
                <h2 class="mt-4">How to use</h2>
                <ol>
                    <li>Enter which tables you want — from/to (2 to 20).</li>
                    <li>View with answers in Chart mode, or blank spaces in Practice mode.</li>
                    <li>Press <strong>Generate Chart</strong>, then <strong>Print</strong> and put it on the wall or copy it into your notebook.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var fromTbl = document.getElementById('fromTbl');
    var toTbl = document.getElementById('toTbl');
    var uptoMult = document.getElementById('uptoMult');
    var modeSel = document.getElementById('modeSel');
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var chartGrid = document.getElementById('chartGrid');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function build() {
        hideError();
        var from = parseInt(fromTbl.value, 10);
        var to = parseInt(toTbl.value, 10);
        var upto = parseInt(uptoMult.value, 10);
        var blank = modeSel.value === 'blank';
        if (isNaN(from) || isNaN(to) || from < 2 || to > 20) { showError('Table must be between 2 and 20.'); return; }
        if (from > to) { showError('"From" table cannot be bigger than "To".'); return; }
        if (to - from > 18) { showError('You cannot select more than 19 tables at one time.'); return; }
        chartGrid.innerHTML = '';
        for (var t = from; t <= to; t++) {
            var col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-md-4';
            var card = document.createElement('div');
            card.className = 'card print-card h-100';
            var body = document.createElement('div');
            body.className = 'card-body';
            var h = document.createElement('h3');
            h.className = 'h5 text-center mb-3';
            h.textContent = 'Table of ' + t + (blank ? ' (Practice)' : '');
            body.appendChild(h);
            for (var m = 1; m <= upto; m++) {
                var row = document.createElement('div');
                row.className = 'tbl-row d-flex justify-content-between px-2 border-bottom';
                var left = document.createElement('span');
                left.textContent = t + ' x ' + m + ' =';
                var right;
                if (blank) {
                    right = document.createElement('span');
                    right.className = 'practice-cell';
                } else {
                    right = document.createElement('strong');
                    right.textContent = t * m;
                }
                row.appendChild(left);
                row.appendChild(right);
                body.appendChild(row);
            }
            card.appendChild(body);
            col.appendChild(card);
            chartGrid.appendChild(col);
        }
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', build);
    printBtn.addEventListener('click', function () {
        hideError();
        if (results.classList.contains('d-none')) { showError('Press Generate Chart first.'); return; }
        window.print();
    });
    build();
})();
</script>
@endsection
