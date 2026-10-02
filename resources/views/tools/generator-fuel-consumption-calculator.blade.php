@extends('layouts.app')

@section('title', 'Generator Fuel Consumption Calculator - Diesel / Petrol / Gas Cost | Azlaan Tools')
@section('meta_description', 'Calculate generator fuel consumption per hour, per day and monthly cost in Rs for diesel, petrol and gas generators in Pakistan. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Generator Fuel Consumption Calculator</h1>
            <p class="lead text-muted">How much fuel will your generator use per hour, and what will it cost per month? Find out instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kva" class="form-label fw-semibold">Generator Size (kVA)</label>
                            <input type="number" class="form-control form-control-lg" id="kva" value="10" min="0.1" step="any">
                            <div class="form-text">At power factor 0.8, kW = kVA × 0.8.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Load (%)</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary loadchip" data-p="25">25%</button>
                                <button type="button" class="btn btn-primary loadchip" data-p="50">50%</button>
                                <button type="button" class="btn btn-outline-primary loadchip" data-p="75">75%</button>
                                <button type="button" class="btn btn-outline-primary loadchip" data-p="100">100%</button>
                            </div>
                            <input type="hidden" id="loadpct" value="50">
                        </div>
                        <div class="col-md-6">
                            <label for="fuel" class="form-label fw-semibold">Fuel Type</label>
                            <select class="form-select form-select-lg" id="fuel">
                                <option value="0.25" selected>Diesel (≈0.25 L/kWh)</option>
                                <option value="0.35">Petrol (≈0.35 L/kWh)</option>
                                <option value="0.30">Gas (≈0.30 m³/kWh)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="factor" class="form-label fw-semibold">Consumption Factor (per kWh)</label>
                            <input type="number" class="form-control form-control-lg" id="factor" value="0.25" min="0.01" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="hours" class="form-label fw-semibold">Hours / Day</label>
                            <input type="number" class="form-control form-control-lg" id="hours" value="6" min="0" max="24" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label fw-semibold">Fuel Price (Rs / L or m³)</label>
                            <input type="number" class="form-control form-control-lg" id="price" value="280" min="0" step="any">
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">Fuel / Hour</div><div class="fs-5 fw-bold" id="outHour">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">Fuel / Day</div><div class="fs-5 fw-bold" id="outDay">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">Cost / Day</div><div class="fs-5 fw-bold" id="outCostDay">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">Cost / Month</div><div class="fs-5 fw-bold" id="outCostMonth">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the generator size (kVA) and select a load % chip.</li>
                        <li>Select the fuel type — the consumption factor is set automatically; change it yourself if you want.</li>
                        <li>Enter daily hours and fuel price — hourly, daily and monthly cost appears instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0">Formula: Fuel/hour = kVA × 0.8 × Load% × Consumption factor. For generator service or electrical work: Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
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
    document.getElementById('fuel').addEventListener('change', function () {
        document.getElementById('factor').value = this.value; calc();
    });
    document.querySelectorAll('.loadchip').forEach(function (b) {
        b.addEventListener('click', function () {
            document.getElementById('loadpct').value = b.getAttribute('data-p');
            document.querySelectorAll('.loadchip').forEach(function (x) { x.className = 'btn btn-outline-primary loadchip'; });
            b.className = 'btn btn-primary loadchip';
            calc();
        });
    });
    function calc() {
        var kw = val('kva') * 0.8 * (val('loadpct') / 100);
        var perHour = kw * val('factor');
        var perDay = perHour * val('hours');
        document.getElementById('outHour').textContent = perHour.toFixed(2);
        document.getElementById('outDay').textContent = perDay.toFixed(2);
        document.getElementById('outCostDay').textContent = money(perDay * val('price'));
        document.getElementById('outCostMonth').textContent = money(perDay * val('price') * 30);
    }
    ['kva', 'factor', 'hours', 'price'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
