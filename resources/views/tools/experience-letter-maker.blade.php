@extends('layouts.app')
@section('title', 'Experience Letter Maker - Free Online | Azlaan Tools')
@section('meta_description', 'Create a professional employee experience letter online for free - tenure, role and conduct with printable letterhead.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Experience Letter Maker</h1>
            <p class="lead text-muted">Create an employee experience certificate — a professional letter with tenure and role, printable and downloadable. Free.</p>

            <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>

            <div class="row">
                <div class="col-12 col-lg-5 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="companyInput" class="form-label fw-semibold">Company / Organization name</label>
                                <input type="text" class="form-control" id="companyInput" placeholder="e.g. Azlaan Electric AC Solar Center">
                            </div>
                            <div class="mb-3">
                                <label for="companyAddrInput" class="form-label fw-semibold">Company address / phone (optional)</label>
                                <input type="text" class="form-control" id="companyAddrInput" placeholder="e.g. Main Road, Faisalabad — 0300-0000000">
                            </div>
                            <div class="mb-3">
                                <label for="empNameInput" class="form-label fw-semibold">Employee full name</label>
                                <input type="text" class="form-control" id="empNameInput" placeholder="e.g. Ahmed Raza">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label for="designationInput" class="form-label fw-semibold">Designation</label>
                                    <input type="text" class="form-control" id="designationInput" placeholder="e.g. Sales Manager">
                                </div>
                                <div class="col-6">
                                    <label for="deptInput" class="form-label fw-semibold">Department (optional)</label>
                                    <input type="text" class="form-control" id="deptInput" placeholder="e.g. Operations">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label for="joinDateInput" class="form-label fw-semibold">Joining date</label>
                                    <input type="date" class="form-control" id="joinDateInput">
                                </div>
                                <div class="col-6">
                                    <label for="endDateInput" class="form-label fw-semibold">Last working date</label>
                                    <input type="date" class="form-control" id="endDateInput">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="conductSel" class="form-label fw-semibold">Conduct / performance line</label>
                                <select id="conductSel" class="form-select">
                                    <option value="excellent">Excellent — hardworking and dedicated</option>
                                    <option value="good" selected>Good — sincere and reliable</option>
                                    <option value="satisfactory">Satisfactory — duties performed well</option>
                                    <option value="none">Do not include a conduct line</option>
                                </select>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label for="signNameInput" class="form-label fw-semibold">Signatory name</label>
                                    <input type="text" class="form-control" id="signNameInput" placeholder="e.g. Malik Arslan">
                                </div>
                                <div class="col-6">
                                    <label for="signDesigInput" class="form-label fw-semibold">Signatory designation</label>
                                    <input type="text" class="form-control" id="signDesigInput" placeholder="e.g. Director">
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-lg" id="goBtn">Generate Letter</button>
                                <button type="button" class="btn btn-success d-none" id="pdfBtn">⬇ Download as PDF</button>
                                <button type="button" class="btn btn-outline-secondary d-none" id="printBtn">🖨 Print Letter</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Letter preview</h2>
                            <div id="results" class="d-none">
                                <div id="letterPaper" class="border rounded p-4 bg-white" style="min-height: 480px;">
                                    <div class="text-center border-bottom pb-3 mb-3">
                                        <div class="h4 mb-1" id="pvCompany"></div>
                                        <div class="text-muted small" id="pvAddr"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <div><strong>Ref:</strong> <span id="pvRef"></span></div>
                                        <div><strong>Date:</strong> <span id="pvDate"></span></div>
                                    </div>
                                    <h3 class="h5 text-center text-decoration-underline mb-3">EXPERIENCE CERTIFICATE</h3>
                                    <p class="text-center fw-semibold mb-3">TO WHOM IT MAY CONCERN</p>
                                    <p id="pvBody" style="line-height: 1.9; text-align: justify;"></p>
                                    <p class="mb-4">We wish him/her success in all future endeavors.</p>
                                    <div class="mt-5">
                                        <div class="mb-4">Sincerely,</div>
                                        <div class="fw-bold" id="pvSignName"></div>
                                        <div class="text-muted" id="pvSignDesig"></div>
                                        <div class="text-muted small" id="pvSignCompany"></div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted" id="emptyNote">Fill the form and press <strong>Generate Letter</strong> — the letter will appear here.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Fill in the company, employee and tenure details on the left.</li>
                <li>Click <strong>Generate Letter</strong> to preview the certificate.</li>
                <li>Download it as a PDF or print it on your company letterhead.</li>
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
    var errorBox = document.getElementById('errorBox');
    var goBtn = document.getElementById('goBtn');
    var pdfBtn = document.getElementById('pdfBtn');
    var printBtn = document.getElementById('printBtn');
    var results = document.getElementById('results');
    var emptyNote = document.getElementById('emptyNote');
    var letterPaper = document.getElementById('letterPaper');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtDate(iso) {
        var d = new Date(iso + 'T00:00:00');
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }
    function tenureText(joinIso, endIso) {
        var j = new Date(joinIso + 'T00:00:00');
        var e = new Date(endIso + 'T00:00:00');
        var months = (e.getFullYear() - j.getFullYear()) * 12 + (e.getMonth() - j.getMonth());
        if (e.getDate() < j.getDate()) { months--; }
        if (months < 0) { months = 0; }
        var years = Math.floor(months / 12);
        var rem = months % 12;
        var parts = [];
        if (years > 0) { parts.push(years + (years === 1 ? ' year' : ' years')); }
        if (rem > 0) { parts.push(rem + (rem === 1 ? ' month' : ' months')); }
        if (parts.length === 0) { return 'less than a month'; }
        return parts.join(' and ');
    }

    var lastData = null;

    goBtn.addEventListener('click', function () {
        hideError();
        var company = document.getElementById('companyInput').value.trim();
        var addr = document.getElementById('companyAddrInput').value.trim();
        var emp = document.getElementById('empNameInput').value.trim();
        var desig = document.getElementById('designationInput').value.trim();
        var dept = document.getElementById('deptInput').value.trim();
        var joinIso = document.getElementById('joinDateInput').value;
        var endIso = document.getElementById('endDateInput').value;
        var conduct = document.getElementById('conductSel').value;
        var signName = document.getElementById('signNameInput').value.trim();
        var signDesig = document.getElementById('signDesigInput').value.trim();

        if (!company) { showError('Please enter the company name.'); return; }
        if (!emp) { showError('Please enter the employee name.'); return; }
        if (!desig) { showError('Please enter the designation.'); return; }
        if (!joinIso || !endIso) { showError('Please select both joining and last working dates.'); return; }
        if (new Date(endIso) < new Date(joinIso)) { showError('Last working date cannot be before the joining date.'); return; }
        if (!signName) { showError('Please enter the signatory name.'); return; }

        var tenure = tenureText(joinIso, endIso);
        var roleLine = dept ? desig + ' in the ' + dept + ' department' : desig;
        var conductLine = '';
        if (conduct === 'excellent') { conductLine = ' His/Her conduct and performance during the tenure were excellent — hardworking, dedicated and a true team player.'; }
        else if (conduct === 'good') { conductLine = ' His/Her conduct and performance during the tenure were good — sincere, reliable and cooperative.'; }
        else if (conduct === 'satisfactory') { conductLine = ' His/Her conduct was satisfactory and all assigned duties were performed well.'; }

        var ref = 'EXP/' + new Date().getFullYear() + '/' + Math.floor(1000 + Math.random() * 9000);

        document.getElementById('pvCompany').textContent = company;
        document.getElementById('pvAddr').textContent = addr;
        document.getElementById('pvAddr').style.display = addr ? '' : 'none';
        document.getElementById('pvRef').textContent = ref;
        document.getElementById('pvDate').textContent = fmtDate(endIso);
        document.getElementById('pvBody').textContent =
            'This is to certify that ' + emp + ' was employed with ' + company +
            ' as ' + roleLine + ' from ' + fmtDate(joinIso) + ' to ' + fmtDate(endIso) +
            ' (' + tenure + ').' + conductLine +
            ' During this period, he/she carried out his/her responsibilities to our satisfaction.';
        document.getElementById('pvSignName').textContent = signName;
        document.getElementById('pvSignDesig').textContent = signDesig;
        document.getElementById('pvSignCompany').textContent = 'For ' + company;

        lastData = { company: company, emp: emp, ref: ref };
        results.classList.remove('d-none');
        emptyNote.classList.add('d-none');
        pdfBtn.classList.remove('d-none');
        printBtn.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    pdfBtn.addEventListener('click', function () {
        if (!lastData || typeof html2pdf === 'undefined') {
            showError('PDF library not ready — please generate the letter again and check your internet connection.');
            return;
        }
        var fname = lastData.emp.replace(/[^a-z0-9]+/gi, '-').toLowerCase() || 'experience-letter';
        html2pdf().set({
            margin: 12,
            filename: fname + '-experience-letter.pdf',
            image: { type: 'jpeg', quality: 0.95 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(letterPaper).save();
    });

    printBtn.addEventListener('click', function () {
        var w = window.open('', '_blank', 'width=800,height=900');
        if (!w) {
            showError('Popup blocked — please allow popups to print.');
            return;
        }
        w.document.write('<html><head><title>Experience Letter</title>');
        w.document.write('<style>body{font-family:Georgia,serif;max-width:700px;margin:40px auto;padding:0 20px;color:#111}h1{font-size:22px}.center{text-align:center}.muted{color:#666;font-size:13px}hr{border:none;border-top:1px solid #ccc;margin:16px 0}p{line-height:1.9;text-align:justify}.u{text-decoration:underline}</style>');
        w.document.write('</head><body>' + letterPaper.innerHTML + '</body></html>');
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); }, 500);
    });
})();
</script>
@endsection
