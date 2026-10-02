@extends('layouts.app')

@section('title', 'Cash Counter - PKR Notes & Coins Total with Amount in Words | Azlaan Tools')
@section('meta_description', 'Count Pakistani rupee notes and coins: Rs 5000 to Rs 1 denominations with total amount, total pieces and amount in words (lakh / crore). Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Cash Counter — PKR</h1>
            <p class="lead text-muted">Enter the count of every note and coin — total amount, total pieces, and the amount in words, instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Denomination</th><th>Count</th><th class="text-end">Subtotal (Rs)</th></tr></thead>
                            <tbody id="rows"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="resetBtn">Reset All</button>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-6"><div class="border rounded p-3 bg-success text-white"><div class="small">Total Amount</div><div class="fs-4 fw-bold" id="outTotal">Rs 0</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light"><div class="small text-muted">Total Pieces</div><div class="fs-4 fw-bold" id="outPieces">0</div></div></div>
                    </div>
                    <p class="mt-3 mb-0"><strong>In words:</strong> <span id="outWords">Zero Rupees Only</span></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the count of each denomination — notes and coins.</li>
                        <li>Each row subtotal, the grand total and the total pieces update instantly.</li>
                        <li>The total amount appears below in English words (lakh / crore system) — useful for cheques or receipts.</li>
                    </ol>
                    <p class="small text-muted mb-0">To count again, press <strong>Reset All</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var denoms = [5000, 1000, 500, 100, 50, 20, 10, 5, 2, 1];
    var labels = ['Rs 5,000 Note', 'Rs 1,000 Note', 'Rs 500 Note', 'Rs 100 Note', 'Rs 50 Note', 'Rs 20 Note', 'Rs 10 Note / Coin', 'Rs 5 Coin', 'Rs 2 Coin', 'Rs 1 Coin'];
    var tbody = document.getElementById('rows');
    denoms.forEach(function (d, idx) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td class="fw-semibold">' + labels[idx] + '</td>' +
            '<td><input type="number" class="form-control form-control-sm cnt" data-v="' + d + '" min="0" step="1" value="0" style="max-width:140px"></td>' +
            '<td class="text-end fw-semibold sub">Rs 0</td>';
        tbody.appendChild(tr);
    });
    var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    function twoDigits(n) {
        if (n < 20) return ones[n];
        return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
    }
    function threeDigits(n) {
        var h = Math.floor(n / 100), r = n % 100;
        return (h ? ones[h] + ' Hundred' + (r ? ' ' : '') : '') + (r ? twoDigits(r) : '');
    }
    function toWords(n) {
        if (n === 0) return 'Zero';
        var parts = [];
        var crore = Math.floor(n / 10000000); n %= 10000000;
        var lakh = Math.floor(n / 100000); n %= 100000;
        var thousand = Math.floor(n / 1000); n %= 1000;
        if (crore) parts.push(toWords(crore) + ' Crore');
        if (lakh) parts.push(twoDigits(lakh) + ' Lakh');
        if (thousand) parts.push(twoDigits(thousand) + ' Thousand');
        if (n) parts.push(threeDigits(n));
        return parts.join(' ');
    }
    function calc() {
        var total = 0, pieces = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            var inp = tr.querySelector('.cnt');
            var c = parseInt(inp.value, 10) || 0;
            var v = parseInt(inp.getAttribute('data-v'), 10);
            total += c * v; pieces += c;
            tr.querySelector('.sub').textContent = 'Rs ' + (c * v).toLocaleString('en-PK');
        });
        document.getElementById('outTotal').textContent = 'Rs ' + total.toLocaleString('en-PK');
        document.getElementById('outPieces').textContent = pieces.toLocaleString('en-PK');
        document.getElementById('outWords').textContent = toWords(total) + ' Rupees Only';
    }
    tbody.querySelectorAll('.cnt').forEach(function (i) { i.addEventListener('input', calc); });
    document.getElementById('resetBtn').addEventListener('click', function () {
        tbody.querySelectorAll('.cnt').forEach(function (i) { i.value = 0; });
        calc();
    });
    calc();
})();
</script>
@endsection
