@extends('layouts.app')

@section('title', 'Tip Calculator & Bill Split — Azlaan Tools')
@section('meta_description', 'Free tip calculator and bill splitter. Bill amount, tip percent and number of people to get tip amount, total bill and per-person share.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Tip Calculator &amp; Bill Split</h1>
            <p class="lead text-muted">How much tip to give on a restaurant bill, and how to split it among friends — calculate the tip, total and each person's share instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipBill" class="form-label fw-semibold">Bill Amount (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="tipBill" min="0" step="any" value="8000">
                        </div>
                        <div class="col-md-6">
                            <label for="tipPct" class="form-label fw-semibold">Tip (%)</label>
                            <input type="number" class="form-control form-control-lg" id="tipPct" min="0" step="any" value="10">
                            <div class="btn-group mt-2" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm tip-preset" data-pct="5">5%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm tip-preset" data-pct="10">10%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm tip-preset" data-pct="15">15%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm tip-preset" data-pct="20">20%</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="tipPeople" class="form-label fw-semibold">Number of People (bill split)</label>
                            <input type="number" class="form-control form-control-lg" id="tipPeople" min="1" step="1" value="4">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Tip Amount</div><div class="fs-5 fw-bold" id="tipAmount">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Bill (with tip)</div><div class="fs-5 fw-bold" id="tipTotal">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Per Person Total</div><div class="fs-4 fw-bold text-success" id="tipPerPerson">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Tip Per Person</div><div class="fs-5 fw-bold" id="tipPerTip">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the bill amount in Rs.</li>
                        <li>Choose the tip % — the 5%, 10%, 15%, 20% chips, or write your own custom %.</li>
                        <li>Write how many people are sharing the bill.</li>
                        <li>The tip amount, total bill and each person's share (with tip) will show at once.</li>
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
    function fmt(n) { return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function calc() {
        var bill = parseFloat(document.getElementById('tipBill').value) || 0;
        var pct = parseFloat(document.getElementById('tipPct').value) || 0;
        var people = parseInt(document.getElementById('tipPeople').value, 10) || 1;
        if (people < 1) people = 1;
        if (bill <= 0) {
            document.getElementById('tipAmount').textContent = '—';
            document.getElementById('tipTotal').textContent = '—';
            document.getElementById('tipPerPerson').textContent = '—';
            document.getElementById('tipPerTip').textContent = '—';
            return;
        }
        var tip = bill * pct / 100;
        var total = bill + tip;
        document.getElementById('tipAmount').textContent = fmt(tip);
        document.getElementById('tipTotal').textContent = fmt(total);
        document.getElementById('tipPerPerson').textContent = fmt(total / people);
        document.getElementById('tipPerTip').textContent = fmt(tip / people);
    }
    ['tipBill', 'tipPct', 'tipPeople'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    document.querySelectorAll('.tip-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('tipPct').value = btn.getAttribute('data-pct');
            calc();
        });
    });
    calc();
})();
</script>
@endsection
