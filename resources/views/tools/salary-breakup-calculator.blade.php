@extends('layouts.app')

@section('title', 'Salary Breakup Calculator - Azlaan Tools')
@section('meta_description', 'Salary breakup — calculate basic pay, allowances and deductions, with a printable salary slip showing net pay.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Salary Breakup Calculator</h1>
            <p class="lead text-muted">Split salary into basic pay, allowances and deductions to find net pay. Data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Salary details</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sbName" class="form-label fw-semibold">Staff name (on slip)</label>
                            <input type="text" class="form-control" id="sbName" placeholder="Example: Ahmed Khan">
                        </div>
                        <div class="col-md-6">
                            <label for="sbMonth" class="form-label fw-semibold">Month</label>
                            <input type="month" class="form-control" id="sbMonth">
                        </div>
                        <div class="col-md-6">
                            <label for="sbGross" class="form-label fw-semibold">Monthly gross salary (Rs)</label>
                            <input type="number" class="form-control" id="sbGross" placeholder="50000" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="sbBasicPct" class="form-label fw-semibold">Basic pay (% of gross): <span id="basicPctLabel">50%</span></label>
                            <input type="range" class="form-range" id="sbBasicPct" min="30" max="70" value="50">
                            <div class="form-text">Most companies keep basic pay at 50%.</div>
                        </div>
                    </div>

                    <hr>
                    <h2 class="h5 mb-3">Allowances (Rs monthly)</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="sbHra" class="form-label fw-semibold">House rent (HRA)</label>
                            <input type="number" class="form-control" id="sbHra" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="sbConv" class="form-label fw-semibold">Conveyance</label>
                            <input type="number" class="form-control" id="sbConv" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="sbMed" class="form-label fw-semibold">Medical</label>
                            <input type="number" class="form-control" id="sbMed" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>

                    <hr>
                    <h2 class="h5 mb-3">Deductions (Rs monthly)</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="sbEobi" class="form-label fw-semibold">EOBI</label>
                            <input type="number" class="form-control" id="sbEobi" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="sbTax" class="form-label fw-semibold">Income tax</label>
                            <input type="number" class="form-control" id="sbTax" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="sbAdv" class="form-label fw-semibold">Advance deduction</label>
                            <input type="number" class="form-control" id="sbAdv" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4">
                        <button type="button" class="btn btn-primary" id="calcBtn">Calculate Breakup</button>
                        <button type="button" class="btn btn-outline-secondary" id="printBtn">Print Breakup Slip</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr><th>Component</th><th class="text-end">Rs (monthly)</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>Basic pay (<span id="rBasicPct">50</span>%)</td><td class="text-end" id="rBasic">-</td></tr>
                                    <tr><td>House rent allowance</td><td class="text-end" id="rHra">-</td></tr>
                                    <tr><td>Conveyance allowance</td><td class="text-end" id="rConv">-</td></tr>
                                    <tr><td>Medical allowance</td><td class="text-end" id="rMed">-</td></tr>
                                    <tr class="table-light"><th>Gross salary</th><th class="text-end" id="rGross">-</th></tr>
                                    <tr><td>EOBI</td><td class="text-end text-danger" id="rEobi">-</td></tr>
                                    <tr><td>Income tax</td><td class="text-end text-danger" id="rTax">-</td></tr>
                                    <tr><td>Advance deduction</td><td class="text-end text-danger" id="rAdv">-</td></tr>
                                    <tr class="table-light"><th>Total deductions</th><th class="text-end text-danger" id="rTotDed">-</th></tr>
                                    <tr class="table-success"><th>Net pay (in hand)</th><th class="text-end fs-5" id="rNet">-</th></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-warning d-none" id="warnBox" role="alert"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the monthly <strong>gross salary</strong> and set the basic pay %.</li>
                <li>Enter allowances (HRA, conveyance, medical) and deductions (EOBI, tax, advance deduction).</li>
                <li>Click <strong>Calculate Breakup</strong> — a warning appears if allowances exceed basic pay.</li>
                <li>Print the <strong>Breakup Slip</strong> and give it to the staff member.</li>
            </ol>
            <p class="text-muted small">Note: this is only a calculation tool — confirm tax and EOBI rates from official sources. Data stays in this browser.</p>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printSheet, #printSheet * { visibility: visible; }
    #printSheet { position: absolute; top: 0; left: 0; width: 100%; }
}
</style>
<div id="printSheet" class="d-none d-print-block"></div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_salary_breakup';
    var sbName = document.getElementById('sbName');
    var sbMonth = document.getElementById('sbMonth');
    var sbGross = document.getElementById('sbGross');
    var sbBasicPct = document.getElementById('sbBasicPct');
    var basicPctLabel = document.getElementById('basicPctLabel');
    var sbHra = document.getElementById('sbHra');
    var sbConv = document.getElementById('sbConv');
    var sbMed = document.getElementById('sbMed');
    var sbEobi = document.getElementById('sbEobi');
    var sbTax = document.getElementById('sbTax');
    var sbAdv = document.getElementById('sbAdv');
    var calcBtn = document.getElementById('calcBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var warnBox = document.getElementById('warnBox');
    var rBasicPct = document.getElementById('rBasicPct');
    var rBasic = document.getElementById('rBasic');
    var rHra = document.getElementById('rHra');
    var rConv = document.getElementById('rConv');
    var rMed = document.getElementById('rMed');
    var rGross = document.getElementById('rGross');
    var rEobi = document.getElementById('rEobi');
    var rTax = document.getElementById('rTax');
    var rAdv = document.getElementById('rAdv');
    var rTotDed = document.getElementById('rTotDed');
    var rNet = document.getElementById('rNet');
    var printSheet = document.getElementById('printSheet');

    var lastCalc = null;

    function num(el) {
        var v = Number(el.value);
        return isNaN(v) || v < 0 ? 0 : v;
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function saveLast(c) {
        try { localStorage.setItem(KEY, JSON.stringify(c)); } catch (e) {}
    }
    function currentMonth() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        return d.getFullYear() + '-' + m;
    }

    function compute() {
        hideError();
        var gross = num(sbGross);
        if (gross <= 0) { showError('Please enter a gross salary above 0.'); return null; }
        var pct = Number(sbBasicPct.value) || 50;
        var basic = Math.round(gross * pct / 100 * 100) / 100;
        var hra = num(sbHra), conv = num(sbConv), med = num(sbMed);
        var eobi = num(sbEobi), tax = num(sbTax), adv = num(sbAdv);
        var totDed = Math.round((eobi + tax + adv) * 100) / 100;
        var net = Math.round((gross - totDed) * 100) / 100;
        return {
            name: sbName.value.trim(),
            month: sbMonth.value || currentMonth(),
            gross: gross, pct: pct, basic: basic,
            hra: hra, conv: conv, med: med,
            eobi: eobi, tax: tax, adv: adv,
            totDed: totDed, net: net
        };
    }

    function render(c) {
        rBasicPct.textContent = c.pct;
        rBasic.textContent = fmt(c.basic);
        rHra.textContent = fmt(c.hra);
        rConv.textContent = fmt(c.conv);
        rMed.textContent = fmt(c.med);
        rGross.textContent = fmt(c.gross);
        rEobi.textContent = fmt(c.eobi);
        rTax.textContent = fmt(c.tax);
        rAdv.textContent = fmt(c.adv);
        rTotDed.textContent = fmt(c.totDed);
        rNet.textContent = fmt(c.net);
        results.classList.remove('d-none');
        var allowTotal = c.hra + c.conv + c.med;
        warnBox.classList.add('d-none');
        if (allowTotal > c.basic) {
            warnBox.textContent = 'Warning: allowances (' + fmt(allowTotal) + ') are more than basic pay (' + fmt(c.basic) + ') — increase the basic % or reduce allowances.';
            warnBox.classList.remove('d-none');
        }
        if (c.net < 0) {
            warnBox.textContent = 'Warning: deductions are more than the gross salary — net pay is negative.';
            warnBox.classList.remove('d-none');
        }
    }

    function slipHtml(c) {
        var rows = [
            ['Basic pay (' + c.pct + '%)', fmt(c.basic), ''],
            ['House rent allowance', fmt(c.hra), ''],
            ['Conveyance allowance', fmt(c.conv), ''],
            ['Medical allowance', fmt(c.med), ''],
            ['<strong>Gross salary</strong>', '<strong>' + fmt(c.gross) + '</strong>', 'table-light'],
            ['EOBI (deduction)', fmt(c.eobi), ''],
            ['Income tax (deduction)', fmt(c.tax), ''],
            ['Advance deduction (deduction)', fmt(c.adv), ''],
            ['<strong>Total deductions</strong>', '<strong>' + fmt(c.totDed) + '</strong>', 'table-light'],
            ['<strong>Net pay (in hand)</strong>', '<strong>' + fmt(c.net) + '</strong>', 'table-success']
        ];
        var html = '<div class="p-4"><h3>Salary Breakup Slip</h3>' +
            '<p><strong>Name:</strong> ' + esc(c.name || '—') + '<br>' +
            '<strong>Month:</strong> ' + esc(c.month) + '</p>' +
            '<table class="table table-bordered"><thead class="table-light"><tr><th>Component</th><th class="text-end">Rs (monthly)</th></tr></thead><tbody>';
        rows.forEach(function (r) {
            html += '<tr class="' + r[2] + '"><td>' + r[0] + '</td><td class="text-end">' + r[1] + '</td></tr>';
        });
        html += '</tbody></table>' +
            '<div class="row mt-5"><div class="col-6"><p>_____________<br>Employer signature</p></div>' +
            '<div class="col-6"><p>_____________<br>Employee signature</p></div></div>' +
            '<p class="text-muted small mt-3">Azlaan Tools — Salary Breakup Calculator</p></div>';
        return html;
    }

    sbBasicPct.addEventListener('input', function () {
        basicPctLabel.textContent = sbBasicPct.value + '%';
        var c = compute();
        if (c) { lastCalc = c; saveLast(c); render(c); }
    });

    calcBtn.addEventListener('click', function () {
        var c = compute();
        if (!c) { results.classList.add('d-none'); return; }
        lastCalc = c;
        saveLast(c);
        render(c);
    });

    printBtn.addEventListener('click', function () {
        var c = lastCalc || compute();
        if (!c) return;
        printSheet.innerHTML = slipHtml(c);
        printSheet.classList.remove('d-none');
        window.print();
        printSheet.classList.add('d-none');
    });

    sbMonth.value = currentMonth();
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var prev = JSON.parse(raw);
            if (prev && prev.gross) {
                sbGross.value = prev.gross;
                sbBasicPct.value = prev.pct || 50;
                basicPctLabel.textContent = sbBasicPct.value + '%';
                sbHra.value = prev.hra || '';
                sbConv.value = prev.conv || '';
                sbMed.value = prev.med || '';
                sbEobi.value = prev.eobi || '';
                sbTax.value = prev.tax || '';
                sbAdv.value = prev.adv || '';
            }
        }
    } catch (e) {}
})();
</script>
@endsection
