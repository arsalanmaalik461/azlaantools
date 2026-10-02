@extends('layouts.app')
@section('title', '30 Day Fitness Challenge Generator - Azlaan Tools')
@section('meta_description', 'Make a 30-day fitness challenge plan for your level. Daily exercise plan, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">30 Day Fitness Challenge Generator</h1>
            <p class="lead text-muted">Choose your level and focus — a 30-day daily exercise plan is ready. Reps will increase a little every week. No equipment needed.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="fitLevel" class="form-label fw-semibold">Your level</label>
                            <select class="form-select" id="fitLevel">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate" selected>Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fitFocus" class="form-label fw-semibold">Focus area</label>
                            <select class="form-select" id="fitFocus">
                                <option value="full">Full Body</option>
                                <option value="upper">Upper Body</option>
                                <option value="core">Core / Abs</option>
                                <option value="lower">Lower Body</option>
                                <option value="cardio">Cardio / Fat Burn</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fitTime" class="form-label fw-semibold">Daily time</label>
                            <select class="form-select" id="fitTime">
                                <option value="15">~15 minutes</option>
                                <option value="25" selected>~25 minutes</option>
                                <option value="40">~40 minutes</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate My 30-Day Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="fitInfo" role="alert"></div>
                        <div id="planOut"></div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-success flex-fill" id="fitDownload">Download Plan (TXT)</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="fitPrint">Print</button>
                        </div>
                        <p class="small text-muted mt-2 mb-0">This is only an estimate, not a treatment. If you feel pain or dizziness, stop and see a doctor.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your fitness level, focus and daily time.</li>
                <li>Press Generate — a 30-day plan is made (rest on every 7th day).</li>
                <li>Download or print the plan and follow it daily.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    var EX = {
        beginner: {
            full: ['Wall Push-ups', 'Chair Squats', 'Standing Knee Lifts', 'Arm Circles', 'Calf Raises', 'Standing Side Bends', 'Marching in Place', 'Glute Bridges'],
            upper: ['Wall Push-ups', 'Arm Circles', 'Chair Dips (light)', 'Standing Shoulder Press (no weight)', 'Arm Raises', 'Wall Angels', 'Tricep Kickbacks (no weight)', 'Prayer Press'],
            core: ['Standing Side Bends', 'Seated Knee Tucks', 'Dead Bug (light)', 'Standing Oblique Crunch', 'Pelvic Tilts', 'Bird Dog', 'Seated Russian Twists', 'Gentle Plank (knees down)'],
            lower: ['Chair Squats', 'Standing Leg Lifts', 'Calf Raises', 'Side Leg Raises', 'Glute Bridges', 'Toe Taps', 'Mini Lunges', 'Heel Slides'],
            cardio: ['Marching in Place', 'Step Touches', 'Gentle Jumping Jacks', 'High Knee Walk', 'Side Steps', 'Toe Taps', 'Slow Burpees (no jump)', 'Dancing / Free Move']
        },
        intermediate: {
            full: ['Push-ups', 'Bodyweight Squats', 'Lunges', 'Plank', 'Jumping Jacks', 'Mountain Climbers', 'Glute Bridges', 'Superman Hold'],
            upper: ['Push-ups', 'Tricep Dips', 'Pike Push-ups', 'Plank Shoulder Taps', 'Superman Hold', 'Reverse Snow Angels', 'Incline Push-ups', 'Arm Circles (fast)'],
            core: ['Plank', 'Bicycle Crunches', 'Leg Raises', 'Russian Twists', 'Dead Bug', 'Mountain Climbers', 'Side Plank', 'Flutter Kicks'],
            lower: ['Bodyweight Squats', 'Lunges', 'Glute Bridges', 'Calf Raises', 'Step-ups', 'Wall Sit', 'Sumo Squats', 'Donkey Kicks'],
            cardio: ['Jumping Jacks', 'High Knees', 'Burpees', 'Mountain Climbers', 'Skater Hops', 'Jump Squats', 'Fast Feet', 'Sprint in Place']
        },
        advanced: {
            full: ['Burpees', 'Jump Squats', 'Diamond Push-ups', 'Pull-up Bar Rows (door bar)', 'Pistol Squat (assisted)', 'Plank to Push-up', 'Box Jumps (low step)', 'V-ups'],
            upper: ['Diamond Push-ups', 'Wide Push-ups', 'Tricep Dips (feet up)', 'Pike Push-ups', 'Plank Up-Downs', 'Archer Push-ups', 'Superman Pulls', 'Decline Push-ups'],
            core: ['V-ups', 'Hanging Knee Raises (bar)', 'Plank (long hold)', 'Russian Twists (weighted)', 'Hollow Hold', 'Mountain Climbers (fast)', 'Side Plank with Dip', 'Leg Raises'],
            lower: ['Jump Squats', 'Bulgarian Split Squats', 'Single-leg Glute Bridge', 'Box Jumps (low step)', 'Wall Sit (long)', 'Curtsy Lunges', 'Calf Raise (single leg)', 'Nordic Curl (assisted)'],
            cardio: ['Burpees', 'Jump Squats', 'High Knees (sprint)', 'Mountain Climbers (fast)', 'Box Jumps', 'Skaters (fast)', 'Tuck Jumps', 'Sprint Intervals']
        }
    };
    var BASE = {
        beginner: { sets: 2, reps: '10-12', hold: '20 sec' },
        intermediate: { sets: 3, reps: '12-15', hold: '30 sec' },
        advanced: { sets: 4, reps: '15-20', hold: '45 sec' }
    };
    var PLANKISH = /plank|wall sit|hold|superman|hollow/i;

    var lastPlanText = '';

    goBtn.addEventListener('click', function () {
        hideError();
        var level = document.getElementById('fitLevel').value;
        var focus = document.getElementById('fitFocus').value;
        var minutes = parseInt(document.getElementById('fitTime').value, 10);
        var pool = (EX[level] && EX[level][focus]) ? EX[level][focus] : EX[level].full;
        var base = BASE[level];
        var perDay = minutes <= 15 ? 4 : (minutes <= 25 ? 5 : 7);

        var weekNames = ['Week 1 — Start', 'Week 2 — Pick Up the Pace', 'Week 3 — Full Effort', 'Week 4 — Final Push'];
        var html = '', txt = '30-DAY FITNESS CHALLENGE\nLevel: ' + level + ' | Focus: ' + focus + ' | Daily: ~' + minutes + ' min\n\n';
        for (var d = 1; d <= 30; d++) {
            var week = Math.ceil(d / 7);
            var dayInWeek = ((d - 1) % 7) + 1;
            var boost = week - 1;
            if (dayInWeek === 7) {
                html += '<div class="border rounded p-3 mb-2 bg-light"><strong>Day ' + d + ' — REST</strong><br><span class="small text-muted">Rest, take a light walk or stretch. Drink more water.</span></div>';
                txt += 'Day ' + d + ': REST (light walk / stretching)\n\n';
                continue;
            }
            var offset = ((d - 1) * 2) % pool.length;
            var dayEx = [];
            for (var e = 0; e < perDay; e++) dayEx.push(pool[(offset + e) % pool.length]);
            var rows = dayEx.map(function (name) {
                var sets = base.sets + (boost >= 3 ? 1 : 0);
                var target = PLANKISH.test(name) ? (parseInt(base.hold, 10) + boost * 10) + ' sec hold' : base.reps + ' reps';
                return { name: name, sets: sets, target: target };
            });
            html += '<div class="border rounded p-3 mb-2"><strong>Day ' + d + ' (' + weekNames[Math.min(week - 1, 3)] + ')</strong><br>' +
                '<span class="small text-muted">Warm-up: 3 min marching + arm circles</span>' +
                '<ul class="mb-1 mt-1 small">' + rows.map(function (r) {
                    return '<li>' + esc(r.name) + ' — ' + r.sets + ' sets x ' + esc(r.target) + '</li>';
                }).join('') + '</ul>' +
                '<span class="small text-muted">Cool-down: 2 min stretching</span></div>';
            txt += 'Day ' + d + ':\n  Warm-up: 3 min marching\n' +
                rows.map(function (r) { return '  - ' + r.name + ': ' + r.sets + ' sets x ' + r.target; }).join('\n') +
                '\n  Cool-down: 2 min stretching\n\n';
        }
        document.getElementById('planOut').innerHTML = html;
        document.getElementById('fitInfo').textContent = 'Your 30-day plan is ready (' + level + ' / ' + focus + '). Every 7th day is rest.';
        lastPlanText = txt;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('fitDownload').addEventListener('click', function () {
        if (!lastPlanText) return;
        var blob = new Blob([lastPlanText], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = '30-day-fitness-plan.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    });
    document.getElementById('fitPrint').addEventListener('click', function () { window.print(); });
})();
</script>
@endsection
