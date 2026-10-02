@extends('layouts.app')
@section('title', 'Job Card Maker for Repair Shops Online Free — Azlaan Tools')
@section('meta_description', 'Make professional repair job cards for your workshop online for free. Customer, device, fault and charges record with print and download. No signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Job Card Maker</h1>
            <p class="lead text-muted">Make a professional job card for your repair workshop — a complete record of customer, device, fault and charges. Print it or download it.</p>

            <div class="row g-4">
                <div class="col-12 col-lg-6 no-print">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Job card details</h2>
                            <div class="mb-3">
                                <label for="shopName" class="form-label fw-semibold">Workshop / shop name</label>
                                <input type="text" class="form-control" id="shopName" placeholder="e.g. City Mobile Repair">
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="jobNo" class="form-label fw-semibold">Job card no.</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="jobNo" placeholder="JC-0001">
                                        <button type="button" class="btn btn-outline-secondary" id="genBtn" title="Generate new number">New</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="jobDate" class="form-label fw-semibold">Date</label>
                                    <input type="date" class="form-control" id="jobDate">
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-6">
                                    <label for="custName" class="form-label fw-semibold">Customer name</label>
                                    <input type="text" class="form-control" id="custName" placeholder="Customer name">
                                </div>
                                <div class="col-6">
                                    <label for="custPhone" class="form-label fw-semibold">Phone</label>
                                    <input type="text" class="form-control" id="custPhone" placeholder="03xx-xxxxxxx" inputmode="tel">
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-6">
                                    <label for="deviceType" class="form-label fw-semibold">Device type</label>
                                    <select class="form-select" id="deviceType">
                                        <option>Mobile phone</option>
                                        <option>Laptop / Computer</option>
                                        <option>Air conditioner</option>
                                        <option>Refrigerator</option>
                                        <option>Television</option>
                                        <option>Washing machine</option>
                                        <option>Generator / UPS</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="deviceModel" class="form-label fw-semibold">Brand / model</label>
                                    <input type="text" class="form-control" id="deviceModel" placeholder="e.g. Samsung A12">
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="faultDesc" class="form-label fw-semibold">Fault / problem</label>
                                <textarea class="form-control" id="faultDesc" rows="2" placeholder="What is the problem? e.g. Screen is broken, not charging"></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-4">
                                    <label for="charges" class="form-label fw-semibold">Charges (Rs)</label>
                                    <input type="number" class="form-control" id="charges" min="0" placeholder="0">
                                </div>
                                <div class="col-4">
                                    <label for="advance" class="form-label fw-semibold">Advance (Rs)</label>
                                    <input type="number" class="form-control" id="advance" min="0" placeholder="0">
                                </div>
                                <div class="col-4">
                                    <label for="deliverDate" class="form-label fw-semibold">Delivery date</label>
                                    <input type="date" class="form-control" id="deliverDate">
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="notes" class="form-label fw-semibold">Notes (optional)</label>
                                <input type="text" class="form-control" id="notes" placeholder="e.g. Customer is responsible for their data">
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="goBtn">Update Preview</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div id="printArea">
                        <div class="card shadow-sm" id="cardPreview">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold fs-5" id="pvShop">Your Workshop</div>
                                        <div class="small">JOB CARD</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold" id="pvJobNo">JC-0001</div>
                                        <div class="small" id="pvDate">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr><th style="width:38%">Customer</th><td id="pvCust">—</td></tr>
                                        <tr><th>Phone</th><td id="pvPhone">—</td></tr>
                                        <tr><th>Device</th><td id="pvDevice">—</td></tr>
                                        <tr><th>Fault</th><td id="pvFault">—</td></tr>
                                        <tr><th>Total charges</th><td id="pvCharges">Rs 0</td></tr>
                                        <tr><th>Advance received</th><td id="pvAdvance">Rs 0</td></tr>
                                        <tr class="table-light"><th>Balance due</th><td class="fw-bold" id="pvBalance">Rs 0</td></tr>
                                        <tr><th>Delivery date</th><td id="pvDeliver">—</td></tr>
                                        <tr><th>Notes</th><td id="pvNotes">—</td></tr>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between mt-4 pt-2">
                                    <div class="text-center"><div style="border-top:1px solid #333; width:130px; padding-top:4px;" class="small">Customer signature</div></div>
                                    <div class="text-center"><div style="border-top:1px solid #333; width:130px; padding-top:4px;" class="small">Workshop stamp</div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3 no-print">
                        <button type="button" class="btn btn-success" id="printBtn">Print Job Card</button>
                        <button type="button" class="btn btn-outline-primary" id="dlBtn">Download (HTML)</button>
                    </div>
                </div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Fill in the workshop, customer and device details on the left.</li>
                <li>Enter charges and advance — <strong>balance due</strong> is calculated automatically.</li>
                <li>Check the live preview on the right, then <strong>Print</strong> or <strong>Download</strong> the job card.</li>
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
    var ids = ['shopName', 'jobNo', 'jobDate', 'custName', 'custPhone', 'deviceType', 'deviceModel', 'faultDesc', 'charges', 'advance', 'deliverDate', 'notes'];
    var goBtn = document.getElementById('goBtn');
    var genBtn = document.getElementById('genBtn');
    var printBtn = document.getElementById('printBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');

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
    function dash(v) {
        return v ? v : '—';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function rs(n) {
        var x = parseFloat(n);
        if (isNaN(x) || x < 0) { x = 0; }
        return 'Rs ' + Math.round(x).toLocaleString('en-PK');
    }

    function updatePreview() {
        document.getElementById('pvShop').textContent = dash(val('shopName')) === '—' ? 'Your Workshop' : val('shopName');
        document.getElementById('pvJobNo').textContent = dash(val('jobNo'));
        document.getElementById('pvDate').textContent = dash(val('jobDate'));
        document.getElementById('pvCust').textContent = dash(val('custName'));
        document.getElementById('pvPhone').textContent = dash(val('custPhone'));
        var dev = val('deviceType');
        if (val('deviceModel')) { dev += ' — ' + val('deviceModel'); }
        document.getElementById('pvDevice').textContent = dash(dev);
        document.getElementById('pvFault').textContent = dash(val('faultDesc'));
        document.getElementById('pvCharges').textContent = rs(val('charges'));
        document.getElementById('pvAdvance').textContent = rs(val('advance'));
        var c = parseFloat(val('charges')) || 0;
        var a = parseFloat(val('advance')) || 0;
        var bal = c - a;
        if (bal < 0) { bal = 0; }
        document.getElementById('pvBalance').textContent = 'Rs ' + Math.round(bal).toLocaleString('en-PK');
        document.getElementById('pvDeliver').textContent = dash(val('deliverDate'));
        document.getElementById('pvNotes').textContent = dash(val('notes'));
    }

    function makeJobNo() {
        var d = new Date();
        var y = d.getFullYear();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var dd = ('0' + d.getDate()).slice(-2);
        var rand = Math.floor(100 + Math.random() * 900);
        document.getElementById('jobNo').value = 'JC-' + y + m + dd + '-' + rand;
        updatePreview();
    }

    genBtn.addEventListener('click', makeJobNo);

    goBtn.addEventListener('click', function () {
        hideError();
        if (!val('custName')) { showError('Please enter the customer name.'); return; }
        if (!val('faultDesc')) { showError('Please describe the fault.'); return; }
        updatePreview();
    });

    ids.forEach(function (id) {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    printBtn.addEventListener('click', function () {
        updatePreview();
        window.print();
    });

    dlBtn.addEventListener('click', function () {
        updatePreview();
        var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Job Card ' + esc(dash(val('jobNo'))) + '</title>';
        html += '<style>body{font-family:Arial,sans-serif;padding:24px;color:#222}table{border-collapse:collapse;width:100%;max-width:600px}th,td{border:1px solid #999;padding:8px;text-align:left}th{background:#f2f2f2;width:38%}h1{font-size:22px}.head{display:flex;justify-content:space-between;max-width:600px;border-bottom:3px solid #0d6efd;padding-bottom:8px;margin-bottom:12px}</style></head><body>';
        html += '<div class="head"><div><h1>' + esc(document.getElementById('pvShop').textContent) + '</h1><div>JOB CARD</div></div><div style="text-align:right"><strong>' + esc(document.getElementById('pvJobNo').textContent) + '</strong><div>' + esc(document.getElementById('pvDate').textContent) + '</div></div></div>';
        html += '<table>';
        var rows = [['Customer', 'pvCust'], ['Phone', 'pvPhone'], ['Device', 'pvDevice'], ['Fault', 'pvFault'], ['Total charges', 'pvCharges'], ['Advance received', 'pvAdvance'], ['Balance due', 'pvBalance'], ['Delivery date', 'pvDeliver'], ['Notes', 'pvNotes']];
        rows.forEach(function (r) {
            html += '<tr><th>' + r[0] + '</th><td>' + esc(document.getElementById(r[1]).textContent) + '</td></tr>';
        });
        html += '</table><p style="margin-top:30px;max-width:600px;display:flex;justify-content:space-between"><span>Customer signature: ____________</span><span>Workshop stamp: ____________</span></p></body></html>';
        var blob = new Blob([html], { type: 'text/html' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = (val('jobNo') || 'job-card').replace(/[^a-zA-Z0-9_-]/g, '') + '.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
    });

    (function init() {
        var d = new Date();
        var iso = d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
        document.getElementById('jobDate').value = iso;
        makeJobNo();
    })();
})();
</script>
@endsection
