@extends('layouts.app')
@section('title', 'Body Fat Calculator — Azlaan Tools')
@section('meta_description', 'Free body fat percentage calculator using the US Navy method and BMI method. Enter height, weight, neck, waist and hip measurements.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Body Fat Calculator</h1>
            <p class="lead text-muted">Estimate your body fat percentage with the US Navy tape method and the BMI method — no signup, instant results.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male" selected>Male</option><option value="female">Female</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="age">Age (years)</label><input type="number" class="form-control" id="age" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>
                    <div class="col-md-6"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>
                    <div class="col-md-4"><label class="form-label" for="neck">Neck (cm)</label><input type="number" class="form-control" id="neck" value="38" step="any"></div>
                    <div class="col-md-4"><label class="form-label" for="waist">Waist (cm)</label><input type="number" class="form-control" id="waist" value="85" step="any"></div>
                    <div class="col-md-4" id="hipWrap"><label class="form-label" for="hip">Hip (cm) — women</label><input type="number" class="form-control" id="hip" value="95" step="any"></div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">US Navy Method</div><div class="fs-4 fw-bold" id="navyOut">—</div><div class="small fw-bold" id="navyCat">—</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">BMI Method Estimate</div><div class="fs-4 fw-bold" id="bmiOut">—</div><div class="small text-muted">Less precise, for comparison</div></div></div>
                </div>
                <p class="small text-muted mt-3 mb-0">Categories — Men: Essential 2–5%, Athletes 6–13%, Fitness 14–17%, Average 18–24%, Obese 25%+. Women: Essential 10–13%, Athletes 14–20%, Fitness 21–24%, Average 25–31%, Obese 32%+. This is an estimate for information only — not medical advice.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Measure neck just below the voice box, waist at the navel, and hip (women) at the widest point — all in cm.</li><li>Enter your details; results update instantly.</li><li>Compare the Navy result with the BMI estimate and check your category.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function log10(x) { return Math.log(x) / Math.LN10; }
    function category(sex, bf) {
        if (sex === 'male') {
            if (bf < 6) return 'Essential Fat';
            if (bf < 14) return 'Athletes';
            if (bf < 18) return 'Fitness';
            if (bf < 25) return 'Average';
            return 'Obese';
        }
        if (bf < 14) return 'Essential Fat';
        if (bf < 21) return 'Athletes';
        if (bf < 25) return 'Fitness';
        if (bf < 32) return 'Average';
        return 'Obese';
    }
    function calc() {
        var sex = document.getElementById('sex').value;
        var age = parseFloat(document.getElementById('age').value) || 0;
        var w = parseFloat(document.getElementById('weight').value) || 0;
        var h = parseFloat(document.getElementById('height').value) || 0;
        var neck = parseFloat(document.getElementById('neck').value) || 0;
        var waist = parseFloat(document.getElementById('waist').value) || 0;
        var hip = parseFloat(document.getElementById('hip').value) || 0;
        document.getElementById('hipWrap').style.opacity = (sex === 'female') ? '1' : '0.5';
        if (w <= 0 || h <= 0 || neck <= 0 || waist <= 0) { return; }
        var navy = null;
        if (sex === 'male') {
            if (waist - neck > 0) navy = 495 / (1.0324 - 0.19077 * log10(waist - neck) + 0.15456 * log10(h)) - 450;
        } else {
            if (hip > 0 && (waist + hip - neck) > 0) navy = 495 / (1.29579 - 0.35004 * log10(waist + hip - neck) + 0.22100 * log10(h)) - 450;
        }
        if (navy !== null && navy > 0 && navy < 70) {
            document.getElementById('navyOut').textContent = navy.toFixed(1) + '%';
            document.getElementById('navyCat').textContent = category(sex, navy);
        } else {
            document.getElementById('navyOut').textContent = 'Check measurements';
            document.getElementById('navyCat').textContent = '—';
        }
        var m = h / 100;
        var bmi = w / (m * m);
        var bfBmi = (1.20 * bmi) + (0.23 * age) - (sex === 'male' ? 16.2 : 5.4);
        document.getElementById('bmiOut').textContent = bfBmi.toFixed(1) + '%';
    }
    ['sex','age','weight','height','neck','waist','hip'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc); document.getElementById(id).addEventListener('change', calc); });
    calc();
})();
</script>
@endsection
