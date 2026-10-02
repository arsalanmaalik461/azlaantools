@extends('layouts.app')

@section('title', 'Fuel Cost Splitter — Free Online Tool')
@section('meta_description', 'Split a road trip fuel bill fairly among passengers by distance shared.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fuel Cost Splitter</h1>
            <p class="lead small text-muted">Split the total fuel bill fairly among passengers — the more km someone travelled with you, the bigger their share. Enter names and each passenger's shared km.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="fuTotal">Total fuel cost (Rs)</label><input type="number" class="form-control inp" id="fuTotal" placeholder="e.g. 6000" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="fuTrip">Total trip distance (km)</label><input type="number" class="form-control inp" id="fuTrip" placeholder="e.g. 300" step="any"></div>
                    </div>
                    <h2 class="h6 mt-4">Passengers — name of each person and the km they travelled with you</h2>
                    <div id="fuRows"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="fuAdd">+ Add Passenger</button>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the total fuel cost and the trip's total km.</li><li>Enter each passenger's name and their shared km (use the + button for more passengers).</li><li>Each person's share will show in Rs in the table.</li></ol>
                    <p class="small text-muted mb-0">Note: Fair share formula: share = (passenger's km ÷ total shared km of all passengers) x total cost. This works when the cost is split by travel km; settle the driver's effort or toll separately.</p>
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
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    var fuCount = 0;
    function fuRow(name, km) {
        fuCount++; var div = document.createElement("div"); div.className = "row g-2 mt-1 fu-row";
        div.innerHTML = "<div class=\"col-md-6\"><input type=\"text\" class=\"form-control fu-name\" placeholder=\"Name\" value=\"" + name + "\"></div><div class=\"col-md-4\"><input type=\"number\" class=\"form-control fu-km\" placeholder=\"Shared km\" step=\"any\" value=\"" + km + "\"></div><div class=\"col-md-2\"><button type=\"button\" class=\"btn btn-outline-danger w-100 fu-del\">Remove</button></div>";
        el("fuRows").appendChild(div);
        div.querySelectorAll("input").forEach(function (i) { i.addEventListener("input", calc); });
        div.querySelector(".fu-del").addEventListener("click", function () { div.remove(); calc(); });
    }
    function calc() {
        var total = num("fuTotal"), trip = num("fuTrip");
        var names = document.querySelectorAll(".fu-name"), kms = document.querySelectorAll(".fu-km");
        var people = [], sumKm = 0;
        names.forEach(function (nm, i) { var km = parseFloat(kms[i].value); if (!isNaN(km) && km > 0) { people.push({ n: nm.value.trim() || ("Passenger " + (i + 1)), km: km }); sumKm += km; } });
        if (total === null || total <= 0) { err("Please enter the total fuel cost."); return; }
        if (!people.length || sumKm <= 0) { err("Enter shared km for at least one passenger."); return; }
        var perKmNote = trip && trip > 0 ? "Fuel cost per km (whole trip) = Rs " + fmt(total / trip, 2) : "";
        var rows = people.map(function (p) { var share = p.km / sumKm * total; return "<tr><td>" + p.n + "</td><td>" + fmt(p.km, 1) + " km</td><td>" + fmt(p.km / sumKm * 100, 1) + "%</td><td><strong>Rs " + fmt(share, 2) + "</strong></td></tr>"; }).join("");
        show("<div class=\"table-responsive\"><table class=\"table table-sm\"><thead><tr><th>Passenger</th><th>Shared distance</th><th>Share</th><th>To pay</th></tr></thead><tbody>" + rows + "</tbody></table></div>");
        showSteps(["Total shared km of all passengers = " + fmt(sumKm, 1), "Each passenger share = (their km ÷ " + fmt(sumKm, 1) + ") x Rs " + fmt(total, 2), perKmNote]);
    }
    el("fuAdd").addEventListener("click", function () { fuRow("", ""); });
    ["fuTotal", "fuTrip"].forEach(function (id) { el(id).addEventListener("input", calc); });
    fuRow("Passenger 1", ""); fuRow("Passenger 2", ""); calc();

})();
</script>
@endsection
