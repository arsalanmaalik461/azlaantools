@extends('layouts.app')

@section('title', 'Battery Sizing Calculator - Required Ah for UPS & Solar | Azlaan Tools')
@section('meta_description', 'Find how many Ah of battery you need for your load and backup hours, and how many 100Ah / 200Ah batteries to buy. For UPS and solar in Pakistan. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Battery Sizing Calculator</h1>
            <p class="lead text-muted">Find out how big a battery you need for your load and backup hours. Calculate it instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="load" class="form-label fw-semibold">Load (W)</label>
                            <input type="number" class="form-control form-control-lg" id="load" value="500" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="hours" class="form-label fw-semibold">Desired Backup (hours)</label>
                            <input type="number" class="form-control form-control-lg" id="hours" value="4" min="0.1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="volt" class="form-label fw-semibold">System Voltage (V)</label>
                            <select class="form-select form-select-lg" id="volt">
                                <option value="12" selected>12 V</option>
                                <option value="24">24 V</option>
                                <option value="48">48 V</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="btype" class="form-label fw-semibold">Battery Type</label>
                            <select class="form-select form-select-lg" id="btype">
                                <option value="50" selected>Lead-Acid (DoD 50%)</option>
                                <option value="60">Tubular (DoD 60%)</option>
                                <option value="85">Lithium (DoD 85%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="eff" class="form-label fw-semibold">Inverter Efficiency (%)</label>
                            <input type="number" class="form-control form-control-lg" id="eff" value="85" min="10" max="100" step="any">
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Required Capacity</div><div class="fs-4 fw-bold" id="outAh">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">200Ah Batteries</div><div class="fs-4 fw-bold" id="out200">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">100Ah Batteries</div><div class="fs-4 fw-bold" id="out100">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Note: connecting batteries in series increases voltage, connecting in parallel increases Ah. Match the combination to your inverter's voltage.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter your total load (W) and how many hours of backup you need.</li>
                        <li>Select the system voltage and battery type — DoD (depth of discharge) is applied automatically.</li>
                        <li>The required Ah and the number of 200Ah / 100Ah batteries appear instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0">Formula: Required Ah = (Load × Hours) ÷ (Voltage × DoD × Efficiency). For battery sizing or solar installation: Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
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
    function fmt(n) { return n.toLocaleString('en-PK', { maximumFractionDigits: 0 }); }
    function calc() {
        var load = val('load'), hours = val('hours'), v = val('volt'), dod = val('btype') / 100, eff = val('eff') / 100;
        if (load > 0 && hours > 0 && v > 0 && dod > 0 && eff > 0) {
            var ah = (load * hours) / (v * dod * eff);
            document.getElementById('outAh').textContent = fmt(Math.ceil(ah)) + ' Ah';
            document.getElementById('out200').textContent = Math.ceil(ah / 200) + ' × 200Ah';
            document.getElementById('out100').textContent = Math.ceil(ah / 100) + ' × 100Ah';
        } else {
            document.getElementById('outAh').textContent = '—';
            document.getElementById('out200').textContent = '—';
            document.getElementById('out100').textContent = '—';
        }
    }
    ['load', 'hours', 'volt', 'btype', 'eff'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
