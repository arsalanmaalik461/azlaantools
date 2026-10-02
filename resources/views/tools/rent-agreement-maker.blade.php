@extends('layouts.app')

@section('title', 'Rent Agreement Maker - Azlaan Tools')
@section('meta_description', 'Create a rent agreement for a house or shop - fill the template and print or download it. Free online rent deed maker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3 no-print">Rent Agreement Maker</h1>
            <p class="lead text-muted no-print">Make a rent agreement for a shop or house — fill in the form, your agreement is ready, then print or download it.</p>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="landlordName" class="form-label fw-semibold">Landlord name</label>
                            <input type="text" class="form-control" id="landlordName" placeholder="e.g. Muhammad Arslan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="landlordCnic" class="form-label fw-semibold">Landlord CNIC</label>
                            <input type="text" class="form-control" id="landlordCnic" placeholder="e.g. 33100-1234567-8">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tenantName" class="form-label fw-semibold">Tenant name</label>
                            <input type="text" class="form-control" id="tenantName" placeholder="e.g. Muhammad Rehan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tenantCnic" class="form-label fw-semibold">Tenant CNIC</label>
                            <input type="text" class="form-control" id="tenantCnic" placeholder="e.g. 33100-7654321-0">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="propertyAddress" class="form-label fw-semibold">Property address (shop / house / plot)</label>
                            <input type="text" class="form-control" id="propertyAddress" placeholder="e.g. Shop No. 4, Main Market, Faisalabad">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="monthlyRent" class="form-label fw-semibold">Monthly rent (Rs.)</label>
                            <input type="number" class="form-control" id="monthlyRent" placeholder="e.g. 25000" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="advanceAmount" class="form-label fw-semibold">Advance / security (Rs.)</label>
                            <input type="number" class="form-control" id="advanceAmount" placeholder="e.g. 50000" min="0" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="agreementMonths" class="form-label fw-semibold">Duration (months)</label>
                            <input type="number" class="form-control" id="agreementMonths" placeholder="e.g. 11" min="1" value="11">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="startDate" class="form-label fw-semibold">Start date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="increasePct" class="form-label fw-semibold">Yearly increase % (if any)</label>
                            <input type="number" class="form-control" id="increasePct" placeholder="e.g. 10" min="0" value="0">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="extraTerms" class="form-label fw-semibold">Extra terms (optional, one term per line)</label>
                            <textarea class="form-control" id="extraTerms" rows="3" placeholder="e.g. The tenant will pay the electricity bill."></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Agreement</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="results" class="d-none">
                <div class="card shadow-sm mb-3 no-print">
                    <div class="card-body d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-success" id="printBtn">Print Agreement</button>
                        <button type="button" class="btn btn-outline-secondary" id="downloadBtn">Download as HTML</button>
                    </div>
                </div>
                <div id="printArea">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body" id="agreementDoc" style="font-family: Georgia, serif; line-height:1.9;"></div>
                    </div>
                </div>
                <p class="small text-muted no-print">Note: This template is for general information only, not legal advice. For stamp paper, notary or registration, contact a lawyer or stamp vendor.</p>
            </div>

            <h2 class="no-print">How to use</h2>
            <ol class="no-print">
                <li>Fill in the landlord, tenant and property details above.</li>
                <li>Enter the monthly rent, advance, duration and start date.</li>
                <li>Click <strong>Create Agreement</strong> — a formal agreement document is generated below.</li>
                <li>Print it or download it as an HTML file, then sign with the other party (and witnesses).</li>
            </ol>
        </div>
    </div>
</div>
<style>
@media print {
    body.print-agreement .no-print { display: none !important; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var ids = ['landlordName','landlordCnic','tenantName','tenantCnic','propertyAddress','monthlyRent','advanceAmount','agreementMonths','startDate','increasePct','extraTerms'];
    var el = {};
    ids.forEach(function (id) { el[id] = document.getElementById(id); });
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var agreementDoc = document.getElementById('agreementDoc');
    var printBtn = document.getElementById('printBtn');
    var downloadBtn = document.getElementById('downloadBtn');

    el.startDate.value = new Date().toISOString().slice(0, 10);

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
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function fmtDate(d) {
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    var ONES = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    var TENS = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
    function twoDigit(n) {
        if (n < 20) return ONES[n];
        return TENS[Math.floor(n / 10)] + (n % 10 ? ' ' + ONES[n % 10] : '');
    }
    function threeDigit(n) {
        var h = Math.floor(n / 100);
        var rest = n % 100;
        return (h ? ONES[h] + ' Hundred' + (rest ? ' ' : '') : '') + (rest ? twoDigit(rest) : '');
    }
    function numWords(n) {
        if (n === 0) return 'Zero';
        var parts = [];
        var crore = Math.floor(n / 10000000);
        var lakh = Math.floor((n % 10000000) / 100000);
        var thousand = Math.floor((n % 100000) / 1000);
        var rest = n % 1000;
        if (crore) parts.push(threeDigit(crore) + ' Crore');
        if (lakh) parts.push(twoDigit(lakh) + ' Lakh');
        if (thousand) parts.push(twoDigit(thousand) + ' Thousand');
        if (rest) parts.push(threeDigit(rest));
        return parts.join(' ');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var v = {};
        ['landlordName','tenantName','propertyAddress'].forEach(function (k) { v[k] = el[k].value.trim(); });
        ['landlordCnic','tenantCnic'].forEach(function (k) { v[k] = el[k].value.trim(); });
        var rent = parseFloat(el.monthlyRent.value);
        var advance = parseFloat(el.advanceAmount.value) || 0;
        var months = parseInt(el.agreementMonths.value, 10);
        var increase = parseFloat(el.increasePct.value) || 0;
        var extras = el.extraTerms.value.split('\n').map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 0; });

        if (!v.landlordName || !v.tenantName || !v.propertyAddress) { showError('Please enter the landlord, tenant and property address.'); return; }
        if (isNaN(rent) || rent <= 0) { showError('Please enter the monthly rent correctly.'); return; }
        if (isNaN(months) || months <= 0) { showError('Please enter the duration (months) correctly.'); return; }
        if (!el.startDate.value) { showError('Please select the start date.'); return; }

        var start = new Date(el.startDate.value + 'T00:00:00');
        var end = new Date(start.getFullYear(), start.getMonth() + months, start.getDate());
        var rentWords = numWords(Math.round(rent));
        var advWords = numWords(Math.round(advance));

        var terms = [
            'The tenant will use the property only for lawful residential / business purposes and will not carry out any illegal activity.',
            'The tenant will pay the rent on time. Rent must be paid by the 7th of each month.',
            'Electricity, gas, water and other utility bills are the responsibility of the tenant.',
            'The tenant will not make any permanent change to the property without the written permission of the landlord.',
            'At the end of the agreement, the tenant will hand the property back to the landlord in vacant condition.'
        ];
        if (increase > 0) terms.push('On renewal, the rent may be increased by up to ' + increase + '% within the new agreement.');
        extras.forEach(function (t) { terms.push(t); });
        var termsHtml = terms.map(function (t, i) { return '<p>' + (i + 1) + '. ' + esc(t) + '</p>'; }).join('');

        var html =
            '<h3 class="text-center mb-4">RENT AGREEMENT</h3>' +
            '<p>This rent agreement is made today, <strong>' + esc(fmtDate(start)) + '</strong>, between the parties:</p>' +
            '<p><strong>Party One (Landlord):</strong> ' + esc(v.landlordName) + (v.landlordCnic ? ', CNIC: ' + esc(v.landlordCnic) : '') + '</p>' +
            '<p><strong>Party Two (Tenant):</strong> ' + esc(v.tenantName) + (v.tenantCnic ? ', CNIC: ' + esc(v.tenantCnic) : '') + '</p>' +
            '<p><strong>Property:</strong> ' + esc(v.propertyAddress) + '</p>' +
            '<p><strong>Duration:</strong> ' + months + ' months, from ' + esc(fmtDate(start)) + ' to ' + esc(fmtDate(end)) + '.</p>' +
            '<p><strong>Monthly Rent:</strong> Rs. ' + Math.round(rent).toLocaleString('en-PK') + ' (' + rentWords + ' Rupees) per month.</p>' +
            (advance > 0 ? '<p><strong>Advance / Security:</strong> Rs. ' + Math.round(advance).toLocaleString('en-PK') + ' (' + advWords + ' Rupees), refundable at the end of the agreement.</p>' : '') +
            '<h5 class="mt-4">Terms:</h5>' + termsHtml +
            '<p class="mt-4">Both parties have signed this agreement after reading and understanding it.</p>' +
            '<div style="display:flex; justify-content:space-between; margin-top:60px;">' +
            '<div>___________________<br>Landlord<br><br>Date: __________</div>' +
            '<div>___________________<br>Tenant<br><br>Date: __________</div>' +
            '<div>___________________<br>Witness<br><br>Date: __________</div>' +
            '</div>';

        agreementDoc.innerHTML = html;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth' });
    });

    printBtn.addEventListener('click', function () {
        document.body.classList.add('print-agreement');
        window.print();
    });
    window.addEventListener('afterprint', function () {
        document.body.classList.remove('print-agreement');
    });

    downloadBtn.addEventListener('click', function () {
        var doc = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Rent Agreement</title></head>' +
            '<body style="font-family:Georgia,serif; line-height:1.9; max-width:800px; margin:40px auto; padding:0 20px;">' +
            agreementDoc.innerHTML + '</body></html>';
        var blob = new Blob([doc], { type: 'text/html' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'rent-agreement.html';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); }, 500);
    });
})();
</script>
@endsection
