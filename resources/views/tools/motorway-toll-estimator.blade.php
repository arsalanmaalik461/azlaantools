@extends('layouts.app')

@section('title', 'Motorway Toll Estimator — Free Online Tool')
@section('meta_description', 'Choose a route and vehicle class and find the total motorway toll.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Motorway Toll Estimator</h1>
                    <p class="lead small text-muted">Choose a motorway route (M-1, M-2, M-3, M-4, M-5, M-9) and vehicle class — get an estimate of the one-way and return total toll. All toll rates are editable.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="mtRoute">Route</label><select class="form-select" id="mtRoute"><option value="m2">M-2 Lahore — Islamabad</option><option value="m1">M-1 Peshawar — Islamabad</option><option value="m3">M-3 Lahore — Abdul Hakeem</option><option value="m4">M-4 Faisalabad — Multan</option><option value="m5">M-5 Multan — Sukkur</option><option value="m9">M-9 Karachi — Hyderabad</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="mtClass">Vehicle class</label><select class="form-select" id="mtClass"><option value="car">Car / Jeep</option><option value="wagon">Wagon / Coaster</option><option value="bus">Bus</option><option value="truck2">Truck (2 axle)</option><option value="truck3">Truck (3+ axle)</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="mtTrip">Trip</label><select class="form-select" id="mtTrip"><option value="1">One way</option><option value="2">Return (both ways)</option></select></div>
                    </div>
                    <h2 class="h6 mt-4">Is route ke toll rates (Rs, har field editable)</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="mtCar">Car / Jeep toll (Rs)</label><input type="number" class="form-control" id="mtCar" value="1100" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="mtWagon">Wagon / Coaster toll (Rs)</label><input type="number" class="form-control" id="mtWagon" value="1900" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="mtBus">Bus toll (Rs)</label><input type="number" class="form-control" id="mtBus" value="2800" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="mtTruck2">Truck 2 axle toll (Rs)</label><input type="number" class="form-control" id="mtTruck2" value="3300" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="mtTruck3">Truck 3+ axle toll (Rs)</label><input type="number" class="form-control" id="mtTruck3" value="4500" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="mtOut">Choose a route and class — the total toll will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose a motorway route and your vehicle class — the route's common published toll rates will fill in automatically.</li>
                        <li>Select one way or return.</li>
                        <li>If the toll board shows a different rate, type the correct rate in the field and see the total again.</li>
                    </ol>
                    <p class="small text-muted mb-0">Toll rates depend on the entry/exit point (toll is calculated interchange to interchange); here you get an estimate of the full route end-to-end rate that is commonly published. M-Tag and non-tag rates may differ. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup. Official source: National Highway Authority (NHA).</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) || v < 0 ? 0 : v; }
    function money(n) { return "Rs " + Math.round(n).toLocaleString("en-PK"); }
    var routes = {
        m2: { car: 1100, wagon: 1900, bus: 2800, truck2: 3300, truck3: 4500, label: "M-2 Lahore — Islamabad (about 375 km)" },
        m1: { car: 550, wagon: 950, bus: 1400, truck2: 1650, truck3: 2250, label: "M-1 Peshawar — Islamabad (about 155 km)" },
        m3: { car: 750, wagon: 1300, bus: 1900, truck2: 2250, truck3: 3050, label: "M-3 Lahore — Abdul Hakeem (about 230 km)" },
        m4: { car: 700, wagon: 1200, bus: 1750, truck2: 2100, truck3: 2850, label: "M-4 Faisalabad — Multan (about 309 km)" },
        m5: { car: 1100, wagon: 1900, bus: 2800, truck2: 3300, truck3: 4500, label: "M-5 Multan — Sukkur (about 392 km)" },
        m9: { car: 350, wagon: 600, bus: 900, truck2: 1050, truck3: 1450, label: "M-9 Karachi — Hyderabad (about 136 km)" }
    };
    function fillRoute() { var r = routes[el("mtRoute").value]; el("mtCar").value = r.car; el("mtWagon").value = r.wagon; el("mtBus").value = r.bus; el("mtTruck2").value = r.truck2; el("mtTruck3").value = r.truck3; }
    el("mtRoute").addEventListener("change", function () { fillRoute(); calc(); });
    function calc() {
        var cls = el("mtClass").value;
        var field = { car: "mtCar", wagon: "mtWagon", bus: "mtBus", truck2: "mtTruck2", truck3: "mtTruck3" }[cls];
        var oneWay = num(field);
        var trips = parseInt(el("mtTrip").value, 10);
        var total = oneWay * trips;
        el("mtOut").innerHTML = "<strong>Estimated total toll: " + money(total) + "</strong><br>Route: " + routes[el("mtRoute").value].label + " &nbsp; One way toll: " + money(oneWay) + " &nbsp; Trip: " + (trips === 2 ? "Return" : "One way") + ". Exiting at an interchange reduces the toll — this is a full route estimate.";
    }
    ["mtClass", "mtTrip", "mtCar", "mtWagon", "mtBus", "mtTruck2", "mtTruck3"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
