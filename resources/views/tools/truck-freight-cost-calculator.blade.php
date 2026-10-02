@extends('layouts.app')

@section('title', 'Truck Freight Cost Calculator — Free Online Tool')
@section('meta_description', 'Enter distance, diesel cost and loading charges to find truck freight cost per trip.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Truck Freight Cost Calculator</h1>
            <p class="lead small text-muted">Enter distance, diesel cost and loading charges to find your truck freight cost per trip.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="dist">Trip Distance (km, one way loaded)</label><input type="number" step="any" class="form-control" id="dist" value="500"></div>
                    <div class="col-md-6"><label class="form-label" for="trips">Trips</label><input type="number" step="any" class="form-control" id="trips" value="1"></div>
                    <div class="col-md-6"><label class="form-label" for="mileage">Fuel Average (km per litre)</label><input type="number" step="any" class="form-control" id="mileage" value="4"></div>
                    <div class="col-md-6"><label class="form-label" for="diesel">Diesel Rate (Rs per litre, editable)</label><input type="number" step="any" class="form-control" id="diesel" value="280"></div>
                    <div class="col-md-6"><label class="form-label" for="toll">Toll and Route Charges per Trip (Rs)</label><input type="number" step="any" class="form-control" id="toll" value="3000"></div>
                    <div class="col-md-6"><label class="form-label" for="loading">Loading / Unloading per Trip (Rs)</label><input type="number" step="any" class="form-control" id="loading" value="5000"></div>
                    <div class="col-md-6"><label class="form-label" for="driver">Driver and Helper per Trip (Rs)</label><input type="number" step="any" class="form-control" id="driver" value="8000"></div>
                    <div class="col-md-6"><label class="form-label" for="other">Other per Trip: maintenance share etc (Rs)</label><input type="number" step="any" class="form-control" id="other" value="2000"></div>
                    </div>
                    
                    <div id="msg" class="alert alert-warning mt-3 d-none"></div>
                    <div class="border rounded p-3 mt-3">
                        <div class="small text-muted mb-1">Result</div>
                        <div id="result" class="fw-semibold">Enter values to see the result.</div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Fill in the fields above with your own figures — results update live as you type.</li>
                        <li>Fuel need is distance divided by the truck average. All per trip charges are added to the fuel cost.</li>
                        <li>Check the result box and adjust any rate or parameter to test other cases.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Diesel rate is the value you enter — no live fuel prices are used. Empty return running is not included unless you add it in distance.</p>
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
    function n(id) { var v = parseFloat(document.getElementById(id).value); return isNaN(v) ? 0 : v; }
    function t(id) { return document.getElementById(id).value; }
    function fmt(x) { return "Rs " + Math.round(x).toLocaleString("en-US"); }
    function fmt2(x) { return "Rs " + x.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function pct(x) { return x.toFixed(2) + "%"; }
    function num(x) { return x.toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function showMsg(m) { var b = document.getElementById("msg"); if (m) { b.textContent = m; b.classList.remove("d-none"); } else { b.classList.add("d-none"); } }
    function setResult(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var fuelL = n("dist") / Math.max(n("mileage"), 0.1); var fuelCost = fuelL * n("diesel"); var perTrip = fuelCost + n("toll") + n("loading") + n("driver") + n("other"); var total = perTrip * n("trips"); setResult("Diesel used per trip: " + num(fuelL) + " litre<br>Diesel cost per trip: " + fmt(fuelCost) + "<br>Cost per trip: " + fmt(perTrip) + "<br><strong>Total cost for " + num(n("trips")) + " trips: " + fmt(total) + "</strong>" + (n("dist") > 0 ? "<br>Cost per km: " + fmt2(perTrip / n("dist")) : "")); showMsg("");
    }
    var inputs = document.querySelectorAll("input, select");
    for (var i = 0; i < inputs.length; i++) { inputs[i].addEventListener("input", calc); inputs[i].addEventListener("change", calc); }
    calc();
})();
</script>
@endsection
