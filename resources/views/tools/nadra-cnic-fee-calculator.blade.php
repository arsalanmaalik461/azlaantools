@extends('layouts.app')

@section('title', 'NADRA CNIC Fee Calculator - Azlaan Tools')
@section('meta_description', 'Free NADRA fee calculator: CNIC, Smart Card, NICOP, B-Form and FRC fees for Normal, Urgent and Executive service with processing time.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">NADRA CNIC Fee Calculator</h1>
            <p class="lead text-muted">NADRA CNIC, Smart Card, NICOP and B-Form fee calculator — for Normal, Urgent and Executive service, with processing time.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="docType" class="form-label fw-semibold">Select a document</label>
                        <select class="form-select" id="docType">
                            <option value="first">First CNIC (first time, at age 18)</option>
                            <option value="smart">Smart CNIC (chip card)</option>
                            <option value="renew">CNIC Renewal / Change / Duplicate</option>
                            <option value="nicopA">NICOP — Zone A (USA, UK, Europe, Canada)</option>
                            <option value="nicopB">NICOP — Zone B (Middle East, Africa)</option>
                            <option value="bform">B-Form / CRC (child certificate)</option>
                            <option value="frc">FRC (Family Registration Certificate)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Service speed</label>
                        <div class="d-flex gap-2 flex-wrap" role="group" aria-label="Service speed">
                            <div class="form-check border rounded px-4 py-2">
                                <input class="form-check-input" type="radio" name="speed" id="spdNormal" value="normal" checked>
                                <label class="form-check-label" for="spdNormal">Normal</label>
                            </div>
                            <div class="form-check border rounded px-4 py-2">
                                <input class="form-check-input" type="radio" name="speed" id="spdUrgent" value="urgent">
                                <label class="form-check-label" for="spdUrgent">Urgent</label>
                            </div>
                            <div class="form-check border rounded px-4 py-2">
                                <input class="form-check-input" type="radio" name="speed" id="spdExec" value="executive">
                                <label class="form-check-label" for="spdExec">Executive</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3" id="courierWrap">
                        <input class="form-check-input" type="checkbox" id="homeDelivery">
                        <label class="form-check-label" for="homeDelivery">Home delivery (Pak-ID app) — courier charges + Rs 165</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Fee</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success text-center mb-3">
                            <div class="small text-muted">Total Fee</div>
                            <div class="fs-2 fw-bold" id="feeMain">-</div>
                            <div class="small" id="feeBreak">-</div>
                        </div>
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="fs-5 fw-bold" id="docLabel">-</div>
                                    <div class="text-muted small">Document</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="fs-5 fw-bold" id="timeLabel">-</div>
                                    <div class="text-muted small">Approx processing time</div>
                                </div>
                            </div>
                        </div>
                        <h5 class="mt-4">Compare all speeds</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center align-middle">
                                <thead class="table-light"><tr><th>Speed</th><th>Fee</th><th>Time</th></tr></thead>
                                <tbody id="cmpBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4 small mb-0">
                        Rates may change — confirm on the official website <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a>. Fee can be paid with Pak-ID app, JazzCash, Easypaisa or bank.
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a document (CNIC, Smart Card, NICOP, B-Form or FRC).</li>
                <li>Choose the service speed: Normal, Urgent or Executive.</li>
                <li>Press "Calculate Fee" — you will get the total fee and approx time.</li>
            </ol>
            <p class="text-muted small">Fee schedule is as per the NADRA April 2026 update. First CNIC (Normal) is free.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var docType = document.getElementById('docType');
    var homeDelivery = document.getElementById('homeDelivery');
    var courierWrap = document.getElementById('courierWrap');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var feeMain = document.getElementById('feeMain');
    var feeBreak = document.getElementById('feeBreak');
    var docLabel = document.getElementById('docLabel');
    var timeLabel = document.getElementById('timeLabel');
    var cmpBody = document.getElementById('cmpBody');

    var DOCS = {
        first:  { label: 'First CNIC (first time)', unit: 'Rs', inland: true,
                  fees: { normal: 0, urgent: 1150, executive: 2150 },
                  times: { normal: '31 days', urgent: '23 days', executive: '9 days' } },
        smart:  { label: 'Smart CNIC', unit: 'Rs', inland: true,
                  fees: { normal: 750, urgent: 1500, executive: 2500 },
                  times: { normal: '31 days', urgent: '23 days', executive: '9 days' } },
        renew:  { label: 'CNIC Renewal / Change / Duplicate', unit: 'Rs', inland: true,
                  fees: { normal: 400, urgent: 1150, executive: 2150 },
                  times: { normal: '31 days', urgent: '23 days', executive: '9 days' } },
        nicopA: { label: 'NICOP — Zone A', unit: '$', inland: false,
                  fees: { normal: 39, urgent: 57, executive: 75 },
                  times: { normal: '31 days', urgent: '23 days', executive: '9 days' } },
        nicopB: { label: 'NICOP — Zone B', unit: '$', inland: false,
                  fees: { normal: 20, urgent: 30, executive: 40 },
                  times: { normal: '31 days', urgent: '23 days', executive: '9 days' } },
        bform:  { label: 'B-Form / CRC', unit: 'Rs', inland: true,
                  fees: { normal: 50, urgent: null, executive: 500 },
                  times: { normal: '7 days', urgent: null, executive: '1 day' } },
        frc:    { label: 'FRC', unit: 'Rs', inland: true,
                  fees: { normal: null, urgent: null, executive: 1000 },
                  times: { normal: null, urgent: null, executive: '24 hours' } }
    };
    var SPEED_LABEL = { normal: 'Normal', urgent: 'Urgent', executive: 'Executive' };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function selSpeed() {
        var r = document.querySelector('input[name="speed"]:checked');
        return r ? r.value : 'normal';
    }
    function fmtFee(d, v) {
        if (v === null || v === undefined) return '—';
        if (v === 0) return 'FREE';
        return d.unit === '$' ? '$' + v : 'Rs ' + v.toLocaleString('en-PK');
    }

    docType.addEventListener('change', function () {
        var d = DOCS[docType.value];
        courierWrap.style.display = d.inland ? '' : 'none';
        if (!d.inland) homeDelivery.checked = false;
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var d = DOCS[docType.value];
        var sp = selSpeed();
        var fee = d.fees[sp];
        if (fee === null || fee === undefined) {
            showError('The "' + SPEED_LABEL[sp] + '" service is not available for this document. Try another speed.');
            return;
        }
        var extra = 0;
        if (d.inland && homeDelivery.checked) extra = 165;
        var total = fee + extra;
        feeMain.textContent = fmtFee(d, total);
        feeBreak.textContent = extra ? ('Card fee ' + fmtFee(d, fee) + ' + courier Rs 165') : d.label + ' — ' + SPEED_LABEL[sp];
        docLabel.textContent = d.label;
        timeLabel.textContent = d.times[sp] || '—';
        cmpBody.innerHTML = '';
        ['normal', 'urgent', 'executive'].forEach(function (s) {
            var tr = document.createElement('tr');
            if (s === sp) tr.className = 'table-primary fw-bold';
            var td1 = document.createElement('td'); td1.textContent = SPEED_LABEL[s];
            var td2 = document.createElement('td'); td2.textContent = fmtFee(d, d.fees[s]);
            var td3 = document.createElement('td'); td3.textContent = d.times[s] || '—';
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            cmpBody.appendChild(tr);
        });
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
