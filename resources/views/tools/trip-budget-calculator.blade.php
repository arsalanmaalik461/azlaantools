@extends('layouts.app')

@section('title', 'Trip Budget Calculator — Free Online Tool')
@section('meta_description', 'Estimate total trip cost from transport, hotel, food, days and travelers.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Trip Budget Calculator</h1>
            <p class="lead small text-muted">Plan your full trip budget in one place: transport, hotel, food, activities and misc costs — live total and per-person share. Whether it is a northern areas trip or any other journey, booking is easier when you know the total first.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="tbTrav">Travelers</label><input type="number" step="1" class="form-control" id="tbTrav" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="tbDays">Days</label><input type="number" step="1" class="form-control" id="tbDays" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="tbNights">Hotel nights</label><input type="number" step="1" class="form-control" id="tbNights" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="tbTrans">Transport total (Rs)</label><input type="number" step="any" class="form-control" id="tbTrans" placeholder="e.g. 40000"></div><div class="col-md-4 mb-3"><label class="form-label" for="tbHotel">Hotel per night (Rs)</label><input type="number" step="any" class="form-control" id="tbHotel" placeholder="e.g. 8000"></div><div class="col-md-4 mb-3"><label class="form-label" for="tbFood">Food per person per day (Rs)</label><input type="number" step="any" class="form-control" id="tbFood" placeholder="e.g. 2500"></div><div class="col-md-4 mb-3"><label class="form-label" for="tbAct">Activities per person (Rs, full trip)</label><input type="number" step="any" class="form-control" id="tbAct" placeholder="e.g. 3000"></div><div class="col-md-4 mb-3"><label class="form-label" for="tbMisc">Misc / shopping total (Rs)</label><input type="number" step="any" class="form-control" id="tbMisc" placeholder="e.g. 10000"></div></div><div class="alert alert-secondary mt-3 mb-0" id="tbRes">Enter values — the result shows live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter travelers, days and hotel nights.</li>
                <li>Enter transport as a total amount, hotel per night, food per person per day, activities per person (full trip), and misc as a total.</li>
                <li>You get the total budget, the per-person share, and a breakdown of each part.</li>
            </ol>
            <p class="small text-muted mb-0">Note: This is only a planning estimate — real prices change with the season, city and booking time. Confirm fuel/transport and hotel rates before booking.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";

function fmt(n, d) { if (n === null || n === undefined || !isFinite(n)) { return "\u2014"; } var dec = (d === undefined ? 4 : d); return Number(n.toFixed(dec)).toLocaleString("en-US", { maximumFractionDigits: dec }); }
function num(id) { var e = document.getElementById(id); if (!e) { return null; } var v = parseFloat(e.value); return isNaN(v) ? null : v; }
function txt(id) { var e = document.getElementById(id); return e ? e.value : ""; }
function setT(id, t) { var e = document.getElementById(id); if (e) { e.textContent = t; } }
function setH(id, t) { var e = document.getElementById(id); if (e) { e.innerHTML = t; } }
function bind(ids, fn) { ids.forEach(function (id) { var e = document.getElementById(id); if (e) { e.addEventListener("input", fn); e.addEventListener("change", fn); } }); }
function parseList(s) { if (!s) { return []; } var parts = s.split(/[\s,;]+/); var out = []; for (var i = 0; i < parts.length; i++) { if (parts[i] === "") { continue; } var v = parseFloat(parts[i]); if (!isNaN(v) && isFinite(v)) { out.push(v); } } return out; }
function esc(s) { return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }


function tbCalc() {
    var tr = num("tbTrav"), days = num("tbDays"), nights = num("tbNights"), trans = num("tbTrans"), hotel = num("tbHotel"), food = num("tbFood"), act = num("tbAct"), misc = num("tbMisc");
    if (tr === null || days === null || nights === null) { setT("tbRes", "Enter travelers, days and nights."); return; }
    trans = trans || 0; hotel = hotel || 0; food = food || 0; act = act || 0; misc = misc || 0;
    var hotelTot = hotel * nights; var foodTot = food * tr * days; var actTot = act * tr;
    var total = trans + hotelTot + foodTot + actTot + misc;
    setT("tbRes", "Transport: Rs " + fmt(trans, 0) + " | Hotel (" + fmt(nights, 0) + " nights): Rs " + fmt(hotelTot, 0) + " | Food: Rs " + fmt(foodTot, 0) + " | Activities: Rs " + fmt(actTot, 0) + " | Misc: Rs " + fmt(misc, 0) + " | TOTAL: Rs " + fmt(total, 0) + " | Per person: Rs " + fmt(tr > 0 ? total / tr : 0, 0) + " | Per day (total): Rs " + fmt(days > 0 ? total / days : 0, 0));
}
bind(["tbTrav","tbDays","tbNights","tbTrans","tbHotel","tbFood","tbAct","tbMisc"], tbCalc); tbCalc();

})();
</script>
@endsection
