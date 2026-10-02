@extends('layouts.app')

@section('title', 'World Clock — Free Online Tool')
@section('meta_description', 'See the current local time in major world cities side by side.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">World Clock</h1>
                    <p class="lead small text-muted">Live clocks for Karachi, Dubai, London, New York and more, updating every second with dates and UTC offsets — no API, all built in time zone data.</p>
                    <div class="row g-2" id="wcGrid"></div>
                    <div class="alert alert-info mt-3 mb-0" id="wcLocal">Your local time will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Just open the page — every clock updates live, every second.</li>
                        <li>Compare your own local time at the bottom with each city.</li>
                        <li>Offsets change automatically when a city moves to or from daylight saving time.</li>
                    </ol>
                    <p class="small text-muted mb-0">Times come from the built in Intl time zone database in your browser, which includes daylight saving rules for every city shown. No external service is contacted.</p>
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
    var zones = [
        ["Karachi, Pakistan", "Asia/Karachi"], ["Lahore / Islamabad, Pakistan", "Asia/Karachi"], ["Dubai, UAE", "Asia/Dubai"], ["Riyadh, Saudi Arabia", "Asia/Riyadh"], ["Doha, Qatar", "Asia/Qatar"], ["Dhaka, Bangladesh", "Asia/Dhaka"], ["Delhi, India", "Asia/Kolkata"], ["Kuala Lumpur, Malaysia", "Asia/Kuala_Lumpur"], ["Singapore", "Asia/Singapore"], ["Beijing, China", "Asia/Shanghai"], ["Tokyo, Japan", "Asia/Tokyo"], ["Sydney, Australia", "Australia/Sydney"], ["London, UK", "Europe/London"], ["Berlin, Germany", "Europe/Berlin"], ["Istanbul, Turkiye", "Europe/Istanbul"], ["Moscow, Russia", "Europe/Moscow"], ["New York, USA", "America/New_York"], ["Chicago, USA", "America/Chicago"], ["Los Angeles, USA", "America/Los_Angeles"], ["Toronto, Canada", "America/Toronto"], ["UTC", "UTC"]
    ];
    var grid = el("wcGrid");
    var html = "";
    zones.forEach(function (z, i) { html += "<div class=\"col-6 col-md-4 col-lg-3\"><div class=\"border rounded p-2 text-center h-100\"><div class=\"small text-muted\">" + z[0] + "</div><div class=\"fw-bold\" id=\"wc" + i + "\">—</div><div class=\"small text-muted\" id=\"wcd" + i + "\"></div></div></div>"; });
    grid.innerHTML = html;
    function tick() {
        var now = new Date();
        zones.forEach(function (z, i) {
            el("wc" + i).textContent = new Intl.DateTimeFormat("en-GB", { timeZone: z[1], hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true }).format(now);
            el("wcd" + i).textContent = new Intl.DateTimeFormat("en-GB", { timeZone: z[1], weekday: "short", day: "2-digit", month: "short" }).format(now);
        });
        el("wcLocal").innerHTML = "<strong>Your local time:</strong> " + now.toLocaleString("en-GB", { weekday: "long", year: "numeric", month: "long", day: "numeric", hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true }) + " (" + (Intl.DateTimeFormat().resolvedOptions().timeZone || "local zone") + ")";
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
@endsection
