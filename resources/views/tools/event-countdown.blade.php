@extends('layouts.app')

@section('title', 'Countdown Maker - Free Event Countdown Timer | Azlaan Tools')
@section('meta_description', 'Free countdown maker: create a live countdown to any event with a shareable link. Days, hours, minutes and seconds update every second. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Countdown Maker</h1>
            <p class="lead text-muted">Create a live countdown for a birthday, exam, Eid, wedding or launch — and share it with a link. Free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="eventName" class="form-label fw-semibold">Event Name</label>
                            <input type="text" class="form-control" id="eventName" placeholder="e.g. My Birthday">
                        </div>
                        <div class="col-md-6">
                            <label for="eventDate" class="form-label fw-semibold">Event Date &amp; Time</label>
                            <input type="datetime-local" class="form-control" id="eventDate">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="startBtn">Start Countdown</button>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="errorBox"></div>
                    <div id="shareWrap" class="d-none mt-3">
                        <label for="shareLink" class="form-label fw-semibold">Shareable Link</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="shareLink" readonly>
                            <button type="button" class="btn btn-success" id="copyLinkBtn">Copy Link</button>
                        </div>
                        <span class="text-success small d-none" id="copyMsg">Copied!</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 d-none" id="countCard">
                <div class="card-body text-center">
                    <h2 class="h4 mb-3" id="countTitle">Countdown</h2>
                    <div class="row g-2" id="countGrid">
                        <div class="col-3"><div class="border rounded p-3 bg-light"><div class="display-6 fw-bold" id="cdDays">0</div><div class="small text-muted">Days</div></div></div>
                        <div class="col-3"><div class="border rounded p-3 bg-light"><div class="display-6 fw-bold" id="cdHours">0</div><div class="small text-muted">Hours</div></div></div>
                        <div class="col-3"><div class="border rounded p-3 bg-light"><div class="display-6 fw-bold" id="cdMins">0</div><div class="small text-muted">Minutes</div></div></div>
                        <div class="col-3"><div class="border rounded p-3 bg-light"><div class="display-6 fw-bold" id="cdSecs">0</div><div class="small text-muted">Seconds</div></div></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 d-none" id="passedBox">This event has started / passed. Pick a future date for a new countdown.</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Type your event name and pick the date and time.</li>
                        <li>Click <strong>Start Countdown</strong> to see days, hours, minutes and seconds ticking live.</li>
                        <li>Copy the shareable link and send it to friends — opening it starts the same countdown automatically.</li>
                        <li>If the date has already passed, you will see an event passed message instead.</li>
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
    var targetTime = null, timerId = null;
    var errorBox = document.getElementById('errorBox');
    function pad(n) { return n < 10 ? '0' + n : '' + n; }
    function tick() {
        if (!targetTime) { return; }
        var diff = targetTime - Date.now();
        if (diff <= 0) {
            document.getElementById('cdDays').textContent = '0';
            document.getElementById('cdHours').textContent = '00';
            document.getElementById('cdMins').textContent = '00';
            document.getElementById('cdSecs').textContent = '00';
            document.getElementById('passedBox').classList.remove('d-none');
            if (timerId) { clearInterval(timerId); timerId = null; }
            return;
        }
        document.getElementById('passedBox').classList.add('d-none');
        var secs = Math.floor(diff / 1000);
        var d = Math.floor(secs / 86400);
        var h = Math.floor((secs % 86400) / 3600);
        var m = Math.floor((secs % 3600) / 60);
        var s = secs % 60;
        document.getElementById('cdDays').textContent = d;
        document.getElementById('cdHours').textContent = pad(h);
        document.getElementById('cdMins').textContent = pad(m);
        document.getElementById('cdSecs').textContent = pad(s);
    }
    function begin(name, dateVal) {
        var dt = new Date(dateVal);
        if (isNaN(dt.getTime())) {
            errorBox.textContent = 'Please pick a valid date and time.';
            errorBox.classList.remove('d-none');
            return;
        }
        errorBox.classList.add('d-none');
        targetTime = dt.getTime();
        document.getElementById('countTitle').textContent = 'Countdown to: ' + (name || 'Your Event');
        document.getElementById('countCard').classList.remove('d-none');
        var link = window.location.origin + window.location.pathname + '?n=' + encodeURIComponent(name || 'Event') + '&t=' + encodeURIComponent(dateVal);
        document.getElementById('shareLink').value = link;
        document.getElementById('shareWrap').classList.remove('d-none');
        if (timerId) { clearInterval(timerId); }
        tick();
        timerId = setInterval(tick, 1000);
    }
    document.getElementById('startBtn').addEventListener('click', function () {
        var name = document.getElementById('eventName').value.trim();
        var dateVal = document.getElementById('eventDate').value;
        if (!dateVal) {
            errorBox.textContent = 'Please pick a date and time first.';
            errorBox.classList.remove('d-none');
            return;
        }
        begin(name, dateVal);
    });
    document.getElementById('copyLinkBtn').addEventListener('click', async function () {
        var box = document.getElementById('shareLink');
        try { await navigator.clipboard.writeText(box.value); }
        catch (e) { box.select(); document.execCommand('copy'); }
        var msg = document.getElementById('copyMsg');
        msg.classList.remove('d-none');
        setTimeout(function () { msg.classList.add('d-none'); }, 2000);
    });
    var params = new URLSearchParams(window.location.search);
    var pn = params.get('n'), pt = params.get('t');
    if (pt) {
        document.getElementById('eventName').value = pn || '';
        document.getElementById('eventDate').value = pt;
        begin(pn || 'Event', pt);
    }
})();
</script>
@endsection
