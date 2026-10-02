@extends('layouts.app')
@section('title', 'Appointment Letter Maker - Azlaan Tools')
@section('meta_description', 'Create a professional appointment letter for a new employee with salary and terms. Free online, download as PDF.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Appointment Letter Maker</h1>
            <p class="lead text-muted">Prepare a professional appointment letter for a new employee — enter the company name, position, salary and terms, preview the letter and download the PDF.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="compName" class="form-label fw-semibold">Company name</label>
                            <input type="text" class="form-control" id="compName" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="compCity" class="form-label fw-semibold">Company city</label>
                            <input type="text" class="form-control" id="compCity" placeholder="e.g. Faisalabad">
                        </div>
                        <div class="col-md-6">
                            <label for="empName" class="form-label fw-semibold">Employee name</label>
                            <input type="text" class="form-control" id="empName" placeholder="e.g. Ali Raza">
                        </div>
                        <div class="col-md-6">
                            <label for="empPos" class="form-label fw-semibold">Position</label>
                            <input type="text" class="form-control" id="empPos" placeholder="e.g. Solar Technician">
                        </div>
                        <div class="col-md-4">
                            <label for="salary" class="form-label fw-semibold">Monthly salary (Rs)</label>
                            <input type="number" class="form-control" id="salary" placeholder="e.g. 45000" min="0">
                        </div>
                        <div class="col-md-4">
                            <label for="joinDate" class="form-label fw-semibold">Joining date</label>
                            <input type="date" class="form-control" id="joinDate">
                        </div>
                        <div class="col-md-4">
                            <label for="probation" class="form-label fw-semibold">Probation period</label>
                            <select class="form-select" id="probation">
                                <option value="1 month">1 month</option>
                                <option value="2 months">2 months</option>
                                <option value="3 months" selected>3 months</option>
                                <option value="6 months">6 months</option>
                                <option value="No probation">No probation</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workHours" class="form-label fw-semibold">Working hours</label>
                            <input type="text" class="form-control" id="workHours" placeholder="e.g. 9:00 AM to 6:00 PM">
                        </div>
                        <div class="col-md-6">
                            <label for="refNo" class="form-label fw-semibold">Letter reference no.</label>
                            <input type="text" class="form-control" id="refNo" placeholder="e.g. AZL/2026/014">
                        </div>
                        <div class="col-12">
                            <label for="extraTerms" class="form-label fw-semibold">Extra terms (one per line)</label>
                            <textarea class="form-control" id="extraTerms" rows="3" placeholder="e.g. Salary will be paid on the 5th of every month"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate Letter</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="border rounded p-4 bg-white" id="letterPreview" style="font-family: Georgia, serif;"></div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-success flex-fill" id="pdfBtn">Download PDF</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="printBtn">Print</button>
                        </div>
                        <p class="small text-muted mt-2 mb-0">This is a standard draft — consult your lawyer for legal advice.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Fill in the company and employee details.</li>
                <li>Click Generate Letter — the letter preview will be created.</li>
                <li>Download the PDF or print it and get it signed.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmtDate(d) {
        var parts = d.split('-');
        if (parts.length !== 3) return esc(d);
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return parts[2].replace(/^0/,'') + ' ' + months[parseInt(parts[1],10)-1] + ' ' + parts[0];
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var comp = document.getElementById('compName').value.trim();
        var city = document.getElementById('compCity').value.trim();
        var emp = document.getElementById('empName').value.trim();
        var pos = document.getElementById('empPos').value.trim();
        var sal = document.getElementById('salary').value.trim();
        var jd = document.getElementById('joinDate').value;
        if (!comp || !emp || !pos || !sal || !jd) {
            showError('Please fill in the company, employee, position, salary and joining date.');
            return;
        }
        var probation = document.getElementById('probation').value;
        var hours = document.getElementById('workHours').value.trim();
        var ref = document.getElementById('refNo').value.trim();
        var extras = document.getElementById('extraTerms').value.split('\n').map(function (l) { return l.trim(); }).filter(Boolean);

        var today = new Date();
        var issue = today.getDate() + ' ' + ['January','February','March','April','May','June','July','August','September','October','November','December'][today.getMonth()] + ' ' + today.getFullYear();
        var terms = [];
        terms.push('Your monthly salary will be <strong>Rs ' + esc(Number(sal).toLocaleString('en-US')) + '</strong>, paid according to the company salary schedule.');
        if (probation !== 'No probation') terms.push('Your probation period will be <strong>' + esc(probation) + '</strong>, with confirmation based on performance.');
        if (hours) terms.push('Working hours: <strong>' + esc(hours) + '</strong>.');
        terms.push('Joining date: <strong>' + esc(fmtDate(jd)) + '</strong>.');
        extras.forEach(function (t) { terms.push(esc(t)); });
        terms.push('Employment will follow company rules and Pakistan labor laws.');

        var html = '<p style="text-align:center;font-size:22px;font-weight:bold;margin-bottom:2px;">' + esc(comp) + '</p>' +
            '<p style="text-align:center;color:#555;">' + esc(city) + '</p>' +
            '<p><strong>Ref: ' + esc(ref || 'N/A') + '</strong><br>Date: ' + esc(issue) + '</p>' +
            '<p><strong>To,</strong><br>' + esc(emp) + '<br>Dear ' + esc(emp) + ',</p>' +
            '<h3 style="text-align:center;text-decoration:underline;">APPOINTMENT LETTER</h3>' +
            '<p>We are pleased to offer you the position of <strong>' + esc(pos) + '</strong> at <strong>' + esc(comp) + '</strong>, effective from <strong>' + esc(fmtDate(jd)) + '</strong>. Your terms of employment are as follows:</p>' +
            '<ol>' + terms.map(function (t) { return '<li>' + t + '</li>'; }).join('') + '</ol>' +
            '<p>Please sign and return a copy of this letter as acceptance of this offer.</p>' +
            '<br><br><table style="width:100%;"><tr>' +
            '<td>______________________<br><strong>For ' + esc(comp) + '</strong><br>(Authorized Signatory)</td>' +
            '<td style="text-align:right;">______________________<br><strong>' + esc(emp) + '</strong><br>(Employee Acceptance)</td>' +
            '</tr></table>';

        document.getElementById('letterPreview').innerHTML = html;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('pdfBtn').addEventListener('click', function () {
        var el = document.getElementById('letterPreview');
        if (typeof html2pdf !== 'undefined') {
            html2pdf().set({ margin: 15, filename: 'appointment-letter.pdf', html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4' } }).from(el).save();
        } else {
            window.print();
        }
    });
    document.getElementById('printBtn').addEventListener('click', function () { window.print(); });
})();
</script>
@endsection
