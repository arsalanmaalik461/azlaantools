@extends('layouts.app')

@section('title', 'Online Big Clock - Azlaan Tools')
@section('meta_description', 'A huge fullscreen digital clock for your screen with big digits, 12/24 hour format and date. Free online clock.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Online Big Clock</h1>
            <p class="lead text-muted">Time in big digits on your screen — great for presentations, classes or shops.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5" id="clockStage">
                    <div id="bigTime" style="font-size: clamp(64px, 16vw, 200px); font-weight: 800; line-height: 1; letter-spacing: 2px; font-variant-numeric: tabular-nums;">--:--</div>
                    <div id="bigDate" class="text-muted mt-3" style="font-size: clamp(16px, 3vw, 28px);">...</div>
                    <div id="ampm" class="fw-semibold mt-1" style="font-size: clamp(14px, 2.5vw, 24px);"></div>
                </div>
                <div class="card-footer">
                    <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center">
                        <div class="btn-group" role="group" aria-label="Hour format">
                            <button type="button" class="btn btn-outline-primary" id="fmt12">12-hour</button>
                            <button type="button" class="btn btn-outline-primary active" id="fmt24">24-hour</button>
                        </div>
                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" id="showSeconds" checked>
                            <label class="form-check-label" for="showSeconds">Seconds</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="showDate" checked>
                            <label class="form-check-label" for="showDate">Date</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="blinkColon">
                            <label class="form-check-label" for="blinkColon">Blink colon</label>
                        </div>
                        <button type="button" class="btn btn-success" id="fsBtn">Fullscreen</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the 12-hour or 24-hour format.</li>
                <li>Turn seconds, date and blinking colon on or off.</li>
                <li>Press "Fullscreen" — the clock fills the whole screen. Press Esc to exit.</li>
            </ol>
            <p class="text-muted small">The time comes from your device clock — keep your device time zone correct for accurate time.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var bigTime = document.getElementById('bigTime');
    var bigDate = document.getElementById('bigDate');
    var ampm = document.getElementById('ampm');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var goBtn = document.getElementById('fsBtn');

    var use24 = true;
    var colonVisible = true;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function tick() {
        var now = new Date();
        var h = now.getHours();
        var m = now.getMinutes();
        var s = now.getSeconds();
        var showSec = document.getElementById('showSeconds').checked;
        var blink = document.getElementById('blinkColon').checked;

        var displayH = h;
        var suffix = '';
        if (!use24) {
            suffix = h >= 12 ? ' PM' : ' AM';
            displayH = h % 12;
            if (displayH === 0) { displayH = 12; }
        }
        var colon = ':';
        if (blink) {
            colonVisible = !colonVisible;
            colon = colonVisible ? ':' : ' ';
        }
        var t = pad(displayH) + colon + pad(m);
        if (showSec) { t += colon + pad(s); }
        bigTime.textContent = t;
        ampm.textContent = use24 ? '' : suffix.trim();

        if (document.getElementById('showDate').checked) {
            var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            bigDate.textContent = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            bigDate.style.display = '';
        } else {
            bigDate.style.display = 'none';
        }
    }

    function setFormat(is24, btn) {
        use24 = is24;
        document.getElementById('fmt12').classList.toggle('active', !is24);
        document.getElementById('fmt24').classList.toggle('active', is24);
        tick();
    }

    document.getElementById('fmt12').addEventListener('click', function () { setFormat(false); });
    document.getElementById('fmt24').addEventListener('click', function () { setFormat(true); });
    document.getElementById('showSeconds').addEventListener('change', tick);
    document.getElementById('showDate').addEventListener('change', tick);

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.remove('d-none');
        var el = document.documentElement;
        try {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else if (el.requestFullscreen) {
                el.requestFullscreen().catch(function () {
                    showError('Fullscreen was not allowed. Check your browser permission.');
                });
            } else {
                showError('Your browser does not support fullscreen.');
            }
        } catch (e) {
            showError('Fullscreen could not start.');
        }
    });

    tick();
    setInterval(tick, 500);
})();
</script>
@endsection
