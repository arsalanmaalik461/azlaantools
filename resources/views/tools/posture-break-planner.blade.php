@extends('layouts.app')

@section('title', 'Posture Break Planner - Azlaan Tools')
@section('meta_description', 'Plan stretch and posture breaks during desk work to protect your back, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Posture Break Planner</h1>
            <p class="lead text-muted">Plan stretch and posture breaks during desk work. A simple routine to protect against back pain and a stiff neck.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="workStart" class="form-label fw-semibold">Work starts at</label>
                            <input type="time" class="form-control" id="workStart" value="09:00">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="workEnd" class="form-label fw-semibold">Work ends at</label>
                            <input type="time" class="form-control" id="workEnd" value="17:00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="interval" class="form-label fw-semibold">Break every (minutes)</label>
                            <input type="number" class="form-control" id="interval" min="20" max="120" value="50">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="breakLen" class="form-label fw-semibold">Break length (minutes)</label>
                            <input type="number" class="form-control" id="breakLen" min="1" max="30" value="5">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stretch activities (rotate automatically)</label>
                        <div id="activityList">
                            <div class="form-check"><input class="form-check-input act" type="checkbox" value="Neck rolls (10 each side)" checked id="a1"><label class="form-check-label" for="a1">Neck rolls (10 each side)</label></div>
                            <div class="form-check"><input class="form-check-input act" type="checkbox" value="Shoulder shrugs and rolls" checked id="a2"><label class="form-check-label" for="a2">Shoulder shrugs and rolls</label></div>
                            <div class="form-check"><input class="form-check-input act" type="checkbox" value="Stand and touch toes / back stretch" checked id="a3"><label class="form-check-label" for="a3">Stand up and back stretch</label></div>
                            <div class="form-check"><input class="form-check-input act" type="checkbox" value="20-20-20 eye rest (look 20ft away, 20 sec)" checked id="a4"><label class="form-check-label" for="a4">20-20-20 eye rest (look 20 ft away for 20 seconds)</label></div>
                            <div class="form-check"><input class="form-check-input act" type="checkbox" value="Walk and drink water" checked id="a5"><label class="form-check-label" for="a5">Walk and drink water</label></div>
                            <div class="form-check"><input class="form-check-input act" type="checkbox" value="Seated spinal twist (each side)" id="a6"><label class="form-check-label" for="a6">Seated spinal twist (each side)</label></div>
                            <div class="form-check"><input class="form-check-input act" type="checkbox" value="Wrist and ankle circles" id="a7"><label class="form-check-label" for="a7">Wrist and ankle circles</label></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Break Schedule</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Your break schedule</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light"><tr><th>#</th><th>Break time</th><th>Activity</th></tr></thead>
                                <tbody id="scheduleBody"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-success small mb-0" id="summaryBox"></div>
                        <button type="button" class="btn btn-outline-primary w-100 mt-2" id="printBtn">Print Schedule</button>
                    </div>
                    <p class="small text-muted mt-3 mb-0">This is an estimate only, not a treatment. If the pain continues, see a doctor.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your work start and end times.</li>
                <li>Select the break interval and activities.</li>
                <li>Press "Generate Break Schedule" — your full-day routine is ready.</li>
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
    function toMin(t) {
        var p = t.split(':');
        return parseInt(p[0], 10) * 60 + parseInt(p[1], 10);
    }
    function toTime(m) {
        var h = Math.floor(m / 60) % 24, mm = m % 60;
        var ap = h >= 12 ? 'PM' : 'AM';
        var hh = h % 12; if (hh === 0) hh = 12;
        return hh + ':' + (mm < 10 ? '0' : '') + mm + ' ' + ap;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var startRaw = document.getElementById('workStart').value;
        var endRaw = document.getElementById('workEnd').value;
        var interval = parseInt(document.getElementById('interval').value, 10);
        var breakLen = parseInt(document.getElementById('breakLen').value, 10);

        if (!startRaw || !endRaw) { showError('Please enter work start and end times.'); return; }
        var start = toMin(startRaw), end = toMin(endRaw);
        if (end <= start) end += 24 * 60; // night shift
        if (isNaN(interval) || interval < 20 || interval > 120) { showError('Keep the break interval between 20 and 120 minutes.'); return; }
        if (isNaN(breakLen) || breakLen < 1 || breakLen > 30) { showError('Keep the break length between 1 and 30 minutes.'); return; }

        var acts = [];
        var boxes = document.querySelectorAll('.act:checked');
        for (var i = 0; i < boxes.length; i++) acts.push(boxes[i].value);
        if (acts.length === 0) { showError('Please select at least one activity.'); return; }

        var html = '', t = start + interval, n = 0;
        while (t < end) {
            n++;
            var act = acts[(n - 1) % acts.length];
            html += '<tr><td>' + n + '</td><td class="fw-semibold">' + toTime(t) +
                ' <span class="text-muted small">(' + breakLen + ' min)</span></td><td>' + act + '</td></tr>';
            t += interval;
        }
        if (n === 0) { showError('No break fits in this short time — reduce the interval or make the day longer.'); return; }

        document.getElementById('scheduleBody').innerHTML = html;
        var totalMin = n * breakLen;
        document.getElementById('summaryBox').textContent =
            'Total ' + n + ' breaks, ' + totalMin + ' minutes of movement — a ' + breakLen + '-minute break every ' + interval + ' minutes. ' +
            'Activities rotate automatically with every break.';
        results.classList.remove('d-none');
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        var rows = document.getElementById('scheduleBody').innerHTML;
        var w = window.open('', '_blank');
        w.document.write('<!DOCTYPE html><html><head><title>Posture Break Schedule</title>' +
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>' +
            '<body><div class="container py-4"><h3>My Posture Break Schedule</h3>' +
            '<table class="table table-bordered"><thead><tr><th>#</th><th>Break time</th><th>Activity</th></tr></thead><tbody>' +
            rows + '</tbody></table></div>' +
            '<script>window.onload=function(){window.print();}<\/script></body></html>');
        w.document.close();
    });
})();
</script>
@endsection
