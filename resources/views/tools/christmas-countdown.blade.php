@extends('layouts.app')

@section('title', 'Christmas Countdown - Azlaan Tools')
@section('meta_description', 'Count down days, hours and minutes to Christmas. Free online Christmas countdown timer.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Christmas Countdown</h1>
            <p class="lead text-muted">How many days are left until Christmas? Watch the live countdown — days, hours, minutes and seconds.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="display-6 mb-1">🎄</div>
                    <h4 id="targetLabel" class="mb-4">Christmas — 25 December</h4>
                    <div id="cdGrid" class="row g-2 justify-content-center mb-4">
                        <div class="col-3 col-md-2">
                            <div class="card bg-light"><div class="card-body p-2"><div class="fs-2 fw-bold" id="cdD">0</div><div class="small text-muted">Days</div></div></div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="card bg-light"><div class="card-body p-2"><div class="fs-2 fw-bold" id="cdH">0</div><div class="small text-muted">Hours</div></div></div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="card bg-light"><div class="card-body p-2"><div class="fs-2 fw-bold" id="cdM">0</div><div class="small text-muted">Minutes</div></div></div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="card bg-light"><div class="card-body p-2"><div class="fs-2 fw-bold" id="cdS">0</div><div class="small text-muted">Seconds</div></div></div>
                        </div>
                    </div>
                    <div class="alert alert-info d-none" id="xmasDay"></div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Year progress</span><span id="yearPct"></span>
                        </div>
                        <div class="progress" style="height:12px">
                            <div class="progress-bar bg-success" id="yearBar" role="progressbar" style="width:0%"></div>
                        </div>
                    </div>
                    <div class="row text-center mb-3">
                        <div class="col-6"><div class="border rounded p-2"><div class="fw-bold fs-5" id="sleeps">0</div><div class="small text-muted">Sleeps left</div></div></div>
                        <div class="col-6"><div class="border rounded p-2"><div class="fw-bold fs-5" id="weeksLeft">0</div><div class="small text-muted">Weeks left</div></div></div>
                    </div>
                    <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy Countdown Message</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>The live countdown starts as soon as you open the page — you do not need to do anything.</li>
                <li>If 25 December has passed, the countdown automatically switches to next year's Christmas.</li>
                <li>Use <strong>Copy Countdown Message</strong> to send it to your friends.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var cdD = document.getElementById('cdD');
    var cdH = document.getElementById('cdH');
    var cdM = document.getElementById('cdM');
    var cdS = document.getElementById('cdS');
    var targetLabel = document.getElementById('targetLabel');
    var xmasDay = document.getElementById('xmasDay');
    var yearBar = document.getElementById('yearBar');
    var yearPct = document.getElementById('yearPct');
    var sleeps = document.getElementById('sleeps');
    var weeksLeft = document.getElementById('weeksLeft');
    var copyBtn = document.getElementById('copyBtn');
    var errorBox = document.getElementById('errorBox');

    function nextChristmas() {
        var now = new Date();
        var y = now.getFullYear();
        var xmas = new Date(y, 11, 25, 0, 0, 0);
        if (now >= new Date(y, 11, 26, 0, 0, 0)) {
            xmas = new Date(y + 1, 11, 25, 0, 0, 0);
        }
        return xmas;
    }
    var target = nextChristmas();
    targetLabel.textContent = 'Christmas — 25 December ' + target.getFullYear();

    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function tick() {
        var now = new Date();
        var diff = target - now;
        if (diff <= 0) {
            xmasDay.textContent = '🎄 Merry Christmas! Today is 25 December — enjoy and celebrate!';
            xmasDay.classList.remove('d-none');
            cdD.textContent = '0'; cdH.textContent = '00'; cdM.textContent = '00'; cdS.textContent = '00';
            return;
        }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor(diff % 86400000 / 3600000);
        var m = Math.floor(diff % 3600000 / 60000);
        var s = Math.floor(diff % 60000 / 1000);
        cdD.textContent = d;
        cdH.textContent = pad(h);
        cdM.textContent = pad(m);
        cdS.textContent = pad(s);
        sleeps.textContent = d;
        weeksLeft.textContent = Math.floor(d / 7);

        var yearStart = new Date(now.getFullYear(), 0, 1);
        var yearEnd = new Date(now.getFullYear() + 1, 0, 1);
        var pct = Math.round((now - yearStart) / (yearEnd - yearStart) * 100);
        yearBar.style.width = pct + '%';
        yearPct.textContent = pct + '% passed';
    }
    tick();
    setInterval(tick, 1000);

    copyBtn.addEventListener('click', function () {
        var msg = '🎄 Christmas Countdown: only ' + cdD.textContent + ' days, ' + cdH.textContent + ' hours and ' +
            cdM.textContent + ' minutes left (25 December ' + target.getFullYear() + ')!';
        navigator.clipboard.writeText(msg).then(function () {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy Countdown Message'; }, 1500);
        }).catch(function () {
            errorBox.textContent = 'Could not copy. Select the text and copy it manually.';
            errorBox.classList.remove('d-none');
        });
    });
})();
</script>
@endsection
