@extends('layouts.app')

@section('title', 'PayPal Fee Calculator - Azlaan Tools')
@section('meta_description', 'Calculate PayPal transaction fees and see exactly how much you receive or need to send. Free online PayPal fee calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PayPal Fee Calculator</h1>
            <p class="lead text-muted">See how much you get after fees — or how much you need to send to receive a set amount. A simple PayPal fee calculation.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Calculation mode</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="calcMode" id="modeSend" value="send" checked>
                            <label class="form-check-label" for="modeSend">I am sending this amount — how much will be received?</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="calcMode" id="modeReceive" value="receive">
                            <label class="form-check-label" for="modeReceive">I want to receive this amount — how much must I send?</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="amountInput" class="form-label fw-semibold">Amount</label>
                        <input type="number" class="form-control" id="amountInput" placeholder="e.g. 100" min="0" step="0.01">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="typeSel" class="form-label fw-semibold">Transaction type</label>
                            <select class="form-select" id="typeSel">
                                <option value="domestic" selected>Domestic — Goods &amp; Services (4.4% + $0.30)</option>
                                <option value="international">International — Goods &amp; Services (5.9% + $0.30)</option>
                                <option value="micro">Micropayments (5% + $0.05)</option>
                                <option value="friends">Friends &amp; Family (no fee)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fixedInput" class="form-label fw-semibold">Fixed fee (currency units)</label>
                            <input type="number" class="form-control" id="fixedInput" value="0.30" min="0" step="0.01">
                            <div class="form-text">Different currencies have different fixed fees — change it to match your currency.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Fee</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Result</h2>
                        <table class="table table-bordered" id="resultTable">
                            <tbody></tbody>
                        </table>
                        <p class="small text-muted mb-0">PayPal rates can change over time — confirm on the official website before the final calculation. This calculator gives an estimate.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose whether you are <strong>sending</strong> an amount or want to <strong>receive</strong> a fixed amount.</li>
                <li>Enter the amount and pick the transaction type (domestic, international, micropayment, or friends &amp; family).</li>
                <li>Adjust the fixed fee if your currency uses a different one, then click <strong>Calculate Fee</strong>.</li>
                <li>The table shows the fee, what is received, and what must be sent.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var amountInput = document.getElementById('amountInput');
    var typeSel = document.getElementById('typeSel');
    var fixedInput = document.getElementById('fixedInput');
    var modeSend = document.getElementById('modeSend');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultTable = document.getElementById('resultTable').querySelector('tbody');

    var RATES = {
        domestic: 0.044,
        international: 0.059,
        micro: 0.05,
        friends: 0.0
    };
    var DEFAULT_FIXED = {
        domestic: 0.30,
        international: 0.30,
        micro: 0.05,
        friends: 0.00
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) {
        return n.toFixed(2);
    }
    function row(label, value, strong) {
        var tr = document.createElement('tr');
        var td1 = document.createElement('td');
        td1.textContent = label;
        var td2 = document.createElement('td');
        td2.className = 'text-end';
        td2.textContent = value;
        if (strong) { td1.style.fontWeight = 'bold'; td2.style.fontWeight = 'bold'; }
        tr.appendChild(td1);
        tr.appendChild(td2);
        return tr;
    }

    typeSel.addEventListener('change', function () {
        fixedInput.value = DEFAULT_FIXED[typeSel.value].toFixed(2);
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var amount = parseFloat(amountInput.value);
        var fixed = parseFloat(fixedInput.value);
        if (isNaN(amount) || amount <= 0) { showError('Please enter a valid amount greater than zero.'); return; }
        if (isNaN(fixed) || fixed < 0) { showError('Please enter a valid fixed fee.'); return; }
        var rate = RATES[typeSel.value];
        var typeName = typeSel.options[typeSel.selectedIndex].text;
        resultTable.innerHTML = '';

        if (modeSend.checked) {
            var fee = amount * rate + fixed;
            if (typeSel.value === 'friends') fee = 0;
            var received = amount - fee;
            if (received < 0) { showError('The amount is so low that the fee is more than the amount. Increase it and try again.'); return; }
            resultTable.appendChild(row('You send', fmt(amount)));
            resultTable.appendChild(row('PayPal fee (' + (rate * 100).toFixed(1) + '% + ' + fmt(fixed) + ')', fmt(fee)));
            resultTable.appendChild(row('Receiver gets', fmt(received), true));
            resultTable.appendChild(row('Fee as % of amount', (fee / amount * 100).toFixed(2) + '%'));
        } else {
            var want = amount;
            var send, fee2;
            if (rate === 0 && fixed === 0) {
                send = want; fee2 = 0;
            } else {
                send = (want + fixed) / (1 - rate);
                fee2 = send - want;
            }
            resultTable.appendChild(row('You want to receive', fmt(want)));
            resultTable.appendChild(row('PayPal fee', fmt(fee2)));
            resultTable.appendChild(row('You must send', fmt(send), true));
        }
        var note = document.createElement('tr');
        var td = document.createElement('td');
        td.colSpan = 2;
        td.className = 'small text-muted';
        td.textContent = 'Type: ' + typeName;
        note.appendChild(td);
        resultTable.appendChild(note);
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
