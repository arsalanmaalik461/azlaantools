@extends('layouts.app')

@section('title', 'Unix Timestamp Converter Online Free - Epoch to Date | Azlaan Tools')
@section('meta_description', 'Free Unix timestamp converter: live epoch clock, convert timestamps to human dates in UTC and local Pakistan time, and convert dates back to timestamps.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Unix Timestamp Converter</h1>
            <p class="lead text-muted">Convert epoch timestamps to readable dates and back. This tool runs 100% in your browser — nothing is uploaded or saved.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="fw-semibold">Current Epoch Time (live)</div>
                    <div class="display-6 font-monospace" id="clockSec">-</div>
                    <div class="text-muted font-monospace">Milliseconds: <span id="clockMs">-</span></div>
                    <div class="text-muted" id="clockHuman">-</div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="copyClockBtn">Copy Seconds</button>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Timestamp &rarr; Human Date</h5>
                    <div class="input-group mb-2"><input id="tsInput" class="form-control font-monospace" placeholder="e.g. 1727740800 or milliseconds"><select id="tsUnit" class="form-select" style="max-width:170px;"><option value="s">Seconds</option><option value="ms">Milliseconds</option></select></div>
                    <div id="tsResult" class="alert alert-light border small" style="white-space:pre-line;">Enter a timestamp to see the date.</div>
                    <hr>
                    <h5>Date &rarr; Timestamp</h5>
                    <input type="datetime-local" id="dateInput" class="form-control mb-2" step="1">
                    <div class="d-flex gap-2 flex-wrap"><button type="button" class="btn btn-primary" id="nowBtn">Use Now</button></div>
                    <div id="dateResult" class="alert alert-light border small mt-2" style="white-space:pre-line;">Pick a date and time.</div>
                    <button type="button" class="btn btn-success btn-sm" id="copyDateBtn">Copy Timestamp (seconds)</button>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Watch the live clock for the current epoch time in seconds and milliseconds.</li>
                <li>Paste any timestamp, choose seconds or milliseconds, and see UTC, local (PKT) and relative time.</li>
                <li>Or pick a date and time to get its timestamp in both units.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var clockSec = document.getElementById('clockSec'); var clockMs = document.getElementById('clockMs'); var clockHuman = document.getElementById('clockHuman');
    function tick() { var now = Date.now(); clockSec.textContent = Math.floor(now / 1000); clockMs.textContent = now; clockHuman.textContent = new Date(now).toString(); }
    tick(); setInterval(tick, 250);
    document.getElementById('copyClockBtn').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(clockSec.textContent); });
    function relative(d) {
        var diff = d.getTime() - Date.now(); var abs = Math.abs(diff); var secs = Math.floor(abs / 1000);
        var val; var unit;
        if (secs >= 86400) { val = Math.floor(secs / 86400); unit = 'days'; } else if (secs >= 3600) { val = Math.floor(secs / 3600); unit = 'hours'; } else if (secs >= 60) { val = Math.floor(secs / 60); unit = 'minutes'; } else { val = secs; unit = 'seconds'; }
        return diff >= 0 ? 'in ' + val + ' ' + unit : val + ' ' + unit + ' ago';
    }
    function convertTs() {
        var raw = document.getElementById('tsInput').value.trim(); var box = document.getElementById('tsResult');
        if (!raw) { box.textContent = 'Enter a timestamp to see the date.'; return; }
        var num = Number(raw); if (isNaN(num)) { box.textContent = 'Please enter a valid number.'; return; }
        var ms = document.getElementById('tsUnit').value === 'ms' ? num : num * 1000;
        var d = new Date(ms); if (isNaN(d.getTime())) { box.textContent = 'That timestamp is out of range.'; return; }
        var pkt = '';
        try { pkt = new Intl.DateTimeFormat('en-GB', { timeZone: 'Asia/Karachi', dateStyle: 'full', timeStyle: 'long' }).format(d); } catch (e) { pkt = d.toLocaleString(); }
        box.textContent = 'UTC: ' + d.toUTCString() + '\nLocal / PKT (Asia/Karachi): ' + pkt + '\nYour browser local: ' + d.toLocaleString() + '\nRelative: ' + relative(d);
    }
    document.getElementById('tsInput').addEventListener('input', convertTs);
    document.getElementById('tsUnit').addEventListener('change', convertTs);
    var lastSec = 0;
    function convertDate() {
        var v = document.getElementById('dateInput').value; var box = document.getElementById('dateResult');
        if (!v) { box.textContent = 'Pick a date and time.'; return; }
        var d = new Date(v); lastSec = Math.floor(d.getTime() / 1000);
        box.textContent = 'Seconds: ' + lastSec + '\nMilliseconds: ' + d.getTime() + '\nUTC: ' + d.toUTCString();
    }
    document.getElementById('dateInput').addEventListener('change', convertDate);
    document.getElementById('dateInput').addEventListener('input', convertDate);
    document.getElementById('nowBtn').addEventListener('click', function () { var now = new Date(); now.setMilliseconds(0); var pad = function (n) { return String(n).padStart(2, '0'); }; document.getElementById('dateInput').value = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds()); convertDate(); });
    document.getElementById('copyDateBtn').addEventListener('click', function () { if (lastSec && navigator.clipboard) navigator.clipboard.writeText(String(lastSec)); });
})();
</script>
@endsection
