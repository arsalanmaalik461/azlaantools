@extends('layouts.app')

@section('title', 'New Year Countdown - Azlaan Tools')
@section('meta_description', 'Live countdown to New Year midnight - see days, hours, minutes and seconds left until January 1st. Free online countdown timer.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">New Year Countdown</h1>
            <p class="lead text-muted">How far is the new year — a live countdown that updates every second.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h2 class="h4 mb-1">Countdown to <span id="targetYear">...</span></h2>
                    <p class="text-muted mb-4">Time left until 1 January, 12:00 AM</p>
                    <div class="row g-2 justify-content-center mb-4">
                        <div class="col-3 col-md-2">
                            <div class="border rounded p-2 p-md-3 bg-light">
                                <div class="display-6 fw-bold font-monospace" id="cdDays">0</div>
                                <div class="small text-muted">Days</div>
                            </div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="border rounded p-2 p-md-3 bg-light">
                                <div class="display-6 fw-bold font-monospace" id="cdHours">0</div>
                                <div class="small text-muted">Hours</div>
                            </div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="border rounded p-2 p-md-3 bg-light">
                                <div class="display-6 fw-bold font-monospace" id="cdMins">0</div>
                                <div class="small text-muted">Minutes</div>
                            </div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="border rounded p-2 p-md-3 bg-light">
                                <div class="display-6 fw-bold font-monospace" id="cdSecs">0</div>
                                <div class="small text-muted">Seconds</div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-success d-none" id="partyBox" role="alert">
                        Happy New Year! Wishing you a happy new year!
                    </div>
                    <h3 class="h6 mt-4 mb-2 text-start">Year progress</h3>
                    <div class="progress" style="height:24px;">
                        <div class="progress-bar bg-success" id="yearProgress" role="progressbar" style="width:0%">0%</div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="yearNote"></p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Just open this page — the countdown to New Year midnight starts automatically.</li>
                <li>Watch the days, hours, minutes and seconds tick down live every second.</li>
                <li>The bar below shows how much of the current year has already passed.</li>
                <li>Keep the tab open on New Year night for the celebration message at midnight.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var targetYear = document.getElementById('targetYear');
    var cdDays = document.getElementById('cdDays');
    var cdHours = document.getElementById('cdHours');
    var cdMins = document.getElementById('cdMins');
    var cdSecs = document.getElementById('cdSecs');
    var yearProgress = document.getElementById('yearProgress');
    var yearNote = document.getElementById('yearNote');
    var partyBox = document.getElementById('partyBox');

    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function tick() {
        var now = new Date();
        var target = new Date(now.getFullYear() + 1, 0, 1, 0, 0, 0);
        targetYear.textContent = target.getFullYear();

        var diff = target - now;
        if (diff <= 0) {
            partyBox.classList.remove('d-none');
            cdDays.textContent = '0'; cdHours.textContent = '00';
            cdMins.textContent = '00'; cdSecs.textContent = '00';
        } else {
            partyBox.classList.add('d-none');
            var totalSecs = Math.floor(diff / 1000);
            var d = Math.floor(totalSecs / 86400);
            var h = Math.floor((totalSecs % 86400) / 3600);
            var m = Math.floor((totalSecs % 3600) / 60);
            var s = totalSecs % 60;
            cdDays.textContent = d;
            cdHours.textContent = pad(h);
            cdMins.textContent = pad(m);
            cdSecs.textContent = pad(s);
        }

        var yearStart = new Date(now.getFullYear(), 0, 1);
        var yearEnd = new Date(now.getFullYear() + 1, 0, 1);
        var pct = Math.min(100, Math.max(0, (now - yearStart) / (yearEnd - yearStart) * 100));
        yearProgress.style.width = pct.toFixed(2) + '%';
        yearProgress.textContent = pct.toFixed(1) + '%';
        var daysGone = Math.floor((now - yearStart) / 86400000);
        yearNote.textContent = daysGone + ' days of ' + now.getFullYear() + ' have passed (' + pct.toFixed(1) + '% of the year complete).';
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endsection
