@extends('layouts.app')

@section('title', 'Cornell Notes Template - Azlaan Tools')
@section('meta_description', 'Write notes in Cornell style and print them: cues, notes and summary columns. Free online lecture notes tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Cornell Notes Template</h1>
            <p class="lead text-muted">Write lecture notes in Cornell style — cues, notes and summary in separate columns. Print-friendly, and your notes are saved.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <span class="fw-semibold">Page:</span>
                        <div class="btn-group" id="pageTabs" role="group" aria-label="Note pages"></div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="addPageBtn">+ New page</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="delPageBtn">Delete page</button>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-auto" id="printBtn">Print</button>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="noteTitle" class="form-label fw-semibold">Topic</label>
                            <input type="text" class="form-control" id="noteTitle" placeholder="e.g. Biology — Chapter 5">
                        </div>
                        <div class="col-md-3">
                            <label for="noteDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="noteDate">
                        </div>
                        <div class="col-md-3">
                            <label for="noteCourse" class="form-label fw-semibold">Course / Class</label>
                            <input type="text" class="form-control" id="noteCourse" placeholder="e.g. 10th">
                        </div>
                    </div>

                    <div class="border rounded overflow-hidden">
                        <div class="row g-0">
                            <div class="col-4 col-md-3 border-end bg-light">
                                <div class="p-2 fw-semibold small text-muted border-bottom">CUES — questions / keywords</div>
                                <textarea class="form-control border-0 rounded-0" id="cuesBox" rows="14" placeholder="Write important questions or keywords here..."></textarea>
                            </div>
                            <div class="col-8 col-md-9">
                                <div class="p-2 fw-semibold small text-muted border-bottom">NOTES — lecture notes</div>
                                <textarea class="form-control border-0 rounded-0" id="notesBox" rows="14" placeholder="Write detailed notes here..."></textarea>
                            </div>
                        </div>
                        <div class="border-top">
                            <div class="p-2 fw-semibold small text-muted border-bottom bg-light">SUMMARY — write this after the lecture</div>
                            <textarea class="form-control border-0 rounded-0" id="summaryBox" rows="4" placeholder="Summary in 2-3 sentences..."></textarea>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Save Page</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="okBox" role="status"></div>
                    <div id="results" class="d-none"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the topic and date, then fill in the cues, notes and summary.</li>
                <li>Press "Save Page" — the page is saved in this browser.</li>
                <li>Use "Print" to get a clean printed copy.</li>
            </ol>
            <h2>What is the Cornell method?</h2>
            <p>During the lecture, write the details in the <strong>notes</strong> column. After the lecture, write questions / keywords in <strong>cues</strong> and a summary at the bottom. When revising, look only at the cues and try to remember the answers — a great way to improve memory.</p>
            <p class="small text-muted">Note: notes are saved only in this browser (local storage).</p>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printSheet, #printSheet * { visibility: visible; }
    #printSheet { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
<div id="printSheet" class="d-none"></div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'cornellNotes_v1';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var okBox = document.getElementById('okBox');
    var current = 0;

    document.getElementById('noteDate').value = new Date().toISOString().slice(0, 10);

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            var arr = raw ? JSON.parse(raw) : null;
            if (!arr || !arr.length) return [{ title: '', date: new Date().toISOString().slice(0, 10), course: '', cues: '', notes: '', summary: '' }];
            return arr;
        } catch (e) {
            return [{ title: '', date: '', course: '', cues: '', notes: '', summary: '' }];
        }
    }
    function persist(pages) {
        try { localStorage.setItem(KEY, JSON.stringify(pages)); } catch (e) { /* full */ }
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        okBox.classList.add('d-none');
    }
    function showOk(msg) {
        okBox.textContent = msg;
        okBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
        setTimeout(function () { okBox.classList.add('d-none'); }, 2500);
    }
    function esc(s) {
        return String(s || '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function nl2br(s) { return esc(s).replace(/\n/g, '<br>'); }

    function readForm() {
        return {
            title: document.getElementById('noteTitle').value,
            date: document.getElementById('noteDate').value,
            course: document.getElementById('noteCourse').value,
            cues: document.getElementById('cuesBox').value,
            notes: document.getElementById('notesBox').value,
            summary: document.getElementById('summaryBox').value
        };
    }
    function fillForm(p) {
        document.getElementById('noteTitle').value = p.title || '';
        document.getElementById('noteDate').value = p.date || '';
        document.getElementById('noteCourse').value = p.course || '';
        document.getElementById('cuesBox').value = p.cues || '';
        document.getElementById('notesBox').value = p.notes || '';
        document.getElementById('summaryBox').value = p.summary || '';
    }

    function renderTabs(pages) {
        var tabs = document.getElementById('pageTabs');
        tabs.innerHTML = '';
        pages.forEach(function (p, i) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'btn btn-sm ' + (i === current ? 'btn-primary' : 'btn-outline-secondary');
            b.textContent = p.title ? p.title.slice(0, 14) : 'Page ' + (i + 1);
            b.title = p.title || ('Page ' + (i + 1));
            (function (idx) {
                b.addEventListener('click', function () {
                    var ps = load();
                    ps[current] = readForm();
                    persist(ps);
                    current = idx;
                    fillForm(ps[current]);
                    renderTabs(ps);
                });
            })(i);
            tabs.appendChild(b);
        });
    }

    goBtn.addEventListener('click', function () {
        errorBox.classList.add('d-none');
        var pages = load();
        pages[current] = readForm();
        persist(pages);
        renderTabs(pages);
        showOk('Page saved.');
    });

    document.getElementById('addPageBtn').addEventListener('click', function () {
        var pages = load();
        pages[current] = readForm();
        pages.push({ title: '', date: new Date().toISOString().slice(0, 10), course: '', cues: '', notes: '', summary: '' });
        current = pages.length - 1;
        persist(pages);
        fillForm(pages[current]);
        renderTabs(pages);
    });

    document.getElementById('delPageBtn').addEventListener('click', function () {
        var pages = load();
        if (pages.length <= 1) { showError('The last page cannot be deleted.'); return; }
        pages.splice(current, 1);
        current = Math.max(0, current - 1);
        persist(pages);
        fillForm(pages[current]);
        renderTabs(pages);
        showOk('Page deleted.');
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        var pages = load();
        pages[current] = readForm();
        persist(pages);
        var p = pages[current];
        var sheet = document.getElementById('printSheet');
        sheet.innerHTML =
            '<h2>' + esc(p.title || 'Cornell Notes') + '</h2>' +
            '<p><strong>Date:</strong> ' + esc(p.date || '') + ' &nbsp; <strong>Course:</strong> ' + esc(p.course || '') + '</p>' +
            '<table style="width:100%;border-collapse:collapse;border:2px solid #333">' +
            '<tr><th style="border:1px solid #333;padding:8px;width:28%;background:#f2f2f2">CUES</th>' +
            '<th style="border:1px solid #333;padding:8px;background:#f2f2f2">NOTES</th></tr>' +
            '<tr><td style="border:1px solid #333;padding:8px;vertical-align:top;min-height:400px">' + nl2br(p.cues) + '</td>' +
            '<td style="border:1px solid #333;padding:8px;vertical-align:top">' + nl2br(p.notes) + '</td></tr>' +
            '<tr><td colspan="2" style="border:1px solid #333;padding:8px"><strong>SUMMARY:</strong><br>' + nl2br(p.summary) + '</td></tr>' +
            '</table>';
        sheet.classList.remove('d-none');
        window.print();
        setTimeout(function () { sheet.classList.add('d-none'); }, 500);
    });

    var initial = load();
    current = 0;
    fillForm(initial[0]);
    renderTabs(initial);
})();
</script>
@endsection
