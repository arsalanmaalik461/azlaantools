@extends('layouts.app')

@section('title', 'Sales Tax Invoice Checker - Azlaan Tools')
@section('meta_description', 'Check whether your bill meets the required conditions of an FBR sales tax invoice — interactive checklist (informational, not official).')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Sales Tax Invoice Checker</h1>
            <p class="lead text-muted">Match your bill against the required conditions of a sales tax invoice — tick everything that appears on your bill and get an automatic verdict.</p>

            <div class="alert alert-warning" role="alert">
                <strong>Important:</strong> This is only an informational checklist — not an official FBR tool. For actual filing or legal questions, contact the FBR website or a tax consultant.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Checklist — does your bill have these?</h5>
                    <p class="text-muted small">Tick each item that is printed/written on your bill.</p>
                    <div id="stList" class="list-group mb-3"></div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="stCheck">Check Verdict</button>
                        <button type="button" class="btn btn-outline-secondary" id="stReset">Reset</button>
                    </div>

                    <div id="stVerdict" class="mt-4 d-none">
                        <div class="alert" id="stVerdictBox" role="alert">
                            <h5 class="alert-heading" id="stVerdictTitle"></h5>
                            <p id="stVerdictText" class="mb-0"></p>
                        </div>
                        <div id="stMissing" class="d-none">
                            <h6>These items are missing:</h6>
                            <ul class="list-group" id="stMissingList"></ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">What each condition means (short guide)</h5>
                    <div class="accordion" id="stGuide">
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stg1">Seller STRN and NTN</button></h2>
                            <div id="stg1" class="accordion-collapse collapse" data-bs-parent="#stGuide"><div class="accordion-body">A sales tax invoice must show the seller <strong>STRN</strong> (Sales Tax Registration Number). Without an STRN a sales tax invoice is not complete for a registered person. NTN is also usually on the bill.</div></div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stg2">Separate tax line (separate tax amount)</button></h2>
                            <div id="stg2" class="accordion-collapse collapse" data-bs-parent="#stGuide"><div class="accordion-body">The bill must clearly show <strong>value before tax</strong>, <strong>sales tax amount on a separate line</strong> and <strong>total amount with tax</strong>. Only writing the total is not enough.</div></div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stg3">Invoice serial number and date</button></h2>
                            <div id="stg3" class="accordion-collapse collapse" data-bs-parent="#stGuide"><div class="accordion-body">Each invoice must have a <strong>unique serial number</strong> (in sequence) and an <strong>issue date</strong> so the record can be traced.</div></div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stg4">Buyer details</button></h2>
                            <div id="stg4" class="accordion-collapse collapse" data-bs-parent="#stGuide"><div class="accordion-body">The buyer name and address must be written. If the buyer is registered, their <strong>STRN/NTN</strong> must also be on the invoice.</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Keep your bill in front of you and tick each item in the checklist that appears on the bill.</li>
                <li>Press <strong>Check Verdict</strong> — you will get "Compliant" or a list of missing items.</li>
                <li>Get the missing items added to the bill or get a new correct bill from the seller.</li>
            </ol>
            <p class="small text-muted">Disclaimer: this tool gives informational guidance only — not official FBR filing or legal advice. Actual requirements can change under FBR rules.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var ITEMS = [
        { key: 'sellerName', label: 'Seller name and address', must: true },
        { key: 'sellerStrn', label: 'Seller STRN (Sales Tax Registration Number)', must: true },
        { key: 'sellerNtn', label: 'Seller NTN', must: false },
        { key: 'serial', label: 'Invoice serial number (unique)', must: true },
        { key: 'date', label: 'Invoice date (date of issue)', must: true },
        { key: 'desc', label: 'Goods/services description', must: true },
        { key: 'qty', label: 'Quantity', must: false },
        { key: 'valueEx', label: 'Value before tax (value exclusive of tax)', must: true },
        { key: 'taxLine', label: 'Sales tax amount on a SEPARATE line', must: true },
        { key: 'taxRate', label: 'Tax rate (for example: 18%)', must: true },
        { key: 'valueIn', label: 'Total with tax (total inclusive of tax)', must: true },
        { key: 'buyerName', label: 'Buyer name and address', must: true },
        { key: 'buyerStrn', label: 'Buyer STRN/NTN (if buyer is registered)', must: false }
    ];

    var stList = document.getElementById('stList');
    var stCheck = document.getElementById('stCheck');
    var stReset = document.getElementById('stReset');
    var stVerdict = document.getElementById('stVerdict');
    var stVerdictBox = document.getElementById('stVerdictBox');
    var stVerdictTitle = document.getElementById('stVerdictTitle');
    var stVerdictText = document.getElementById('stVerdictText');
    var stMissing = document.getElementById('stMissing');
    var stMissingList = document.getElementById('stMissingList');

    function buildList() {
        stList.innerHTML = '';
        ITEMS.forEach(function (it) {
            var label = document.createElement('label');
            label.className = 'list-group-item d-flex align-items-start gap-2';
            label.style.cursor = 'pointer';
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input mt-1';
            cb.id = 'st_' + it.key;
            var span = document.createElement('span');
            span.innerHTML = '<strong>' + it.label + '</strong>' +
                (it.must ? ' <span class="badge bg-danger ms-1">Required</span>' : ' <span class="badge bg-secondary ms-1">Recommended</span>');
            label.appendChild(cb);
            label.appendChild(span);
            stList.appendChild(label);
        });
    }

    function verdict() {
        var missingMust = [];
        var missingOpt = [];
        ITEMS.forEach(function (it) {
            var cb = document.getElementById('st_' + it.key);
            if (!cb || !cb.checked) {
                if (it.must) missingMust.push(it.label);
                else missingOpt.push(it.label);
            }
        });
        stVerdict.classList.remove('d-none');
        stVerdictBox.classList.remove('alert-success', 'alert-danger', 'alert-warning');
        stMissingList.innerHTML = '';
        if (missingMust.length === 0) {
            stVerdictBox.classList.add('alert-success');
            stVerdictTitle.textContent = 'Compliant — your bill seems to meet the conditions';
            var txt = 'All required fields are present. ';
            if (missingOpt.length) {
                txt += 'Better to also add these recommended items: ' + missingOpt.join('; ') + '.';
            } else {
                txt += 'All recommended items are present too.';
            }
            stVerdictText.textContent = txt;
            stMissing.classList.add('d-none');
        } else if (missingMust.length <= 3) {
            stVerdictBox.classList.add('alert-warning');
            stVerdictTitle.textContent = 'Almost there — some required items are missing';
            stVerdictText.textContent = 'Get these ' + missingMust.length + ' required items added to the bill below.';
            stMissing.classList.remove('d-none');
            missingMust.forEach(function (m) {
                var li = document.createElement('li');
                li.className = 'list-group-item list-group-item-warning';
                li.textContent = m;
                stMissingList.appendChild(li);
            });
        } else {
            stVerdictBox.classList.add('alert-danger');
            stVerdictTitle.textContent = 'Not compliant — your bill is missing many required items';
            stVerdictText.textContent = missingMust.length + ' required fields are missing. This does not meet the conditions of a sales tax invoice — get a correct bill from the seller.';
            stMissing.classList.remove('d-none');
            missingMust.forEach(function (m) {
                var li = document.createElement('li');
                li.className = 'list-group-item list-group-item-danger';
                li.textContent = m;
                stMissingList.appendChild(li);
            });
        }
        stVerdict.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    stCheck.addEventListener('click', verdict);
    stReset.addEventListener('click', function () {
        buildList();
        stVerdict.classList.add('d-none');
    });

    buildList();
})();
</script>
@endsection
