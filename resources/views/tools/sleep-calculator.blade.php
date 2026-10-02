@extends('layouts.app')
@section('title', 'Sleep Calculator — Azlaan Tools')
@section('meta_description', 'Free sleep cycle calculator. Find the best time to sleep or wake up based on 90-minute sleep cycles so you wake up fresh.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Sleep Calculator</h1>
            <p class="lead text-muted">Sleep works in 90-minute cycles. Wake up between cycles — not in the middle — and you will feel fresh instead of groggy.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item"><button class="nav-link active" id="tabWake" type="button">I want to wake up at…</button></li>
                    <li class="nav-item"><button class="nav-link" id="tabSleep" type="button">I am sleeping at…</button></li>
                </ul>
                <div id="modeWake">
                    <label class="form-label" for="wakeTime">Wake-up time</label>
                    <input type="time" class="form-control form-control-lg" id="wakeTime" value="07:00">
                    <p class="form-label mt-3">Go to sleep at one of these times:</p>
                    <div class="row g-2" id="sleepTimes"></div>
                </div>
                <div id="modeSleep" class="d-none">
                    <label class="form-label" for="sleepTime">Sleep time</label>
                    <input type="time" class="form-control form-control-lg" id="sleepTime" value="23:00">
                    <button type="button" class="btn btn-outline-primary mt-2" id="nowBtn">Use current time (sleep now)</button>
                    <p class="form-label mt-3">Set your alarm for one of these times:</p>
                    <div class="row g-2" id="wakeTimes"></div>
                </div>
                <p class="small text-muted mt-3 mb-0">Times include about 15 minutes to fall asleep. 6 cycles (9 hours) is ideal for most adults; 5 cycles (7.5 hours) is a good minimum. This is an estimate for information only — not medical advice.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Pick a mode: plan bedtime from a wake-up time, or plan wake-up from a bedtime.</li><li>Choose one of the big time cards — the green one is recommended.</li><li>Keep a regular schedule for the best results.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function parseTime(v) { var p = v.split(':'); return (parseInt(p[0], 10) * 60) + parseInt(p[1], 10); }
    function fmt(mins) {
        var m = ((mins % 1440) + 1440) % 1440;
        var h = Math.floor(m / 60), mm = m % 60;
        var ap = h >= 12 ? 'PM' : 'AM';
        var h12 = h % 12; if (h12 === 0) h12 = 12;
        return h12 + ':' + (mm < 10 ? '0' : '') + mm + ' ' + ap;
    }
    function card(timeStr, cycles, best) {
        return '<div class="col-6 col-md-4"><div class="border rounded p-3 text-center ' + (best ? 'bg-success text-white' : 'bg-light') + '"><div class="fs-4 fw-bold">' + timeStr + '</div><div class="small">' + cycles + ' cycles · ' + (cycles * 1.5) + ' hrs sleep</div></div></div>';
    }
    function renderWake() {
        var wake = parseTime(document.getElementById('wakeTime').value || '07:00');
        var html = '';
        [6, 5, 4].forEach(function (c) { html += card(fmt(wake - 15 - (c * 90)), c, c === 6); });
        document.getElementById('sleepTimes').innerHTML = html;
    }
    function renderSleep() {
        var sleep = parseTime(document.getElementById('sleepTime').value || '23:00');
        var html = '';
        [6, 5, 4].forEach(function (c) { html += card(fmt(sleep + 15 + (c * 90)), c, c === 6); });
        document.getElementById('wakeTimes').innerHTML = html;
    }
    document.getElementById('wakeTime').addEventListener('input', renderWake);
    document.getElementById('sleepTime').addEventListener('input', renderSleep);
    document.getElementById('nowBtn').addEventListener('click', function () {
        var n = new Date();
        var hh = n.getHours(), mm = n.getMinutes();
        document.getElementById('sleepTime').value = (hh < 10 ? '0' : '') + hh + ':' + (mm < 10 ? '0' : '') + mm;
        renderSleep();
    });
    document.getElementById('tabWake').addEventListener('click', function () {
        document.getElementById('tabWake').classList.add('active'); document.getElementById('tabSleep').classList.remove('active');
        document.getElementById('modeWake').classList.remove('d-none'); document.getElementById('modeSleep').classList.add('d-none');
    });
    document.getElementById('tabSleep').addEventListener('click', function () {
        document.getElementById('tabSleep').classList.add('active'); document.getElementById('tabWake').classList.remove('active');
        document.getElementById('modeSleep').classList.remove('d-none'); document.getElementById('modeWake').classList.add('d-none');
    });
    renderWake(); renderSleep();
})();
</script>
@endsection
