@extends('layouts.app')

@section('title', 'Appliance Running Cost Calculator - Monthly Bill per Appliance | Azlaan Tools')
@section('meta_description', 'Calculate units and monthly running cost of AC, fridge, fan, washing machine, motor and more in Pakistan. Editable appliances table, free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Appliance Running Cost Calculator</h1>
            <p class="lead text-muted">How many units and rupees does each appliance use per month? Set the quantity and hours — instant total.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="rate" class="form-label fw-semibold">Per Unit Rate (Rs / kWh)</label>
                            <input type="number" class="form-control form-control-lg" id="rate" value="55" min="0" step="any">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Appliance</th><th>Watts (W)</th><th>Qty</th><th>Hours / Day</th><th>Rs / Month</th><th></th></tr></thead>
                            <tbody id="rows"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRow">+ Add Custom Appliance</button>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Units / Day</div><div class="fs-4 fw-bold" id="outDay">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Units / Month</div><div class="fs-4 fw-bold" id="outMonth">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Cost / Month</div><div class="fs-4 fw-bold" id="outCost">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter each appliance's quantity and daily hours — you can also change the watts to match your own model.</li>
                        <li>Set your per-unit rate (default Rs 55).</li>
                        <li>For an appliance not in the list, press <strong>Add Custom Appliance</strong>. To delete a row, press ×.</li>
                    </ol>
                    <p class="small text-muted mb-0">Formula: Units/day = Watts × Qty × Hours ÷ 1000. For AC, fridge or motor repair / service: Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var presets = [
        ['1.5 Ton Inverter AC', 1200, 1, 8],
        ['1.5 Ton Non-Inverter AC', 1800, 0, 8],
        ['Fridge', 150, 1, 24],
        ['Ceiling Fan', 75, 2, 12],
        ['Washing Machine', 500, 1, 1],
        ['Water Motor (1 HP)', 750, 1, 1],
        ['Microwave', 1200, 1, 0.5],
        ['Iron', 1000, 1, 0.5],
        ['LED TV', 100, 1, 5],
        ['Electric Geyser', 2000, 0, 2]
    ];
    var tbody = document.getElementById('rows');
    function makeRow(name, watts, qty, hours) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td><input type="text" class="form-control form-control-sm a-name" value=""></td>' +
            '<td><input type="number" class="form-control form-control-sm a-w" min="0" step="any"></td>' +
            '<td><input type="number" class="form-control form-control-sm a-q" min="0" step="1" style="width:70px"></td>' +
            '<td><input type="number" class="form-control form-control-sm a-h" min="0" max="24" step="any" style="width:90px"></td>' +
            '<td class="a-cost fw-semibold">Rs 0</td>' +
            '<td><button type="button" class="btn btn-outline-danger btn-sm a-del">×</button></td>';
        tr.querySelector('.a-name').value = name;
        tr.querySelector('.a-w').value = watts;
        tr.querySelector('.a-q').value = qty;
        tr.querySelector('.a-h').value = hours;
        tr.querySelector('.a-del').addEventListener('click', function () { tr.remove(); calc(); });
        tr.querySelectorAll('input').forEach(function (i) { i.addEventListener('input', calc); });
        tbody.appendChild(tr);
    }
    presets.forEach(function (p) { makeRow(p[0], p[1], p[2], p[3]); });
    document.getElementById('addRow').addEventListener('click', function () { makeRow('Custom Appliance', 100, 1, 1); calc(); });
    document.getElementById('rate').addEventListener('input', calc);
    function calc() {
        var rate = parseFloat(document.getElementById('rate').value) || 0;
        var dayUnits = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            var w = parseFloat(tr.querySelector('.a-w').value) || 0;
            var q = parseFloat(tr.querySelector('.a-q').value) || 0;
            var h = parseFloat(tr.querySelector('.a-h').value) || 0;
            var u = w * q * h / 1000;
            dayUnits += u;
            tr.querySelector('.a-cost').textContent = 'Rs ' + Math.round(u * 30 * rate).toLocaleString('en-PK');
        });
        document.getElementById('outDay').textContent = dayUnits.toFixed(2) + ' units';
        document.getElementById('outMonth').textContent = (dayUnits * 30).toFixed(1) + ' units';
        document.getElementById('outCost').textContent = 'Rs ' + Math.round(dayUnits * 30 * rate).toLocaleString('en-PK');
    }
    calc();
})();
</script>
@endsection
