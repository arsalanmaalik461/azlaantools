@extends('layouts.app')

@section('title', 'Umrah Cost Planner — Free Online Tool')
@section('meta_description', 'Enter ticket, visa, hotel, food and transport costs, and plan your total Umrah budget.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Umrah Cost Planner</h1>
            <p class="lead small text-muted">Enter every cost yourself — ticket, visa, hotel (per night), food (per day), transport and ziarat — and get the per-person and full group budget.</p>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="umPersons">Persons</label><input type="number" class="form-control um-in" id="umPersons" value="2" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umDays">Trip days</label><input type="number" class="form-control um-in" id="umDays" value="15" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umTicket">Air ticket per person (Rs)</label><input type="number" class="form-control um-in" id="umTicket" value="180000" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umVisa">Visa + processing per person (Rs)</label><input type="number" class="form-control um-in" id="umVisa" value="55000" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umHotelNight">Hotel per room per night (Rs)</label><input type="number" class="form-control um-in" id="umHotelNight" value="18000" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umRooms">Rooms</label><input type="number" class="form-control um-in" id="umRooms" value="1" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umFood">Food per person per day (Rs)</label><input type="number" class="form-control um-in" id="umFood" value="4000" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umTransport">Transport total (Rs) — transfers, ziarat trips</label><input type="number" class="form-control um-in" id="umTransport" value="40000" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="umMisc">Misc per person (Rs) — shopping, SIM, laundry</label><input type="number" class="form-control um-in" id="umMisc" value="30000" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-4"><div class="text-muted small">Cost per person</div><div class="fs-4 fw-bold" id="umPer">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Total group budget</div><div class="fs-4 fw-bold" id="umTotal">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Hotel total</div><div class="fs-4 fw-bold" id="umHotel">—</div></div>
            </div><ul class="small text-muted mb-0 mt-2" id="umBreak"></ul></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter persons and days.</li><li>Enter ticket, visa, hotel per night, food per day and other costs as per your information.</li><li>See the breakdown of each part with the per-person and total budget.</li></ol>
            <p class="small text-muted mb-0">Note: This is only a budget worksheet — it has no live package prices. Air ticket and hotel rates change a lot by season (Ramadan, school holidays); confirm a fresh rate with your agent before booking. Rates change — verify with the official source before relying on this.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    function calc() {
        var persons = Math.max(1, num("umPersons")), days = Math.max(1, num("umDays"));
        var ticket = num("umTicket"), visa = num("umVisa"), hotel = num("umHotelNight") * num("umRooms") * days;
        var food = num("umFood") * days * persons, transport = num("umTransport"), misc = num("umMisc") * persons;
        var total = ticket * persons + visa * persons + hotel + food + transport + misc;
        el("umPer").textContent = rs(total / persons); el("umTotal").textContent = rs(total); el("umHotel").textContent = rs(hotel);
        el("umBreak").innerHTML = "<li>Tickets: " + rs(ticket * persons) + "</li><li>Visa: " + rs(visa * persons) + "</li><li>Hotel (" + days + " nights): " + rs(hotel) + "</li><li>Food: " + rs(food) + "</li><li>Transport: " + rs(transport) + "</li><li>Misc: " + rs(misc) + "</li>";
    }
    document.querySelectorAll(".um-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
