@extends('layouts.app')

@section('title', 'Exam Revision Planner - Azlaan Tools')
@section('meta_description', 'Enter your exam date and topics to get a complete day-by-day revision plan. Free online study schedule planner.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Exam Revision Planner</h1>
            <p class="lead text-muted">Enter your exam date and topics — get a complete day-by-day revision plan. How many days are left, how many topics per day, everything is calculated here.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="examName" class="form-label fw-semibold">Exam / subject name</label>
                            <input type="text" class="form-control" id="examName" placeholder="e.g. 10th Class Physics">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="examDate" class="form-label fw-semibold">Exam date</label>
                            <input type="date" class="form-control" id="examDate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="startDate" class="form-label fw-semibold">Preparation starts from</label>
                            <input type="date" class="form-control" id="startDate">
                            <div class="form-text">To start from today, keep today's date.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hoursPerDay" class="form-label fw-semibold">How many hours will you study daily</label>
                            <input type="number" class="form-control" id="hoursPerDay" value="3" min="0.5" max="16" step="0.5">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="topics" class="form-label fw-semibold">Topics (one topic per line)</label>
                            <textarea class="form-control" id="topics" rows="6" placeholder="e.g.&#10;Chapter 1 - Measurements&#10;Chapter 2 - Kinematics&#10;Chapter 3 - Dynamics&#10;Numericals practice&#10;Past papers"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="statsBox" role="alert"></div>
                        <div class="d-flex flex-wrap gap-2 mb-3 no-print">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="downloadBtn">Download Plan (.txt)</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="printBtn">Print Plan</button>
                        </div>
                        <div class="table-responsive" id="printArea">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr><th>Day</th><th>Date</th><th>Topics (with time)</th><th>Total time</th></tr>
                                </thead>
                                <tbody id="planRows"></tbody>
                            </table>
                        </div>
                        <p class="small text-muted">Tip: Keep only light revision one day before the exam — do not touch new topics that day.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your exam name, exam date, and when you will start preparing.</li>
                <li>Tell how many hours you can study each day.</li>
                <li>Write all topics, one per line, in the topics box.</li>
                <li>Click <strong>Make Plan</strong> — a day-by-day schedule appears, ready to print or download.</li>
            </ol>
        </div>
    </div>
</div>
<style>
@media print {
    body.print-plan .no-print { display: none !important; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var examName = document.getElementById('examName');
    var examDate = document.getElementById('examDate');
    var startDate = document.getElementById('startDate');
    var hoursPerDay = document.getElementById('hoursPerDay');
    var topics = document.getElementById('topics');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var statsBox = document.getElementById('statsBox');
    var planRows = document.getElementById('planRows');
    var downloadBtn = document.getElementById('downloadBtn');
    var printBtn = document.getElementById('printBtn');

    startDate.value = new Date().toISOString().slice(0, 10);
    var DAY = 86400000;
    var planText = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtDate(d) {
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return days[d.getDay()] + ' ' + d.getDate() + ' ' + months[d.getMonth()];
    }
    function fmtDur(mins) {
        var h = Math.floor(mins / 60);
        var m = Math.round(mins % 60);
        if (h === 0) return m + ' min';
        return m === 0 ? h + ' hr' : h + ' hr ' + m + ' min';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var topicList = topics.value.split('\n').map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 0; });
        if (topicList.length === 0) { showError('Please enter at least one topic.'); return; }
        if (!examDate.value) { showError('Select the exam date.'); return; }
        if (!startDate.value) { showError('Select the start date.'); return; }
        var hours = parseFloat(hoursPerDay.value);
        if (isNaN(hours) || hours <= 0) { showError('Enter the correct daily study hours.'); return; }

        var exam = new Date(examDate.value + 'T00:00:00');
        var start = new Date(startDate.value + 'T00:00:00');
        var totalDays = Math.floor((exam - start) / DAY);
        if (totalDays < 1) { showError('The exam date must be after the start date.'); return; }
        var planDays = totalDays;
        var dayMinutes = Math.round(hours * 60);

        var days = [];
        for (var i = 0; i < planDays; i++) {
            days.push({ date: new Date(start.getTime() + i * DAY), topics: [] });
        }
        for (var t = 0; t < topicList.length; t++) {
            days[t % planDays].topics.push(topicList[t]);
        }

        planRows.innerHTML = '';
        var lines = [];
        var examTitle = examName.value.trim() || 'Exam';
        lines.push(examTitle + ' - Revision Plan');
        lines.push('Exam date: ' + examDate.value + ' | ' + planDays + ' days | ' + hours + ' hours daily');
        lines.push('');

        days.forEach(function (day, idx) {
            var tr = document.createElement('tr');
            var tdDay = document.createElement('td');
            tdDay.className = 'fw-semibold';
            tdDay.textContent = 'Day ' + (idx + 1);
            var tdDate = document.createElement('td');
            tdDate.textContent = fmtDate(day.date);
            var tdTopics = document.createElement('td');
            var tdTime = document.createElement('td');
            if (day.topics.length === 0) {
                tdTopics.textContent = 'Revision / practice (no new topics)';
                tdTime.textContent = fmtDur(dayMinutes);
            } else {
                var perTopic = dayMinutes / day.topics.length;
                var ul = document.createElement('ul');
                ul.className = 'mb-0 ps-3';
                day.topics.forEach(function (tp) {
                    var li = document.createElement('li');
                    li.textContent = tp + ' (' + fmtDur(perTopic) + ')';
                    ul.appendChild(li);
                });
                tdTopics.appendChild(ul);
                tdTime.textContent = fmtDur(dayMinutes);
            }
            tr.appendChild(tdDay);
            tr.appendChild(tdDate);
            tr.appendChild(tdTopics);
            tr.appendChild(tdTime);
            planRows.appendChild(tr);

            lines.push('Day ' + (idx + 1) + ' - ' + fmtDate(day.date) + ' (' + fmtDur(dayMinutes) + ')');
            if (day.topics.length === 0) {
                lines.push('  - Revision / practice');
            } else {
                var per = dayMinutes / day.topics.length;
                day.topics.forEach(function (tp) { lines.push('  - ' + tp + ' (' + fmtDur(per) + ')'); });
            }
        });
        planText = lines.join('\n');

        var totalHours = Math.round(planDays * hours * 10) / 10;
        statsBox.textContent = 'Plan ready! ' + topicList.length + ' topics, ' + planDays + ' days, ' + hours + ' hours daily — total ' + totalHours + ' hours of preparation. Exam: ' + examDate.value + '.';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth' });
    });

    downloadBtn.addEventListener('click', function () {
        var blob = new Blob([planText], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'revision-plan.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); }, 500);
    });

    printBtn.addEventListener('click', function () {
        document.body.classList.add('print-plan');
        window.print();
    });
    window.addEventListener('afterprint', function () {
        document.body.classList.remove('print-plan');
    });
})();
</script>
@endsection
