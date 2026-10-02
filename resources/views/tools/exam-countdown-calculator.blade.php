@extends('layouts.app')

@section('title', 'Exam Countdown Calculator - Azlaan Tools')
@section('meta_description', 'Find out how many days are left until your exam. Free online exam countdown calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Exam Countdown Calculator</h1>
            <p class="lead text-muted">How many days are left until your exam? Enter the date and get a live countdown.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="examName" class="form-label fw-semibold">Exam name (optional)</label>
                        <input type="text" class="form-control" id="examName" placeholder="e.g. Matric Board Exam">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="examDate" class="form-label fw-semibold">Exam date</label>
                            <input type="date" class="form-control" id="examDate">
                        </div>
                        <div class="col-6">
                            <label for="prepStart" class="form-label fw-semibold">When did you start preparing? (optional)</label>
                            <input type="date" class="form-control" id="prepStart">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">View Countdown</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the exam date (entering the name is optional).</li>
                <li>If you want, also enter the date you started preparing — you will get a progress bar.</li>
                <li>Press "View Countdown" — you will get days, hours and tips.</li>
            </ol>
            <p class="text-muted small">The countdown updates every second. Dates follow your device local time.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var examName = document.getElementById('examName');
    var examDate = document.getElementById('examDate');
    var prepStart = document.getElementById('prepStart');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var timer = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
        if (timer) { clearInterval(timer); timer = null; }
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function studyTip(daysLeft) {
        if (daysLeft < 0) return 'The exam has passed — focus on preparing for the next paper.';
        if (daysLeft === 0) return 'The exam is today! Stay calm, take a quick look at your notes and reach on time.';
        if (daysLeft <= 7) return 'Last week: only revise, do not start new topics. Solve past papers.';
        if (daysLeft <= 30) return 'Last month: make a daily timetable, give more time to weak chapters.';
        if (daysLeft <= 90) return 'You have time: make a chapter-wise plan for every subject and take weekly tests.';
        return 'You have plenty of time: studying a little every day is better than cramming at the end. Stay consistent.';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (timer) { clearInterval(timer); timer = null; }
        var dateVal = examDate.value;
        if (!dateVal) { showError('Please select your exam date.'); return; }
        var target = new Date(dateVal + 'T00:00:00');
        if (isNaN(target.getTime())) { showError('The date was not understood. Please select it again.'); return; }
        var name = examName.value.trim() || 'Your Exam';
        var safeName = name.replace(/</g, '&lt;');

        var startVal = prepStart.value;
        var startDate = startVal ? new Date(startVal + 'T00:00:00') : null;
        if (startVal && isNaN(startDate.getTime())) startDate = null;

        results.classList.remove('d-none');

        function render() {
            var now = new Date();
            var diff = target.getTime() - now.getTime();
            var html = '<h4 class="mb-3">' + safeName + '</h4>';
            if (diff < 0) {
                html += '<div class="alert alert-secondary text-center"><strong>The exam date has passed.</strong><br>' + studyTip(-1) + '</div>';
                results.innerHTML = html;
                clearInterval(timer); timer = null;
                return;
            }
            var days = Math.floor(diff / 86400000);
            var hours = Math.floor((diff % 86400000) / 3600000);
            var mins = Math.floor((diff % 3600000) / 60000);
            var secs = Math.floor((diff % 60000) / 1000);

            html += '<div class="row g-2 text-center mb-3">';
            var units = [[days, 'Days'], [hours, 'Hours'], [mins, 'Minutes'], [secs, 'Seconds']];
            for (var i = 0; i < units.length; i++) {
                html += '<div class="col-3"><div class="border rounded p-2 bg-light"><div class="h3 fw-bold mb-0">' + pad(units[i][0]) + '</div><div class="small text-muted">' + units[i][1] + '</div></div></div>';
            }
            html += '</div>';

            if (startDate && startDate < target) {
                var totalSpan = target.getTime() - startDate.getTime();
                var elapsed = now.getTime() - startDate.getTime();
                var pct = Math.max(0, Math.min(100, Math.round((elapsed / totalSpan) * 100)));
                html += '<label class="form-label fw-semibold small">Preparation progress: ' + pct + '% time has passed</label>';
                html += '<div class="progress mb-3"><div class="progress-bar" style="width:' + pct + '%">' + pct + '%</div></div>';
            }

            var weeks = Math.floor(days / 7);
            var remDays = days % 7;
            html += '<div class="alert alert-light border">That is about <strong>' + weeks + ' weeks' + (remDays ? ' and ' + remDays + ' days' : '') + '</strong> left.</div>';
            html += '<div class="alert alert-info mb-0"><strong>Tip:</strong> ' + studyTip(days) + '</div>';
            results.innerHTML = html;
        }

        render();
        timer = setInterval(render, 1000);
    });
})();
</script>
@endsection
