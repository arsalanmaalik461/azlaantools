@extends('layouts.app')

@section('title', 'Slow Cooker Conversion Calculator — Free Online Tool')
@section('meta_description', 'Convert oven and stovetop cooking times to slow cooker low and high settings.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Slow Cooker Conversion Calculator</h1>
            <p class="lead small text-muted mb-4">Convert oven and stovetop cooking times to slow cooker low and high settings.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label for="ovenTime" class="form-label fw-semibold">Select Oven / Stovetop Time</label>
                        <select class="form-select" id="ovenTime">
                            <option value="0">15 – 20 minutes</option>
                            <option value="1">20 – 30 minutes</option>
                            <option value="2" selected>30 minutes – 1 hour</option>
                            <option value="3">1 – 2 hours</option>
                            <option value="4">2 – 3 hours</option>
                            <option value="5">3 – 4 hours</option>
                        </select></div>
                    <div class="row text-center g-2">
                        <div class="col-md-6"><div class="border rounded p-3"><div class="small text-muted">Slow Cooker — LOW</div><div class="fs-5 fw-bold" id="scLow">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3"><div class="small text-muted">Slow Cooker — HIGH</div><div class="fs-5 fw-bold" id="scHigh">—</div></div></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live at once — no button press needed.</li>
                        <li>Change the value or unit and the new result shows on its own.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Times are guides based on standard published conversion charts (for dishes cooked at about 350°F / 175°C). Meat should reach a safe internal temperature — check doneness before serving, and add delicate vegetables near the end.</p>
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
    var table = [
        ["4 – 6 hours", "2 – 3 hours"],
        ["5 – 7 hours", "2.5 – 3.5 hours"],
        ["6 – 8 hours", "3 – 4 hours"],
        ["8 – 10 hours", "4 – 6 hours"],
        ["10 – 12 hours", "5 – 7 hours"],
        ["12 – 14 hours", "6 – 8 hours"]
    ];
    var sel = document.getElementById("ovenTime");
    function calc() {
        var row = table[parseInt(sel.value, 10)];
        document.getElementById("scLow").textContent = row[0];
        document.getElementById("scHigh").textContent = row[1];
    }
    sel.addEventListener("change", calc);
    calc();
})();
</script>
@endsection
