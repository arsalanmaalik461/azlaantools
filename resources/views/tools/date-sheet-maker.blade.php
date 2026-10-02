@extends('layouts.app')
@section('title', 'Exam Date Sheet Maker Online Free — Azlaan Tools')
@section('meta_description', 'Make a printable exam date sheet for your school or academy online for free. Add subjects with dates and timings, print or download. No signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Exam Date Sheet Maker</h1>
            <p class="lead text-muted">Make and print a date sheet for your school or academy. Add subjects, dates and timings — get a clean table ready.</p>

            <div class="row g-4">
                <div class="col-12 col-lg-5 no-print">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Exam details</h2>
                            <div class="mb-3">
                                <label for="instName" class="form-label fw-semibold">School / academy name</label>
                                <input type="text" class="form-control" id="instName" placeholder="e.g. City Public School">
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="examTitle" class="form-label fw-semibold">Exam title</label>
                                    <input type="text" class="form-control" id="examTitle" placeholder="e.g. First Term">
                                </div>
                                <div class="col-6">
                                    <label for="className" class="form-label fw-semibold">Class</label>
                                    <input type="text" class="form-control" id="className" placeholder="e.g. 8th">
                                </div>
                            </div>

                            <hr>
                            <h2 class="h5 mb-3">Add paper</h2>
                            <div class="mb-3">
                                <label for="subject" class="form-label fw-semibold">Subject</label>
                                <input type="text" class="form-control" id="subject" placeholder="e.g. Mathematics">
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="paperDate" class="form-label fw-semibold">Date</label>
                                    <input type="date" class="form-control" id="paperDate">
                                </div>
                                <div class="col-3">
                                    <label for="startTime" class="form-label fw-semibold">Start</label>
                                    <input type="time" class="form-control" id="startTime" value="09:00">
                                </div>
                                <div class="col-3">
                                    <label for="endTime" class="form-label fw-semibold">End</label>
                                    <input type="time" class="form-control" id="endTime" value="12:00">
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary w-100 mt-3" id="addBtn">+ Add Paper</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                            <div class="mt-3">
                                <h3 class="h6">Papers added (<span id="paperCount">0</span>)</h3>
                                <ul class="list-group" id="paperList">
                                    <li class="list-group-item text-muted">No papers yet — add one above.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div id="printArea">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white text-center">
                                <div class="fw-bold fs-5" id="pvInst">Your School / Academy</div>
                                <div id="pvExam">Exam Date Sheet</div>
                                <div class="small" id="pvClass"></div>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr><th style="width:8%">#</th><th>Subject</th><th>Date</th><th>Day</th><th>Timing</th></tr>
                                    </thead>
                                    <tbody id="pvRows">
                                        <tr><td colspan="5" class="text-center text-muted">Add papers — your date sheet will appear here.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-muted small d-flex justify-content-between">
                                <span id="pvFooterL">Total papers: 0</span>
                                <span>Prepared with Azlaan Tools</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3 no-print">
                        <button type="button" class="btn btn-success" id="printBtn">Print Date Sheet</button>
                        <button type="button" class="btn btn-outline-primary" id="dlBtn">Download (HTML)</button>
                        <button type="button" class="btn btn-outline-secondary" id="clearBtn">Clear All</button>
                    </div>
                </div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Enter the school or academy name, exam title and class.</li>
                <li>Enter each paper subject, date and timing, then press <strong>+ Add Paper</strong> — the day is calculated automatically.</li>
                <li>Check the preview, then <strong>Print</strong> or <strong>Download</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<style>
@media print {
    .no-print { display: none !important; }
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
<script>
(function () {
    'use strict';
    var addBtn = document.getElementById('addBtn');
    var printBtn = document.getElementById('printBtn');
    var dlBtn = document.getElementById('dlBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var paperList = document.getElementById('paperList');
    var paperCount = document.getElementById('paperCount');

    var papers = [];
    var DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function val(id) {
        return document.getElementById(id).value.trim();
    }

    function prettyDate(iso) {
        var parts = iso.split('-');
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }
    function dayName(iso) {
        var d = new Date(iso + 'T00:00:00');
        return DAYS[d.getDay()];
    }
    function prettyTime(t) {
        var parts = t.split(':');
        var h = parseInt(parts[0], 10);
        var m = parts[1];
        var ap = h >= 12 ? 'PM' : 'AM';
        var h12 = h % 12;
        if (h12 === 0) { h12 = 12; }
        return h12 + ':' + m + ' ' + ap;
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function sortPapers() {
        papers.sort(function (a, b) {
            if (a.date < b.date) { return -1; }
            if (a.date > b.date) { return 1; }
            return 0;
        });
    }

    function render() {
        sortPapers();
        paperCount.textContent = papers.length;
        paperList.innerHTML = '';
        if (papers.length === 0) {
            var li = document.createElement('li');
            li.className = 'list-group-item text-muted';
            li.textContent = 'No papers yet — add one above.';
            paperList.appendChild(li);
        } else {
            papers.forEach(function (p, idx) {
                var item = document.createElement('li');
                item.className = 'list-group-item d-flex justify-content-between align-items-center';
                var span = document.createElement('span');
                span.textContent = p.subject + ' — ' + prettyDate(p.date) + ' (' + prettyTime(p.start) + ' to ' + prettyTime(p.end) + ')';
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-outline-danger';
                btn.textContent = 'Remove';
                btn.setAttribute('data-idx', idx);
                btn.addEventListener('click', function () {
                    papers.splice(parseInt(this.getAttribute('data-idx'), 10), 1);
                    render();
                });
                item.appendChild(span);
                item.appendChild(btn);
                paperList.appendChild(item);
            });
        }

        var inst = val('instName');
        var title = val('examTitle');
        var cls = val('className');
        document.getElementById('pvInst').textContent = inst || 'Your School / Academy';
        document.getElementById('pvExam').textContent = title ? title + ' — Date Sheet' : 'Exam Date Sheet';
        document.getElementById('pvClass').textContent = cls ? 'Class: ' + cls : '';

        var tbody = document.getElementById('pvRows');
        tbody.innerHTML = '';
        if (papers.length === 0) {
            var tr = document.createElement('tr');
            var td = document.createElement('td');
            td.colSpan = 5;
            td.className = 'text-center text-muted';
            td.textContent = 'Add papers — your date sheet will appear here.';
            tr.appendChild(td);
            tbody.appendChild(tr);
        } else {
            papers.forEach(function (p, i) {
                var row = document.createElement('tr');
                var cells = [i + 1, p.subject, prettyDate(p.date), dayName(p.date), prettyTime(p.start) + ' – ' + prettyTime(p.end)];
                cells.forEach(function (c) {
                    var cell = document.createElement('td');
                    cell.textContent = c;
                    row.appendChild(cell);
                });
                tbody.appendChild(row);
            });
        }
        document.getElementById('pvFooterL').textContent = 'Total papers: ' + papers.length;
    }

    addBtn.addEventListener('click', function () {
        hideError();
        var subject = val('subject');
        var date = val('paperDate');
        var start = val('startTime');
        var end = val('endTime');
        if (!subject) { showError('Please enter the subject.'); return; }
        if (!date) { showError('Please select the paper date.'); return; }
        if (!start || !end) { showError('Please enter the start and end timing.'); return; }
        papers.push({ subject: subject, date: date, start: start, end: end });
        document.getElementById('subject').value = '';
        render();
    });

    ['instName', 'examTitle', 'className'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', render);
    });

    printBtn.addEventListener('click', function () {
        render();
        window.print();
    });

    clearBtn.addEventListener('click', function () {
        papers = [];
        render();
        hideError();
    });

    dlBtn.addEventListener('click', function () {
        render();
        var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Date Sheet</title>';
        html += '<style>body{font-family:Arial,sans-serif;padding:24px;color:#222;text-align:center}table{border-collapse:collapse;width:100%;max-width:700px;margin:16px auto}th,td{border:1px solid #555;padding:8px;text-align:left}th{background:#eee}h1{margin-bottom:2px}p.sub{color:#555;margin-top:0}</style></head><body>';
        html += '<h1>' + esc(document.getElementById('pvInst').textContent) + '</h1>';
        html += '<p class="sub"><strong>' + esc(document.getElementById('pvExam').textContent) + '</strong> ' + esc(document.getElementById('pvClass').textContent) + '</p>';
        html += '<table><thead><tr><th>#</th><th>Subject</th><th>Date</th><th>Day</th><th>Timing</th></tr></thead><tbody>';
        papers.forEach(function (p, i) {
            html += '<tr><td>' + (i + 1) + '</td><td>' + esc(p.subject) + '</td><td>' + prettyDate(p.date) + '</td><td>' + dayName(p.date) + '</td><td>' + prettyTime(p.start) + ' - ' + prettyTime(p.end) + '</td></tr>';
        });
        html += '</tbody></table><p class="sub">Total papers: ' + papers.length + '</p></body></html>';
        var blob = new Blob([html], { type: 'text/html' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'date-sheet.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
    });

    render();
})();
</script>
@endsection
