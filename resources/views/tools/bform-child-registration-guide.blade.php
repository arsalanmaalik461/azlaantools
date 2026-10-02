@extends('layouts.app')
@section('title', 'B-Form Child Registration Guide - Azlaan Tools')
@section('meta_description', 'The complete guide to getting a B-Form (Child Registration Certificate) from NADRA: documents checklist, fees and process. Free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">B-Form / Child Registration Certificate Guide</h1>
            <p class="lead text-muted">The full process for getting your child's B-Form (Child Registration Certificate - CRC) from NADRA: which documents are needed, how much the fee is, and where to apply.</p>

            <div class="alert alert-warning">
                <strong>Important note:</strong> Fees and the process can change over time. Before applying, please confirm on the official website <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a>. This guide is for information only; it does not verify anything live.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Documents Checklist — <span id="checkCount">0</span> / <span id="checkTotal">0</span> ready</h2>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar" id="checkBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="checklist" class="mb-2"></div>
                    <p class="text-muted small mb-0">Tick each document once it is ready. A B-Form can be made for every child under 18 — the father or the mother can apply.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step-by-Step Process</h2>
                    <ol class="mb-0">
                        <li class="mb-2"><strong>Get the birth certificate:</strong> First get your child's computerized birth certificate from your Union Council / Municipal Committee. This is the most important document for the B-Form.</li>
                        <li class="mb-2"><strong>Collect the documents:</strong> Prepare all the documents in the checklist above — copies of both the father's and mother's CNIC are required.</li>
                        <li class="mb-2"><strong>Visit the NADRA office:</strong> Go to the nearest NADRA Registration Center, take a token and submit the B-Form (CRC) form. Biometric verification will be of the father or the mother.</li>
                        <li class="mb-2"><strong>Or apply online:</strong> You can also apply for a CRC through NADRA's <strong>PAK ID</strong> mobile app (Play Store / App Store).</li>
                        <li class="mb-2"><strong>Pay the fee:</strong> Pay the fee at the NADRA counter or through the app. The fee differs by category — see the fee section below.</li>
                        <li><strong>Receive your B-Form:</strong> In a normal case the B-Form arrives within a few days. Keep your token/receipt safe.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Fee (Approximate)</h2>
                    <p class="mb-2">NADRA updates the B-Form fee from time to time. The approximate fee:</p>
                    <ul>
                        <li><strong>First time / duplicate:</strong> about Rs. 50 to Rs. 100</li>
                    </ul>
                    <p class="text-muted small mb-0"><strong>Rates may change</strong> — confirm on the official website <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a> or the NADRA helpline <strong>1777</strong>.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Official Links</h2>
                    <ul class="mb-0">
                        <li>NADRA official website: <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a></li>
                        <li>NADRA helpline: <strong>1777</strong> (Pakistan se)</li>
                        <li>To find the nearest NADRA center, use the center locator on the NADRA website.</li>
                    </ul>
                </div>
            </div>

            <h2>Common Questions</h2>
            <div class="accordion mb-4" id="faqAcc">
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq1">Up to what age can a B-Form be made?</button></h2>
                    <div id="fq1" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body">A Child Registration Certificate (B-Form) can be made for any child under 18. After 18, a CNIC is made.</div></div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq2">Do both the father and the mother need to go?</button></h2>
                    <div id="fq2" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body">No — usually either the father <em>or</em> the mother can apply with their own CNIC, but copies of both parents' CNICs must be with you.</div></div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq3">Can a B-Form be made without a birth certificate?</button></h2>
                    <div id="fq3" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body">No — the Union Council's computerized birth certificate is a required document. Get that first, then apply for the B-Form.</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var items = [
        'Computerized Birth Certificate of the child (from the Union Council)',
        'CNIC of the father (original + photocopy)',
        'CNIC of the mother (original + photocopy)',
        'Presence of either the father or the mother (for biometrics)',
        'NADRA application form (available at the center)',
        'Fee amount (check the latest fee on nadra.gov.pk)'
    ];
    var checklist = document.getElementById('checklist');
    var checkCount = document.getElementById('checkCount');
    var checkTotal = document.getElementById('checkTotal');
    var checkBar = document.getElementById('checkBar');

    checkTotal.textContent = items.length;

    function updateProgress() {
        var checked = checklist.querySelectorAll('input[type="checkbox"]:checked').length;
        checkCount.textContent = checked;
        checkBar.style.width = Math.round(checked / items.length * 100) + '%';
        checkBar.setAttribute('aria-valuenow', checked);
    }

    items.forEach(function (label, i) {
        var wrap = document.createElement('div');
        wrap.className = 'form-check mb-2 p-2 border rounded';
        var cb = document.createElement('input');
        cb.className = 'form-check-input ms-1';
        cb.type = 'checkbox';
        cb.id = 'doc' + i;
        cb.addEventListener('change', updateProgress);
        var lb = document.createElement('label');
        lb.className = 'form-check-label ms-2';
        lb.setAttribute('for', 'doc' + i);
        lb.textContent = label;
        wrap.appendChild(cb);
        wrap.appendChild(lb);
        checklist.appendChild(wrap);
    });
    updateProgress();
})();
</script>
@endsection
