@extends('layouts.app')
@section('title', 'Driving License Verification Guide - Azlaan Tools')
@section('meta_description', 'Step by step guide to verify a Punjab, Sindh or KP driving license online on the official portals, free.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Driving License Verification Guide</h1>
            <p class="lead text-muted">The right way to verify your driving license on the <strong>official government portal</strong> of Punjab, Sindh or KP. Step by step guide, in simple English.</p>

            <div class="alert alert-warning">
                <strong>Important note:</strong> This page does not verify a license itself — it only shows the official method. Always do the verification yourself on the official <code>.gov.pk</code> portals listed below. Do not give a CNIC copy or money to any agent or non-government website.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="provinceSel" class="form-label fw-semibold">Select your province / area</label>
                        <select id="provinceSel" class="form-select">
                            <option value="punjab" selected>Punjab</option>
                            <option value="sindh">Sindh</option>
                            <option value="kp">Khyber Pakhtunkhwa (KP)</option>
                            <option value="ict">Islamabad (ICT)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Show Guide</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <div id="guideBox"></div>
                    </div>
                </div>
            </div>

            <h2>How to Avoid a Fake License</h2>
            <ul>
                <li>Always check the <code>.gov.pk</code> domain in the address bar. Fake sites often use similar names (for example, the Punjab Traffic Police has declared a site like <code>dastakpunjab.online</code> a fraud).</li>
                <li>An official portal never asks for money on WhatsApp, an Easypaisa/JazzCash transfer, or a photo of your CNIC.</li>
                <li>New licenses have a QR code — scan it and match the details on the official portal.</li>
                <li>If it says "Not Found", first re-check your CNIC/license number (without dashes), then contact the issuing office.</li>
            </ul>

            <h2>How to use</h2>
            <ol>
                <li>Select your province and press <strong>Show Guide</strong>.</li>
                <li>Open the official portal link and enter your CNIC or license number.</li>
                <li>In the result, match your name, category and expiry date.</li>
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
    var guideBox = document.getElementById('guideBox');
    var provinceSel = document.getElementById('provinceSel');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    var GUIDES = {
        punjab: {
            title: 'Punjab — DLIMS Portal',
            portal: 'https://dlims.punjab.gov.pk',
            portalLabel: 'dlims.punjab.gov.pk',
            need: 'CNIC number (without dashes) or Driving License number',
            steps: [
                'Open the official DLIMS portal: <a href="https://dlims.punjab.gov.pk" target="_blank" rel="noopener">dlims.punjab.gov.pk</a>',
                'On the homepage, choose the <strong>Verify License</strong> option (it is different from Track License — Track only shows card printing/delivery).',
                'Enter your CNIC number (without dashes, e.g. 3520212345678) or license number.',
                'Enter the letters shown in the CAPTCHA correctly and press <strong>Verify</strong>.',
                'On the screen you will see the license holder name, category (Motorcycle / Car / LTV / HTV), issue and expiry date — match them with your card.',
                'If you like, take a print of the result or save it as PDF.'
            ],
            sms: 'SMS method: In Punjab you can also get license details by SMSing the license number to 8070 (charges as per operator).'
        },
        sindh: {
            title: 'Sindh — DLS Online Portal',
            portal: 'https://dls.gos.pk',
            portalLabel: 'dls.gos.pk',
            need: 'CNIC number or License number',
            steps: [
                'Open the official DLS website: <a href="https://dls.gos.pk" target="_blank" rel="noopener">dls.gos.pk</a> and click on <strong>Online Verification</strong>.',
                'Enter your 13-digit CNIC number or license number.',
                'Complete the security captcha and press <strong>Search / Verify</strong>.',
                'In the result you will see your name, license type, issue date, expiry date and verification status.',
                'For new apply/renewal, a DLS Online account is created on <a href="https://dlsonline.sindhpolice.gov.pk" target="_blank" rel="noopener">dlsonline.sindhpolice.gov.pk</a>.'
            ],
            sms: 'For overseas or special verification, the official email is: verification.dls@sindhpolice.gov.pk'
        },
        kp: {
            title: 'Khyber Pakhtunkhwa — Traffic Police Portal',
            portal: 'https://ptpkp.gov.pk',
            portalLabel: 'ptpkp.gov.pk',
            need: 'CNIC number (13 digits)',
            steps: [
                'Open the Peshawar Traffic Police official website: <a href="https://ptpkp.gov.pk" target="_blank" rel="noopener">ptpkp.gov.pk</a>.',
                'Open the <strong>Driving License Verification</strong> page.',
                'Enter your 13-digit CNIC number and press <strong>Verify</strong>.',
                'On the screen you will see the license issue date, expiry date and status.',
                'New KP licenses have a QR code — scan it and the details are verified on the official portal right away.',
                'The KP digital (e-driving license) can also be obtained in the <strong>Rabta app</strong> by entering your CNIC; it is legally accepted at traffic stops.'
            ],
            sms: 'In KP the fee voucher and learner apply also happen through ptpkp.gov.pk; for the learner permit, test and card you still need to visit a branch.'
        },
        ict: {
            title: 'Islamabad — ITP Portal',
            portal: 'https://islamabadpolice.gov.pk/itp',
            portalLabel: 'islamabadpolice.gov.pk/itp',
            need: 'CNIC number or License number',
            steps: [
                'Open the Islamabad Traffic Police page: <a href="https://islamabadpolice.gov.pk/itp" target="_blank" rel="noopener">islamabadpolice.gov.pk/itp</a>.',
                'Open the online license verification section.',
                'Enter your CNIC or license number and verify.',
                'Renewal (including for overseas Pakistanis) is also available online; Medical Form-B may be required.',
                'For a license expired for a long time, you may need to take the test again — confirm with ITP.'
            ],
            sms: 'The ITP license branch is in Shakarparian; book your appointment online first.'
        }
    };

    function renderGuide(key) {
        var g = GUIDES[key];
        if (!g) { showError('Please select a province.'); return; }
        var html = '<h4>' + g.title + '</h4>';
        html += '<p><strong>Official portal:</strong> <a href="' + g.portal + '" target="_blank" rel="noopener">' + g.portalLabel + '</a></p>';
        html += '<p><strong>What you need:</strong> ' + g.need + '</p>';
        html += '<ol>';
        for (var i = 0; i < g.steps.length; i++) { html += '<li>' + g.steps[i] + '</li>'; }
        html += '</ol>';
        html += '<div class="alert alert-info mb-0"><strong>Extra tip:</strong> ' + g.sms + '</div>';
        guideBox.innerHTML = html;
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        renderGuide(provinceSel.value);
    });
    provinceSel.addEventListener('change', function () {
        hideError();
        renderGuide(provinceSel.value);
    });

    renderGuide('punjab');
})();
</script>
@endsection
