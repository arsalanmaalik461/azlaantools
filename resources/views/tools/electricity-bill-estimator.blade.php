@extends('layouts.app')

@section('title', 'Electricity Bill Estimator Pakistan - Calculate Electricity Bill by Units | Azlaan Tools')
@section('meta_description', 'Estimate your Pakistan electricity bill from units consumed. Free electricity bill calculator with slab rates, fuel price adjustment (FPA), GST and fixed charges - single and three phase residential estimate.')

@section('content')
<div class="tool-wrap">
    <h1 class="mb-3">Electricity Bill Estimator - Pakistan</h1>
    <p class="lead text-muted">Enter the units on your meter and get an instant estimate of your monthly electricity bill, with a full slab-by-slab breakdown.</p>

    <div class="alert alert-warning" role="alert">
        <strong>This is an estimate</strong> — your actual bill may differ according to your company tariff. This is an estimate only: your actual bill depends on your company tariff, protected / non-protected status, peak hours, arrears and the exact taxes and adjustments applied that month.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="units">Units Consumed (kWh)</label>
                    <input type="number" min="0" step="1" class="form-control calc-input" id="units" placeholder="e.g. 350" value="300">
                    <div class="form-text">Units = current meter reading minus previous reading. It is also printed on your bill.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="phase">Phase / Connection Type</label>
                    <select class="form-select calc-input" id="phase">
                        <option value="single" selected>Single Phase (most homes)</option>
                        <option value="three">Three Phase</option>
                    </select>
                    <div class="form-text">Changing phase updates the default fixed charges below - you can still edit them.</div>
                </div>
            </div>

            <h2 class="h6 mt-4">Slab Rates - approx 2025-26 residential rates, editable</h2>
            <p class="small text-muted">Rates are applied progressively: the first 100 units are charged at the first rate, the next 100 at the second rate, and so on. Change any rate if your company tariff differs.</p>
            <div class="row g-2" id="slabRates">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate1">1-100 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate1" data-rate-idx="0" value="13.42">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate2">101-200 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate2" data-rate-idx="1" value="19.58">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate3">201-300 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate3" data-rate-idx="2" value="21.70">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate4">301-400 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate4" data-rate-idx="3" value="24.48">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate5">401-500 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate5" data-rate-idx="4" value="26.13">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate6">501-600 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate6" data-rate-idx="5" value="27.46">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate7">601-700 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate7" data-rate-idx="6" value="28.06">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="rate8">Above 700 units (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input slab-rate" id="rate8" data-rate-idx="7" value="32.90">
                </div>
            </div>

            <h2 class="h6 mt-4">Extra Charges &amp; Taxes - editable</h2>
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="fpaRate">Fuel Price Adjustment (Rs/unit)</label>
                    <input type="number" min="0" step="0.01" class="form-control calc-input" id="fpaRate" value="2.00">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="gstPct">GST (%)</label>
                    <input type="number" min="0" step="0.1" class="form-control calc-input" id="gstPct" value="18">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="edPct">Electricity Duty (%)</label>
                    <input type="number" min="0" step="0.1" class="form-control calc-input" id="edPct" value="1.5">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="fixedCharges">Fixed Charges (Rs)</label>
                    <input type="number" min="0" step="1" class="form-control calc-input" id="fixedCharges" value="1000">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="tvFee">TV Fee (Rs)</label>
                    <input type="number" min="0" step="1" class="form-control calc-input" id="tvFee" value="35">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="meterRent">Meter Rent (Rs)</label>
                    <input type="number" min="0" step="1" class="form-control calc-input" id="meterRent" value="0">
                </div>
            </div>

            <button type="button" class="btn btn-primary mt-4" id="calcBtn">Calculate Estimated Bill</button>
            <div class="alert alert-danger mt-3 d-none" id="calcError" role="alert"></div>

            <div id="resultBox" class="mt-4 d-none">
                <h2 class="h5">Estimated Bill Breakdown</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr><th>Slab</th><th class="text-end">Units</th><th class="text-end">Rate (Rs)</th><th class="text-end">Amount (Rs)</th></tr>
                        </thead>
                        <tbody id="slabBody"></tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <tbody>
                            <tr><td>Energy Cost (all slabs)</td><td class="text-end fw-semibold" id="outEnergy">-</td></tr>
                            <tr><td>Fuel Price Adjustment (FPA)</td><td class="text-end" id="outFpa">-</td></tr>
                            <tr><td>Fixed Charges</td><td class="text-end" id="outFixed">-</td></tr>
                            <tr><td>Meter Rent</td><td class="text-end" id="outMeter">-</td></tr>
                            <tr><td>TV Fee</td><td class="text-end" id="outTv">-</td></tr>
                            <tr><td>Electricity Duty</td><td class="text-end" id="outEd">-</td></tr>
                            <tr><td>GST</td><td class="text-end" id="outGst">-</td></tr>
                            <tr class="table-primary"><td class="fw-bold">Total Estimated Bill</td><td class="text-end fw-bold fs-5" id="outTotal">-</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="small text-muted mb-0" id="outAvg"></p>
                <p class="small text-muted mb-0">GST is estimated on energy cost + FPA + fixed charges. Electricity Duty is estimated on energy cost. Your actual bill may apply taxes differently and may include arrears, late surcharge, quarterly tariff adjustment or other items not included here.</p>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2>How to use</h2>
            <ol>
                <li>Find your units consumed - from your last bill, or subtract the previous meter reading from the current reading.</li>
                <li>Enter the units and select single phase (most homes) or three phase.</li>
                <li>Adjust the slab rates, FPA per unit, GST, fixed charges and TV fee if your last bill shows different values - the defaults are approximate 2025-26 residential figures.</li>
                <li>Click <strong>Calculate Estimated Bill</strong> (the result also updates as you type).</li>
                <li>Read the breakdown: energy cost slab by slab, then FPA, fixed charges and taxes, and the total estimated bill.</li>
            </ol>
            <p class="mb-0">More units, higher bill — as the slab goes up, the per-unit rate also goes up, so the jump from 200 to 300 units is felt the most.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2>Frequently Asked Questions</h2>
            <h3 class="h6 mt-3">Why is my actual bill different from this estimate?</h3>
            <p>Actual bills include items that change every month: the exact FPA for that month, quarterly tariff adjustments, peak-hour usage, arrears or instalments, late payment surcharge, and your protected / non-protected consumer status. This tool gives a close planning estimate, not an official bill.</p>
            <h3 class="h6">What is FPA (Fuel Price Adjustment)?</h3>
            <p>FPA is a per-unit charge (or sometimes a credit) set according to fuel costs for electricity generation. It changes monthly and is printed as a separate line on your bill - copy the FPA per unit from your last bill into the field above for a closer estimate.</p>
            <h3 class="h6">What does "slab" mean?</h3>
            <p>Electricity is charged in blocks called slabs. The first block of units is cheapest and each higher block costs more per unit. Using fewer units keeps more of your usage in the cheaper slabs.</p>
            <h3 class="h6">How can I lower my bill?</h3>
            <p class="mb-0">Staying under the next slab boundary makes the biggest difference. Run heavy appliances (AC, iron, water motor) thoughtfully, avoid peak hours where possible, and consider solar for daytime load - even a small drop in units can move you into a cheaper slab.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var slabLimits = [100, 100, 100, 100, 100, 100, 100, Infinity];
    var slabLabels = ['1-100', '101-200', '201-300', '301-400', '401-500', '501-600', '601-700', 'Above 700'];
    var fixedByPhase = { single: 1000, three: 2000 };

    function num(id) {
        var v = parseFloat(document.getElementById(id).value);
        return isNaN(v) || v < 0 ? 0 : v;
    }

    function fmt(n) {
        return 'Rs ' + Math.round(n).toLocaleString('en-US');
    }

    function calculate() {
        var errorBox = document.getElementById('calcError');
        var resultBox = document.getElementById('resultBox');
        var unitsRaw = document.getElementById('units').value;
        var units = parseFloat(unitsRaw);
        if (unitsRaw === '' || isNaN(units) || units < 0) {
            errorBox.textContent = 'Please enter a valid number of units (0 or more).';
            errorBox.classList.remove('d-none');
            resultBox.classList.add('d-none');
            return;
        }
        errorBox.classList.add('d-none');

        var rates = [];
        document.querySelectorAll('.slab-rate').forEach(function (input) {
            var idx = parseInt(input.getAttribute('data-rate-idx'), 10);
            var v = parseFloat(input.value);
            rates[idx] = isNaN(v) || v < 0 ? 0 : v;
        });

        var remaining = units;
        var energy = 0;
        var rows = '';
        for (var i = 0; i < slabLimits.length; i++) {
            if (remaining <= 0) break;
            var slabUnits = Math.min(remaining, slabLimits[i]);
            var amount = slabUnits * rates[i];
            energy += amount;
            remaining -= slabUnits;
            rows += '<tr><td>' + slabLabels[i] + '</td><td class="text-end">' + slabUnits + '</td><td class="text-end">' + rates[i].toFixed(2) + '</td><td class="text-end">' + Math.round(amount).toLocaleString('en-US') + '</td></tr>';
        }
        if (rows === '') {
            rows = '<tr><td colspan="4" class="text-muted">No units entered - energy cost is zero, only fixed charges and fees apply.</td></tr>';
        }

        var fpa = units * num('fpaRate');
        var fixed = num('fixedCharges');
        var meterRent = num('meterRent');
        var tvFee = num('tvFee');
        var ed = energy * num('edPct') / 100;
        var gstBase = energy + fpa + fixed;
        var gst = gstBase * num('gstPct') / 100;
        var total = energy + fpa + fixed + meterRent + tvFee + ed + gst;

        document.getElementById('slabBody').innerHTML = rows;
        document.getElementById('outEnergy').textContent = fmt(energy);
        document.getElementById('outFpa').textContent = fmt(fpa);
        document.getElementById('outFixed').textContent = fmt(fixed);
        document.getElementById('outMeter').textContent = fmt(meterRent);
        document.getElementById('outTv').textContent = fmt(tvFee);
        document.getElementById('outEd').textContent = fmt(ed);
        document.getElementById('outGst').textContent = fmt(gst);
        document.getElementById('outTotal').textContent = fmt(total);
        document.getElementById('outAvg').textContent = units > 0
            ? 'Average cost per unit (all charges included): Rs ' + (total / units).toFixed(2) + ' for ' + units + ' units.'
            : '';
        resultBox.classList.remove('d-none');
    }

    document.getElementById('calcBtn').addEventListener('click', calculate);
    document.querySelectorAll('.calc-input').forEach(function (input) {
        input.addEventListener('input', calculate);
        input.addEventListener('change', calculate);
    });
    document.getElementById('phase').addEventListener('change', function () {
        var phase = document.getElementById('phase').value;
        document.getElementById('fixedCharges').value = fixedByPhase[phase] || 1000;
        calculate();
    });

    calculate();
})();
</script>
@endsection
