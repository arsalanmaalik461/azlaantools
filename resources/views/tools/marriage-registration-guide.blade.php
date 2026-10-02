@extends('layouts.app')

@section('title', 'Marriage Registration Guide - Azlaan Tools')
@section('meta_description', 'Complete guide to register a Nikah in Pakistan and get the NADRA marriage certificate — free online guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Marriage Registration Guide</h1>
            <p class="lead text-muted">Step-by-step guide to register your Nikah at the Union Council and get the computerized Marriage Certificate from NADRA. Select your situation — the checklist is made for you.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="scenario" class="form-label fw-semibold">Your situation</label>
                        <select class="form-select" id="scenario">
                            <option value="local">Both live in Pakistan</option>
                            <option value="overseas">One or both are overseas Pakistanis</option>
                            <option value="remarry">Remarriage of a widow / divorced woman</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Guide</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Steps</h5>
                        <ol id="stepsList" class="mb-4"></ol>
                        <h5>Documents Checklist</h5>
                        <div id="docsList" class="list-group mb-3"></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary" id="copyBtn">Copy Checklist</button>
                            <button type="button" class="btn btn-outline-secondary" id="printBtn">Print</button>
                        </div>
                        <div class="alert alert-info mt-4 mb-0">
                            <strong>Official links:</strong><br>
                            NADRA: <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a> &nbsp;|&nbsp;
                            Pak Identity app (online apply) &nbsp;|&nbsp; NADRA helpline: <strong>1777</strong><br>
                            <small class="text-muted">Fees may change over time — confirm on the official website. This is an informational guide, not legal advice.</small>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your situation.</li>
                <li>Click <strong>Make Guide</strong> — you will get a checklist of steps and documents.</li>
                <li>Copy or print the checklist and prepare.</li>
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

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    var DATA = {
        local: {
            steps: [
                'Get the Nikah read by a licensed Nikah Khawan — fill all copies of the Nikah Nama completely (groom, bride, witness and mehr details).',
                'The Nikah Khawan must submit the Nikah Nama to the relevant Union Council (registration).',
                'Get the computerized Nikah Nama from the Union Council — it is required for the NADRA certificate.',
                'Visit the nearest NADRA Registration Center for the NADRA Marriage Certificate: take the computerized Nikah Nama + CNIC of the groom/bride (original + copy) with you.',
                'Or apply online through the Pak Identity mobile app — scan and upload the documents.',
                'Pay the fee (fee may change — confirm at nadra.gov.pk) and keep the receipt safe.',
                'When you get the Computerized Marriage Certificate, verify the name, date and CNIC number.'
            ],
            docs: ['CNIC of groom and bride (original + photocopy)', 'Computerized Nikah Nama (from Union Council)', 'Father CNIC number of groom/bride', 'CNIC numbers of 2 witnesses', 'Passport size photo (if the center asks)', 'Fee receipt']
        },
        overseas: {
            steps: [
                'If the Nikah happened in Pakistan, follow the steps above; for an overseas Nikah, register the Nikah according to the law of that country.',
                'Get the overseas Nikah Nama attested by the Pakistani embassy/consulate.',
                'Register it at the Union Council after returning to Pakistan or through a lawyer.',
                'Apply for the Marriage Certificate at a NADRA Registration Center or through the Pak Identity app — attested Nikah Nama is required.',
                'If one party is in Pakistan, they can apply; the other party may need an attested power of attorney.',
                'Verify the details on the certificate — this same document will be used for immigration/visa.'
            ],
            docs: ['Attested Nikah Nama (from embassy/consulate)', 'CNIC/NICOP of both (original + copy)', 'Passport copies', 'Power of attorney (if one party is not present)', 'Computerized Nikah Nama (from Union Council, if registered in Pakistan)', 'Fee receipt']
        },
        remarry: {
            steps: [
                'If widowed, get the husband death certificate; if divorced, get the divorce effectiveness certificate from the Union Council.',
                'A new Nikah can only happen after the iddat period is complete.',
                'Get the Nikah read by a licensed Nikah Khawan and register it at the Union Council.',
                'Get the computerized Nikah Nama and apply for the NADRA Marriage Certificate.',
                'The marital status in the NADRA record will be updated — this information will help when making a new CNIC.'
            ],
            docs: ['Death certificate (widow) or divorce effectiveness certificate', 'Previous Nikah Nama (if available)', 'CNIC of groom/bride (original + copy)', 'Computerized Nikah Nama (new)', 'CNIC numbers of 2 witnesses', 'Fee receipt']
        }
    };

    var currentDocs = [];

    goBtn.addEventListener('click', function () {
        hideError();
        var sc = document.getElementById('scenario').value;
        var d = DATA[sc];
        if (!d) { showError('Please select a situation.'); return; }
        var stepsList = document.getElementById('stepsList');
        stepsList.innerHTML = '';
        d.steps.forEach(function (s) {
            var li = document.createElement('li');
            li.textContent = s;
            li.className = 'mb-2';
            stepsList.appendChild(li);
        });
        var docsList = document.getElementById('docsList');
        docsList.innerHTML = '';
        currentDocs = d.docs.slice();
        d.docs.forEach(function (doc, i) {
            var label = document.createElement('label');
            label.className = 'list-group-item d-flex align-items-center gap-2';
            label.style.cursor = 'pointer';
            var cb = document.createElement('input');
            cb.type = 'checkbox'; cb.className = 'form-check-input me-1'; cb.id = 'doc' + i;
            var span = document.createElement('span');
            span.textContent = doc;
            label.appendChild(cb); label.appendChild(span);
            docsList.appendChild(label);
        });
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        if (!currentDocs.length) { showError('First click Make Guide.'); return; }
        var text = 'Marriage Registration Checklist:\n\nSteps:\n' +
            Array.prototype.map.call(document.getElementById('stepsList').children, function (li, i) { return (i + 1) + '. ' + li.textContent; }).join('\n') +
            '\n\nDocuments:\n' + currentDocs.map(function (d, i) { return (i + 1) + '. ' + d; }).join('\n');
        navigator.clipboard.writeText(text).then(function () {
            document.getElementById('copyBtn').textContent = 'Copied!';
            setTimeout(function () { document.getElementById('copyBtn').textContent = 'Copy Checklist'; }, 1500);
        }, function () { showError('Could not copy.'); });
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        if (!currentDocs.length) { showError('First click Make Guide.'); return; }
        window.print();
    });
})();
</script>
@endsection
