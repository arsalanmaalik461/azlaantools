@extends('layouts.app')

@section('title', 'Assignment Cover Page Maker - Azlaan Tools')
@section('meta_description', 'Make a beautiful cover page for your university assignment and print it — free and fast, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Assignment Cover Page Maker</h1>
            <p class="lead text-muted">Make a professional cover page for your university or college assignment: enter the details, choose a design, see the preview and print it.</p>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="uni" class="form-label fw-semibold">University / College Name</label>
                        <input type="text" class="form-control" id="uni" placeholder="e.g. Government College University Faisalabad">
                    </div>
                    <div class="mb-3">
                        <label for="dept" class="form-label fw-semibold">Department</label>
                        <input type="text" class="form-control" id="dept" placeholder="e.g. Department of Computer Science">
                    </div>
                    <div class="mb-3">
                        <label for="atitle" class="form-label fw-semibold">Assignment Title</label>
                        <input type="text" class="form-control" id="atitle" placeholder="e.g. Impact of Solar Energy in Pakistan">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="subject" class="form-label fw-semibold">Subject</label>
                            <input type="text" class="form-control" id="subject" placeholder="e.g. Pakistan Studies">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="instructor" class="form-label fw-semibold">Teacher Name</label>
                            <input type="text" class="form-control" id="instructor" placeholder="e.g. Prof. Ahmed Khan">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sname" class="form-label fw-semibold">Your Name</label>
                            <input type="text" class="form-control" id="sname" placeholder="e.g. Ali Raza">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="roll" class="form-label fw-semibold">Roll number</label>
                            <input type="text" class="form-control" id="roll" placeholder="e.g. 2024-CS-101">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="classSem" class="form-label fw-semibold">Class / Semester</label>
                            <input type="text" class="form-control" id="classSem" placeholder="e.g. BS-CS, 3rd Semester">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cdate" class="form-label fw-semibold">Submission date</label>
                            <input type="date" class="form-control" id="cdate">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="styleSel" class="form-label fw-semibold">Select a Design</label>
                        <select class="form-select" id="styleSel">
                            <option value="classic" selected>Classic (blue border, formal)</option>
                            <option value="modern">Modern (green header)</option>
                            <option value="minimal">Minimal (simple, black and white)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Cover Page</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="results" class="d-none">
                <div class="d-flex gap-2 mb-3 no-print">
                    <button type="button" class="btn btn-success flex-fill" id="printBtn">Print / Save PDF</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill" id="editBtn">Edit Again</button>
                </div>
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div id="coverPage" style="min-height:600px;"></div>
                    </div>
                </div>
            </div>

            <div class="no-print">
                <h2>How to use</h2>
                <ol>
                    <li>Enter the university, assignment title, your name, roll number and other details.</li>
                    <li>Select your favourite design and click "Create Cover Page".</li>
                    <li>Check the preview, then print or save a PDF with "Print / Save PDF".</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    #coverPage { min-height: auto !important; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var coverPage = document.getElementById('coverPage');
    var printBtn = document.getElementById('printBtn');
    var editBtn = document.getElementById('editBtn');
    var styleSel = document.getElementById('styleSel');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function val(id) { return document.getElementById(id).value.trim(); }
    function esc(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    var STYLES = {
        classic: {
            page: 'border:6px double #1d4ed8;padding:48px 36px;text-align:center;font-family:Georgia,serif;color:#111827;background:#ffffff;',
            uni: 'color:#1d4ed8;font-size:26px;font-weight:bold;margin-bottom:6px;',
            title: 'font-size:30px;font-weight:bold;margin:36px 0 12px;color:#111827;',
            label: 'color:#6b7280;font-size:13px;text-transform:uppercase;letter-spacing:1px;margin-top:22px;',
            value: 'font-size:17px;font-weight:600;color:#111827;'
        },
        modern: {
            page: 'padding:0;text-align:center;font-family:Arial,Helvetica,sans-serif;color:#111827;background:#ffffff;',
            head: 'background:linear-gradient(135deg,#059669,#10b981);color:#ffffff;padding:40px 30px;',
            uni: 'font-size:26px;font-weight:bold;margin-bottom:6px;',
            body: 'padding:36px 30px;',
            title: 'font-size:28px;font-weight:bold;margin:10px 0 12px;color:#065f46;',
            label: 'color:#6b7280;font-size:13px;text-transform:uppercase;letter-spacing:1px;margin-top:20px;',
            value: 'font-size:17px;font-weight:600;color:#111827;'
        },
        minimal: {
            page: 'padding:48px 36px;text-align:center;font-family:Arial,Helvetica,sans-serif;color:#000000;background:#ffffff;border-top:8px solid #000000;',
            uni: 'font-size:24px;font-weight:bold;margin-bottom:6px;',
            title: 'font-size:28px;font-weight:bold;margin:36px 0 12px;',
            label: 'color:#555555;font-size:13px;text-transform:uppercase;letter-spacing:1px;margin-top:22px;',
            value: 'font-size:17px;font-weight:600;'
        }
    };

    function row(label, value, st) {
        if (!value) { return ''; }
        return '<div style="' + st.label + '">' + esc(label) + '</div>' +
               '<div style="' + st.value + '">' + esc(value) + '</div>';
    }

    function buildCover() {
        var uni = val('uni'), dept = val('dept'), title = val('atitle');
        var subject = val('subject'), instructor = val('instructor');
        var sname = val('sname'), roll = val('roll'), classSem = val('classSem'), cdate = val('cdate');
        var style = styleSel.value;
        var st = STYLES[style];
        var html = '';

        if (style === 'modern') {
            html = '<div style="' + st.page + '">' +
                '<div style="' + st.head + '">' +
                    '<div style="' + st.uni + '">' + esc(uni || 'University Name') + '</div>' +
                    (dept ? '<div>' + esc(dept) + '</div>' : '') +
                '</div>' +
                '<div style="' + st.body + '">' +
                    '<div style="' + st.title + '">' + esc(title || 'Assignment Title') + '</div>' +
                    row('Subject', subject, st) +
                    row('Submitted To', instructor, st) +
                    row('Submitted By', sname, st) +
                    row('Roll Number', roll, st) +
                    row('Class / Semester', classSem, st) +
                    row('Submission Date', cdate, st) +
                '</div></div>';
        } else {
            html = '<div style="' + st.page + '">' +
                '<div style="' + st.uni + '">' + esc(uni || 'University Name') + '</div>' +
                (dept ? '<div>' + esc(dept) + '</div>' : '') +
                '<div style="' + st.title + '">' + esc(title || 'Assignment Title') + '</div>' +
                row('Subject', subject, st) +
                row('Submitted To', instructor, st) +
                row('Submitted By', sname, st) +
                row('Roll Number', roll, st) +
                row('Class / Semester', classSem, st) +
                row('Submission Date', cdate, st) +
                '</div>';
        }
        coverPage.innerHTML = html;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!val('atitle')) { showError('Assignment title is required.'); return; }
        if (!val('sname')) { showError('Your name is required.'); return; }
        if (!val('roll')) { showError('Roll number is required.'); return; }
        buildCover();
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth' });
    });

    styleSel.addEventListener('change', function () {
        if (!results.classList.contains('d-none')) { buildCover(); }
    });

    printBtn.addEventListener('click', function () { window.print(); });
    editBtn.addEventListener('click', function () {
        results.classList.add('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>
@endsection
