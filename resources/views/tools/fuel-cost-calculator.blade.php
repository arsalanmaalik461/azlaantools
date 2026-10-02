@extends('layouts.app')

@section('title', 'Fuel Cost Calculator - Trip and Monthly Petrol Cost | Azlaan Tools')
@section('meta_description', 'Free fuel cost calculator: enter distance, mileage and petrol price to find fuel needed, total trip cost, cost per person and monthly commute cost. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Fuel Cost Calculator</h1>
            <p class="lead text-muted">Find out your trip or daily commute fuel cost in advance — enter the distance, mileage and current fuel price, and get the total cost instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="distance" class="form-label fw-semibold">Distance (km)</label>
                            <input type="number" class="form-control" id="distance" min="0" step="any" placeholder="e.g. 50">
                            <div class="form-text">This is the one-way distance; it will double if round trip is on.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="mileage" class="form-label fw-semibold">Vehicle Mileage (km per litre)</label>
                            <input type="number" class="form-control" id="mileage" min="0" step="any" placeholder="e.g. 15">
                        </div>
                        <div class="col-md-6">
                            <label for="fuelPrice" class="form-label fw-semibold">Fuel Price per Litre (PKR)</label>
                            <input type="number" class="form-control" id="fuelPrice" min="0" step="any" value="260">
                            <div class="form-text">Default Rs 260 — check today's rate and enter it; rates keep changing.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="passengers" class="form-label fw-semibold">Passengers / Cost Split (optional)</label>
                            <input type="number" class="form-control" id="passengers" min="1" step="1" placeholder="e.g. 3 — leave empty for no split">
                        </div>
                        <div class="col-md-6">
                            <label for="tripsPerMonth" class="form-label fw-semibold">Trips per Month (monthly commute)</label>
                            <input type="number" class="form-control" id="tripsPerMonth" min="0" step="any" placeholder="e.g. 22">
                            <div class="form-text">For a daily office commute, 22 trips/month is common.</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="roundTrip">
                                <label class="form-check-label fw-semibold" for="roundTrip">Round Trip (return included)</label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="fuelMsg"></div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Distance</div><div class="fs-5 fw-bold" id="totalDistOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Fuel Needed</div><div class="fs-5 fw-bold" id="fuelNeededOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Cost</div><div class="fs-5 fw-bold text-success" id="totalCostOut">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Cost Per Person</div><div class="fs-5 fw-bold" id="perPersonOut">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Monthly Cost</div><div class="fs-5 fw-bold" id="monthlyCostOut">—</div><div class="small text-muted" id="monthlyDetail"></div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the one-way distance in kilometres, and turn on Round Trip if you will return too.</li>
                        <li>Enter your car or bike average mileage (km per litre).</li>
                        <li>Check and enter the fuel price — default is Rs 260; check today's rate and enter it.</li>
                        <li>Optional: enter the number of passengers to see cost per person, and trips per month to see the monthly commute cost.</li>
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
    var ids = ['distance', 'mileage', 'fuelPrice', 'passengers', 'tripsPerMonth', 'roundTrip'];
    function fmt(n) { return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function calculate() {
        var msg = document.getElementById('fuelMsg');
        var distance = parseFloat(document.getElementById('distance').value);
        var mileage = parseFloat(document.getElementById('mileage').value);
        var price = parseFloat(document.getElementById('fuelPrice').value);
        var passengers = parseFloat(document.getElementById('passengers').value);
        var trips = parseFloat(document.getElementById('tripsPerMonth').value);
        var roundTrip = document.getElementById('roundTrip').checked;
        function reset(text) {
            document.getElementById('totalDistOut').textContent = '—';
            document.getElementById('fuelNeededOut').textContent = '—';
            document.getElementById('totalCostOut').textContent = '—';
            document.getElementById('perPersonOut').textContent = '—';
            document.getElementById('monthlyCostOut').textContent = '—';
            document.getElementById('monthlyDetail').textContent = '';
            if (text) { msg.textContent = text; msg.classList.remove('d-none'); } else { msg.classList.add('d-none'); }
        }
        if (isNaN(distance) || isNaN(mileage) || isNaN(price)) { reset(null); return; }
        if (distance < 0 || price < 0) { reset('Distance and fuel price cannot be negative.'); return; }
        if (mileage <= 0) { reset('Mileage must be more than 0, otherwise fuel cannot be calculated.'); return; }
        msg.classList.add('d-none');
        var totalDist = roundTrip ? distance * 2 : distance;
        var litres = totalDist / mileage;
        var cost = litres * price;
        document.getElementById('totalDistOut').textContent = totalDist.toLocaleString('en-PK') + ' km' + (roundTrip ? ' (round trip)' : '');
        document.getElementById('fuelNeededOut').textContent = litres.toFixed(2) + ' litres';
        document.getElementById('totalCostOut').textContent = fmt(cost);
        if (!isNaN(passengers) && passengers >= 1) {
            document.getElementById('perPersonOut').textContent = fmt(cost / passengers) + ' (split between ' + Math.floor(passengers) + ')';
        } else { document.getElementById('perPersonOut').textContent = '—'; }
        if (!isNaN(trips) && trips > 0) {
            document.getElementById('monthlyCostOut').textContent = fmt(cost * trips);
            document.getElementById('monthlyDetail').textContent = trips + ' trips x ' + fmt(cost) + ' | Monthly fuel: ' + (litres * trips).toFixed(2) + ' litres';
        } else { document.getElementById('monthlyCostOut').textContent = '—'; document.getElementById('monthlyDetail').textContent = 'Enter trips per month to see the monthly cost.'; }
    }
    ids.forEach(function (id) {
        var el = document.getElementById(id);
        el.addEventListener('input', calculate);
        el.addEventListener('change', calculate);
    });
    calculate();
})();
</script>
@endsection
