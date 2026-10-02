@extends('layouts.app')

@section('title', 'Letterhead Maker - Azlaan Tools')
@section('meta_description', 'Design a professional business letterhead with your logo text and contact details. Free and printable.')

@section('content')
<style>
    #lhPreview { background:#ffffff; }
    .lh-topbar { height:8px; }
    .lh-logo { font-weight:800; font-size:34px; line-height:1; }
    .lh-tag { font-size:13px; letter-spacing:2px; text-transform:uppercase; }
    .lh-contact { font-size:12.5px; }
    .lh-divider { border-top:2px solid; margin:14px 0; }
    .lh-body { font-size:14px; color:#333; min-height:220px; }
    .lh-foot { font-size:11px; color:#777; border-top:1px solid #ddd; padding-top:8px; }
    @media print {
        body * { visibility:hidden; }
        #lhPrintArea, #lhPrintArea * { visibility:visible; }
        #lhPrintArea { position:absolute; left:0; top:0; width:100%; }
        #lhPreview { box-shadow:none !important; border:none !important; }
    }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Letterhead Maker</h1>
            <p class="lead text-muted">Make a professional letterhead for your business — with company name, logo text and contact, print-ready.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="lhCompany" class="form-label fw-semibold">Company name</label>
                            <input type="text" class="form-control" id="lhCompany" placeholder="e.g. Azlaan Electric AC Solar Center" value="Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="lhTagline" class="form-label fw-semibold">Tagline (optional)</label>
                            <input type="text" class="form-control" id="lhTagline" placeholder="e.g. Quality Service, Honest Price" value="Quality Service, Honest Price">
                        </div>
                        <div class="col-md-6">
                            <label for="lhAddress" class="form-label fw-semibold">Address</label>
                            <input type="text" class="form-control" id="lhAddress" placeholder="e.g. Main Market Road, Faisalabad" value="Main Market Road, Faisalabad">
                        </div>
                        <div class="col-md-6">
                            <label for="lhPhone" class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="lhPhone" placeholder="e.g. 0300-8987448" value="0300-8987448">
                        </div>
                        <div class="col-md-6">
                            <label for="lhEmail" class="form-label fw-semibold">Email (optional)</label>
                            <input type="text" class="form-control" id="lhEmail" placeholder="e.g. info@example.com">
                        </div>
                        <div class="col-md-6">
                            <label for="lhColor" class="form-label fw-semibold">Brand color</label>
                            <input type="color" class="form-control form-control-color w-100" id="lhColor" value="#1d4ed8">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Make Preview</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label class="form-label fw-semibold">Preview</label>
                        <div id="lhPrintArea">
                            <div id="lhPreview" class="border rounded shadow-sm p-4 p-md-5"></div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-3">
                            <button type="button" class="btn btn-success flex-grow-1" id="printBtn">Print / Save as PDF</button>
                            <button type="button" class="btn btn-outline-primary flex-grow-1" id="dlBtn">Download HTML</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the company name, tagline, address and phone, and pick a brand color.</li>
                <li>Press "Make Preview" — the letterhead will appear below.</li>
                <li>Press Print to print it, or choose "Save as PDF".</li>
            </ol>
            <p class="text-muted small">To change the preview, edit the fields and press Preview again.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var preview = document.getElementById('lhPreview');

    function val(id) { return document.getElementById(id).value.trim(); }
    function esc(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function buildLetterhead() {
        var company = val('lhCompany');
        var tagline = val('lhTagline');
        var address = val('lhAddress');
        var phone = val('lhPhone');
        var email = val('lhEmail');
        var color = document.getElementById('lhColor').value || '#1d4ed8';
        if (!company) return null;
        var initials = company.split(/\s+/).map(function (w) { return w.charAt(0); }).join('').substring(0, 3).toUpperCase();
        var html = '';
        html += '<div class="lh-topbar" style="background:' + color + '"></div>';
        html += '<div class="d-flex align-items-center gap-3 mt-3 flex-wrap">';
        html += '<div class="lh-logo d-flex align-items-center justify-content-center text-white rounded" style="background:' + color + '; width:64px; height:64px;">' + esc(initials) + '</div>';
        html += '<div><div class="lh-logo" style="color:' + color + '">' + esc(company) + '</div>';
        if (tagline) html += '<div class="lh-tag text-muted">' + esc(tagline) + '</div>';
        html += '</div></div>';
        html += '<div class="lh-divider" style="border-color:' + color + '"></div>';
        var contact = [];
        if (address) contact.push(esc(address));
        if (phone) contact.push('Phone: ' + esc(phone));
        if (email) contact.push(esc(email));
        if (contact.length) html += '<div class="lh-contact text-muted mb-3">' + contact.join(' &nbsp;|&nbsp; ') + '</div>';
        html += '<div class="lh-body">';
        html += '<p class="text-muted mb-1">Date: ____ / ____ / ________</p>';
        html += '<p class="text-muted mb-4">Ref No: ____________</p>';
        html += '<p class="fw-semibold">Dear Sir/Madam,</p>';
        html += '<p class="text-muted">Write your letter here... (you can write by hand or on computer after printing)</p>';
        html += '<div style="height:120px"></div>';
        html += '<p class="mb-0">Yours sincerely,</p><p class="fw-semibold">' + esc(company) + '</p>';
        html += '</div>';
        html += '<div class="lh-foot text-center mt-4">' + esc(company) + (address ? ' &bull; ' + esc(address) : '') + (phone ? ' &bull; ' + esc(phone) : '') + '</div>';
        return html;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var html = buildLetterhead();
        if (!html) { showError('Please enter your company name.'); return; }
        preview.innerHTML = html;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    printBtn.addEventListener('click', function () { window.print(); });

    dlBtn.addEventListener('click', function () {
        var html = buildLetterhead();
        if (!html) { showError('Make the preview first.'); return; }
        var doc = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Letterhead</title>' +
            '<style>body{font-family:Arial,sans-serif;max-width:700px;margin:40px auto;padding:0 20px;}' +
            '.lh-topbar{height:8px;}.lh-logo{font-weight:800;font-size:34px;line-height:1;}' +
            '.lh-tag{font-size:13px;letter-spacing:2px;text-transform:uppercase;}' +
            '.lh-contact{font-size:12.5px;}.lh-divider{border-top:2px solid;margin:14px 0;}' +
            '.lh-body{font-size:14px;color:#333;min-height:220px;}' +
            '.lh-foot{font-size:11px;color:#777;border-top:1px solid #ddd;padding-top:8px;}' +
            '.d-flex{display:flex;}.align-items-center{align-items:center;}.gap-3{gap:12px;}.mt-3{margin-top:12px;}' +
            '.mb-3{margin-bottom:12px;}.mb-4{margin-bottom:16px;}.mb-1{margin-bottom:4px;}.mb-0{margin-bottom:0;}' +
            '.mt-4{margin-top:16px;}.text-muted{color:#6c757d;}.text-center{text-align:center;}' +
            '.text-white{color:#fff;}.rounded{border-radius:6px;}.fw-semibold{font-weight:600;}</style></head>' +
            '<body>' + html + '</body></html>';
        var blob = new Blob([doc], { type: 'text/html' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'letterhead.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
