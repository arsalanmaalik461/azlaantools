@extends('layouts.app')

@section('title', 'Simple Interest Calculator PKR — Azlaan Tools')
@section('meta_description', 'Free simple interest calculator for Pakistan. Principal, rate and time to get interest and total amount. Also find rate, time or principal mode.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Simple Interest Calculator</h1>
            <p class="lead text-muted">Calculate simple interest — find interest and total amount from principal, rate and time. Perfect for a committee, a loan from a friend, or basic saving.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="siMode" class="form-label fw-semibold">What do you want to find?</label>
                        <select class="form-select form-select-lg" id="siMode">
                            <option value="interest" selected>Interest &amp; Total Amount</option>
                            <option value="rate">Rate (%)</option>
                            <option value="time">Time</option>
                            <option value="principal">Principal</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6" id="siPrincipalWrap">
                            <label for="siPrincipal" class="form-label fw-semibold">Principal Amount (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="siPrincipal" min="0" step="any" value="200000">
                        </div>
                        <div class="col-md-6" id="siRateWrap">
                            <label for="siRate" class="form-label fw-semibold">Annual Rate (%)</label>
                            <input type="number" class="form-control form-control-lg" id="siRate" min="0" step="any" value="10">
                        </div>
                        <div class="col-md-6" id="siTimeWrap">
                            <label for="siTime" class="form-label fw-semibold">Time</label>
                            <div class="input-group input-group-lg">
                                <input type="number" class="form-control" id="siTime" min="0" step="any" value="3">
                                <select class="form-select" id="siTimeUnit" style="max-width: 130px;">
                                    <option value="years" selected>Years</option>
                                    <option value="months">Months</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="siInterestWrap">
                            <label for="siInterestIn" class="form-label fw-semibold">Interest Amount (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="siInterestIn" min="0" step="any" value="60000">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small" id="siOut1Label">Interest Earned</div><div class="fs-4 fw-bold text-success" id="siOut1">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small" id="siOut2Label">Total Amount</div><div class="fs-4 fw-bold" id="siOut2">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Formula</div><div class="fs-6 fw-bold">I = P × R × T / 100</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Choose the mode from above — find interest, rate, time or principal.</li>
                        <li>Enter the other values: principal in Rs, rate in %, time in years or months.</li>
                        <li>The result shows instantly — no button to press.</li>
                        <li>Simple interest is charged only on the original principal — it does not add interest on interest like compound interest.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
    function updateVisibility() {
        var mode = document.getElementById('siMode').value;
        document.getElementById('siPrincipalWrap').style.display = mode === 'principal' ? 'none' : '';
        document.getElementById('siRateWrap').style.display = mode === 'rate' ? 'none' : '';
        document.getElementById('siTimeWrap').style.display = mode === 'time' ? 'none' : '';
        document.getElementById('siInterestWrap').style.display = mode === 'interest' ? 'none' : '';
    }
    function calc() {
        updateVisibility();
        var mode = document.getElementById('siMode').value;
        var P = parseFloat(document.getElementById('siPrincipal').value) || 0;
        var R = parseFloat(document.getElementById('siRate').value) || 0;
        var tVal = parseFloat(document.getElementById('siTime').value) || 0;
        var unit = document.getElementById('siTimeUnit').value;
        var T = unit === 'months' ? tVal / 12 : tVal;
        var Iin = parseFloat(document.getElementById('siInterestIn').value) || 0;
        var l1 = document.getElementById('siOut1Label');
        var l2 = document.getElementById('siOut2Label');
        var o1 = document.getElementById('siOut1');
        var o2 = document.getElementById('siOut2');
        if (mode === 'interest') {
            if (P <= 0) { o1.textContent = '—'; o2.textContent = '—'; return; }
            var I = P * R * T / 100;
            l1.textContent = 'Interest Earned'; l2.textContent = 'Total Amount';
            o1.textContent = fmt(I); o2.textContent = fmt(P + I);
        } else if (mode === 'rate') {
            if (P <= 0 || T <= 0) { o1.textContent = '—'; o2.textContent = '—'; return; }
            var rate = Iin * 100 / (P * T);
            l1.textContent = 'Required Rate'; l2.textContent = 'Total Amount';
            o1.textContent = rate.toFixed(2) + '% per year'; o2.textContent = fmt(P + Iin);
        } else if (mode === 'time') {
            if (P <= 0 || R <= 0) { o1.textContent = '—'; o2.textContent = '—'; return; }
            var yrs = Iin * 100 / (P * R);
            l1.textContent = 'Required Time'; l2.textContent = 'In Months';
            o1.textContent = yrs.toFixed(2) + ' years'; o2.textContent = Math.round(yrs * 12) + ' months';
        } else {
            if (R <= 0 || T <= 0) { o1.textContent = '—'; o2.textContent = '—'; return; }
            var prin = Iin * 100 / (R * T);
            l1.textContent = 'Required Principal'; l2.textContent = 'Total Amount';
            o1.textContent = fmt(prin); o2.textContent = fmt(prin + Iin);
        }
    }
    ['siMode', 'siPrincipal', 'siRate', 'siTime', 'siTimeUnit', 'siInterestIn'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
