@extends('layouts.app')

@section('title', 'Discount Calculator - Sale Price and Savings | Azlaan Tools')
@section('meta_description', 'Free discount calculator: find final price and savings from original price and discount percent, or reverse-calculate discount percent from prices. Optional sales tax. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Discount Calculator</h1>
            <p class="lead text-muted">Find the real price in a sale — final price after discount, savings, or reverse calculation: find the discount percentage from two prices.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Mode 1: Price + Discount %</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="origPrice1" class="form-label fw-semibold">Original Price (PKR)</label>
                            <input type="number" class="form-control" id="origPrice1" min="0" step="any" placeholder="e.g. 5000">
                        </div>
                        <div class="col-md-6">
                            <label for="discountPct" class="form-label fw-semibold">Discount (%)</label>
                            <input type="number" class="form-control" id="discountPct" min="0" max="100" step="any" placeholder="e.g. 20">
                        </div>
                        <div class="col-md-6">
                            <label for="salesTax" class="form-label">Add Sales Tax After Discount (%) — optional</label>
                            <input type="number" class="form-control" id="salesTax" min="0" step="any" placeholder="e.g. 18 — empty = no tax">
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="msg1"></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Final Price (after discount)</div><div class="fs-5 fw-bold text-success" id="finalPriceOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">You Save</div><div class="fs-5 fw-bold" id="saveOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Final Price incl. Sales Tax</div><div class="fs-5 fw-bold" id="taxPriceOut">—</div><div class="small text-muted" id="taxDetail"></div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Mode 2 (Reverse): Original + Final Price → Discount %</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="origPrice2" class="form-label fw-semibold">Original Price (PKR)</label>
                            <input type="number" class="form-control" id="origPrice2" min="0" step="any" placeholder="e.g. 5000">
                        </div>
                        <div class="col-md-6">
                            <label for="finalPrice2" class="form-label fw-semibold">Final / Sale Price (PKR)</label>
                            <input type="number" class="form-control" id="finalPrice2" min="0" step="any" placeholder="e.g. 4000">
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="msg2"></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Discount Percentage</div><div class="fs-4 fw-bold text-primary" id="revPctOut">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">You Save (Amount)</div><div class="fs-4 fw-bold" id="revSaveOut">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>In Mode 1 enter the original price and discount % — the final price and savings will show right away.</li>
                        <li>If sales tax applies after the discount, also enter the optional tax %.</li>
                        <li>In Mode 2 (reverse) enter the original and final price to get the discount % — great for checking a shop sale.</li>
                        <li>Both modes calculate live, no button needed.</li>
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
    function calc1() {
        var msg = document.getElementById('msg1');
        var orig = parseFloat(document.getElementById('origPrice1').value);
        var disc = parseFloat(document.getElementById('discountPct').value);
        var tax = parseFloat(document.getElementById('salesTax').value);
        if (isNaN(orig) || isNaN(disc)) {
            document.getElementById('finalPriceOut').textContent = '—'; document.getElementById('saveOut').textContent = '—'; document.getElementById('taxPriceOut').textContent = '—'; document.getElementById('taxDetail').textContent = ''; msg.classList.add('d-none'); return;
        }
        if (orig < 0 || disc < 0 || disc > 100) { msg.textContent = 'Discount must be between 0% and 100% and the price cannot be negative.'; msg.classList.remove('d-none'); return; }
        msg.classList.add('d-none');
        var save = orig * disc / 100;
        var finalP = orig - save;
        document.getElementById('finalPriceOut').textContent = fmt(finalP);
        document.getElementById('saveOut').textContent = fmt(save);
        if (!isNaN(tax) && tax >= 0) {
            var taxAmt = finalP * tax / 100;
            document.getElementById('taxPriceOut').textContent = fmt(finalP + taxAmt);
            document.getElementById('taxDetail').textContent = 'Tax amount: ' + fmt(taxAmt) + ' at ' + tax + '%';
        } else { document.getElementById('taxPriceOut').textContent = fmt(finalP) + ' (no tax)'; document.getElementById('taxDetail').textContent = 'No sales tax entered.'; }
    }
    function calc2() {
        var msg = document.getElementById('msg2');
        var orig = parseFloat(document.getElementById('origPrice2').value);
        var fin = parseFloat(document.getElementById('finalPrice2').value);
        if (isNaN(orig) || isNaN(fin)) { document.getElementById('revPctOut').textContent = '—'; document.getElementById('revSaveOut').textContent = '—'; msg.classList.add('d-none'); return; }
        if (orig <= 0) { msg.textContent = 'Original price must be more than 0.'; msg.classList.remove('d-none'); return; }
        if (fin < 0 || fin > orig) { msg.textContent = 'Final price is more than the original price — that is not a discount, it is an increase. Check your values.'; msg.classList.remove('d-none'); document.getElementById('revPctOut').textContent = '—'; document.getElementById('revSaveOut').textContent = fmt(orig - fin); return; }
        msg.classList.add('d-none');
        var save = orig - fin;
        var pct = (save / orig) * 100;
        document.getElementById('revPctOut').textContent = pct.toFixed(2) + '% OFF';
        document.getElementById('revSaveOut').textContent = fmt(save);
    }
    ['origPrice1', 'discountPct', 'salesTax'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc1); });
    ['origPrice2', 'finalPrice2'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc2); });
    calc1(); calc2();
})();
</script>
@endsection
