@extends('layouts.app')

@section('title', 'Percentage Calculator — Azlaan Tools')
@section('meta_description', 'Free online percentage calculator. Calculate what is X% of Y, X is what percent of Y, and percentage increase or decrease. Fast, free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">Percentage Calculator</h1>
            <p class="text-muted mb-4">Answers to three common percentage questions in one place — marks, discounts, salary increases, tax, or any calculation. The result updates live as you type the numbers.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">1. What is X% of Y?</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-4">
                            <label for="p1x" class="form-label">Percent (X%)</label>
                            <input type="number" class="form-control" id="p1x" placeholder="e.g. 15" step="any">
                        </div>
                        <div class="col-4">
                            <label for="p1y" class="form-label">Of (Y)</label>
                            <input type="number" class="form-control" id="p1y" placeholder="e.g. 2000" step="any">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Result</label>
                            <div class="form-control bg-light fw-bold" id="r1">—</div>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Formula: (X &divide; 100) &times; Y</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">2. X is what % of Y?</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-4">
                            <label for="p2x" class="form-label">Value (X)</label>
                            <input type="number" class="form-control" id="p2x" placeholder="e.g. 450" step="any">
                        </div>
                        <div class="col-4">
                            <label for="p2y" class="form-label">Total (Y)</label>
                            <input type="number" class="form-control" id="p2y" placeholder="e.g. 600" step="any">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Result</label>
                            <div class="form-control bg-light fw-bold" id="r2">—</div>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Formula: (X &divide; Y) &times; 100 — example: exam marks 450 out of 600 = 75%.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">3. % Increase / Decrease from X to Y</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-4">
                            <label for="p3x" class="form-label">From (X)</label>
                            <input type="number" class="form-control" id="p3x" placeholder="e.g. 1000" step="any">
                        </div>
                        <div class="col-4">
                            <label for="p3y" class="form-label">To (Y)</label>
                            <input type="number" class="form-control" id="p3y" placeholder="e.g. 1200" step="any">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Result</label>
                            <div class="form-control bg-light fw-bold" id="r3">—</div>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Formula: ((Y &minus; X) &divide; X) &times; 100. Difference: <span class="fw-semibold" id="r3diff">—</span></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Choose your question — there are three calculators above.</li>
                        <li>Enter the numbers in the relevant boxes (X and Y).</li>
                        <li>The result shows live right away — no need to press any button.</li>
                        <li>Change the numbers and the result updates by itself.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function fmt(n) {
    if (!isFinite(n)) return '—';
    return Number(n.toFixed(4)).toLocaleString('en-PK', { maximumFractionDigits: 4 });
}
function val(id) {
    var v = document.getElementById(id).value;
    return v === '' ? null : parseFloat(v);
}
function calc1() {
    var x = val('p1x'), y = val('p1y');
    document.getElementById('r1').textContent = (x === null || y === null) ? '—' : fmt((x / 100) * y);
}
function calc2() {
    var x = val('p2x'), y = val('p2y');
    document.getElementById('r2').textContent = (x === null || y === null || y === 0) ? '—' : fmt((x / y) * 100) + '%';
}
function calc3() {
    var x = val('p3x'), y = val('p3y');
    var out = document.getElementById('r3'), diff = document.getElementById('r3diff');
    if (x === null || y === null || x === 0) { out.textContent = '—'; diff.textContent = '—'; return; }
    var pct = ((y - x) / x) * 100;
    var label = pct > 0 ? 'increase' : (pct < 0 ? 'decrease' : 'no change');
    out.textContent = fmt(Math.abs(pct)) + '% ' + label;
    out.className = 'form-control bg-light fw-bold ' + (pct > 0 ? 'text-success' : (pct < 0 ? 'text-danger' : ''));
    diff.textContent = fmt(y - x);
}
['p1x','p1y'].forEach(function(id){ document.getElementById(id).addEventListener('input', calc1); });
['p2x','p2y'].forEach(function(id){ document.getElementById(id).addEventListener('input', calc2); });
['p3x','p3y'].forEach(function(id){ document.getElementById(id).addEventListener('input', calc3); });
</script>
@endsection
