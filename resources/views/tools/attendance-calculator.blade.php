@extends('layouts.app')

@section('title', 'Attendance Calculator - Classes Needed or Can Miss | Azlaan Tools')
@section('meta_description', 'Free attendance calculator: check current attendance percentage, how many classes to attend to reach 75, 80, 85 or 90 percent, and how many you can safely miss. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Attendance Calculator</h1>
            <p class="lead text-muted">Find your attendance percentage, and see how many more classes you need to reach your target — or how many you can safely miss.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="attended" class="form-label fw-semibold">Classes Attended</label>
                            <input type="number" class="form-control" id="attended" min="0" step="1" placeholder="e.g. 35">
                        </div>
                        <div class="col-md-4">
                            <label for="totalClasses" class="form-label fw-semibold">Total Classes Held</label>
                            <input type="number" class="form-control" id="totalClasses" min="0" step="1" placeholder="e.g. 50">
                        </div>
                        <div class="col-md-4">
                            <label for="targetPct" class="form-label fw-semibold">Target Attendance (%)</label>
                            <select class="form-select" id="targetPct">
                                <option value="75" selected>75%</option>
                                <option value="80">80%</option>
                                <option value="85">85%</option>
                                <option value="90">90%</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="attMsg"></div>

                    <div class="border rounded p-3 bg-light text-center mt-3">
                        <div class="text-muted small">Current Attendance</div>
                        <div class="fs-2 fw-bold text-primary" id="currentOut">—</div>
                        <div class="small text-muted" id="currentDetail"></div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><div class="alert alert-success mb-0 h-100" id="needBox">Select a target and enter attended and total classes.</div></div>
                        <div class="col-md-6"><div class="alert alert-info mb-0 h-100" id="missBox">How many classes you can miss will show here.</div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the classes attended so far and the total classes held.</li>
                        <li>Select your target — 75%, 80%, 85% or 90%.</li>
                        <li>You will get two clear answers with the current percentage: how many classes in a row you must attend to reach the target, and how many you can miss while staying above the target.</li>
                        <li>The result updates live as you change the values.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function calculate() {
        var msg = document.getElementById('attMsg');
        var attended = parseFloat(document.getElementById('attended').value);
        var total = parseFloat(document.getElementById('totalClasses').value);
        var target = parseFloat(document.getElementById('targetPct').value);
        var needBox = document.getElementById('needBox');
        var missBox = document.getElementById('missBox');
        if (isNaN(attended) || isNaN(total)) {
            document.getElementById('currentOut').textContent = '—'; document.getElementById('currentDetail').textContent = ''; msg.classList.add('d-none'); return;
        }
        if (total <= 0 || attended < 0 || attended > total) {
            msg.textContent = 'Attended classes cannot be more than total, and total must be greater than 0.'; msg.classList.remove('d-none');
            document.getElementById('currentOut').textContent = '—'; document.getElementById('currentDetail').textContent = ''; return;
        }
        msg.classList.add('d-none');
        var current = (attended / total) * 100;
        document.getElementById('currentOut').textContent = current.toFixed(2) + '%';
        document.getElementById('currentDetail').textContent = attended + ' out of ' + total + ' classes | Target: ' + target + '%';
        var t = target / 100;
        if (current >= target) {
            needBox.textContent = 'Good news! You are already at or above the ' + target + '% target (' + current.toFixed(2) + '%). You do not need to attend more classes for this target.';
            var canMiss = Math.floor((attended / t) - total);
            if (canMiss < 0) canMiss = 0;
            if (canMiss === 0) {
                missBox.textContent = 'But the margin is very small: missing even one class can take your attendance below ' + target + '%. Do not miss the next classes.';
            } else {
                var pctAfter = (attended / (total + canMiss)) * 100;
                missBox.textContent = 'You can miss ' + canMiss + ' class(es) and still stay at or above ' + target + '%. Attendance after missing ' + canMiss + ': ' + pctAfter.toFixed(2) + '%.';
            }
        } else {
            var need = Math.ceil(((t * total) - attended) / (1 - t));
            if (need < 0) need = 0;
            var pctAfterNeed = ((attended + need) / (total + need)) * 100;
            needBox.textContent = 'To reach the ' + target + '% target you must attend ' + need + ' more class(es) in a row (without missing any). Attendance after that: ' + pctAfterNeed.toFixed(2) + '%.';
            missBox.textContent = 'You are currently below the target (' + current.toFixed(2) + '% vs ' + target + '%), so missing any class is not safe right now — missing will lower the percentage further.';
        }
    }
    ['attended', 'totalClasses', 'targetPct'].forEach(function (id) {
        var el = document.getElementById(id);
        el.addEventListener('input', calculate); el.addEventListener('change', calculate);
    });
    calculate();
})();
</script>
@endsection
