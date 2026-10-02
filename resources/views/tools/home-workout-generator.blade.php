@extends('layouts.app')

@section('title', 'Home Workout Generator - Azlaan Tools')
@section('meta_description', 'Generate a no-equipment home workout routine by level, focus and time. Free bodyweight exercise plan.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Home Workout Generator</h1>
            <p class="lead text-muted">Build a full-body workout routine at home with no equipment. Choose your level, focus and time — get a ready exercise plan.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="levelSel" class="form-label fw-semibold">Fitness level</label>
                            <select class="form-select" id="levelSel">
                                <option value="beginner">Beginner (new starter)</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="focusSel" class="form-label fw-semibold">Focus area</label>
                            <select class="form-select" id="focusSel">
                                <option value="full">Full body</option>
                                <option value="upper">Upper body (chest, shoulders, arms)</option>
                                <option value="lower">Lower body (legs, hips)</option>
                                <option value="core">Core (stomach, back)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="timeSel" class="form-label fw-semibold">Time</label>
                            <select class="form-select" id="timeSel">
                                <option value="15">15 minutes</option>
                                <option value="25" selected>25 minutes</option>
                                <option value="40">40 minutes</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="daysSel" class="form-label fw-semibold">Days per week?</label>
                        <select class="form-select" id="daysSel">
                            <option value="3">3 days</option>
                            <option value="4">4 days</option>
                            <option value="5" selected>5 days</option>
                            <option value="6">6 days</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Workout</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" id="planTitle"></h5>
                            <div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="regenBtn">Make Again</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="printBtn">Print Plan</button>
                            </div>
                        </div>
                        <div id="printArea">
                            <div class="alert alert-info py-2" id="estLine"></div>
                            <h6>Warm-up (5 min)</h6>
                            <div id="warmupList" class="mb-3"></div>
                            <h6>Main Workout</h6>
                            <div id="mainList" class="mb-3"></div>
                            <h6>Cool-down (3 min)</h6>
                            <div id="coolList" class="mb-3"></div>
                            <h6>Weekly Schedule</h6>
                            <div id="weekList"></div>
                        </div>
                        <p class="text-muted small mt-3">Disclaimer: This is general fitness guidance, not medical advice. If you have any illness, injury or heart problem, talk to a doctor first. Stop right away if you feel pain.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose your level, focus area and time.</li>
                <li>Press <strong>Make Workout</strong> — you will get a full plan with warm-up, main set and cool-down.</li>
                <li>If you do not like it, use <strong>Make Again</strong> for a new plan, or print it.</li>
            </ol>
        </div>
    </div>
</div>
<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var levelSel = document.getElementById('levelSel');
    var focusSel = document.getElementById('focusSel');
    var timeSel = document.getElementById('timeSel');
    var daysSel = document.getElementById('daysSel');
    var goBtn = document.getElementById('goBtn');
    var regenBtn = document.getElementById('regenBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    var EX = [
        { n: 'Jumping Jacks', f: ['full', 'lower'], l: 1, d: 'Warms up the whole body. Jump lightly, arms up and down.', m: 'Full body' },
        { n: 'Knee Push-ups', f: ['full', 'upper'], l: 1, d: 'Push-ups with knees on the ground. Strengthens chest and arms.', m: 'Chest, arms' },
        { n: 'Bodyweight Squats', f: ['full', 'lower'], l: 1, d: 'Feet shoulder-width apart, do squats. Keep knees behind your toes.', m: 'Legs, glutes' },
        { n: 'Standing Knee Raises', f: ['full', 'core', 'lower'], l: 1, d: 'Standing, lift knees to hip level, alternate sides.', m: 'Core, legs' },
        { n: 'Wall Push-ups', f: ['upper'], l: 1, d: 'Push-ups with hands on a wall. Best for complete beginners.', m: 'Chest, arms' },
        { n: 'Glute Bridges', f: ['lower', 'core'], l: 1, d: 'Lie on your back and lift your hips up, hold 2 seconds.', m: 'Glutes, hamstrings' },
        { n: 'Dead Bug', f: ['core'], l: 1, d: 'On your back, arms and legs in the air, lower them alternately. Keep your back on the floor.', m: 'Core' },
        { n: 'Inchworms', f: ['full', 'upper'], l: 2, d: 'From standing, hands to the floor, walk forward into a plank, then back.', m: 'Full body' },
        { n: 'Standard Push-ups', f: ['full', 'upper'], l: 2, d: 'Straight body, chest close to the floor. Breathe in and out.', m: 'Chest, triceps' },
        { n: 'Reverse Lunges', f: ['full', 'lower'], l: 2, d: 'Step one foot back, knee close to the floor. Alternate legs.', m: 'Legs, glutes' },
        { n: 'Plank Hold', f: ['full', 'core', 'upper'], l: 2, d: 'Hold your body straight on your elbows. Breathe normally.', m: 'Core' },
        { n: 'Mountain Climbers', f: ['full', 'core'], l: 2, d: 'In plank position, drive your knees fast toward your chest.', m: 'Core, cardio' },
        { n: 'Superman Hold', f: ['upper', 'core'], l: 2, d: 'Lie face down, lift your arms and legs up. Strengthens the back.', m: 'Lower back' },
        { n: 'Tricep Dips (chair)', f: ['upper'], l: 2, d: 'Hands on the edge of a chair, lower and lift your body.', m: 'Triceps' },
        { n: 'Bicycle Crunches', f: ['core'], l: 2, d: 'Elbow touches the opposite knee, like cycling. Pull your stomach in.', m: 'Abs, obliques' },
        { n: 'High Knees', f: ['full', 'lower'], l: 2, d: 'Run in place, knees up to hip level. 30 seconds fast.', m: 'Cardio, legs' },
        { n: 'Diamond Push-ups', f: ['upper'], l: 3, d: 'Make a diamond shape with your hands for the push-up. Focus on triceps.', m: 'Triceps, chest' },
        { n: 'Jump Squats', f: ['full', 'lower'], l: 3, d: 'Jump up from the squat, land softly.', m: 'Legs, power' },
        { n: 'Burpees', f: ['full'], l: 3, d: 'Squat, plank, push-up, jump — all in one. A full-body burner.', m: 'Full body' },
        { n: 'Pike Push-ups', f: ['upper'], l: 3, d: 'Hips up, push-up with your head going down. Focus on shoulders.', m: 'Shoulders' },
        { n: 'Single-leg Glute Bridge', f: ['lower'], l: 3, d: 'Glute bridge on one leg. Balance and strength.', m: 'Glutes' },
        { n: 'Plank Shoulder Taps', f: ['core', 'upper'], l: 3, d: 'In plank, tap each shoulder with your hand, keep hips still.', m: 'Core, shoulders' },
        { n: 'Skater Jumps', f: ['lower', 'full'], l: 3, d: 'Jump to the side, land on one leg. Like a skater.', m: 'Legs, balance' },
        { n: 'Hollow Body Hold', f: ['core'], l: 3, d: 'On your back, arms and legs in the air, body curved like a boat.', m: 'Core' }
    ];
    var WARM = [
        { n: 'March in Place', d: '1 minute of light marching, move your arms.' },
        { n: 'Arm Circles', d: '30 seconds forward, 30 seconds back, big circles.' },
        { n: 'Hip Circles + Leg Swings', d: '30 seconds each side. To open up the joints.' },
        { n: 'Torso Twists', d: '30 seconds, twist your waist left and right.' }
    ];
    var COOL = [
        { n: 'Standing Quad Stretch', d: '30 seconds each leg. Hold a chair.' },
        { n: 'Chest Opener', d: 'Clasp your hands behind you, open your chest, 30 seconds.' },
        { n: 'Child Pose', d: '1 minute rest, breathe deeply.' },
        { n: 'Neck Rolls', d: '30 seconds of gentle circles, both sides.' }
    ];
    var LEVEL_NAMES = { beginner: 'Beginner', intermediate: 'Intermediate', advanced: 'Advanced' };
    var FOCUS_NAMES = { full: 'Full Body', upper: 'Upper Body', lower: 'Lower Body', core: 'Core' };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function pick(arr, k) {
        var c = arr.slice(), out = [];
        while (c.length && out.length < k) { out.push(c.splice(Math.floor(Math.random() * c.length), 1)[0]); }
        return out;
    }
    function schemeFor(level) {
        if (level === 'beginner') { return { sets: 2, reps: '8-10 reps', rest: '60 sec' }; }
        if (level === 'intermediate') { return { sets: 3, reps: '12-15 reps', rest: '45 sec' }; }
        return { sets: 4, reps: '15-20 reps', rest: '30 sec' };
    }

    function exCard(e, scheme, idx) {
        var reps = /Hold|Plank|Hollow|Superman|Dead Bug/.test(e.n) ? (levelSel.value === 'beginner' ? '20-30 sec hold' : levelSel.value === 'intermediate' ? '40 sec hold' : '60 sec hold') : scheme.reps;
        return '<div class="card mb-2"><div class="card-body py-2 px-3">' +
            '<div class="d-flex justify-content-between align-items-center">' +
            '<strong>' + (idx + 1) + '. ' + e.n + '</strong>' +
            '<span class="badge bg-primary">' + scheme.sets + ' sets x ' + reps + '</span></div>' +
            '<div class="small text-muted mt-1">' + e.d + '</div>' +
            '<div class="small mt-1"><span class="badge bg-light text-dark border">' + e.m + '</span> ' +
            '<span class="text-muted">Rest: ' + scheme.rest + ' between sets</span></div>' +
            '</div></div>';
    }
    function simpleCard(e) {
        return '<div class="card mb-2"><div class="card-body py-2 px-3"><strong>' + e.n + '</strong><div class="small text-muted">' + e.d + '</div></div></div>';
    }

    function build() {
        hideError();
        var level = levelSel.value, focus = focusSel.value;
        var minutes = parseInt(timeSel.value, 10);
        var days = parseInt(daysSel.value, 10);
        var lv = level === 'beginner' ? 1 : level === 'intermediate' ? 2 : 3;
        var pool = EX.filter(function (e) { return e.f.indexOf(focus) !== -1 && e.l <= lv; });
        if (pool.length < 3) { pool = EX.filter(function (e) { return e.l <= lv; }); }
        var mainCount = minutes <= 15 ? 4 : minutes <= 25 ? 6 : 8;
        var scheme = schemeFor(level);
        var main = pick(pool, Math.min(mainCount, pool.length));
        var warm = pick(WARM, 3), cool = pick(COOL, 3);

        document.getElementById('planTitle').textContent =
            LEVEL_NAMES[level] + ' ' + FOCUS_NAMES[focus] + ' Plan (' + minutes + ' min)';
        var perEx = scheme.sets * 2 + 1;
        var est = 8 + main.length * perEx;
        document.getElementById('estLine').textContent =
            'Estimated time: ' + est + ' minutes • Level: ' + LEVEL_NAMES[level] + ' • ' + main.length + ' exercises • ' + scheme.rest + ' rest between sets';
        document.getElementById('warmupList').innerHTML = warm.map(simpleCard).join('');
        document.getElementById('mainList').innerHTML = main.map(function (e, i) { return exCard(e, scheme, i); }).join('');
        document.getElementById('coolList').innerHTML = cool.map(simpleCard).join('');

        var focuses = ['full', 'upper', 'lower', 'core'];
        var weekHtml = '<div class="row">';
        var dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        for (var d = 0; d < 7; d++) {
            var label, cls;
            if (d < days) {
                var f = focus === 'full' ? focuses[d % 4] : (d % 2 === 0 ? focus : 'full');
                label = 'Workout: ' + FOCUS_NAMES[f];
                cls = 'bg-success-subtle border-success';
            } else { label = 'Rest day'; cls = 'bg-light'; }
            weekHtml += '<div class="col-6 col-md-3 mb-2"><div class="card ' + cls + '"><div class="card-body p-2 text-center">' +
                '<div class="fw-semibold small">' + dayNames[d] + '</div><div class="small">' + label + '</div></div></div></div>';
        }
        document.getElementById('weekList').innerHTML = weekHtml + '</div>';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    goBtn.addEventListener('click', build);
    regenBtn.addEventListener('click', build);
    printBtn.addEventListener('click', function () { window.print(); });
})();
</script>
@endsection
