@extends('layouts.app')

@section('title', 'Calorie Deficit Calculator — Free Online Tool')
@section('meta_description', 'Find the daily calorie deficit you need for safe and steady weekly weight loss')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Calorie Deficit Calculator</h1>
            <p class="lead small text-muted">Work out your maintenance calories with the Mifflin-St Jeor formula, then see the daily target for steady weekly weight loss.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male">Male</option><option value="female">Female</option></select></div>                    <div class="mb-3"><label class="form-label" for="age">Age (years)</label><input type="number" class="form-control" id="age" value="30" step="any"></div>                    <div class="mb-3"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="80" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>                    <div class="mb-3"><label class="form-label" for="activity">Activity level</label><select class="form-select" id="activity"><option value="1.2">Sedentary — little or no exercise (x1.2)</option><option value="1.375">Light — exercise 1 to 3 days a week (x1.375)</option><option value="1.55">Moderate — exercise 3 to 5 days a week (x1.55)</option><option value="1.725">Active — exercise 6 to 7 days a week (x1.725)</option><option value="1.9">Athlete — very hard exercise or physical job (x1.9)</option></select></div>                    <div class="mb-3"><label class="form-label" for="loss">Target weekly loss</label><select class="form-select" id="loss"><option value="0.25">0.25 kg per week</option><option value="0.5">0.5 kg per week</option><option value="0.75">0.75 kg per week</option><option value="1">1 kg per week</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your sex, age, weight and height.</li><li>Choose your activity level.</li><li>Choose a weekly loss target and read your daily calorie target.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Maintenance uses the Mifflin-St Jeor formula. The deficit uses the approximation that 1 kg of body fat stores about 7,700 kcal. Very low calorie targets are flagged because intakes below about 1,200 kcal (women) or 1,500 kcal (men) are hard to keep nutritionally adequate without supervision. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var sex = document.getElementById("sex").value;
        var age = num("age"), w = num("weight"), h = num("height"), act = num("activity"), loss = num("loss");
        if ([age, w, h].some(isNaN) || age <= 0 || w <= 0 || h <= 0) { out("Please enter valid age, weight and height values."); return; }
        var bmr = 10 * w + 6.25 * h - 5 * age + (sex === "male" ? 5 : -161);
        var tdee = bmr * act;
        var deficit = loss * 7700 / 7;
        var target = tdee - deficit;
        var warn = target < 1200 ? "<br><strong>Warning:</strong> this target is very low. A slower loss rate is usually safer and easier to sustain." : "";
        out("<strong>Maintenance (TDEE):</strong> about " + fmt(tdee, 0) + " kcal per day<br><strong>Daily deficit needed:</strong> " + fmt(deficit, 0) + " kcal<br><strong>Daily target:</strong> about " + fmt(target, 0) + " kcal per day" + warn);
    }
    bind(["sex", "age", "weight", "height", "activity", "loss"], calc); calc();
})();
</script>
@endsection
