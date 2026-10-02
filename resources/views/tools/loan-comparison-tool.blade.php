@extends('layouts.app')

@section('title', 'Loan Offer Comparison - Azlaan Tools')
@section('meta_description', 'Side-by-side comparison of 2 or 3 loan offers — EMI, total interest and total payable; the cheapest offer is highlighted.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Loan Offer Comparison</h1>
            <p class="lead text-muted">Compare 2-3 bank loan offers side by side — the offer with the lowest total payable is highlighted automatically.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="border rounded p-3">
                                <h2 class="h6">Offer 1</h2>
                                <label for="o1Amount" class="form-label small">Loan amount (Rs)</label>
                                <input type="number" class="form-control mb-2" id="o1Amount" placeholder="500000" min="1" step="0.01">
                                <label for="o1Rate" class="form-label small">Annual rate (%)</label>
                                <input type="number" class="form-control mb-2" id="o1Rate" placeholder="18" min="0" max="100" step="0.01">
                                <label for="o1Months" class="form-label small">Term (months)</label>
                                <input type="number" class="form-control" id="o1Months" placeholder="36" min="1" max="600" step="1">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="border rounded p-3">
                                <h2 class="h6">Offer 2</h2>
                                <label for="o2Amount" class="form-label small">Loan amount (Rs)</label>
                                <input type="number" class="form-control mb-2" id="o2Amount" placeholder="500000" min="1" step="0.01">
                                <label for="o2Rate" class="form-label small">Annual rate (%)</label>
                                <input type="number" class="form-control mb-2" id="o2Rate" placeholder="20" min="0" max="100" step="0.01">
                                <label for="o2Months" class="form-label small">Term (months)</label>
                                <input type="number" class="form-control" id="o2Months" placeholder="24" min="1" max="600" step="1">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="border rounded p-3">
                                <h2 class="h6">Offer 3 <span class="text-muted small">(optional)</span></h2>
                                <label for="o3Amount" class="form-label small">Loan amount (Rs)</label>
                                <input type="number" class="form-control mb-2" id="o3Amount" placeholder="500000" min="1" step="0.01">
                                <label for="o3Rate" class="form-label small">Annual rate (%)</label>
                                <input type="number" class="form-control mb-2" id="o3Rate" placeholder="16" min="0" max="100" step="0.01">
                                <label for="o3Months" class="form-label small">Term (months)</label>
                                <input type="number" class="form-control" id="o3Months" placeholder="48" min="1" max="600" step="1">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="cmpBtn">Compare</button>
                        <button type="button" class="btn btn-outline-secondary" id="cmpResetBtn">Reset</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="cmpError" role="alert"></div>

                    <div id="cmpResults" class="mt-4 d-none">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start">Feature</th>
                                        <th id="hOffer1">Offer 1</th>
                                        <th id="hOffer2">Offer 2</th>
                                        <th id="hOffer3">Offer 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><th class="text-start">Loan amount</th><td id="c1Amount">-</td><td id="c2Amount">-</td><td id="c3Amount">-</td></tr>
                                    <tr><th class="text-start">Annual rate</th><td id="c1Rate">-</td><td id="c2Rate">-</td><td id="c3Rate">-</td></tr>
                                    <tr><th class="text-start">Term</th><td id="c1Months">-</td><td id="c2Months">-</td><td id="c3Months">-</td></tr>
                                    <tr><th class="text-start">Monthly Installment (EMI)</th><td id="c1Emi">-</td><td id="c2Emi">-</td><td id="c3Emi">-</td></tr>
                                    <tr><th class="text-start">Total Interest</th><td id="c1Int">-</td><td id="c2Int">-</td><td id="c3Int">-</td></tr>
                                    <tr><th class="text-start">Total Payable</th><td id="c1Total">-</td><td id="c2Total">-</td><td id="c3Total">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-success" id="winnerBox" role="status"></div>
                        <p class="text-muted small mb-0">The cheapest offer is highlighted based on total payable. Processing fees, insurance or later conditions are not included in this calculation — read the offer letter yourself before deciding. This is an estimate, not financial advice.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    function $(id) { return document.getElementById(id); }

    function money(n) {
        return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2, minimumFractionDigits: 2 });
    }

    function emiFor(p, rate, n) {
        var r = rate / 1200;
        if (r === 0) return p / n;
        var f = Math.pow(1 + r, n);
        return p * r * f / (f - 1);
    }

    function showError(msg) {
        var box = $('cmpError');
        if (msg) { box.textContent = msg; box.classList.remove('d-none'); }
        else { box.classList.add('d-none'); }
    }

    function readOffer(idx) {
        var p = parseFloat($('o' + idx + 'Amount').value);
        var rate = parseFloat($('o' + idx + 'Rate').value);
        var n = parseInt($('o' + idx + 'Months').value, 10);
        if (!isFinite(p) || p <= 0) return null;
        if (!isFinite(rate) || rate < 0 || rate > 100) return null;
        if (!isFinite(n) || n < 1 || n > 600) return null;
        var emi = emiFor(p, rate, n);
        var total = emi * n;
        return { p: p, rate: rate, n: n, emi: emi, total: total, interest: total - p };
    }

    function fillColumn(idx, offer) {
        $('c' + idx + 'Amount').textContent = offer ? money(offer.p) : '-';
        $('c' + idx + 'Rate').textContent = offer ? offer.rate + '%' : '-';
        $('c' + idx + 'Months').textContent = offer ? offer.n + ' months' : '-';
        $('c' + idx + 'Emi').textContent = offer ? money(offer.emi) : '-';
        $('c' + idx + 'Int').textContent = offer ? money(offer.interest) : '-';
        $('c' + idx + 'Total').textContent = offer ? money(offer.total) : '-';
    }

    function compare() {
        showError(null);
        var offers = [readOffer(1), readOffer(2), readOffer(3)];
        var valid = offers.filter(function (o) { return o !== null; });
        if (valid.length < 2) { showError('Enter all fields (amount, rate, term) correctly for at least 2 offers.'); return; }

        offers.forEach(function (offer, i) { fillColumn(i + 1, offer); });

        // cheapest = lowest total payable; remove old highlights
        var best = valid[0], bestIdx = offers.indexOf(valid[0]);
        offers.forEach(function (offer) {
            if (offer && offer.total < best.total) { best = offer; bestIdx = offers.indexOf(offer); }
        });

        for (var i = 1; i <= 3; i++) {
            $('hOffer' + i).classList.remove('bg-success', 'text-white');
            $('hOffer' + i).innerHTML = 'Offer ' + i;
        }
        var h = $('hOffer' + (bestIdx + 1));
        h.classList.add('bg-success', 'text-white');
        h.innerHTML = 'Offer ' + (bestIdx + 1) + ' <span class="badge bg-light text-success">Cheapest</span>';

        $('winnerBox').innerHTML = '<strong>Offer ' + (bestIdx + 1) + '</strong> is the cheapest — total payable ' +
            money(best.total) + ' (' + money(best.emi) + ' × ' + best.n + ' months), total interest ' + money(best.interest) + '.';

        $('cmpResults').classList.remove('d-none');
    }

    $('cmpBtn').addEventListener('click', compare);

    $('cmpResetBtn').addEventListener('click', function () {
        for (var i = 1; i <= 3; i++) {
            $('o' + i + 'Amount').value = '';
            $('o' + i + 'Rate').value = '';
            $('o' + i + 'Months').value = '';
        }
        $('cmpResults').classList.add('d-none');
        showError(null);
    });
})();
</script>
@endsection
