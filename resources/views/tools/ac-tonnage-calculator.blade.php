@extends('layouts.app')

@section('title', 'AC Tonnage Calculator - How Many Tons of AC for Your Room? | Azlaan Tools')
@section('meta_description', 'Calculate the right AC size (tons and BTU) for your room in Pakistan from room size, ceiling height, sun-facing walls and people. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">AC Tonnage Calculator</h1>
            <p class="lead text-muted">How many tons of AC are right for your room size? Enter the details — the recommended size appears instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="len" class="form-label fw-semibold">Room Length (ft)</label>
                            <input type="number" class="form-control form-control-lg" id="len" value="14" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="wid" class="form-label fw-semibold">Room Width (ft)</label>
                            <input type="number" class="form-control form-control-lg" id="wid" value="12" min="1" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="height" class="form-label fw-semibold">Ceiling Height (ft)</label>
                            <input type="number" class="form-control form-control-lg" id="height" value="10" min="7" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="floor" class="form-label fw-semibold">Floor</label>
                            <select class="form-select form-select-lg" id="floor">
                                <option value="1" selected>Ground / Middle Floor</option>
                                <option value="1.15">Top Floor (sun on roof)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sun" class="form-label fw-semibold">Sun-Facing Walls</label>
                            <select class="form-select form-select-lg" id="sun">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="people" class="form-label fw-semibold">People in Room (usually)</label>
                            <input type="number" class="form-control form-control-lg" id="people" value="2" min="0" step="1">
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Required Cooling</div><div class="fs-4 fw-bold" id="outBtu">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-success text-white"><div class="small">Recommended AC</div><div class="fs-4 fw-bold" id="outTon">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Approx Running Watts</div><div class="fs-5 fw-bold" id="outWatts">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0" id="outNote"></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the room length, width and ceiling height in feet.</li>
                        <li>Select top floor, sun-facing walls and the usual number of people.</li>
                        <li>The recommended tonnage and approximate inverter / non-inverter watts appear instantly.</li>
                    </ol>
                    <p class="small text-muted mb-0">Model: Base = Area (sq ft) x 70 BTU (65-80 range for Pakistan's heat). If height is over 10 ft, add +5% for each extra foot. Top floor x1.15. Each sun-facing wall +7%. Each person over 2 adds +500 BTU. 1 Ton = 12,000 BTU. For AC fitting, service or a new AC — Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function val(id) { return parseFloat(document.getElementById(id).value) || 0; }
    function calc() {
        var area = val('len') * val('wid');
        var btu = area * 70;
        var h = val('height');
        if (h > 10) { btu *= 1 + (h - 10) * 0.05; }
        btu *= val('floor');
        btu *= 1 + val('sun') * 0.07;
        var extraPeople = Math.max(0, val('people') - 2);
        btu += extraPeople * 500;
        var tonsNeeded = btu / 12000;
        var sizes = [1, 1.5, 2, 2.5];
        var rec = 2.5;
        for (var i = 0; i < sizes.length; i++) { if (tonsNeeded <= sizes[i]) { rec = sizes[i]; break; } }
        document.getElementById('outBtu').textContent = Math.round(btu).toLocaleString('en-PK') + ' BTU';
        document.getElementById('outTon').textContent = rec + ' Ton';
        var invW = Math.round(rec * 800), nonW = Math.round(rec * 1200);
        document.getElementById('outWatts').textContent = 'Inverter ~' + invW.toLocaleString('en-PK') + 'W · Non-Inv ~' + nonW.toLocaleString('en-PK') + 'W';
        document.getElementById('outNote').textContent = 'Room area: ' + Math.round(area) + ' sq ft - Exact load: ' + tonsNeeded.toFixed(2) + ' ton — so the standard ' + rec + ' ton size is recommended.' + (tonsNeeded > 2.5 ? ' Note: Load is over 2.5 tons — consider 2 ACs or a bigger commercial unit.' : '');
    }
    ['len', 'wid', 'height', 'floor', 'sun', 'people'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
