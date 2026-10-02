@extends('layouts.app')

@section('title', 'Solar ROI & Payback Calculator Pakistan - Net Metering Savings | Azlaan Tools')
@section('meta_description', 'Calculate solar system savings, payback period, annual ROI and 25-year savings in Pakistan with net metering / net billing rates. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Solar ROI &amp; Payback Calculator</h1>
            <p class="lead text-muted">How long until your solar system pays for itself? Enter your cost and rates — get the answer instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cost" class="form-label fw-semibold">Total System Cost (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="cost" value="650000" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="kw" class="form-label fw-semibold">System Size (kW)</label>
                            <input type="number" class="form-control form-control-lg" id="kw" value="5" min="0.1" step="any">
                            <div class="form-text">Monthly units auto-update when you change the size (kW × 120).</div>
                        </div>
                        <div class="col-md-6">
                            <label for="units" class="form-label fw-semibold">Monthly Units Produced (kWh)</label>
                            <input type="number" class="form-control form-control-lg" id="units" value="600" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="selfUse" class="form-label fw-semibold">Self-Use (%) — rest is exported</label>
                            <input type="number" class="form-control form-control-lg" id="selfUse" value="70" min="0" max="100" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="importRate" class="form-label fw-semibold">Import Unit Rate (Rs) — approx 2026</label>
                            <input type="number" class="form-control form-control-lg" id="importRate" value="55" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="exportRate" class="form-label fw-semibold">Export / Net-Billing Rate (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="exportRate" value="10" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="esc" class="form-label fw-semibold">Tariff Escalation per Year (%) — optional</label>
                            <input type="number" class="form-control form-control-lg" id="esc" value="0" min="0" max="30" step="any">
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-6"><div class="border rounded p-3 bg-light"><div class="small text-muted">Monthly Savings</div><div class="fs-4 fw-bold" id="outMonthly">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light"><div class="small text-muted">Simple Payback</div><div class="fs-4 fw-bold" id="outPayback">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light"><div class="small text-muted">Annual ROI</div><div class="fs-4 fw-bold" id="outRoi">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light"><div class="small text-muted">25-Year Net Savings</div><div class="fs-4 fw-bold" id="out25">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Note: Rates are approximate and actual savings may vary with slabs/taxes. Edit them to match your bill's rates.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the total system cost and size (kW) — monthly units auto-set to kW × 120; you can change it yourself if you want.</li>
                        <li>Set how much electricity is used at home (self-use %) and how much is exported.</li>
                        <li>See monthly savings, payback period, ROI and 25-year net savings instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0">Want to install solar or need a free survey? Contact Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
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
    var unitsTouched = false;
    document.getElementById('units').addEventListener('input', function () { unitsTouched = true; });
    document.getElementById('kw').addEventListener('input', function () {
        if (!unitsTouched) { document.getElementById('units').value = Math.round(val('kw') * 120); }
        calc();
    });
    function calc() {
        var cost = val('cost'), units = val('units'), self = Math.min(100, Math.max(0, val('selfUse'))) / 100;
        var imp = val('importRate'), exp = val('exportRate'), esc = val('esc') / 100;
        var monthly = units * self * imp + units * (1 - self) * exp;
        if (cost > 0 && monthly > 0) {
            document.getElementById('outMonthly').textContent = money(monthly);
            var months = cost / monthly;
            var y = Math.floor(months / 12), mo = Math.round(months % 12);
            if (mo === 12) { y += 1; mo = 0; }
            document.getElementById('outPayback').textContent = y + ' yrs ' + mo + ' mo';
            document.getElementById('outRoi').textContent = ((monthly * 12 / cost) * 100).toFixed(1) + '% / year';
            var total = 0;
            for (var i = 0; i < 25; i++) { total += monthly * 12 * Math.pow(1 + esc, i); }
            document.getElementById('out25').textContent = money(total - cost);
        } else {
            document.getElementById('outMonthly').textContent = '—';
            document.getElementById('outPayback').textContent = '—';
            document.getElementById('outRoi').textContent = '—';
            document.getElementById('out25').textContent = '—';
        }
    }
    ['cost', 'units', 'selfUse', 'importRate', 'exportRate', 'esc'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
