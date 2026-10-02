@extends('layouts.app')

@section('title', 'Electricity Units Converter - Watts to kWh & Bill Estimate | Azlaan Tools')
@section('meta_description', 'Convert appliance watts and usage hours into electricity units (kWh) and estimated cost in Pakistan, and find how long an appliance can run on given units. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Electricity Units Converter</h1>
            <p class="lead text-muted">Enter your appliance watts and daily usage hours to calculate units (kWh) and the estimated bill. Find out your electricity consumption and estimated cost instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">A. Units and Cost from Appliance</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="watts" class="form-label fw-semibold">Appliance Power (Watts)</label>
                            <input type="number" class="form-control" id="watts" value="100" min="0" step="any" placeholder="e.g. 100">
                            <div class="form-text">Fan ~80-100W, LED bulb ~10W, AC ~1200-1800W, fridge ~150W</div>
                        </div>
                        <div class="col-md-6">
                            <label for="hoursPerDay" class="form-label fw-semibold">Hours per Day (hours / day)</label>
                            <input type="number" class="form-control" id="hoursPerDay" value="8" min="0" max="24" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="days" class="form-label fw-semibold">Days</label>
                            <input type="number" class="form-control" id="days" value="30" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="rate" class="form-label fw-semibold">Per Unit Rate (Rs / kWh)</label>
                            <input type="number" class="form-control" id="rate" value="40" min="0" step="any">
                            <div class="form-text">You can change the rate according to your bill.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="calcA">Calculate Units &amp; Cost</button>
                    <div class="alert alert-success mt-3 mb-0 d-none" id="resultA"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">B. How Long Will an Appliance Run on Your Units?</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="unitsInput" class="form-label fw-semibold">Available Units (kWh)</label>
                            <input type="number" class="form-control" id="unitsInput" value="10" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="wattsB" class="form-label fw-semibold">Appliance Power (Watts)</label>
                            <input type="number" class="form-control" id="wattsB" value="100" min="1" step="any">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="calcB">Calculate Running Time</button>
                    <div class="alert alert-info mt-3 mb-0 d-none" id="resultB"></div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li><strong>Card A:</strong> Enter the appliance watts, daily hours and days, set the per-unit rate, then press <strong>Calculate</strong> — you will get total units (kWh) and estimated cost.</li>
                        <li><strong>Card B:</strong> If you have fixed units (for example on UPS / solar), enter the units and appliance watts — you will see how many hours the appliance can run.</li>
                        <li>Formula: Units (kWh) = Watts × Hours × Days ÷ 1000. Cost = Units × Per-Unit Rate.</li>
                    </ol>
                    <p class="small text-muted mb-0">Note: This is only an estimate. The actual bill depends on slabs, taxes, fuel adjustment and meter reading.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function num(id) { return parseFloat(document.getElementById(id).value); }
    function fmt(n, digits) { return n.toLocaleString('en-PK', { maximumFractionDigits: digits, minimumFractionDigits: 0 }); }

    document.getElementById('calcA').addEventListener('click', function () {
        var watts = num('watts'), hours = num('hoursPerDay'), days = num('days'), rate = num('rate');
        var box = document.getElementById('resultA');
        box.classList.remove('d-none');
        if (isNaN(watts) || isNaN(hours) || isNaN(days) || isNaN(rate) || watts < 0 || hours < 0 || days < 0 || rate < 0) {
            box.className = 'alert alert-danger mt-3 mb-0';
            box.textContent = 'Please enter valid non-negative numbers in all fields.';
            return;
        }
        var kwh = watts * hours * days / 1000;
        var cost = kwh * rate;
        var daily = watts * hours / 1000;
        box.className = 'alert alert-success mt-3 mb-0';
        box.innerHTML = '<strong>' + fmt(kwh, 2) + ' units (kWh)</strong> consumed in ' + fmt(days, 0) + ' days<br>' +
            'Estimated cost: <strong>Rs ' + fmt(cost, 2) + '</strong> at Rs ' + fmt(rate, 2) + ' per unit<br>' +
            '<span class="small">Per day: ' + fmt(daily, 3) + ' units — Rs ' + fmt(daily * rate, 2) + '</span>';
    });

    document.getElementById('calcB').addEventListener('click', function () {
        var units = num('unitsInput'), watts = num('wattsB');
        var box = document.getElementById('resultB');
        box.classList.remove('d-none');
        if (isNaN(units) || isNaN(watts) || units < 0 || watts <= 0) {
            box.className = 'alert alert-danger mt-3 mb-0';
            box.textContent = 'Please enter valid units and a watt value greater than zero.';
            return;
        }
        var hours = units * 1000 / watts;
        var wholeHours = Math.floor(hours);
        var minutes = Math.round((hours - wholeHours) * 60);
        if (minutes === 60) { wholeHours += 1; minutes = 0; }
        var daysApprox = hours / 24;
        box.className = 'alert alert-info mt-3 mb-0';
        box.innerHTML = 'A <strong>' + fmt(watts, 0) + 'W</strong> appliance can run for about <strong>' + fmt(hours, 2) + ' hours</strong> on ' + fmt(units, 2) + ' units' +
            ' (approx. ' + wholeHours + ' hours ' + minutes + ' minutes, or ' + fmt(daysApprox, 2) + ' days of continuous use).';
    });
})();
</script>
@endsection
