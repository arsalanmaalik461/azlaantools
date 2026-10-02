@extends('layouts.app')

@section('title', 'BMI Calculator — Azlaan Tools')
@section('meta_description', 'Free BMI calculator. Enter height (cm or ft/in) and weight in kg to get your Body Mass Index, category and healthy weight range.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">BMI Calculator</h1>
            <p class="text-muted mb-4">Body Mass Index (BMI) is the ratio of your weight and height. Find your BMI and see which category you are in.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Height Unit</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="unitToggle" id="unitCm" checked>
                            <label class="btn btn-outline-primary" for="unitCm">Centimetres (cm)</label>
                            <input type="radio" class="btn-check" name="unitToggle" id="unitFt">
                            <label class="btn btn-outline-primary" for="unitFt">Feet / Inches</label>
                        </div>
                    </div>

                    <div id="cmFields" class="mb-3">
                        <label for="heightCm" class="form-label">Height (cm)</label>
                        <input type="number" class="form-control" id="heightCm" placeholder="e.g. 170" step="any">
                    </div>
                    <div id="ftFields" class="row g-2 mb-3 d-none">
                        <div class="col-6">
                            <label for="heightFt" class="form-label">Feet</label>
                            <input type="number" class="form-control" id="heightFt" placeholder="e.g. 5" step="any">
                        </div>
                        <div class="col-6">
                            <label for="heightIn" class="form-label">Inches</label>
                            <input type="number" class="form-control" id="heightIn" placeholder="e.g. 7" step="any">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="weightKg" class="form-label">Weight (kg)</label>
                        <input type="number" class="form-control" id="weightKg" placeholder="e.g. 70" step="any">
                    </div>

                    <div id="bmiResult" class="d-none">
                        <div class="text-center border rounded p-3 mb-3">
                            <div class="text-muted small">Your BMI</div>
                            <div class="display-5 fw-bold" id="bmiValue">—</div>
                            <span class="badge fs-6" id="bmiCategory">—</span>
                        </div>
                        <div class="progress mb-3" style="height: 14px;">
                            <div class="progress-bar bg-info" style="width:25%">Under</div>
                            <div class="progress-bar bg-success" style="width:25%">Normal</div>
                            <div class="progress-bar bg-warning" style="width:25%">Over</div>
                            <div class="progress-bar bg-danger" style="width:25%">Obese</div>
                        </div>
                        <p class="mb-1">Healthy weight range for your height: <strong id="healthyRange">—</strong></p>
                        <p class="small text-muted mb-0">Categories: Underweight &lt; 18.5 &nbsp;|&nbsp; Normal 18.5–24.9 &nbsp;|&nbsp; Overweight 25–29.9 &nbsp;|&nbsp; Obese 30+. BMI is a general guide — athletes and people with special conditions should ask a doctor.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Choose the height unit — cm or feet/inches.</li>
                        <li>Enter your height and weight (kg).</li>
                        <li>Your BMI, category and healthy weight range will show right away.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var unitCm = document.getElementById('unitCm'), unitFt = document.getElementById('unitFt');
function toggleUnit() {
    document.getElementById('cmFields').classList.toggle('d-none', !unitCm.checked);
    document.getElementById('ftFields').classList.toggle('d-none', unitCm.checked);
    calc();
}
unitCm.addEventListener('change', toggleUnit);
unitFt.addEventListener('change', toggleUnit);

function getHeightCm() {
    if (unitCm.checked) return parseFloat(document.getElementById('heightCm').value) || 0;
    var ft = parseFloat(document.getElementById('heightFt').value) || 0;
    var inch = parseFloat(document.getElementById('heightIn').value) || 0;
    return ((ft * 12) + inch) * 2.54;
}
function calc() {
    var hCm = getHeightCm();
    var w = parseFloat(document.getElementById('weightKg').value) || 0;
    var box = document.getElementById('bmiResult');
    if (hCm <= 0 || w <= 0) { box.classList.add('d-none'); return; }
    var m = hCm / 100;
    var bmi = w / (m * m);
    var cat, cls;
    if (bmi < 18.5) { cat = 'Underweight'; cls = 'bg-info text-dark'; }
    else if (bmi < 25) { cat = 'Normal'; cls = 'bg-success'; }
    else if (bmi < 30) { cat = 'Overweight'; cls = 'bg-warning text-dark'; }
    else { cat = 'Obese'; cls = 'bg-danger'; }
    document.getElementById('bmiValue').textContent = bmi.toFixed(1);
    var badge = document.getElementById('bmiCategory');
    badge.textContent = cat;
    badge.className = 'badge fs-6 ' + cls;
    var low = 18.5 * m * m, high = 24.9 * m * m;
    document.getElementById('healthyRange').textContent = low.toFixed(1) + ' kg — ' + high.toFixed(1) + ' kg';
    box.classList.remove('d-none');
}
['heightCm','heightFt','heightIn','weightKg'].forEach(function(id){
    document.getElementById(id).addEventListener('input', calc);
});
</script>
@endsection
