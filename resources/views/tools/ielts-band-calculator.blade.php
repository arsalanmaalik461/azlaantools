@extends('layouts.app')
@section('title', 'IELTS Band Score Calculator Online Free — Azlaan Tools')
@section('meta_description', 'Calculate your IELTS band score from raw scores online for free. Listening, Reading (Academic/General), Writing, Speaking and overall band with official rounding.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">IELTS Band Score Calculator</h1>
            <p class="lead text-muted">Find your band score from your raw score — Listening, Reading, Writing, Speaking and overall band. Free IELTS score calculation for study abroad.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="readingType" class="form-label fw-semibold">Reading test type</label>
                        <select class="form-select" id="readingType">
                            <option value="academic">Academic</option>
                            <option value="general">General Training</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="listening" class="form-label fw-semibold">Listening raw score (0–40)</label>
                            <input type="number" class="form-control" id="listening" min="0" max="40" placeholder="e.g. 30">
                        </div>
                        <div class="col-md-6">
                            <label for="reading" class="form-label fw-semibold">Reading raw score (0–40)</label>
                            <input type="number" class="form-control" id="reading" min="0" max="40" placeholder="e.g. 28">
                        </div>
                        <div class="col-md-6">
                            <label for="writing" class="form-label fw-semibold">Writing band (0–9)</label>
                            <input type="number" class="form-control" id="writing" min="0" max="9" step="0.5" placeholder="e.g. 6.5">
                        </div>
                        <div class="col-md-6">
                            <label for="speaking" class="form-label fw-semibold">Speaking band (0–9)</label>
                            <input type="number" class="form-control" id="speaking" min="0" max="9" step="0.5" placeholder="e.g. 7">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate Band Scores</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h3 class="h5">Your band scores</h3>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Section</th><th>Your score</th><th>Band</th></tr>
                            </thead>
                            <tbody id="bandRows"></tbody>
                        </table>
                        <div class="alert alert-success">
                            <div class="fs-5">Overall band: <strong id="overallBand">—</strong></div>
                            <div class="small text-muted" id="avgNote"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">IELTS scoring rules (official)</h2>
                    <ul class="mb-0">
                        <li>Listening and Reading: your raw score out of 40 questions converts into a band (see the conversion tables below).</li>
                        <li>Writing and Speaking: the examiner gives a 0–9 band directly.</li>
                        <li>Overall band = average of all four sections, rounded to the nearest 0.5 (e.g. 6.25 → 6.5, 6.75 → 7.0).</li>
                    </ul>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your Reading test type (Academic or General Training).</li>
                <li>Enter raw scores (0–40) for Listening and Reading, and band scores for Writing/Speaking.</li>
                <li>Press <strong>Calculate Band Scores</strong> — you will get the section bands and the overall band.</li>
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

    // Tables: [minimum raw score, band]. Checked from high to low.
    var LISTENING = [
        [39, 9], [37, 8.5], [35, 8], [33, 7.5], [30, 7], [27, 6.5], [23, 6],
        [20, 5.5], [18, 5], [16, 4.5], [13, 4], [10, 3.5], [8, 3], [6, 2.5], [4, 2]
    ];
    var READING_AC = [
        [39, 9], [37, 8.5], [35, 8], [33, 7.5], [30, 7], [27, 6.5], [23, 6],
        [19, 5.5], [15, 5], [13, 4.5], [10, 4], [8, 3.5], [6, 3], [4, 2.5]
    ];
    var READING_GT = [
        [40, 9], [39, 8.5], [37, 8], [36, 7.5], [34, 7], [32, 6.5], [30, 6],
        [27, 5.5], [23, 5], [21, 4.5], [18, 4], [15, 3.5], [12, 3], [9, 2.5], [6, 2]
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function rawToBand(raw, table) {
        for (var i = 0; i < table.length; i++) {
            if (raw >= table[i][0]) { return table[i][1]; }
        }
        return 1;
    }

    function bandStr(b) {
        return (Math.round(b * 10) / 10).toFixed(1);
    }

    function checkNum(id, min, max, label) {
        var el = document.getElementById(id);
        var v = el.value.trim();
        if (v === '') { return { ok: false, msg: 'Please enter ' + label + '.' }; }
        var n = parseFloat(v);
        if (isNaN(n) || n < min || n > max) {
            return { ok: false, msg: label + ' must be between ' + min + ' and ' + max + '.' };
        }
        return { ok: true, val: n };
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var l = checkNum('listening', 0, 40, 'Listening raw score');
        if (!l.ok) { showError(l.msg); return; }
        var r = checkNum('reading', 0, 40, 'Reading raw score');
        if (!r.ok) { showError(r.msg); return; }
        var w = checkNum('writing', 0, 9, 'Writing band');
        if (!w.ok) { showError(w.msg); return; }
        var s = checkNum('speaking', 0, 9, 'Speaking band');
        if (!s.ok) { showError(s.msg); return; }

        var type = document.getElementById('readingType').value;
        var lBand = rawToBand(Math.round(l.val), LISTENING);
        var rBand = rawToBand(Math.round(r.val), type === 'academic' ? READING_AC : READING_GT);

        var avg = (lBand + rBand + w.val + s.val) / 4;
        var overall = Math.round(avg * 2) / 2;

        var tbody = document.getElementById('bandRows');
        tbody.innerHTML = '';
        var rows = [
            ['Listening', Math.round(l.val) + ' / 40', lBand],
            ['Reading (' + (type === 'academic' ? 'Academic' : 'General Training') + ')', Math.round(r.val) + ' / 40', rBand],
            ['Writing', bandStr(w.val), w.val],
            ['Speaking', bandStr(s.val), s.val]
        ];
        rows.forEach(function (row) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = row[0];
            var td2 = document.createElement('td'); td2.textContent = row[1];
            var td3 = document.createElement('td');
            var strong = document.createElement('strong'); strong.textContent = bandStr(row[2]);
            td3.appendChild(strong);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            tbody.appendChild(tr);
        });

        document.getElementById('overallBand').textContent = bandStr(overall);
        document.getElementById('avgNote').textContent = 'The average was ' + (Math.round(avg * 100) / 100) + ', rounded to the nearest 0.5 it becomes ' + bandStr(overall) + '.';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
