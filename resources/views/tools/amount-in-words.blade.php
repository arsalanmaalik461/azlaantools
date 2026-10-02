@extends('layouts.app')

@section('title', 'Amount in Words - Azlaan Tools')
@section('meta_description', 'Write invoice and cheque amounts in words — PKR and USD, with paisa/cents. Free number to words converter.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Amount in Words</h1>
            <p class="lead text-muted">Type the amount and get it ready in words — for cheques and invoices. Both PKR (lakh/crore) and USD systems.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="awAmount" class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg" id="awAmount" placeholder="Example: 45000.50" min="0" step="0.01">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="awCurrency" class="form-label fw-semibold">Currency</label>
                            <select class="form-select" id="awCurrency">
                                <option value="PKR">PKR — Rupees</option>
                                <option value="USD">USD — Dollars</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="awSystem" class="form-label fw-semibold">Numbering system</label>
                            <select class="form-select" id="awSystem">
                                <option value="pk">Pakistani (lakh/crore)</option>
                                <option value="intl">International (million)</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="awConvert">Convert</button>
                        <button type="button" class="btn btn-outline-secondary" id="awClear">Clear</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="awError" role="alert"></div>

                    <div id="awResult" class="mt-4 d-none">
                        <label class="form-label fw-semibold">Result</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="fs-5 mb-1" id="awWords"></p>
                            <p class="text-muted small mb-0" id="awTitleWords"></p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button type="button" class="btn btn-outline-success" id="awCopy">Copy</button>
                        </div>
                        <div class="alert alert-success mt-2 d-none py-2" id="awCopied">Copied!</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Examples</h5>
                    <ul class="mb-0">
                        <li>45000 &rarr; <em>Rupees forty-five thousand only</em></li>
                        <li>125000.75 (Pakistani) &rarr; <em>Rupees one lakh twenty-five thousand and paisa seventy-five only</em></li>
                        <li>125000.75 (International) &rarr; <em>Rupees one hundred twenty-five thousand and paisa seventy-five only</em></li>
                        <li>1500.25 (USD) &rarr; <em>Dollars one thousand five hundred and cents twenty-five only</em></li>
                    </ul>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the amount (decimals are fine).</li>
                <li>Select the <strong>Currency</strong> and <strong>Numbering system</strong>.</li>
                <li>Press <strong>Convert</strong>, then use <strong>Copy</strong> to paste it into your cheque or invoice.</li>
            </ol>
            <p class="small text-muted">This tool is for information only — for official banking or legal wording, please confirm with your bank.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var awAmount = document.getElementById('awAmount');
    var awCurrency = document.getElementById('awCurrency');
    var awSystem = document.getElementById('awSystem');
    var awConvert = document.getElementById('awConvert');
    var awClear = document.getElementById('awClear');
    var awError = document.getElementById('awError');
    var awResult = document.getElementById('awResult');
    var awWords = document.getElementById('awWords');
    var awTitleWords = document.getElementById('awTitleWords');
    var awCopy = document.getElementById('awCopy');
    var awCopied = document.getElementById('awCopied');

    var ONES = ['zero','one','two','three','four','five','six','seven','eight','nine','ten',
        'eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen'];
    var TENS = ['','','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];

    function twoDigits(n) {
        if (n < 20) return ONES[n];
        var t = TENS[Math.floor(n / 10)];
        var r = n % 10;
        return r ? t + '-' + ONES[r] : t;
    }
    function threeDigits(n) {
        var out = '';
        if (n >= 100) {
            out += ONES[Math.floor(n / 100)] + ' hundred';
            n = n % 100;
            if (n) out += ' ';
        }
        if (n) out += twoDigits(n);
        return out;
    }
    function wordsIntl(n) {
        if (n === 0) return 'zero';
        var scales = [[1e12, 'trillion'], [1e9, 'billion'], [1e6, 'million'], [1e3, 'thousand']];
        var out = [];
        for (var i = 0; i < scales.length; i++) {
            if (n >= scales[i][0]) {
                out.push(threeDigits(Math.floor(n / scales[i][0])) + ' ' + scales[i][1]);
                n = n % scales[i][0];
            }
        }
        if (n) out.push(threeDigits(n));
        return out.join(' ');
    }
    function wordsPk(n) {
        if (n === 0) return 'zero';
        var scales = [[1e9, 'arab'], [1e7, 'crore'], [1e5, 'lakh'], [1e3, 'thousand']];
        var out = [];
        for (var i = 0; i < scales.length; i++) {
            if (n >= scales[i][0]) {
                out.push(threeDigits(Math.floor(n / scales[i][0])) + ' ' + scales[i][1]);
                n = n % scales[i][0];
            }
        }
        if (n) out.push(threeDigits(n));
        return out.join(' ');
    }
    function titleCase(s) {
        return s.replace(/(^|\s|-)([a-z])/g, function (m, p1, p2) { return p1 + p2.toUpperCase(); });
    }

    function showError(msg) {
        awError.textContent = msg;
        awError.classList.remove('d-none');
        awResult.classList.add('d-none');
    }
    function hideError() {
        awError.classList.add('d-none');
        awError.textContent = '';
    }

    function convert() {
        hideError();
        awCopied.classList.add('d-none');
        var val = parseFloat(awAmount.value);
        if (isNaN(val) || val < 0) { showError('Enter a valid amount (0 or more).'); return; }
        if (val >= 1e15) { showError('This amount is too large to support.'); return; }
        var intPart = Math.floor(val);
        var decPart = Math.round((val - intPart) * 100);
        if (decPart === 100) { intPart += 1; decPart = 0; }
        var cur = awCurrency.value;
        var sys = awSystem.value;
        var w = sys === 'pk' ? wordsPk(intPart) : wordsIntl(intPart);
        var main = cur === 'PKR' ? 'Rupees' : 'Dollars';
        var sub = cur === 'PKR' ? 'paisa' : 'cents';
        var text = main + ' ' + w;
        if (decPart > 0) {
            text += ' and ' + sub + ' ' + twoDigits(decPart);
        }
        text += ' only';
        awWords.textContent = text;
        awTitleWords.textContent = titleCase(text);
        awResult.classList.remove('d-none');
    }

    awConvert.addEventListener('click', convert);
    awAmount.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); convert(); }
    });
    awCurrency.addEventListener('change', convert);
    awSystem.addEventListener('change', convert);
    awClear.addEventListener('click', function () {
        hideError();
        awAmount.value = '';
        awResult.classList.add('d-none');
        awCopied.classList.add('d-none');
        awAmount.focus();
    });
    awCopy.addEventListener('click', function () {
        var txt = awWords.textContent;
        if (!txt) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(function () {
                awCopied.classList.remove('d-none');
            }, function () { showError('Copy failed.'); });
        } else { showError('Copy failed.'); }
    });
})();
</script>
@endsection
