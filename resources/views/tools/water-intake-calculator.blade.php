@extends('layouts.app')
@section('title', 'Water Intake Calculator — Azlaan Tools')
@section('meta_description', 'Free daily water intake calculator. Enter your weight and activity to find how many litres and glasses of water you should drink each day.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Water Intake Calculator</h1>
            <p class="lead text-muted">How much water should you drink daily? Get a personal target in litres and glasses, adjusted for exercise and hot Pakistani summers.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>
                    <div class="col-md-6"><label class="form-label" for="activity">Exercise Today (minutes)</label><input type="number" class="form-control" id="activity" value="30"></div>
                    <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="hot"><label class="form-check-label" for="hot">Hot climate today (summer heat / outdoor work) — adds 20%</label></div></div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4"><div class="border rounded p-3 bg-primary text-white text-center"><div class="small">Daily Target</div><div class="fs-4 fw-bold" id="litresOut">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Glasses (250 ml)</div><div class="fs-4 fw-bold" id="glassesOut">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Per Waking Hour</div><div class="fs-4 fw-bold" id="hourlyOut">—</div></div></div>
                </div>
                <p class="small text-muted mt-3 mb-0">Tip: spread it out — roughly one glass every 1.5–2 hours while awake beats drinking it all at once. This is an estimate for information only — not medical advice.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Enter your weight in kg and today&rsquo;s exercise minutes.</li><li>Turn on the hot-climate switch on very hot days.</li><li>Your daily litres, glasses and hourly pace update instantly.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function calc() {
        var w = parseFloat(document.getElementById('weight').value) || 0;
        var mins = parseFloat(document.getElementById('activity').value) || 0;
        var hot = document.getElementById('hot').checked;
        if (w <= 0) { return; }
        var ml = (w * 35) + (mins * 12);
        if (hot) ml = ml * 1.2;
        var litres = ml / 1000;
        document.getElementById('litresOut').textContent = litres.toFixed(2) + ' L';
        document.getElementById('glassesOut').textContent = Math.round(ml / 250) + ' glasses';
        document.getElementById('hourlyOut').textContent = Math.round(ml / 16) + ' ml';
    }
    ['weight','activity'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc); });
    document.getElementById('hot').addEventListener('change', calc);
    calc();
})();
</script>
@endsection
