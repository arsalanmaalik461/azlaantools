@extends('layouts.app')

@section('title', 'Dues Reminder Maker - WhatsApp Message - Azlaan Tools')
@section('meta_description', 'Create polite dues reminder messages for WhatsApp free. Write a payment reminder and send it directly on WhatsApp.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Dues Reminder Maker</h1>
            <p class="lead text-muted">Make a polite WhatsApp reminder message for your dues. Enter the customer name and the amount — your message will be ready, then copy it or send it directly on WhatsApp.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="custName" class="form-label fw-semibold">Customer name</label>
                            <input type="text" class="form-control" id="custName" placeholder="e.g. Ahmed Khan">
                        </div>
                        <div class="col-md-6">
                            <label for="amountInput" class="form-label fw-semibold">Due amount (Rs.)</label>
                            <input type="number" class="form-control" id="amountInput" placeholder="e.g. 5000" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="bizName" class="form-label fw-semibold">Your shop / business name</label>
                            <input type="text" class="form-control" id="bizName" placeholder="e.g. Azlaan Store">
                        </div>
                        <div class="col-md-6">
                            <label for="dueDate" class="form-label fw-semibold">Due date (optional)</label>
                            <input type="date" class="form-control" id="dueDate">
                        </div>
                        <div class="col-md-6">
                            <label for="toneSelect" class="form-label fw-semibold">Tone</label>
                            <select class="form-select" id="toneSelect">
                                <option value="polite">Polite</option>
                                <option value="friendly">Friendly</option>
                                <option value="firm">Firm (polite but strict)</option>
                                <option value="urdu">Urdu (fully in Urdu)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="phoneInput" class="form-label fw-semibold">Customer WhatsApp number (optional)</label>
                            <input type="text" class="form-control" id="phoneInput" placeholder="03XXXXXXXXX" inputmode="tel">
                            <div class="form-text">If you enter it, the "Send on WhatsApp" button will open the chat directly.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Create Message</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label for="msgOutput" class="form-label fw-semibold">Ready message</label>
                        <textarea class="form-control" id="msgOutput" rows="6" readonly></textarea>
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy Message</button>
                            <a class="btn btn-success" id="waBtn" href="#" target="_blank" rel="noopener">Send on WhatsApp</a>
                            <button type="button" class="btn btn-outline-secondary" id="newBtn">New Message</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the customer name, the due amount and your business name.</li>
                <li>Choose a tone — polite, friendly, firm or fully Urdu.</li>
                <li>Press "Create Message", then copy it or send it directly on WhatsApp.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var custName = document.getElementById('custName');
    var amountInput = document.getElementById('amountInput');
    var bizName = document.getElementById('bizName');
    var dueDate = document.getElementById('dueDate');
    var toneSelect = document.getElementById('toneSelect');
    var phoneInput = document.getElementById('phoneInput');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var waBtn = document.getElementById('waBtn');
    var newBtn = document.getElementById('newBtn');
    var msgOutput = document.getElementById('msgOutput');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function fmtAmount(n) {
        return 'Rs. ' + n.toLocaleString('en-PK');
    }

    function buildMessage(name, amt, biz, due, tone) {
        var amtStr = fmtAmount(amt);
        var dueStr = due ? ' (due date: ' + due.split('-').reverse().join('-') + ')' : '';
        if (tone === 'urdu') {
            return 'Assalam-o-Alaikum ' + name + ' sahib,\n\nHope you are well. A small reminder from ' + biz + ': an amount of ' + amtStr + ' is due from your side' + dueStr + '.\n\nPlease pay it when convenient. Thank you!';
        }
        if (tone === 'friendly') {
            return 'Hi ' + name + '! Hope you are doing well. Just a friendly reminder from ' + biz + ' that ' + amtStr + ' is still pending from your side' + dueStr + '. Please clear it whenever convenient. Thanks a lot!';
        }
        if (tone === 'firm') {
            return 'Dear ' + name + ', this is a reminder from ' + biz + ' regarding your outstanding balance of ' + amtStr + dueStr + '. Kindly settle the payment at the earliest to avoid any inconvenience. Thank you.';
        }
        return 'Assalam-o-Alaikum ' + name + ', hope you are well. A reminder from ' + biz + ': an amount of ' + amtStr + ' is due from your side' + dueStr + '. Please pay it soon. Thank you very much!';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var name = custName.value.trim();
        var amt = parseFloat(amountInput.value);
        var biz = bizName.value.trim();
        if (!name) { showError('Please enter the customer name.'); return; }
        if (!amt || amt <= 0) { showError('Please enter a valid amount.'); return; }
        if (!biz) { showError('Please enter your business name.'); return; }

        var msg = buildMessage(name, amt, biz, dueDate.value, toneSelect.value);
        msgOutput.value = msg;

        var phone = phoneInput.value.replace(/\D/g, '');
        if (phone.length === 11 && phone.charAt(0) === '0') {
            phone = '92' + phone.substring(1);
        }
        waBtn.href = phone
            ? 'https://wa.me/' + phone + '?text=' + encodeURIComponent(msg)
            : 'https://wa.me/?text=' + encodeURIComponent(msg);

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    copyBtn.addEventListener('click', function () {
        msgOutput.select();
        var done = function () {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy Message'; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(msgOutput.value).then(done).catch(function () {
                document.execCommand('copy');
                done();
            });
        } else {
            document.execCommand('copy');
            done();
        }
    });

    newBtn.addEventListener('click', function () {
        custName.value = '';
        amountInput.value = '';
        results.classList.add('d-none');
        custName.focus();
    });
})();
</script>
@endsection
