@extends('layouts.app')

@section('title', 'Gold Price Calculator Pakistan - Tola, Gram, Masha, Ratti Value | Azlaan Tools')
@section('meta_description', 'Calculate gold value for any weight in tola, gram, masha and ratti for 24K, 22K, 21K and 18K with making charges and wastage deduction. Enter your own sarafa rate. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Gold Price Calculator</h1>
            <p class="lead text-muted">Find the price of your gold by weight — in tola, gram, masha or ratti, with making charges.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="rate" class="form-label fw-semibold">Today's 24K Rate per Tola (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="rate" value="385000" min="1" step="any">
                            <div class="form-text text-danger fw-semibold">Enter your own market rate for today — this rate is not live.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="weight" class="form-label fw-semibold">Weight</label>
                            <input type="number" class="form-control form-control-lg" id="weight" value="1" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label fw-semibold">Unit</label>
                            <select class="form-select form-select-lg" id="unit">
                                <option value="11.6638" selected>Tola</option>
                                <option value="1">Gram</option>
                                <option value="0.971983">Masha (1 tola = 12 masha)</option>
                                <option value="0.121249">Ratti (1 tola = 96 ratti)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="karat" class="form-label fw-semibold">Purity (Karat)</label>
                            <select class="form-select form-select-lg" id="karat">
                                <option value="24" selected>24K (100%)</option>
                                <option value="22">22K (91.7%)</option>
                                <option value="21">21K (87.5%)</option>
                                <option value="18">18K (75%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="making" class="form-label fw-semibold">Making Charges (%) — on buying</label>
                            <input type="number" class="form-control form-control-lg" id="making" value="0" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="wastage" class="form-label fw-semibold">Wastage Deduction (%) — on selling</label>
                            <input type="number" class="form-control form-control-lg" id="wastage" value="0" min="0" max="50" step="any">
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Gold Value</div><div class="fs-5 fw-bold" id="outGross">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Buy Price (+ making)</div><div class="fs-5 fw-bold" id="outBuy">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Sell Value (− wastage)</div><div class="fs-5 fw-bold" id="outSell">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0" id="outConv"></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>First, enter today's <strong>24K per-tola market rate</strong> (the default is only an example, not a live rate).</li>
                        <li>Select your weight and unit (tola / gram / masha / ratti), then choose the karat.</li>
                        <li>Enter making charges % for buying and wastage % for selling — all three prices will appear at once.</li>
                    </ol>
                    <p class="small text-muted mb-0">Constants: 1 tola = 11.6638 gram = 12 masha = 96 ratti. Karat value = Rate × (Karat ÷ 24). The real market rate changes daily — always confirm today's rate before entering it.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function val(id) { return parseFloat(document.getElementById(id).value) || 0; }
    function money(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
    function calc() {
        var ratePerGram24 = val('rate') / 11.6638;
        var grams = val('weight') * val('unit');
        var gross = grams * ratePerGram24 * (val('karat') / 24);
        document.getElementById('outGross').textContent = money(gross);
        document.getElementById('outBuy').textContent = money(gross * (1 + val('making') / 100));
        document.getElementById('outSell').textContent = money(gross * (1 - val('wastage') / 100));
        document.getElementById('outConv').textContent = 'Your weight: ' + grams.toFixed(4) + ' gram = ' + (grams / 11.6638).toFixed(4) + ' tola = ' + (grams / 0.971983).toFixed(2) + ' masha = ' + (grams / 0.121249).toFixed(2) + ' ratti.';
    }
    ['rate', 'weight', 'unit', 'karat', 'making', 'wastage'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
