@extends('layouts.app')

@section('title', 'Payment Receipt Maker - Azlaan Tools')
@section('meta_description', 'Create a professional payment receipt for cash or online payments, free online. Make a receipt and print or download it.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Payment Receipt Maker</h1>
            <p class="lead text-muted">Make a receipt when you get a payment from a customer — proof of a cash or online payment. Fill in the details, see the receipt, and print or download it as PNG.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bizName" class="form-label fw-semibold">Business / shop name</label>
                            <input type="text" class="form-control" id="bizName" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="receiptNo" class="form-label fw-semibold">Receipt number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="receiptNo" value="R-0001">
                                <button type="button" class="btn btn-outline-secondary" id="autoNo">Auto</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="recvFrom" class="form-label fw-semibold">Received from (customer name)</label>
                            <input type="text" class="form-control" id="recvFrom" placeholder="Customer name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="recvDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="recvDate">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="amount" min="1" placeholder="e.g. 25000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payMethod" class="form-label fw-semibold">Payment method</label>
                            <select class="form-select" id="payMethod">
                                <option>Cash</option>
                                <option>Bank Transfer</option>
                                <option>Easypaisa</option>
                                <option>JazzCash</option>
                                <option>Cheque</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="purpose" class="form-label fw-semibold">Purpose of payment</label>
                        <input type="text" class="form-control" id="purpose" placeholder="e.g. Solar panel advance payment">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes (optional)</label>
                        <input type="text" class="form-control" id="notes" placeholder="Any extra detail">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Receipt</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div id="printArea">
                            <div class="border rounded p-4 bg-white" id="receiptCard" style="border-width:2px !important;">
                                <div class="text-center mb-3">
                                    <h3 class="mb-1" id="rBiz">Business Name</h3>
                                    <div class="text-muted small">PAYMENT RECEIPT</div>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <div><strong>Receipt No:</strong> <span id="rNo"></span></div>
                                    <div><strong>Date:</strong> <span id="rDate"></span></div>
                                </div>
                                <hr>
                                <p><strong>Received from:</strong> <span id="rFrom"></span></p>
                                <p><strong>Amount:</strong> Rs <span id="rAmt"></span></p>
                                <p><strong>In words:</strong> <span id="rWords" class="fst-italic"></span></p>
                                <p><strong>Payment method:</strong> <span id="rMethod"></span></p>
                                <p><strong>Purpose:</strong> <span id="rPurpose"></span></p>
                                <p id="rNotesRow" class="d-none"><strong>Notes:</strong> <span id="rNotes"></span></p>
                                <hr>
                                <div class="d-flex justify-content-between mt-5">
                                    <div class="text-center"><div style="border-top:1px solid #333; width:160px; padding-top:4px;">Receiver signature</div></div>
                                    <div class="text-center"><div style="border-top:1px solid #333; width:160px; padding-top:4px;">Customer signature</div></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" class="btn btn-success" id="dlBtn">Download PNG</button>
                            <button type="button" class="btn btn-outline-primary" id="printBtn">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the business name, customer name, amount and date.</li>
                <li>Press <strong>Make Receipt</strong> — the amount is written in words automatically.</li>
                <li>Print it or download the PNG and send it on WhatsApp.</li>
            </ol>
        </div>
    </div>
</div>
<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var bizName = document.getElementById('bizName');
    var receiptNo = document.getElementById('receiptNo');
    var autoNo = document.getElementById('autoNo');
    var recvFrom = document.getElementById('recvFrom');
    var recvDate = document.getElementById('recvDate');
    var amount = document.getElementById('amount');
    var payMethod = document.getElementById('payMethod');
    var purpose = document.getElementById('purpose');
    var notes = document.getElementById('notes');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    recvDate.value = new Date().toISOString().slice(0, 10);
    autoNo.addEventListener('click', function () {
        receiptNo.value = 'R-' + String(Math.floor(1000 + Math.random() * 9000));
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    var ONES = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    var TENS = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    function twoDigits(n) {
        if (n < 20) { return ONES[n]; }
        return TENS[Math.floor(n / 10)] + (n % 10 ? ' ' + ONES[n % 10] : '');
    }
    function threeDigits(n) {
        var h = Math.floor(n / 100), r = n % 100, out = '';
        if (h) { out = ONES[h] + ' Hundred' + (r ? ' ' : ''); }
        if (r) { out += twoDigits(r); }
        return out;
    }
    function amountInWords(n) {
        if (n === 0) { return 'Zero Rupees Only'; }
        if (n > 999999999) { return n.toLocaleString('en-PK') + ' Rupees Only'; }
        var parts = [];
        var crore = Math.floor(n / 10000000); n %= 10000000;
        var lakh = Math.floor(n / 100000); n %= 100000;
        var thou = Math.floor(n / 1000); n %= 1000;
        if (crore) { parts.push(threeDigits(crore) + ' Crore'); }
        if (lakh) { parts.push(twoDigits(lakh) + ' Lakh'); }
        if (thou) { parts.push(twoDigits(thou) + ' Thousand'); }
        if (n) { parts.push(threeDigits(n)); }
        return parts.join(' ') + ' Rupees Only';
    }

    var R = {};
    goBtn.addEventListener('click', function () {
        hideError();
        var bn = bizName.value.trim(), rf = recvFrom.value.trim();
        var amt = parseInt(amount.value, 10);
        var pur = purpose.value.trim();
        if (!bn) { showError('Enter the business name.'); return; }
        if (!rf) { showError('Enter the customer name.'); return; }
        if (!amt || amt <= 0) { showError('Enter a valid amount.'); return; }
        if (!pur) { showError('Enter the payment purpose.'); return; }
        R = {
            biz: bn, no: receiptNo.value.trim() || 'R-0001', date: recvDate.value || new Date().toISOString().slice(0, 10),
            from: rf, amt: amt, words: amountInWords(amt), method: payMethod.value, purpose: pur, notes: notes.value.trim()
        };
        document.getElementById('rBiz').textContent = R.biz;
        document.getElementById('rNo').textContent = R.no;
        document.getElementById('rDate').textContent = R.date;
        document.getElementById('rFrom').textContent = R.from;
        document.getElementById('rAmt').textContent = R.amt.toLocaleString('en-PK');
        document.getElementById('rWords').textContent = R.words;
        document.getElementById('rMethod').textContent = R.method;
        document.getElementById('rPurpose').textContent = R.purpose;
        var nr = document.getElementById('rNotesRow');
        if (R.notes) { nr.classList.remove('d-none'); document.getElementById('rNotes').textContent = R.notes; }
        else { nr.classList.add('d-none'); }
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('printBtn').addEventListener('click', function () { window.print(); });

    document.getElementById('dlBtn').addEventListener('click', function () {
        var W = 1000, H = 1180;
        var c = document.createElement('canvas');
        c.width = W; c.height = H;
        var x = c.getContext('2d');
        x.fillStyle = '#ffffff'; x.fillRect(0, 0, W, H);
        x.strokeStyle = '#111111'; x.lineWidth = 4; x.strokeRect(20, 20, W - 40, H - 40);
        x.fillStyle = '#111111'; x.textAlign = 'center';
        x.font = 'bold 44px Arial';
        x.fillText(R.biz, W / 2, 110);
        x.font = '24px Arial'; x.fillStyle = '#555555';
        x.fillText('P A Y M E N T   R E C E I P T', W / 2, 152);
        x.fillStyle = '#111111'; x.textAlign = 'left';
        x.font = '28px Arial';
        x.fillText('Receipt No: ' + R.no, 60, 215);
        x.textAlign = 'right';
        x.fillText('Date: ' + R.date, W - 60, 215);
        x.strokeStyle = '#999999'; x.lineWidth = 2;
        x.beginPath(); x.moveTo(60, 245); x.lineTo(W - 60, 245); x.stroke();
        x.textAlign = 'left';
        var rows = [
            ['Received from:', R.from],
            ['Amount:', 'Rs ' + R.amt.toLocaleString('en-PK')],
            ['In words:', R.words],
            ['Payment method:', R.method],
            ['Purpose:', R.purpose]
        ];
        if (R.notes) { rows.push(['Notes:', R.notes]); }
        var y = 310;
        rows.forEach(function (row) {
            x.font = 'bold 28px Arial';
            x.fillText(row[0], 60, y);
            x.font = '28px Arial';
            var labelW = x.measureText(row[0]).width;
            var maxW = W - 60 - (60 + labelW + 15) - 20;
            var val = row[1], words = val.split(' '), line = '', yy = y;
            words.forEach(function (w) {
                var test = line ? line + ' ' + w : w;
                if (x.measureText(test).width > maxW && line) { x.fillText(line, 60 + labelW + 15, yy); yy += 40; line = w; }
                else { line = test; }
            });
            x.fillText(line, 60 + labelW + 15, yy);
            y = yy + 70;
        });
        x.strokeStyle = '#999999';
        x.beginPath(); x.moveTo(60, y + 10); x.lineTo(W - 60, y + 10); x.stroke();
        var sy = H - 130;
        x.textAlign = 'center'; x.font = '26px Arial';
        x.beginPath(); x.moveTo(90, sy - 40); x.lineTo(330, sy - 40); x.stroke();
        x.beginPath(); x.moveTo(W - 330, sy - 40); x.lineTo(W - 90, sy - 40); x.stroke();
        x.fillText('Receiver signature', 210, sy);
        x.fillText('Customer signature', W - 210, sy);
        var a = document.createElement('a');
        a.href = c.toDataURL('image/png');
        a.download = 'receipt-' + R.no.replace(/[^a-z0-9]+/gi, '-') + '.png';
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
})();
</script>
@endsection
